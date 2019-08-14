<?php
/**
 * News List admin edit form tabs block
 *
 * @author Magento
 */
class Sendabox_Shippingext_Block_Adminhtml_Order_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sendabox_order_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('sendabox_shippingext')->__('Order Details'));
    }
}
