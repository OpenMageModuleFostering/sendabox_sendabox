<?php
/**
 * Created by: Stathis Kapaniaris
 */

class Sendabox_Shippingext_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action{

    private $_url = "http://ws.sendabox.it/SBWS.asmx/";
    public function indexAction()
    {
        $username = Mage::helper('sendabox_shippingext')->getConfigData('general/username');
        $password = Mage::helper('sendabox_shippingext')->getConfigData('general/password');
        $boxes=$this->getRequest()->getParam("boxes");

        $baseDir = Mage::getBaseDir('app').DS.'code/community/Sendabox/Shippingext/tmp/';

        $file=$this->getRequest()->getParam("orderid").'.xml';
        if(!is_writable($baseDir)){
            echo Mage::helper('sendabox_shippingext')->__('The tmp file is not writable. Please change folder permissions');
            return null;
        }

        $xmlLoad = new Varien_Simplexml_Config();
        $results=array();

        for($i=1;$i<=$boxes;$i++){
            $modified=false;
            if($xmlLoad->loadFile($baseDir.$file)){
                $xml_edit = simplexml_load_file($baseDir.$file);

                $departure_city=$xml_edit -> departure -> city;
                $departure_province=$xml_edit -> departure -> province;
                $departure_zip=$xml_edit -> departure -> zip;
                $destination_city=$xml_edit -> destination -> city;
                $destination_province=$xml_edit -> destination -> province;
                $destination_country=$xml_edit -> destination -> country;
                $destination_zip=$xml_edit -> destination -> zip;

                if(strpos($departure_city,$this->getRequest()->getParam("shipping_city"))!==0 ||
                    strpos($departure_province,$this->getRequest()->getParam("shipping_province"))!==0 ||
                    strpos($departure_zip,$this->getRequest()->getParam("shipping_zip"))!==0){
                    $city=dom_import_simplexml($xml_edit -> departure->city);
                    $city->parentNode->removeChild($city);
                    $province=dom_import_simplexml($xml_edit -> departure->province);
                    $province->parentNode->removeChild($province);
                    $zip=dom_import_simplexml($xml_edit -> departure->zip);
                    $zip->parentNode->removeChild($zip);
                    $departure=$xml_edit -> departure[0];
                    $departure->addChild("city", $this->getRequest()->getParam("shipping_city"));
                    $departure->addChild("province", $this->getRequest()->getParam("shipping_province"));
                    $departure->addChild("zip", $this->getRequest()->getParam("shipping_zip"));
                    $xml_edit->asXML($baseDir.$file);
                    $modified=true;
                }

                if(strpos($destination_city,$this->getRequest()->getParam("destination_city"))!==0 ||
                    strpos($destination_province,$this->getRequest()->getParam("destination_province"))!==0 ||
                    strpos($destination_zip,$this->getRequest()->getParam("destination_zip"))!==0 ||
                    strpos($destination_country,$this->getRequest()->getParam("destination_country"))!==0){
                    $city=dom_import_simplexml($xml_edit -> destination ->city);
                    $city->parentNode->removeChild($city);
                    $province=dom_import_simplexml($xml_edit -> destination->province);
                    $province->parentNode->removeChild($province);
                    $zip=dom_import_simplexml($xml_edit -> destination->zip);
                    $zip->parentNode->removeChild($zip);
                    $country=dom_import_simplexml($xml_edit -> destination->country);
                    $country->parentNode->removeChild($country);
                    $departure=$xml_edit -> destination[0];

                    $departure->addChild("city", $this->getRequest()->getParam("destination_city"));
                    $departure->addChild("province", $this->getRequest()->getParam("destination_province"));
                    $departure->addChild("zip", $this->getRequest()->getParam("destination_zip"));
                    $departure->addChild("country", $this->getRequest()->getParam("destination_country"));
                    $modified=true;

                }

                $box=$xml_edit -> xpath('//boxes/box[@id='.$i.']');
                if(count($box)>0){
                    $width=$box[0]->width["value"];
                    $height=$box[0]->height["value"];
                    $depth=$box[0]->depth["value"];
                    $weight=$box[0]->weight["value"];
                    if(strpos($width,$this->getRequest()->getParam("width-".$i))!==0 ||
                        strpos($height,$this->getRequest()->getParam("height-".$i))!==0 ||
                        strpos($depth,$this->getRequest()->getParam("depth-".$i))!==0 ||
                        strpos($weight,$this->getRequest()->getParam("weight-".$i))!==0){
                        $node=dom_import_simplexml($box[0]);
                        $node->parentNode->removeChild($node);
                        $newbox=$xml_edit->boxes->addChild('box','');
                        $newbox->addAttribute('id',$i);
                        $newwidth=$newbox->addChild('width','');
                        $newwidth->addAttribute('value',$this->getRequest()->getParam("width-".$i));
                        $newheight=$newbox->addChild('height','');
                        $newheight->addAttribute('value',$this->getRequest()->getParam("height-".$i));
                        $newdepth=$newbox->addChild('depth','');
                        $newdepth->addAttribute('value',$this->getRequest()->getParam("depth-".$i));
                        $newweight=$newbox->addChild('weight','');
                        $newweight->addAttribute('value',$this->getRequest()->getParam("weight-".$i));
                        $modified=true;
                    }

                }
                else{
                    $newbox=$xml_edit->boxes->addChild('box','');
                    $newbox->addAttribute('id',$i);
                    $newwidth=$newbox->addChild('width','');
                    $newwidth->addAttribute('value',$this->getRequest()->getParam("width-".$i));
                    $newheight=$newbox->addChild('height','');
                    $newheight->addAttribute('value',$this->getRequest()->getParam("height-".$i));
                    $newdepth=$newbox->addChild('depth','');
                    $newdepth->addAttribute('value',$this->getRequest()->getParam("depth-".$i));
                    $newweight=$newbox->addChild('weight','');
                    $newweight->addAttribute('value',$this->getRequest()->getParam("weight-".$i));
                    $modified=true;
                }


                $dom = dom_import_simplexml($xml_edit)->ownerDocument;
                $dom->formatOutput = true;
                $dom->save($baseDir.$file);

            }
            else{
                $xml_array=array(
                    'name' => 'shipment',
                    array(
                        'name' => 'departure',
                        array(
                            'name' => 'name',
                            'value' => $this->getRequest()->getParam("shipping_name"),
                        ),
                        array(
                            'name' => 'surname',
                            'value'=> $this->getRequest()->getParam("shipping_surname"),
                        ),
                        array(
                            'name' => 'co',
                            'value' => $this->getRequest()->getParam("shipping_co"),
                        ),
                        array(
                            'name' => 'address',
                            'value'=> $this->getRequest()->getParam("shipping_address"),
                        ),
                        array(
                            'name' => 'streetnumber',
                            'value' => $this->getRequest()->getParam("shipping_streetnumber"),
                        ),
                        array(
                            'name' => 'city',
                            'value'=> $this->getRequest()->getParam("shipping_city"),
                        ),
                        array(
                            'name' => 'province',
                            'value' => $this->getRequest()->getParam("shipping_province"),
                        ),
                        array(
                            'name' => 'zip',
                            'value'=> $this->getRequest()->getParam("shipping_zip"),
                        ),
                        array(
                            'name' => 'phone',
                            'value' => $this->getRequest()->getParam("shipping_tel"),
                        ),
                        array(
                            'name' => 'email',
                            'value'=> $this->getRequest()->getParam("shipping_email"),
                        ),
                    ),
                    array(
                        'name' => 'destination',
                        array(
                            'name' => 'name',
                            'value' => $this->getRequest()->getParam("destination_name"),
                        ),
                        array(
                            'name' => 'surname',
                            'value'=> $this->getRequest()->getParam("destination_surname"),
                        ),
                        array(
                            'name' => 'co',
                            'value' => $this->getRequest()->getParam("destination_co"),
                        ),
                        array(
                            'name' => 'address',
                            'value'=> $this->getRequest()->getParam("destination_address"),
                        ),
                        array(
                            'name' => 'streetnumber',
                            'value' => $this->getRequest()->getParam("destination_streetnumber"),
                        ),
                        array(
                            'name' => 'city',
                            'value'=> $this->getRequest()->getParam("destination_city"),
                        ),
                        array(
                            'name' => 'province',
                            'value' => $this->getRequest()->getParam("destination_province"),
                        ),
                        array(
                            'name' => 'country',
                            'value'=> $this->getRequest()->getParam("destination_country"),
                        ),
                        array(
                            'name' => 'zip',
                            'value'=> $this->getRequest()->getParam("destination_zip"),
                        ),
                        array(
                            'name' => 'phone',
                            'value' => $this->getRequest()->getParam("destination_tel"),
                        ),
                        array(
                            'name' => 'email',
                            'value'=> $this->getRequest()->getParam("destination_email"),
                        ),
                    ),
                    array(
                        'name' => 'boxes',
                        array(
                            'name' => 'box',
                            'attributes' => array(
                                'id' => $i,
                            ),
                            array(
                                'name' => 'width',
                                'attributes' => array(
                                    'value' => $this->getRequest()->getParam( "width-".$i),
                                ),
                            ),
                            array(
                                'name' => 'height',
                                'attributes' => array(
                                    'value' => $this->getRequest()->getParam( "height-".$i),
                                ),
                            ),
                            array(
                                'name' => 'depth',
                                'attributes' => array(
                                    'value' => $this->getRequest()->getParam( "depth-".$i),
                                ),
                            ),
                            array(
                                'name' => 'weight',
                                'attributes' => array(
                                    'value' => $this->getRequest()->getParam( "weight-".$i),
                                ),
                            ),
                        ),
                    ),
                );


                $doc = new DOMDocument();
                $child = $this->array_to_xml( $doc, $xml_array );
                if ( $child )
                    $doc->appendChild($child);
                $doc->formatOutput = true;
                $doc->save($baseDir.$file);
                $modified=true;
            }
            if(!$modified) continue;

            $jsonData = array(
                'apikey' => $username,
                'apisecret' => $password,
                'data'=>array(
                    'fromCity'=>$this->getRequest()->getParam("shipping_city"),
                    'fromProvince'=>$this->getRequest()->getParam("shipping_province"),
                    'fromZip'=>$this->getRequest()->getParam("shipping_zip"),
                    'toCity'=>$this->getRequest()->getParam("destination_city"),
                    'toProvince'=>$this->getRequest()->getParam("destination_province"),
                    'toZip'=>$this->getRequest()->getParam("destination_zip"),
                    'toCountry'=>$this->getRequest()->getParam("destination_country"),
                    'weight'=>$this->getRequest()->getParam("weight-".$i),
                    'width'=>$this->getRequest()->getParam("width-".$i),
                    'height'=>$this->getRequest()->getParam("height-".$i),
                    'depth'=>$this->getRequest()->getParam("depth-".$i)
                )
            );

            $url=$this->_url."GetOffersJson";

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
                echo json_encode(array("ErrorMsg"=>$result["ErrorMsg"]));
                return null;
            } else {
                $offers=array();
                    foreach($result as $offer) {
                        $available_services=array();
                        foreach($offer['ServiziAggiuntivi'] as $service){
                            $available_services[]=$service['Name']." ";
                        }
                        $of=array(
                            'idcarrier' => $offer['IdCarrier'],
                            'idshippingdetail' => $offer['IdShipping'],
                            'idpricelist' => $offer['IdPriceList'],
                            'carrier' => $offer['Info'],
                            'price' => str_replace(',','.',$offer['Price']),
                            'services' => $available_services,
                    );
                    array_push($offers,$of);
                }
                $results[$i]=array('offers'=>$offers);
            }

        }

        echo json_encode($results);

    }

    public function servicesAction(){
        $username = Mage::helper('sendabox_shippingext')->getConfigData('general/username');
        $password = Mage::helper('sendabox_shippingext')->getConfigData('general/password');
        $box=$this->getRequest()->getParam("b");
        $idcarrier=$this->getRequest()->getParam("i");
        $jsonData = array(
            'apikey' => $username,
            'apisecret' => $password,
            'data'=>array(
                'idcarrier'=>$idcarrier,
            )
        );

        $url=$this->_url."GetServiceJson";
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
            $table="<td colspan='6'><table>";
            $table.="<thead><tr class=\"headings\">
                        <th>".Mage::helper('sendabox_shippingext')->__('Choose service(s)')."</th>
                        <th><strong>".Mage::helper('sendabox_shippingext')->__('Service')."</strong></th>
                        <th><strong>".Mage::helper('sendabox_shippingext')->__('Value')."</strong></th>
                        <th><strong>".Mage::helper('sendabox_shippingext')->__('Price')."</strong></th>
                        <th style=\"background-color: #fff;border: 0;\"><button onclick='closePanel(".$box.",".$idcarrier.")' class=\"scalable\">".Mage::helper('sendabox_shippingext')->__('Close')."</button></th>
                    </tr></thead>";
            $servicecount=0;
            foreach($result as $offer){
                $table.="<tr>";
                $table.="<td><input type=\"checkbox\" name=\"box_".$box."_service_".$idcarrier."_".$servicecount."\" id=\"box_".$box."_service_".$idcarrier."_".$servicecount."\" onclick=\"servicecheck(".$box.",".$idcarrier.",".$servicecount.")\" value=".$offer['idadditionalservices']."></td>";
                $table.="<td>".$offer['servizio']."</td>";
                if(strpos($offer['servizio'],'Assicurata')===0){
                    $table.="<td><input type=\"text\" onkeypress=\"validateInput(this)\" onkeyup=\"updateInsurance(".$box.",".$idcarrier.",".$servicecount.")\" name=\"box_".$box."_service_ins_".$idcarrier."_".$servicecount."\" id=\"box_".$box."_service_ins_".$idcarrier."_".$servicecount."\"> From: ".$offer['prezzo']."€</td>";
                    $table.="<td><div id=\"box_".$box."_serviceprice_".$idcarrier."_".$servicecount++."\">0€</div></td>";
                }
                else if(strpos($offer['servizio'],'Contrassegno')===0){
                    $table.="<td><input type=\"text\" onkeypress=\"validateInput(this)\" onkeyup=\"updateContrassegno(".$box.",".$idcarrier.",".$servicecount.")\" name=\"box_".$box."_service_con_".$idcarrier."_".$servicecount."\" id=\"box_".$box."_service_con_".$idcarrier."_".$servicecount."\"> From: ".$offer['prezzo']."€</td>";
                    $table.="<td><div id=\"box_".$box."_serviceprice_".$idcarrier."_".$servicecount++."\">0€</div></td>";
                }else{
                    $table.="<td></td>";
                    $table.="<td><div id=\"box_".$box."_serviceprice_".$idcarrier."_".$servicecount++."\">".$offer['prezzo']."€</div></td>";
                }

                $table.="</tr>";
            }
            $table.="<input type=\"hidden\" name=\"box_".$box."_service_".$idcarrier."_count\" value=\"".$servicecount."\" ></table></td>";
            echo $table;
        }

    }

    public function orderAction(){
        $username = Mage::helper('sendabox_shippingext')->getConfigData('general/username');
        $password = Mage::helper('sendabox_shippingext')->getConfigData('general/password');
        $create_shipment = Mage::helper('sendabox_shippingext')->getConfigData('general/create_shipment');
        $boxes=$this->getRequest()->getParam("boxes");
        $boxes_array=array();

        for($i=1;$i<=$boxes;$i++){
            $carrier = $this->getRequest()->getParam("box_".$i);
            $selectedservices=array();
            $serviceavail=$this->getRequest()->getParam("box_".$i."_service_".$carrier."_count");

            if(!is_null($serviceavail)){
                for($j=0;$j<intval($serviceavail);$j++){
                    $service=$this->getRequest()->getParam("box_".$i."_service_".$carrier."_".$j);
                    if(!is_null($service)){
                        $assurance=$this->getRequest()->getParam("box_".$i."_service_ins_".$carrier."_".$j);
                        $contrasegno=$this->getRequest()->getParam("box_".$i."_service_con_".$carrier."_".$j);

                        if(!is_null($assurance)){
                            array_push($selectedservices,array( 'service' => $service, 'value' => $assurance ));
                            continue;
                        }
                        if(!is_null($contrasegno)){
                            array_push($selectedservices,array(  'service' => $service, 'value' => $contrasegno ));
                            continue;
                        }

                        array_push($selectedservices,array( 'service' => $service ));
                    }
                }
            }

            array_push($boxes_array,array(
                            'idpricelist' => $this->getRequest()->getParam("box_".$i."_carrier_".$carrier."_idpricelist"),
                            'idshippingdetail' => $this->getRequest()->getParam("box_".$i."_carrier_".$carrier."_idshippingdetail"),
                            'width' => $this->getRequest()->getParam("width-".$i),
                            'height' => $this->getRequest()->getParam("height-".$i),
                            'depth' => $this->getRequest()->getParam("depth-".$i),
                            'weight' => $this->getRequest()->getParam("weight-".$i),
                            'selected_services' => $selectedservices,
                            'box_price' =>  $this->getRequest()->getParam("box_".$i."_final_price"),
            ));

        }

        $jsonData = array(
            'apikey' => $username,
            'apisecret' => $password,
            'departure' => array(
                'name' => $this->getRequest()->getParam("shipping_name"),
                'surname' => $this->getRequest()->getParam("shipping_surname"),
                'co' => $this->getRequest()->getParam("shipping_co"),
                'address' => $this->getRequest()->getParam("shipping_address"),
                'streetnumber' => $this->getRequest()->getParam("shipping_streetnumber"),
                'city'=> $this->getRequest()->getParam("shipping_city"),
                'province'=> $this->getRequest()->getParam("shipping_province"),
                'zip'=> $this->getRequest()->getParam("shipping_zip"),
                'tel'=> $this->getRequest()->getParam("shipping_tel"),
                'email'=> $this->getRequest()->getParam("shipping_email"),
            ),
            'destination' => array(
                'name' => $this->getRequest()->getParam("destination_name"),
                'surname' => $this->getRequest()->getParam("destination_surname"),
                'co' => $this->getRequest()->getParam("destination_co"),
                'address' => $this->getRequest()->getParam("destination_address"),
                'streetnumber' => $this->getRequest()->getParam("destination_streetnumber"),
                'city'=> $this->getRequest()->getParam("destination_city"),
                'province'=> $this->getRequest()->getParam("destination_province"),
                'country'=> $this->getRequest()->getParam("destination_country"),
                'zip'=> $this->getRequest()->getParam("destination_zip"),
                'tel'=> $this->getRequest()->getParam("destination_tel"),
                'email'=> $this->getRequest()->getParam("destination_email"),
            ),
            'boxes' => $boxes_array,
            'final-price' => $this->getRequest()->getParam("final-price")
        );

        $url= $this->_url."MakeOrder";
        $jsonDataEncoded = json_encode($jsonData);

        $headers=array("Content-Type" => "application/json");

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
            echo json_encode(array("ErrorMsg"=>$result["ErrorMsg"]));
            return null;
        } else {
            if(isset($result["Message"]) && $result["Message"]){

                $data = array(
                    'order_id' => $this->getRequest()->getParam("orderid"),
                    'customer_cost' => $this->getRequest()->getParam("final-price"),
                    'shipping_name' => $this->getRequest()->getParam("shipping_name"),
                    'shipping_surname' => $this->getRequest()->getParam("shipping_surname"),
                    'shipping_at' => $this->getRequest()->getParam("shipping_co"),
                    'shipping_street' => $this->getRequest()->getParam("shipping_address"),
                    'shipping_number' => $this->getRequest()->getParam("shipping_streetnumber"),
                    'shipping_city'=> $this->getRequest()->getParam("shipping_city"). "(".$this->getRequest()->getParam("shipping_province").")",
                    'shipping_postcode'=> $this->getRequest()->getParam("shipping_zip"),
                    'shipping_phone'=> $this->getRequest()->getParam("shipping_tel"),
                    'shipping_email'=> $this->getRequest()->getParam("shipping_email"),
                    'destination_name' => $this->getRequest()->getParam("destination_name"),
                    'destination_surname' => $this->getRequest()->getParam("destination_surname"),
                    'destination_at' => $this->getRequest()->getParam("destination_co"),
                    'destination_street' => $this->getRequest()->getParam("destination_address"),
                    'destination_number' => $this->getRequest()->getParam("destination_streetnumber"),
                    'destination_city'=> $this->getRequest()->getParam("destination_city")." (".$this->getRequest()->getParam("destination_province").") " ,
                    'destination_postcode'=> $this->getRequest()->getParam("destination_zip"),
                    'destination_phone'=> $this->getRequest()->getParam("destination_tel"),
                    'destination_email'=> $this->getRequest()->getParam("destination_email"),
                );

                $model = Mage::getModel('sendabox_shippingext/shipment')->setData($data);
                $insertId = $model->save()->getId();
                $i=1;

                $_order = Mage::getModel('sales/order')->loadByIncrementId($this->getRequest()->getParam("orderid"));

                $shipment = Mage::getModel('sales/service_order', $_order)
                        ->prepareShipment($this->_getItemQtys($_order));

                $trackings="";
                foreach( $result["Orders"] as $order){

                    $data = array(
                        'shipment_id' => $insertId,
                        'sendabox_order_id' => $order["IdUserCityOrder"] ,
                        'sendabox_order_id_encoded' => $order["Encoded"] ,
                        'carrier' => $order["Carrier"],
                        'Comment' => "Box-".$i,
                        'value' => $this->getRequest()->getParam("box_".$i."_final_price") ,
                        'width' => $this->getRequest()->getParam("width-".$i),
                        'height' => $this->getRequest()->getParam("height-".$i),
                        'depth' => $this->getRequest()->getParam("depth-".$i),
                        'weight' => $this->getRequest()->getParam("weight-".$i),
                    );
                    $model = Mage::getModel('sendabox_shippingext/box')->setData($data);
                    $model->save();

                    $arrTracking = array(
                        'carrier_code' => 'custom',
                        'title' =>'Sendabox',
                        'number' => $order["IdUserCityOrder"],
                    );

                    $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
                    $shipment->addTrack($track);
                    $trackings.='http://sendabox.it/Trackings/Index/'.$order["Encoded"]." ";
                    $i++;
                }
                $shipment -> addComment($trackings);
                if(strpos($create_shipment,"1")===0){
                    $shipment->register();
                    $this->_saveShipment($shipment, $_order, "");
                }

                echo json_encode( array( 'id' => $insertId )) ;
            }
        }
    }

    public function agendaAction(){
        $username = Mage::helper('sendabox_shippingext')->getConfigData('general/username');
        $password = Mage::helper('sendabox_shippingext')->getConfigData('general/password');

        $jsonData = array(
            'apikey' => $username,
            'apisecret' => $password
        );

        $url=$this->_url."GetAgenda";
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

            Mage::getResourceModel('sendabox_shippingext/agenda')->truncate();
            foreach($result as $sender){
                $data = array(
                    'name' => $sender["Nome"],
                    'surname' => $sender["Cognome"],
                    'address' => $sender["Indirizzo"],
                    'street_number' => $sender["Civico"],
                    'city' => $sender["Citta"],
                    'province' => $sender["Provincia"],
                    'co'=> $sender["presso"],
                    'zip'=> $sender["cap"],
                    'mail'=> $sender["mail"],
                    'tel'=> $sender["tel"],
                );

                $model = Mage::getModel('sendabox_shippingext/agenda')->setData($data);
                $model->save();

            }
            echo "Contacts inserted successfully";
        }
    }

    public function populateAction(){

        $id = $this->getRequest()->getParam("shipping_agenda");
        $d = Mage::getModel('sendabox_shippingext/agenda')->load($id);

        $data = array(
            'name' => $d->getData('name'),
            'surname' => $d->getData('surname'),
            'co' => $d->getData('co'),
            'address' => $d->getData('address'),
            'street_number' => $d->getData('street_number'),
            'city' => $d->getData('city'),
            'province' => $d->getData('province'),
            'zip' => $d->getData('zip'),
            'tel' => $d->getData('tel'),
            'email' => $d->getData('mail'),
            );
        echo json_encode($data);
    }

    protected function _getItemQtys(Mage_Sales_Model_Order $order)
    {
        $qty = array();

        foreach ($order->getAllItems() as $_eachItem) {
            if ($_eachItem->getParentItemId()) {
                $qty[$_eachItem->getParentItemId()] = $_eachItem->getQtyOrdered();
            } else {
                $qty[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
            }
        }

        return $qty;
    }

    protected function _saveShipment(Mage_Sales_Model_Order_Shipment $shipment, Mage_Sales_Model_Order $order, $customerEmailComments = '')
    {
        $shipment->getOrder()->setIsInProcess(true);
        Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($order)
            ->save();

        $emailSentStatus = $shipment->getData('email_sent');
        if (!is_null($order->getShippingAddress()->getEmail()) && !$emailSentStatus) {
            $shipment->sendEmail(true, $customerEmailComments);
            $shipment->setEmailSent(true);
        }

        return $this;
    }

    function array_to_xml($dom, &$data) {
        if ( empty( $data['name'] ) )
            return false;

        // Create the element
        $element_value = ( ! empty( $data['value'] ) ) ? $data['value'] : null;
        $element = $dom->createElement( $data['name'], $element_value );

        // Add any attributes
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
                $element->setAttribute( $attribute_key, $attribute_value );
            }
        }

        // Any other items in the data array should be child elements
        foreach ( $data as $data_key => $child_data ) {
            if ( ! is_numeric( $data_key ) )
                continue;

            $child = $this->array_to_xml( $dom, $child_data );
            if ( $child )
                $element->appendChild( $child );
        }

        return $element;
    }

}