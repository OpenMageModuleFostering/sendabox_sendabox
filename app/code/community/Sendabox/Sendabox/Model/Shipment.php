<?php

/**
 * @method int getId()
 * @method int getOrderId()
 * @method string getCustomerSelectedQuoteId()
 * @method string getCustomerSelectedQuoteDescription()
 * @method string getAdminSelectedQuoteId()
 * @method float getAnticipatedCost()
 * @method int getStatus()
 * @method int getBookingRequestId()
 * @method boolean getInsurance()
 * @method string getDestinationCountry()
 * @method string getDestinationPostcode()
 * @method string getDestinationCity()
 * @method string getReadyDate()
 * @method string getReadyTime()
 * @method string getCustomerSelectedOptions()
 * @method string getOrderItems()
 *
 */
class Sendabox_Sendabox_Model_Shipment extends Mage_Core_Model_Abstract
{
    
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order = null;
    
    /**
     * @var array
     */
    protected $_boxes = null;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('sendabox_sendabox/shipment');
    }
	
	public function getData($key = '', $index = null)
    {
        switch ($key) {
            case 'selected_quote_description': return $this->getSelectedQuoteDescription();
            case 'created_at':                 return $this->getCreatedAt();
            case 'order_number':               return $this->getOrderNumber();
            case 'shipping_paid':              return $this->getShippingPaid();
            default:
        }
        return parent::getData($key, $index);
    }
    
    /**
     * Gets the Magento order associated with this shipment.
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order && $this->getId()) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        return $this->_order;
    }
    
    /**
     * Gets the creation date of this shipment.
     */
    public function getCreatedAt()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getCreatedAt();
        }
        return null;
    }
    
    /**
     * Gets the Magento order number (as shown to customers, e.g. 100000123)
     */
    public function getOrderNumber()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getIncrementId();
        }
        return null;
    }
    
	public function getBoxes()
    {
        return Mage::getModel('sendabox_sendabox/box')->getCollection()
            ->addFieldToFilter('shipment_id', $this->getId());
    }
	
	/**
     * Gets the selected quote.
     *
     * If the Sendabox shipment status is Pending or Cancelled, this will be the
     * quote selected by the user during checkout. If the Sendabox shipment
     * status is Booked, then it will be the booked quote.
     *
     * @return Sendabox_Sendabox_Model_Quote
     */
	public function getSelectedQuote()
    {
        $quote = null;
        
        switch ($this->getStatus()) {
            case Sendabox_Sendabox_Model_System_Config_Source_Shipment_Status::BOOKED:
                $quote = Mage::getModel('sendabox_sendabox/quote')
                    ->load($this->getAdminSelectedQuoteId());
                break;
            case Sendabox_Sendabox_Model_System_Config_Source_Shipment_Status::PENDING:
            default:
                $quote = Mage::getModel('sendabox_sendabox/quote')
                    ->load($this->getCustomerSelectedQuoteId());
                break;
        }
        
        return $quote;
    }
	
	/**
     * Clears all quotes from the database relating to this shipment.
     */
    public function clearQuotes()
    {
		
        $old_quotes = Mage::getModel('sendabox_sendabox/quote')->getCollection()
            ->addFieldToFilter('magento_quote_id', $this->getOrder()->getQuoteId());
        foreach ($old_quotes as $quote) {
            /* @var $quote Sendabox_Sendabox_Model_Quote */
            $quote->delete();
        }
        return $this;
    }
	
	public function getQuotes()
    {
        return Mage::getModel('sendabox_sendabox/quote')->getCollection()
            ->addFieldToFilter('magento_quote_id', $this->getOrder()->getQuoteId());
    }
	
	public function getNewQuotes(){
	Mage::log(
				"dentro getnewquotes",
				null, 
				'aggiornamento.log', true
			);
		$boxes = $this->getBoxes();
		if(!$boxes) return;
		$i = 1;
		$destination = $this->getDestinationId();
		foreach($boxes as $shipment){
			$i++;
			$weight = $shipment->getWeight();
			$weight = ceil(Mage::helper('sendabox_sendabox')->getWeightInKGrams($weight,$shipment->getWeightUnit()));
			$unit = Mage::helper('sendabox_sendabox')->getConfigData('units/weight');
			Mage::log(
				"{$weight}",
				null, 
				'units.log', true
			);
			$dimensions = '' .ceil(Mage::helper('sendabox_sendabox')->getDistanceInCentimetres($shipment->getWidth(),$shipment->getMeasureUnit())) .',' .ceil(Mage::helper('sendabox_sendabox')->getDistanceInCentimetres($shipment->getHeight(),$shipment->getMeasureUnit())) .',' .ceil(Mage::helper('sendabox_sendabox')->getDistanceInCentimetres($shipment->getLength(),$shipment->getMeasureUnit()));
			Mage::log(
				"dimensioni: {$dimensions}",
				null, 
				'units.log', true
			);
			$destination = $this->getDestinationId();
			$from = Mage::getStoreConfig('sendabox_options/origin/city_id');
			if($destination){
			
				$this->createQuotes($from,$destination,$weight,$dimensions,$this->getOrder()->getQuoteId());
				$this->createQuotes($from,$destination,$weight,$dimensions,$this->getOrder()->getQuoteId(),11);
				$this->createQuotes($from,$destination,$weight,$dimensions,$this->getOrder()->getQuoteId(),13);
			}
		}
		$i--;
		foreach($this->getQuotes() as $quote){
			$price = $quote->getTotalPrice();
			$quote->setTotalPrice($price*$i)->save();
		}
	}
	
	protected function createQuotes($from= 0, $to= 0, $weight, $dimensions='10,10,10', $salesQuoteId=null,$special=null){
	/*if(!$from || !$to || !$weight)
			return false;*/
		$url='http://www.sendabox.it/_magento/get/prices?from=' .$from .'&to=' .$to .'&weight=' .$weight .'&dimensions=' .$dimensions;
		if($special)
			$url .= '&special=' .$special;
		$html = file_get_contents($url);
		if ($html) {
			$chiamata = json_decode($html, true);
			if(isset($chiamata['error']) && $chiamata['error']){
				//$this->clearQuotes()->save();	
				return;
			   }
			if(!is_array($chiamata)) { $chiamata = array($chiamata); }
			foreach($chiamata as $quote_available){
				$carrier = Mage::getModel('sendabox_sendabox/carrier')->load($quote_available['idcarrier'],'carrier_id');
				$id = $carrier ? $carrier->getId() : 3 ;
				$allowedCarriers = explode(',', Mage::getStoreConfig('carriers/sendabox_sendabox/allowed_methods'));
				if(!in_array($carrier->getCarrierId(), $allowedCarriers)){
					continue;}
				$existing_quote = Mage::getModel('sendabox_sendabox/quote')->getCollection()
					->addFieldToFilter('magento_quote_id', $this->getOrder()->getQuoteId())
					->addFieldToFilter('carrier_id', $id)
					->addFieldToFilter('commercial_name', $quote_available['commercialname'])->getFirstItem();
				if($existing_quote->getTotalPrice() < $quote_available['price']){
					$existing_quote->delete()->save();
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
			}
		} else {
			   echo 'ERRORE CHIAMATA';
		}
	}
}
