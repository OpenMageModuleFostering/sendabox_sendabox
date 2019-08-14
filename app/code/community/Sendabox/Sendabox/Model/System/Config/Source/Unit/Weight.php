<?php

class Sendabox_Sendabox_Model_System_Config_Source_Unit_Weight extends Sendabox_Sendabox_Model_System_Config_Source_Unit
{
    
    const KILOGRAMS = 'Kilograms';
    const OUNCES    = 'Ounces';
    const POUNDS    = 'Pounds';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::KILOGRAMS => 'Kilograms',
            self::OUNCES    => 'Ounces',
            self::POUNDS    => 'Pounds',
        );
    }
    
    protected function _setupBriefOptions()
    {
        $this->_brief_options = array(
            self::KILOGRAMS => 'kg',
            self::OUNCES    => 'oz.',
            self::POUNDS    => 'lb.',
        );
    }
    
}
