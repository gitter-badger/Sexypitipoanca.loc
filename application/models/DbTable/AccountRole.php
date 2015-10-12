<?php
class Default_Model_DbTable_AccountRole extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_account_role';
	protected $_primary = 'id';
    protected $_dependentTables = array('Default_Model_DbTable_Accounts');
}