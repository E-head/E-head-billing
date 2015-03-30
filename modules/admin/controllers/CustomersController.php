<?php

class Admin_CustomersController extends OSDN_Controller_Action
{
    /**
     * Accounts object
     *
     * @var OSDN_Accounts
     */
    protected $_accounts;

    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->admin);
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-all');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'add');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'update');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'delete');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'remind-password');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'change-password');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'send-mail');
    }

    public function init()
    {
        $this->_accounts = new OSDN_Accounts();
        parent::init();
    }

    public function getAllAction()
    {
        $response = $this->_accounts->fetchByRole(
            OSDN_Accounts::CUSTOMER_ROLE, $this->_getAllParams());
        if ($response->isError()) {
            $this->_collectErrors($response);
            return;
        }
        $this->view->rows = $response->rows;
        $this->view->total = $response->total;
        $this->view->success = true;
    }

    public function getAction()
    {
        $response = $this->_accounts->fetchAccount($this->_getParam('id'));
        if ($response->isError()) {
            $this->_collectErrors($response);
            $this->view->data = array();
            return;
        }

        $this->view->data = $response->rowset;
        $this->view->success = true;
    }

    public function addAction()
    {
        $response = $this->_accounts->createAccount(array(
            'login'     => $this->_getParam('login'),
            'password'  => $this->_getParam('password'),
            'roleId'    => OSDN_Accounts::CUSTOMER_ROLE
        ));

        if ($response->isError()) {
            $this->_collectErrors($response);
        } else {
            $this->view->success = true;
            $this->view->errors = array();
        }
    }

    public function updateAction()
    {
    	$customers = new BS_Customers();
        $response = $customers->update($this->_getParam('id'), $this->_getAllParams());
        if ($response->isError()) {
            $this->_collectErrors($response);
            return;
        }
        $this->view->success = true;
    }

    public function deleteAction()
    {
        $response = $this->_accounts->deleteAccount($this->_getParam('id'));
        if ($response->isError()) {
            $this->_collectErrors($response);
            return;
        }
        $this->view->success = true;
    }

    public function sendMailAction()
    {
        $config = Zend_Registry::get('config');
        $mail = new Zend_Mail('UTF-8');
//        $mail->setDefaultTransport(new Zend_Mail_Transport_Smtp(
//            $config->mail->SMTP,
//            $config->mail->authentificate->toArray()
//        ));
        $mail->addTo($this->_getParam('email'), $this->_getParam('name'));
        $mail->setFrom($config->mail->from->address, $config->mail->from->caption);
        $mail->setSubject("Активация системы e-head");
        $mail->setBodyHtml(nl2br($this->_getParam('body')));
        try {
            @$mail->send();
        } catch (Exception $e) {
            //echo $e->getMessage();
        }
        $this->view->success = true;
    }
}