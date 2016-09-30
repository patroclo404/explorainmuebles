// JavaScript Document


/*
	Carga las funciones y estilos al terminar de cargar la interfaz
*/
$(document).ready(function(){
	$("#perfil_fechaNac").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		max: true,
		selectMonths: true,
		selectYears: 30
	});
	
	
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#perfil_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				perfil_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	
	//parametros recibidos por post para eventos de selects
	if (typeof post_usuario_estado !== 'undefined') {
		$("#perfil_estado p").attr("data-value", post_usuario_estado);
		$("#perfil_estado p").text($("#perfil_estado li.lista li[data-value='"+post_usuario_estado+"']").text());
		perfil_actualizar_ciudad("perfil_estado", post_usuario_ciudad, post_usuario_colonia);
	}
	
	//evalua si se recibir el parametro para cargar al inicio el popup
	if ($("#perfil_errorNuevoAnuncio").length == 1) {
		perfil_errorNuevoAnuncio();
	}
});


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function perfil_cerrarPopUp() {
	$("#perfil_errorNuevoAnuncio").hide();
}


/*
	Muestra un popup con los elementos que faltan para crear un nuevo anuncio
*/
function perfil_errorNuevoAnuncio() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#perfil_errorNuevoAnuncio");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idCiudad:		[Integer], es el id de la ciudad ya precargado
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function perfil_actualizar_ciudad(nomElemento, idCiudad, idColonia) {
	var indexEstado = -1;
	var arrayEstados = Array("perfil_estado");
	var arrayCiudades = Array("perfil_ciudad");
	var arrayColonias = Array("perfil_colonia");
	
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
	var arrayCiudades = Array("perfil_ciudad");
	var arrayColonias = Array("perfil_colonia");
	
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
	Valida los campos para guardar los datos de perfil de usuario
*/
function perfil_validarCampos() {
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
											perfil_save();
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
					perfil_save();
				}
			}
		}
	}
}


/*
	Guarda los cambios del perfil de usuario
*/
function perfil_save() {
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
/**/