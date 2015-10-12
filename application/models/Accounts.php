<?php
class Default_Model_Accounts
{
	protected $_id;
	protected $_roleId;
	protected $_role;
	protected $_username;
	protected $_password;
	protected $_email;
	protected $_status;
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

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setRoleId($id)
	{
		$model = new Default_Model_AccountRole();
		$model->find($id);
		if(null !== $model->getId()) {
			$this->setRole($model);
			$this->_roleId = $model->getId();
		}
		return $this;
	}

	public function getRoleId()
	{
		return $this->_roleId;
	}

	public function setRole(Default_Model_AccountRole $value)
	{
		$this->_role = $value;
		return $this;
	}

	public function getRole()
	{
		return $this->_role;
	}

	public function setUsername($value)
	{
		$this->_username = (string) strip_tags($value);
		return $this;
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function setPassword($value)
	{
		$this->_password = (string) strip_tags($value);
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setFirstname($value)
	{
		$this->_firstname = (!empty($value))?(string) strip_tags($value):null;
		return $this;
	}

	public function getFirstname()
	{
		return $this->_firstname;
	}

	public function setLastname($value)
	{
		$this->_lastname = (!empty($value))?(string) strip_tags($value):null;
		return $this;
	}

	public function getLastname()
	{
		return $this->_lastname;
	}

	public function setEmail($email)
	{
		$this->_email = (!empty($value))?(string) strip_tags($value):null;
		return $this;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setStatus($value)
	{
		$this->_status = (!empty($value))?(string) $value:'0';
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

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if (null === $this->_mapper) {
			$this->setMapper(new Default_Model_AccountsMapper());
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