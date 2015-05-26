<?php
class Admin_MarketingController extends Zend_Controller_Action
{

    /**
     * @var Default_Model_Marketing $model
     */
    protected $model;

	public function init()
	{
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();

        $this->model = new Default_Model_Marketing();
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
        if($this->model->find($id)) {
            $form = new Admin_Form_Marketing();
            $form->edit($this->model);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/marketing/banner.phtml'))));
            $this->view->form = $form;
            if($this->getRequest()->isPost()){
                if($form->isValid($this->getRequest()->getPost())){
                    $this->model->setOptions($form->getValues());
                    if($this->model->save()){
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
        if ($this->model->find($id)) {
            $nextStatus = $this->model->getStatus() ? 0 : 1;
            $this->model->setStatus($nextStatus);
            $this->model->save();
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
        if ($this->model->find($id)) {
           if ($this->model->delete()) {
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

    /**
     * Send an email to a non user
     * @throws Zend_Form_Exception
     * @throws Zend_Mail_Exception
     */
    public function sendEmailAction()
    {
        // BEGIN: Send form
        $form = new Admin_Form_Message();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/message.phtml'))));
        $this->view->form = $form;

        // Send mail
        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){
                // BEGIN: Send email
                $mail = new Zend_Mail();
                $mail->setFrom($form->getValue('fromEmail'), $form->getValue('fromName'));
                $mail->setSubject($form->getValue('subject'));
                $mail->setBodyHtml($form->getValue('message'));
                $mail->addTo($form->getValue('to'));
                if($mail->send())
                {
                    $this->_flashMessenger->addMessage('<div class="mess-true">Your message was sent!</div>');
                }
                else{
                    $this->_flashMessenger->addMessage('<div class="mess-false">Your message was not sent!</div>');
                }
                $this->_redirect('/admin/user');
                // END: Send email
            }
        }
    }
}