<?php
class Default_Form_Auth extends Zend_Form 
{
	public function init()
	{
		
	}

	public function login()
	{
		$this->setMethod('post');
		$this->setAction('/auth');
		$this->addAttribs(array('id'=>'frmAuthLogin'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('login');
		$control->setAttribs(array('id'=>'control-auth'));
		$this->addElement($control);

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Nume utilizator');
		$username->addValidator(new Zend_Validate_StringLength(3,32));
		$username->setAttribs(array('class'=>'validate[required]'));
		$username->setRequired(true);
		$this->addElement($username);

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Parola');
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'validate[required]'));
		$password->setRequired(true);
		$this->addElement($password);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Autentificare');
		$submit->setValue('autentificare');
		$submit->setAttribs(array('id'=>'submit-auth', 'class'=>'bt1 fl'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function loginIframe()
	{
		$this->setMethod('post');
		$this->setAction('/iframe/login');
		$this->addAttribs(array('id'=>'frmAuthLogin'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Nume utilizator');
		$username->addValidator(new Zend_Validate_StringLength(3,32));
		$username->setAttribs(array('class'=>'validate[required]'));
		$username->setRequired(TRUE);
		$this->addElement($username);

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Parola');
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'validate[required]'));
		$password->setRequired(true);
		$this->addElement($password);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Autentificare');
		$submit->setValue('autentificare');
		$submit->setAttribs(array('id'=>'submit-auth', 'class'=>'bt1 fl'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function forgotPassword()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmAuthForgotPassword', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		
		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('forgotPassword');
		$this->addElement($control);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email');
		$email->addValidator(new Zend_Validate_EmailAddress());
		$email->addValidator(new Zend_Validate_StringLength(1,32));
		$email->setAttribs(array('maxlenght'=>'32', 'class'=>'validate[required] f4'));
		$email->setRequired(true);
		$this->addElement($email);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Trimite');
		$submit->setValue('trimite');
		$submit->setAttribs(array('class'=>'bt1 fl'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}