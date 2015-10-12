<?php
class Default_Model_Twitter
{
	protected $_id;
	protected $_userId;
	protected $_username;
	protected $_json;
	protected $_oauthToken;
	protected $_oauthTokenSecret;
	protected $_invalid;
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

	public function setUserId($value)
	{
		$this->_userId = (int) $value;
		return $this;
	}

	public function getUserId()
	{
		return $this->_userId;
	}


	public function setUsername($username)
	{
		$this->_username = (!empty($username))?(string) strip_tags($username):null;
		return $this;
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function setJson($value)
	{
		$this->_json = (!empty($value))?(string) json_encode($value):null;
		return $this;
	}

	public function getJson()
	{
		return $this->_json;
	}
	
	public function setOauthToken($value)
	{
		$this->_oauthToken = (!empty($value))?(string) strip_tags($value):null;
		return $this;
	}

	public function getOauthToken()
	{
		return $this->_oauthToken;
	}

	public function setOauthTokenSecret($value)
	{
		$this->_oauthTokenSecret = (!empty($value))?(string) strip_tags($value):null;
		return $this;
	}

	public function getOauthTokenSecret()
	{
		return $this->_oauthTokenSecret;
	}


	public function setInvalid($value)
	{
		$this->_invalid = (int) $value;
		return $this;
	}

	public function getInvalid()
	{
		return $this->_invalid;
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
		$this->_created = (!empty($value) && strtotime($value)>0)?strtotime($value):null;
		return $this;
	}

	public function getModified()
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
			$this->setMapper(new Default_Model_TwitterMapper());
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

	public function fetchAll($select=null)
	{
		return $this->getMapper()->fetchAll($select);
	}

	public function delete()
	{
		if(null === ($id = $this->getId())) {
			throw new Exception("Invalid record selected!");
		}
		return $this->getMapper()->delete($id);
	}
}