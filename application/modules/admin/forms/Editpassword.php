<?php
class Admin_Form_Editpassword extends Zend_Form
{
	public function init()
	{
		
	}

	public function editpassword(Default_Model_AdminUser $operator)
	{
		$this->setMethod('post');
        $this->addAttribs(array('id'=>'frmPass'));

		$changepass = new Zend_Form_Element_Hidden('changepass');
		$changepass->setValue('yes');

		$username = new Zend_Form_Element_Text('username');
		$username->setValue($operator->getUsername());
        $username->setLabel(Zend_Registry::get('translate')->_('user_username'));

		$password = new Zend_Form_Element_Password('password');
        $password->setLabel(Zend_Registry::get('translate')->_('user_new_password'));
        $password->setRequired(true);
		$password->addFilter('StringTrim');
		$validPasswordLength = new Zend_Validate_StringLength(6,20);
		$password->addValidators(array($validPasswordLength));
        $password->setAttribs(array('class'=>'validate[required,length[6,20]] ', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
        $password->setDescription(Zend_Registry::get('translate')->_('user_new_password_note'));
		$password->setIgnore(true);

		$tbRePassword = new Zend_Form_Element_Password('tbRePassword');
        $tbRePassword->setLabel(Zend_Registry::get('translate')->_('user_new_password_confirmation'));
        $tbRePassword->setAllowEmpty(false);
        $tbRePassword->setRequired(true);
		$tbRePassword->addFilter('StringTrim');
        $tbRePassword->setAttribs(array('class'=>'validate[confirm[password]]', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$validatePasswordIdenticalField = new App_Validate_IdenticalField('password', 'Password');
		$tbRePassword->addValidators(array($validatePasswordIdenticalField));
		$tbRePassword->setIgnore(true);

		$submit = new Zend_Form_Element_Submit('submitPass');
        $submit->setValue(Zend_Registry::get('translate')->_('user_change_password'));
        $submit->setAttribs(array());
        $submit->setIgnore(true);
		$this->addElements(array($submit, $password, $tbRePassword, $username, $changepass));
	}
}
