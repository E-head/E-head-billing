<?php

class Customer_DocumentsController extends OSDN_Controller_Action
{
    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->customers);
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'index');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'act');
    }

    public function indexAction()
    {
        $this->view->page = 'documents';
    }

    public function actAction()
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
        $data['act_date'] = $dateObj->toString('dd MMMM Y');

        $data['act_number'] = $dateObj->toString('Mddss');

        $data['price'] = BS_TariffPlans::getCost($data['tariff']);

        $data['price_letter'] = OSDN_Plural::asString($data['price'],
            OSDN_Plural::MALE, array('рубль', 'рубля', 'рублей'));

        $data['customer_id'] = $data['id'];

        $payerName = empty($params['customer_name'])
                   ? (1 == $data['iscompany']) ? $data['company_name'] : $data['name']
                   : $params['customer_name'];

        $data['customer_name'] = htmlspecialchars($payerName);
        $this->view->assign($data);
    }
}