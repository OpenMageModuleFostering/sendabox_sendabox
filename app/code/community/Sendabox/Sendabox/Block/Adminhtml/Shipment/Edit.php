<?php

class Sendabox_Sendabox_Block_Adminhtml_Shipment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected $_shipment;
    
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'sendabox_sendabox';
        $this->_controller = 'adminhtml_shipment';
        
        $this->removeButton('save');
        $this->removeButton('delete');

        $add_button_method = 'addButton';
        if (!method_exists($this, $add_button_method)) {
            $add_button_method = '_addButton';
        }
        
        if ($this->getShipment()->getStatus() == Sendabox_Sendabox_Model_System_Config_Source_Shipment_Status::PENDING) {
            $this->$add_button_method('getquote', array(
                'label' => Mage::helper('sendabox_sendabox')->__('Save and Get Quotes'),
                'id' => 'getquote',
                'onclick' => 'saveAndGetQuotes()',
                'value' => '',
                'class' => 'save',
            ));
        }
       

        $max_boxes = 40;
        $script = "
            function saveAndGetQuotes() {
                editForm.submit($('edit_form').action+'and/getquotes/');
            }
            
            function box_add() {
                num_boxes = $$('table#boxes tbody tr').length - 1;
                if (num_boxes < $max_boxes) {
                    if (!box_add.highest_num) {
                        box_add.highest_num = num_boxes;
                    }
                    // add a new box (row) to the table
                    new_row = new Element('tr');
                    new_row.id = 'box_row_' + ++box_add.highest_num;
                    new_row
                        .addClassName(box_add.highest_num % 2 ? 'odd' : 'even')
                        .addClassName('new_box')
                        .insert($('blank_box_row').innerHTML
                            .replace(/@@id@@/g, box_add.highest_num)
                            .replace(/box_blank/g, 'box'));
                    
                    $$('table#boxes tbody')[0].insert(new_row);
                    return box_add.highest_num;
                } else {
                    return false;
                }
            }
            
            function box_clear() {
                // remove all rows in the table.
                $$('table#boxes tr.existing_box, table#boxes tr.new_box').each(function (row) {
                    row.remove();
                });
            }
            
            function box_remove(num) {
                row = $('box_row_' + num);
                if ($('box_' + num + '_id') && $('boxes_deleted')) {
                    if (!$('boxes_deleted').value.length == 0) {
                        $('boxes_deleted').value += ',';
                    }
                    $('boxes_deleted').value += $('box_' + num + '_id').value;
                }
                if (row) {
                    row.remove();
                }
            }
        ";
        
        $this->_formScripts[] = $script;
    }
    
    /**
     * Gets the shipment being edited.
     *
     * @return Sendabox_Sendabox_Model_Shipment
     */
    public function getShipment()
    {
        if (!$this->_shipment) {
            $this->_shipment = Mage::registry('sendabox_shipment_data');
        }
        return $this->_shipment;
    }
    
    /**
     * Gets the current order for the shipment being edited.
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if($this->getShipment()) {
            return $this->getShipment()->getOrder();
        }
        return null;
    }

    public function getHeaderText()
    {
        if ($this->getShipment() && $this->getShipment()->getId()) {
            return $foo= Mage::helper('sendabox_sendabox')->__(
                'Order # %s | %s',
                $this->htmlEscape($this->getShipment()->getOrder()->getRealOrderId()),
                $this->htmlEscape($this->formatDate($this->getShipment()->getOrder()->getCreatedAtDate(), 'medium', true))
            );
        }
    }
    
}
