<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 10:41
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table=$installer->getConnection()
    ->newTable($installer->getTable('sendabox_shippingext/shipment'))
    ->addColumn('shipping_id',Varien_Db_Ddl_Table::TYPE_BIGINT,20,array(
        'unsigned'=>true,
        'identity'=>true,
        'nullable'=>false,
        'primary'=>true
    ),'ID')

    ->addColumn('order_id',Varien_Db_Ddl_Table::TYPE_INTEGER,10,array(
        'unsigned'=>true,
        'nullable'=>false,
    ),'Order ID')
    ->addColumn('customer_cost',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(
        'unsigned'=>true,
        'nullable'=>false,
    ),'Customer cost')
    ->addColumn('shipping_name',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Shipping name')
    ->addColumn('shipping_surname',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Shipping surname')
    ->addColumn('shipping_at',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Shipping co')
    ->addColumn('shipping_street',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Shipping address')
    ->addColumn('shipping_number',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Shipping number')
    ->addColumn('shipping_city',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Shipping city')
    ->addColumn('shipping_postcode',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Shipping postcode')
    ->addColumn('shipping_phone',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Shipping phone')
    ->addColumn('shipping_email',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Shipping email')
    ->addColumn('shipping_note',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Shipping note')
    ->addColumn('destination_name',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Destination name')
    ->addColumn('destination_surname',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Destination surname')
    ->addColumn('destination_at',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Destination co')
    ->addColumn('destination_street',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Destination address')
    ->addColumn('destination_number',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Destination number')
    ->addColumn('destination_city',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Destination city')
    ->addColumn('destination_postcode',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Destination postcode')
    ->addColumn('destination_phone',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Destination phone')
    ->addColumn('destination_email',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Destination email')
    ->addColumn('destination_note',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'Destination note');

$installer->getConnection()->createTable($table);

$table=$installer->getConnection()
    ->newTable($installer->getTable('sendabox_shippingext/box'))
    ->addColumn('box_id',Varien_Db_Ddl_Table::TYPE_BIGINT,20,array(
        'unsigned'=>true,
        'identity'=>true,
        'nullable'=>false,
        'primary'=>true
    ),'ID')
    ->addColumn('shipment_id',Varien_Db_Ddl_Table::TYPE_INTEGER,13,array(
        'nullable'=>false,
    ),'Shipment ID')
    ->addColumn('sendabox_order_id',Varien_Db_Ddl_Table::TYPE_INTEGER,10,array(
        'unsigned'=>true,
        'nullable'=>false,
    ),'Sendabox Order ID')
    ->addColumn('sendabox_order_id_encoded',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'unsigned'=>true,
        'nullable'=>false,
    ),'Sendabox Order ID Encoded')
    ->addColumn('carrier',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Carrier')
    ->addColumn('Comment',Varien_Db_Ddl_Table::TYPE_TEXT,255,null,'Status')
    ->addColumn('value',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(
        'nullable'=>false,
    ),'Value')
    ->addColumn('depth',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(
        'nullable'=>false,
    ),'Length')
    ->addColumn('width',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(
        'nullable'=>false,
    ),'Width')
    ->addColumn('height',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(
        'nullable'=>false,
    ),'Height')
    ->addColumn('weight',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(
        'nullable'=>false,
    ),'Weight');

$installer->getConnection()->createTable($table);

$table=$installer->getConnection()
    ->newTable($installer->getTable('sendabox_shippingext/agenda'))
    ->addColumn('sender_id',Varien_Db_Ddl_Table::TYPE_BIGINT,20,array(
        'unsigned'=>true,
        'identity'=>true,
        'nullable'=>false,
        'primary'=>true
    ),'ID')
    ->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Name')
    ->addColumn('surname',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Surname')
    ->addColumn('address',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Address')
    ->addColumn('street_number',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Street Number')
    ->addColumn('city',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'City')
    ->addColumn('province',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Province')
    ->addColumn('co',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>true,
    ),'CO')
    ->addColumn('zip',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Zip')
    ->addColumn('mail',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Email')
    ->addColumn('tel',Varien_Db_Ddl_Table::TYPE_VARCHAR,250,array(
        'nullable'=>false,
    ),'Tel');

$installer->getConnection()->createTable($table);

$installer->endSetup();