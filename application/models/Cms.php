<?php
class Default_Model_DbTable_Cms extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_cms';
	protected $_primary = 'id';
}

class Default_Model_Cms
{
	protected $_id;
	protected $_position;
	protected $_layout;
	protected $_title;
	protected $_keywords;
	protected $_description;
	protected $_content;
	protected $_link;
	protected $_status;
	protected $_created;
	protected $_updated;
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

	public function setPosition($value)
	{
		$this->_position = (int) $value;
		return $this;
	}

	public function getPosition()
	{
		return $this->_position;
	}

	public function setLayout($value)
	{
		$this->_layout = (string) $value;
		return $this;
	}

	public function getLayout()
	{
		return $this->_layout;
	}

	public function setTitle($value)
	{
		$this->_title = (string) $value;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
	}

	public function setKeywords($value)
	{
		$this->_keywords = (!empty($value))?(string) $value:null;
		return $this;
	}

	public function getKeywords()
	{
		return $this->_keywords;
	}

	public function setDescription($value)
	{
		$this->_description = (!empty($value))?(string) $value:null;
		return $this;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setContent($value)
	{
		$this->_content = (string) $value;
		return $this;
	}

	public function getContent()
	{
		return $this->_content;
	}

	public function setLink($value)
	{
		$this->_link = (string) $value;
		return $this;
	}

	public function getLink()
	{
		return $this->_link;
	}

	public function setStatus($value)
	{
		$this->_status = (!empty($value))?true:false;
		return $this;
	}

	public function getStatus()
	{
		return $this->_status;
	}

	public function setCreated($value)
	{
		$this->_created = (!empty($value) && strtotime($value)>0)?strtotime($value):null;
		return $this;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setUpdated($value)
	{
		$this->_updated = (!empty($value) && strtotime($value)>0)?strtotime($value):null;
		return $this;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_CmsMapper());
		}
		return $this->_mapper;
	}

	public function save()
	{
		return $this->getMapper()->save($this);
	}

	public function find($id)
	{
		return $this->getMapper()->find($id, $this);
	}

	public function fetchAll($select = null)
	{
		return $this->getMapper()->fetchAll($select);
	}

	public function delete()
	{
		if(null === ($id = $this->getId())) {
			throw new Exception('Invalid record selected!');
		}
		return $this->getMapper()->delete($id);
	}
}

class Default_Model_CmsMapper
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
			$this->setDbTable('Default_Model_DbTable_Cms');
		}
		return $this->_dbTable;
	}

	public function save(Default_Model_Cms $model)
	{
		$data = array(
			'position'	 	 	 => $model->getPosition(),
			'layout'	 	 	 => $model->getLayout(),
			'title'	 	 		 => $model->getTitle(),
			'keywords'	 	 	 => $model->getKeywords(),
			'description'	 	 => $model->getDescription(),
			'content'	 	 	 => $model->getContent(),
			'link'	 	 		 => $model->getLink(),
			'status'			 => $model->getStatus()?'1':'0',
		);
		if(null === ($id = $model->getId())) {
			$data['created']	 = new Zend_Db_Expr('NOW()');
			$data['updated']	 = new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);
		} else {
			$data['updated']	 = new Zend_Db_Expr('NOW()');
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
		return $id;
	}

	public function find($id, Default_Model_Cms $model)
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
			$model = new Default_Model_Cms();
			$model->setOptions($row->toArray())
				->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function delete($id)
	{
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
		return $this->getDbTable()->delete($where);
	}
}