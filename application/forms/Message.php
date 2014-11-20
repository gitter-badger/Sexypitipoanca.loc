<?php
class Default_Form_Message extends Zend_Form
{
	function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmMessage'));
		
		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Mesaj');
		$message->addValidator(new Zend_Validate_StringLength(1,3000));
		$message->setAttribs(array('class'=>'', 'cols'=>'', 'rows'=>'0', 'maxlenght'=>'3000'));
		$message->setRequired(false);
		$this->addElement($message);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Trimite');
		$submit->setAttribs(array('class'=>'bt_creaza btnTrimiteMesaj'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}
