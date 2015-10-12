<?php
class Default_Model_AccountUsers
{
	protected $_id;
	protected $_roleId;
	protected $_role;
	protected $_email;
	protected $_username;
	protected $_password;
	protected $_avatar;
	protected $_birth_day;
	protected $_gender;
	protected $_activationcode;
	protected $_status;
	protected $_created;
	protected $_lastlogin;
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

	public function setEmail($string)
	{
		$this->_email = (string) strip_tags($string);
		return $this;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setUsername($string)
	{
		$this->_username = (string) strip_tags($string);
		return $this;
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function setPassword($string)
	{
		$this->_password = (string) strip_tags($string);
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setAvatar($string)
	{
		$this->_avatar = (string) strip_tags($string);
		return $this;
	}

	public function getAvatar()
	{
		return $this->_avatar;
	}

	public function setBirth_day($date)
	{
		$this->_birth_day = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
		return $this;
	}

	public function getBirth_day()
	{
		return $this->_birth_day;
	}

	public function setGender($string)
	{
		$this->_gender = (string)(!empty($string))?strip_tags($string):'0';
		
		return $this;
	}

	public function getGender()
	{
		return $this->_gender;
	}
	
	public function setActivationcode($activationcode)
	{
	   $this->_activationcode = (!empty($activationcode))?(string) $activationcode:null;
		return $this;
	}

	public function getActivationcode()
	{
		return $this->_activationcode;
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

	public function setCreated($date)
    {
        $this->_created = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
        return $this;
    }

    public function getCreated()
    {
        return $this->_created;
    }

	public function setLastlogin($date)
    {
        $this->_lastlogin = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
        return $this;
    }

    public function getLastlogin()
    {
        return $this->_lastlogin;
    }

	public function setModified($date)
    {
        $this->_modified = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
        return $this;
    }
	
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_AccountUsersMapper());
		}
		return $this->_mapper;
	}

	public function find($id)
	{
		return $this->getMapper()->find($id, $this);
	}

	public function fetchAll($select=null)
	{
		return $this->getMapper()->fetchAll($select);
	}

	public function fetchRow($select =null)
	{
		return $this->getMapper()->fetchRow($select,$this);
	}

	public function save()
	{
		return $this->getMapper()->save($this);
	}
	
	public function saveLastlogin()
    {
        $this->getMapper()->saveLastlogin($this);
    }

	public function delete()
	{
		if(null === ($id = $this->getId())) {
			throw new Exception('Invalid record selected!');
		}
		return $this->getMapper()->delete($id);
	}
}