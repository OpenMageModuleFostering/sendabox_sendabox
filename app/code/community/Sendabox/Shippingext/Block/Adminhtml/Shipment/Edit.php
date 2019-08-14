<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 16:03
 */
class Sendabox_Shippingext_Block_Adminhtml_Shipment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_order;
    public function __construct()
    {
        // $this->_objectId = 'id';
        parent::__construct();
        $this->_blockGroup      = 'sendabox_shippingext';
        $this->_controller      = 'adminhtml_shipment';
        $this->_mode            = 'edit';

        $this->removeButton('save');
        $this->removeButton('delete');



    }


    public function getOrder()
    { if (!$this->_order) {
        $this->_order = Mage::registry('sendabox_order_data');
    }
        return $this->_order;
    }

    public function getHeaderText()
    {
        $baseDir = Mage::getBaseDir('app').DS.'code/community/Sendabox/Shippingext/tmp/';

        $file=$this->getOrder()->getRealOrderId().'.xml';
        if(file_exists($baseDir.$file)){
            unlink($baseDir.$file);
        }
        if ($this->getOrder() && $this->getOrder()->getId()) {
            return $foo= Mage::helper('sendabox_shippingext')->__(
                'Order # %s | %s',
                $this->htmlEscape($this->getOrder()->getRealOrderId()),
                $this->htmlEscape($this->formatDate($this->getOrder()->getCreatedAtDate(), 'medium', true))
            );
        }
    }


}
