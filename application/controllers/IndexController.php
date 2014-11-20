<?php
class IndexController extends Zend_Controller_Action
{
	public function init()
    {
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{
		$model = new Default_Model_CatalogProducts();
    	$select = $model->getMapper()->getDbTable()->select()
    			->where("(`type` = 'gallery' OR `type` = 'embed' OR `type` = 'video')")
				->where('status = ?', '1')
				->order('added DESC');
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->result = $paginator;
		$this->view->curentPage = $this->_getParam('page');
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
    	
	}
}