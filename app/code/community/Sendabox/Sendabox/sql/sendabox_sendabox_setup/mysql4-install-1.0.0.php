<?php

set_time_limit(0);

/* @var $this Mage_Eav_Model_Entity_Setup */
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer = $this;
$installer->startSetup();

// Add custom Sendabox attributes
$installer->addAttributeGroup('catalog_product', 'Default', 'Sendabox', 99);

$installer->addAttribute('catalog_product', 'sendabox_length',
        array(
            'type' => 'decimal',
            'label' => 'Length',
            'group' => 'Sendabox',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false
        )
);

$installer->addAttribute('catalog_product', 'sendabox_width',
        array(
            'type' => 'decimal',
            'label' => 'Width',
            'group' => 'Sendabox',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false
        )
);

$installer->addAttribute('catalog_product', 'sendabox_height',
        array(
            'type' => 'decimal',
            'label' => 'Height',
            'group' => 'Sendabox',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'unique' => false
        )
);

// Create custom tables
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('sendabox_shipment')};
CREATE TABLE {$this->getTable('sendabox_shipment')} (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(10) UNSIGNED NOT NULL,
  `status` int(10) NOT NULL DEFAULT '0',
  `customer_selected_quote_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `customer_selected_quote_description` TEXT NOT NULL,
  `admin_selected_quote_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `customer_cost` decimal(12,4) UNSIGNED NOT NULL,
  `destination_name` varchar(250) NOT NULL,
  `destination_surname` varchar(250) NOT NULL,
  `destination_at` varchar(250),
  `destination_street` varchar(250) NOT NULL,
  `destination_number` varchar(250),
  `destination_city` varchar(250) NOT NULL,
  `destination_id` varchar(250),
  `destination_postcode` varchar(250) NOT NULL,
  `destination_phone` varchar(250),
  `destination_email` varchar(250) NOT NULL,
  `destination_note` varchar(250),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->run("
DROP TABLE IF EXISTS {$this->getTable('sendabox_box')};
CREATE TABLE {$this->getTable('sendabox_box')} (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipment_id` int(13) NOT NULL,
  `comment` text NOT NULL,
  `qty` int(11) NOT NULL DEFAULT '1',
  `value` decimal(12,4) NOT NULL,
  `measure_unit` varchar(255) NOT NULL,
  `length` decimal(12,4) NOT NULL,
  `width` decimal(12,4) NOT NULL,
  `height` decimal(12,4) NOT NULL,
  `weight_unit` varchar(255) NOT NULL,
  `weight` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('sendabox_quote')};
CREATE TABLE {$this->getTable('sendabox_quote')} (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `magento_quote_id` int(10) UNSIGNED NOT NULL,
  `carrier_id` int(13) UNSIGNED NOT NULL,
  `accepted` boolean NOT NULL DEFAULT '0',
  `total_price` decimal(12, 4) NOT NULL,
  `base_price` decimal(12, 4) NOT NULL,
  `tax` decimal(12, 4) NOT NULL,
  `insurance_total_price` decimal(12, 4) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `id_price` int(13) UNSIGNED NOT NULL,
  `commercial_name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('sendabox_carrier')};
CREATE TABLE {$this->getTable('sendabox_carrier')} (
  `id` int(13) UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrier_id` bigint(20) NOT NULL,
  `company_name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`carrier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('sendabox_carrier')} (`id`, `carrier_id`, `company_name`) VALUES
(1, 2, 'Bartolini'),
(2, 6, 'SDA');
");



$installer
	->getConnection()
	->addConstraint(
	'FK_QUOTE_CARRIER',
	$installer->getTable('sendabox_quote'),
	'carrier_id',
	$installer->getTable('sendabox_carrier'),
	'id',
	'cascade',
	'cascade'
);





// Insert a list of states into the regions database. Magento will then pick
// these up when displaying addresses and allow the user to select from a drop-down
// list, rather than having to type them in manually.
/*$regions = array(
    array('code' => 'ACT', 'name' => 'Australia Capital Territory'),
    array('code' => 'NSW', 'name' => 'New South Wales'),
    array('code' => 'NT', 'name' => 'Northern Territory'),
    array('code' => 'QLD', 'name' => 'Queensland'),
    array('code' => 'SA', 'name' => 'South Australia'),
    array('code' => 'TAS', 'name' => 'Tasmania'),
    array('code' => 'VIC', 'name' => 'Victoria'),
    array('code' => 'WA', 'name' => 'Western Australia')
);

$db = Mage::getSingleton('core/resource')->getConnection('core_read');

foreach ($regions as $region) {
    // Check if this region has already been added
    $result = $db->fetchOne("SELECT code FROM " . $this->getTable('directory_country_region') . " WHERE `country_id` = 'AU' AND `code` = '" . $region['code'] . "'");
    if ($result != $region['code']) {
        $installer->run(
                "INSERT INTO `{$this->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
            ('AU', '" . $region['code'] . "', '" . $region['name'] . "');
            INSERT INTO `{$this->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
            ('en_US', LAST_INSERT_ID(), '" . $region['name'] . "'), ('en_AU', LAST_INSERT_ID(), '" . $region['name'] . "');"
        );
    }
}
*/
	/**Add foreign key to table. If FK with same name exist - it will be deleted
     *
     * @param string $fkName foreign key name
     * @param string $tableName main table name
     * @param string $keyName main table field name
     * @param string $refTableName refered table name
     * @param string $refKeyName refered table field name
     * @param string $onUpdate on update statement
     * @param string $onDelete on delete statement
     * @param bool $purge
     * @return mixed
     */

$installer->endSetup();
