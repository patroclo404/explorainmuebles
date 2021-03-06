// JavaScript Document
var map;
var marker;


$(document).ready(function(){
	formulario_inicializarBotones();
	
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#crearAnuncio_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				nuevoAnuncio_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	//agrega evento para cuando se cambie de categoria de inmueble
	$("#crearAnuncio_categoria").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				$("#crearAnuncio_tipo p").attr("data-value", -1);
				$("#crearAnuncio_tipo p").text("");
				
				if (parseInt($(this).attr("data-value")) != -1) {
					$("#crearAnuncio_tipo li.lista li").each(function(){
						tempArray = $(this).attr("data-categorias").split(",");
						
						if ($.inArray($("#crearAnuncio_categoria p").attr("data-value"), tempArray) > -1)
							$(this).show();
						else
							$(this).hide();
					});
					
					//si es renta vacacional, se oculta el tipo de transaccion y este por default se marca en vacional
					if (parseInt($(this).attr("data-value")) == 3) {
						$("#crearAnuncio_transaccion p").attr("data-value", "3");
						$("#crearAnuncio_transaccion p").text($("#crearAnuncio_transaccion li.lista li[data-value='3']").text());
						$("#crearAnuncio_transaccion").parent().find(".mascara").show();
						$("#etiquetaPrecio").text("Precio por noche*");
					}
					else {
						$("#crearAnuncio_transaccion").parent().find(".mascara").hide();
						$("#crearAnuncio_transaccion p").attr("data-value", -1);
						$("#crearAnuncio_transaccion p").text("");
						$("#etiquetaPrecio").text("Precio*");
					}
				}
			}
		});
	});
	
	//agrega evento para cuando se cambie tipo de inmueble
	$("#crearAnuncio_tipo").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				if (parseInt($("#crearAnuncio_categoria p").attr("data-value")) != 3) {
					$("#crearAnuncio_transaccion p").attr("data-value", -1);
					$("#crearAnuncio_transaccion p").text("");
				}
				
				if (parseInt($(this).attr("data-value")) != -1) {
					$("#crearAnuncio_transaccion li.lista li[data-value='3']").hide();
					
					if ((parseInt($(this).attr("data-value")) == 1) || (parseInt($(this).attr("data-value")) == 2)) {
						$("#crearAnuncio_transaccion li.lista li[data-value='3']").show();
					}
				}
			}
		});
	});
	
	
	//agrega evento para cuando se cambie de usuario, actualizar el codigo e inmobiliaria
	$("#crearAnuncio_usuario").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				if (parseInt($(this).attr("data-inmobiliaria")) != -1) {
					$("#celdaCodigo").show();
				}
				else
					$("#celdaCodigo").hide();
			}
		});
	});
	
	
	//eventos para el mapa
	tempCenter = new google.maps.LatLng(20.650118, -103.422227);
	
	//define el google maps
	var mapaGoogle = document.getElementById("contenedorMapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 14,
		mapMaker: true,
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	map = new google.maps.Map(mapaGoogle, mapOptions);
	
	google.maps.event.addListener(map, "click", mapDefinirMarca);
	
	//actualiza los datos para editar el inmueble (campos selects)
	if (typeof post_categoria !== 'undefined') {
		$("#crearAnuncio_categoria li.lista li[data-value='"+post_categoria+"']").click();
		$("#crearAnuncio_categoria li.lista").hide();
		
		$("#crearAnuncio_tipo li.lista li[data-value='"+post_tipo+"']").click();
		$("#crearAnuncio_tipo li.lista").hide();
		
		$("#crearAnuncio_transaccion li.lista li[data-value='"+post_transaccion+"']").click();
		$("#crearAnuncio_transaccion li.lista").hide();
		
		$("#crearAnuncio_usuario p").attr("data-value", post_usuario);
		$("#crearAnuncio_usuario p").text($("#crearAnuncio_usuario li.lista li[data-value='"+post_usuario+"']").text());
		$("#crearAnuncio_usuario li.lista li[data-value='"+post_usuario+"']").click();
		
		$("#crearAnuncio_estado p").attr("data-value", post_estado);
		$("#crearAnuncio_estado p").text($("#crearAnuncio_estado li.lista li[data-value='"+post_estado+"']").text());
		nuevoAnuncio_actualizar_ciudad("crearAnuncio_estado", post_ciudad, post_colonia);
		
		tempCenter = new google.maps.LatLng(post_latitud, post_longitud);
		map.setCenter(tempCenter);
		mapDefinirMarca({latLng: tempCenter});
		
		
		if (parseInt(post_estadoConservacion) != -1) {
			$("#crearAnuncio_estadoConservacion li.lista li[data-value='"+post_estadoConservacion+"']").click();
			$("#crearAnuncio_estadoConservacion li.lista").hide();
		}
		
		
		if (parseInt(post_antiguedad) != -1) {
			$("#crearAnuncio_antiguedad li.lista li[data-value='"+post_antiguedad+"']").click();
			$("#crearAnuncio_antiguedad li.lista").hide();
		}
		
		if (parseInt(post_wcs) != "") {
			$("#crearAnuncio_wcs li.lista li[data-value='"+post_wcs+"']").click();
			$("#crearAnuncio_wcs li.lista").hide();
		}
		
		if (parseInt(post_recamaras) != "") {
			$("#crearAnuncio_recamaras li.lista li[data-value='"+post_recamaras+"']").click();
			$("#crearAnuncio_recamaras li.lista").hide();
		}
	}
	
	
	//agrega eventos en los selects ciudades, cuando se hace click en ellos y no se ha seleccionado un estando previamente
	$("#crearAnuncio_ciudad").each(function(index){
		$(this).on({
			click: function() {
				var arrayEstados = Array("crearAnuncio_estado");
				if (parseInt($("#"+arrayEstados[index]+" p").attr("data-value")) == -1) {
					template_errorSelectMunicipio();
				}
			}
		});
	});
	
	
	//agrega eventos en los selects colonias, cuando se hace click en ellos y no se ha seleccionado una ciudad previamente
	$("#crearAnuncio_colonia").each(function(index){
		$(this).on({
			click: function() {
				var arrayCiudades = Array("crearAnuncio_ciudad");
				if (parseInt($("#"+arrayCiudades[index]+" p").attr("data-value")) == -1) {
					template_errorSelectColonia();
				}
			}
		});
	});
	
	
	//agrega evento para borrar las imagenes de la galeria
	$("#galeriaImagenes .bloqueImagen").each(function(){
		var elemento = $(this);
		
		$(this).find("span.borrar").on({
			click: function() {
				if (confirm("¿Esta seguro de eliminar la imágen?")) {
					$.ajax({
						url: "lib_php/updInmuebleImagen.php",
						type: "POST",
						dataType: "json",
						data: {
							borrar: 1,
							id: -1,
							idImagen: elemento.attr("data-imagen")
						}
					}).always(function(respuesta_json){
						if (respuesta_json.isExito == 1) {
							elemento.remove();
						}
					});
				}
			}
		});
	});
	
	//eventos para los checkbox-inputs
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkCajonesEstacionamiento,#chkNumeroOficinas").each(function(){
		var elemento = $(this);
		
		$(this).on({
			change: function() {
				if ($(this).prop("checked")) {
					elemento.parent().find("input[type='text']").show();
				}
				else {
					elemento.parent().find("input[type='text']").hide();
					elemento.parent().find("input[type='text']").val("");
				}
			}
		});
	});
	
	//para cuando se edita el inmueble, carge los campos chekbox-inputs
	if (typeof post_categoria !== 'undefined') {
		$("#crearAnuncio_cuotaMantenimiento,#crearAnuncio_elevador,#crearAnuncio_estacionamientoVisitas,#crearAnuncio_cajonesEstacionamiento,#crearAnuncio_numeroOficinas").each(function(){
			var elemento = $(this);
			
			if ($(this).val() != "") {
				elemento.parent().find("input[type='checkbox']").prop("checked", true);
			}
		});
	}
	
	//para la primera vez
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkCajonesEstacionamiento,#chkNumeroOficinas").change();
	
	
	//evento para cuando se ingresa una url de youtube para los videos del inmueble
	$("#nuevoAnuncio_urlVideo").on({
		keyup: function(evt) {
			template_displayUnicode(evt, nuevoAnuncio_validarUrl);
		}
	});
	
	
	//agrega evento para borrar los videos de la galeria
	$("#galeriaVideos .bloqueVideo").each(function(){
		var elemento = $(this);
		
		$(this).find("span.borrar").on({
			click: function() {
				if (confirm("¿Esta seguro de eliminar la url del video?")) {
					$.ajax({
						url: "lib_php/updInmuebleVideo.php",
						type: "POST",
						dataType: "json",
						data: {
							borrar: 1,
							id: -1,
							idVideo: elemento.attr("data-video")
						}
					}).always(function(respuesta_json){
						if (respuesta_json.isExito == 1) {
							elemento.remove();
						}
					});
				}
			}
		});
	});
});


/*
	Crea los eventos y las funciones de botones especiales como el nuevo select
*/
function formulario_inicializarBotones() {
	//todos los "nuevos selects" tiene esta misma funcionalidad
	$("ul.template_campos").each(function(){
		var elemento = $(this);
		var _evtBuscar;
		
		$(this).on({
			click: function() {
				var isOpen = false;
				
				if (elemento.find("li.lista").css("display") != "none")
					isOpen = true;
					
				if (isOpen) {
					elemento.find("li.lista").hide();
					elemento.find("input").val("");
				}
				else {
					if (elemento.find("li.lista ul li").length > 0) {
						$("ul.template_campos li.lista").hide();//oculta los demas si hubieras mas abiertos
						elemento.find("li.lista").show();
						elemento.find("input").focus();
					}
				}
			}
		});
		
		//agrega evento para simular cuando se escribe y hacer busqueda de acuerdo las opciones disponibles
		$(this).find("input").on({
			keyup: function(evt) {//simula el evento de buscar conforme se escribe
				clearTimeout(_evtBuscar);
				_evtBuscar = setTimeout(function(){
					var altura = 0;
			
					elemento.find("li.lista ul li").each(function(){
						if ($(this).text().toUpperCase().indexOf(elemento.find("input").val().toUpperCase()) == 0)
							return false;
						else
							altura += $(this).height();
					});
					
					elemento.find("li.lista ul").animate({
						scrollTop: altura
					}, 100);
					
					elemento.find("input").val("");
				}, 600);
			}
		});
		
		//agrega evento para marcar como selecciona la opcion que se le dio click
		$(this).find("li.lista li").on({
			click: function() {
				elemento.find("p").attr("data-value", $(this).attr("data-value"));
				elemento.find("p").text($(this).text());
				elemento.find("input").val("");
			}
		});
	});
}


/*
	Muestra un popup donde indica que debe seleccionar primero un estado
*/
function template_errorSelectMunicipio() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_errorSelectMunicipio");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Muestra un popup donde indica que debe seleccionar primero un municipio
*/
function template_errorSelectColonia() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_errorSelectColonia");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Muestra un popup donde tiene un texto personalizado antes de mostrarse dicho popup
*/
function template_alertPersonalizado() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_alertPersonalizado");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Cierra todos los poups de la interfaz actual
*/
function formularioInmueble_cerrarPopup(){
	$("#template_mascaraPrincipal").hide();
	$("#template_errorSelectMunicipio").hide();
	$("#template_errorSelectColonia").hide();
	$("#template_alertPersonalizado").hide();
}


/*
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idCiudad:		[Integer], es el id de la ciudad ya precargado
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function nuevoAnuncio_actualizar_ciudad(nomElemento, idCiudad, idColonia) {
	var indexEstado = -1;
	var arrayEstados = Array("crearAnuncio_estado");
	var arrayCiudades = Array("crearAnuncio_ciudad");
	var arrayColonias = Array("crearAnuncio_colonia");
	
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
			url: "lib_php/consDireccion.php",
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
						nuevoAnuncio_actualizar_colonia(objMunicipio.prop("id"));
					}
				});
				
				
				if ((idCiudad != null) && (idCiudad != -1)) {
					objMunicipio.find("li.lista li[data-value='"+idCiudad+"']").click();
					objMunicipio.find("li.lista").hide();
					
					if ((idColonia != null) && (idColonia != -1)) {
						nuevoAnuncio_actualizar_colonia(objMunicipio.prop("id"), idColonia);
					}
				}
			}
		});
	}
}


/*
	Actualiza las colonias para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function nuevoAnuncio_actualizar_colonia(nomElemento, idColonia) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("crearAnuncio_ciudad");
	var arrayColonias = Array("crearAnuncio_colonia");
	
	indexMunicipio = arrayCiudades.indexOf(nomElemento);
	
	if (indexMunicipio > -1) {
		objMunicipio = $("#"+arrayCiudades[indexMunicipio]);
		objColonia = $("#"+arrayColonias[indexMunicipio]);
		
		objColonia.find("li.lista ul").html("");
		objColonia.find("p").attr("data-value", -1);
		objColonia.find("p").text("");
		
		
		$.ajax({
			url: "lib_php/consDireccion.php",
			type: "POST",
			dataType: "json",
			data: {
				consColonia: 1,
				ciudad: objMunicipio.find("p").attr("data-value")
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					objColonia.find("li.lista ul").append("<li data-value='"+respuesta_json.datos[x].id+"' data-cp='"+respuesta_json.datos[x].cp+"'>"+respuesta_json.datos[x].nombre+"</li>");
				}
				
				objColonia.find("li.lista li").on({
					click: function() {
						objColonia.find("p").attr("data-value", $(this).attr("data-value"));
						objColonia.find("p").text($(this).text());
					}
				});
				
				if ((idColonia != null) && (idColonia != -1)) {
					objColonia.find("li.lista li[data-value='"+idColonia+"']").click();
					objColonia.find("li.lista").hide();
				}
			}
		});
	}
}


/*
	Asigna una marca en el mapa, ademas de los campos de latitud y longitud
	
		* evt:	Event, es el evento asignado en el mapa para llamar esta funcion
*/
function mapDefinirMarca(evt) {
	$("#_crearAnuncioLatitud").val(evt.latLng.lat());
	$("#_crearAnuncioLongitud").val(evt.latLng.lng());
	
	if (typeof marker !== 'undefined')
		marker.setMap(null);
	
	marker = new google.maps.Marker({
		position: evt.latLng,
		map: map,
		icon: "../images/marcador3.png"
	});
}


/*
	Trata de localizar en el mapa la direccion escritra en los campos del anuncio
*/
function nuevoAnuncio_encontrarUbicacion() {
	if (!vacio($("#crearAnuncio_calleNumero").val(), $("#crearAnuncio_calleNumero").attr("placeholder"))) {
		if (!vacio((parseInt($("#crearAnuncio_estado p").attr("data-value")) != -1 ? 1 : ""), "Estado")) {
			if (!vacio((parseInt($("#crearAnuncio_ciudad p").attr("data-value")) != -1 ? 1 : ""), "Ciudad")) {
				direCalle = "";
				direNumero = "";
				partes = $("#crearAnuncio_calleNumero").val().replace(/#/g, "");
				partes = partes.replace(/-/g, " ");
				partes = partes.split(" ");
				
				for (var x = 0; x < partes.length; x++) {
					if (isEntero(partes[x])) {
						direNumero = partes[x];
						break;
					}
					else
						direCalle += (x != 0 ? " " : "") + partes[x];
				}
				
				direccionBusqueda = (direNumero != "" ? direNumero+"+" : "")+direCalle+"+"+$("#crearAnuncio_ciudad li.lista li[data-value='"+$("#crearAnuncio_ciudad p").attr("data-value")+"']").text()+",+"+$("#crearAnuncio_estado li.lista li[data-value='"+$("#crearAnuncio_estado p").attr("data-value")+"']").text();
				
				$.ajax({
					url: "http://maps.googleapis.com/maps/api/geocode/json?address="+direccionBusqueda+"&sensor=true_or_false",
					dataType: "json"
				}).always(function(respuesta_json){
					if (respuesta_json.results.length > 0) {
						var obtenerPosicion = respuesta_json.results[0].geometry.location;
						tempPosicion = new google.maps.LatLng(obtenerPosicion.lat, obtenerPosicion.lng);
						map.setCenter(tempPosicion);
						mapDefinirMarca({latLng: tempPosicion});
					}
					else
						alert("No se encontro una posición en el mapa.");
				});
			}
		}
	}
}


/*
	Se cargo una imagen nueva por medio del iframe
	
		* nombreArchivo:	Array String, es el nombre del/los archivo/s guardardo/s en temp
*/
function nuevoAnuncio_tempImagenCargada(nombreArchivo) {
	var urlArchivosTemporales = "../images/images/temp/";
	var imagenesTemporales = $("#imagenesTemporales");
	var isPrimera = false;
	if ($("#galeriaImagenes").length == 0)
		isPrimera = imagenesTemporales.find(".bloqueImagen").length == 0 ? true : false;
	var arrayArchivos = nombreArchivo.split(",");
	
	for (var x = 0; x < arrayArchivos.length; x++) {
		var elemento = 
			"<div class='bloqueImagen' data-imagen='"+arrayArchivos[x]+"'>"+
				"<img src='"+urlArchivosTemporales+arrayArchivos[x]+"' />"+
				"<span class='borrar'>X</span>"+
				"<p><input type='radio' name='radioImagenPrincipal' "+(isPrimera ? "checked='checked'" : "")+" /></p>"+
			"</div>";
		isPrimera = false;
		
		imagenesTemporales.append(elemento);
	}

	
	imagenesTemporales.find(".bloqueImagen .borrar").unbind();
	imagenesTemporales.find(".bloqueImagen").each(function(){
		var elemento = $(this);
		
		$(this).find(".borrar").on({
			click: function() {
				$.ajax({
					url: "../lib_php/tempSubirImagen2.php",
					type: "POST",
					dataType: "json",
					data: {
						borrar: 1,
						imagen: elemento.attr("data-imagen")
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						elemento.remove();
					}
				});
			}
		});
	});
	
	
	$("#iframeSubirImagen").html("");
	if (imagenesTemporales.find(".bloqueImagen").length < 20) {
		$("#iframeSubirImagen").html('<iframe src="../lib_php/tempSubirImagen.php" frameborder="0" width="400" height="50"></iframe>');
	}
}


/*
	Valida los campos para guardar los datos de perfil de usuario
*/
function nuevoAnuncio_validarCampos_inmueble() {
	if (!vacio($("#crearAnuncio_titulo").val(), $("#crearAnuncio_titulo").attr("placeholder"))) {
		if (!vacio((parseInt($("#crearAnuncio_categoria p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_categoria p").attr("data-value")), "Categoría")) {
			if (!vacio((parseInt($("#crearAnuncio_tipo p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_tipo p").attr("data-value")), "Tipo")) {
				if (!vacio((parseInt($("#crearAnuncio_transaccion p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_transaccion p").attr("data-value")), "Transacción")) {
					if (!vacio($("#crearAnuncio_precio").val(), $("#crearAnuncio_precio").attr("placeholder"))) {
						_precio = $("#crearAnuncio_precio").val().replace(/\$/g, "").replace(/,/g, "");
						$("#crearAnuncio_precio").val(_precio);
						if (flotante($("#crearAnuncio_precio").val(), $("#crearAnuncio_precio").attr("placeholder"))) {
							if (!vacio($("#crearAnuncio_calleNumero").val(), $("#crearAnuncio_calleNumero").attr("placeholder"))) {
								if (!vacio((parseInt($("#crearAnuncio_estado p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_estado p").attr("data-value")), "Estado")) {
									if (!vacio((parseInt($("#crearAnuncio_ciudad p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_ciudad p").attr("data-value")), "Ciudad")) {
										if (!vacio((parseInt($("#crearAnuncio_colonia p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_colonia p").attr("data-value")), "Colonia")) {
											if ($("#_crearAnuncioLatitud").val() != "") {
												if (!vacio($("#crearAnuncio_descripcion").val(), $("#crearAnuncio_descripcion").attr("placeholder"))) {
													if (!vacio((parseInt($("#crearAnuncio_usuario p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_usuario p").attr("data-value")), "Usuario")) {
														var continuar = true;
															
														if (parseInt($("#idInmueble").val()) == -1) {//nuevo
															if ($("#imagenesTemporales .bloqueImagen").length == 0) {
																alert("Ingrese al menos una imágen para el inmueble");
																return false;
															}
															else {
																if($("input[name='radioImagenPrincipal']:checked").length == 0) {
																	alert("Selecciona una imágen como la principal");
																	return false;
																}
															}
														}
														else {//modificar
															if($("input[name='radioImagenPrincipal']:checked").length == 0) {
																alert("Selecciona una imágen como la principal");
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_dimesionTotal").val())) {
															if (!flotante($("#crearAnuncio_dimesionTotal").val(), $("#crearAnuncio_dimesionTotal").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_dimensionConstruida").val())) {
															if (!flotante($("#crearAnuncio_dimensionConstruida").val(), $("#crearAnuncio_dimensionConstruida").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_cuotaMantenimiento").val())) {
															_precio = $("#crearAnuncio_cuotaMantenimiento").val().replace(/\$/g, "").replace(/,/g, "");
															$("#crearAnuncio_cuotaMantenimiento").val(_precio);
															
															if (!flotante($("#crearAnuncio_cuotaMantenimiento").val(), $("#crearAnuncio_cuotaMantenimiento").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_elevador").val())) {
															if (!entero($("#crearAnuncio_elevador").val(), $("#crearAnuncio_elevador").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_estacionamientoVisitas").val())) {
															if (!entero($("#crearAnuncio_estacionamientoVisitas").val(), $("#crearAnuncio_estacionamientoVisitas").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_numeroOficinas").val())) {
															if (!entero($("#crearAnuncio_numeroOficinas").val(), $("#crearAnuncio_numeroOficinas").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_metrosFrente").val())) {
															if (!flotante($("#crearAnuncio_metrosFrente").val(), $("#crearAnuncio_metrosFrente").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_metrosFondo").val())) {
															if (!flotante($("#crearAnuncio_metrosFondo").val(), $("#crearAnuncio_metrosFondo").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_cajonesEstacionamiento").val())) {
															if (!entero($("#crearAnuncio_cajonesEstacionamiento").val(), $("#crearAnuncio_cajonesEstacionamiento").attr("placeholder"))) {
																return false;
															}
														}
														
														//solo para casas y departamentos: wcs y recamaras son obligatorios
														if ((parseInt($("#crearAnuncio_tipo p").attr("data-value")) == 1) || (parseInt($("#crearAnuncio_tipo p").attr("data-value")) == 2)) {//casa o departamento
															if (!vacio((parseInt($("#crearAnuncio_wcs p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_wcs p").attr("data-value")), "Baños")) {
																if (vacio((parseInt($("#crearAnuncio_recamaras p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_recamaras p").attr("data-value")), "Recamaras"))
																	return false;
															}
															else
																return false;
														}
														
														
														if (!isVacio($("#crearAnuncio_codigo").val())) {
															$.ajax({
																url: "lib_php/updInmueble.php",
																type: "POST",
																dataType: "json",
																data: {
																	id: $("#idInmueble").val(),
																	validarCodigo: 1,
																	usuario: $("#crearAnuncio_usuario p").attr("data-value"),
																	codigo: $("#crearAnuncio_codigo").val()
																}
															}).always(function(respuesta_json){
																if (respuesta_json.isExito == 1) {
																	nuevoAnuncio_save();
																}
																else
																	alert(respuesta_json.mensaje);
															});
														}
														else {
															if (continuar) {
																nuevoAnuncio_save();
															}
														}
													}
												}
											}
											else {
												alert("Agrege la posición del inmueble en el mapa.");
												return false;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


/*
	Valida que la url sea una url valida y de youtube
*/
function nuevoAnuncio_validarUrl() {
	if (!isVacio($("#nuevoAnuncio_urlVideo").val())) {
		if (validaURL($("#nuevoAnuncio_urlVideo").val(), "Videos en Youtube")) {
			if ($("#nuevoAnuncio_urlVideo").val().indexOf("youtube") != -1) {
				var videosTemporales = $("#videosTemporales");
				_urlVideo = $("#nuevoAnuncio_urlVideo").val().replace("watch?v=", "v/");
				var elemento = 
					"<div class='bloqueVideo' data-video='"+$("#nuevoAnuncio_urlVideo").val()+"'>"+
						"<object>"+
							"<param name='movie' value='"+_urlVideo+"?version=3&feature=player_detailpage'>"+
							"<param name='allowFullScreen' value='true'>"+
							"<param name='allowScriptAccess' value='always'>"+
							"<embed src='"+_urlVideo+"?version=3&feature=player_detailpage&showinfo=0&autohide=1&rel=0' type='application/x-shockwave-flash' allowfullscreen='true' allowScriptAccess='always' wmode=transparent width='60' height='60' showinfo=0>"+
						"</object>"+
						"<span class='borrar'>X</span>"+
					"</div>";
				
				videosTemporales.append(elemento);
				videosTemporales.find(".bloqueVideo .borrar").unbind();
				videosTemporales.find(".bloqueVideo").each(function(){
					var elemento = $(this);
					
					$(this).find(".borrar").on({
						click: function() {
							elemento.remove();
						}
					});
				});
				
				
				$("#nuevoAnuncio_urlVideo").val("");
				if (videosTemporales.find(".bloqueVideo").length > 20) {
					$("#nuevoAnuncio_urlVideo").hide();
				}
			}
			else
				alert("La url debe ser de Youtube.");
		}
	}
}


/*
	Guarda un nuevo inmueble
*/
function nuevoAnuncio_save() {
	$("#_crearAnuncioCategoria").val($("#crearAnuncio_categoria p").attr("data-value"));
	$("#_crearAnuncioTipo").val($("#crearAnuncio_tipo p").attr("data-value"));
	$("#_crearAnuncioTransaccion").val($("#crearAnuncio_transaccion p").attr("data-value"));
	$("#_crearAnuncioEstado").val($("#crearAnuncio_estado p").attr("data-value"));
	$("#_crearAnuncioCiudad").val($("#crearAnuncio_ciudad p").attr("data-value"));
	$("#_crearAnuncioColonia").val($("#crearAnuncio_colonia p").attr("data-value"));
	$("#_crearAnuncioCP").val($("#crearAnuncio_colonia li.lista li[data-value='"+$("#crearAnuncio_colonia p").attr("data-value")+"']").attr("data-cp"));
	$("#_crearAnuncioAntiguedad").val($("#crearAnuncio_antiguedad p").attr("data-value"));
	$("#_crearAnuncioEstadoConservacion").val($("#crearAnuncio_estadoConservacion p").attr("data-value"));
	$("#_crearAnuncioWcs").val($("#crearAnuncio_wcs p").attr("data-value"));
	$("#_crearAnuncioRecamaras").val($("#crearAnuncio_recamaras p").attr("data-value"));
	$("#_crearAnuncioUsuario").val($("#crearAnuncio_usuario p").attr("data-value"));
		
	
	//ajuste para imagenes
	if ($("#imagenesTemporales .bloqueImagen").length > 0) {
		var imagenes = Array();
		var _tempPrincipal = Array();
		
		$("#imagenesTemporales .bloqueImagen").each(function(){
			imagenes.push($(this).attr("data-imagen"));
			_tempPrincipal.push(($(this).find("input[name='radioImagenPrincipal']").prop("checked") ? 1 : 0));
		});
		
		$("#imagen").val(imagenes.toString());
		$("#imagenPrincipal").val(_tempPrincipal.toString());
	}
	
	
	if ($("#idImagenPrincipal").length == 1) {
		$("#idImagenPrincipal").val($("#galeriaImagenes input[name='radioImagenPrincipal']:checked").attr("data-id"));
	}
	
	//ajuste para videos
	if ($("#videosTemporales .bloqueVideo").length > 0) {
		var videos = Array();
		
		$("#videosTemporales .bloqueVideo").each(function(){
			videos.push($(this).attr("data-video"));
		});
		
		$("#videos").val(videos.toString());
	}
	
	//si estos checks estan activos (y no tienen un valor en el input, asignar automaticamente 1)
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkCajonesEstacionamiento,#chkNumeroOficinas").each(function(){
		var elemento = $(this);
		
		if ($(this).prop("checked")) {
			if (elemento.parent().find("input[type='text']").val() == "")
				elemento.parent().find("input[type='text']").val("1");
		}
	});
	
	
	$("#btnGuardar2").hide();
	$("#mensajeTemporal2").show();
	$(".formularioInmueble_cuerpo").css({cursor:"wait"});	
	
	
	$("#subirAnuncio").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				gotoURL("inmueble.php");
			}
			else
				alert(respuesta_json.mensaje);
		}
	});
}
/**/