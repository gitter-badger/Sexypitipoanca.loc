<?php
class Admin_MarketingController extends Zend_Controller_Action
{
	public function init()
	{
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
	}

	public function indexAction()
	{
		// BEGIN: Fetch commercials
		$result = TS_Marketing::fetchCommercials();
		if($result)
		{
			$this->view->commercial = $result;
		}
		// END: Fetch commercials
		
		// BEGIN: Save new commercial
		if($this->getRequest()->isPost()){
			$code = $this->getRequest()->getParam('code');
			if(TS_Marketing::saveCommercial($code))
			{
				$this->_redirect('/admin/marketing');
			}
		}
		// END: Save new commercial
	}
	
	public function analyticsAction()
	{
		
	}
}