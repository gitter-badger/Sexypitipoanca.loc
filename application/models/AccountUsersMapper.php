<?php
class Default_Model_AccountUsersMapper
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
			$this->setDbTable('Default_Model_DbTable_AccountUsers');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_AccountUsers $model)
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
			$model = new Default_Model_AccountUsers();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_AccountUsers $model)
	{
		$data = array(
			'roleId'				=> $model->getRoleId(),
			'email'					=> $model->getEmail(),
			'username'				=> $model->getUsername(),
			'password'				=> $model->getPassword(),
			'avatar'				=> $model->getAvatar(),
			'birth_day'				=> date('Y-m-d', $model->getBirth_day()),
			'gender'				=> $model->getGender(),
			'status'				=> $model->getStatus(),
			'activationcode'		=> $model->getActivationcode(),
		);
		if(null === ($id = $model->getId())) {
			$data['created']	 = new Zend_Db_Expr('NOW()');
            $id = $this->getDbTable()->insert($data);
		} else {
			$data['modified']	 = new Zend_Db_Expr('NOW()');
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
		return $id;
	}
	
	public function saveLastlogin(Default_Model_AccountUsers $user)
    {
        $data = array();

        if(null === ($id = $user->getId())) {
        	throw new Exception("Invalid account selected!");
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