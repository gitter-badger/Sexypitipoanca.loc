<?php
class Admin_Form_News extends Zend_Form
{
	public function init()
	{
		// Set the method for the display form to POST
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmNews'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel(Zend_Registry::get('translate')->_('marketing_add_news_table_title'));
        $title->setAttribs(array('maxlength'=>'120', 'class'=>'validate[required,length[1,120]]', 'style'=>'width:250px;'));
        $title->setRequired(true);

        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel(Zend_Registry::get('translate')->_('marketing_add_news_table_message'));
        $message->setAttribs(array('height:100px;', 'class'=>'validate[required] input', 'style'=>'width:250px;'));
        $message->setRequired(true);

		$status = new Zend_Form_Element_Radio('status');
        $status->setRequired(true);
        $status->setLabel(Zend_Registry::get('translate')->_('marketing_add_news_table_status'));
        $optionStatus = array(Zend_Registry::get('translate')->_('marketing_add_news_table_inactive'),Zend_Registry::get('translate')->_('marketing_add_news_table_active'));
        $status->addMultiOptions($optionStatus);
        $status->addValidator(new Zend_Validate_InArray(array_keys($optionStatus)));
        $status->setAttribs(array('class'=>'validate[required]'));
		$status->setValue('1');
        $status->setSeparator('');

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue(Zend_Registry::get('translate')->_('marketing_add_news_table_submit'));
        $submit->setAttribs(array());
        $submit->setIgnore(true);

		$this->addElements(array($title, $message, $status, $submit));
	}
}
