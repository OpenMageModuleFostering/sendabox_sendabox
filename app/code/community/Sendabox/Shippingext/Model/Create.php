<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 25/6/2015
 * Time: 15:38
 */

class Sendabox_Shippingext_Model_Create {

    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('sendabox_shippingext')->__('Yes')),
            array('value'=>2, 'label'=>Mage::helper('sendabox_shippingext')->__('No')),
        );
    }

}