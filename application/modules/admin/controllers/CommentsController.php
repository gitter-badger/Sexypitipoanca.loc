<?php
class Admin_CommentsController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->order('added DESC');
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->result = $paginator;
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
	}
	
	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_CatalogProductComments();
		if($model->find($id)) {
			$form = new Admin_Form_Comments();
			$form->edit($model);
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/comments.phtml'))));
			$this->view->form = $form;
			if($this->getRequest()->isPost()){
				if($form->isValid($this->getRequest()->getPost())){
					$comment = $form->getValue('comment');
					$model->setComment($comment);
					if($model->save()){
						$this->_flashMessenger->addMessage('<div class="mess-true">Comment successfully moderated!</div>');
						$this->_redirect('/admin/comments/');		
					}else{
						$this->_flashMessenger->addMessage('<div class="mess-false">Error moderating comment!</div>');	
					}
				}
			}
		} 
	}
	
	public function deleteAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = new Default_Model_CatalogProductComments();
		if($model->find($id)) {
			if($model->delete()) {
					$this->_flashMessenger->addMessage('<div class="mess-true">Commentul a fost sters cu succes!</div>');
				} else {
					$this->_flashMessenger->addMessage('<div class="mess-false">Eroare stergere comment!</div>');
				}				
		}
		$this->_redirect('/admin/comments/');
	}
}