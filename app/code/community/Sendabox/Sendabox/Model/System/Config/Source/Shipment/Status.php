<?php

class Sendabox_Sendabox_Model_System_Config_Source_Shipment_Status extends Sendabox_Sendabox_Model_System_Config_Source
{
    
    const PENDING =     '0';
    const BOOKED =      '1';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::PENDING     => Mage::helper('sendabox_sendabox')->__('Pending'),
            self::BOOKED      => Mage::helper('sendabox_sendabox')->__('Booked'),
        );
    }
    
}
