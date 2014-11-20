<?php
class Admin_Form_Settings extends Zend_Form
{
	function init()
	{
		// Set the method for the display form to POST
	}

	public function rambus()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmRamburs'));

		$rambus = new Zend_Form_Element_Hidden('rambus');
		$rambus->setValue('rambus');
		$this->addElement($rambus);

		$price = new Zend_Form_Element_Text('price');
		$price->setLabel(Zend_Registry::get('translate')->_('settings_payments_table_price'));
		$price->setAttribs(array('style'=>'width:60px;'));
		$price->setRequired(true);
		$this->addElement($price);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue(Zend_Registry::get('translate')->_('settings_payments_table_update'));
		$submit->setAttribs(array('class'=>'button1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function rambusPercentage()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmRambusPercentage'));

		$rambus = new Zend_Form_Element_Hidden('rambus');
		$rambus->setValue('rambusPercentage');
		$this->addElement($rambus);

		$price = new Zend_Form_Element_Text('price');
		$price->setLabel(Zend_Registry::get('translate')->_('settings_payments_table_price'));
		$price->setAttribs(array('style'=>'width:60px;'));
		$price->setRequired(true);
		$this->addElement($price);
		
		$percent = new Zend_Form_Element_Select('percent');
		$percent->setLabel(Zend_Registry::get('translate')->_('settings_payments_table_percentage'));
		$percent->setRegisterInArrayValidator(false);
		$percentOptions = array();
		for($i=0; $i<=100; $i++){
			$percentOptions[$i] = $i.'%';
		}
		$percent->addMultiOptions($percentOptions);
		$percent->setAttribs(array('class'=>'select', 'style'=>'width:50px;'));
		$percent->setRequired(true);
		$this->addElement($percent);
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue(Zend_Registry::get('translate')->_('settings_payments_table_update'));
		$submit->setAttribs(array('class'=>'button1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	function productTaxesAdd()
	{
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'frmAddProductTaxes'));
		
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel(Zend_Registry::get('translate')->_('settings_add_tax_table_name'));
        $name->setAttribs(array('maxlength'=>'120', 'class'=>'validate[required,length[1,120]]', 'style'=>'width:250px;'));
        $name->setRequired(true);
		$this->addElement($name);
		
        $value = new Zend_Form_Element_Text('value');
        $value->setLabel(Zend_Registry::get('translate')->_('settings_add_tax_table_value'));
        $value->setAttribs(array('maxlength'=>'120', 'class'=>'validate[required,length[1,120]]', 'style'=>'width:250px;'));
        $value->setRequired(true);
		$valueFloat = new Zend_Validate_Float();
        $value->addValidator($valueFloat);
		$this->addElement($value);

		$type = new Zend_Form_Element_Select('type');
		$type->setLabel(Zend_Registry::get('translate')->_('settings_add_tax_table_type'));
		$type->setRegisterInArrayValidator(false);
		$typeOptions = array('%' => '%','flatfee'=>'Flat Fee');
		$type->addMultiOptions($typeOptions);
		$type->setAttribs(array('class'=>'select', 'style'=>'width:100px;'));
		$type->setRequired(true);
		$this->addElement($type);

        $status = new Zend_Form_Element_Radio('status');
        $status->setRequired(true);
        $status->setLabel(Zend_Registry::get('translate')->_('settings_add_tax_table_status'));
        $optionStatus = array(Zend_Registry::get('translate')->_('settings_taxes_table_inactive'),Zend_Registry::get('translate')->_('settings_taxes_table_active'));
        $status->addMultiOptions($optionStatus);
        $status->addValidator(new Zend_Validate_InArray(array_keys($optionStatus)));
		$status->setValue('1');
        $status->setAttribs(array('class'=>'validate[required]'));
        $status->setSeparator('');
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue(Zend_Registry::get('translate')->_('settings_taxes_table_submit'));
        $submit->setAttribs(array('class'=>'button1'));
        $submit->setIgnore(true);
		$this->addElement($submit);
	}

	function productTaxesEdit(Default_Model_ProductsTaxes $value)
	{
		$this->name->setValue($value->getName());
		$this->value->setValue($value->getValue());
		$this->type->setValue($value->getType());
		$this->status->setValue($value->getStatus());
		$this->submit->setValue(Zend_Registry::get('translate')->_('products_add_table_button_modify'));
	}

	function vatEdit(Default_Model_ProductsTaxes $value)
	{
	   $this->name->setValue($value->getName());
	   $this->value->setValue($value->getValue());
	   $this->status->setValue($value->getStatus());
	   $this->submit->setValue(Zend_Registry::get('translate')->_('products_add_table_button_modify'));
	   $this->removeElement('type');	 
	}
}
