<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 23/6/2015
 * Time: 14:28
 */
class Sendabox_Shippingext_Block_Adminhtml_System_Config_Agenda_Button extends Mage_Adminhtml_Block_System_Config_Form_Field{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sendabox/system/config/agenda/button.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxCheckUrl()
    {
        return $this->getUrl('sendabox/adminhtml_ajax/agenda');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'sendabox_button',
                'label'     => $this->helper('adminhtml')->__('Get data from Sendabox'),
                'onclick'   => 'check()'
            ));

        return $button->toHtml();
    }
}