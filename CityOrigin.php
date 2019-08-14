<?php
$destination_city = $_REQUEST['destination_city'];
if(isset($destination_city)){
	$destination_city = str_replace(" ", "%20", $destination_city);
	$url = 'http://www.sendabox.it/_magento/get/city?q=' .$destination_city;
	$html = file_get_contents($url);
	if ($html) {
		   $chiamata = json_decode($html, true);
		   if(!is_array($chiamata)) { $chiamata = array($chiamata); }
		   if(isset($chiamata['error']) && $chiamata['error']){
				   //echo $chiamata['msg'];
				   return;
		   } 
		   else { 
					if(count($chiamata)==0) return;
					echo "<ul>" ;
					foreach($chiamata as $suggest){ 
						$city = explode('-',$suggest['label'])[0];
						$id= $suggest['id'];
						echo "<li id='$id'>$city</li>";
					}
					echo "</ul>" ;
		   }
	} else {
		   //echo 'ERRORE CHIAMATA';
		   return;
	}
}
?> 