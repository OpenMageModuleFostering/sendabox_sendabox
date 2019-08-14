<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 16:03
 */
class Sendabox_Shippingext_Block_Adminhtml_Shipment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_order;
    private function getOrder()
    {
        if (!$this->_order) {
            $this->_order = Mage::registry('sendabox_order_data');
        }
        return $this->_order;
    }

    protected function _prepareForm()
    {
        $url='http://ws.sendabox.it/SBWS.asmx/GetCountries';

        $headers=array("Content-Type"=>"application/json");
        $http = new Varien_Http_Adapter_Curl();
        $http->write(Zend_Http_Client::POST, $url, '1.1', $headers, "");
        if ($http->read() === false) {
            return false;
        }

        $countries = array();
        $header_size = $http->getInfo(CURLINFO_HEADER_SIZE) ;
        $body = substr($http->read(), $header_size);
        $result = json_decode($body,true);
        if(isset($result["ErrorMsg"]) && $result["ErrorMsg"]){
            echo $result["ErrorMsg"];
        } else {
           foreach($result as $res)
           {
               $countries[$res["idcountry"]] = $res["name"] ;
           }
        }

        $agenda = array();
        $agenda[0] = "Select destination...";
        $collection = Mage::getModel('sendabox_shippingext/agenda')->getCollection();
        foreach($collection as $c){
            $agenda[$c->getData("sender_id")] = $c->getData("name")." ".$c->getData("surname")." ".$c->getData("address")." ".$c->getData("street_number")." ".$c->getData("city")." ".$c->getData("province");
        }


        $form = new Varien_Data_Form();

        $form->setUseContainer(true);
        $this->setForm($form);
//        $form->addField(
//            'fake_note',
//            'note',
//            array(
//                'text' => '<ul class="messages"><li class="notice-msg"><ul><li>'
//                    .  Mage::helper('sendabox_shippingext')->__('This form is fake, so the data in the grid '
//                        . 'you just clicked on won\'t be here, do not be alarmed, this is normal')
//                    . '</li></ul></li></ul>',
//            )
//        );

        $form->addField(
            'box_shiiping_from',
            'note',
            array(
                'text' => '<div class="box-left">',
            )
        );
        $fieldset = $form->addFieldset(
            'shipping_fieldset',
            array(
                'legend' => Mage::helper('sendabox_shippingext')->__('Departure Information'),
            )
        );
        # now add fields on to the fieldset object, for more detailed info
        # see https://makandracards.com/magento/12737-admin-form-field-types
        $fieldset->addField(
            'shipping_agenda', # the input id
            'select', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Agenda'),
                'name'     => 'shipping_agenda',
                'values'   => $agenda,
                'onchange' => 'populateFields()'
            )
        );
        $fieldset->addField(
            'shipping_name', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Name'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_name',
            )
        );
        $fieldset->addField(
            'shipping_surname', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Surname'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_surname',
            )
        );
        $fieldset->addField(
            'shipping_co', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping CO'),
                'name'     => 'shipping_co',
            )
        );
        $fieldset->addField(
            'shipping_address', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Address'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_address',
            )
        );
        $fieldset->addField(
            'shipping_streetnumber', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Street Number'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_streetnumber',
            )
        );
        $fieldset->addField(
            'shipping_city', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping City'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_city',
            )
        );
        $fieldset->addField(
            'shipping_province', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Province'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_province',
            )
        );
        $fieldset->addField(
            'shipping_zip', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Zip'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_zip',
            )
        );
        $fieldset->addField(
            'shipping_tel', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping Telephone'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'shipping_tel',
            )
        );
        $fieldset->addField(
            'shipping_email', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Shipping email'),
                'class'    => 'required-entry validate-email',
                'required' => true,
                'name'     => 'shipping_email',
            )
        );
        $form->addField(
            '',
            'note',
            array(
                'text' => '</div>',
            )
        );

        $form->addField(
            'box_shiiping_to',
            'note',
            array(
                'text' => '<div class="box-right">',
            )
        );
        $fieldset = $form->addFieldset(
            'destination_fieldset',
            array(
                'legend' => Mage::helper('sendabox_shippingext')->__('Destination Information'),
            )
        );

        $fieldset->addField(
            'destination_name', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Name'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_name',
                'value'     =>$this->getOrder()->getShippingAddress()->getFirstname(),

            )
        );
        $fieldset->addField(
            'destination_surname', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Surname'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_surname',
                'value'     =>$this->getOrder()->getShippingAddress()->getLastname(),
            )
        );
        $fieldset->addField(
            'destination_co', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination CO'),
                'name'     => 'destination_co',
                'value'     =>$this->getOrder()->getShippingAddress()->getCompany(),
            )
        );
        $fieldset->addField(
            'destination_address', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Address'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_address',
                'value'    =>$this->getOrder()->getShippingAddress()->getStreet(1),
            )
        );
        $fieldset->addField(
            'destination_streetnumber', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Street Number'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_streetnumber',

            )
        );
        $fieldset->addField(
            'destination_city', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination City'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_city',
                'value'     =>$this->getOrder()->getShippingAddress()->getCity(),
            )
        );
        $fieldset->addField(
            'destination_province', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Province'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_province',
                'value'     =>$this->getOrder()->getShippingAddress()->getRegion(),
            )
        );

        $fieldset->addField(
            'destination_country', # the input id
            'select', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Country'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_country',
                'value'     => '1',
                'values'    => $countries,
            )
        );
        $fieldset->addField(
            'destination_zip', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Zip'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_zip',
                'value'     =>$this->getOrder()->getShippingAddress()->getPostcode(),
            )
        );
        $fieldset->addField(
            'destination_tel', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Destination Telephone'),
                'class'    => 'required-entry',
                'required' => true,
                'name'     => 'destination_tel',
                'value'     =>$this->getOrder()->getShippingAddress()->getTelephone(),
            )
        );
        $fieldset->addField(
            'payment_method', # the input id
            'text', # the type
            array(
                'label'    => Mage::helper('sendabox_shippingext')->__('Payment Method'),
                'name'     => 'payment_method',
                'value'     =>$this->getOrder()->getPayment()->getMethodInstance()->getTitle(),
                'readonly' => true
            )
        );
        $form->addField(
            'close_destination',
            'note',
            array(
                'text' => '</div>',
            )
        );


        return parent::_prepareForm();
    }

}
