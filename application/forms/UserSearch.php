<?php
class Default_Form_UserSearch extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmSearchUser'));

		$txtHeaderSearch = new Zend_Form_Element_Text('txtHeaderSearch');
        $txtHeaderSearch->setLabel('');
        $txtHeaderSearch->setValue('Cauta utilizator ...');
        $txtHeaderSearch->setAttribs(array(
			'class'		=> 'validate[required]',
			'onblur'	=> "if (this.value == '') {this.value = 'Cauta utilizator ...';}",
			'onfocus'	=> "if (this.value == 'Cauta utilizator ...') {this.value = '';}"
		));
        $txtHeaderSearch->setRequired(true);
		$this->addElement($txtHeaderSearch);
		
		$search_type = new Zend_Form_Element_Select('search_type');	
		$search_type_Options = array('user'=>'Utilizator','friend'=>'Prieten');		
		$search_type->addMultiOptions($search_type_Options);
		$search_type->addValidator(new Zend_Validate_InArray(array_keys($search_type_Options)));
		$search_type->setValue('user');
		$search_type->setAttribs(array('class'=>'selectmenu drop_down'));
		$search_type->setRequired(false);
		$this->addElement($search_type);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttribs(array('class'=>'search_lupa'));
        $submit->setIgnore(true);
		$this->addElement($submit);
	}
}