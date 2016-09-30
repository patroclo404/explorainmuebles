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


/*
	Realiza el pago inmediatamente para el inmueble
	
		* idInmueble:	Integer, es el id del inmueble
*/
function opcionPago_realizarPago(idInmueble) {
	$.ajax({
		url: "lib_php/updOpcionPagoInmueble.php",
		type: "POST",
		dataType: "json",
		data: {
			nuevo: 1,
			idInmueble: idInmueble
		}
	}).always(function(respuesta_text){
		gotoURL('misAnuncios.php');
	});
}
/**/