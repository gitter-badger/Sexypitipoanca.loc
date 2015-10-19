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
        if ($formPassword->isValid($this->getRequest()->getPost())) {
            $post = $this->getRequest()->getPost();
            if (md5($post['oldPassword']) == $account->getPassword()) {
                $account->setPassword(md5($post['password']));
                if ($account->save()) {
                    $this->_flashMessenger->addMessage('<span class="mess-true">Modificarile au fost efectuate cu succes</span>');
                } else {
                    $this->_flashMessenger->addMessage('<span class="mess-true">Eroare! Parola nu a putut fi modificata.</span>');
                }
            } else {
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
        $filePathData = (array)$filePaths;

        foreach ($filePathData as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            } else {
                // ToDo: catch exception
            }
        }
    }

    protected function safeRemoveDir($dirNames)
    {
        $dirNameData = (array)$dirNames;

        foreach ($dirNameData as $dirName) {
            if (is_dir($dirName)) {
                rmdir($dirName);
            } else {
                // ToDo: catch exception
            }
        }
    }

    /**
     * paginate result and send it to Zend_View
     * @param $result
     * @throws Zend_Paginator_Exception
     */
    protected function paginateResult($result)
    {
        $paginator = Zend_Paginator::factory($result);
        $paginator->setItemCountPerPage(25);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setPageRange(5);
        $this->view->result = $paginator;
        $this->view->itemCountPerPage = $paginator->getItemCountPerPage();
        $this->view->totalItemCount = $paginator->getTotalItemCount();

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('_pagination.phtml');
    }

    /**
     * paginate result and send it to Zend_View
     * @param $select
     * @throws Zend_Paginator_Exception
     */
    protected function paginateSelect($select)
    {
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

    /**
     * subscribe to newsletter
     * @param $email
     * @return bool
     */
    protected function subscribe($email)
    {
        $model = new Default_Model_NewsletterSubscribers();
        $model->setEmail($email);
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * send activation email to new user
     * @param $user
     * @return bool
     * @throws Zend_Mail_Exception
     */
    protected function activate($user)
    {
        $url            = "http://".$_SERVER['SERVER_NAME'];
        $username		= $user->getUsername();
        $email          = $user->getEmail();
        $activationLink = '<a href="'.$url.'/auth/activation?code='.$user->getActivationcode().'">Activare</a>';

        $signUp = new Default_Model_Templates();
        $signUp->find('signUp');

        $subject = $signUp->getSubjectro();
        $message = $signUp->getValuero();

        $message = str_replace('{'.'$'.'username}', $username, $message);
        $message = str_replace('{'.'$'.'email}', $email, $message);
        $message = str_replace('{'.'$'.'activationlink}', $activationLink, $message);

        // ToDo: change hardcoded variables
        $emailcompany = 'contact@sexypitipoanca.ro';
        $institution = 'SexyPitipoanca.ro';

        $mail = new Zend_Mail();
        $mail->setFrom($emailcompany, $institution);
        $mail->setSubject($subject);
        $mail->setBodyHtml($message);
        $mail->addTo($user->getEmail());
        if ($mail->send()) {
            return true;
        }
        return false;
    }
}