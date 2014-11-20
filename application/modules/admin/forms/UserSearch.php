<?php
class Admin_Form_UserSearch extends Zend_Form{
	function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmSearchUser'));

		$txtHeaderSearch = new Zend_Form_Element_Text('txtHeaderSearch');
        $txtHeaderSearch->setLabel('Cauta Utilizator');
        $txtHeaderSearch->setAttribs(array('class'=>'validate[required]', 'style'=>'width:200px'));
        $txtHeaderSearch->setRequired(true);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Cauta');
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);

		$this->addElements(array($txtHeaderSearch, $submit));
	}
}