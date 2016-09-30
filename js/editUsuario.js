// JavaScript Document


/*
	Carga las funciones y estilos al terminar de cargar la interfaz
*/
$(document).ready(function(){
	$("#edit_fechaNac").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		max: true,
		selectMonths: true,
		selectYears: 30
	});
	
	
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#edit_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				edit_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	
	//parametros recibidos por post para eventos de selects
	if (typeof post_usuario_estado !== 'undefined') {
		$("#edit_estado p").attr("data-value", post_usuario_estado);
		$("#edit_estado p").text($("#edit_estado li.lista li[data-value='"+post_usuario_estado+"']").text());
		edit_actualizar_ciudad("edit_estado", post_usuario_ciudad, post_usuario_colonia);
	}
	
	//agrega eventos en los selects ciudades, cuando se hace click en ellos y no se ha seleccionado un estando previamente
	$("#edit_ciudad").each(function(index){
		$(this).on({
			click: function() {
				var arrayEstados = Array("edit_estado");
				if (parseInt($("#"+arrayEstados[index]+" p").attr("data-value")) == -1) {
					template_errorSelectMunicipio();
				}
			}
		});
	});
	
	
	//agrega eventos en los selects colonias, cuando se hace click en ellos y no se ha seleccionado una ciudad previamente
	$("#edit_colonia").each(function(index){
		$(this).on({
			click: function() {
				var arrayCiudades = Array("edit_ciudad");
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
function edit_actualizar_ciudad(nomElemento, idCiudad, idColonia) {
	var indexEstado = -1;
	var arrayEstados = Array("edit_estado");
	var arrayCiudades = Array("edit_ciudad");
	var arrayColonias = Array("edit_colonia");
	
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
						edit_actualizar_colonia(objMunicipio.prop("id"));
					}
				});
				
				
				if ((idCiudad != null) && (idCiudad != -1)) {
					objMunicipio.find("li.lista li[data-value='"+idCiudad+"']").click();
					objMunicipio.find("li.lista").hide();
					
					if ((idColonia != null) && (idColonia != -1)) {
						edit_actualizar_colonia(objMunicipio.prop("id"), idColonia);
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
function edit_actualizar_colonia(nomElemento, idColonia) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("edit_ciudad");
	var arrayColonias = Array("edit_colonia");
	
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
function edit_validarCampos() {
	if (!vacio($("#edit_nombre").val(), $("#edit_nombre").attr("placeholder"))) {
		if (!vacio($("#edit_email").val(), $("#edit_email").attr("placeholder"))) {
			if (correoValido($("#edit_email").val())) {
				var id = parseInt($("#idUsuario").val());
				var continuar = true;
				
				tempFecha = $("#edit_fechaNacDia").val()+"/"+$("#edit_fechaNacMes").val()+"/"+$("#edit_fechaNacYear").val();
				
				if (tempFecha != "//") {
					if (fechaValida(tempFecha, tempFecha)) {
						$("#edit_fechaNac").val(tempFecha);
					}
					else
						return false;
				}
				else
					$("#edit_fechaNac").val("");
				
				if (id == -1) {
					continuar = false;
					
					if (!vacio($("#edit_password").val(), $("#edit_password").attr("placeholder"))) {
						if (!vacio($("#edit_confPassword").val(), $("#edit_confPassword").attr("placeholder"))) {
							if ($("#edit_password").val() == $("#edit_confPassword").val()) {
								continuar = true;
							}
							else {
								alert("Las contraseñas son diferentes, vuelva a intentarlo");
								return false;
							}
						}
					}
				}
				
				if (continuar) {
					$.ajax({
						url: "lib_php/updUsuarioInmobiliaria.php",
						type: "POST",
						dataType: "json",
						data: {
							id: $("#idUsuario").val(),
							validarEmail: 1,
							email: $("#edit_email").val()
						}
					}).always(function(respuesta_json){
						if (respuesta_json.isExito == 1) {
							edit_save();
						}
						else
							alert(respuesta_json.mensaje);
					});
				}
			}
		}
	}
}


/*
	Valida los campos para guardar el nuevo password del usuario
*/
function edit_validarCamposPassword() {
	if (!vacio($("#edit_newPassword").val(), $("#edit_newPassword").attr("placeholder"))) {
		if (!vacio($("#edit_confNewPassword").val(), $("#edit_confNewPassword").attr("placeholder"))) {
			if ($("#edit_newPassword").val() == $("#edit_confNewPassword").val()) {
				edit_savePassword();
			}
			else {
				alert("Las contraseñas son diferentes, vuelva a intentarlo");
				return false;
			}
		}
	}
}


/*
	Guarda los cambios del perfil de usuario
*/
function edit_save() {
	$("#_editEstado").val($("#edit_estado p").attr("data-value"));
	$("#_editCiudad").val($("#edit_ciudad p").attr("data-value"));
	$("#_editColonia").val($("#edit_colonia p").attr("data-value"));
	$("#_editCP").val(-1);
	if (parseInt($("#edit_colonia p").attr("data-value")) != -1)
		$("#_editCP").val($("#edit_colonia li.lista li[data-value='"+$("#edit_colonia p").attr("data-value")+"']").attr("data-cp"));
	$("#edit_password").val(md5Script($("#edit_password").val()));
		
		
	$("#btnGuardar").hide();
	$("#mensajeTemporal").show();
	$("#editarPerfil").css({cursor:"wait"});	
	
	
	$("#subirPerfilInmobiliaria").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			gotoURL("usuarios.php");
		}
	});
}


/*
	guarda la nueva contraseña del usuario
*/
function edit_savePassword() {
	$("#btnGuardar2").hide();
	$("#mensajeTemporal2").show();
	$("#editarPassword").css({cursor:"wait"});
	
	$.ajax({
		url: "lib_php/updUsuarioInmobiliaria.php",
		type: "POST",
		dataType: "json",
		data: {
			id: $("#idUsuario").val(),
			changePass: 1,
			password: md5Script($("#edit_newPassword").val())
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			$("#btnGuardar2").show();
			$("#mensajeTemporal2").hide();
			$("#editarPassword").css({cursor:"default"});
			
			alert(respuesta_json.mensaje);
			$("#edit_newPassword").val("");
			$("#edit_confNewPassword").val("");
		}
	});
}
/**/