<?php

class Sendabox_Sendabox_Helper_Data extends Mage_Core_Helper_Abstract {

    const DEFAULT_WAREHOUSE_NAME = 'Magento Warehouse';
    
    private $_sendaboxAttributes = array(
	'sendabox_length',
	'sendabox_width',
	'sendabox_height'
    );

    protected $_allowedCountries = array(
	'IT' => 'Italia',
    );

    /**
     * Retrieves an element from the module configuration data.
     *
     * @param string $field
     */
    public function getConfigData($field) {
		$path = 'sendabox_options/' . $field;
	return Mage::getStoreConfig($path);
    }

    /**
     * Returns array of allowed countries based on Magento system configuration
     * and Sendabox plugin allowed countries.
     * 
     * @return array
     */
    public function getAllowedCountries() {
	$specific = Mage::getStoreConfig('carriers/sendabox_sendabox/sallowspecific');
	//check if all allowed and return selected
	if($specific == 1) {
	    $availableCountries = explode(',', Mage::getStoreConfig('carriers/sendabox_sendabox/specificcountry'));
	    $countries = array_intersect_key($this->_allowedCountries, array_flip($availableCountries));
		return $countries;
	}
	//return all allowed
	    return $this->_allowedCountries;	
    }
    
    
    
    /**
     * Retrieve Default Sendabox Product Attributes from Configuration
     * 
     * @return array
     */
    public function getDefaultSendaboxAttributes()
    {
		return array(

			'sendabox_length'	    => (float)$this->getConfigData('defaults/length'),
			'sendabox_width'	    => (float)$this->getConfigData('defaults/width'),
			'senadbox_height'	    => (float)$this->getConfigData('defaults/height'),
		);
    }
    
    /**
     * Converts given weight from configured unit to grams
     * 
     * @param float $value Weight to convert
     * @param string $currentUnit Current weight unit
     * @return float Converted weight in grams
     */
    public function getWeightInKGrams($value, $currentUnit = null)
    {
	$value = floatval($value);
	$currentUnit = $currentUnit ? $currentUnit : $this->getConfigData('units/weight');
	//from units as specified in configuration
	switch($currentUnit) {
	    
	    case Sendabox_Sendabox_Model_System_Config_Source_Unit_Weight::OUNCES: 
		return $value * 28.3495; break;
	    
	    case Sendabox_Sendabox_Model_System_Config_Source_Unit_Weight::POUNDS: 
		return $value * 453.592; break;
	    
	    default: return $value; break;
	}
    }
    
    /**
     * Converts given distance from configured unit to centimetres
     * 
     * @param float $value Distance to convert
     * @param string $currentUnit Current measure unit 
     * @return float Converted distance in centimetres
     */
    public function getDistanceInCentimetres($value, $currentUnit = null)
    {
	$value = floatval($value);
	$currentUnit = $currentUnit ? $currentUnit : $this->getConfigData('units/measure');
	switch($currentUnit) {
	    
	    case Sendabox_Sendabox_Model_System_Config_Source_Unit_Measure::FEET:
		return $value * 30.48; break;
	    
	    case Sendabox_Sendabox_Model_System_Config_Source_Unit_Measure::INCHES:
		return $value * 2.54; break;
	    
	    default: return $value; break;
	}
    }

    

    

    /**
     * Returns region name saved in customers session
     * @return string|null
     */
    public function getSessionRegion() {
	$data = Mage::getSingleton('customer/session')->getData('estimate_product_shipping');
	if ($data) {
	    return Mage::getModel('directory/region')->load($data['region_id'])->getName();
	}

	return null;
    }

    /**
     * Returns city name saved in customers session
     * @return string|null
     */
    public function getSessionCity() {
	$data = Mage::getSingleton('customer/session')->getData('estimate_product_shipping');
	if ($data) {
	    return $data['city'];
	}

	return null;
    }

    /**
     * Returns postal code saved in customers session
     * @return string|null
     */
    public function getSessionPostcode() {
	$data = Mage::getSingleton('customer/session')->getData('estimate_product_shipping');
	if ($data) {
	    return $data['postcode'];
	}

	return null;
    }

    /**
     * Returns id of the region saved in customers session
     * @return int|null
     */
    public function getSessionRegionId() {
	$data = Mage::getSingleton('customer/session')->getData('estimate_product_shipping');
	if ($data) {
	    return $data['region_id'];
	}

	return null;
    }

    /**
     * Return list of available origin locations
     * 
     * @return array
     */
    public function getLocationList() {
	return array(
	    self::DEFAULT_WAREHOUSE_NAME => self::DEFAULT_WAREHOUSE_NAME
	);
    }
    
    /**
     * Returns location create/update request array
     * 
     * @return array
     */
    public function getOriginRequestArray(Varien_Object $data) {
	return array(
	    'description'   => self::DEFAULT_WAREHOUSE_NAME,
	    'type'	    => 'Origin',
	    'contactName'   => $data->getContactName(),
	    'companyName'   => $data->getCompanyName(),
	    'street'	    => $data->getStreet(),
	    'suburb'	    => $data->getCity(),
	    'state'	    => $data->getRegion(),
	    'code'	    => $data->getPostcode(),
	    'country'	    => $data->getCountry(),
	    'phone1'	    => $data->getPhone1(),
	    'phone2'	    => $data->getPhone2(),
	    'fax'	    => $data->getFax(),
	    'email'	    => $data->getEmail(),
	    'loadingFacilities' => $data->getLoadingFacilities() ? 'Y' : 'N',
	    'forklift'		=> $data->getForklift() ? 'Y' : 'N',
	    'dock'		=> $data->getDock() ? 'Y' : 'N',
	    'limitedAccess'	=> $data->getLimitedAccess() ? 'Y' : 'N',
	    'postalBox'		=> $data->getPobox() ? 'Y' : 'N'
	);
    }


    /**
     * Returns true if shipping quote is dynamic quote 
     * 
     * @param int $quote_id
     * @return boolean
     */
    public function isQuoteDynamic($quote_id) {
	$fixed_carriers = array(
	    Sendabox_Sendabox_Model_Carrier::FLAT_RATE,
	    Sendabox_Sendabox_Model_Carrier::FREE,
	);

	if (in_array($quote_id, $fixed_carriers)) {
	    return false;
	}
	
	return true;
    }
	
	
	public function getCheapestQuote($quotes)
    {
        $cheapest = null;
        foreach ($quotes as $quote) {
            $cheapest = $this->_getCheaper($quote, $cheapest);
        }
        return $cheapest;
    }
    
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
   
}