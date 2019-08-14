<?php

class Sendabox_Sendabox_Model_Resource_Quote_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    protected $_options = null;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('sendabox_sendabox/quote');
    }
    
    public function __clone()
    {
        $this->load();
        $new_items = array();
        foreach ($this->_items as $item) {
            $new_items[] = clone $item;
        }
        $this->_items = $new_items;
    }
    
    
    /**
     * Gets the cheapest quote in the collection.
     *
     * @return Sendabox_Sendabox_Model_Quote
     */
    public function getCheapest()
    {
        $cheapest = null;
        foreach ($this->_items as $item) {
            $cheapest = $this->_getCheaper($item, $cheapest);
        }
        return $cheapest;
    }
    
    /**
     * Returns the cheaper of two quotes.
     *
     * If one is null, the other is returned. If both are the same price, the
     * first quote is returned.
     *
     * @param Sendabox_Sendabox_Model_Quote $a the first quote.
     * @param Sendabox_Sendabox_Model_Quote $b the second quote.
     *
     * @return Sendabox_Sendabox_Model_Quote
     */
    protected function _getCheaper($a, $b)
    {
        // if one is null, return the other (if both are null, null is returned).
        if (is_null($a)) {
            return $b;
        }
        if (is_null($b)) {
            return $a;
        }
        
        return $a->getTotalPrice() <= $b->getTotalPrice() ? $a : $b;
    }
    
    /**
     * Don't try to load the collection if specific items have been added.
     *
     * @see Varien_Data_Collection::addItem()
     */
    public function addItem(Varien_Object $item)
    {
        $this->_setIsLoaded();
        return parent::addItem($item);
    }
    
}
