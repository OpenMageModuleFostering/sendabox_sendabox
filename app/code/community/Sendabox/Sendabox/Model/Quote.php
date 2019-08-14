<?php

class Sendabox_Sendabox_Model_Quote extends Mage_Core_Model_Abstract
{
    
    protected $_carrier = null;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('sendabox_sendabox/quote');
    }
    
    public function __clone()
    {
        $this->_carrier = clone $this->getCarrier();
    }
    
    /**
     * Sets the carrier providing this quote.
     *
     * @param <type> $carrier_id
     *
     * @return Sendabox_Sendabox_Model_Quote
     */
    public function setCarrier($carrier_id)
    {
        $carrier = Mage::getModel('sendabox_sendabox/carrier')
            ->load($carrier_id);
            
        if ($carrier->getId() == $carrier_id) {
            // exists in the database
            $this->_carrier = $carrier;
            $this->setData('carrier_id', $carrier_id);
        }
        return $this;
    }
    
    /**
     * Gets the carrier providing this quote.
     *
     * @return Sendabox_Sendabox_Model_Carrier
     */
    public function getCarrier()
    {
        if (!$this->_carrier) {
            $this->setCarrier($this->getCarrierId());
        }
        return $this->_carrier;
    }
    
    
    public function toBookingRequestArray($options)
    {
	
        
        $request = array(
            'totalPrice'     => $this->getTotalPrice(),
            'basePrice'      => $this->getBasePrice(),
            'tax'            => $this->getTax(),
            'currency'       => $this->getCurrency(),
            'carrierId'      => $this->getCarrier()->getCarrierId(),
        );
        
        
        return $request;
    }
    
    
    public function getDescription($showMethod = false)
    {
        $title = '';
		if ($showMethod)
			$title .= $this->getCarrier()->getCompanyName() .' - ';
		$title .= $this->getCommercialName();
        
        //return $title . ' ' . $this->getExtraTitle();
		return $title;
    }
}
