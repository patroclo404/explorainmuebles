// JavaScript Document


$(document).ready(function(){
	template_addListener_busquedas(usuario_fcnBuscar);
});


/*
	funcion a realizar cuando se da click en el boton de buscar
*/
function usuario_fcnBuscar() {
	var datos = template_busquedas_getData();
	var datos2 = datos;
	var params = "";
	
	for (var key in datos) {
		if ((key == "transaccion") || (key == "tipoInmueble") || (key == "estado") || (key == "ciudad")) {
			if (params == "")
				params += "?"
			else
				params += "&";
				
			params += key+"="+datos[key];
		}
	}
	
	$.ajax({
		url: "lib_php/updFiltros.php",
		type: "POST",
		dataType: "json",
		data: {
			set: 1,
			parametros: datos2
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1)
			gotoURL("catalogo.php"+params);
	});
	//gotoURL("catalogo.php"+params);
}