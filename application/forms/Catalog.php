<?php
class Default_Form_Catalog extends Zend_Form
{
	function init()
	{
		
	}

	function productAdd()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formProductAdd', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('productAdd');
		$this->addElement($control);

		$category = new Zend_Form_Element_Select('category');
		$category->setLabel('Alege categoria (obligatoriu)');
		$options = array();
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
				->where('status = ?', '1')
				->order('position ASC');
		$result = $model->fetchAll($select);
		if($result)
		{
			foreach($result as $value)
			{
				$options[$value->getId()] = $value->getName();
			}
		}
		$category->addMultiOptions($options);
		$category->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$category->setAttribs(array('class'=>'f3'));
		$category->setRequired(true);
		$this->addElement($category);

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('Titlu galerie (obligatoriu)');
        $name->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'j_catalog_products', 'field'=>'name')));
		$name->addValidator(new Zend_Validate_StringLength(1,32));
		$name->setAttribs(array('class'=>'f3','size'=>'32'));
		$name->setRequired(true);
		$this->addElement($name);

		$tags = new Zend_Form_Element_Textarea('tags');
		$tags->setLabel('Taguri galerie');
		$tags->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)));
		$tags->addValidator(new Zend_Validate_StringLength(3,500));
		$tags->setAttribs(array('class'=>'txt', 'cols'=>'50', 'rows'=>'10'));
		$tags->setRequired(true);
		$this->addElement($tags);

		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Upload galerie');
		$image->setAttrib('multiple', true);
		$image->setIsArray(true);
		$image->setAttribs(array('class'=>''));
		$image->setRequired(true);
		$this->addElement($image);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('trimite');
		$submit->setAttribs(array('class'=>'bt1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	function comentAdd()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formComentAdd', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('comentAdd');
		$this->addElement($control);

		$message = new Zend_Form_Element_Textarea('message');
		$message->setLabel('Lasa un comentariu');
		$message->setAttribs(array('class'=>'txt', 'cols'=>'50', 'rows'=>'10'));
		$message->setRequired(true);
		$this->addElement($message);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Trimite');
		$submit->setAttribs(array('class'=>'bt3'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}