<?php

/**
 * Basic accounts webservice
 *
 * @category    BS
 * @package     BS_XmlRpc
 * @version     $Id: $
 */
class BS_XmlRpc_Accounts
{
    /**
     * Authenticate by login/password
     *
     * @param string login
     * @param string password
     *
     * @return OSDN_Response
     */
    public function authenticate($login, $password)
    {
        $model = new OSDN_Accounts();
        try {
            $response = $model->fetchByLoginPassword($login, $password);
        } catch (Exception $e) {
            throw new Zend_XmlRpc_Server_Exception($e->getMessage(), $e->getCode());
        }

        // Clear account data to prevent information leaks
        $response->row = Array();

        // Return only authentication status
        return $response->toArray();
    }

    /**
     * Fetch a list of registered inactive customers
     *
     * @return OSDN_Response
     */
    public function customers()
    {
        $response = new OSDN_Response();
        $table = new OSDN_Accounts_Table_Accounts();
        $select = $table->getAdapter()->select();
        $select->from($table->getTableName(),
            array('id', 'company_name', 'name', 'email', 'phone'));
        $select->where('role_id = ?', OSDN_Accounts::CUSTOMER_ROLE);
        $select->where("status = 'inactive'");
        $select->order("id DESC");

        $status = null;
        try {
            $rowset = $select->query()->fetchAll();
            $response->setRowset($rowset);
            $status = OSDN_Acl_Status::OK;
        } catch (Exception $e) {
            if (OSDN_DEBUG) {
                throw $e;
            }
            $status = OSDN_Acl_Status::DATABASE_ERROR;
        }

        $response->addStatus(new OSDN_Acl_Status($status));
        return $response->toArray();
    }
}