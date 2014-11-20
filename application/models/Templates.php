<?php
class Default_Model_DbTable_Templates extends Zend_Db_Table_Abstract
{
	protected $_primary = 'const';
	protected $_name    = 'j_templates';
}

class Default_Model_Templates
{
	protected $_const;
	protected $_subject;
	protected $_value;
	protected $_subjectro;
	protected $_valuero;

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

	public function setConst($const)
	{
		$this->_const = (string) $const;
		return $this;
	}

	public function getConst()
	{
		return $this->_const;
	}

	public function setSubject($subject)
	{
		$this->_subject = (!empty($subject))?(string)$subject:null;
		return $this;
	}

	public function getSubject()
	{
		return $this->_subject;
	}

	public function setValue($value)
	{
		$this->_value = (!empty($value))?(string)$value:null;
		return $this;
	}

	public function getValue()
	{
		return $this->_value;
	}

	public function setSubjectro($subjectro)
	{
		$this->_subjectro = (!empty($subjectro))?(string)$subjectro:null;
		return $this;
	}

	public function getSubjectro()
	{
		return $this->_subjectro;
	}

	public function setValuero($valuero)
	{
		$this->_valuero = (!empty($valuero))?(string)$valuero:null;
		return $this;
	}

	public function getValuero()
	{
		return $this->_valuero;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_TemplatesMapper());
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
		if(null === ($id = $this->getConst())) {
			throw new Exception("Invalid record selected!");
		}
		return $this->getMapper()->delete($id);
	}
}


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