<?php
class Admin_Form_Template extends Zend_Form 
{
	function init()
	{
		
	}

	function site()
	{
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmSite'));

        $value = new Zend_Form_Element_Textarea('value');
        $value->setLabel('Mesaj');
        $value->setAttribs(array('class'=>'input', 'rows'=>'5', 'cols'=>'60'));
        $value->setRequired(false);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Submit');
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);

        $this->addElements(array($value, $submit));
	}

	function email()
	{
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmEmail'));

		$subject = new Zend_Form_Element_Text('subject');
        $subject->setLabel('Subiect');
        $subject->setAttribs(array('class'=>'input'));
        $subject->setRequired(false);

        $value = new Zend_Form_Element_Textarea('value');
        $value->setLabel('Mesaj');
        $value->setAttribs(array('class'=>'input', 'rows'=>'5', 'cols'=>'60'));
        $value->setRequired(false);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Submit');
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);

        $this->addElements(array($subject, $value, $submit));
	}
}