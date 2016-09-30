// JavaScript Document


/*
*/
function contacto_validarCampos() {
	if (!vacio($("#contacto_nombre").val(), $("#contacto_nombre").attr("placeholder"))) {
		if (!vacio($("#contacto_email").val(), $("#contacto_email").attr("placeholder"))) {
			if (correoValido($("#contacto_email").val())) {
				$.ajax({
					url: "lib_php/emailContacto.php",
					type: "POST",
					dataType: "text",
					data: {
						nombre: $("#contacto_nombre").val(),
						email: $("#contacto_email").val(),
						telefono: $("#contacto_telefono").val(),
						mensaje: $("#contacto_mensaje").val()
					}
				}).always(function(){
					alert("Tus datos han sido enviados, pronto nos comunicaremos contigo.");
					$("#contacto_nombre").val("");
					$("#contacto_email").val("");
					$("#contacto_telefono").val("");
					$("#contacto_mensaje").val("");
				});
			}
		}
	}
}
/**/