<?php

class Sendabox_Sendabox_Model_Shipping_Carrier_Sendabox extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'sendabox_sendabox';
	protected $_title;
	protected $request_new;
	protected $request_old;
	protected $_pricing_method;
	protected $_from;
	
	public function __construct()
    {
        parent::__construct();
        $this->_pricing_method = Mage::getStoreConfig( 'sendabox_options/pricing/method');
		$this->_from  = Mage::getStoreConfig('sendabox_options/origin/city_id');
		$this->_title = 'Sendabox';
    }
	
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {

		$to_name = $request->getDestCity();
		$region = $request->getDestRegionCode();
		
        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */
		
		//check origin/destination country
        if (!$this->_canShip($request)) { 
			return;
		}
		
		$quote=Mage::getModel('sendabox_sendabox/quote');
		
		$salesQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
		if (!$salesQuoteId && Mage::app()->getStore()->isAdmin()) {
			$salesQuoteId = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getId();
		}
		if (!$salesQuoteId && $this->getIsProductPage()) {
			$salesQuoteId = 100000000 + mt_rand(0, 100000);
		}

		$total_weight = 0;
        foreach ($request->getAllItems() as $item) {
	    /* @var $item Mage_Sales_Model_Quote_Item */
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) { continue; }
	    
			
				$qty = $item->getQty() ? $item->getQty() : $item->getQtyOrdered();
				$value = $item->getWeight() * $qty;
			
			$total_weight += $value;
		}
		$total_weight = ceil($total_weight);
		$lastRequest = Mage::getSingleton('checkout/session')->getSendaboxRequestString();
		$to_id = $this->getCityId($to_name, $region);
		if($to_id==0) return;
		$new_request = $this->_from .'_' .$to_id .'_' .$total_weight .'_10,10,10_' .$salesQuoteId;
		if ($lastRequest !== $new_request){
			$quotes_to_clear = Mage::getModel('sendabox_sendabox/quote')->getCollection()
		    ->addFieldToFilter('magento_quote_id', $salesQuoteId);
			foreach ($quotes_to_clear as $quote) {
				/* @var $quote Sendabox_Sendabox_Model_Quote */
				$quote->delete();
			}
			$this->getQuotes($this->_from,$to_id,$total_weight,'10,10,10',$salesQuoteId,'STANDARD');
			$this->getQuotes($this->_from,$to_id,$total_weight,'10,10,10',$salesQuoteId,11);
			$this->getQuotes($this->_from,$to_id,$total_weight,'10,10,10',$salesQuoteId,13);
		}
		Mage::getSingleton('checkout/session')->setSendaboxRequestString($this->_from .'_' .$to_id .'_' .$total_weight .'_10,10,10_' .$salesQuoteId);
		//check if eligible for free shipping
		if ($this->isFreeShipping($request)) {
            $result->append($this->_getFreeRateMethod());
	    return $result;
        }
        if($this->_pricing_method == Sendabox_Sendabox_Model_System_Config_Source_Pricing::FLAT_RATE){
			$result->append($this->_getFlatRateMethod());
			return $result;
		}
		
		$quotes = Mage::getModel('sendabox_sendabox/quote')->getCollection()
		    ->addFieldToFilter('magento_quote_id', $salesQuoteId)
		    ->getItems();
		if(!is_array($quotes)) { $quotes = array($quotes); }
		foreach($quotes as $quote){
			$method_title = 'sendabox_' . $quote->getId();
			$result->append($this->_getRateFromQuote($quote,$method_title));
		}
        return $result;
    }

    
	/**
     * Returns true if request is elegible for free shipping, false otherwise
     * 
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return boolean
     */
    public function isFreeShipping($request)
    {
		//check pricing method first
		if($this->_pricing_method == Sendabox_Sendabox_Model_System_Config_Source_Pricing::FREE) {
			return true;
		}
	
		//check if all items have free shipping or free shipping over amount enabled and valid for this request
		$allItemsFree = true; $total = 0;
			foreach ($request->getAllItems() as $item) {
			/* @var $item Mage_Sales_Model_Quote_Item */
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) { continue; }
				if ($item->getFreeShipping()) { continue; }
			
			$value = $item->getValue();
			if (!$value) { $value = $item->getRowTotalInclTax(); }
			if (!$value) { $value = $item->getRowTotal(); }	    
			if (!$value) {
				$qty = $item->getQty() ? $item->getQty() : $item->getQtyOrdered();
				$value = $item->getPrice() * $qty;
			}
			$total += $value;
			//not all items with free shipping if here
			$allItemsFree = false;
			}
		
		if($allItemsFree ||
			($this->getConfigData('free_shipping_enable') && $total >= $this->getConfigData('free_shipping_subtotal'))) {
			 return true;
		}
		
		return false;
    }
	
	/**
     * Creates the flat rate method, with the price set in the config. An
     * optional parameter allows the price to be overridden.
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getFreeRateMethod()
    {   
        $title = 'Free Shipping';
        $method = Mage::getModel('shipping/rate_result_method')
            ->setCarrier($this->_code)
            ->setCarrierTitle($this->_title)
            ->setMethodTitle($title)
            ->setMethod('sendabox_' .Sendabox_Sendabox_Model_Carrier::FREE)
            ->setPrice(0.00)
            ->setCost(0.00);
            
        return $method;
    }
	
	/**
     * Creates the flat rate method, with the price set in the config. An
     * optional parameter allows the price to be overridden.
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getFlatRateMethod()
    {   
        $title = 'Flat Shipping';
		$price = Mage::helper('sendabox_sendabox')->getConfigData('pricing/shipping_fee');
        $method = Mage::getModel('shipping/rate_result_method')
            ->setCarrier($this->_code)
            ->setCarrierTitle($this->_title)
            ->setMethodTitle($title)
            ->setMethod('sendabox_' .Sendabox_Sendabox_Model_Carrier::FLAT_RATE)
            ->setPrice($price)
            ->setCost(0.00);
            
        return $method;
    }
	
	protected function getQuotes($from=0, $to= 0, $weight, $dimensions='10,10,10', $salesQuoteId=null,$special='STANDARD'){
	
		/*if(!$from || !$to || !$weight)
			return false;*/
			
		$url='http://www.sendabox.it/_magento/get/prices?from=' .$from .'&to=' .$to .'&weight=' .$weight .'&dimensions=' .$dimensions;
		if($special)
			$url .= '&special=' .$special;
		
		$html = file_get_contents($url);
		
		if ($html) {
			$chiamata = json_decode($html, true);
			if(!is_array($chiamata)) { $chiamata = array($chiamata); }
			foreach($chiamata as $quote_available){
				$carrier = Mage::getModel('sendabox_sendabox/carrier')->load($quote_available['idcarrier'],'carrier_id');
				$id = $carrier ? $carrier->getId() : 3 ;
				$allowedCarriers = explode(',', Mage::getStoreConfig('carriers/sendabox_sendabox/allowed_methods'));
				if($allowedCarriers != '' && !in_array($carrier->getCarrierId(), $allowedCarriers))
					continue;
				$quote=Mage::getModel('sendabox_sendabox/quote')
					->setCarrierID($id)
					->setAccepted(1)
					->setTotalPrice($quote_available['price'])
					->setBasePrice($quote_available['price'])
					->setTax(0)
					->setInsuranceTotalPrice(0)
					->setCurrency('€')
					->setIdPrice($quote_available['idprice'])
					->setCommercialName($quote_available['commercialname'])
					->save();
				
				if($salesQuoteId) $quote->setMagentoQuoteId($salesQuoteId)->save();
			}
		} else {
			   echo 'ERRORE CHIAMATA';
		}
	}
	
	/**
     * Creates a rate method based on a Sendabox quote.
     *
     * @param Mage_Shipping_Model_Rate_Result_Method the quote
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getRateFromQuote($quote, $method_id)
    {
        $price = $quote->getTotalPrice();
        $title = $quote->getDescription();

        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->_title);
        $method->setMethodTitle($title);
        $method->setMethod($method_id);
        $method->setPrice($price);
        $method->setCost(0);
        Mage::log(
				"carrier: {$method->getCarrier()}, CarrierTitle: {$method->getCarrierTitle()}, MethodTitle: {$method->getMethodTitle()}, Method: {$method->getMethod()}, Price: {$method->getPrice()}, Cost: {$method->getCost()}",
				null, 
				'createSendaboxShippingMethod.log', true
			);
        return $method;
    }
	
	protected function getCityId($city_name, $region_name="sa"){
		$city = str_replace(" ", "%20", $city_name);
		$url = 'http://www.sendabox.it/_magento/get/city?q=' .$city;
		$html = file_get_contents($url);
		Mage::log(
				"{$html}",
				null, 
				'getCityId.log', true
			);
		if ($html) {
			   $chiamata = json_decode($html, true);
			   $safe_return = 14323;
			   if(!is_array($chiamata)) { $chiamata = array($chiamata); }
			   if(isset($chiamata['error']) && $chiamata['error']){
					   //echo $chiamata['msg'];
					   return $safe_return;
			   } 
			   else { 
					if(count($chiamata)==0) return $safe_return;
					$i=0;
					foreach($chiamata as $suggest){ 
						$name = strtolower(trim(explode('-',$suggest['value'])[0]));
						$city_tmp = strtolower(trim($city_name));
						Mage::log(
							"suggerito: {$name}, inserito: {$city_tmp}",
							null, 
							'getCityId.log', true
						);
						if($name==$city_tmp){
							$i++;
							$region_tmp = strtolower(trim($region_name));
							$abbreviation = strtolower($suggest['abbreviation']);
							$province = strtolower($suggest['province']);
							if($region_tmp ==$abbreviation || $region_tmp==$province){	
								$id= $suggest['id'];
								return $id;
							}
							$safe_return = $i<2 ? $suggest['id'] : 14323;
						}
					}
					return $safe_return;
			   }
		} else {
			   //echo 'ERRORE CHIAMATA';
			   return 14323;
		}
	}
	
	/**
     * Checks if the to address is within allowed countries
     *
     * @return boolean
     */
    protected function _canShip(Mage_Shipping_Model_Rate_Request $request)
    {
        return array_key_exists($request->getDestCountryId(), Mage::helper('sendabox_sendabox')->getAllowedCountries());
    }
	
	/**
     * Check if carrier has shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return false;
    }
	
	/*public function getAllowedMethods()
	{
		return array(
			'standard' => 'Standard',
			'express' => 'Express',
		);
	}*/
	
	public function getAllowedMethods()
    {
        return explode(',', Mage::getStoreConfig('carriers/sendabox_sendabox/allowed_methods'));
    }
}
