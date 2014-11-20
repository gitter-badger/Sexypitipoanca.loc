<?php
class NewsController extends Zend_Controller_Action
{
	public function init()
    {
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{
    	$model = new Default_Model_News();
    	$select = $model->getMapper()->getDbTable()->select();
    	if(($result = $model->fetchAll($select))) {
			$paginator = Zend_Paginator::factory($result);
			$paginator->setItemCountPerPage(25);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange(10);
			$this->view->result = $paginator;
			$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
			$this->view->totalItemCount = $paginator->getTotalItemCount();

			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
    	}
	}

	public function detailsAction()
	{
		$id = $this->getRequest()->getParam('id');
    	$model = new Default_Model_News();
    	$select = $model->getMapper()->getDbTable()->select()
				->where('id  = ?', $id)
				;
    	if(($result = $model->fetchAll($select))) {
			$this->view->result = $result;
    	}
	}

	public function subscribeAction()
    {
		$form = new Default_Form_Newsletter();
		$form->subscribe();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/subscribe-errors.phtml'))));
		$this->view->form = $form;
		

		if($this->getRequest()->isPost()){
			if($this->getRequest()->getPost('control') == 'newsletter') {
				if($form->isValid($this->getRequest()->getPost())){
					$model = new Default_Model_NewsletterSubscribers();
					$model->setOptions($this->getRequest()->getPost());;
					if($model->save())
					{
						$this->_redirect('/');
					}else{
						$this->_redirect('/');
					}
				}
			}
		}
	}

	public function unsubscribeAction()
    {
		$form = new Default_Form_Unsubscribe();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/Unsubscribe.phtml'))));
		$this->view->form = $form;
		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getPost())) {
				$unsubscribe = $this->getRequest()->getPost('unsubscribe');
				$model = new Default_Model_Newsletter();
				$select = $model->getMapper()->getDbTable()->select()
						->where('unsubscribe  = ?', $unsubscribe)
						;
				if(($result = $model->fetchAll($select))) {
					if($result[0]->delete()) {
						$this->_flashMessenger->addMessage('<div class="mess-true">Ai fost dezabonat cu succes!</div>');
						$this->_redirect('/news/unsubscribe');
					} else {
						$this->_flashMessenger->addMessage('<div class="mess-false">Eroare dezabonare</div>');
						$this->_redirect('/news/unsubscribe');
					}
				}
				else
				{
					$this->_flashMessenger->addMessage('<div class="mess-false">Eroare! Nu m gasit adresa in baza de date!</div>');
					$this->_redirect('/news/unsubscribe');
				}				
			}
		}
    }
}