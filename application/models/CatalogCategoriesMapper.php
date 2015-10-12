<?php
class Default_Model_CatalogCategoriesMapper
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
			$this->setDbTable('Default_Model_DbTable_CatalogCategories');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_CatalogCategories $model)
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
		foreach ($resultSet as $row) {
			$model = new Default_Model_CatalogCategories();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function save(Default_Model_CatalogCategories $model)
	{
		$data = array(
			'parent_id'				=> $model->getParent_id(),
			'position'				=> $model->getPosition(),
			'name'					=> $model->getName(),
			'status'				=> $model->getStatus(),
		);
		if(null === ($id = $model->getId())) {
			$data['added']		 = new Zend_Db_Expr('NOW()');
			$id = $this->getDbTable()->insert($data);
		} else {
//			$data['updated']	 = new Zend_Db_Expr('NOW()');
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