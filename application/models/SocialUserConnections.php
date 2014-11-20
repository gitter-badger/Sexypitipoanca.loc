<?php
class Default_Model_DbTable_SocialUserConnections extends Zend_Db_Table_Abstract
{
	protected $_name    = 'social_user_connections';
	protected $_primary = 'id';
}

class Default_Model_SocialUserConnections
{
	protected $_id;
	protected $_initiatorUserId;
	protected $_receiverUserId;
	protected $_isConfirmed;
	protected $_type;
	protected $_created;

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

	public function setId($int)
	{
		$this->_id = (int) $int;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}
	
	public function setInitiatorUserId($value)
    {
        $this->_initiatorUserId = (int) $value;
        return $this;
    }

    public function getInitiatorUserId()
    {
        return $this->_initiatorUserId;
    }
	
	public function setReceiverUserId($value)
    {
        $this->_receiverUserId = (int) $value;
        return $this;
    }

    public function getReceiverUserId()
    {
        return $this->_receiverUserId;
    }
	
	public function setIsConfirmed($value)
    {
        $this->_isConfirmed = (int) $value;
        return $this;
    }

    public function getIsConfirmed()
    {
        return $this->_isConfirmed;
    }
	
	public function setType($value)
    {
        $this->_type= (string) $value;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }    

	public function setCreated($date)
	{
		$this->_created = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
		return $this;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_SocialUserConnectionsMapper());
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

class Default_Model_SocialUserConnectionsMapper
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
			$this->setDbTable('Default_Model_DbTable_SocialUserConnections');
		}
		return $this->_dbTable;
	}
	
	public function find($id, Default_Model_SocialUserConnections $model)
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
			$model = new Default_Model_SocialUserConnections();
			$model->setOptions($row->toArray())
				->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_SocialUserConnections $model)
	{
		$data = array(
			'initiatorUserId'					=> $model->getInitiatorUserId(),
			'receiverUserId'					=> $model->getreceiverUserId(),
			'isConfirmed'						=> $model->getIsConfirmed(),
			'type'								=> $model->getType()
		);
		if(null === ($id = $model->getId())) {
			$data['created']					= new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);
		} else {
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