<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 23/6/2015
 * Time: 18:20
 */
class Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        return number_format($value,2).'€';
    }
}