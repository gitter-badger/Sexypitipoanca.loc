<?php
class Admin_UserController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
	{
		// BEGIN: DISPLAY SEARCH FORM
		$form = new Admin_Form_UserSearch();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/userSearch.phtml'))));
		$this->view->searchForm = $form;
		// END: DISPLAY SEARCH FORM
		
		$txtSearch = $this->getRequest()->getParam('txtHeaderSearch');
		if($this->getRequest()->getPost('txtHeaderSearch')){
			$txtSearch = $this->getRequest()->getPost('txtHeaderSearch');
		}		
		$form->txtHeaderSearch->setValue($txtSearch);
		
		$model = new Default_Model_AccountUsers();
		$select = $model->getMapper()->getDbTable()->select();
		if($txtSearch){
			$select->where("username LIKE '%".$txtSearch."%'");
		}
		else
		{
			// BEGIN: Sort by created interval
			if(isset($_SESSION['filterUserCreated']))
			{
				switch ($_SESSION['filterUserCreated']) {
					case 'today':
						$select->where('DAY(created) = ?', date('j', time()));
						$select->where('MONTH(created) = ?', date('n', time()));
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'yestarday':
						$select->where('DAY(created) = ?', date('j', time()-86400));
						$select->where('MONTH(created) = ?', date('n', time()));
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'this week':
						$select->where('WEEK(created) = ?', date('W', time()));
						$select->where('MONTH(created) = ?', date('n', time()));
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'last week':
						$select->where('WEEK(created) = ?', date('W', time()-604800));
						$select->where('MONTH(created) = ?', date('n', time()));
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'this month':
						$select->where('MONTH(created) = ?', date('n', time()));
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'last month':
						$select->where('MONTH(created) = ?', date('n', time()-2419200));
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'this year':
						$select->where('YEAR(created) = ?', date('Y', time()));
						break;
					case 'last year':
						$select->where('YEAR(created) = ?', date('Y', time()-29030400));
						break;
					default:
						// nuff said
						break;
				}
			}
			// END: Sort by created interval
			
			if(isset($_SESSION['userFilter']))
				$select->where('status = ?', $_SESSION['userFilter']=='inactive'?"0":"1");
			if(isset($_SESSION['userGender']))
				$select->where('gender = ?', $_SESSION['userGender']=='male'?"0":"1");
		}
		$select->order('created DESC');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setItemCountPerPage(50);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		$paginator->setPageRange(5);
		$this->view->customers = $paginator;
		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
		$this->view->totalItemCount = $paginator->getTotalItemCount();

		$param = array();
		if($txtSearch){
			$param = array('txtHeaderSearch' => $txtSearch);
		}
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial(array('_pagination.phtml', $param));
		
	}

	public function detailsAction()
	{
		$id = (int) $this->getRequest()->getParam('id');
		$model = new Default_Model_AccountUsers();
		if($model->find($id)){
			$this->view->user = $model;
			
			// BEGIN: User comments
			$modelComments = new Default_Model_CatalogProductComments();
			$select = $modelComments->getMapper()->getDbTable()->select()
				->where('userId = ?', $id);
			$resultComments = $modelComments->fetchAll($select);
			if($resultComments)
			{
				$this->view->comments = $resultComments;
			}
			// END: User comments
			
			// BEGIN: User favorite articles
			$modelFavorite = new Default_Model_CatalogProducts();
			$select = $modelFavorite->getMapper()->getDbTable()->select()
				->from(array('c' => 'j_catalog_products'), array('c.id', 'c.name'))
				->join(array('f' => 'j_catalog_product_favorites'), 'c.id = f.productId', array('f.userId', 'f.productId', 'added' => 'f.created'))
				->where('f.userId = ?', $id)
				->order('f.created DESC')
				->setIntegrityCheck(false);
			$resultFavorite = $modelFavorite->fetchAll($select);
			if($resultFavorite)
			{
				$this->view->favorite = $resultFavorite;
			}
			// END: User favorite articles
			
			// BEGIN: User friends
			$modelFriends = new Default_Model_SocialUserConnections();
			$select = $modelFriends->getMapper()->getDbTable()->select()
				->where('receiverUserId = ?', $id)
				->orwhere('initiatorUserId = ?', $id)
				->where('isConfirmed IS TRUE');
			$resultFriends = $modelFriends->fetchAll($select);
			if($resultFriends)
			{
				$this->view->friends = $resultFriends;
			}
			// END: User friends
		}else{
			$this->_redirect('/admin/user');
		}
	}
	
	public function messageAction()
	{
		$userId = $this->getRequest()->getParam('uid');
		$model = new Default_Model_AccountUsers();
		if($model->find($userId))
		{
			// BEGIN: Send form
			$form = new Admin_Form_Message();
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/message.phtml'))));
			$form->fill($model);
			$this->view->form = $form;
			// BEGIN: Send form
		
			if($this->getRequest()->isPost()){
				if($form->isValid($this->getRequest()->getPost())){
					// BEGIN: Send email
					$mail = new Zend_Mail();
					$mail->setFrom($form->getValue('fromEmail'), $form->getValue('fromName'));
					$mail->setSubject($form->getValue('subject'));
					$mail->setBodyHtml($form->getValue('message'));
					$mail->addTo($form->getValue('to'));
					if($mail->send())
					{
						$this->_flashMessenger->addMessage('<div class="mess-true">Your message was sent!</div>');
					}
					else{
						$this->_flashMessenger->addMessage('<div class="mess-false">Your message was not sent!</div>');
					}
					$this->_redirect('/admin/user');
					// END: Send email
				}
			}
		}
		else
		{
			$this->_flashMessenger->addMessage('<div class="mess-false">Invalid user!</div>');
			$this->_redirect('/admin/user');
		}
	}
	
	public function deleteAction()
	{
		$uid = (int) $this->getRequest()->getParam('uid');
		$model = new Default_Model_AccountUsers();
		if($model->find($uid)){
			if($model->delete()){
				$this->_flashMessenger->addMessage('<div class="mess-true">Success! User was deleted.</div>');
			}else{
				$this->_flashMessenger->addMessage('<div class="mess-false">Error! User could not be deleted.</div>');
			}
		}
		else
		{
			$this->_flashMessenger->addMessage('<div class="mess-false">Error! Invalid user selected.</div>');
		}
		$this->_redirect('/admin/user');
	}
	
	public function filterAction()
	{
		// BEGIN: Sort by time
		if($_SESSION['filterUserCreated']) unset($_SESSION['filterUserCreated']);
		
		$interval = $this->getRequest()->getParam('interval');
		if($interval) $_SESSION['filterUserCreated'] = $interval;
		// END: Sort by time
		
		// BEGIN: Sort by status
		if($_SESSION['userFilter']) unset($_SESSION['userFilter']);
		
		$status = $this->getRequest()->getParam('status');
		if($status) $_SESSION['userFilter'] = $status;
		// END: Sort by status
		
		// BEGIN: Sort by gender
		if($_SESSION['userGender']) unset($_SESSION['userGender']);
		
		$gender = $this->getRequest()->getParam('gender');
		if($gender) $_SESSION['userGender'] = $gender;
		// END: Sort by gender
		
		$this->_redirect('/admin/user');
	}

	public function statusAction()
	{
		$id = (int) $this->getRequest()->getParam('id');
		$model = new Default_Model_AccountUsers();
		if($model->find($id)){
			if($model->getStatus() == '1'){
				$model->setStatus('0');
			}else{
				$model->setStatus('1');
			}

			if($model->save()){
				$this->_flashMessenger->addMessage('<div class="mess-true">Success! Status was changed.</div>');
			}else{
				$this->_flashMessenger->addMessage('<div class="mess-false">Error! Status could not be changed.</div>');
			}
		}
		$this->_redirect('/admin/user');
	}
}