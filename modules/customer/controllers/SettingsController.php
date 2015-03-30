<?php

class Customer_SettingsController extends OSDN_Controller_Action
{
    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->customers);
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'index');
    }

    public function indexAction()
    {
        $do = trim($this->_getParam('do'));
        $this->view->page = 'settings';
        $this->view->tariffPlans = BS_TariffPlans::getAll();
        $data = array();
        $accountId = OSDN_Accounts_Prototype::getId();

        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchAccount($accountId);
        if ($response->isError()) {
            $this->_collectErrors($response);
        }
        $data = $response->getRowset();
        $login = $data['login'];
        $tariff = $data['tariff'];
        $this->view->iscompany = $data['iscompany'];

        if (!empty($do)) {
            $data = $this->_getAllParams();
            $customers = new BS_Customers();
            $response = $customers->update($accountId, $data);
            if ($response->isError()) {
                $this->_collectErrors($response);
            } else {
                $this->view->success = true;
                if ($this->_getParam('tariff') != $tariff) {
                    $this->sendEmailAdminTariffChange($login, $this->_getParam('tariff'));
                }
            }
        }

        foreach ($data as &$field) {
            if (is_string($field)) {
                $field = htmlspecialchars($field);
            }
        }
        $this->view->assign($data);

        //$this->sendEmailAdmin($this->_getParam('login'));
        //$this->sendEmailCustomer($this->_getAllParams());
    }

    private function sendEmailAdmin($login, $tariff)
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
            $mail->setSubject("Клиент $login сменил тариф на $tariff");
            $mail->setBodyHtml("");
            try {
                @$mail->send();
            } catch (Exception $e) {
                //echo $e->getMessage();
            }
        }
    }
}