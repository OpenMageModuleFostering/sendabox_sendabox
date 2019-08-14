<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 24/6/2015
 * Time: 12:39
 */

class Sendabox_Shippingext_Model_Resource_Agenda extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('sendabox_shippingext/agenda', 'sender_id');
    }

    public function truncate() {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE '.$this->getMainTable());
        return $this;
    }
}