<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 23/6/2015
 * Time: 14:33
 */
class Sendabox_Shippingext_Block_Adminhtml_Check extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup      = 'sendabox_shippingext';
        $this->_controller      = 'adminhtml_check';
        parent::__construct();
        $this->removeButton('add');
    }
}