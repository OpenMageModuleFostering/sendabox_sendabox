<?php

class Sendabox_Shippingext_Block_Adminhtml_Shipment_Edit_Tab_Form_Quotes extends Sendabox_Sendabox_Block_Adminhtml_Shipment_Edit_Tab_Abstract
{
    
    protected $_cheapest_quote_id;
    protected $_customer_selected_quote_id;
    
    public function __construct() {
        parent::__construct();
        if (($quotes = $this->getQuotes()->load())) {
            if(($cheapest_quote = $quotes->getCheapest())) {
                $this->_cheapest_quote_id = $cheapest_quote->getId();
            }
        }
    
        if ($this->getShipment()) {
            $this->_customer_selected_quote_id = $this->getShipment()->getCustomerSelectedQuoteId();
        }
    }
    
    public function formatQuotePrice(Sendabox_Sendabox_Model_Quote $quote)
    {
        return $this->formatCurrency($quote->getTotalPrice());
            /*$quote->getCurrency() . ' ' .
            $this->formatCurrency($quote->getTotalPrice()) .
            ' (inc. ' .
            $this->formatCurrency($quote->getTax()) .
            ' tax)';*/
    }
    
    public function getQuoteNotes(Sendabox_Sendabox_Model_Quote $quote)
    {
        $text = '';
        
        if ($this->_cheapest_quote_id == $quote->getId()) {
            $text .= 'Cheapest';
        }
        
        if ($this->_customer_selected_quote_id == $quote->getId()) {
            if ($text) {
                $text .= ', ';
            }
            $text .= 'Customer Selected';
        }	
        
        return $text;
    }
    
}
