<?php

class Admin_Form_Newsletter extends Zend_Form
{
	function init()
	{
		// Set the method for the display form to POST
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmSendNewsletter'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel(Zend_Registry::get('translate')->_('marketing_newsletter_table_title'));
        $title->setAttribs(array('maxlength'=>'120', 'class'=>'validate[required,length[1,120]]', 'style'=>'width:250px;'));
        $title->setRequired(true);

        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel(Zend_Registry::get('translate')->_('marketing_newsletter_table_message'));
        $message->setAttribs(array('style'=>'width:250px; height:100px;'));
        $message->setRequired(false);
		$message->setIgnore(true);

//        $from = new Zend_Form_Element_Text('from');
//        $from->setLabel(Zend_Registry::get('translate')->_("marketing_newsletter_table_from_name"));
//        $from->setAttribs(array('maxlength' => 120, 'style'=>'width:250px;', 'class'=>'validate[required,length[1,120]]'));
//        $from->setRequired(true);

//        $fromemail = new Zend_Form_Element_Text('fromemail');
//        $fromemail->setLabel(Zend_Registry::get('translate')->_("marketing_newsletter_table_from_email"));
//        $fromemail->setAttribs(array('maxlength' => 120, 'style'=>'width:250px;', 'class'=>'validate[required, custom[email], length[1,120]]'));
//        $fromemail->setRequired(true);
//		$validateEmailAddress = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS);
//        try {$validateEmailAddress->setValidateMx(true);} catch (Exception $e) {}
//        $fromemail->setFilters(array(new Zend_Filter_StringTrim()));
//        $fromemailStringLength = new Zend_Validate_StringLength(1,120);
//		$fromemail->addValidators(array($validateEmailAddress, ));

		$company = new Default_Model_Company();
    	$select = $company->getMapper()->getDbTable()->select();
    	if(($company = $company->fetchAll($select))) {
			$email = $company[0]->getEmail();
    	}

		$status = new Zend_Form_Element_Checkbox('status');
		$status->setLabel(Zend_Registry::get('translate')->_('marketing_newsletter_table_to_email').' '.$email);
		$status->setRequired(false);
		$status->setIgnore(true);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue(Zend_Registry::get('translate')->_('marketing_newsletter_table_send'));
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);

		$this->addElements(array(
			$title,
			$message,
//			$from,
//			$fromemail,
			$status,
			$submit
		));
	}
}
