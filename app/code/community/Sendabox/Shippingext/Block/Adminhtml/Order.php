<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 18/6/2015
 * Time: 11:30
 */
class Sendabox_Shippingext_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup      = 'sendabox_shippingext';
        $this->_controller      = 'adminhtml_order';
        $this->_headerText = Mage::helper('sendabox_shippingext')->__('Orders');
        parent::__construct();
        $this->removeButton('add');
    }
}