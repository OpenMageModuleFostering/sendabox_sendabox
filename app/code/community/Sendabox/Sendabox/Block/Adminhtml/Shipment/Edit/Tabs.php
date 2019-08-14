<?php

class Sendabox_Sendabox_Block_Adminhtml_Shipment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sendabox_shipment_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('sendabox_sendabox')->__('Shipment View'));
    }

}
