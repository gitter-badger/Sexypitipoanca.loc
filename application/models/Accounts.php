<?php
class Default_Model_DbTable_Accounts extends Zend_Db_Table_Abstract
{
	protected $_name			= 'j_accounts';
	protected $_primary			= 'id';
	protected $_referenceMap	= array(
		'Roles' => array(
			'columns'           => 'roleId',
			'refTableClass'     => 'Default_Model_DbTable_Role',
			'refColumns'        => 'id'
		),
	);
}

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

class Default_Model_AccountsMapper
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
            $this->setDbTable('Default_Model_DbTable_Accounts');
        }
        return $this->_dbTable;
    }
    
    public function find($id, Default_Model_Accounts $adminUser)
    {
        $result = $this->getDbTable()->find($id);
        if(0 == count($result)) {
            return;
        }
        $row = $result->current();
        $adminUser -> setOptions($row->toArray());
		return $adminUser;
    }

    public function fetchAll($select)
    {
        $resultSet = $this->getDbTable()->fetchAll($select);
        
        $entries = array();
        foreach($resultSet as $row) {
            $adminUser = new Default_Model_Accounts();
            $adminUser->setOptions($row->toArray())
                  	->setMapper($this);
            $entries[] = $adminUser;
        }
        return $entries;
    }
    
    public function save(Default_Model_Accounts $value)
    {
        $data = array(
			'roleId'			=> $value->getRoleId(),
			'username'			=> $value->getUsername(),
			'password'			=> $value->getPassword(),
			'salutation'		=> $value->getSalutation(),
			'firstname'			=> $value->getFirstname(),
			'lastname'			=> $value->getLastname(),
            'cnp'				=> $value->getCnp(),
            'seria'				=> $value->getSeria(),
            'address'			=> $value->getAddress(),
			'county'			=> $value->getCounty(),
            'city'				=> $value->getCity(),
            'zip'				=> $value->getZip(),
            'phone'				=> $value->getPhone(),
            'phone2'			=> $value->getPhone2(),
            'fax'				=> $value->getFax(),
			'email'				=> $value->getEmail(),
            'addressS'			=> $value->getAddressS(),
			'countyS'			=> $value->getCountyS(),
            'cityS'				=> $value->getCityS(),
            'zipS'				=> $value->getZipS(),
            'phoneS'			=> $value->getPhoneS(),
			'institution'		=> $value->getInstitution(),
			'fiscalcode'		=> $value->getFiscalcode(),
			'traderegister'		=> $value->getTraderegister(),
			'bank'				=> $value->getBank(),
			'ibancode'			=> $value->getIbancode(),
			'function'			=> $value->getFunction(),
			'department'		=> $value->getDepartment(),
            'activationcode'	=> $value->getActivationcode(),
            'person'			=> $value->getPerson(),
			'status'			=> $value->getStatus(),
        );
        
        if (null === ($id = $value->getId())) {
        	$data['created']	 = new Zend_Db_Expr('NOW()');
            $id = $this->getDbTable()->insert($data);
            
        } else {
        	$data['modified']	 = new Zend_Db_Expr('NOW()');
            $this->getDbTable()->update($data, array('id = ?' => $id));
            
        }
        return $id;
    }

	public function saveLastlogin(Default_Model_AdminUser $adminUser)
    {
        $data = array();

        if(null === ($id = $adminUser->getId())) {
        	throw new Exception("Invalid admin account selected!");
        } else {
			$data['lastlogin']	 = new Zend_Db_Expr('NOW()');
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