// JavaScript Document

/*
	Envia un email al usuario respecto al ultipo paso para anunciar su inmueble
	
		* id:	Integer, es el id del inmueble
*/
function pagarMasTarde(id) {
	$.ajax({
		url: "lib_php/emailInmueblePendiente.php",
		type: "POST",
		dataType: "text",
		data: {
			id: id
		}
	}).always(function(respuesta_text){
		gotoURL('misAnuncios.php');
	});
}