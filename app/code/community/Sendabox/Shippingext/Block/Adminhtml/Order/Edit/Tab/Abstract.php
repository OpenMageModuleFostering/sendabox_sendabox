<?php

abstract class Sendabox_Shippingext_Block_Adminhtml_Order_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Template
{
    
    protected $_helper = null;

    public function __construct()
    {
        parent::__construct();
        $this->setParentBlock(Mage::getBlockSingleton('sendabox_shippingext/adminhtml_order_edit'));
    }
    
    /**
     * Gets the shipment being edited.
     *
     * @return Sendabox_Sendabox_Model_Shipment
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }
    
    /**
     * Gets the saved Sendabox quotes for this order from the database.
     *
     * @return Sendabox_Sendabox_Model_Mysql4_Quote_Collection
     */
    public function getQuotes()
    {
        return $this->getOrder()->getQuotes(true);
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
        return Mage::getModel('sendabox_shippingext/system_config_source_unit_weight')
            ->getBriefOptionLabel($unit);
    }
    
    public function getMeasureUnitText($unit = null)
    {
        if (!$unit) {
            $unit = $this->getSendaboxHelper()->getConfigData('units/measure');
        }
        return Mage::getModel('sendabox_shippingext/system_config_source_unit_measure')
            ->getBriefOptionLabel($unit);
    }
    
    public function getSendaboxHelper() {
        if (!$this->_helper) {
            $this->_helper = Mage::helper('sendabox_shippingext');
        }
        return $this->_helper;
    }
    
}
