<?php

class Sendabox_Sendabox_Adminhtml_ShipmentController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
	$this->loadLayout()
		->_setActiveMenu('sendabox/shipment')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Shipment Manager'), Mage::helper('adminhtml')->__('Shipment Manager'))
		->renderLayout();
    }
	
	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$shipment = Mage::getModel('sendabox_sendabox/shipment')->load($id);
		/* @var $shipment Sendabox_Sendabox_Model_Shipment */

		if ($shipment->getId()) {
			$notices = array();

			if (!count($shipment->getBoxes())) {
				$notices[] = 'Quotes cannot be refreshed until at least one box is added to the shipment.';
			}
			if (!count($shipment->getQuotes())){
				$notices[] = 'No quotes available for the boxes of the shipment';
			}
			if($this->checkCredits() == 0){
				$this->_getSession()->addError('No credits remaining or error in the settings configuration of the extension');
				//$notices[] = 'No credits remaining or error in the settings configuration of the extension';
			}
			if($this->checkCredits() != 0 && $this->checkCredits()<= 10){
				$notices[] = 'Check your credits, only {$this->checkCredits()} remaining';
			}
			foreach ($notices as $notice) {
				$this->_getSession()->addNotice($this->__($notice));
			}
			
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
				if (!empty($data)) {
			$shipment->addData($data);
			}
			
			Mage::register('sendabox_shipment_data', $shipment);

			$this->loadLayout()
				->_setActiveMenu('sendabox/shipment');

			/*$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);*/

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Shipment does not exist.'));
			$this->_redirect('*/*/');
		}
    }
	
	public function saveAction() {
	if ($data = $this->getRequest()->getPost()) {
	    $shipment = Mage::getModel('sendabox_sendabox/shipment');

	    try {
			$shipment->setId($this->getRequest()->getParam('id'))
				->addData($data)->save();

			if ($data['boxes_deleted']) {
				$box_ids = explode(',', $data['boxes_deleted']);
				foreach ($box_ids as $box_id) {
					$box = Mage::getModel('sendabox_sendabox/box');
					/* @var $box Sendabox_Sendabox_Model_Box */
					$box->load($box_id);
					if ($box->getId()) {
						$box->delete();
					}
				}
			}

			if (isset($data['box']) && is_array($data['box'])) {
				foreach ($data['box'] as $box_data) {

					$box = Mage::getModel('sendabox_sendabox/box');
					/* @var $box Sendabox_Sendabox_Model_Box */
					if (isset($box_data['id'])) {
						$box->load($box_data['id']);
					}

					$box->setShipmentId($shipment->getId())
						->addData($box_data)
						->save();
				}
			}

			
			$shipment = Mage::getModel('sendabox_sendabox/shipment')->load($shipment->getId());
			$shipment->clearQuotes()->save();
			if ($shipment->getBoxes()) {
				$shipment->getNewQuotes();
			}

			$this->_getSession()->addSuccess($this->__('The shipment data has been saved.'));

			
			$this->_redirect('*/*/edit', array('id' => $shipment->getId()));
	    } catch (Exception $ex) {
			$this->_getSession()->addError($ex->getMessage())->setFormData($data);
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	    }
	    return;
	}

	// nothing to save
	$this->_redirect('*/*/');
    }
	
	public function bookAction() {
		$shipment_id = $this->getRequest()->getParam('shipment');
		$shipment = Mage::getModel('sendabox_sendabox/shipment')
			->load($shipment_id);
		/* @var $shipment Sendabox_Sendabox_Model_Shipment */

		$quote_id = $this->getRequest()->getParam('quote');
		$quote = Mage::getModel('sendabox_sendabox/quote')
			->load($quote_id);
		/* @var $quote Sendabox_Sendabox_Model_Quote */

		$error = null;

		if (!$shipment->getId()) {
			$error = 'Shipment does not exist.';
		} else {
			if (!$quote->getId()) {
			$error = 'Selected quote does not exist.';
			} else {
				$username = Mage::helper('sendabox_sendabox')->getConfigData('general/username');
				$password = Mage::helper('sendabox_sendabox')->getConfigData('general/password');
				if (!$error && $username !='test' && $password !='test') {
					// try to make booking
					try {
						$booking_result = $this->_makeBooking($shipment, $quote);
					} catch (Exception $ex) {
						$error = $ex->getMessage();
					}
				}
			}
		}

		if (!$error) {
			
			$shipment
				->setAdminSelectedQuoteId($quote_id)
				->setStatus(Sendabox_Sendabox_Model_System_Config_Source_Shipment_Status::BOOKED)
				->setCustomerCost($quote->getTotalPrice())
				->save();

			$magento_shipment = Mage::getModel('sales/convert_order')
				->toShipment($shipment->getOrder());
			/* @var $magento_shipment Mage_Sales_Model_Order_Shipment */

			$totalQty = 0;
			foreach ($shipment->getOrder()->getAllItems() as $item) {
				if ($item->getQtyToShip() && !$item->getIsVirtual()) {
					$magento_shipment_item = Mage::getModel('sales/convert_order')
						->itemToShipmentItem($item);

					$qty = $item->getQtyToShip();

					$magento_shipment_item->setQty($qty);
					$magento_shipment->addItem($magento_shipment_item);

					$totalQty += $qty;
				}
			}

			$magento_shipment->setTotalQty($totalQty);

			try {
			$magento_shipment->getOrder()->setIsInProcess(true)->setCustomerNoteNotify(true);
			Mage::getModel('core/resource_transaction')
				->addObject($shipment)
				->addObject($magento_shipment)
				->addObject($magento_shipment->getOrder())
				->save();
			$magento_shipment->sendEmail();
			} catch (Mage_Core_Exception $e) {
				$error = $e->getMessage();
			}

			$this->_getSession()->addSuccess($this->__('Shipment booked.'));
		}

	if ($error) {
	    $this->_getSession()
		    ->addError($this->__($error))
	    /* ->setFormData($data) */;
	}
	$this->_redirect('*/*/edit', array('id' => $shipment_id));
    }
	
	protected function _makeBooking($shipment, $quote){
		$username = Mage::helper('sendabox_sendabox')->getConfigData('general/username');
		$password = Mage::helper('sendabox_sendabox')->getConfigData('general/password');
		$nameFrom = Mage::getStoreConfig('sendabox_options/origin/contact_name');
		$surnameFrom = Mage::getStoreConfig('sendabox_options/origin/contact_surname');
		$careFrom = Mage::getStoreConfig('sendabox_options/origin/c_o');
		$addressFrom = Mage::getStoreConfig('sendabox_options/origin/street');
		$streetnumberFrom = Mage::getStoreConfig('sendabox_options/origin/number');
		$cityFrom = Mage::getStoreConfig('sendabox_options/origin/city_id');
		$zipFrom = Mage::getStoreConfig('sendabox_options/origin/postcode');
		$notesFrom = Mage::getStoreConfig('sendabox_options/origin/notes');
		$contactphoneFrom = Mage::getStoreConfig('sendabox_options/origin/phone');
		$emailconfirmFrom = Mage::getStoreConfig('sendabox_options/origin/email');
		foreach($shipment->getBoxes() as $box){
			$weight = ceil($box->getWeight());
			$dimensions = '' .ceil($box->getWidth()) .',' .ceil($box->getHeight()) .',' .ceil($box->getLength());
			$url = 'http://www.sendabox.it/_magento/set/order?api_user=' .$username .'&api_secret=' .$password .'&weight=' .$weight .'&dimensions=' .$dimensions .'&nameFrom=' .$nameFrom .'&surnameFrom=' .$surnameFrom;
			if($careFrom) $url .= '&careFrom=' .$careFrom;
			$url .= '&addressFrom=' .$addressFrom .'&streetnumberFrom=' .$streetnumberFrom .'&cityFrom=' .$cityFrom .'&zipFrom=' .$zipFrom;
			if($notesFrom) $url .= '&notesFrom=' .$notesFrom;
			$url .= '&contactphoneFrom=' .$contactphoneFrom .'&emailconfirmFrom=' .$emailconfirmFrom .'&nameTo=' .$shipment->getDestinationName() .'&surnameTo=' .$shipment->getDestinationSurname();
			if($shipment->getDestinationAt()) $url .= '&careTo=' .$shipment->getDestinationAt();
			$url .= '&addressTo=' .$shipment->getDestinationStreet() .'&streetnumberTo=' .$shipment->getDestinationNumber() .'&cityTo=' .$shipment->getDestinationId() .'&zipTo=' .$shipment->getDestinationPostcode();
			if($shipment->getDestinationNote()) $url .= '&notesTo=' .$shipment->getDestinationNote();
			$url .= '&idprice=' .$quote->getIdPrice();
			$url = str_replace(" ", "%20", $url);
			$html = file_get_contents($url);
			if ($html) {
				   $chiamata = json_decode($html, true);
				   if(!is_array($chiamata)) { $chiamata = array($chiamata); }
				   if(isset($chiamata['error']) && $chiamata['error']){
						   //echo $chiamata['msg'];
						   throw new Exception($chiamata['msg']);
				   } 
				   else { 
						foreach($chiamata as $shipped_box){ 
							$shipment_number = $shipped_box['id'];
						}
				   }
			} else {
				   //echo 'ERRORE CHIAMATA';
				   throw new Exception('Connection with the server failed');
			}
		}
	}
	
	protected function checkCredits(){
		$username = Mage::helper('sendabox_sendabox')->getConfigData('general/username');
		$password = Mage::helper('sendabox_sendabox')->getConfigData('general/password');
		$url='http://www.sendabox.it/_magento/get/credits?api_user=' .$username .'&api_secret=' .$password;
		$html = file_get_contents($url);
		if ($html) {
			   $chiamata = json_decode($html, true);
			   if(isset($chiamata['error']) && $chiamata['error']){
					   return 0;
			   } else { 
					   return $chiamata['credit'];
			   }
		} else {
				//exit('not html');
			    return 0;
		}
	}
}
