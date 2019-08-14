<?php

class Sendabox_Sendabox_Block_Adminhtml_Shipment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        $this->_blockGroup = 'sendabox_sendabox';
        $this->_controller = 'adminhtml_shipment';
        $this->_headerText = Mage::helper('sendabox_sendabox')->__('Manage Shipments');
        parent::__construct();
        $this->removeButton('add');
    }
    
}
