<?php

class Admin_Form_Tags extends Zend_Form
{
	function init()
	{
		// Set the method for the display form to POST
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmAddTag'));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setAttribs(array('maxlength'=>'120', 'class'=>'validate[required,length[1,120]]', 'style'=>'width:250px;'));
        $name->setRequired(true);
		$this->addElement($name);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Submit');
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);
		$this->addElement($submit);
	}
	
	function edit(Default_Model_Tags $model)
	{
		$this->name->setValue($model->getName());
		$this->submit->setValue('Modify');
	}
}
