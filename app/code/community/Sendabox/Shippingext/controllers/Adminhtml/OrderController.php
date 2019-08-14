<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 18/6/2015
 * Time: 11:21
 */

class Sendabox_Shippingext_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action{

    private $_url = "http://ws.sendabox.it/SBWS.asmx/";
    public function indexAction()
    {
        if($this->getRequest()->getParam("id")){
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Order '.$this->getRequest()->getParam("id").' succesfully created'));
        }
        $this->loadLayout()
            ->_setActiveMenu('sendabox/shipment')
            ->renderLayout();
    }

    public function editAction(){
        $this->loadLayout()
            ->_setActiveMenu('sendabox/shipment');
        $id = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sendabox_shippingext/shipment')->load($id);
        if (!$order->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Shipment does not exist.'));
            $this->_redirect('*/*/');
        }

        Mage::register('sendabox_order', $order);

        $username = Mage::helper('sendabox_shippingext')->getConfigData('general/username');
        $password = Mage::helper('sendabox_shippingext')->getConfigData('general/password');

        $boxes= Mage::getModel('sendabox_shippingext/box')->getCollection()->addFilter('shipment_id',$order->getId());
        $orders=array();
        foreach($boxes as $box){
            array_push($orders,array('idusercityorder' => $box->sendabox_order_id,'encoded' => $box->sendabox_order_id_encoded));
        }
        $jsonData = array(
            'apikey' => $username,
            'apisecret' => $password,
            'orders' => $orders,
        );

        $url=$this->_url.'GetOrders';
        $jsonDataEncoded = json_encode($jsonData);

        $headers=array("Content-Type"=>"application/json");

        $http = new Varien_Http_Adapter_Curl();
        $http->write(Zend_Http_Client::POST, $url, '1.1', $headers, $jsonDataEncoded);
        $header_result = $http->read();
        if ($header_result === false) {
            return false;
        }

        $header_size = $http->getInfo(CURLINFO_HEADER_SIZE) ;
        $body = substr($header_result, $header_size);
        $result = json_decode($body,true);
        if(isset($result["ErrorMsg"]) && $result["ErrorMsg"]){
            echo $result["ErrorMsg"];
        } else {
            Mage::register('sendabox_order_details', $result["Orders"]);
        }
        $this->renderLayout();
    }

}