<?php

class Sendabox_Sendabox_Model_Resource_Box_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('sendabox_sendabox/box');
    }
    
}
