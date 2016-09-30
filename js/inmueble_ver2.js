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
				preciosMin: $("#inmueble_precios_min").val().replace(/\$/g, "").replace(/,/g, ""),
				preciosMax: $("#inmueble_precios_max").val().replace(/\$/g, "").replace(/,/g, "")
			}
				
			gotoURLPOST("catalogo.php", data);
		}
	});
	
	
	//onchange de precios
	$("#inmueble_precios_min,#inmueble_precios_max").on({
		change: function() {
			if ($(this).val() != "") {
				valor = $(this).val();
				valor = valor.replace(/\$/g, "");
				valor = valor.replace(/,/g, "");
				
				if (!flotante(valor, $(this).attr("placeholder"))) {
					$(this).val("");
				}
			}
		}
	});
	
	
	tempCenter = new google.maps.LatLng($("#inmueble_mapa").attr("data-latitud"), $("#inmueble_mapa").attr("data-longitud"));
	//define el google maps
	var mapaGoogle = document.getElementById("inmueble_mapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 16,
		mapMaker: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	var map = new google.maps.Map(mapaGoogle, mapOptions);
	
	marker = new google.maps.Marker({
		position: tempCenter,
		map: map,
		icon: "images/marcador.png"
	});
	
	inmueble_galeria();
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


/*
	Realiza los ajustes de la galeria para inmuebles
*/
function inmueble_galeria() {
	var galeria = $(".galeria");
	var desplazamiento = galeria.find(".desplazamiento");
	var hijos = desplazamiento.find(".bloque").length;
	
	if (hijos > 5) {
		var wBloque = desplazamiento.find(".bloque").eq(0).width() + (parseInt(desplazamiento.find(".bloque").eq(0).css("margin-left")) * 2);
		desplazamiento.css("width", (wBloque * hijos)+"px");
		desplazamiento.attr("data-pos", "0");
		
		
		galeria.find(".contenedorFlechas a.flechas").each(function(index){
			$(this).on({
				click: function() {
					inmueble_galeria_desplazamiento(index);
				}
			});
		});
	}
	else {
		galeria.find(".contenedorFlechas").hide();
		galeria.find(".contenedorDesplazamiento").css("width", "100%");
	}
	
	desplazamiento.find(".bloque").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				$("img.imagenPrincipal").prop("src", elemento.find("img").prop("src"));
			}
		});
	});
}


/*
	Realiza el desplazamiento de la galeria segun su posicion:
	
		* posicion:	Integer, 0 a la izquierda, 1 a la derecha
*/
function inmueble_galeria_desplazamiento(posicion) {
	var galeria = $(".galeria");
	var desplazamiento = galeria.find(".desplazamiento");
	var hijos = desplazamiento.find(".bloque").length;
	var ancho = galeria.find(".contenedorDesplazamiento").width();
	var posMaximo = Math.ceil(desplazamiento.width() / ancho);
	var nextPos = parseInt(desplazamiento.attr("data-pos"));
	
	if (parseInt(posicion) == 0) {//izquierda
		if (nextPos > 0) {
			nextPos--;
			desplazamiento.attr("data-pos", nextPos);
			
			desplazamiento.stop().animate({
				left: "-"+(nextPos * ancho)+"px"
			}, 500);
		}
	}
	else {//derecha
		if (nextPos < (posMaximo - 1)) {
			nextPos++;
			desplazamiento.attr("data-pos", nextPos);
			
			desplazamiento.stop().animate({
				left: "-"+(nextPos * ancho)+"px"
			}, 500);
		}
	}
}
/**/