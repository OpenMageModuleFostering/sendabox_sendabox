<?php

abstract class Sendabox_Sendabox_Block_Adminhtml_Shipment_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Template
{
    
    protected $_helper = null;

    public function __construct()
    {
        parent::__construct();
        $this->setParentBlock(Mage::getBlockSingleton('sendabox_sendabox/adminhtml_shipment_edit'));
    }
    
    /**
     * Gets the shipment being edited.
     *
     * @return Sendabox_Sendabox_Model_Shipment
     */
    public function getShipment()
    {
        return $this->getParentBlock()->getShipment();
    }
    
    /**
     * Gets the saved Sendabox quotes for this order from the database.
     *
     * @return Sendabox_Sendabox_Model_Mysql4_Quote_Collection
     */
    public function getQuotes()
    {
        return $this->getShipment()->getQuotes(true);
    }
    
    public function formatCurrency($price)
    {
        return Mage::helper('core')->currency($price);
    }
    
    public function getWeightUnitText($unit = null)
    {
        if (!$unit) {
            $unit = $this->getSendaboxHelper()->getConfigData('units/weight');
        }
        return Mage::getModel('sendabox_sendabox/system_config_source_unit_weight')
            ->getBriefOptionLabel($unit);
    }
    
    public function getMeasureUnitText($unit = null)
    {
        if (!$unit) {
            $unit = $this->getSendaboxHelper()->getConfigData('units/measure');
        }
        return Mage::getModel('sendabox_sendabox/system_config_source_unit_measure')
            ->getBriefOptionLabel($unit);
    }
    
    public function getSendaboxHelper() {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('sendabox_sendabox');
        }
        return $this->_helper;
    }
    
}
