<?php

/**
 * @property null|Zend_Form|Zend_Form_Element title
 * @property null|Zend_Form|Zend_Form_Element description
 * @property null|Zend_Form|Zend_Form_Element url
 * @property null|Zend_Form|Zend_Form_Element submit
 */
class Admin_Form_ImportSource extends Zend_Form{
	function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'frmImportSources'));
		
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title');
		$title->setRequired(true);
		$this->addElement($title);

		$description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description');
        $description->setRequired(true);
		$this->addElement($description);

		$url = new Zend_Form_Element_Text('url');
		$url->setLabel('Url');
		$url->setRequired(true);
		$this->addElement($url);

        $schema = new Zend_Form_Element_Text('schema');
        $schema->setLabel('Schema');
        $schema->setRequired(true);
        $this->addElement($schema);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Add source');
		$submit->setAttribs(array('class'=>'button1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
	
	function edit(Admin_Model_ImportSource $model)
	{
		$this->title->setValue($model->getTitle());
        $this->description->setValue($model->getDescription());
        $this->url->setValue($model->getDescription());
        $this->submit->setValue('Modify source');
	}
}