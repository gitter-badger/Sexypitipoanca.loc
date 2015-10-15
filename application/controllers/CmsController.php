<?php
class CmsController extends Base_Controller_Action
{
	public function viewAction()
	{
		$page = $this->getRequest()->getParam('page');
		$model = new Default_Model_Cms();
		$select = $model->getMapper()->getDbTable()->select()
				->where('link = ?', $page);
		if(($result = $model->fetchAll($select))) {
			$this->view->page = $result[0];
		}
	}
}