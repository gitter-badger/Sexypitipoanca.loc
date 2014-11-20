<?php
class Default_Model_DbTable_CatalogProductTags extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_catalog_product_tags';
	protected $_primary = 'id';
}

class Default_Model_CatalogProductTags
{
	protected $_id;
	protected $_product_id;
	protected $_product;
	protected $_tag_id;

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
		if (('mapper' == $name) || !method_exists($this, $method)) {
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

	public function setProduct_id($id)
	{
		$model = new Default_Model_CatalogProducts();
		if($model->find($id)) {
			$this->setProduct($model);
			$this->_product_id = $model->getId();
		}
		return $this;
	}

	public function getProduct_id()
	{
		return $this->_product_id;
	}

	public function setProduct(Default_Model_CatalogProducts $model)
	{
		$this->_product = $model;
		return $this;
	}

	public function getProduct()
	{
		return $this->_product;
	}

	public function setTag_id($id)
	{
		$this->_tag_id = (int) $id;
		return $this;
	}

	public function getTag_id()
	{
		return $this->_tag_id;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_CatalogProductTagsMapper());
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

class Default_Model_CatalogProductTagsMapper
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
			$this->setDbTable('Default_Model_DbTable_CatalogProductTags');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_CatalogProductTags $model)
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
			$model = new Default_Model_CatalogProductTags();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_CatalogProductTags $model)
	{
		$data = array(			
			'product_id'			=> $model->getProduct_id(),
			'tag_id'				=> $model->getTag_id(),
		);
		if(null == ($id = $model->getId())) {
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