<?php

class RegistrationController extends OSDN_Controller_Action
{
    public function indexAction()
    {
        $do = trim($this->_getParam('do'));

        if (empty($do)) {
            return;
        }

        $customers = new BS_Customers();
        $response = $customers->createAccount($this->_getAllParams());
        if ($response->isError()) {
            $data = $this->_getAllParams();
            foreach ($data as &$field) {
                if (is_string($field)) {
                    $field = htmlspecialchars($field);
                }
            }
        	$this->view->assign($data);
            $this->_collectErrors($response);
            return;
        }

        $this->sendEmailAdmin($this->_getParam('login'), $this->_getParam('tariff'));
        $this->sendEmailCustomer($this->_getAllParams());
        $this->_redirect('/default/registration/success');
    }

    public function successAction()
    {
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
            $mail->setSubject("Добавлен клиент, логин: $login");
            try {
                @$mail->send();
            } catch (Exception $e) {
                //echo $e->getMessage();
            }
        }
    }

    private function sendEmailCustomer($data)
    {
        $config = Zend_Registry::get('config');
        $server = $config->mail->SMTP;
        $mail = new Zend_Mail('UTF-8');
        $mail->addTo($data['email'], $data['name']);
        $mail->setFrom($config->mail->from->address, $config->mail->from->caption);
        $mail->setSubject("Создана учётная запись");
        $mail->setBodyHtml("Здравствуйте, " . $data['name'] . "!<br /><br />
            В личном кабинете e-head для вас создана учётная запись.<br />
            Вы можете войти в личный кабинет e-head здесь:
            <a href='http://billing.e-head.ru/'>http://billing.e-head.ru/</a><br /><br />
            Логин: " . $data['login'] . "<br />
            Пароль: " . $data['password'] . "<br /><br />
            Войдя в личный кабинет вы сможете заказать тарифный план,
            а также пополнить счёт.<br />
            После выбора тарифного плана и оплаты
            в течение суток будет активирован доступ к системе e-head,<br />
            о чём будет сообщено Вам в письме с инструкциями.
            <br /><br />
            <br />----------------------<br />
			С уважением,<br />
			Виртуальный менеджер e-head
        ");
        try {
            @$mail->send();
        } catch (Exception $e) {
            //echo $e->getMessage();
        }
    }
}