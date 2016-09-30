// JavaScript Document


$(document).ready(function(){
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#inmueble_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				inmueble_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	//agrega el evento para realizar la busqueda
	$(".inmueble_cuerpo .columna1 .titulo").on({
		click: function() {
			var data = {
				transaccion: $("#inmueble_transaccion").val(),
				tipoInmueble: $("#inmueble_tipoInmueble").find("p").attr("data-value"),
				estado: $("#inmueble_estado").find("p").attr("data-value"),
				ciudad: $("#inmueble_municipio").find("p").attr("data-value"),
				colonia: $("#inmueble_colonia").find("p").attr("data-value"),
				codigo: $("#inmueble_codigo").val(),
				precios: $("#inmueble_precios").find("p").attr("data-value")
			}
				
			gotoURLPOST("catalogo.php", data);
		}
	});
});


/*
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
*/
function inmueble_actualizar_ciudad(nomElemento) {
	var indexEstado = -1;
	var arrayEstados = Array("inmueble_estado");
	var arrayCiudades = Array("inmueble_municipio");
	var arrayColonias = Array("inmueble_colonia");
	
	indexEstado = arrayEstados.indexOf(nomElemento);
	
	if (indexEstado > -1) {
		objEstado = $("#"+nomElemento);
		objMunicipio = $("#"+arrayCiudades[indexEstado]);
		objColonia = $("#"+arrayColonias[indexEstado]);
		
		
		objMunicipio.find("li.lista ul").html("");
		objMunicipio.find("p").attr("data-value", -1);
		objMunicipio.find("p").text("");
		objColonia.find("li.lista ul").html("");
		objColonia.find("p").attr("data-value", -1);
		objColonia.find("p").text("");
		
		
		$.ajax({
			url: "admin/lib_php/consDireccion.php",
			type: "POST",
			dataType: "json",
			data: {
				consCiudad: 1,
				estado: objEstado.find("p").attr("data-value")
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					objMunicipio.find("li.lista ul").append("<li data-value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</li>");
				}
				
				objMunicipio.find("li.lista li").on({
					click: function() {
						objMunicipio.find("p").attr("data-value", $(this).attr("data-value"));
						objMunicipio.find("p").text($(this).text());
						inmueble_actualizar_colonia(objMunicipio.prop("id"));
					}
				});
			}
		});
	}
}


/*
	Actualiza las colonias para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
*/
function inmueble_actualizar_colonia(nomElemento, idColonia) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("inmueble_municipio");
	var arrayColonias = Array("inmueble_colonia");
	
	indexMunicipio = arrayCiudades.indexOf(nomElemento);
	
	if (indexMunicipio > -1) {
		objMunicipio = $("#"+arrayCiudades[indexMunicipio]);
		objColonia = $("#"+arrayColonias[indexMunicipio]);
		
		objColonia.find("li.lista ul").html("");
		objColonia.find("p").attr("data-value", -1);
		objColonia.find("p").text("");
		
		
		$.ajax({
			url: "admin/lib_php/consDireccion.php",
			type: "POST",
			dataType: "json",
			data: {
				consColonia: 1,
				ciudad: objMunicipio.find("p").attr("data-value")
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					objColonia.find("li.lista ul").append("<li data-value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</li>");
				}
				
				objColonia.find("li.lista li").on({
					click: function() {
						objColonia.find("p").attr("data-value", $(this).attr("data-value"));
						objColonia.find("p").text($(this).text());
					}
				});
			}
		});
	}
}
/**/