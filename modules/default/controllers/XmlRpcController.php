<?php

class XmlRpcController extends OSDN_Controller_Action
{
    /**
     * Basic authenticate way
     *
     * wait for the account and password, check
     * and if not success redirect to the error page
     *
     */
    public function init()
    {
    	ini_set('max_execution_time' , '180');

	    $config = array(
		    'accept_schemes'    => 'basic',
	    	'realm'				=> 'XML-RPC authentication',
		    'digest_domains'    => '/xml-rpc/index',
		    'nonce_timeout'     => 3600
		);

		$adapter = new Zend_Auth_Adapter_Http($config);

		$basicResolver = new Zend_Auth_Adapter_Http_Resolver_File();
		$basicResolver->setFile(ROOT_DIR . '/setup/_files/passwd.txt');

		$adapter->setBasicResolver($basicResolver);
		$adapter->setRequest(new Zend_Controller_Request_Http());
		$adapter->setResponse(new Zend_Controller_Response_Http());

		$auth = Zend_Auth::getInstance();
		$auth->setStorage(new Zend_Auth_Storage_NonPersistent());
		$result = $auth->authenticate($adapter);

		if (true !== $result->isValid()) {
		    echo 'Error 403. Unauthorized access.';
	        $this->_redirectOnDenied();
	        return;
	    }

	    return;

	    $locale = $this->_getParam('locale');
	    if (!empty($locale)) {
	    	OSDN_Language::setDefaultLocale($locale, true);
	    }

        $accounts = OSDN_Accounts_Prototype::model();
        $response = $accounts->fetchAnonymousAccount();

        do {
        	if (!$response->isSuccess()) {
        		break;
        	}

        	$anonymous = $response->row;
        	if (empty($anonymous)) {
        		break;
        	}

        	Zend_Auth::getInstance()->getStorage()->write((object) $anonymous);
            parent::init();

            return;

        } while(false);

        $this->_redirectOnDenied();
    }

    protected function _redirectOnDenied()
    {
        $this->getRequest()->setControllerName('error')
            ->setActionName('denied')
            ->setDispatched(false);

        exit;
    }

    /**
     * The main point for access to webservice
     * Before assess to this action send login and password
     * <code>
     *  array(
     *      'account'   => mylogin
     *      'password   => mypass
     *  );
     * </code>
     *
     * If your authorization is not success you will got and
     * 405 apache error with message "Permission denied"
     * or if exception catched on server side then 500 apache error.
     * Always use the response object status to define the status operation.
     *
     * Initialize the XML-RPC service
     *
     * @return void
     */
    public function indexAction()
    {
        $this->disableRender(true);

        $cacheFile = CACHE_DIR . '/xmlrpc.cache';

        $server = new OSDN_XmlRpc_Server();
        if (OSDN_DEBUG || !Zend_XmlRpc_Server_Cache::get($cacheFile, $server)) {

            $server->setClass('BS_XmlRpc_Accounts', 'Accounts');

            Zend_XmlRpc_Server_Cache::save($cacheFile, $server);
        }

        echo $server->handle();
    }
}