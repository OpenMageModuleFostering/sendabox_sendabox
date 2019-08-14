<?php

class Sendabox_Sendabox_Model_Resource_Shipment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('sendabox_sendabox/shipment');
    }
    
    /**
     * Returns the shipment associated to an order or null if none
     * 
     * NO SUPPORT FOR PARTIAL-SHIPMENTS HERE; ONLY 1 IS RETURNED
     * 
     * @param string $orderId
     * @return null|\Sendabox_Sendabox_Model_Shipment 
     */
    public function loadByOrderId($orderId) {
	if(!$orderId) return false;
	
	$this->addFieldToFilter('order_id', $orderId)->load();
	if($this->count())
	    return $this->getFirstItem ();
	
	return false;
    }       
}