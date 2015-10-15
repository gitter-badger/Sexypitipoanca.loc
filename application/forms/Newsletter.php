<?php
class Default_Form_Newsletter extends Zend_Form 
{
	public function init()
	{
		
	}

	public function subscribe()
	{
		$this->setMethod('post');
		$this->setAction('/news/subscribe');
		$this->addAttribs(array('id'=>'frmNewsletter', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('newsletter');
		$control->setAttribs(array('id'=>'controlNewsletter'));
		$this->addElement($control);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email');
		$email->setAttribs(array('class'=>'news_input validate[required,custom[email]', 'size'=>'45'));
		$email->setRequired(true);
		$this->addElement($email);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Trimite');
		$submit->setAttribs(array('id'=>'submit-subscribe', 'class'=>'bt_newsletter'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}