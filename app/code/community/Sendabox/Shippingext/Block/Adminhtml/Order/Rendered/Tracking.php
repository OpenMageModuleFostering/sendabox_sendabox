<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 23/6/2015
 * Time: 18:01
 */
class Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_Tracking extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        return '<a href="http://sendabox.it/Trackings/Index/'.$value.'" target="_blank">Link</a>';
    }
}