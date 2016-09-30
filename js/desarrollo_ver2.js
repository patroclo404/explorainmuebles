// JavaScript Document


$(document).ready(function(){
	template_addListener_busquedas(desarrollo_fcnBuscar);
	
	
	tempCenter = new google.maps.LatLng($("#desarrollo_mapa").attr("data-latitud"), $("#desarrollo_mapa").attr("data-longitud"));
	//define el google maps
	var mapaGoogle = document.getElementById("desarrollo_mapa");
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
	
	//carga los estilos y eventos de la galeria del inmueble
	desarrollo_galeria();
	
	//evento para hacer el scroll con los elementos de los botones laterales derechos
	$(".contenedorInfo td.descripcionBotones a[data-label]").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				$("html,body").animate({
					scrollTop: $("."+elemento.attr("data-label")).offset().top
				}, 2000);
			}
		});
	});
	
	
	//evento para mostrar en un popup la imagen principal en grande
	$("table.contenedorInfo td.imagen img.imagenPrincipal").on({
		click: function() {
			desarrollo_mostrarImagen($(this).attr("data-pos"));
		}
	});
	
	//eventos para las flechas dentro del popup de las imagenes en grande
	$("#desarrollo_mostrarImagen a.flechas").each(function(index){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				desarrollo_mostrarImagen_desplazamiento(index);
			}
		});
	});
});


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function desarrollo_cerrarPopup() {
	$("#desarrollo_mostrarImagen").hide();
	$("#desarrollo_reportarAnuncio").hide();
	$("#desarrollo_botonesCompartir").hide();
	$(window).unbind("keyup");
}


/*
	Muestra un popup con la imagen en grande de la galeria de imagenes (imagen central)
	
		* posicion:	Integer, muestra la imagen que se envia por posicion y actualiza el parametro
*/
function desarrollo_mostrarImagen(posicion) {
	$("#template_mascaraPrincipal").show();
	var objDiv = $("#desarrollo_mostrarImagen");
	
	objDiv.find("img").prop("src", $("table.contenedorInfo .desplazamiento .bloque").eq(posicion).find("img").prop("src"));
	objDiv.find("img").attr("data-pos", posicion);
	
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	if ($("table.contenedorInfo .desplazamiento .bloque").length == 1)
		objDiv.find("a.flechas").hide();
	else {//activa eventos del teclado para cambiar la galeria por medio de las flechas
		$(window).on({
			keyup: function(evt) {
				var unicode = evt.keyCode;
				evt.stopPropagation();
				evt.preventDefault();
				evt.stopImmediatePropagation();
				
				if (unicode == 37)//prev
					desarrollo_mostrarImagen_desplazamiento(0);
				if (unicode == 39)//next
					desarrollo_mostrarImagen_desplazamiento(1);
				return false;
			}
		});
	}
}


/*
	Muestra e inicializa los campos para mostrar lo de reportar un anuncio
*/
function desarrollo_reportarAnuncio() {
	var objDiv = $("#desarrollo_reportarAnuncio");
	$("#reportar_motivo p").attr("data-value", "-1");
	$("#reportar_motivo p").text("");
	$("#reportar_motivo li.lista").hide();
	objDiv.show();
}


/*
	Muestra los botones de compartir
*/
function desarrollo_botonesCompartir() {
	var objDiv = $("#desarrollo_botonesCompartir");
	objDiv.show();
}


/*
	Realiza los ajustes de la galeria para inmuebles
*/
function desarrollo_galeria() {
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
					desarrollo_galeria_desplazamiento(index);
				}
			});
		});
	}
	else {
		galeria.find(".contenedorFlechas").hide();
		galeria.find(".contenedorDesplazamiento").css("width", "100%");
	}
	
	desplazamiento.find(".bloque").each(function(index){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				$("img.imagenPrincipal").prop("src", elemento.find("img").prop("src"));
				$("img.imagenPrincipal").attr("data-pos", index);
			}
		});
	});
}


/*
	Realiza el desplazamiento de la galeria segun su posicion:
	
		* posicion:	Integer, 0 a la izquierda, 1 a la derecha
*/
function desarrollo_galeria_desplazamiento(posicion) {
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
	Cambia de imagen dentro del popup y ajusta la posicion del popup para mostrarla en el centro
	
		* posicion:	Integer, 0 a la izquierda, 1 a la derecha
*/
function desarrollo_mostrarImagen_desplazamiento(posicion) {
	var padre = $("table.contenedorInfo .desplazamiento");
	var hijos = padre.find(".bloque").length;
	var posActual = parseInt($("#desarrollo_mostrarImagen img").attr("data-pos"));
	var posNext = posActual;
	
	if (posicion == 0)//anterior
		posNext = (posNext - 1) == -1 ? (hijos - 1) : (posNext - 1);
	else//siguiente
		posNext = (posNext + 1) == hijos ? 0 : (posNext + 1);
		
	desarrollo_mostrarImagen(posNext);
}


/*
	funcion a realizar cuando se da click en el boton de buscar
*/
function desarrollo_fcnBuscar() {
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
function desarrollo_validarReporte() {
	if (!vacio(($("#reportar_motivo p").attr("data-value") == -1 ? "" : $("#reportar_motivo p").attr("data-value")), "Motivo")) {
		desarrollo_saveReporte();
	}
}


/*
	Valida los campos para enviar informacion al vendedor
*/
function desarrollo_validarContacto() {
	if (!vacio($("#desarrollo_nombre").val(), $("#desarrollo_nombre").attr("placeholder"))) {
		if (!vacio($("#desarrollo_email").val(), $("#desarrollo_email").attr("placeholder"))) {
			if (correoValido($("#desarrollo_email").val())) {
				if (!vacio($("#desarrollo_mensaje").val(), $("#desarrollo_mensaje").attr("placeholder"))) {
					$.ajax({
						url: "lib_php/emailDesarrollo.php",
						type: "POST",
						dataType: "text",
						data: {
							nombre: $("#desarrollo_nombre").val(),
							email: $("#desarrollo_email").val(),
							telefono: $("#desarrollo_telefono").val(),
							mensaje: $("#desarrollo_mensaje").val(),
							desarrollo: $("table.desarrollo_contacto span.btnEnviar").attr("data-desarrollo")
						}
					}).always(function(){
						alert("Hemos enviado un mensaje con tus datos a la Inmobiliaria del Desarrollo.");
						$("#desarrollo_nombre").val("");
						$("#desarrollo_email").val("");
						$("#desarrollo_telefono").val("");
						$("#desarrollo_mensaje").val("");
					});
				}
			}
		}
	}
}


/*
	Guarda un reporte
*/
function desarrollo_saveReporte() {
	$.ajax({
		url: "lib_php/updReporte.php",
		type: "POST",
		dataType: "json",
		data: {
			inmueble: -1,
			razonReporte: $("#reportar_motivo p").attr("data-value"),
			comentarios: "",
			desarrollo: $("#desarrollo_reportarAnuncio span.btnEnviar").attr("data-desarrollo")
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			$("#reportar_motivo p").attr("data-value", -1);
			$("#reportar_motivo p").text("");
			$("#desarrollo_reportarAnuncio").hide();
			$("#template_alertPersonalizado td").text("Tu reporte ha sido enviado.");
			template_alertPersonalizado();
		}
		else {
			$("#template_alertPersonalizado td").text(respuesta_json.mensaje);
			template_alertPersonalizado();
		}
	});
}
/**/