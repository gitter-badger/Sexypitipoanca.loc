<?php
class Default_Model_DbTable_StatisticsVisits extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_statistics_visits';
	protected $_primary = 'id';
}

class Default_Model_StatisticsVisits
{
	protected $_id;
	protected $_statisticsId;
	protected $_productId;
	protected $_galleryType;
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

	public function setStatisticsId($value)
	{
		$this->_statisticsId = (int) $value;
		return $this;
	}

	public function getStatisticsId()
	{
		return $this->_statisticsId;
	}

	public function setProductId($value)
	{
		$this->_productId = (int) $value;
		return $this;
	}

	public function getProductId()
	{
		return $this->_productId;
	}

	public function setGalleryType($value)
	{
		$this->_galleryType = (string) $value;
		return $this;
	}

	public function getGalleryType()
	{
		return $this->_galleryType;
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

	public function setModified($date)
	{
		$this->_modified = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
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
		if (null === $this->_mapper) {
			$this->setMapper(new Default_Model_StatisticsVisitsMapper());
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

	public function notduplicate($statisticsId, $productId)
	{
		return $this->getMapper()->notduplicate($statisticsId, $productId, $this);
	}
}

class Default_Model_StatisticsVisitsMapper
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
			$this->setDbTable('Default_Model_DbTable_StatisticsVisits');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_StatisticsVisits $model)
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
			$model = new Default_Model_StatisticsVisits();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_StatisticsVisits $model)
	{
		$data = array(
			'statisticsId'			=> $model->getStatisticsId(),
			'productId'			=> $model->getProductId(),
		);
		if(null === ($id = $model->getId())){
			$data['created'] = new Zend_Db_Expr('NOW()');
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

	public function notduplicate($statisticsId, $productId, Default_Model_StatisticsVisits $model)
	{
		$model = new Default_Model_StatisticsVisits();
		$select = $model->getMapper()->getDbTable()->select()
				->where('statisticsId = ?', $statisticsId)
				->where('productId = ?', $productId)
				->where('YEAR(created) = ?', date('Y'))
				->where('MONTH(created) = ?', date('m'))
				->where('DAY(created) = ?', date('d'));
		$result = $model->fetchAll($select);
		if(null == $result){
			return true;
		}else{
			return false;
		}
	}
}