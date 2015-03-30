<?php

/**
 * Reports conroller
 * @version $Id: $
 */
class Admin_ReportController extends OSDN_Controller_Action
{
    protected $_reports;

	public function init()
	{
        $this->_helper->layout->setLayout('report');
	}

    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->admin);
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'customers');
        //$acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'mass-mail');
    }

    public function customersAction()
    {
        $accounts = new OSDN_Accounts();
    	$response = $accounts->fetchByRole(OSDN_Accounts::CUSTOMER_ROLE);
        if ($response->isError()) {
            $this->_collectErrors($response);
            return;
        }
        $this->view->data = $response->getRowset();
    }

    /*
    public function massMailAction()
    {
        $this->disableRender(true);
        $accounts = new OSDN_Accounts();
        $response = $accounts->fetchByRole(2); // 1 = administrators, 2 = clients
        if ($response->isSuccess()) {
            $config = Zend_Registry::get('config');
            $server = $config->mail->SMTP;
            $rows = $response->getRowset();
            foreach ($rows as $row) {
                $mail = new Zend_Mail('UTF-8');
                $mail->addTo($row['email'], $row['name']);
                $mail->setFrom($config->mail->from->address, $config->mail->from->caption);
                $mail->setSubject('Новости e-head');
                $mail->setBodyHtml('Уважаемый, абонент<br /><br />'
                    . 'С 1-го февраля 2011 года введен в эксплуатацию первый бесплатный тариф '
                    . '"<a href="http://e-head.ru/index.php/tarify">Старт<a/>".<br />'
                    . 'Вы можете выбрать тариф в "<a href="http://billing.e-head.ru">'
                    . 'Вашем личном кабинете</a>" в разделе "Настройки".'
                );
                try {
                    $mail->send();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    return;
                }
            }
            echo 'Letters have been succesfully send';
        }
    }
    */
}