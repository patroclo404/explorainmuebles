// JavaScript Document
var map;
var marker;


/*
	Carga las funciones y estilos al terminar de cargar la interfaz
*/
$(document).ready(function(){
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
				
				if ($(this).val() != -1) {
					$("#crearAnuncio_tipo li.lista li").each(function(){
						tempArray = $(this).attr("data-categorias").split(",");
						
						if ($.inArray($("#crearAnuncio_categoria p").attr("data-value"), tempArray) > -1)
							$(this).show();
						else
							$(this).hide();
					});
				}
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
		
		$("#crearAnuncio_estado p").attr("data-value", post_estado);
		$("#crearAnuncio_estado p").text($("#crearAnuncio_estado li.lista li[data-value='"+post_estado+"']").text());
		nuevoAnuncio_actualizar_ciudad("crearAnuncio_estado", post_ciudad, post_colonia);
		
		tempCenter = new google.maps.LatLng(post_latitud, post_longitud);
		map.setCenter(tempCenter);
		mapDefinirMarca({latLng: tempCenter});
		
		$("#crearAnuncio_estadoConservacion li.lista li[data-value='"+post_estadoConservacion+"']").click();
		$("#crearAnuncio_estadoConservacion li.lista").hide();
		
		$("#crearAnuncio_amueblado li.lista li[data-value='"+post_amueblado+"']").click();
		$("#crearAnuncio_amueblado li.lista").hide();
		
		if (parseInt(post_antiguedad) != -1) {
			$("#crearAnuncio_antiguedad li.lista li[data-value='"+post_antiguedad+"']").click();
			$("#crearAnuncio_antiguedad li.lista").hide();
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
});


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
		icon: "images/marcador.png"
	});
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
													if (!vacio((parseInt($("#crearAnuncio_estadoConservacion p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_estadoConservacion p").attr("data-value")), "Estado de Conservación")) {
														if (!vacio((parseInt($("#crearAnuncio_amueblado p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_amueblado p").attr("data-value")), "Está Amueblado")) {
															var continuar = true;
															
															if (parseInt($("#idInmueble").val() == -1)) {
																if (vacio($("#crearAnuncio_imagen").val(), "Imágen"))
																	return false;
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
															
															if (!isVacio($("#crearAnuncio_wcs").val())) {
																if (!flotante($("#crearAnuncio_wcs").val(), $("#crearAnuncio_wcs").attr("placeholder"))) {
																	return false;
																}
															}
															
															if (!isVacio($("#crearAnuncio_recamaras").val())) {
																if (!entero($("#crearAnuncio_recamaras").val(), $("#crearAnuncio_recamaras").attr("placeholder"))) {
																	return false;
																}
															}
															
															if (parseInt($("#crearAnuncio_codigo").attr("data-inmueble")) != 0) {
																continuar = false;
																
																if (!vacio($("#crearAnuncio_codigo").val(), $("#crearAnuncio_codigo").attr("placeholder"))) {
																	$.ajax({
																		url: "lib_php/updInmueble.php",
																		type: "POST",
																		dataType: "json",
																		data: {
																			id: $("#idInmueble").val(),
																			validarCodigo: 1,
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
															}
															
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
	$("#_crearAnuncioEstaAmueblado").val($("#crearAnuncio_amueblado p").attr("data-value"));
		
		
	$("#btnGuardar2").hide();
	$("#mensajeTemporal2").show();
	$("#lk_crearAnuncio").css({cursor:"wait"});	
	
	
	$("#subirAnuncio").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			if (parseInt($("#idInmueble").val() == -1))
				gotoURL("inmueble.php?id="+respuesta_json.id+"&create=1");
			else
				gotoURL("inmueble.php?id="+$("#idInmueble").val());
		}
	});
}
/**/