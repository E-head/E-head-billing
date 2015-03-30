<?php

class TestController extends OSDN_Controller_Action
{
    public function indexAction()
    {
        $this->disableRender(true);
//        $class = new BS_XmlRpc_Accounts();
//        print_r($class->customers());
    }
}