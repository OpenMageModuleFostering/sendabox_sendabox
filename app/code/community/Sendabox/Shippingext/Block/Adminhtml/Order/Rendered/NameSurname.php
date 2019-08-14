<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 25/6/2015
 * Time: 16:05
 */
class Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_NameSurname  extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $name = $row->getData($this->getColumn()->getIndex());
        $surname = $row->getData($this->getColumn()->getParams());
        return $name . " " . $surname;
    }
}