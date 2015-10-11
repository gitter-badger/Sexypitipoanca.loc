<?php
class Default_Model_DbTable_AccountUsers extends Zend_Db_Table_Abstract
{
	protected $_name    = 'j_account_users';
	protected $_primary = 'id';
	protected $_referenceMap	= array(
		'Roles' => array(
			'columns'           => 'roleId',
			'refTableClass'     => 'Default_Model_DbTable_Role',
			'refColumns'        => 'id'
		),
	);
}