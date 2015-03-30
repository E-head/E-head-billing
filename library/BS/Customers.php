<?php

/**
 * General class for manipulate customers
 *
 * @category	BS
 * @package		BS_Customers
 * @version		$Id: Customers.php 8098 2009-04-16 07:08:48Z flash $
 */
class BS_Customers
{
    /**
     * The accounts table
     *
     * @var OSDN_Accounts_Table_Accounts
     */
    protected $_tableAccounts;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_tableAccounts = new OSDN_Accounts_Table_Accounts();
    }

    /**
     * Update account
     *
     * @param int $id       The account id
     * @param array $data   Update data
     */
    public function update($id, array $data = array())
    {
        $response = new OSDN_Response();

        $data['id'] = $id;

        $validators = array(
            'id'        => array('Id', 'presence' => 'required'),
            'name'      => array(array('StringLength', 1, 250), 'presence' => 'required'),
            'email'     => array('EmailAddress', 'presence' => 'required'),
            'phone'     => array(array('StringLength', 0, 250), 'allowEmpty' => true),
            'tariff'    => array(array('InArray', array_keys(BS_TariffPlans::getAll())), 'allowEmpty' => true),
            'password'  => array(array('StringLength', 0, 250), 'allowEmpty' => true),
            'password2' => array(array('StringLength', 0, 250), 'allowEmpty' => true),
            'old_password'  => array(array('StringLength', 0, 250))
        );

        if (OSDN_Accounts_Prototype::getRoleId() == OSDN_Accounts::ADMIN_ROLE) {
            $validators = array_merge($validators, array(
                'iscompany' => array(array('InArray', array(0, 1)), 'presence' => 'required'),
                'status'    => array(array('InArray', array_keys(BS_Statuses::getAll())), 'presence' => 'required'),
                'balance'   => array('Int'),
                'active'    => array(array('InArray', array(0, 1)), 'presence' => 'required'),
                'site'      => array(array('StringLength', 0, 250)),
                'site_pwd'  => array(array('StringLength', 0, 50)),
                'comments'  => array(array('StringLength', 0, 4096))
            ));
        }

        $f = new OSDN_Filter_Input(array(
            'id'        => 'Int',
            '*'         => 'StringTrim'
        ), $validators, $data);

        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $accountData = $f->getData();

        /*
        $accountData = array(
            'name'      => $f->name,
            'email'     => $f->email,
            'phone'     => $f->phone,
            'tariff'    => $f->tariff
        );
        */

        $acccounts = new OSDN_Accounts();
        $acccountResponse = $acccounts->fetchAccount($f->id);
        if ($acccountResponse->hasNotSuccess() ) {
            return $acccountResponse;
        }

        $acccountInfo = $acccountResponse->rowset;
        if (empty($acccountInfo)) {
            return $response->addStatus(new OSDN_Accounts_Status(
                OSDN_Accounts_Status::ACCOUNT_IS_NOT_EXISTS));
        }

        if (!empty($f->old_password) || !empty($f->password) || !empty($f->password2)) {

            $accountsTable = new OSDN_Accounts_Table_Accounts();
            $currentPassword = $accountsTable->fetchPassword($f->id);

            if (empty($f->old_password) || $currentPassword !== md5($f->old_password)) {
                return $response->addStatus(new OSDN_Accounts_Status(
                    OSDN_Accounts_Status::WRONG_PASSWORD, 'old password'));
            }

            $pf = new OSDN_Filter_Input(array(
                '*'         => array('StringTrim')
            ), array(
                'password'  => array(array('StringLength', 6, 250), 'presence' => 'required'),
                'password2' => array(array('StringLength', 6, 250), 'presence' => 'required')
            ), array(
                'password'  => $f->password,
                'password2' => $f->password2
            ));

            $response->addInputStatus($pf);
            if ($response->hasNotSuccess()) {
                return $response;
            }

            if ($pf->password !== $pf->password2) {
                return $response->addStatus(new OSDN_Accounts_Status(
                    OSDN_Accounts_Status::PASSWORDS_DOES_NOT_MATCH, 'password, password2'));
            }

            $accountData = array_merge($accountData, array('password' => md5($pf->password)));
        } else {
            unset($accountData['password']);
        }

        $companyData = array();

        if (1 == $f->iscompany) {

            $companyFields = array(
                'company_name',
                'company_address',
                'company_postaddress',
                'company_inn',
                'company_kpp',
                'company_rs',
                'company_bank',
                'company_ks',
                'company_bik',
                'company_ogrn',
                'company_director'
            );

            $companyValidators = array_fill_keys($companyFields,
                array(array('StringLength', 1, 250), 'presence' => 'required')
            );

            $c = new OSDN_Filter_Input(array('*' => 'StringTrim'), $companyValidators, $data);
            $response->addInputStatus($c);
            if ($response->hasNotSuccess()) {
                return $response;
            }

            foreach ($companyFields as $field) {
                $companyData[$field] = $c->getEscaped($field);
            }
        }

        $infoData = array_merge($accountData, $companyData);
        $affectedRows = $this->_tableAccounts->updateByPk($infoData, $f->id);

        $response->affectedRows = $affectedRows;
        $response->addStatus(new OSDN_Accounts_Status(
            OSDN_Accounts_Status::retrieveAffectedRowStatus($affectedRows)));

        if ($response->isSuccess() && $acccountInfo['tariff'] !== $f->tariff) {
            $this->_recalculateTariff($f->id, $f->tariff);
        }
        return $response;
    }

    /**
     * Create new account
     *
     * @param array $data
     * <code>
     *  login       string  REQUIRED
     *  password    string  REQUIRED
     * </code>
     * @return OSDN_Response
     * <code>
     *  id: int
     * </code>
     */
    public function createAccount(array $data)
    {
        $response = new OSDN_Response();

        $f = new OSDN_Filter_Input(array(
            'iscompany' => 'Int',
            '*'         => 'StringTrim'
        ), array(
            'login'     => array(array('Regex', '/^[a-z0-9-]+$/'), 'presence' => 'required'),
            'password'  => array(array('StringLength', 6, 250), 'presence' => 'required'),
            'password2' => array(array('StringLength', 6, 250), 'presence' => 'required'),
            'name'      => array(array('StringLength', 1, 250), 'presence' => 'required'),
            'email'     => array('EmailAddress', 'presence' => 'required'),
            'phone'     => array(array('StringLength', 0, 20)),
            'tariff'    => array(array('InArray', array_keys(BS_TariffPlans::getAll())), 'allowEmpty' => true),
            'iscompany' => array(array('InArray', array(0, 1)), 'presence' => 'required')
        ), $data);

        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $existsResponse = $this->accountExists(strtolower($f->login));
        if ($existsResponse->isError()) {
            return $existsResponse;
        }

        if ($f->password !== $f->password2) {
        	return $response->addStatus(new OSDN_Accounts_Status(
                OSDN_Accounts_Status::PASSWORDS_DOES_NOT_MATCH));
        }

        $accountData = array(
            'login'     => strtolower($f->login),
            'password'  => md5($f->password),
            'name'      => $f->name,
            'email'     => $f->email,
            'phone'     => $f->phone,
            'iscompany' => $f->iscompany,
            'tariff'    => $f->tariff,
            'role_id'   => 2,
            'active'    => 1
        );

        $companyData = array();

        if (1 == intval($f->iscompany)) {

            $companyFields = array(
                'company_name',
                'company_address',
                'company_postaddress',
                'company_inn',
                'company_kpp',
                'company_rs',
                'company_bank',
                'company_ks',
                'company_bik',
                'company_ogrn',
                'company_director'
            );

            $companyValidators = array_fill_keys($companyFields,
                array(array('StringLength', 1, 250), 'presence' => 'required')
            );

            $c = new OSDN_Filter_Input(array('*' => 'StringTrim'), $companyValidators, $data);
            $response->addInputStatus($c);
            if ($response->hasNotSuccess()) {
                return $response;
            }

            foreach ($companyFields as $field) {
                $companyData[$field] = $c->getEscaped($field);
            }

        }

        $id = $this->_tableAccounts->insert(array_merge($accountData, $companyData));

        if ($id > 0) {
            $status = OSDN_Accounts_Status::OK;
            $response->id = $id;
        } else {
            $status = OSDN_Accounts_Status::FAILURE;
        }
        return $response->addStatus(new OSDN_Accounts_Status($status));
    }

    /**
     * Change password
     *
     * @param int $id       The account id
     * @param array $data   contains old password and new one
     *                      (old_password, new_password1, new_password2)
     * @return OSDN_Response
     * <data>
     * array(
     *  affectedRows: int
     * )
     * </data>
     */
    public function chPassword($id, array $data)
    {
        $response = new OSDN_Response();
        $data['id'] = $id;

        $f = new OSDN_Filter_Input(array(
            '*'     => array('StringTrim')
        ), array(
            'old_password'   => array('password', 'presense' => 'required'),
            'new_password1'  => array('password', 'presense' => 'required'),
            'new_password2'  => array('password', 'presense' => 'required')
        ), $data);

        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $password = $this->_tableAccounts->fetchPassword($id);

        if ($password !== md5($f->old_password)) {
            return $response->addStatus(new OSDN_Accounts_Status(
                OSDN_Accounts_Status::WRONG_PASSWORD, 'old_password'));
        }

        if ($f->new_password1 !== $f->new_password2) {
            return $response->addStatus(new OSDN_Accounts_Status(
                OSDN_Accounts_Status::INCORRECT_NEW_PASSWORD, 'new_password2'));
        }

        $affectedRows = $this->_tableAccounts->updateByPk(array(
            'password' => md5($f->new_password1)
        ), $id);

        $response->affectedRows = $affectedRows;
        return $response->addStatus(new OSDN_Accounts_Status(
            OSDN_Accounts_Status::retrieveAffectedRowStatus($affectedRows)));
    }

    /**
     * Check if present account
     *
     * @param string $login
     * @return OSDN_Response
     * <code>
     *  exists: bool
     * </code>
     */
    public function accountExists($login)
    {
        $f = new Zend_Filter_StringTrim();
        $login = $f->filter($login);

        $stringLengthValidator = new Zend_Validate_StringLength(3, 50);
        $loginValidator = new OSDN_Validate_Login();
        $response = new OSDN_Response();
        if (!$stringLengthValidator->isValid($login) || !$loginValidator->isValid($login)) {
            return $response->addStatus(new OSDN_Accounts_Status(
                OSDN_Accounts_Status::INPUT_PARAMS_INCORRECT, 'login'));
        }

        $count = $this->_tableAccounts->count(array('login = ?' => $login));
        $status = null;
        $exists = true;

        if (0 === $count) {
            $status = OSDN_Accounts_Status::OK;
            $exists = false;
        } elseif (false === $count) {
            $status = OSDN_Accounts_Status::DATABASE_ERROR;
        } else {
            $status = OSDN_Accounts_Status::ACCOUNT_IS_ALREADY_EXISTS;
        }

        $response->exists = $exists;
        return $response->addStatus(new OSDN_Accounts_Status($status));
    }

    private function _recalculateTariff($acccountId, $tariff)
    {
        //TODO: make tariff recalculation
    }
}