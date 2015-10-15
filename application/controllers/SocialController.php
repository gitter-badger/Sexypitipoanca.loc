<?php

class SocialController extends Base_Controller_Action
{
    /**
     * Delete conversations from a specific user
     * @throws Zend_Exception
     */
    public function deleteBulkMessagesAction()
    {
        $displayUserId = $this->getRequest()->getParam('uid');
        $loggedInUserId = Zend_Registry::get('authUser')->getId();

        $messageModel = new Default_Model_Message();
        $select = $messageModel->getMapper()->getDbTable()->select()
            ->where("`sentTo` = $loggedInUserId AND `from` = $displayUserId")
            ->orwhere("`from` = $loggedInUserId AND `sentTo` = $displayUserId");
        $result = $messageModel->fetchAll($select);
        if(null != $result)
        {
            foreach($result as $value)
            {
                $value->delete();
            }
        }
        $this->_redirect('/account/messages');
    }

    /**
     * Shows system messages
     * @throws Zend_Exception
     */
    public function systemAction()
    {
        if(Zend_Registry::isRegistered('authUser')){
            $id = Zend_Registry::get('authUser')->getId();
            $messageModel = new Default_Model_Notifications();
            $select = $messageModel->getMapper()->getDbTable()->select()
                ->from(array('nu'=>'notifications'))
                ->joinLeft(array('u'=>'notification_users'),'nu.id = u.notificationId',array('ucreated'=>'u.created','read'=>'u.read'))
                ->where('u.sentTo = ?',$id)
                ->order('nu.created DESC')
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
        }else{
            $this->_redirect('/');
        }
    }
}