<?php

class SocialController extends Zend_Controller_Action
{
    /**
     * init, initializes flash messenger
     */
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

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
}