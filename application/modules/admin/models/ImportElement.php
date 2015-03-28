<?php
class Admin_Model_DbTable_ImportItem extends Zend_Db_Table_Abstract
{
	protected $_name    = 'import_element';
	protected $_primary = 'id';
}

class Admin_Model_ImportItem
{
	protected $_id;
	protected $_title;
	protected $_description;
	protected $_url;
	protected $_created;
	protected $_modified;
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

	public function setTitle($title)
	{
		$this->_title = (string) $title;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
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

	public function setUrl($url)
	{
		$this->_url = (string) $url;
		return $this;
	}

	public function getUrl()
	{
		return $this->_url;
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
		$this->_modified = (!empty($value) && strtotime($value)>0)?strtotime($value):null;
		return $this;
	}

	public function getUpdated()
	{
		return $this->_modified;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Admin_Model_ImportSourceMapper());
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

class Admin_Model_ImportSourceMapper
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
			$this->setDbTable('Admin_Model_DbTable_ImportSource');
		}
		return $this->_dbTable;
	}

	public function save(Admin_Model_ImportSource $model)
	{
		$data = array(
			'title'	 	 		 => $model->getTitle(),
			'description'	 	 => $model->getDescription(),
			'url'	 	 		 => $model->getUrl(),
		);
		if(null === ($id = $model->getId())) {
			$data['created']	 = new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);
		} else {
			$data['modified']	 = new Zend_Db_Expr('NOW()');
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
		return $id;
	}

	public function find($id, Admin_Model_ImportSource $model)
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
			$model = new Admin_Model_ImportSource();
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