<?php

class Sendabox_Sendabox_Model_System_Config_Source_Unit_Measure extends Sendabox_Sendabox_Model_System_Config_Source_Unit
{
    
    const CENTIMETRES = 'Centimetres';
    const INCHES      = 'Inches';
    const FEET        = 'Feet';
    
    protected function _setupOptions()
    {
        $this->_options = array(
            self::CENTIMETRES => 'Centimetres',
            self::INCHES      => 'Inches',
            self::FEET        => 'Feet',
        );
    }
    
    protected function _setupBriefOptions()
    {
        $this->_brief_options = array(
            self::CENTIMETRES => 'cm',
            self::INCHES      => 'in.',
            self::FEET        => 'ft.',
        );
    }
    
}
