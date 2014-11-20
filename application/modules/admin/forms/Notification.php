<?php
class Admin_Form_Notification extends Zend_Form{
	function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmNotifications'));

		$user = new Zend_Form_Element_Multiselect('user');
		$user->setLabel('Trimite doar la Useri');
        $options = array();	
		$model = new Default_Model_AccountUsers();
		$select = $model->getMapper()->getDbTable()->select();				
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				$options[$value->getId()] = $value->getUsername();
			}
		}
        $user->addMultiOptions($options);
        $user->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$user->setAttribs(array('class'=>'','style'=>'height:150px;'));
		$user->setRequired(false);
		$this->addElement($user);
		
		$all = new Zend_Form_Element_Checkbox('all');
		$all->setLabel('Timite la toti');
		$all->setAttribs(array('class'=>''));
		$all->setRequired(false);
		$this->addElement($all);
		
		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Mesaj');
		$message->addValidator(new Zend_Validate_StringLength(1,3000));
		$message->setAttribs(array('class'=>'', 'cols'=>'', 'rows'=>'0', 'maxlenght'=>'3000','style'=>'height:150px;width:62%'));
		$message->setRequired(false);
		$this->addElement($message);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Trimite');
		$submit->setAttribs(array('class'=>'button1'));
		$submit->setIgnore(true);
		$this->addElement($submit);	
	}
}