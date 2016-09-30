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
	
	//carga los estilos y eventos de la galeria del inmueble
	inmueble_galeria();
	
	//muestra un popup del exito de la creacion de un nuevo anuncio
	if ($("#inmueble_exitoNuevoAnuncio").length == 1)
		inmueble_exitoNuevoAnuncio();
		
	
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
			inmueble_mostrarImagen($(this).attr("data-pos"));
		}
	});
	
	//eventos para las flechas dentro del popup de las imagenes en grande
	$("#inmueble_mostrarImagen a.flechas").each(function(index){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				inmueble_mostrarImagen_desplazamiento(index);
			}
		});
	});
});


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function inmueble_cerrarPopup() {
	$("#inmueble_exitoNuevoAnuncio").hide();
	$("#inmueble_mostrarImagen").hide();
	$("#inmueble_reportarAnuncio").hide();
	$("#inmueble_botonesCompartir").hide();
	$(window).unbind("keyup");
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
	Muestra un popup con la imagen en grande de la galeria de imagenes (imagen central)
	
		* posicion:	Integer, muestra la imagen que se envia por posicion y actualiza el parametro
*/
function inmueble_mostrarImagen(posicion) {
	$("#template_mascaraPrincipal").show();
	var objDiv = $("#inmueble_mostrarImagen");
	
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
					inmueble_mostrarImagen_desplazamiento(0);
				if (unicode == 39)//next
					inmueble_mostrarImagen_desplazamiento(1);
				return false;
			}
		});
	}
}


/*
	Muestra e inicializa los campos para mostrar lo de reportar un anuncio
*/
function inmueble_reportarAnuncio() {
	var objDiv = $("#inmueble_reportarAnuncio");
	$("#reportar_motivo p").attr("data-value", "-1");
	$("#reportar_motivo p").text("");
	$("#reportar_motivo li.lista").hide();
	objDiv.show();
}


/*
	Muestra los botones de compartir
*/
function inmueble_botonesCompartir() {
	var objDiv = $("#inmueble_botonesCompartir");
	objDiv.show();
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
	Cambia de imagen dentro del popup y ajusta la posicion del popup para mostrarla en el centro
	
		* posicion:	Integer, 0 a la izquierda, 1 a la derecha
*/
function inmueble_mostrarImagen_desplazamiento(posicion) {
	var padre = $("table.contenedorInfo .desplazamiento");
	var hijos = padre.find(".bloque").length;
	var posActual = parseInt($("#inmueble_mostrarImagen img").attr("data-pos"));
	var posNext = posActual;
	
	if (posicion == 0)//anterior
		posNext = (posNext - 1) == -1 ? (hijos - 1) : (posNext - 1);
	else//siguiente
		posNext = (posNext + 1) == hijos ? 0 : (posNext + 1);
		
	inmueble_mostrarImagen(posNext);
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
	Valida los campos para enviar informacion al vendedor
*/
function inmueble_validarContacto() {
	if (!vacio($("#contacto_nombre").val(), $("#contacto_nombre").attr("placeholder"))) {
		if (!vacio($("#contacto_email").val(), $("#contacto_email").attr("placeholder"))) {
			if (correoValido($("#contacto_email").val())) {
				if (!vacio($("#contacto_comentarios").val(), $("#contacto_comentarios").attr("placeholder"))) {
					$.ajax({
						url: "lib_php/emailVendedor.php",
						type: "POST",
						dataType: "text",
						data: {
							nombre: $("#contacto_nombre").val(),
							email: $("#contacto_email").val(),
							telefono: $("#contacto_telefono").val(),
							comentarios: $("#contacto_comentarios").val(),
							inmueble: $("table.inmueble_contacto span.btnEnviar").attr("data-inmueble")
						}
					}).always(function(){
						alert("Hemos enviado un mensaje con tus datos al anunciante.");
						$("#contacto_nombre").val("");
						$("#contacto_email").val("");
						$("#contacto_telefono").val("");
						$("#contacto_comentarios").val("");
					});
				}
			}
		}
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
			inmueble: $("#inmueble_reportarAnuncio span.btnEnviar").attr("data-inmueble"),
			razonReporte: $("#reportar_motivo p").attr("data-value"),
			comentarios: "",
			desarrollo: -1
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			$("#reportar_motivo p").attr("data-value", -1);
			$("#reportar_motivo p").text("");
			$("#inmueble_reportarAnuncio").hide();
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