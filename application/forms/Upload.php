<?php
class Default_Form_Intrari extends Zend_Form
{
	function init()
	{
		
	}

	function intrari()
	{		
		$this->setMethod('post');
		$this->setAction(WEBROOT.'/iframe/intrari');
		$this->addAttribs(array('id'=>'fileupload', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		
		$nrInreg = new Zend_Form_Element_Text('nrInreg');
		$nrInreg->setLabel('Numar inregistare document');
		$nrInreg->setAttribs(array('class'=>'tsFormText'));
		$nrInreg->addErrorMessages(array('Camp obligatoriu'));
		$nrInreg->setRequired(FALSE);
		$this->addElement($nrInreg);

		$nomenclatorId = new Zend_Form_Element_Select('nomenclatorId');
		$nomenclatorId->setLabel('Nomenclator');
		$options = array();
		$model = new Default_Model_Nomenclator();
		$select = $model->getMapper()->getDbTable()->select()
				->where('NOT deleted')
				->order('nume ASC');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			foreach($result as $value)
			{
				$options[$value->getId()] = $value->getNume();
			}
		}
		$nomenclatorId->addMultiOptions($options);
		$nomenclatorId->setAttribs(array('class'=>'tsFormSelect required selectmenu'));
		$nomenclatorId->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$nomenclatorId->setRequired(FALSE);
		$nomenclatorId->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($nomenclatorId);
		
		$tipDocument = new Zend_Form_Element_Select('tipDocument');
		$tipDocument->setLabel('Tip document');
		$optionsTipDocument = array();
		$model = new Default_Model_TipDocument();
		$select = $model->getMapper()->getDbTable()->select()
				->where('NOT deleted')
				->order('nume ASC');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			foreach($result as $value)
			{
				$optionsTipDocument[$value->getId()] = $value->getNume();
			}
		}
		$tipDocument->addMultiOptions($optionsTipDocument);
		$tipDocument->setAttribs(array('class'=>'tsFormSelect required selectmenu'));
		$tipDocument->addValidator(new Zend_Validate_InArray(array_keys($optionsTipDocument)));
		$tipDocument->setRequired(FALSE);
		$tipDocument->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($tipDocument);
		
		$cuprins = new Zend_Form_Element_Textarea('cuprins');
		$cuprins->setLabel('Cuprins');
		$cuprins->setAttribs(array('class'=>'tsFormTextarea'));
		$validator = new Zend_Validate_StringLength(array('max' => 300));
		$cuprins->addValidator($validator);
		$cuprins->setRequired(FALSE);
		$cuprins->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($cuprins);
		
		$nrExemplare = new Zend_Form_Element_Text('nrExemplare');
		$nrExemplare->setLabel('Numar exemplare');
		$nrExemplare->setAttribs(array('class'=>'tsFormText'));
		$nrExemplare->addErrorMessages(array('Camp obligatoriu'));
		$nrExemplare->setRequired(FALSE);
		$this->addElement($nrExemplare);
		
		$nrFile = new Zend_Form_Element_Text('nrFile');
		$nrFile->setLabel('Numar file');
		$nrFile->setAttribs(array('class'=>'tsFormText'));
		$nrFile->addErrorMessages(array('Camp obligatoriu'));
		$nrFile->setRequired(FALSE);
		$this->addElement($nrFile);
		
		$expeditor = new Zend_Form_Element_Text('expeditor');
		$expeditor->setLabel('Expeditor');
		$expeditor->setAttribs(array('class'=>'tsFormText'));
		$expeditor->setRequired(FALSE);
		$expeditor->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($expeditor);

		$adresaExpeditor = new Zend_Form_Element_Textarea('adresaExpeditor');
		$adresaExpeditor->setLabel('Adresa expeditor');
		$adresaExpeditor->setAttribs(array('class'=>'tsFormTextarea'));
		$validator = new Zend_Validate_StringLength(array('max' => 300));
		$adresaExpeditor->addValidator($validator);
		$adresaExpeditor->setRequired(FALSE);
		$adresaExpeditor->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($adresaExpeditor);
		
		$raspuns = new Zend_Form_Element_Select('raspuns');
		$raspuns->setLabel('Raspuns');
        $options = array('0'=>'Nu','1'=>'Da');
        $raspuns->addMultiOptions($options);
		$raspuns->setValue('0');
		$raspuns->setAttribs(array('class'=>'tsFormSelect selectmenu'));
		$raspuns->setRequired(FALSE);
		$this->addElement($raspuns);
		
		$departament = new Zend_Form_Element_Select('depRaspuns');
		$departament->setLabel('Alegeți departamentul');
		$departament_options = array();
		$model = new Default_Model_Departament();
		$select = $model->getMapper()->getDbTable()->select()
				->where('NOT deleted')
				->order('nume ASC');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			foreach($result as $value)
			{
				$departament_options[$value->getId()] = $value->getNume();
			}
		}
		$departament->addMultiOptions($departament_options);
		$departament->addValidator(new Zend_Validate_InArray(array_keys($departament_options)));
		$departament->setAttribs(array('class'=>'tsFormSelect required selectmenu'));
		$departament->setRequired(FALSE);
		$departament->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($departament);
		
		$termenRaspuns = new Zend_Form_Element_Text('termenRaspuns');
		$termenRaspuns->setLabel('Termen raspuns');
		$termenRaspuns->addValidator(new Zend_Validate_StringLength(5,32));
		$termenRaspuns->setAttribs(array('id'=>'datepicker', 'class'=>'tsDatepicker'));
		$termenRaspuns->setRequired(FALSE);
		$termenRaspuns->addErrorMessages(array('Camp obligatoriu'));
		$this->addElement($termenRaspuns);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Adauga intrare');
		$submit->setAttribs(array('id'=>'btnLogin', 'class'=>'submit tsSubmitLogin fL', 'style'=>'margin: 5px 0 0 20px; '));
		$submit->setRequired(FALSE);
		$submit->setIgnore(TRUE);
		$this->addElement($submit);
	}
	
	function iesiri()
	{
		$user = '';
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if(null != $authAccount){
			if(null != $authAccount->getId()){
				$model = new Default_Model_Utilizatori();
				if($model->find($authAccount->getId()))
				{
					$user = $model->getNumeUtilizator();
				}
			}
		}
		
		$this->expeditor->setValue($user);
		$this->expeditor->setAttribs(array('readonly'=>'readonly'));
	}
	
	function semnatura()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmSemnatura'));

		$user = '';
		$departamentId = '';
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if(null != $authAccount){
			if(null != $authAccount->getId()){
				$model = new Default_Model_Utilizatori();
				if($model->find($authAccount->getId()))
				{
					$user = $model->getNumeUtilizator();
					$departamentId = $model->getDepartament()->getId();
				}
			}
		}
		
		$departament = new Zend_Form_Element_Hidden('departament');
		$departament->setValue($departamentId);
		$departament->setRequired(TRUE);
		$this->addElement($departament);
		
		$username = new Zend_Form_Element_Text('utilizator');
		$username->setLabel('Nume utilizator');
		$username->addValidator(new Zend_Validate_StringLength(5,32));
		$username->setValue($user);
		$username->setAttribs(array('class'=>'text large required', 'id'=>'utilizator', 'readonly'=>'readonly'));
		$username->setRequired(TRUE);
		$this->addElement($username);

		$password = new Zend_Form_Element_Password('parola');
		$password->setLabel('Parola utilizator');
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'text large required', 'id'=>'parola'));
		$password->setRequired(TRUE);
		$this->addElement($password);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Semneaza');
		$submit->setValue('semneaza');
		$submit->setAttribs(array('id'=>'btnLogin', 'class'=>'submit tsSubmitLogin fL'));
		$submit->setIgnore(TRUE);
		$this->addElement($submit);
	}
}