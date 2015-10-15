<?php
class Default_Form_Account extends Zend_Form
{
	public function init(){}

	public function add()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formAccountNew', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$filters = array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags());
		
		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('new');
		$this->addElement($control);
		
		$image = new Zend_Form_Element_File('image');
        $image->setLabel('Adauga avatar');
        $image->setAttribs(array('class'=>''));
		$image->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $image->setRequired(true);
		$image->setIgnore(false);
		$this->addElement($image);

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Numele utilizator');
        $username->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'j_account_users', 'field'=>'username')));
		$username->addValidator(new Zend_Validate_StringLength(3,32));
	    $username->addValidator(new Zend_Validate_Alnum(false));
		$username->setAttribs(array('class'=>'f4 validate[required,minSize[3],maxSize[32]]', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$username->setRequired(true);
		$username->removeDecorator('helper');
		$this->addElement($username);

		$password = new Zend_Form_Element_Password('passwordnew');
		$password->setLabel('Parola');
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'f4 validate[required,minSize[5],maxSize[32]]', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$password->setRequired(true);
		$this->addElement($password);

		$retypePassword = new Zend_Form_Element_Password('retypePassword');
		$retypePassword->setLabel('Reintrodu parola');
		$retypePassword->addValidator(new Zend_Validate_Identical('passwordnew'));
		$retypePassword->addValidator(new Zend_Validate_StringLength(6,32));
		$retypePassword->setAttribs(array('class'=>'f4 validate[equals[passwordnew]]', 'maxlenght'=>'32 ', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$retypePassword->setRequired(true);
		$retypePassword->setIgnore(true);
		$this->addElement($retypePassword);

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email');
		$email->addValidator(new Zend_Validate_EmailAddress());
        $email->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'j_account_users', 'field'=>'email')));
		$email->addValidator(new Zend_Validate_StringLength(1,32));
		$email->setAttribs(array('class'=>'f4 validate[required,custom[email]]', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$email->setRequired(true);
		$this->addElement($email);

		$currentDay = date('d');
		$currentMonth = date('m');
		$currentYear = date('Y');
		$birthday = mktime(date('j'), date('i'), date('s'), $currentMonth, $currentDay, $currentYear-18);
		$birthday_year = date('Y', $birthday);

		$birth_day = new Zend_Form_Element_Select('birth_day');
		$birth_day->setLabel('birth_day');
		$birth_day_Options = array();
		for($i=1; $i<=31; $i++) {
			$birth_day_Options[$i] = $i;
		}
		$birth_day->addMultiOptions($birth_day_Options);
		$birth_day->addValidator(new Zend_Validate_InArray(array_keys($birth_day_Options)));
		$birth_day->setValue($currentDay);
		$birth_day->setAttribs(array('class'=>'zi validate[required] selectmenu'));
		$birth_day->setRequired(true);
		$this->addElement($birth_day);

		$birth_month = new Zend_Form_Element_Select('birth_month');
		$birth_month->setLabel('birth_month');
		$birth_month_Options = array();
		for($i=1; $i<=12; $i++) {
			$birth_month_Options[$i] = $i;
		}
		$birth_month->addMultiOptions($birth_month_Options);
		$birth_month->addValidator(new Zend_Validate_InArray(array_keys($birth_month_Options)));
		$birth_month->setValue($currentMonth);
		$birth_month->setAttribs(array('class'=>'luna validate[required] selectmenu'));
		$birth_month->setRequired(true);
		$this->addElement($birth_month);

		$birth_year = new Zend_Form_Element_Select('birth_year');
		$birth_year->setLabel('birth_year');
		$birth_year_Options = array();
		for($i=$currentYear; $i >= '1930'; $i--) {
			$birth_year_Options[$i] = $i;
		}
		$birth_year->addMultiOptions($birth_year_Options);
		$birth_year->addValidator(new Zend_Validate_InArray(array_keys($birth_year_Options)));
		$birth_year->setValue($birthday_year);
		$birth_year->setAttribs(array('class'=>'an validate[required] selectmenu'));
		$birth_year->setRequired(true);
		$this->addElement($birth_year);

		$gender = new Zend_Form_Element_Radio('gender');
		$gender->setLabel('Sex');
		$genderOptions = array('masculin', 'feminin');
		$gender->addMultiOptions($genderOptions);
		$gender->addValidator(new Zend_Validate_InArray(array_keys($genderOptions)));
		$gender->setValue('0');
		$gender->setAttribs(array('class'=>''));
		$gender->setRequired(true);
		$this->addElement($gender);

		$newsletter = new Zend_Form_Element_Checkbox('newsletter');
		$newsletter->setLabel('Doriti sa va abonati la newsletter?');
		$newsletter->setValue('1');
		$newsletter->setAttribs(array('class'=>'fl'));
		$newsletter->setRequired(true);
		$this->addElement($newsletter);

		$terms = new Zend_Form_Element_Checkbox('terms');
		$terms->setLabel('Am citit si sunt de acord cu');
		$terms->setAttribs(array('class'=>'fl validate[required]'));
		$terms->setRequired(true);
		$this->addElement($terms);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Creeaza');
		$submit->setAttribs(array('class'=>'bt_creaza'));
		$submit->setIgnore(true);
		$this->addElement($submit);
		$this->setElementFilters($filters);
	}

	public function edit(Default_Model_AccountUsers $model)
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formAccountEdit', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$image = new Zend_Form_Element_File('image');
        $image->setLabel('Adauga avatar');
        $image->setAttribs(array('class'=>''));
		$image->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $image->setRequired(true);
		$image->setIgnore(false);
		$this->addElement($image);

		$this->control->setValue('edit');
		$this->username->setValue($model->getUsername());
		$this->username->setAttribs(array('readonly' => 'readonly'));
		$this->birth_day->setValue(date('d', $model->getBirth_day()));
		$this->birth_month->setValue(date('m', $model->getBirth_day()));
		$this->birth_year->setValue(date('Y', $model->getBirth_day()));
		$this->gender->setValue($model->getGender());
		$this->submit->setValue('Salveaza');

		$usernameValidateDbNotExists = $this->username->getValidator('Zend_Validate_Db_NoRecordExists');
		$usernameValidateDbNotExists->setExclude(array('field'=>'username', 'value' => $model->getUsername()));
		$this->removeElement('passwordnew');		
		$this->removeElement('retypePassword');
		$this->removeElement('email');		
		$this->removeElement('newsletter');
		$this->removeElement('terms');
	}

	public function editPassword()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formAccountEditPassword', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$filters = array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags());
		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('editPassword');
		$this->addElement($control);

		$oldPassword = new Zend_Form_Element_Password('oldPassword');
		$oldPassword->setLabel('Parola veche');
		$oldPassword->addValidator(new Zend_Validate_StringLength(6,32));
		$oldPassword->setAttribs(array('class'=>'f4 validate[required,minSize[5],maxSize[32]]', 'maxlenght'=>'32'));
		$oldPassword->setRequired(true);
		$this->addElement($oldPassword);

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Parola');
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'f4 validate[required,minSize[5],maxSize[32]]', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$password->setRequired(true);
		$this->addElement($password);

		$retypePassword = new Zend_Form_Element_Password('retypePassword');
		$retypePassword->setLabel('Repeta parola');
		$retypePassword->addValidator(new Zend_Validate_Identical('password'));
		$retypePassword->addValidator(new Zend_Validate_StringLength(6,32));
		$retypePassword->setAttribs(array('class'=>'f4 validate[required,equals[password]]', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$retypePassword->setRequired(true);
		$retypePassword->setIgnore(true);
		$this->addElement($retypePassword);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Modifica');
		$submit->setAttribs(array('class'=>'bt_creaza'));
		$submit->setIgnore(true);
		$this->addElement($submit);
		$this->setElementFilters($filters);
	}
}
