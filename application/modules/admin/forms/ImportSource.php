<?php

/**
 * @property null|Zend_Form|Zend_Form_Element title
 */
class Admin_Form_ImportSource extends Zend_Form{
	function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmImportSources'));
		
		$title = new Zend_Form_Element_Text('Title');
		$title->setLabel('Title');
		$title->setRequired(true);
		$this->addElement($title);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Modify');
		$submit->setAttribs(array('class'=>'button1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
	
	function edit(Admin_Model_ImportSource $model)
	{
		$this->title->setValue($model->getTitle());
	}
}