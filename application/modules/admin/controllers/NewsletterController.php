<?php
class Admin_NewsletterController extends Zend_Controller_Action
{
	public function init()
	{
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
	}

	public function indexAction()
	{
		
	}
	
	public function subscribersAction()
	{
		$model = new Default_Model_NewsletterSubscribers();
		$select = $model->getMapper()->getDbTable()->select()
				->order('added DESC');
		$result = $model->fetchAll($select);
		if(null != $result){
			$paginator = Zend_Paginator::factory($result);
			$paginator->setItemCountPerPage(25);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange(5);
			$this->view->subscribers = $paginator;
			$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
			$this->view->totalItemCount = $paginator->getTotalItemCount();

			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
		}
	}
}