<?php
class Admin_DashboardController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

    public function indexAction()
    {
		$tsDashboard = new TS_AdminDashboard;
		$this->view->activeGalleries = $tsDashboard->_activeGalleriesPerCateg;
		$this->view->inactiveGalleries = $tsDashboard->_inactiveGalleriesPerCateg;
		$this->view->allGalleries = $tsDashboard->_allGalleries;
		$this->view->activeUsers = $tsDashboard->_activeUsersNr;
		$this->view->inactiveUsers = $tsDashboard->_inactiveUsersNr;
		$this->view->activeMaleUsersNr = $tsDashboard->_activeMaleUsersNr;
		$this->view->activeFemaleUsersNr = $tsDashboard->_activeFemaleUsersNr;
	}
}