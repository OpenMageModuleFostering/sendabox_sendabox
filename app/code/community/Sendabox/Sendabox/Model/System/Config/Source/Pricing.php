<?php

class Sendabox_Sendabox_Model_System_Config_Source_Pricing extends Sendabox_Sendabox_Model_System_Config_Source
{
    
    const FREE                         = 'free';
    const FLAT_RATE                    = 'flat';
    const DYNAMIC                      = 'dynamic';
    //const DYNAMIC_CHEAPEST             = 'dynamiccheap';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::FREE                         => 'Free Shipping',
            self::FLAT_RATE                    => 'Fixed Price / Flat Rate',
            self::DYNAMIC                      => 'Dynamic Pricing (All)',
            //self::DYNAMIC_CHEAPEST             => 'Dynamic Pricing (Cheapest only)',
        );
    }
    
}
