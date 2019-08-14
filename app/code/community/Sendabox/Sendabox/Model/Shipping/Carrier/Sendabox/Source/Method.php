<?php

class Sendabox_Sendabox_Model_Shipping_Carrier_Sendabox_Source_Method
{
    
    public function toOptionArray()
    {
        $options = array();
        
        $carriers = Mage::getModel('sendabox_sendabox/carrier')->getCollection();
        foreach ($carriers as $carrier) {
            $options[] = array('value' => $carrier->getCarrierId(), 'label' => $carrier->getCompanyName());
        }
        
        return $options;
    }


    /**
     * Gets all the options in the key => value type array.
     *
     * @return array
     */
    public function getOptions($please_select = false)
    {
        if ($please_select) {
            $options = array(null => '--Please Select--');
        }

        $carriers = Mage::getModel('sendabox_sendabox/carrier')->getCollection();
        foreach ($carriers as $carrier) {
	    if(!$carrier->getCarrierId())
		continue;
	    
            $options[$carrier->getCarrierId()] = $carrier->getCompanyName();
        }

        return $options;
    }  
    
    /**
     * Gets all the options for html form.
     * 
     * @return array ( 0 => array(
     *		'label' = 'my label',
     *		'value' = 'my value'
     *		)
     * );
     */
    public function getOptionsForForm($enabledOnly = false)
    {
	$options = array();
        $carriers = Mage::getModel('sendabox_sendabox/carrier')->getCollection();
        foreach ($carriers as $carrier) {
	    if(!$carrier->getCarrierId())
		continue;
	    
	    //skip carriers which are not allowed in config
	    if($enabledOnly) {
		$allowedCarriers = explode(',', Mage::getStoreConfig('carriers/sendabox_sendabox/allowed_methods'));
		if(!in_array($carrier->getCarrierId(), $allowedCarriers)) {
		    continue;
		}
	    }
	    
	    $options[] = array(
		'label' => $carrier->getCompanyName(),
		'value' => $carrier->getCarrierId()
	    );
        }

        return $options;
    }      
}
