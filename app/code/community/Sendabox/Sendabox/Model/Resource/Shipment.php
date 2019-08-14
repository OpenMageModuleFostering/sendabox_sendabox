<?php

class Sendabox_Sendabox_Model_Resource_Shipment extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('sendabox_sendabox/shipment', 'id');
    }
    
}
