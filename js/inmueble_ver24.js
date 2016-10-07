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
		icon: "images/logoIcon.png"
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
	$("img.imagenPrincipal").on({
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
	
	//evento en la etiqueta de favoritos
	/*
	$(" .otrosBotones.estrellaTexto").on({
		click: function(e) {
			e.stopPropagation();
			if (parseInt($(".btnBotones.estrella").attr("data-id")) != -1) {
				if ($(".btnBotones.estrella").hasClass("activo"))
					$(".descripcionBotones .otrosBotones .estrellaTexto").text("Agregar a Favoritos");
				else
					$(".descripcionBotones .otrosBotones .estrellaTexto").text("Quitar de Favoritos");
			}
			
			$(".btnBotones.estrella").first().click();
			return false;
		}
	});*/
	
	//evento tambien en el icono de la estrella para el texto
	$(".btnBotones.estrella").on({
		click: function(e) {

			if (parseInt($(".btnBotones.estrella").attr("data-id")) != -1) {


				if ($(this).find("i").hasClass("fa-heart-o") || $(this).find("i").hasClass("fa-star-o")) {

					$(".btnBotones.estrella").each(function(){
						if ($(this).find("i").hasClass("fa-heart-o")){
							$(this).find("i").addClass("fa-heart");
							$(this).find("i").removeClass("fa-heart-o");
						}

						if ($(this).find("i").hasClass("fa-star-o")){
							$(this).find("i").addClass("fa-star");
							$(this).find("i").removeClass("fa-star-o");
						}

						$(" .btnBotones .estrellaTexto").text("Quitar de Favoritos");
					});

				}else {
					$(".btnBotones.estrella").each(function(){
						if ($(this).find("i").hasClass("fa-heart")){
							$(this).find("i").addClass("fa-heart-o");
							$(this).find("i").removeClass("fa-heart");
						}

						if ($(this).find("i").hasClass("fa-star")){
							$(this).find("i").addClass("fa-star-o");
							$(this).find("i").removeClass("fa-star");
						}

						$(" .btnBotones .estrellaTexto").text("Agregar a Favoritos");
					});

				}
			}


		}
	});

	$('div.gallery-item').on('click', function(e){
		$head = $(document).find('.head-img img');
		$source = $(this).find('img');

		//$headSrc = $head.attr('src');
		$head.attr('src', $source.attr('src'));
		//$source.attr('src', $headSrc);
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
	$("#inmueble_mascara2").hide();
	$("#inmueble_compartir_email").hide();
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
	$("#inmueble_mascara2").show();
	var objDiv = $("#inmueble_mostrarImagen");
	console.log(posicion, objDiv);
	console.log($("div.contenedorDesplazamiento .desplazamiento .bloque"));

	objDiv.find("img").prop("src", $(".contenedorDesplazamiento .desplazamiento .bloque").eq(posicion).find("img").prop("src"));
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
	console.log("AQUI");
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
	Muestra un popup con datos para compartir por email
*/
function inmueble_compartir_email() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#inmueble_compartir_email");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	objDiv.find("input").val("");
	objDiv.find("textarea").val("");
	$("#inmueble_compartir_tuNombre").val($("#inmueble_compartir_tuNombre").attr("data-value"));
	$("#inmueble_compartir_tuEmail").val($("#inmueble_compartir_tuEmail").attr("data-value"));
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
	var padre = $(".contenedorDesplazamiento .desplazamiento");
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
	
	
	params2 =
		(datos2["transaccion"] == 1 ? "renta" : (datos2["transaccion"] == 2 ? "venta" : "renta-vacacional"))+
		"/"+($("#template_busqueda_tipoInmueble p").text() != "" ? ($("#template_busqueda_tipoInmueble p").text().toLowerCase()+($("#template_busqueda_tipoInmueble p").attr("data-value") != 6 ? "s" : "es")) : "todos-los-tipos")+
		"/"+($("#template_busqueda_estado p").text() != "" ? $("#template_busqueda_estado p").text() : "todo-mexico")+
		"/"+($("#template_busqueda_municipio p").text() != "" ? $("#template_busqueda_municipio p").text() : "todas-las-ciudades");
		
	
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
			gotoURL(""+params2);
	});
	//gotoURL("catalogo.php"+params);
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
				if (!vacio($("#contacto_mensaje").val(), $("#contacto_mensaje").attr("placeholder"))) {
					$.ajax({
						url: "lib_php/emailVendedor.php",
						type: "POST",
						dataType: "text",
						data: {
							nombre: $("#contacto_nombre").val(),
							email: $("#contacto_email").val(),
							telefono: $("#contacto_telefono").val(),
							mensaje: $("#contacto_mensaje").val(),
							inmueble: $("table.inmueble_contacto span.btnEnviar").attr("data-inmueble")
						}
					}).always(function(){
						$("#template_alertPersonalizado td").text("Hemos enviado un mensaje con tus datos al anunciante.");
						template_alertPersonalizado();
						$("#contacto_nombre").val("");
						$("#contacto_email").val("");
						$("#contacto_telefono").val("");
						$("#contacto_mensaje").val("");
					});
				}
			}
		}
	}
}

function inmueble_validarContacto_2() {
	if (!vacio($("#contacto_nombre_2").val(), $("#contacto_nombre_2").attr("placeholder"))) {
		if (!vacio($("#contacto_email_2").val(), $("#contacto_email_2").attr("placeholder"))) {
			if (correoValido($("#contacto_email_2").val())) {
				if (!vacio($("#contacto_mensaje_2").val(), $("#contacto_mensaje_2").attr("placeholder"))) {
					$.ajax({
						url: "lib_php/emailVendedor.php",
						type: "POST",
						dataType: "text",
						data: {
							nombre: $("#contacto_nombre_2").val(),
							email: $("#contacto_email_2").val(),
							telefono: $("#contacto_telefono_2").val(),
							mensaje: $("#contacto_mensaje_2").val(),
							inmueble: $("table.inmueble_contacto span.btnEnviar").attr("data-inmueble")
						}
					}).always(function(){
						$("#template_alertPersonalizado td").text("Hemos enviado un mensaje con tus datos al anunciante.");
						template_alertPersonalizado();
						$("#contacto_nombre_2").val("");
						$("#contacto_email_2").val("");
						$("#contacto_telefono_2").val("");
						$("#contacto_mensaje_2").val("");
					});
				}
			}
		}
	}
}


/*
	Valida que los datos para compartir por email sean correctos
*/
function inmueble_validar_compartirEmail() {
	if (!vacio($("#inmueble_compartir_tuNombre").val(), $("#inmueble_compartir_tuNombre").attr("placeholder"))) {
		if (!vacio($("#inmueble_compartir_tuEmail").val(), $("#inmueble_compartir_tuEmail").attr("placeholder"))) {
			if (correoValido($("#inmueble_compartir_tuEmail").val())) {
				if (!vacio($("#inmueble_compartir_amigoEmail").val(), $("#inmueble_compartir_amigoEmail").attr("placeholder"))) {
					if (correoValido($("#inmueble_compartir_amigoEmail").val())) {
						if (!vacio($("#inmueble_compartir_mensaje").val(), $("#inmueble_compartir_mensaje").attr("placeholder"))) {
							$.ajax({
								url: "lib_php/emailCompartir.php",
								type: "POST",
								dataType: "text",
								data: {
									nombre: $("#inmueble_compartir_tuNombre").val(),
									fromEmail: $("#inmueble_compartir_tuEmail").val(),
									toEmail: $("#inmueble_compartir_amigoEmail").val(),
									mensaje: $("#inmueble_compartir_mensaje").val(),
									url: $("#inmueble_botonesCompartir .template_btnsShare.email").attr("data-url")
								}
							}).always(function(){
								$("#inmueble_compartir_email").hide();
								$("#template_alertPersonalizado td").text("Hemos compartido el inmueble a tu amigo, pronto le llegara un email.");
								template_alertPersonalizado();
							});
						}
					}
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

/*
 Redireccion a la url, pero antes, asigna a la seccion el parametro de regresar
 */
function catalogo_redirecciona_regresar(newUrl) {
	$.ajax({
		url: "lib_php/updFiltros.php",
		type: "POST",
		dataType: "json",
		data: {
			updateParam: 1,
			nombre : "regresar",
			valor: 1
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1)
			gotoURL(newUrl);
	});
}
/**/

/*regresar*/

