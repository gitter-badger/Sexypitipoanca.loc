<?php
class ExportController extends Zend_Controller_Action
{
	public function init()
    {
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{

	}
}