<?php
class ContactController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

	public function indexAction()
    {
		$form = new Default_Form_Contact();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/contact.phtml'))));
		$this->view->form = $form;

		if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {

				$message = '<table border="0" cellpadding="5" cellspacing="0">';
				$message.=		'<tr>';
				$message.=			'<th align="right">Nume : </th>';
				$message.=			'<td align="left">'.$this->getRequest()->getPost('name').'</td>';
				$message.=		'</tr>';			
				$message.=		'<tr>';
				$message.=			'<th align="right">Email : </th>';
				$message.=			'<td align="left">'.$this->getRequest()->getPost('email').'</td>';
				$message.=		'</tr>';
				$message.=		'<tr>';
				$message.=			'<th align="right">Comentariu: </th>';
				$message.=			'<td align="left">'.$this->getRequest()->getPost('message').'</td>';
				$message.=		'</tr>';
				$message.=	'</table>';
		
				$emailcompany = 'contact@sexypitipoanca.ro';
				$institution = 'SexyPitipoanca';
				$mail = new Zend_Mail();
				$mail->setFrom($emailcompany, $institution);
				$mail->setSubject('Contact pitipoanca: '.$this->getRequest()->getPost('subject'));
				$mail->setBodyHtml($message);
				$mail->addTo($emailcompany);

				if($mail->send()) {
					$this->_flashMessenger->addMessage('<div class="mess-true">Mesajul a fost trimis cu succes</div>');
				} else {
					$this->_flashMessenger->addMessage('<div class="mess-false">Eroare trimitere mesaj! Va rugam incercati mai tarziu</div>');
				}

	    		$this->_redirect('/contact');
            }
        }

	}
}