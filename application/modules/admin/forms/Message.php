<?php
class Admin_Form_Message extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmSendMessage', 'class'=>''));

		$fromEmail = new Zend_Form_Element_Text('fromEmail');
		$fromEmail->setLabel('From Email');
		$fromEmail->setAttribs(array('class'=>'', 'style'=>'width: 368px', 'readonly'=>'readonly'));
		$fromEmail->setRequired(true);
		$fromEmail->setValue('contact@sexypitipoanca.ro');
		$this->addElement($fromEmail);

		$fromName = new Zend_Form_Element_Text('fromName');
		$fromName->setLabel('From Name');
		$fromName->setAttribs(array('class'=>'', 'style'=>'width: 368px', 'readonly'=>'readonly'));
		$fromName->setRequired(true);
		$fromName->setValue('SexyPitipoanca.ro');
		$this->addElement($fromName);

		$to = new Zend_Form_Element_Text('to');
		$to->setLabel('To email');
		$to->setAttribs(array('class'=>'', 'style'=>'width: 368px'));
		$to->setRequired(true);
		$this->addElement($to);

		$subject = new Zend_Form_Element_Text('subject');
		$subject->setLabel('Subject');
		$subject->setAttribs(array('class'=>'', 'style'=>'width: 368px'));
		$subject->setRequired(true);
		$this->addElement($subject);
		
		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Message');		
		$message->setAttribs(array('class'=>'', 'cols'=>'50', 'rows'=>'10'));
		$validator = new Zend_Validate_StringLength(array('max' => 500));
		$message->addValidator($validator);
		$message->setRequired(true);
		$this->addElement($message);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Send');
		$submit->setAttribs(array('class'=>'bt1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
	
	public function fill(Default_Model_AccountUsers $model)
	{
		$this->to->setValue($model->getEmail());
	}
}