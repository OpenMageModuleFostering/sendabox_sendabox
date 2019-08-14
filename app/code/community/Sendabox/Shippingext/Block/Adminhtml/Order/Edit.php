<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 18/6/2015
 * Time: 11:32
 */

class Sendabox_Shippingext_Block_Adminhtml_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    protected $_order;
    protected $_order_details;
    protected $_order_boxes;
    public function __construct()
    {
        // $this->_objectId = 'id';
        parent::__construct();
        $this->_blockGroup      = 'sendabox_shippingext';
        $this->_controller      = 'adminhtml_order';
        $this->_mode            = 'edit';

        $this->removeButton('save');
        $this->removeButton('delete');

    }

    public function getOrder()
    { if (!$this->_order) {
        $this->_order = Mage::registry('sendabox_order');
    }
        return $this->_order;
    }

    public function getOrderDetails()
    { if (!$this->_order_details) {
        $this->_order_details = Mage::registry('sendabox_order_details');
    }
        return $this->_order_details;
    }

    public function getHeaderText()
    {
        if ($this->getOrder() && $this->getOrder()->getId()) {
            return $foo= Mage::helper('sendabox_shippingext')->__(
                'Order # %s ',
                $this->htmlEscape($this->getOrder()->getId())
            );
        }
    }
}