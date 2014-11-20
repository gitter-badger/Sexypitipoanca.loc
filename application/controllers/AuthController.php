<?php
class AuthController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
		$bootstrap = $this->getInvokeArg('bootstrap');
		if($bootstrap->hasResource('db')) {
			$this->db = $bootstrap->getResource('db');
		}
	}

	public function indexAction()
    {
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if(null != $authAccount) {
			if(null != $authAccount->getId()) {
				$account = new Default_Model_AccountUsers();
				$account->find($authAccount->getId());
				$this->_redirect('/account');
			}
		} else {
			$formAuth = new Default_Form_Auth();
			$formAuth->login();
			$formAuth->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/auth/login.phtml'))));
			$this->view->formAuth = $formAuth;

			if($this->getRequest()->isPost()) {
				if($this->getRequest()->getPost('control') == 'login') {
					if($formAuth->isValid($this->getRequest()->getPost())) {
						$dbAdapter = new Zend_Auth_Adapter_DbTable($this->db, 'j_account_users', 'username', 'password', 'MD5(?) AND status = "1"');
						$dbAdapter->setIdentity($this->getRequest()->getPost('username'))
								->setCredential($this->getRequest()->getPost('password'));

						$auth = Zend_Auth::getInstance($this->getRequest()->getPost('password'));
						$result = $auth->authenticate($dbAdapter);
						if(!$result->isValid()) {
							switch($result->getCode()) {
								case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
								case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
									$this->_flashMessenger->addMessage('<span class="mess-false">Eroare logare! User sau parola gresita.</span>');
									break;

								default:
									$this->_flashMessenger->addMessage('<span class="mess-false">Eroare logare!</span>');
									break;
							}
							$this->_redirect('/account/new');
						} else {
							$account = $dbAdapter->getResultRowObject();
							$model = new Default_Model_AccountUsers();
							$model->find($account->id);
							$model->saveLastLogin();
							$storage = $auth->getStorage();
							$storage->write($model);
							$this->_redirect('/index');
						}
					} else {
						$this->_flashMessenger->addMessage('<span class="mess-false">Eroare logare! Date invalide.</span>');
						$this->_redirect('/account/new');
					}
				}
			}
		}
    }

    public function logOutAction()
    {
    	$this->_helper->layout->disableLayout();
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) {
    		$auth->clearIdentity();
    	}
   		$this->_redirect('/index');
    }

	public function forgotPasswordAction()
	{
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if(null != $authAccount) {
			if(null != $authAccount->getId()) {
				$account = new Default_Model_AccountUsers();
				$account->find($authAccount->getId());
				$this->_redirect('/account');
			}
		} else {
			$form = new Default_Form_Auth();
			$form->forgotPassword();
			$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/auth/forgot-password.phtml'))));
			$this->view->form = $form;

			if($this->getRequest()->isPost()) {
				if($this->getRequest()->getPost('control') == 'forgotPassword') {
					if($form->isValid($this->getRequest()->getPost())) {	
						$model = new Default_Model_AccountUsers();
						$select = $model->getMapper()->getDbTable()->select()						
								->where('email = ?', $form->getValue('email'))
								;
						if(($result = $model->fetchAll($select))) {
							$new_password = substr(md5(uniqid(mt_rand(), true)),0,10);
							$password = md5($new_password);
							$account = new Default_Model_AccountUsers();
							$account->find($result[0]->getId());
							$account->setPassword($password);
							if($account->save()) {
								
								$forgotPass = new Default_Model_Templates();
								$forgotPass->find('forgotPass');

								$subject = $forgotPass->getSubjectro();
								$message = $forgotPass->getValuero();							

								$email = $account->getEmail();

								$message = str_replace('{'.'$'.'username}', $account->getUsername(), $message);
								$message = str_replace('{'.'$'.'password}', $new_password, $message);
								$message = str_replace('{'.'$'.'email}', $email, $message);

						
								$emailcompany = 'contact@sexypitipoanca.ro';
								$institution = 'SexyPitipoanca.ro';
								
								$mail = new Zend_Mail();
								$mail->setFrom($emailcompany, $institution);
								$mail->setSubject($subject);
								$mail->setBodyHtml($message);
								$mail->addTo($email);
								
								if($mail->send())
								{
									$this->_flashMessenger->addMessage('<span class="mess-true">O parola noua a fost trimisa la adresa de email.</span>');
								}
								else
								{
									$this->_flashMessenger->addMessage('<span class="mess-false">Eroare resetare parola!</span>');
								}	
							}
						}else{
							$this->_flashMessenger->addMessage('<span class="mess-false">Adresa de email nu exista in baza noastra de date</span>');
						}
						$this->_redirect('/auth/forgot-password');
					}
				}
			}
		}
	}
	
	public function activationAction()
	{
		$account = new Default_Model_AccountUsers();
		$activationcode = $this->getRequest()->getParam('code');		

		if(null != $activationcode) {			
			$select = $account->getMapper()->getDbTable()->select()
					->where('`activationcode` = ?', $activationcode);
			if($accounts = $account->fetchAll($select)) {
				$account1 = new Default_Model_AccountUsers();
				$account1->find($accounts['0']->getId());
				$account1->setStatus('1');
				$account1->setActivationcode('');
				if($account1->save()) {
					$this->_flashMessenger->addMessage('<span class="mess-true">Contul a fost activat cu succes!</span>');
				} else {
					$this->_flashMessenger->addMessage('<span class="mess-false">Eroare activare cont!</span>');
				}
				$this->_redirect('/account/new');
			} else {
				$this->_flashMessenger->addMessage('<span class="mess-false">Eroare activare cont! Codul este invalid!</span>');
				$this->_redirect('/account/new');
			}
		}
		$this->view->message = $this->_flashMessenger->getMessages();
	}
}