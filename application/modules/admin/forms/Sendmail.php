<?php
class Admin_Form_Sendmail extends Zend_Form
{
	function init()
	{
		 // Set the method for the display form to POST
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'sendMail'));
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$subject = new Zend_Form_Element_Text('subject');
        $subject->setLabel('Subject');
        $subject->setRequired(true);
        $subject->setAttribs(array('style'=>'width:400px;', 'class'=>'validate[required] input', 'autocomplete'=>'off'));

		$to = new Zend_Form_Element_Text('to');
        $to->setLabel('To (Full Name)');
        $to->setRequired(true);
        $to->setAttribs(array( 'style'=>'width:400px;', 'class'=>'validate[required] input', 'autocomplete'=>'off'));

		$emailto = new Zend_Form_Element_Text('emailto');
        $emailto->setLabel('To (Email Address)');
        $emailto->setRequired(true);
        $emailto->setAttribs(array( 'style'=>'width:400px;', 'class'=>'validate[required] input', 'autocomplete'=>'off'));

		$from = new Zend_Form_Element_Text('from');
        $from->setLabel('From (Full Name)');
        $from->setRequired(true);
        $from->setAttribs(array('style'=>'width:400px;', 'class'=>'validate[required] input', 'autocomplete'=>'off'));

		$emailfrom = new Zend_Form_Element_Text('emailfrom');
        $emailfrom->setLabel('From (Email Address)');
        $emailfrom->setRequired(true);
        $emailfrom->setAttribs(array( 'style'=>'width:400px;', 'class'=>'validate[required] input', 'autocomplete'=>'off'));

		$message=new Zend_Form_Element_Textarea('message');
	    $message->setLabel('Message:');
        $message->setRequired(true);
        $message->setAttribs(array( 'class'=>'validate[required] input', 'autocomplete'=>'off'));
		
		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Send');
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);

		$this->addElements(array($subject, $to, $emailto, $emailfrom, $from, $message, $submit));
	}

	public function email(Default_Model_Users $to)
	{
		$this->to->setValue($to->getFirstname().' '.$to->getLastname());
		$this->emailto->setValue($to->getEmail());
	}
}
