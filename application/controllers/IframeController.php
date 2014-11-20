<?php
class IframeController extends Zend_Controller_Action
{
	public function init()
    {
		/* Initialize action controller here */
		$bootstrap = $this->getInvokeArg('bootstrap');
        if($bootstrap->hasResource('db')) {
        	$this->db = $bootstrap->getResource('db');
        }
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
    }

	public function loginAction()
	{
		$redirect = $this->getRequest()->getParam('rdr');
		if(NULL != $redirect)
		{
			$this->view->redirect = $redirect;
		}
		
		$formAuth = new Default_Form_Auth();
		$formAuth->loginIframe();
		$formAuth->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/auth/login.phtml'))));
		$this->view->formAuth = $formAuth;

		if($this->getRequest()->isPost())
		{
			if($formAuth->isValid($this->getRequest()->getPost()))
			{
				$dbAdapter = new Zend_Auth_Adapter_DbTable($this->db, 'j_account_users', 'username', 'password', 'MD5(?) AND status = "1"');
				$dbAdapter->setIdentity($this->getRequest()->getPost('username'))
						->setCredential($this->getRequest()->getPost('password'));

				$auth = Zend_Auth::getInstance($this->getRequest()->getPost('password'));
				$result = $auth->authenticate($dbAdapter);
				if(!$result->isValid())
				{
					switch($result->getCode())
					{
						case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
						case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
							$this->_flashMessenger->addMessage('
								<span class="t">Eroare</span>
								<span>Numele de utilizator sau parola sunt incorecte.</span>
								<span>Foloseste linkul <em>"am uitat parola"</em></span>
							');
							break;

						default:
							$this->_flashMessenger->addMessage('
								<span class="t">Eroare</span>
								<span>Numele de utilizator sau parola sunt incorecte.</span>
								<span>Foloseste linkul <em>"am uitat parola"</em></span>
							');
							break;
					}
					$this->_redirect('/iframe/login');
				}
				else
				{
					$account = $dbAdapter->getResultRowObject();
					$model = new Default_Model_AccountUsers();
					$model->find($account->id);
					$model->saveLastLogin();
					$storage = $auth->getStorage();
					$storage->write($model);
					$this->_redirect('/iframe/login/rdr/home');
				}
			}
			else
			{
				$this->_flashMessenger->addMessage('
					<div><strong>Eroare</strong></div>
					<div>
						Numele de utilizator sau parola sunt incorecte.
						Foloseste linkul "am uitat parola"
					</div>
				');
				$this->_redirect('/iframe/login');
			}
		}
	}

	public function moreInfoAction()
	{
		$form = new Default_Form_Moreinfo();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/Moreinfo.phtml'))));

		$id = $this->getRequest()->getParam('id');

		$company = new Default_Model_Company();
    	$select = $company->getMapper()->getDbTable()->select();
    	if($companyx = $company->fetchAll($select)) {
			$companyName = $companyx[0]->getInstitution();
			$companyEmail = $companyx[0]->getEmail();
    	}

		$product = new Default_Model_Products();
		$product->find($id);
		if((null != $product)) {
			$this->view->product = $product;
		}
		
		if($this->getRequest()->isPost()) {
			if($form->isValid($this->getRequest()->getPost())) {
				$result = $this->getRequest()->getPost();
				$subject = 'Informatii suplimentare produse';

				$message ='<fieldset style="width:420px; padding:8px; margin:0px auto; border:1px solid #e0e7c7;">';
				$message.=	'<table border="0" width="100%" cellpadding="0" cellspacing="0">';
				$message.=		'<col width="50%" />';
				$message.=		'<col width="50%" />';
				$message.=		'<tr>';
				$message.=			'<th colspan="2" align="center">Informatii suplimentare despre produsul '.$product->getName().'</th>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<td colspan="2"><hr style="height:1px; border:1px solid #e0e7c7;" /></td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<th>Nume</th>';
				$message.=			'<td>'.$result['name'].'</td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<td colspan="2"><hr style="height:1px; border:1px solid #e0e7c7;" /></td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<th>Email</th>';
				$message.=			'<td>'.$result['email'].'</td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<td colspan="2"><hr style="height:1px; border:1px solid #e0e7c7;" /></td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<th>Telefon</th>';
				$message.=			'<td>'.$result['phone'].'</td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<td colspan="2"><hr style="height:1px; border:1px solid #e0e7c7;" /></td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			"<th>Mesaj</th>";
				$message.=			'<td>'.$result['comments'].'</td>';
				$message.=		'</tr>';
				$message.=	'</table>';
				$message.='</fieldset>';

				$mail = new Zend_Mail();
				$mail->setFrom($companyName, $result['email']);
				$mail->setSubject($subject);
				$mail->setBodyHtml($message);
				$mail->addTo($companyEmail);

				if($mail->send()) {
					$this->_flashMessenger->addMessage('<div class="mess-true">Cererea ta a fost trimisa!</div>');
				} else {
					$this->_flashMessenger->addMessage('<div class="mess-false">Cererea ta nu a putut fii trimisa!</div>');
				}
				$this->_redirect('/iframe/more-info/id/'.$id.'');
			}
		}
		$this->view->form = $form;
	}
	
	public function browserAction()
	{
		require_once APPLICATION_PUBLIC_PATH.'/library/App/Browser/Browser.php';
		$browser = new Browser();
		$this->view->browserName = $browser->getBrowser();
		$this->view->browserVersion = $browser->getVersion();
		$this->view->browserPlatform = $browser->getPlatform();
	}

	public function inconstructionAction()
	{

	}

	public function photogalleryAction()
	{
		function imageGallery($productId){
			if(null != $productId){
				$gallery = new Default_Model_Productsgallery();
				$select = $gallery->getMapper()->getDbTable()->select()
						->where('productId = ?', $productId)
						->order('date DESC');
				$galleries = $gallery->fetchAll($select);
				if(null != $galleries){
					return $galleries;
				}else{
					return null;
				}
			}else{
				return null;
			}
		}

		$id = $this->getRequest()->getParam('id');
		if(null != $id){
			$this->view->productId = $id;
			$product = new Default_Model_Products();
			$product->find($id);
			if(null != $product){
				$this->view->product = $product;
			}
		}
	}

	public function addtocartAction()
	{
		$id = $this->getRequest()->getParam('id');
		if(null != $id){
			$product = new Default_Model_Products();
			$product->find($id);
			if(null != $product){
				$this->view->product = $product;
			}
		}
	}
}