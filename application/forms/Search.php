<?php
class Default_Form_Search extends Zend_Form 
{
	function init()
	{
		$this->setMethod('post');
		$this->setAction('/catalog/search');
		$this->addAttribs(array('id'=>'frmSearch', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('search');
		$this->addElement($control);

		$search = new Zend_Form_Element_Text('search');
		$search->setLabel('Search');
		$search->addValidator(new Zend_Validate_StringLength(3,32));
		$search->setAttribs(array('class'=>'txt validate[required,min[3]]'));
		$search->setRequired(true);
		$this->addElement($search);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Cauta');
		$submit->setAttribs(array('id'=>'submit-search', 'class'=>'sub'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}