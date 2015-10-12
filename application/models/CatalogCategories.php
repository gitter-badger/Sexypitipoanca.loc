<?php
class Default_Model_CatalogCategories
{
	protected $_id;
	protected $_parent_id;
	protected $_position;
	protected $_name;
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
		$this->_parent_id = (!empty($id))?(int) $id:null;
		return $this;
	}

	public function getParent_id()
	{
		return $this->_parent_id;
	}

	public function setPosition($id)
	{
		$this->_position = (!empty($id))?(int) $id:null;
		return $this;
	}

	public function getPosition()
	{
		return $this->_position;
	}

	public function setName($name)
	{
		$this->_name = (string) strip_tags($name);
		return $this;
	}

	public function getName()
	{
		return $this->_name;
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
			$this->setMapper(new Default_Model_CatalogCategoriesMapper());
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