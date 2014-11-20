<?php
class Default_Model_DbTable_Marketing extends Zend_Db_Table_Abstract
{
	protected $_name    = 'marketing';
	protected $_primary = 'id';
}

class Default_Model_Marketing
{
	protected $_id;
	protected $_type;
	protected $_code;
	protected $_status;
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

	public function setType($value)
	{
		$this->_type = (string) $value;
		return $this;
	}

	public function getType()
	{
		return $this->_type;
	}

	public function setCode($value)
	{
		$this->_code = (string) $value;
		return $this;
	}

	public function getCode()
	{
		return $this->_code;
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

	public function setModified($value)
	{
		$this->_modified = (!empty($value) && strtotime($value)>0)?strtotime($value):null;
		return $this;
	}

	public function getModified()
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
			$this->setMapper(new Default_Model_MarketingMapper());
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

class Default_Model_MarketingMapper
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
			$this->setDbTable('Default_Model_DbTable_Marketing');
		}
		return $this->_dbTable;
	}

	public function save(Default_Model_Marketing $model)
	{
		$data = array(
			'type'	 	 		 => $model->getType(),
			'code'	 		 	 => $model->getCode(),
			'status'			 => $model->getStatus()?'1':'0',
		);
		if(null === ($id = $model->getId())) {
			$data['created']	 = new Zend_Db_Expr('NOW()');
			$data['modified']	 = new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);
		} else {
			$data['modified']	 = new Zend_Db_Expr('NOW()');
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
		return $id;
	}

	public function find($id, Default_Model_Marketing $model)
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
			$model = new Default_Model_Marketing();
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