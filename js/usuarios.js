// JavaScript Document


/*
	Confirma la eliminacion de usuarios y realiza la eliminacion del usuario
*/
function usuario_deleteUsuario(idUsuario) {
	if (confirm("¿Está seguro de eliminar el usuario?")) {
		$.ajax({
			url: "lib_php/updUsuarioInmobiliaria.php",
			type: "POST",
			dataType: "json",
			data: {
				id: idUsuario,
				borrar: 1
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1)
				gotoURL("usuarios.php");
		});
	}
}
/**/