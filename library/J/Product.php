<?php
class J_Product
{
	protected $_id;
	protected $_name;
	protected $_type;
	protected $_categoryId;
	protected $_category;
	protected $_userId;
	protected $_smallImage;
	protected $_bigImage;
	protected $_smallImages;
	protected $_bigImages;
	protected $_tags;
	protected $_comments;
	protected $_commentsNo;
	protected $_visits;
	protected $_description;

	public function setId($string)
	{
		$this->_id = $string;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setName($string)
	{
		$this->_name = $string;
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setType($string)
	{
		$this->_type = $string;
		return $this;
	}

	public function getType()
	{
		return $this->_type;
	}
	
	public function setCategory($string)
	{
		$this->_category = $string;
		return $this;
	}

	public function getCategory()
	{
		return $this->_category;
	}

	public function setCategoryId($string)
	{
		$this->_categoryId = $string;
		return $this;
	}

	public function getCategoryId()
	{
		return $this->_categoryId;
	}

	public function setUserId($int)
	{
		$this->_userId = (int) $int;
		return $this;
	}

	public function getUserId()
	{
		return $this->_userId;
	}

	public function setSmallImage($string)
	{
		$this->_smallImage = $string;
		return $this;
	}

	public function getSmallImage()
	{
		return $this->_smallImage;
	}

	public function setBigImage($string)
	{
		$this->_bigImage = $string;
		return $this;
	}

	public function getBigImage()
	{
		return $this->_bigImage;
	}

	public function setSmallImages($array)
	{
		$this->_smallImages = $array;
		return $this;
	}

	public function getSmallImages()
	{
		return $this->_smallImages;
	}

	public function setBigImages($array)
	{
		$this->_bigImages = $array;
		return $this;
	}

	public function getBigImages()
	{
		return $this->_bigImages;
	}

	public function setTags($array)
	{
		$this->_tags = $array;
		return $this;
	}

	public function getTags()
	{
		return $this->_tags;
	}

	public function setComments($array)
	{
		$this->_comments = $array;
		return $this;
	}

	public function getComments()
	{
		return $this->_comments;
	}

	public function setCommentsNo($string)
	{
		$this->_commentsNo = $string;
		return $this;
	}

	public function getCommentsNo()
	{
		return $this->_commentsNo;
	}

	public function setVisits($string)
	{
		$this->_visits = $string;
		return $this;
	}

	public function getVisits()
	{
		return $this->_visits;
	}
	
	public function setDescription($value)
	{
		$this->_description = $value;
		return $this;
	}
	
	public function getDescription()
	{
		return $this->_description;
	}

	public function product($id, $details)
	{
		$model = new Default_Model_CatalogProducts();
		if($model->find($id)) {
			$this->setId($model->getId());
			$this->setUserId($model->getUser_id());
			$this->setName($model->getName());
			$this->setType($model->getType());
			if(in_array('category', $details)) {
				$this->category($model->getCategory_id());
			}
			if(in_array('image', $details)) {
				$this->image($model->getId(), $model->getUser_id(), $model->getName());
			}
			if(in_array('images', $details)) {
				$this->images($model->getId(), $model->getUser_id(), $model->getName());
			}
			if(in_array('tags', $details)) {
				$this->tags($model->getId());
			}
			if(in_array('comments', $details)) {
				$this->comments($model->getId());
			}
			if(in_array('commentsNo', $details)) {
				$this->commentsNo($model->getId());
			}
			if(in_array('visits', $details)) {
				$this->visits($model->getId());
			}
			if(in_array('description', $details)) {
				$this->setDescription($model->getDescription());
			}
		}
	}

	public function category($id)
	{
		$model = new Default_Model_CatalogCategories();
		if($model->find($id)) {
			$this->setCategoryId($model->getId());
			$this->setCategory($model->getName());
		}
	}

	public function image($id, $user_id, $name)
	{
		$allowed = "/[^a-z0-9\\-\\_]+/i";
		$name = preg_replace($allowed,"-", strtolower($name));

		$model = new Default_Model_CatalogProductImages();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id)
				->order('position ASC')
				->limit('1')
				;
		if(($result = $model->fetchAll($select))) {
			if($user_id) {
				$smallImagePath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$user_id.'/'.$name.'/small/'.$result[0]->getName();
				if(file_exists($smallImagePath)) {
					$smallImage = '/media/catalog/products/'.$user_id.'/'.$name.'/small/'.$result[0]->getName();
				} else { $smallImage = '/theme/default/images/no-pic-150.gif'; }
				$bigImagePath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$user_id.'/'.$name.'/big/'.$result[0]->getName();
				if(file_exists($bigImagePath)) {
					$bigImage = '/media/catalog/products/'.$user_id.'/'.$name.'/big/'.$result[0]->getName();
				} else { $bigImage = '/theme/default/images/no-pic-150.gif'; }
			} else {
				$smallImagePath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/0/'.$name.'/small/'.$result[0]->getName();
				if(file_exists($smallImagePath)) {
					$smallImage = '/media/catalog/products/0/'.$name.'/small/'.$result[0]->getName();
				} else { $smallImage = '/theme/default/images/no-pic-150.gif'; }
				$bigImagePath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/0/'.$name.'/big/'.$result[0]->getName();
				if(file_exists($bigImagePath)) {
					$bigImage = '/media/catalog/products/0/'.$name.'/big/'.$result[0]->getName();
				} else { $bigImage = '/theme/default/images/no-pic-150.gif'; }
			}
			$this->setSmallImage($smallImage);
			$this->setBigImage($bigImage);
		}
	}

	public function images($id, $user_id, $name)
	{
		$allowed = "/[^a-z0-9\\-\\_]+/i";
		$name = preg_replace($allowed,"-", strtolower($name));

		$smallImages = array();
		$bigImages = array();
		$model = new Default_Model_CatalogProductImages();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id)
				->order('position ASC');
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				if($user_id) {
					$smallImagesPath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$user_id.'/'.$name.'/small/'.$value->getName();
					if(file_exists($smallImagesPath)) {
						$smallImages[] = '/media/catalog/products/'.$user_id.'/'.$name.'/small/'.$value->getName();
					} else { $smallImages[] = '/theme/default/images/no-pic-150.gif'; }
					$bigImagesPath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/'.$user_id.'/'.$name.'/big/'.$value->getName();
					if(file_exists($bigImagesPath)) {
						$bigImages[] = '/media/catalog/products/'.$user_id.'/'.$name.'/big/'.$value->getName();
					} else { $bigImages[] = '/theme/default/images/no-pic-150.gif'; }
				} else {
					$smallImagesPath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/0/'.$name.'/small/'.$value->getName();
					if(file_exists($smallImagesPath)) {
						$smallImages[] = '/media/catalog/products/0/'.$name.'/small/'.$value->getName();
					} else { $smallImages[] = '/theme/default/images/no-pic-150.gif'; }
					$bigImagesPath = APPLICATION_PUBLIC_PATH.'/media/catalog/products/0/'.$name.'/big/'.$value->getName();
					if(file_exists($bigImagesPath)) {
						$bigImages[] = '/media/catalog/products/0/'.$name.'/big/'.$value->getName();
					} else { $bigImages[] = '/theme/default/images/no-pic-150.gif'; }
				}
			}
			$this->setSmallImages($smallImages);
			$this->setBigImages($bigImages);
		}
	}

	public function tags($id)
	{
		$tags = array();
		$model = new Default_Model_CatalogProductTags();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id)
				;
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				$model2 = new Default_Model_Tags();
				if($model2->find($value->getTag_id())) {
					$tags[] = $model2->getName();
				}
			}
			$this->setTags($tags);
		}
	}

	public function comments($id)
	{
		$comment = array();
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id);
		if(($result = $model->fetchAll($select))) {
			foreach($result as $value) {
				$comment[$value->getId()]['userId'] = $value->getUserId();
				$comment[$value->getId()]['name'] = $value->getName();
				$comment[$value->getId()]['comment'] = $value->getComment();
				$comment[$value->getId()]['added'] = $value->getAdded();
			}
			$this->setComments($comment);
		}
	}

	public function commentsNo($id)
	{
		$model = new Default_Model_CatalogProductComments();
		$select = $model->getMapper()->getDbTable()->select()
				->from($model->getMapper()->getDbTable(), array('id'=>'COUNT(id)'))
				->where('product_id = ?', $id)
				;
		if(($result = $model->fetchAll($select))) {
			$this->setCommentsNo($result[0]->getId());
		}
	}

	public function visits($id)
	{
		$model = new Default_Model_CatalogProductVisits();
		$select = $model->getMapper()->getDbTable()->select()
				->where('product_id = ?', $id)
				;
		if(($result = $model->fetchAll($select))) {
			$this->setVisits($result[0]->getVisits());
		} else {
			$this->setVisits('0');
		}
	}
}