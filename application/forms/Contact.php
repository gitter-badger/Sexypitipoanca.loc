<?php
class Default_Form_Contact extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formContact'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Numele');
		$name->addValidator(new Zend_Validate_StringLength(1,32));
		$name->setAttribs(array('class'=>'f4 validate[required]', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$name->setRequired(true);
		$this->addElement($name);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Adresa email');
		$email->addValidator(new Zend_Validate_EmailAddress());
		$email->addValidator(new Zend_Validate_StringLength(1,32));
		$email->setAttribs(array('class'=>'f4 validate[required,custom[email]', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$email->setRequired(true);
		$this->addElement($email);

		$subject = new Zend_Form_Element_Text('subject');
		$subject->setLabel('Subiect');
		$subject->addValidator(new Zend_Validate_StringLength(1,32));
		$subject->setAttribs(array('class'=>'f4 validate[required]', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$subject->setRequired(true);
		$this->addElement($subject);

		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Mesaj');
		$message->addValidator(new Zend_Validate_StringLength(1,500));
		$message->setAttribs(array('class'=>'txt', 'style'=>'width: 231px; height: 156px;', 'maxlenght'=>'500', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$message->setRequired(false);
		$this->addElement($message);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Trimite');
		$submit->setAttribs(array('class'=>'bt_creaza'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}