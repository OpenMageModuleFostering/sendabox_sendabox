<?php

class Sendabox_Shippingext_Block_Adminhtml_Order_Edit_Tab_Form
    extends Sendabox_Shippingext_Block_Adminhtml_Order_Edit_Tab_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();

    }

    public function getTabLabel()
    {
        return $this->__('Information');
    }

    public function getTabTitle()
    {
        return $this->__('Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }



}
