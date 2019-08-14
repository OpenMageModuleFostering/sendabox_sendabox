<?php


class Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_LDV extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $carrier =  $row->getData($this->getColumn()->getParams());
        return '<a href="http://sendabox.it/'.$carrier.'Trasporto/Index/'.$value.'" target="_blank">Link</a>';
    }
}