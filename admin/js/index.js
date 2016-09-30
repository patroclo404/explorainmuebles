// JavaScript Document


/*
	Carga e inicializa las funciones principales para el index
*/
$(document).ready(function(){
	$("#email").on({
		keyup: function(evento){
			template_displayUnicode(evento, index_password_focus);
		}
	});
	
	$("#password").on({
		keyup: function(evento){
			template_displayUnicode(evento, index_validaCampos);
		}
	});
});


/*
	focus de password
*/
function index_password_focus() {
	$("#password").focus();
}


/*
	Valida los campos y enseguida realiza el login al sistema
*/
function index_validaCampos () {
	if (!vacio($("#email").val(), $("#email").attr("placeholder"))) {
		if (correoValido($("#email").val())) {
			if (!vacio($("#password").val(), $("#password").attr("placeholder"))) {
				$.ajax({
					url: "lib_php/login.php",
					type: "POST",
					dataType: "json",
					data: {
						email: $("#email").val(),
						password: md5Script($("#password").val())
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1)
						gotoURL("menu.php");
					else
						$("#msg").html(respuesta_json.mensaje);
				});
			}
		}
	}
}