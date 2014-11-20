<?php
class Default_Model_DbTable_CatalogProducts extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_catalog_products';
	protected $_primary = 'id';
}
class Default_Model_CatalogProducts
{
	protected $_id;
	protected $_parent_id;
	protected $_user_id;
	protected $_category_id;
	protected $_type;
	protected $_position;
	protected $_name;
	protected $_description;
	protected $_visits;
	protected $_rating;
	protected $_ratingNumber;
	protected $_status;
	protected $_added;

	protected $_mapper;

	public function __construct(array $options = null)
	{
		if(is_array($options)) {
			$this->setOptions($options);
		}
	}

	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if(('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid '.$name.' property '.$method);
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if(('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid '.$name.' property '.$method);
		}
		return $this->$method();
	}

	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if(in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setParent_id($id)
	{
		$this->_parent_id = (int) $id;
		return $this;
	}

	public function getParent_id()
	{
		return $this->_parent_id;
	}

	public function setUser_id($id)
	{
		$this->_user_id = (int) $id;
		return $this;
	}

	public function getUser_id()
	{
		return $this->_user_id;
	}

	public function setCategory_id($id)
	{
		$this->_category_id = (int) $id;
		return $this;
	}

	public function getCategory_id()
	{
		return $this->_category_id;
	}

	public function setType($string)
	{
		$this->_type = (string) $string;
		return $this;
	}

	public function getType()
	{
		return $this->_type;
	}
	
	public function setPosition($value)
	{
		$this->_position = $value ? (int) $value : NULL;
		return $this;
	}

	public function getPosition()
	{
		return $this->_position;
	}

	public function setName($string)
	{
		$this->_name = (string) strip_tags($string);
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}
	
	public function setDescription($string)
	{
		$this->_description = (!empty($string))?(string) $string:null;
		return $this;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setVisits($int)
	{
		$this->_visits = (int) ($int);
		return $this;
	}

	public function getVisits()
	{
		return $this->_visits;
	}

	public function setRating($float)
	{
		$this->_rating = (float) ($float);
		return $this;
	}

	public function getRating()
	{
		return $this->_rating;
	}

	public function setRatingNumber($int)
	{
		$this->_ratingNumber = (int) ($int);
		return $this;
	}

	public function getRatingNumber()
	{
		return $this->_ratingNumber;
	}

	public function setStatus($status)
	{
		$this->_status = (!empty($status))?(string) $status:'0';
		return $this;
	}

	public function getStatus()
	{
		return $this->_status;
	}

	public function setAdded($date)
	{
		$this->_added = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
		return $this;
	}

	public function getAdded()
	{
		return $this->_added;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_CatalogProductsMapper());
		}
		return $this->_mapper;
	}

	public function find($id)
	{
		return $this->getMapper()->find($id, $this);
	}

	public function fetchAll($select = null)
	{
		return $this->getMapper()->fetchAll($select);
	}

	public function save()
	{
		return $this->getMapper()->save($this);
	}

	public function activate()
	{
		return $this->getMapper()->activate($this);
	}

	public function delete()
	{
		if(null === ($id = $this->getId())) {
			throw new Exception('Invalid record selected!');
		}
		return $this->getMapper()->delete($id);
	}
}

class Default_Model_CatalogProductsMapper
{
	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if(is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if(!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if(null === $this->_dbTable) {
			$this->setDbTable('Default_Model_DbTable_CatalogProducts');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_CatalogProducts $model)
	{
		$result = $this->getDbTable()->find($id);
		if(0 == count($result)) {
			return;
		}
		$row = $result->current();
		$model->setOptions($row->toArray());
		return $model;
	}

	public function fetchAll($select)
	{
		$resultSet = $this->getDbTable()->fetchAll($select);
		$entries = array();
		foreach($resultSet as $row) {
			$model = new Default_Model_CatalogProducts();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_CatalogProducts $model)
	{
		$data = array(
			'parent_id'				=> $model->getParent_id(),
			'user_id'				=> $model->getUser_id(),
			'category_id'			=> $model->getCategory_id(),
			'type'					=> $model->getType(),
			'position'				=> $model->getPosition(),
			'name'					=> $model->getName(),
			'description'			=> $model->getDescription(),
			'visits'				=> $model->getVisits(),
			'rating'				=> $model->getRating(),
			'ratingNumber'			=> $model->getRatingNumber(),
			'status'				=> $model->getStatus(),
		);
		
		if(null === ($id = $model->getId())) {
			$data['added']			= new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
		return $id;
	}
	
	public function activate(Default_Model_CatalogProducts $model)
    {
		$id = $model->getId();
        $data = array();
        if(NULL == $id)
		{
        	throw new Exception("Invalid gallery selected!");
        }
		else
		{
			$data['status']	 = '1';
			$data['added']	 = new Zend_Db_Expr('NOW()');
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
        return $id;
    }

	public function delete($id)
	{
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
		return $this->getDbTable()->delete($where);
	}
}