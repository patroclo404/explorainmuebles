// JavaScript Document
var map;
var marker;


/*
	Carga las funciones y estilos al terminar de cargar la interfaz
*/
$(document).ready(function(){
	//eventos para scroll en los labels
	$(".perfil_cuerpo .columna1 p[data-label]").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				$("html,body").animate({
					scrollTop: $("#"+elemento.attr("data-label")).offset().top
				}, 1000);
			}
		});
	});
	
	
	$("#perfil_fechaNac").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		max: true,
		selectMonths: true,
		selectYears: 30
	});
	
	
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#perfil_estado,#crearAnuncio_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				perfil_actualizar_ciudad(elemento.prop("id"));
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
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	map = new google.maps.Map(mapaGoogle, mapOptions);
	
	google.maps.event.addListener(map, "click", mapDefinirMarca);
	
	
	//parametros recibidos por post para eventos de selects
	if (typeof post_usuario_estado !== 'undefined') {
		$("#perfil_estado p").attr("data-value", post_usuario_estado);
		$("#perfil_estado p").text($("#perfil_estado li.lista li[data-value='"+post_usuario_estado+"']").text());
		perfil_actualizar_ciudad("perfil_estado", post_usuario_ciudad, post_usuario_colonia);
	}
});


/*
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idCiudad:		[Integer], es el id de la ciudad ya precargado
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function perfil_actualizar_ciudad(nomElemento, idCiudad, idColonia) {
	var indexEstado = -1;
	var arrayEstados = Array("perfil_estado", "crearAnuncio_estado");
	var arrayCiudades = Array("perfil_ciudad", "crearAnuncio_ciudad");
	var arrayColonias = Array("perfil_colonia", "crearAnuncio_colonia");
	
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
						perfil_actualizar_colonia(objMunicipio.prop("id"));
					}
				});
				
				
				if ((idCiudad != null) && (idCiudad != -1)) {
					objMunicipio.find("li.lista li[data-value='"+idCiudad+"']").click();
					objMunicipio.find("li.lista").hide();
					
					if ((idColonia != null) && (idColonia != -1)) {
						perfil_actualizar_colonia(objMunicipio.prop("id"), idColonia);
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
function perfil_actualizar_colonia(nomElemento, idColonia) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("perfil_ciudad", "crearAnuncio_ciudad");
	var arrayColonias = Array("perfil_colonia", "crearAnuncio_colonia");
	
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
function perfil_validarCampos_perfil() {
	if (!vacio($("#perfil_nombre").val(), $("#perfil_nombre").attr("placeholder"))) {
		if (!vacio($("#perfil_email").val(), $("#perfil_email").attr("placeholder"))) {
			if (correoValido($("#perfil_email").val())) {
				var continua = true;
				
				if (!isVacio($("#perfil_empresa").val())) {
					continua = false;
					
					if (!vacio($("#perfil_rfc").val(), $("#perfil_rfc").attr("placeholder"))) {
						if (!vacio($("#perfil_login").val(), $("#perfil_login").attr("placeholder"))) {
							$.ajax({
								url: "lib_php/updUsuario.php",
								type: "POST",
								dataType: "json",
								data: {
									id: -1,
									validarRFC: 1,
									rfc: $("#perfil_rfc").val()
								}
							}).always(function(respuesta_json){
								if (respuesta_json.isExito == 1) {
									$.ajax({
										url: "lib_php/updUsuario.php",
										type: "POST",
										dataType: "json",
										data: {
											id: -1,
											validarLogin: 1,
											login: $("#perfil_login").val()
										}
									}).always(function(respuesta_json2){
										if (respuesta_json2.isExito == 1) {
											perfil_save_usuario();
										}
										else
											alert(respuesta_json2.mensaje);
									});
								}
								else
									alert(respuesta_json.mensaje);
							});
						}
					}
				}
				
				if (continua) {
					perfil_save_usuario();
				}
			}
		}
	}
}


/*
	Valida los campos para guardar los datos de perfil de usuario
*/
function perfil_validarCampos_inmueble() {
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
												crearAnuncio_descripcion
												if (!vacio($("#crearAnuncio_descripcion").val(), $("#crearAnuncio_descripcion").attr("placeholder"))) {
													if (!vacio((parseInt($("#crearAnuncio_estadoConservacion p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_estadoConservacion p").attr("data-value")), "Estado de Conservación")) {
														if (!vacio((parseInt($("#crearAnuncio_amueblado p").attr("data-value")) == -1 ? "" : $("#crearAnuncio_amueblado p").attr("data-value")), "Está Amueblado")) {
															if (!vacio($("#crearAnuncio_imagen").val(), "Imágen")) {
																var continuar = true;
																
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
																	if (!entero($("#crearAnuncio_wcs").val(), $("#crearAnuncio_wcs").attr("placeholder"))) {
																		return false;
																	}
																}
																
																if (!isVacio($("#crearAnuncio_recamaras").val())) {
																	if (!entero($("#crearAnuncio_recamaras").val(), $("#crearAnuncio_recamaras").attr("placeholder"))) {
																		return false;
																	}
																}
																
																if (parseInt($("#crearAnuncio_codigo").attr("data-inmueble")) == 1) {
																	continuar = false;
																	
																	if (!vacio($("#crearAnuncio_codigo").val(), $("#crearAnuncio_codigo").attr("placeholder"))) {
																		$.ajax({
																			url: "lib_php/updInmueble.php",
																			type: "POST",
																			dataType: "json",
																			data: {
																				id: -1,
																				validarCodigo: 1,
																				codigo: $("#crearAnuncio_codigo").val()
																			}
																		}).always(function(respuesta_json){
																			if (respuesta_json.isExito == 1) {
																				perfil_save_inmueble();
																			}
																			else
																				alert(respuesta_json.mensaje);
																		});
																	}
																}
																
																if (continuar) {
																	perfil_save_inmueble();
																}
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
	Valida los campos para guardar los datos de perfil de usuario
*/
function perfil_validarCampos_chgPass() {
	if (!vacio($("#cambiarPassword_passActual").val(), $("#cambiarPassword_passActual").attr("placeholder"))) {
		if (!vacio($("#cambiarPassword_newPass").val(), $("#cambiarPassword_newPass").attr("placeholder"))) {
			if (!vacio($("#cambiarPassword_confNewPass").val(), $("#cambiarPassword_confNewPass").attr("placeholder"))) {
				if ($("#cambiarPassword_newPass").val() == $("#cambiarPassword_confNewPass").val()) {
					perfil_save_password();
				}
				else
					alert("Las contraseñas son diferentes, vuelva a intentarlo");
			}
		}
	}
}


/*
	Guarda los cambios del perfil de usuario
*/
function perfil_save_usuario() {
	$("#_perfilEstado").val($("#perfil_estado p").attr("data-value"));
	$("#_perfilCiudad").val($("#perfil_ciudad p").attr("data-value"));
	$("#_perfilColonia").val($("#perfil_colonia p").attr("data-value"));
	$("#_perfilCP").val(-1);
	if (parseInt($("#perfil_colonia p").attr("data-value")) != -1)
		$("#_perfilCP").val($("#perfil_colonia li.lista li[data-value='"+$("#perfil_colonia p").attr("data-value")+"']").attr("data-cp"));
		
		
	$("#btnGuardar").hide();
	$("#mensajeTemporal").show();
	$("#lk_miPerfil").css({cursor:"wait"});	
	
	
	$("#subirPerfil").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			gotoURL("perfil.php");
		}
	});
}


/*
	Guarda un nuevo inmueble
*/
function perfil_save_inmueble() {
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
			gotoURL("perfil.php");
		}
	});
}


/*
	Guarda el cambio de password
*/
function perfil_save_password() {
	$("#btnGuardar3").hide();
	$("#mensajeTemporal3").show();
	$("#lk_cambiarPassword").css({cursor:"wait"});
	
	
	$.ajax({
		url: "lib_php/updUsuario.php",
		type: "POST",
		dataType: "json",
		data: {
			changePass: 1,
			oldPass: md5Script($("#cambiarPassword_passActual").val()),
			newPass: md5Script($("#cambiarPassword_newPass").val())
		}
	}).always(function(respuesta_json){
		$("#btnGuardar3").show();
		$("#mensajeTemporal3").hide();
		$("#lk_cambiarPassword").css({cursor:"default"});
		
		if (respuesta_json.isExito == 1) {
			$("#cambiarPassword_passActual").val("");
			$("#cambiarPassword_newPass").val("");
			$("#cambiarPassword_confNewPass").val("");
		}
		
		alert(respuesta_json.mensaje);
	});
}
/**/