<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 16:03
 */
class Sendabox_Shippingext_Block_Adminhtml_Shipment extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup      = 'sendabox_shippingext';
        $this->_controller      = 'adminhtml_shipment';
        $this->_headerText = Mage::helper('sendabox_shippingext')->__('Manage Shipments');
        parent::__construct();
        $this->removeButton('add');
            }


}

