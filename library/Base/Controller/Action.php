<?php
/**
 * Created by PhpStorm.
 * User: sergiu
 * Date: 9/23/15
 * Time: 2:04 PM
 */
class Base_Controller_Action extends Zend_Controller_Action
{
    /**
     * init, initializes flash messenger
     */
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

    /**
     * @param $formPassword
     * @param $account
     */
    protected function changePassword($formPassword, $account)
    {
        if($formPassword->isValid($this->getRequest()->getPost())) {
            $post = $this->getRequest()->getPost();
            if (md5($post['oldPassword']) == $account->getPassword()) {
                $account->setPassword(md5($post['password']));
                if($account->save()) {
                    $this->_flashMessenger->addMessage('<span class="mess-true">Modificarile au fost efectuate cu succes</span>');
                }else{
                    $this->_flashMessenger->addMessage('<span class="mess-true">Eroare! Parola nu a putut fi modificata.</span>');
                }
            } else{
                $this->_flashMessenger->addMessage('<span class="mess-false">Eroare! Parola veche eronata!</span>');
            }
        }
    }

    /**
     * handle request, used to handle social request between users
     * @param bool $accept
     * @throws Exception
     */
    protected function handleRequest($accept = true)
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_SocialUserConnections();
        if ($model->find($id)) {
            if ($accept) {
                $model->setIsConfirmed(1);
                $model->save();
            } else {
                $model->delete();
            }
        }
        $this->_redirect('/account/user-requests');
    }

    /**
     * Check if user authenticated and return account model if so
     * @return bool|mixed
     */
    protected function isAuthenticated()
    {
        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null != $authAccount) {
            $result = $authAccount;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Safely delete files
     * @param $filePaths
     */
    protected function safeDelete($filePaths)
    {
        $filePathData = (array) $filePaths;

        foreach ($filePathData as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            } else {
                // ToDo: catch exception
            }
        }
    }
}