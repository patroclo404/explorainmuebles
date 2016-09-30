// JavaScript Document


/*
	Valida los campos para guardar los datos de perfil de usuario
*/
function chgPassword_validarCampos() {
	if (!vacio($("#cambiarPassword_passActual").val(), $("#cambiarPassword_passActual").attr("placeholder"))) {
		if (!vacio($("#cambiarPassword_newPass").val(), $("#cambiarPassword_newPass").attr("placeholder"))) {
			if (!vacio($("#cambiarPassword_confNewPass").val(), $("#cambiarPassword_confNewPass").attr("placeholder"))) {
				if ($("#cambiarPassword_newPass").val() == $("#cambiarPassword_confNewPass").val()) {
					chgPassword_save();
				}
				else
					alert("Las contraseñas son diferentes, vuelva a intentarlo");
			}
		}
	}
}


/*
	Guarda el cambio de password
*/
function chgPassword_save() {
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