<?php
class Admin_NotificationsController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
	}
	
	public function indexAction()
	{
		$form = new Admin_Form_Notification();		
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/notifications.phtml'))));
		$this->view->form = $form;
		
		if($this->getRequest()->isPost()){
			if($form->isValid($this->getRequest()->getPost())){
				$posts = $this->getRequest()->getPost();
				$sent = true;
				$notification = new Default_Model_Notifications();
				$notification->setContent($posts['message']);
				$all = (!empty($posts['all']))?$posts['all']:'0';
				$notification->setToAll($all);
				$id = $notification->save();
				if(!$id){
					$sent = false;
				}else{
					if(empty($posts['all'])){					
						foreach ($posts['user'] as $value) {
							$notificationUser = new Default_Model_NotificationUsers();
							$notificationUser->setSentTo($value);
							$notificationUser->setNotificationId($id);
							if(!$notificationUser->save()){
								$sent = false;
							}
						}
					}else{
						$model = new Default_Model_AccountUsers();
						$select = $model->getMapper()->getDbTable()->select();				
						if(($result = $model->fetchAll($select))) {
							foreach($result as $value) {
								$notificationUser = new Default_Model_NotificationUsers();
								$notificationUser->setSentTo($value->getId());
								$notificationUser->setNotificationId($id);
								if(!$notificationUser->save()){
									$sent = false;
								}
							}
						}	
					}
					
				}	
				if($sent){
					$this->_flashMessenger->addMessage('<div class="mess-true">Notification successfully sent!</div>');		
				}else{
					$this->_flashMessenger->addMessage('<div class="mess-false">Error sending notifications!</div>');	
				}
					
				$this->_redirect('/admin/notifications');
			}
		}	
	}
	
	public function sentAction()
	{
		$messageModel = new Default_Model_Notifications();
		$select = $messageModel->getMapper()->getDbTable()->select()				
//				->where('`from` IS NULL')		
				->order('created DESC');		
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
	
	public function inboxAction()
	{
		$messageModel = new Default_Model_Notifications();
		$select = $messageModel->getMapper()->getDbTable()->select()
				->where('`from` IS NOT NULL')
				->group('from')
				->order('created DESC');		
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
	
	public function deleteAction()
	{
		$sent = true;
		$id = $this->getRequest()->getParam('id');
		$messageModel = new Default_Model_Notifications();
		if($messageModel->find($id)){
			if($messageModel->delete()){
				//delete from notificationUsers 
				$table = new Default_Model_NotificationUsers(); 
				$where = $table->getMapper()->getDbTable()->getAdapter()->quoteInto('notificationId = ?', $id);				
				if(!$table->getMapper()->getDbTable()->delete($where)){
					$sent = false;
				}
			}			
		}else{
			$sent = false;
		}
		
		if($sent){
			$this->_flashMessenger->addMessage('<div class="mess-true">Notification successfully deleted!</div>');		
		}else{
			$this->_flashMessenger->addMessage('<div class="mess-false">Error deleting notifications!</div>');	
		}

		$this->_redirect('/admin/notifications/sent');
	}
	
	public function detailsAction()
	{
//		$id = $this->getRequest()->getParam('userId');
//		
//		$messageModel = new Default_Model_Notifications();
//		$select = $messageModel->getMapper()->getDbTable()->select()
//				->from(array('nu'=>'notifications'))
//				->joinLeft(array('u'=>'notification_users'),'nu.id = u.notificationId',array('ucreated'=>'u.created','read'=>'u.read'))
//				->where('u.sentTo = ?',$id)
//				->orWhere('nu.toAll = ?','1')
//				->orWhere('nu.from = ?',$id)
//				->order('nu.created DESC')
//				->setIntegrityCheck(false);	
//		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
//		$paginator->setItemCountPerPage(10);
//		$paginator->setCurrentPageNumber($this->_getParam('page'));
//		$paginator->setPageRange(5);
//		$this->view->result = $paginator;
//		$this->view->itemCountPerPage = $paginator->getItemCountPerPage();
//		$this->view->totalItemCount = $paginator->getTotalItemCount();
//
//		Zend_Paginator::setDefaultScrollingStyle('Sliding');
//		Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');		
	}
}	
