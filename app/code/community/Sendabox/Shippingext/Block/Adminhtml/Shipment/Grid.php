<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 16:03
 */
class Sendabox_Shippingext_Block_Adminhtml_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        // $this->setDefaultSort('COLUMN_ID');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addAttributeToSelect('*')
            ->join('sales/order_address', 'main_table.entity_id=`sales/order_address`.parent_id and `sales/order_address`.address_type="shipping"', array('city','region'))
            ->addAttributeToFilter('status', array('nin' => array('canceled','complete')));
        ;
            $collection->getSelect()->order(array(
                'created_at DESC'
            ));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('order_number', array(
            'header' => $this->__('Order #'),
            'index' => 'increment_id',
        ));

        $this->addColumn('order_status', array(
            'header' => $this->__('Order status'),
            'index' => 'status'
        ));

        $this->addColumn('created_at', array(
            'header' => $this ->__('Purchased On'),
            'type' => 'datetime',
            'index' => 'created_at',
            'filter_index' => '`sales/order`.created_at',
        ));
        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
             'index' => 'shipping_name',
         ));

        $this->addColumn('city', array(
            'header' => $this->__('Ship to city'),
            'index' => 'city',
        ));
        $this->addColumn('region', array(
            'header' => $this->__('Ship to region'),
            'index' => 'region',
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
            'index' => 'stores',
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

        protected function _prepareMassaction()
    {
        $modelPk = Mage::getModel('sendabox_shippingext/shipment')->getResource()->getIdFieldName();
        $this->setMassactionIdField($modelPk);
        $this->getMassactionBlock()->setFormFieldName('ids');
        // $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> $this->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
        ));
        return $this;
    }
    }
