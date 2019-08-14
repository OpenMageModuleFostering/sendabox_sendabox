<?php

class Sendabox_Sendabox_Model_Observer
{
    /**
     * Magento passes a Varien_Event_Observer object as
     * the first parameter of dispatched events.
     */
    /*public function logSpedito(Varien_Event_Observer $observer)
    {
        // Retrieve the shipment being updated from the event observer
        $shipment = $observer->getEvent()->getShipment();

        // Write a new line to var/log/product-updates.log
        $order = $shipment->getOrder();
        $id = $order->getId();
        Mage::log(
            "{$id} spedito",
            null, 
            'ordinispediti.log', true
        );
		$items = $order->getAllItems();
        $itemcount=count($items);
        $name=array();
        $unitPrice=array();
        $sku=array();
        $ids=array();
        $qty=array();
		$weight=array();
		$width=array();
		$length=array();
		$height=array();
		Mage::log(
            "{$itemcount} oggetti",
            null, 
            'ordinispediti.log', true
        );
		$i=0;
        foreach ($items as $itemId => $item)
		{
			$name[] = $item->getName();
			$unitPrice[]=$item->getPrice();
			$sku[]=$item->getSku();
			$ids[]=$item->getProductId();
			$qty[]=$item->getQtyToInvoice();
			$weight[]=$item->getWeight();
			$product = Mage::getModel('catalog/product')->load($ids[$i]);
			$width[] = $product->getSendaboxWidth();
			$length[] = $product->getSendaboxLength();
			$height[] = $product->getSendaboxHeight();
			Mage::log(
				"nome: {$name[$i]},prezzo: {$unitPrice[$i]}, sku: {$sku[$i]}, id: {$ids[$i]}, quantità: {$qty[$i]}, peso: {$weight[$i]}, larghezza: {$width[$i]}, lunghezza: {$length[$i]}, altezza: {$height[$i]}",
				null, 
				'ordinispediti.log', true
			);
			$i++;
		}
    }*/
	
	/**
     * Handles sales_order_place_after event
     */
    public function createSendaboxShipment(Varien_Event_Observer $observer)
    {
		$order = $observer->getOrder();
        /* @var $order Mage_Sales_Model_Order */

		if(!array_key_exists($order->getShippingAddress()->getCountryId(), Mage::helper('sendabox_sendabox')->getAllowedCountries()))
			return;
		
		$shippingMethod = $order->getShippingMethod();
		$__t = explode('_', $shippingMethod);	
		$is_sandabox_quote = ($__t[0] == 'sendabox') ;
		
		$selected_quote_id = preg_replace('#^([^_]*_){3}#', '', $shippingMethod);
        
		if($is_sandabox_quote) 
		{
			$selected_quote =  Mage::getModel('sendabox_sendabox/quote')->load($selected_quote_id);
		}
		if(!$is_sandabox_quote or !$selected_quote->getId()) {
		//try loading cheapeast quote
			try {
				$selected_quote = $this->loadCheapestQuote($order);
				/* @var $selected_quote Sendabox_Sendabox_Model_Quote */
			} 
			catch (Exception $e) 
			{
				$selected_quote = null;
			}
		}
		
		
		
		$sendabox_shipment = Mage::getModel('sendabox_sendabox/shipment');
		/* @var $sendabox_shipment Sendabox_Sendabox_Model_Shipment */
	
		if($is_sandabox_quote && Mage::helper('sendabox_sendabox')->isQuoteDynamic($selected_quote_id)) {
			//DYNAMIC: carrier quote selected by customer - must have at least 1 available quote
			$sendabox_shipment
				->setAdminSelectedQuoteId($selected_quote->getId())
				->setCustomerSelectedQuoteId($selected_quote->getId());
			$selected_quote = $sendabox_shipment->getSelectedQuote();
			$sendabox_shipment
						->setCustomerSelectedQuoteDescription($selected_quote->getDescription())
						->setCustomerCost($selected_quote->getTotalPrice());
		}
		elseif(!$is_sandabox_quote){
			$sendabox_shipment
				->setCustomerSelectedQuoteDescription($order->getShippingDescription());
			
			if($selected_quote instanceof Sendabox_Sendabox_Model_Quote) {
			//set cheapest as admin selected
			$sendabox_shipment->setAdminSelectedQuoteId($selected_quote->getId())
					 ->setCustomerCost($selected_quote->getTotalPrice());
					 }
		}
		else {
			//STATIC: flat rate / free shipping selected by customer
			$sendabox_shipment
				->setCustomerSelectedQuoteDescription('Free Shipping');
			
			if($selected_quote instanceof Sendabox_Sendabox_Model_Quote) {
			//set cheapest as admin selected
			$sendabox_shipment->setAdminSelectedQuoteId($selected_quote->getId())
					 ->setCustomerCost($selected_quote->getTotalPrice());
			}
		}
        
        $email = $order->getShippingAddress()->getEmail();
        if(!$email) {
            $email = $order->getCustomerEmail();
        }
		
        $sendabox_shipment
            ->setOrderId($order->getId() ? $order->getId() : null)
            ->setStatus(Sendabox_Sendabox_Model_System_Config_Source_Shipment_Status::PENDING)
            ->setDestinationName($order->getShippingAddress()->getFirstname())
            ->setDestinationSurname($order->getShippingAddress()->getLastname())
            ->setDestinationStreet(str_replace("\n", ', ', $order->getShippingAddress()->getStreetFull()))
            ->setDestinationPhone($order->getShippingAddress()->getTelephone())
            ->setDestinationEmail($email)
            ->setDestinationPostcode($order->getShippingAddress()->getPostcode())
            ->setDestinationCity($order->getShippingAddress()->getCity())
            ->save();
        
			$weight = 0;
			$comment = '';
			$value = 0;
		foreach ($order->getAllVisibleItems() as $item) {
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			if($product->isVirtual()) { continue; }
			
			/*Mage::helper('sendabox_sendabox')->applySendaboxParamsToItem($item);*/
			
			$qty = $item->getQty() ? $item->getQty() : $item->getQtyOrdered();
			$weight += $item->getWeight()*$qty;
			$comment .= " " .$item->getName() ."-";
			$value += $item->getRowTotalInclTax()*$qty;
		}
		
		$box = Mage::getModel('sendabox_sendabox/box');
			/* @var $box Sendabox_Sendabox_Model_Box */
			$box
			->setShipmentId($sendabox_shipment->getId())
			->setComment($comment)
			->setQty(1)
			->setValue($value)
			->setLength(10)
			->setWidth(10)
			->setHeight(10)
			->setMeasureUnit(Mage::helper('sendabox_sendabox')->getConfigData('units/measure'))
			->setWeight($weight)
			->setWeightUnit(Mage::helper('sendabox_sendabox')->getConfigData('units/weight'))
			->save();
    }
	
	public function addJavascriptBlock($observer)
    {
        //exit(__METHOD__);
		$controller = $observer->getAction();
        $layout = $controller->getLayout();
        $block = $layout->createBlock('core/text');
		$credits = $this->getCredits();
        $block->setText(
        "<script type='text/javascript'>
            function main_pulsestorm_hellojavascript()
            {
				var credits = document.getElementById('sendabox_options_general_credits');
				if(credits!=null)
					credits.value = ' {$credits} ';
            }
            main_pulsestorm_hellojavascript();
        </script>"
        );        
        $layout->getBlock('js')->append($block);
    }
	
	protected function getCredits(){
		$username = Mage::helper('sendabox_sendabox')->getConfigData('general/username');
		$password = Mage::helper('sendabox_sendabox')->getConfigData('general/password');
		$url='http://www.sendabox.it/_magento/get/credits?api_user=' .$username .'&api_secret=' .$password;
		$html = file_get_contents($url);
		if ($html) {
			   $chiamata = json_decode($html, true);
			   if(isset($chiamata['error']) && $chiamata['error']){
					   return 'Sendabox account data not valid';
			   } else { 
					   return $chiamata['credit'];
			   }
		} else {
				//exit('not html');
			    return 'Sendabox account data not valid';
		}
	}
	
	protected function loadCheapestQuote($order)
    {
		$salesQuoteId = $order->getQuoteId();
		$quotes = Mage::getModel('sendabox_sendabox/quote')->getCollection()
		    ->addFieldToFilter('magento_quote_id', $salesQuoteId)
		    ->getItems();
		if(!is_array($quotes)) { $quotes = array($quotes); }
		return Mage::helper('sendabox_sendabox')->getCheapestQuote($quotes);
    }
}