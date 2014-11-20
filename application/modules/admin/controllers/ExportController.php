<?php
class Admin_ExportController extends Zend_Controller_Action
{
	protected $_id;
	protected $_order;
	protected $_orderProducts;
	protected $_company;
	protected $_print;

	public function setId($value)
	{
		$this->_language = $value;
		return $this;
	}

	public function getId()
	{
		return $this->_language;
	}

	public function setOrder($value)
	{
		$this->_order = $value;
		return $this;
	}

	public function getOrder()
	{
		return $this->_order;
	}

	public function setOrderProducts($value)
	{
		$this->_orderProducts = $value;
		return $this;
	}

	public function getOrderProducts()
	{
		return $this->_orderProducts;
	}

	public function setCompany($value)
	{
		$this->_company = $value;
		return $this;
	}

	public function getCompany()
	{
		return $this->_company;
	}

	public function setPrint($value)
	{
		$this->_print = $value;
		return $this;
	}

	public function getPrint()
	{
		return $this->_print;
	}

	public function init()
	{
		/* Initialize action controller here */
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();
		
		$this->setId($this->getRequest()->getParam('orderId'));
		$this->setPrint($this->getRequest()->getParam('print'));

		$order = new Default_Model_Orders();
		if($order->find($this->getId())) {
			$this->setOrder($order);
		}

		$val = new Default_Model_OrderProducts();
		$select = $val->getMapper()->getDbTable()->select()
				->where('orderId = ?', $this->getId())
				;
		if($result = $val->fetchAll($select)) {
			$this->setOrderProducts($result);
		}

		$val = new Default_Model_Company();
		$select = $val->getMapper()->getDbTable()->select();
		if($result = $val->fetchAll($select)) {
			$this->setCompany($result);
    	}
	}

	public function indexAction()
	{

	}

	public function orderdetailsAction()
	{
		$this->view->order = $this->getOrder();
		$this->view->orderProducts = $this->getOrderProducts();
		$this->view->print = $this->getPrint();
	}

	public function facturaAction()
	{
		$this->view->order = $this->getOrder();
		$this->view->orderProducts = $this->getOrderProducts();
		$this->view->company = $this->getCompany();
		$this->view->print = $this->getPrint();
	}

	public function csvAction()
	{
		$model = new Default_Model_Products();
		$select = $model->getMapper()->getDbTable()->select()
				->where('roleId = ?', '1');
		if(($result = $model->fetchAll($select))) {
			$s = ';';
			$array = array();
			$newline = array("\r\n", "\n", "\r");
			$i=0; foreach($result as $value) {
				$array['sku'][$i] = $value->getSku();
				$array['name'][$i] = $value->getName();
				$array['regprice'][$i] = $value->getRegprice()?$value->getRegprice():'0';
				$array['sellprice'][$i] = $value->getSellprice();
				$array['warranty'][$i] = $value->getWarranty()?$value->getWarranty():'0';
				$array['weight'][$i] = $value->getWeight()?$value->getWeight():'0';
				$array['description'][$i] = str_replace($newline, ' ', str_replace(';', ',', str_replace('&nbsp;', ' ', strip_tags(stripslashes($value->getDescription())))));
				$array['unlimitedStock'][$i] = $value->getStockNelimitat()?'da':'nu';
				$array['stock'][$i] = $value->getStock()?$value->getStock():'0';
//				$array['image'][$i] = $value->getImage()?$value->getImage():'NULL';
			$i++; }
			$csv = "SKU".$s."Nume".$s."Pret normal".$s."Pret de vanzare".$s."Garantie".$s."Greutate".$s."Descriere".$s."Stoc Nelimitat".$s."Stoc"."\n"; //$s."Imagine".
			for($j=0; $j<$i; $j++) {
				$csv.= $array['sku'][$j].$s;
				$csv.= $array['name'][$j].$s;
				$csv.= $array['regprice'][$j].$s;
				$csv.= $array['sellprice'][$j].$s;
				$csv.= $array['warranty'][$j].$s;
				$csv.= $array['weight'][$j].$s;
				$csv.= $array['description'][$j].$s;
				$csv.= $array['unlimitedStock'][$j].$s;
				$csv.= $array['stock'][$j];
//				$csv.= $array['image'][$j];
				$csv.= "\n";
			}
			$this->view->csv = $csv;
		}
	}
}