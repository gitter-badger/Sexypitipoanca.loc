<?php
class TS_AdminDashboard
{
	public $_activeGalleriesPerCateg;
	public $_inactiveGalleriesPerCateg;
	public $_allGalleries;
	public $_activeUsersNr;
	public $_inactiveUsersNr;
	public $_activeMaleUsersNr;
	public $_activeFemaleUsersNr;

	// CONSTRUCTS
	public function  __construct()
	{
		$this->galleriesPerCateg();
		$this->_allGalleries = array_sum($this->_activeGalleriesPerCateg)+array_sum($this->_inactiveGalleriesPerCateg);
		$this->maleAndFemaleUsers();
		$this->usersDetails();
	}

	// OTHER FUNCTIONS
	private function maleAndFemaleUsers()
	{
		/////////////////////////////////////////////////////////////////
		$model = new Default_Model_AccountUsers();
		$select = $model->getMapper()->getDbTable()->select()
		    ->from(array('j_account_users'), array('id'=>'COUNT(id)'))
		    ->where('gender = ?', '0')
		    ->where('status = ?', '1');
		$result = $model->fetchAll($select);
		if($result)
		{
			$this->_activeMaleUsersNr = $result[0]->getId();
		}
		/////////////////////////////////////////////////////////////////
		$select = $model->getMapper()->getDbTable()->select()
		    ->from(array('j_account_users'), array('id'=>'COUNT(id)'))
		    ->where('gender = ?', '1')
		    ->where('status = ?', '1');
		$result = $model->fetchAll($select);
		if($result)
		{
			$this->_activeFemaleUsersNr = $result[0]->getId();
		}
		/////////////////////////////////////////////////////////////////
	}
	
	private function galleriesPerCateg(){
		
		$model = new Default_Model_CatalogCategories(); // fetch categories
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_catalog_categories'), array('id', 'name', 'status'))
				->where('status = ?', '1')
				->order('name ASC');
		$result = $model->fetchAll($select);

		if (NULL != $result)
		{
			foreach($result as $value) // iterate categories
			{
				$model = new Default_Model_CatalogProducts(); // fetch inactive galleries count
				$select = $model->getMapper()->getDbTable()->select()
						->from(array('j_catalog_products'), array('id'=>'COUNT(id)', 'category_id', 'status'))
						->where('category_id = ?', $value->getId())
						->where('status = ?', '0');
				$result2 = $model->fetchAll($select);
				unset ($model);
				unset ($select);
				if(NULL != $result2)
				{
					$this->_inactiveGalleriesPerCateg[$value->getName()] = $result2[0]->getId();
				}
				else
				{
					$this->_inactiveGalleriesPerCateg[$value->getName()] = 0;
				}

				//
				
				$model = new Default_Model_CatalogProducts(); // fetch active galleries count
				$select = $model->getMapper()->getDbTable()->select()
						->from(array('j_catalog_products'), array('id'=>'COUNT(id)', 'category_id', 'status'))
						->where('category_id = ?', $value->getId())
						->where('status = ?', '1');
				$result2 = $model->fetchAll($select);
				unset ($model);
				unset ($select);
				if(NULL != $result2)
				{
					$this->_activeGalleriesPerCateg[$value->getName()] = $result2[0]->getId();
				}
				else
				{
					$this->_activeGalleriesPerCateg[$value->getName()] = 0;
				}
			}
		}
		unset($model);
		unset($select);
		unset($result);
		unset($result2);
	}

	private function usersDetails()
	{
		$model = new Default_Model_AccountUsers(); // Count active users
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_account_users'), array('id'=>'COUNT(id)', 'status'))
				->where('status = ?', '1');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			$this->_activeUsersNr = $result[0]->getId();
		}
		else
		{
			$this->_activeUsersNr = 0;
		}

		//
		
		$model = new Default_Model_AccountUsers(); // Count inactive users
		$select = $model->getMapper()->getDbTable()->select()
				->from(array('j_account_users'), array('id'=>'COUNT(id)', 'status'))
				->where('status = ?', '0');
		$result = $model->fetchAll($select);
		if(NULL != $result)
		{
			$this->_inactiveUsersNr = $result[0]->getId();
		}
		else
		{
			$this->_inactiveUsersNr = 0;
		}
		
		unset($select);
		unset($result);
		unset($model);
	}
}