// JavaScript Document


$(document).ready(function(){
	template_addListener_busquedas(inmueble_fcnBuscar);
	
	
	tempCenter = new google.maps.LatLng($("#inmueble_mapa").attr("data-latitud"), $("#inmueble_mapa").attr("data-longitud"));
	//define el google maps
	var mapaGoogle = document.getElementById("inmueble_mapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 16,
		mapMaker: true,
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	var map = new google.maps.Map(mapaGoogle, mapOptions);
	
	marker = new google.maps.Marker({
		position: tempCenter,
		map: map,
		icon: "images/marcador.png"
	});
	
	inmueble_galeria();
	
	if ($("#inmueble_exitoNuevoAnuncio").length == 1)
		inmueble_exitoNuevoAnuncio();
		
		
	$(".contenedorInfo td.descripcionBotones a.otrosBotones[data-label]").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				$("html,body").animate({
					scrollTop: $("."+elemento.attr("data-label")).offset().top
				}, 2000);
			}
		});
	});
});


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function inmueble_cerrarPopup() {
	$("#inmueble_exitoNuevoAnuncio").hide();
}


/*
	Muestra un popup con una mensaje del exito de la creacion del inmueble
*/
function inmueble_exitoNuevoAnuncio() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#inmueble_exitoNuevoAnuncio");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
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


/*
	funcion a realizar cuando se da click en el boton de buscar
*/
function inmueble_fcnBuscar() {
	var datos = template_busquedas_getData();
	var params = "";
	
	for (var key in datos) {
		if (params == "")
			params += "?"
		else
			params += "&";
			
		params += key+"="+datos[key];
	}
	
	gotoURL("catalogo.php"+params);
}


/*
	Valida los campos para el envio del reporte
*/
function inmueble_validarReporte() {
	if (!vacio(($("#reportar_motivo p").attr("data-value") == -1 ? "" : $("#reportar_motivo p").attr("data-value")), "Motivo")) {
		inmueble_saveReporte();
	}
}


/*
	Guarda un reporte
*/
function inmueble_saveReporte() {
	$.ajax({
		url: "lib_php/updReporte.php",
		type: "POST",
		dataType: "json",
		data: {
			inmueble: $("table.reportarAnuncio span.btnEnviar").attr("data-inmueble"),
			razonReporte: $("#reportar_motivo p").attr("data-value"),
			comentarios: $("#reportar_comentarios").val()
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			alert("Tu reporte ha sido enviado.");
			$("#reportar_motivo p").attr("data-value", -1);
			$("#reportar_motivo p").text("");
			$("#reportar_comentarios").val("");
		}
	});
}
/**/