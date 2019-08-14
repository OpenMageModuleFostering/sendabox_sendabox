<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 10:41
 */ 
class Sendabox_Shippingext_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getConfigData($field) {
        $path = 'sendabox_settings/' . $field;
        return Mage::getStoreConfig($path);
    }

}