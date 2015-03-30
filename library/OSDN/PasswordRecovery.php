<?php

class OSDN_PasswordRecovery
{
    public function doRequest($data)
    {
        $table = new OSDN_PasswordRecoveryTable();
        $hash = substr(uniqid(rand()).uniqid(rand()).uniqid(rand()), 0, 34);
        $result = $table->insert(array('id' => $hash, 'account_id' => $data['id']));
        if (!$result) {
            $response = new OSDN_Response();
            return $response->addStatus(new OSDN_Accounts_Status(OSDN_Accounts_Status::FAILURE));
        }
        $data['hash'] = $hash;
        $response = $this->_sendMail($data);
        if ($response->hasNotSuccess()) {
            return $response;
        }
        return $response->addStatus(new OSDN_Accounts_Status(OSDN_Accounts_Status::OK));
    }

    public function checkRequest($id)
    {
        $this->_clearExpired();

        $response = new OSDN_Response();
        $table = new OSDN_PasswordRecoveryTable();

        $result = $table->findOne($id);
        if (false === $result || is_null($result)) {
            return $response->addStatus(new OSDN_Accounts_Status(OSDN_Accounts_Status::FAILURE));
        }

        $response->setRow($result->toArray());
        return $response->addStatus(new OSDN_Accounts_Status(OSDN_Accounts_Status::OK));
    }

    public function doReset($id, $account_id, $password)
    {
        $accounts = new OSDN_Accounts();
        $response = $accounts->changePassword($account_id, $password);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $table = new OSDN_PasswordRecoveryTable();
        try {
            $result = $table->delete(array('id = ?' => $id));
        } catch (Exception $e) {
            if (OSDN_DEBUG) {
                throw $e;
            }
        }
        $status = $result ? OSDN_Accounts_Status::OK : OSDN_Accounts_Status::FAILURE;
        $response = new OSDN_Response();
        return $response->addStatus(new OSDN_Accounts_Status($status));
    }

    private function _clearExpired()
    {
        $table = new OSDN_PasswordRecoveryTable();
        $table->delete(new Zend_Db_Expr('time <  TIMESTAMPADD(DAY,-1,NOW())'));
    }

    private function _sendMail($data)
    {
        $response = new OSDN_Response();

        $config = Zend_Registry::get('config');
        $server = $config->mail->SMTP;
        $mail = new Zend_Mail('UTF-8');
        $mail->addTo($data['email'], $data['name']);
        $mail->setFrom($config->mail->from->address, $config->mail->from->caption);
        $mail->setSubject("Восстановление пароля учётной записи");
        $mail->setBodyHtml("Здравствуйте, " . $data['name'] . ".<br /><br />
            На http://billing.e-head.ru/ был сделан запрос на восстановление доступа
            к учётной записи, связанной с этим адресом email.<br /><br />
            Информация об аккаунте:<br />
            Логин: " . $data['login'] . "<br />
            Email: " . $data['email'] . "<br />
            <br />
            Проследуйте по этой ссылке, чтобы подтвердить сброс вашего пароля:<br /><br />
            <a href='http://billing.e-head.ru/password-recovery/reset-form/id/"
            . $data['hash'] . "'>http://billing.e-head.ru/password-recovery/reset-form/id/"
            . $data['hash'] . "</a><br /><br />
            Если вы не делали такого запроса, проигнорируйте это письмо,
            и пароль останется прежним. Ccылка будет активна 24 часа.
            <br /><br />
            <br />----------------------<br />
            С уважением,<br />
            Виртуальный менеджер e-head
        ");
        try {
            @$mail->send();
            return $response->addStatus(new OSDN_Accounts_Status(OSDN_Accounts_Status::OK));
        } catch (Exception $e) {
            $response->errorMessage = $e->getMessage();
            return $response->addStatus(new OSDN_Accounts_Status(OSDN_Accounts_Status::FAILURE));
        }
    }
}