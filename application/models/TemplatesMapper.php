<?php
class Default_Model_TemplatesMapper
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
        if (null === $this->_dbTable) {
            $this->setDbTable('Default_Model_DbTable_Templates');
        }
        return $this->_dbTable;
    }
    
    public function find($id, Default_Model_Templates $templates)
    {
        $result = $this->getDbTable()->find($id);
        if(0 == count($result)) {
            return;
        }
        $row = $result->current();
        $templates->setOptions($row->toArray());
		return $templates;
    }

    public function fetchAll($select)
    {
        $resultSet = $this->getDbTable()->fetchAll($select);
        
        $entries = array();
        foreach($resultSet as $row) {
            $val = new Default_Model_Templates();
            $val->setOptions($row->toArray())
            		  ->setMapper($this);
            $entries[] = $val;
        }
        return $entries;
    }

    public function save(Default_Model_Templates $val)
    {
        $data = array(
            'subject'				=> $val->getSubject(),
            'value'					=> $val->getValue(),
            'subjectro'				=> $val->getSubjectro(),
            'valuero'				=> $val->getValuero(),
        );
		if(null === ($id = $val->getConst())) {
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('const = ?' => $id));
		}
		return $id;
    }

    public function delete($id)
    {
    	$where = $this->getDbTable()->getAdapter()->quoteInto('const = ?', $id);
        return $this->getDbTable()->delete($where);
    }
}