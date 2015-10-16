<?php
class Admin_Form_Comments extends Zend_Form{
	public function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmNotifications'));
		
		$comment = new Zend_Form_Element_Textarea('comment');
		$comment->setLabel('Comment');
		$comment->addValidator(new Zend_Validate_StringLength(1,3000));
		$comment->setAttribs(array('class'=>'', 'cols'=>'', 'rows'=>'0', 'maxlenght'=>'3000','style'=>'height:150px;width:62%'));
		$comment->setRequired(false);
		$this->addElement($comment);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Moderate');
		$submit->setAttribs(array('class'=>'button1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
	
	public function edit(Default_Model_CatalogProductComments $model)
	{
		$this->comment->setValue($model->getComment());
	}
}