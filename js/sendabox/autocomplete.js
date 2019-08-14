window.onload = function() {var origin = document.getElementById('sendabox_options_origin_city');
	if(origin!=null)
		new Ajax.Autocompleter('sendabox_options_origin_city','autocompleter_city_origin','/CityOrigin.php', {paramName: 'destination_city',minChars: 3, afterUpdateElement : getSelectionIdOrigin});
}
function getSelectionIdOrigin(text, li) {
			var city_id = document.getElementById("sendabox_options_origin_city_id").value = li.id;
		}