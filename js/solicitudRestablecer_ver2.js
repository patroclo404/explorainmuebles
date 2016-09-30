// JavaScript Document

/*
	Valida los campos para enviar un email para recuperacion de password
*/
function validarCampos() {
	if (!vacio($("#solicitud_email").val(), $("#solicitud_email").attr("placeholder"))) {
		if (correoValido($("#solicitud_email").val())) {
			$.ajax({
				url: "lib_php/updUsuario.php",
				type: "POST",
				dataType: "json",
				data: {
					solicitudRestablecer: 1,
					email: $("#solicitud_email").val()
				}
			}).always(function(respuesta_json){
				if (respuesta_json.isExito == 1) {
					$("#template_alertPersonalizado td").html("Te hemos enviado un email con instrucciones para<br />que puedas recuperar tu contraseña.<br />Recuerda revisar tu correo no deseado");
					template_alertPersonalizado();
					$("#solicitud_email").val("");
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}