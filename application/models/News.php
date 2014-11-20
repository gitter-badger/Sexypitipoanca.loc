<?php
class Default_Model_DbTable_News extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_news';
	protected $_primary = 'id';
}

class Default_Model_News
{
	protected $_id;
	protected $_title;
	protected $_message;
	protected $_status;
	protected $_added;
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
			throw new Exception('Invalid '.$name.' property'.$method);
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if(('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid '.$name.' property');
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

	public function setMessage($message)
	{
		$this->_message = (string) $message;
		return $this;
	}

	public function getMessage()
	{
		return $this->_message;
	}

	public function setStatus($status)
	{
		$this->_status = (!empty($status))?(string) $status:"0";
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

	public function setUpdated($date)
	{
		$this->_updated = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
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
		if (null === $this->_mapper) {
			$this->setMapper(new Default_Model_NewsMapper());
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

	public function delete()
	{
		if(null === ($id = $this->getId())) {
			throw new Exception('Invalid record selected!');
		}
		return $this->getMapper()->delete($id);
	}
}

class Default_Model_NewsMapper
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
			$this->setDbTable('Default_Model_DbTable_News');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_News $model)
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
			$model = new Default_Model_News();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_News $model)
	{
		$data = array(
			'title'					=> $model->getTitle(),
			'message'				=> $model->getMessage(),
			'status'				=> $model->getStatus()?'1':'0',
		);
		if(null === ($id = $model->getId())) {
			$data['added']		= new Zend_Db_Expr('NOW()');
			$data['updated']	= new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);

		} else {
			$data['modified']	= new Zend_Db_Expr('NOW()');
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