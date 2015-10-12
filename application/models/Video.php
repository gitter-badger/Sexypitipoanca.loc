<?php
class Default_Model_Video
{
	protected $_id;
	protected $_productId;
	protected $_url;
	protected $_embed;
	protected $_image;

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

	public function setId($value)
	{
		$this->_id = (int) $value;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
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

	public function setUrl($value)
	{
		$this->_url = (string) $value;
		return $this;
	}

	public function getUrl()
	{
		return $this->_url;
	}

	public function setEmbed($value)
	{
		$this->_embed = (string) $value;
		return $this;
	}

	public function getEmbed()
	{
		return $this->_embed;
	}
	
	public function setImage($value)
	{
		$this->_image = (!empty($value))?(string) strip_tags($value):null;
		return $this;
	}

	public function getImage()
	{
		return $this->_image;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_VideoMapper());
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