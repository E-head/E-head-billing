<?php

class Customer_BillsController extends OSDN_Controller_Action
{
    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->customers);
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'index');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'bank-pay');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'bank-bill');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'wm-pay');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'wm-success');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'wm-failure');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'qiwi-pay');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'ym-pay');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'ym-success');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'ym-failure');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'qiwi-pay');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'qiwi-success');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'qiwi-failure');
    }

    public function preDispatch()
    {
        $this->view->page = 'bills';
    }

    public function indexAction()
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount(OSDN_Accounts_Prototype::getId());
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $this->view->assign($data);
    }

    public function bankPayAction()
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount(OSDN_Accounts_Prototype::getId());
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $data['summ'] = BS_TariffPlans::getCost($data['tariff']);
        $data['payer_name'] = htmlspecialchars((1 == $data['iscompany'])
                            ? $data['company_name'] : $data['name']);
        $this->view->assign($data);
    }

    public function bankBillAction()
    {
        $this->disableLayout(true);

        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount(OSDN_Accounts_Prototype::getId());
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $params = $this->_getAllParams();

        $dateObj = new Zend_Date();
        $data['bill_date'] = $dateObj->toString('dd MMMM Y');

        $data['bill_number'] = $dateObj->toString('Mddss');

        $data['price'] = (intval($params['summ']) > 0)
                       ? intval($params['summ'])
                       : BS_TariffPlans::getCost($data['tariff']);

        $data['price_letter'] = OSDN_Plural::asString($data['price'],
            OSDN_Plural::MALE, array('рубль', 'рубля', 'рублей'));

        $data['payer_id'] = $data['id'];

        $payerName = empty($params['payer_name'])
                   ? (1 == $data['iscompany']) ? $data['company_name'] : $data['name']
                   : $params['payer_name'];

        $data['payer_name'] = htmlspecialchars($payerName);
        $this->view->assign($data);

        $login = $data['login'];
        $this->sendEmailAdmin("Клиент $login выписал счёт", $this->view->render('bills/bank-bill.phtml'));
    }

    public function qiwiPayAction()
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount(OSDN_Accounts_Prototype::getId());
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $data['summ'] = BS_TariffPlans::getCost($data['tariff']);
        $this->view->assign($data);
    }

    public function wmPayAction()
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount(OSDN_Accounts_Prototype::getId());
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $data['summ'] = BS_TariffPlans::getCost($data['tariff']);
        $this->view->assign($data);
    }

    public function ymPayAction()
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount(OSDN_Accounts_Prototype::getId());
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $data['summ'] = BS_TariffPlans::getCost($data['tariff']);
        $this->view->assign($data);
    }

    public function qiwiSuccessAction() {}

    public function qiwiFailureAction() {}

    public function wmSuccessAction() {}

    public function wmFailureAction() {}

    public function ymSuccessAction() {}

    public function ymFailureAction() {}

    private function sendEmailAdmin($subj = '', $body = '')
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchByRole(1); // 1 = administrators
        if ($response->isSuccess()) {
            $config = Zend_Registry::get('config');
            $server = $config->mail->SMTP;
            $mail = new Zend_Mail('UTF-8');
            $rows = $response->getRowset();
            foreach ($rows as $row) {
                $mail->addTo($row['email'], $row['name']);
            }
            $mail->setFrom($config->mail->from->address, $config->mail->from->caption);
            $mail->setSubject($subj);
            $mail->setBodyHtml($body);
            try {
                @$mail->send();
            } catch (Exception $e) {
                //echo $e->getMessage();
            }
        }
    }

}