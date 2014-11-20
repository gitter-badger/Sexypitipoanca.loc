<?php
class CmsController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
	}

	public function viewAction()
	{
		$page = $this->getRequest()->getParam('page');
		$model = new Default_Model_Cms();
		$select = $model->getMapper()->getDbTable()->select()
				->where('link = ?', $page);
		if(($result = $model->fetchAll($select))) {
			$this->view->page = $result[0];
		}
	}
}