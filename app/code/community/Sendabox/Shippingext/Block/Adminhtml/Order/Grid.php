<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 18/6/2015
 * Time: 11:38
 */

class Sendabox_Shippingext_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sendabox_shippingext/shipment')->getCollection()
           ->join('sendabox_shippingext/box', 'main_table.shipping_id=`sendabox_shippingext/box`.`shipment_id`',array('sendabox_order_id_encoded','value','carrier'));

        $collection->getSelect()->group('shipment_id')->order(array(
            'shipping_id DESC'
        ));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('shipping_id', array(
            'header' => $this->__('Shipping Id #'),
            'index' => 'shipping_id',
        ));

        $this->addColumn('order_id', array(
            'header' => $this->__('Order ID'),
            'index' => 'order_id'
        ));

        $this->addColumn('destination_name', array(
            'header' => $this->__('Order Name/Surname'),
            'renderer' =>  'Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_NameSurname',
            'params' => 'destination_surname',
            'index' => 'destination_name'
        ));

        $this->addColumn('value', array(
            'header' => $this->__('Price'),
            'renderer' =>  'Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_Price',
            'index' => 'value'
        ));
        $this->addColumn('ldv', array(
            'header' => $this->__('LDV'),
            'type' => 'text',
            'renderer' =>  'Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_LDV',
            'params' => 'carrier',
            'index' => 'sendabox_order_id_encoded'
        ));

        $this->addColumn('tracking', array(
            'header' => $this->__('Tracking'),
            'type' => 'text',
            'renderer' =>  'Sendabox_Shippingext_Block_Adminhtml_Order_Rendered_Tracking',
            'index' => 'sendabox_order_id_encoded'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('sendabox_shippingext')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                'view' => array(
                    'caption' => Mage::helper('sendabox_shippingext')->__('View'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));

        $this->addExportType('*/*/exportExcel', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}