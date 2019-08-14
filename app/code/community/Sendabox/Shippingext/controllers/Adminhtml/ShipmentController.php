<?php
/**
 * Created by PhpStorm.
 * User: Stathis
 * Date: 28/5/2015
 * Time: 16:18
 */
class Sendabox_Shippingext_Adminhtml_ShipmentController extends Mage_Adminhtml_Controller_Action{

    private $_url = "http://ws.sendabox.it/SBWS.asmx/";
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sendabox/shipment')
            ->renderLayout();
    }

    public function editAction(){
        $this->loadLayout()
            ->_setActiveMenu('sendabox/shipment');
        $id = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($id);
        if (!$order->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Shipment does not exist.'));
            $this->_redirect('*/*/');
        }

        $notices = array();

        if($this->checkCredits() == 0){
            $this->_getSession()->addError('No credits remaining or error in the settings configuration of the extension');
            //$notices[] = 'No credits remaining or error in the settings configuration of the extension';
        }

        if($this->checkCredits()<= 10){
            $notices[] = 'Check your credits, only {$this->checkCredits()} remaining';
        }

        Mage::register('sendabox_order_data', $order);

        $this->renderLayout();
    }

    protected function checkCredits(){
        $username = Mage::helper('sendabox_shippingext')->getConfigData('general/username');
        $password = Mage::helper('sendabox_shippingext')->getConfigData('general/password');
        $jsonData = array(
            'apikey' => $username,
            'apisecret' => $password
        );
        $url=$this->_url.'GetCredits';
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
            return 0;
        } else {
            return $result['Credits'];
        }


    }

}