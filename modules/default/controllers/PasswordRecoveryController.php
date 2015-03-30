<?php

/**
 * Password recovery conroller
 */
class PasswordRecoveryController extends OSDN_Controller_Action
{
    public function requestFormAction()
    {
        $noDataMsg = 'Совпадений не найдено! Пробуйте ввести только логин или e-mail.';
        $errorSendEmailMsg = 'При отправке письма произошла ошибка';
        $this->view->message = '';

        $do = trim($this->_getParam('do'));
        $login = trim($this->_getParam('login'));
        $email = trim($this->_getParam('email'));
        if (empty($do) || (empty($login) && empty($email))) {
            return;
        }

        $accounts = new OSDN_Accounts();

        if (!empty($login)) {
            $response = $accounts->fetchByLogin($login);
            if ($response->hasNotSuccess()) {
                $this->view->message = $noDataMsg;
                return;
            }
            $accountInfo = $response->row;
            if (!empty($email) && $accountInfo['email'] != $email) {
                $this->view->message = $noDataMsg;
                return;
            }
        } else if (!empty($email)) {
            $response = $accounts->fetchByEmail($email);
            if ($response->hasNotSuccess()) {
                $this->view->message = $noDataMsg;
                return;
            }
            $accountInfo = $response->row;
        }

        $passwordRecovery = new OSDN_PasswordRecovery();
        $response = $passwordRecovery->doRequest($accountInfo);
        if ($response->hasNotSuccess()) {
            $this->view->message = $errorSendEmailMsg;
            return;
        }
        $this->_redirect('/password-recovery/request-success');
    }

    public function resetFormAction()
    {
        $do = $this->_getParam('do');
        $id = $this->_getParam('id');
        $account_id = $this->_getParam('account_id');
        $password = $this->_getParam('password');
        $repassword = $this->_getParam('repassword');

        $passwordRecovery = new OSDN_PasswordRecovery();
        $response = $passwordRecovery->checkRequest($id);

        if ($response->hasNotSuccess()) {
            $this->_redirect('/default/password-recovery/reset-failure');
            return;
        }
        $data = $response->getRow();
        $this->view->id = $data['id'];
        $this->view->account_id = $data['account_id'];

        if (empty($do)) {
            return;
        }

        if (empty($password) || empty($repassword)) {
            $this->view->message = 'Ошибка! Не все поля заполнены.';
            return;
        }

        if ($password !== $repassword) {
            $this->view->message = 'Ошибка! Поля не совпадают.';
            return;
        }

        if ($data['account_id'] !== $account_id) {
            $this->view->message = 'Ошибка! Учётная запись не совпадает.';
            return;
        }

        $response = $passwordRecovery->doReset($id, $account_id, $password);
        if ($response->hasNotSuccess()) {
            $msg = 'Ошибка!';
            $errors = $this->_getErrors($response);
            foreach ($errors as $error) {
                $msg .= '<br />' . $error['msg'];
            }
            $this->view->message = $msg;
            return;
        }
        $this->_redirect('/default/password-recovery/reset-success');
    }

    public function requestSuccessAction() {}

    public function resetSuccessAction() {}

    public function resetFailureAction() {}
}