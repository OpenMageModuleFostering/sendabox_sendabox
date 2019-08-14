<?php

class Sendabox_Sendabox_Model_Resource_Box extends Mage_Core_Model_Mysql4_Abstract
{
    
    public function _construct()
    {
        $this->_init('sendabox_sendabox/box', 'id');
    }
    
}
