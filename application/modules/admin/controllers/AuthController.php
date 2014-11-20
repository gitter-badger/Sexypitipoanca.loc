<?php
class Admin_AuthController extends Zend_Controller_Action
{
	protected $_flashMessenger = null;
	
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $bootstrap = $this->getInvokeArg('bootstrap');
        if($bootstrap->hasResource('db')) {
        	$this->db = $bootstrap->getResource('db');
        }
    }

    public function loginAction()
    {
        $form = new Admin_Form_Login();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/login.phtml'))));
        $this->view->form = $form;
        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
            	$dbAdapter = new Zend_Auth_Adapter_DbTable($this->db, 'j_accounts', 'username', 'password', 'MD5(?) AND status = "1" AND roleId = "1"');
            	$dbAdapter -> setIdentity($this->getRequest()->getPost('tbUser'))
            			   -> setCredential($this->getRequest()->getPost('tbPass'));
            	
            	$auth = Zend_Auth::getInstance();
            	$result = $auth->authenticate($dbAdapter);
            	if(!$result->isValid()) {
					switch($result->getCode()) {
					
					    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
					    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
								$this->_flashMessenger->addMessage("<div class='mess-false'>error</div>");
					        break;
					
					    default:
					        /** do stuff for other failure **/
								$this->_flashMessenger->addMessage("<div class='mess-false'>error</div>");
					        break;
					}
            	} else {
		        	$accountId = $dbAdapter->getResultRowObject();
		        	$model = new Default_Model_Accounts();
		        	$model->find($accountId->id);
		        	$storage = $auth->getStorage();
		        	$storage->write($model);
		       	}
           		$this->_redirect('/admin/auth/login');
            }
        }
    }

    public function logoutAction()
    {
    	$this->_helper->layout->disableLayout();
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) {
    		$auth->clearIdentity();
    	}
   		$this->_redirect('/admin/auth/login');
    }
}