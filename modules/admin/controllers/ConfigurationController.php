<?php



class Admin_ConfigurationController extends OSDN_Configuration_Controllers_Abstract
{
    
    protected $_periods = array(
        300     => '*/5 * * * *',
        900     => '*/15 * * * *',
        1800    => '*/30 * * * *',
        3600    => '0 0-23 * * *',
        21600   => '0 */6 * * *',
        43200   => '0 */12 * * *', 
        86400   => '0 */24 * * *'
    );
    
    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->admin->configuration);
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'load-system-settings');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'save-system-settings');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-notification-keywords');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-notification-keywords-manager');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-reminder-hr-keywords');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-reminder-exit-form-keywords');
        
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-jobs');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-periods');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'get-cron-common-data');
        $acl->isAllowed(OSDN_Acl_Privilege::VIEW, 'run-job');
        
        
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'update-job');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'delete-job');
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'set-cron-common-data');
        
    }
    
    public function getNotificationKeywordsAction()
    {
        $inv = new CA_Invitation();
        $this->view->data = $inv->getKeywords();
        $this->view->success = true;
    }
    
    public function getNotificationKeywordsManagerAction()
    {
        $inv = new CA_Invitation();
        $this->view->data = $inv->getKeywordsManager();
        $this->view->success = true;
    }
    
    public function getReminderHrKeywordsAction()
    {
        $reminderHr = new CA_Reminder_Hr();
        $this->view->data = $reminderHr->getKeywords();
        $this->view->success = true;
    }
    
    public function getReminderExitFormKeywordsAction()
    {
        $reminderHr = new CA_Reminder_StudentExit();
        $this->view->data = $reminderHr->getKeywords();
        $this->view->success = true;
    }
    
    public function runJobAction()
    {
        $synchronization = new CA_Synchronization();
        switch ($this->_getParam('id')) {
            case 1:
                $res = $synchronization->runTranslationSynchronization();
            break;
            case 2:
                $res = $synchronization->runCatalogueSynchronization();
            break;
            case 3:
				$reminder = new CA_Reminder_Hr();
				$res = $reminder->checkHours();
            break;
            case 4:
                $reminder = new CA_Reminder_Hr();
                $res = $reminder->sendReminder();
            break;
        }
        if ($res && $res->isError()) {
            $this->_collectErrors($res);
            $this->view->success = false;
            return;
        }
        $this->view->assign($res->data);
        $this->view->success = true;
    }
    
    public function getCronCommonDataAction()
    {
        $email = OSDN_Cron::factory()->getParam('MAILTO');
        $this->view->email = $email;
        $this->view->success = $email? true: false;
    }
    
    public function setCronCommonDataAction()
    {
        OSDN_Cron::factory()->add(new OSDN_Cron_Unix_Assign('MAILTO', $this->_getParam('email')));
        $this->view->success = true;
    }
    
    public function getJobsAction()
    {
        $ca_reupdate = Zend_Registry::get('dynamic_config')->get('reupdate_datetime_of_catalogue_synchronization');
        $tr_reupdate = Zend_Registry::get('dynamic_config')->get('reupdate_datetime_of_translation_synchronization');
        $this->view->data = array(
            array(
                'id'   => 1,
                'name' => 'Translation',
                'lastupdate' => Zend_Registry::get('dynamic_config')->get('last_datetime_of_translation_synchronization'),
                'reupdate' => $tr_reupdate,
                'exist'     => ($cmd = OSDN_Cron::factory()->findTaskByCommand($this->getCommand(1)))
                    ? (stripslashes($cmd->getParams()) == $this->_periods[$tr_reupdate]): false
                
            ),
            array(
                'id'   => 2,
                'name' => 'Catalogue',
                'lastupdate' => Zend_Registry::get('dynamic_config')->get('last_datetime_of_catalogue_synchronization'),
                'reupdate' => $ca_reupdate,
                'exist'     => ($cmd = OSDN_Cron::factory()->findTaskByCommand($this->getCommand(2)))
                    ? (stripslashes($cmd->getParams()) == $this->_periods[$ca_reupdate]): false
            ),
            array(
                'id'   => 3,
                'name' => 'Remind Hoursegistration input',
                'lastupdate' => Zend_Registry::get('dynamic_config')->get('last_datetime_remind_hourregistration_input'),
                'reupdate' => $ca_reupdate,
                'exist'     => ($cmd = OSDN_Cron::factory()->findTaskByCommand($this->getCommand(3)))
                    ? (stripslashes($cmd->getParams()) == $this->_periods[$ca_reupdate]): false
            ),
            array(
                'id'   => 4,
                'name' => 'Send Reminds for Hoursegistration input',
                'lastupdate' => Zend_Registry::get('dynamic_config')->get('last_datetime_send_remind_hourregistration_input'),
                'reupdate' => $ca_reupdate,
                'exist'     => ($cmd = OSDN_Cron::factory()->findTaskByCommand($this->getCommand(4)))
                    ? (stripslashes($cmd->getParams()) == $this->_periods[$ca_reupdate]): false
            )
        );
    }
    
    public function updateJobAction() {
        if (!$this->_getParam('id') || !$this->_getParam('reupdate')) {
            $this->view->success = false;
            return;
        }
        $command = $this->getCommand($this->_getParam('id'));
        $task = new OSDN_Cron_Unix_Cmd(
            $this->_periods[$this->_getParam('reupdate')]. $command
        );
        $res = OSDN_Cron::factory()->add($task)->findTaskByCommand($command);
        if (!$res) {
            $this->view->success = false;
            return;
        }
        
        Zend_Registry::get('dynamic_config')
            ->saveSystemSettings(array(
                $this->getParamName($this->_getParam('id'))=> 
                $this->_getParam('reupdate')
            ));
        $this->view->success = true;
    }
    
    public function deleteJobAction() {
        if (!$this->_getParam('id')) {
            $this->view->success = false;
            return;
        }
        $command = $this->getCommand($this->_getParam('id'));
        
        $task = OSDN_Cron::factory()->findTaskByCommand($command);
        if ($task) {
            OSDN_Cron::factory()->deleteByCommand($task);
        }
        Zend_Registry::get('dynamic_config')->saveSystemSettings(array(
            $this->getParamName($this->_getParam('id')) => ''
        ));
        $this->view->success = true;
    }
    
    public function getPeriodsAction()
    {
        $data = array();
        foreach ($this->_periods as $k => $p) {
            $data[] = array('name' => $p, 'value' => $k);
        }
        $this->view->data = $data; 
    } 
     
    protected function getCommand($id)
    {
        switch ($id) {
            case 1:
                return " /usr/bin/lynx -source " . $this->_helper->baseUrl(true) . '/json/default/index/synchronize-translations'/*$this->link('synchronize-translations', 'index', 'default')*/;
            break;
            case 2:
                return " /usr/bin/lynx -source " . $this->_helper->baseUrl(true) . '/json/default/index/synchronize-catalogue'/*$this->link('synchronize-catalogue', 'index', 'default')*/;
            break;
            case 3:
                return " /usr/bin/lynx -source " . $this->_helper->baseUrl(true) . '/json/default/index/check-reminder';
            break;
            case 3:
                return " /usr/bin/lynx -source " . $this->_helper->baseUrl(true) . '/json/default/index/check-reminder';
            break;
            case 4:
                return " /usr/bin/lynx -source " . $this->_helper->baseUrl(true) . '/json/default/index/send-reminder';
            break;
        }
    } 
    protected function getParamName($id) 
    {
        switch ($id) {
            case 1:
                return 'reupdate_datetime_of_translation_synchronization';
            break;
            case 2:
                return 'reupdate_datetime_of_catalogue_synchronization';
            break;
			case 3:
                return 'make_reminds_for_hour_registration_iunput';
            break;
            case 4:
                return 'send_make_reminds_for_hour_registration_iunput';
            break;
        }
    }
    
}
