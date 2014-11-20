<?php
class Admin_Form_Catalog extends Zend_Form
{
	function init()
	{
		
	}

	function productAdd()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formProductAdd', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$user = new Zend_Form_Element_Select('user');
		$user->setLabel('user');
        $options = array();
		$options[] = 'Selecteaza user';
		$model = new Default_Model_AccountUsers();
		$select = $model->getMapper()->getDbTable()->select()
				->where('id IN (?)', explode(',', TEST_USERS));
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				$options[$value->getId()] = $value->getUsername();
			}
		}
        $user->addMultiOptions($options);
        $user->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$user->setAttribs(array('class'=>''));
		$user->setRequired(false);
		$this->addElement($user);

		$category = new Zend_Form_Element_Select('category');
		$category->setLabel('name');
        $options = array();
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
				->where('status = ?', '1')
				->order('position ASC');
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				$options[$value->getId()] = $value->getName();
			}
		}
        $category->addMultiOptions($options);
        $category->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$category->setAttribs(array('class'=>''));
		$category->setRequired(false);
		$this->addElement($category);

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('name');
        $name->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'j_catalog_products', 'field'=>'name')));
		$name->addValidator(new Zend_Validate_StringLength(1,96));
		$name->setAttribs(array('class'=>'', 'style'=>'width: 368px;'));
		$name->setRequired(true);
		$this->addElement($name);

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description');		
		$description->setAttribs(array('class'=>'', 'cols'=>'50', 'rows'=>'10'));
		$validator = new Zend_Validate_StringLength(array('max' => 300));
		$description->addValidator($validator);
		$description->setRequired(false);
		$this->addElement($description);
		
		$tags = new Zend_Form_Element_Textarea('tags');
		$tags->setLabel('Tags');
		$tags->addValidator(new Zend_Validate_StringLength(3,500));
		$tags->setAttribs(array('class'=>'', 'cols'=>'50', 'rows'=>'10'));
		$tags->setRequired(true);
		$this->addElement($tags);

		$image = new Zend_Form_Element_File('image');
		$image->setAttrib('multiple', true);
		$image->setIsArray(true);
		$image->setAttribs(array('class'=>''));
		$image->setRequired(true);
//		$image->addValidator('Count', false, array('min' => 1, 'max' => 20));
		$this->addElement($image);

		$status = new Zend_Form_Element_Radio('status');
		$status->setLabel('status');
        $options = array('0'=>'inactive','1'=>'active');
        $status->addMultiOptions($options);
		$status->setValue('0');
		$status->setAttribs(array('class'=>''));
		$status->setRequired(true);
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('trimite');
		$submit->setAttribs(array('class'=>'bt1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	function productEdit(Default_Model_CatalogProducts $model)
	{
		$this->category->setValue($model->getCategory_id());
		$this->user->setValue($model->getUser_id());
		$this->name->setValue($model->getName());
		$this->status->setValue($model->getStatus());
		$this->description->setValue($model->getDescription());
		$this->submit->setValue('modifica');
		
		$image = new Zend_Form_Element_File('image');
		$image->setAttrib('multiple', true);
		$image->setIsArray(true);
		$image->setAttribs(array('class'=>''));
		$image->setRequired(false);
		$this->addElement($image);		
		

		$tags = new Zend_Form_Element_Textarea('tags');
		$tags->setLabel('tags');
		$tags->addValidator(new Zend_Validate_StringLength(3,500));
		$tags->setAttribs(array('class'=>'', 'cols'=>'50', 'rows'=>'10'));
		$tags->setRequired(false);
		$this->addElement($tags);

		$this->name->getValidator('Zend_Validate_Db_NoRecordExists')->setExclude(array('field'=>'name', 'value' => $model->getName()));
	}

	function categoriesAdd()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formCategoriesAdd', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$name = new Zend_Form_Element_Text('name');
		$name->setLabel('name');
		$name->setAttribs(array('class'=>''));
		$name->setRequired(true);
		$this->addElement($name);

		$status = new Zend_Form_Element_Radio('status');
		$status->setLabel('status');
        $options = array('1'=>'active', '0'=>'inactive');
        $status->addMultiOptions($options);
		$status->setValue('1');
		$status->setAttribs(array('class'=>''));
		$status->setRequired(true);
		$this->addElement($status);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('trimite');
		$submit->setAttribs(array('class'=>'bt1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	function categoriesEdit(Default_Model_CatalogCategories $model)
	{
		$this->name->setValue($model->getName());
		$this->status->setValue($model->getStatus());
		$this->submit->setValue('modifica');
	}

	function visitsAdd()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formVisitsAdd', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$min = new Zend_Form_Element_Text('min');
		$min->setLabel('minimum number of visits');
		$min->setDescription('betwen 1 - 99');
		$min->addValidator(new Zend_Validate_Digits);
		$min->addValidator(new Zend_Validate_Between(1, 99));
		$min->setAttribs(array('class'=>''));
		$min->setRequired(false);
		$this->addElement($min);

		$max = new Zend_Form_Element_Text('max');
		$max->setLabel('maximum number of visits');
		$max->setDescription('betwen 100 - 999');
		$max->addValidator(new Zend_Validate_Digits);
		$max->addValidator(new Zend_Validate_Between(100, 999));
		$max->setAttribs(array('class'=>''));
		$max->setRequired(false);
		$this->addElement($max);

		$fixed = new Zend_Form_Element_Text('fixed');
		$fixed->setLabel('fixed');
		$fixed->setDescription('betwen 1 - 999 , empty if min & max is set');
		$fixed->addValidator(new Zend_Validate_Digits);
		$fixed->addValidator(new Zend_Validate_Between(1, 999));
		$fixed->setAttribs(array('class'=>''));
		$fixed->setRequired(false);
		$this->addElement($fixed);

		$products = new Zend_Form_Element_Multiselect('products');
		$products->setLabel('select products to add visits');
        $options = array();
		$model = new Default_Model_CatalogProductVisits();
		$select = $model->getMapper()->getDbTable()->select()
				->order('visits ASC');
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				$model2 = new Default_Model_CatalogProducts();
				if($model2->find($value->getProduct_id())) {
					$options[$model2->getId()] = $model2->getName().' ('.$value->getVisits().' visit/s) ';
				}
			}
		}
        $products->addMultiOptions($options);
        $products->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$products->setAttribs(array('class'=>''));
		$products->setRequired(false);
		$this->addElement($products);

		$all = new Zend_Form_Element_Checkbox('all');
		$all->setLabel('add visits to all products');
		$all->setAttribs(array('class'=>''));
		$all->setRequired(false);
		$this->addElement($all);

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('trimite');
		$submit->setAttribs(array('class'=>'bt1'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}