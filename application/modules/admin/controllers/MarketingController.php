<?php
class Admin_MarketingController extends Zend_Controller_Action
{
	public function init()
	{
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
	}

	public function indexAction()
	{
		// BEGIN: Fetch commercials
		$result = TS_Marketing::fetchCommercials();
		if($result)
		{
			$this->view->commercial = $result;
		}
		// END: Fetch commercials
		
		// BEGIN: Save new commercial
		if($this->getRequest()->isPost()){
			$code = $this->getRequest()->getParam('code');
			if(TS_Marketing::saveCommercial($code))
			{
				$this->_redirect('/admin/marketing');
			}
		}
		// END: Save new commercial
	}

    /**
     * add, ads a new commercial
     */
    public function addAction()
    {

    }

    /**
     * edit, modify commercial info
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Marketing();
        if($model->find($id)) {
            $form = new Admin_Form_Marketing();
            $form->edit($model);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/marketing/banner.phtml'))));
            $this->view->form = $form;
            if($this->getRequest()->isPost()){
                if($form->isValid($this->getRequest()->getPost())){
                    $model->setOptions($form->getValues());
                    if($model->save()){
                        $this->_flashMessenger->addMessage('<div class="mess-true">Comment successfully moderated!</div>');
                        $this->_redirect('/admin/marketing/');
                    }else{
                        $this->_flashMessenger->addMessage('<div class="mess-false">Error moderating comment!</div>');
                    }
                }
            }
        }
    }

    /**
     * status, change status of the current commercial
     * ToDo: error handling
     */
    public function statusAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Marketing();
        if ($model->find($id)) {
            $nextStatus = $model->getStatus() ? 0 : 1;
            $model->setStatus($nextStatus);
            $model->save();
        }
        $this->_redirect('/admin/marketing');
    }

    /**
     * delete, deletes a banner
     * ToDo: error handling
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        $model = new Default_Model_Marketing();
        if ($model->find($id)) {
           if ($model->delete()) {
               $this->_flashMessenger->addMessage('<div class="mess-true">Success! Data deleted.</div>');
           } else {
               $this->_flashMessenger->addMessage('<div class="mess-false">Error! Data not deleted.</div>');
           }
        } else {
            $this->_flashMessenger->addMessage('<div class="mess-false">Error! Data not found.</div>');
        }
        $this->_redirect('/admin/marketing');
    }
	
	public function analyticsAction()
	{
		
	}
}