<?php
class Admin_IndexController extends Zend_Controller_Action
{
	public function init()
	{
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if(null != $authAccount) {
			if(null!=$authAccount->getId()) {
				if($authAccount->getRoleId() != '1' && $authAccount->getRoleId() != '3') {
					$this->_helper->layout->disableLayout();

					$auth = Zend_Auth::getInstance();
					if($auth->hasIdentity()) {
						$auth->clearIdentity();
					}
					$this->_redirect('/admin/auth/login');
				}
			}
		}
		$this->_redirect('/admin/dashboard');
	}

	public function indexAction()
	{
//		$this->_redirect('/admin/auth/login');
	}
}



