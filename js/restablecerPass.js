// JavaScript Document

/*
	Valida los campos para restablecer el password del usuario con la clave
*/
function validarCampos() {
	if (!vacio($("#restablecer_pass").val(), $("#restablecer_pass").attr("placeholder"))) {
		if (!vacio($("#confRestablecer_pass").val(), $("#confRestablecer_pass").attr("placeholder"))) {
			if ($("#restablecer_pass").val() == $("#confRestablecer_pass").val()) {
				$.ajax({
					url: "lib_php/updUsuario.php",
					type: "POST",
					dataType: "json",
					data: {
						restablecerPass: 1,
						codigo: $("#restablecer_pass").attr("data-codigo"),
						newPass: $("#restablecer_pass").val()
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						$("#template_alertPersonalizado td").text("Te hemos enviado un email con tu contraseña.");
						template_alertPersonalizado();
						$("#restablecer_pass").val("");
						$("#confRestablecer_pass").val("");
					}
					else
						alert(respuesta_json.mensaje);
				});
			}
			else
				alert("Las contraseñas son diferentes, vuelva a intentarlo.");
		}
	}
}