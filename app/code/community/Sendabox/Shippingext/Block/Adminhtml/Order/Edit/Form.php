<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 16:03
 */
class Sendabox_Shippingext_Block_Adminhtml_Order_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_order;
    private function getOrder()
    {
        if (!$this->_order) {
            $this->_order = Mage::registry('sendabox_order_data');
        }
        return $this->_order;
    }



}
