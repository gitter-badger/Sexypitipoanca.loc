<?php
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