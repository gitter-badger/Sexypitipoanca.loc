<?php
class Admin_Form_Post extends Zend_Form
{
	function addVideo()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formProductAdd', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

		$user = new Zend_Form_Element_Select('user_id');
		$user->setLabel('User');
        $options = array();
		$options['0'] = 'admin';
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

		$category = new Zend_Form_Element_Select('category_id');
		$category->setLabel('Category');
        $options = array();
		$model = new Default_Model_CatalogCategories();
		$select = $model->getMapper()->getDbTable()->select()
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
		$name->setLabel('Name');
        $name->addValidator(new Zend_Validate_Db_NoRecordExists(array('table'=>'j_catalog_products', 'field'=>'name')));
		$name->addValidator(new Zend_Validate_StringLength(1,96));
		$name->setAttribs(array('class'=>'', 'style'=>'width: 368px'));
		$name->setRequired(true);
		$this->addElement($name);

		$url = new Zend_Form_Element_Text('url');
		$url->setLabel('URL');
		$url->setAttribs(array('class'=>'', 'style'=>'width: 368px'));
		$url->setRequired(FALSE);
		$this->addElement($url);
		
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description');		
		$description->setAttribs(array('class'=>'', 'cols'=>'50', 'rows'=>'10'));
		$validator = new Zend_Validate_StringLength(array('max' => 500));
		$description->addValidator($validator);
		$description->setRequired(false);
		$this->addElement($description);

		$embed = new Zend_Form_Element_Textarea('embed');
		$embed->setLabel('Embed');		
		$embed->setAttribs(array('class'=>'', 'cols'=>'50', 'rows'=>'10'));
		$embed->setRequired(false);
		$this->addElement($embed);

		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Image');
		$image->setIsArray(true);
		$image->setAttribs(array('class'=>''));
		$image->setRequired(FALSE);
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

	function editVideo(Default_Model_CatalogProducts $model)
	{
		$this->user_id->setValue($model->getUser_id());
		$this->category_id->setValue($model->getCategory_id());
		$this->name->setValue($model->getName());
		$this->status->setValue($model->getStatus());
		$this->description->setValue($model->getDescription());

		$video = new Default_Model_Video();
		$select = $video->getMapper()->getDbTable()->select()
				->where('productId = ?', $model->getId());
		$result = $video->fetchAll($select);
		if(NULL != $result)
		{
			$this->url->setValue($result[0]->getUrl());
			$this->embed->setValue($result[0]->getEmbed());
			
			$photo = new Zend_Form_Element_Hidden('photo');
			$this->addElement($photo);
			$this->photo->setValue($result[0]->getImage());
		}

		$this->image->setRequired(false);
		$this->submit->setValue('modifica');
		$this->name->getValidator('Zend_Validate_Db_NoRecordExists')->setExclude(array('field'=>'name', 'value' => $model->getName()));
	}
}