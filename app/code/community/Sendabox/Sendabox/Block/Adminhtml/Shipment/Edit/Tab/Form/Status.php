<?php

class Sendabox_Sendabox_Block_Adminhtml_Shipment_Edit_Tab_Form_Status extends Sendabox_Sendabox_Block_Adminhtml_Shipment_Edit_Tab_Abstract
{
    /**
     * @var Sendabox_Sendabox_Model_Quote
     */
    protected $_customer_selected_quote = null;
    
	/**
     * Gets the description of the Sendabox quote selected by the customer.
     *
     * @return TSendabox_Sendabox_Model_Quote
     */
    public function getCustomerSelectedQuoteDescription()
    {
        return $this->getShipment()->getCustomerSelectedQuoteDescription();
    }
    
	
    public function getShipmentStatusText()
    {
        return Mage::getModel('sendabox_sendabox/system_config_source_shipment_status')
            ->getOptionLabel($this->getShipment()->getStatus());
    }
    
}
