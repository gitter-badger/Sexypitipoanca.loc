<?php
class CatalogController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
	}

	public function indexAction()
	{
		$this->_redirect('/');
	}
	
	public function scoreAction()
	{
		$tsStatistics = new TS_Statistics();
		$tsProducts = new TS_Products();
//		$topRated = $tsProducts->getTopGalleries('rating', 'gallery', null);
		
		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
//				->where('id IN (?)', array_keys($topRated))
				->where('status = ?', '1')
				->order('added DESC');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->result = $paginator;
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
	}

	public function categoriesAction()
	{
		$id = $this->getRequest()->getParam('id');
		if($id != null){
			$this->view->seoId = $id;
		}

		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
				->where('category_id = ?', $id)
				->where('status = ?', '1')
				->order('added DESC');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->result = $paginator;
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
		
	}

	public function tagsAction()
	{
		$tag = $this->getRequest()->getParam('tag');

		$model = new Default_Model_Tags();
		$select = $model->getMapper()->getDbTable()->select()
				->where('name = ?', $tag);
		$result = $model->fetchAll($select);
		if(null != $result)
		{
			$model2 = new Default_Model_CatalogProducts();
			$select = $model2->getMapper()->getDbTable()->select()
					->from(array('p'=>'j_catalog_products'), array('p.id', 'p.name', 'p.type'))
					->join(array('t'=>'j_catalog_product_tags'), 'p.id = t.product_id', array('t.product_id', 't.tag_id'))
					->where('t.tag_id = ?', $result[0]->getId())
					->setIntegrityCheck(false);
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange(5);
			$this->view->result = $paginator;
			$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
			$this->view->totalItemCount = $paginator->getTotalItemCount();

			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
			
		}
	}

	public function cloudtagAction()
	{
		$tag = $this->getRequest()->getParam('tag');

		$model = new Default_Model_Tags();
		$select = $model->getMapper()->getDbTable()->select()
				->where('name = ?', $tag);
		if(($result = $model->fetchAll($select))) {
			$model2 = new Default_Model_CatalogProducts();
			$select = $model2->getMapper()->getDbTable()->select()
					->from(array('p'=>'j_catalog_products'), array('p.id', 'p.name', 'p.type'))
					->join(array('t'=>'j_catalog_product_tags'), 'p.id = t.product_id', array('t.product_id', 't.tag_id'))
					->where('t.tag_id = ?', $result[0]->getId())
					->setIntegrityCheck(false);
			$result2 = $model2->fetchAll($select);
			if(NULL != $result2){
				$paginator = Zend_Paginator::factory($result2);
				$paginator->setItemCountPerPage(10);
				$paginator->setCurrentPageNumber($this->_getParam('page'));
				$paginator->setPageRange(5);
				$this->view->result = $paginator;
				$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
				$this->view->totalItemCount = $paginator->getTotalItemCount();

				Zend_Paginator::setDefaultScrollingStyle('Sliding');
				Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
			}
		}
	}

	public function productDetailsAction()
	{
		$id = $this->getRequest()->getParam('id');
		$this->view->product_id = $id;
		$loggedIn = false;

		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if($authAccount){
			if($authAccount->getId()){
				$loggedIn = true;
				
				$account = new Default_Model_AccountUsers();
				$account->find($authAccount->getId());
				$this->view->loggedInUserId = $authAccount->getId();
			}
		}

		// BEGIN: THE FUCK?
		$model = new Default_Model_CatalogProductVisits();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id);
		if(($result = $model->fetchAll($select))){
			$result[0]->setVisits($result[0]->getVisits()+1);
			$result[0]->save();
		}else{
			$model->setProduct_id($id);
			$model->setVisits(1);
			$model->save();
		}
		// END: THE FUCK?

		$model = new Default_Model_CatalogProducts();
		if($model->find($id)){
			if($model->getStatus() == 1) // gallery active
			{
				$this->view->result = $model;				
			}else
			{
				throw new Zend_Controller_Action_Exception('Your message here', 404);
			}

			// BEGIN: FETCH 5 SIMILAR GALLERIES
			$similar = new Default_Model_CatalogProducts();
			$select = $similar->getMapper()->getDbTable()->select()
					->where('category_id = ?', $model->getCategory_id())
					->where('status = ?', '1')
					->limit('5');
			$similare = $similar->fetchAll($select);
			$this->view->similare = $similare;
			// END: FETCH 5 SIMILAR GALLERIES

			// BEGIN: REDIRECT FOR 18+ CATEGORIES
			$censored = false;
			if($loggedIn == false){
				if(null != $model->getCategory_id()){
					if($model->getCategory_id() == 8 || $model->getCategory_id() == 2){
						$censored = true;
					}
				}
			}
			$this->view->censored = $censored;
			// END: REDIRECT FOR 18+ CATEGORIES
		}

		$form = new Default_Form_Catalog();
		$form->comentAdd();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/catalog/product-coment-add.phtml'))));
		$this->view->form = $form;

		if($this->getRequest()->isPost()) {
			if($this->getRequest()->getPost('control') == 'comentAdd') {
				if($form->isValid($this->getRequest()->getPost())) {
					if($loggedIn == true){
						$model = new Default_Model_CatalogProductComments();
						$model->setProduct_id($id);
						$model->setUserId($account->getId());
						$model->setName($account->getUsername());
						$model->setComment($form->getValue('message'));
						if($model->save()){
							$this->_flashMessenger->addMessage('<div class="mess-true">Comentariul tau a fost salvat!</div>');
						}else{
							$this->_flashMessenger->addMessage('<div class="mess-false">nu</div>');
						}
						$this->_redirect('/catalog/product-details/id/'.$id.'/#comment');
					}else{
						$this->_flashMessenger->addMessage('<div class="mess-false">Te rugam sa te autentifici!</div>');
						$this->_redirect('/catalog/product-details/id/'.$id.'/#comment');
					}
				}
			}
		}
	}

	public function searchAction()
	{
		$search = $this->getRequest()->getParam('search');
		$search = urldecode($search);
		if(null != $search){
			$this->view->cautare = $search;
		}

		$model = new Default_Model_CatalogProducts();
		$select = $model->getMapper()->getDbTable()->select()
				->where('name LIKE (?)', '%'.$search.'%')
				->where('status = ?', '1')
				->order('added DESC');
		if(($result = $model->fetchAll($select))) {
			$paginator = Zend_Paginator::factory($result);
			$paginator->setItemCountPerPage(10);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			$paginator->setPageRange(5);
			$this->view->result = $paginator;
			$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
			$this->view->totalItemCount = $paginator->getTotalItemCount();

			$param = array('search' => urlencode($search));
			
			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial(array('_pagination.phtml', $param));
		}
	}

	public function addtofavoritesAction()
	{
		$id = (int) $this->getRequest()->getParam('id');
		$redirUser = $this->getRequest()->getParam('ru');
		$type = $this->getRequest()->getParam('type')?$this->getRequest()->getParam('type'):"favorite";

		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if($authAccount){
			if($authAccount->getId()) {
				$account = new Default_Model_AccountUsers();
				$account->find($authAccount->getId());

				if(null != $id){
					$model = new Default_Model_CatalogProducts();
					if($model->find($id)){
						$favorites = new Default_Model_CatalogProductFavorites();
						$favorites->setUserId($authAccount->getId());
						$favorites->setProductId($model->getId());
						$favorites->setType('favorite');
						$favorites->save();
						if($redirUser)
						{
							$this->_redirect('/user/'.$redirUser);
						}
						else
						{
							$this->_redirect('/catalog/product-details/id/'.$id.'/#favorite');
						}
					}
				}
			}else{
				$this->_flashMessenger->addMessage('<div class="mess-false">Te rugam sa te autentifici!</div>');
				$this->_redirect('/catalog/product-details/id/'.$id.'/#favorite');
			}
		}else{
			$this->_flashMessenger->addMessage('<div class="mess-false">Te rugam sa te autentifici!</div>');
			$this->_redirect('/catalog/product-details/id/'.$id.'/#favorite');
		}
	}

	public function tagcloudAction()
	{
		
	}

	public function topratedAction()
	{
		
	}
}