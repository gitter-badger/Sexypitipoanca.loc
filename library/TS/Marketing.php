<?php
class TS_Marketing
{
	public static function customCommercial($code)
	{
		$modelMarketing = new Default_Model_Marketing();
		$selectMarketing = $modelMarketing->getMapper()->getDbTable()->select()
			->where('status = ?', 1)
			->order(new Zend_Db_Expr('RAND()'));
		$resultMarketing = $modelMarketing->fetchAll($selectMarketing);
		if($resultMarketing)
		{
			if($code)
				echo $resultMarketing[0]->getCode();
			else
				return $resultMarketing[0];
		}
		return null;
	}
	
	public static function saveCommercial($code)
	{
		$modelMarketing = new Default_Model_Marketing();
		$modelMarketing->setType('commercial');
		$code = TS_Base::magicQuotes($code);
		$modelMarketing->setCode($code);
		$modelMarketing->setStatus(1);
		if($modelMarketing->save())
		{
			return true;
		}
		return false;
	}
	
	public static function fetchCommercials()
	{
		$modelMarketing = new Default_Model_Marketing();
		$select = $modelMarketing->getMapper()->getDbTable()->select()
			->where('type = ?', 'commercial')
			->order('created DESC');
		$result = $modelMarketing->fetchAll($select);
		if($result)
		{
			return $result;
		}
		return null;
	}
}