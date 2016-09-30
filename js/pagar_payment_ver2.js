// JavaScript Document
var conektaSuccessResponseHandler;
var conektaErrorResponseHandler;


$(document).ready(function(){
	$(".card-errors").hide();
	
	Conekta.setPublishableKey('key_FrMLJr9QBWo3wrmzquxC9MA');//Llave pública
	
	
	//evento al cambiar la forma de pago
	$("input[name='formaPago']").on({
		change: function() {
			if ($(this).val() != "card")
				$(".pagar_payment_cuerpo table tr[data-name='data_tarjeta']").hide();
			else
				$(".pagar_payment_cuerpo table tr[data-name='data_tarjeta']").show();
		}
	});
});


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function pagarPayment_cerrarPopup() {
	$("#pagarPayment_popupCVC").hide();
}


/*
	Muestra un popup que muestra una imagen para indicar donde obtener el cvc de tu tarjeta de credito
*/
function pagarPayment_popupCVC() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#pagarPayment_popupCVC");

	lPos = ($(window).width() - objDiv.innerWidth())/2;
	tPos = ($(window).height() - objDiv.innerHeight())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}

/*
	valida todos los campos de la tarjeta
*/
function validarCampos() {
	$(".card-errors").text("");
	$(".card-errors").hide();
	
	if ($("input[name='formaPago']:checked").val() == "card") {//validacion de pago con tarjeta
		if (!isVacio($("#pag_nombre").val())) {
			if (!isVacio($("#pag_tarjeta").val())) {
				if (isEntero($("#pag_tarjeta").val())) {
					if (!isVacio($("#pag_cvc").val())) {
						if (isEntero($("#pag_cvc").val())) {
							if (!isVacio($("#pag_mes").val())) {
								if (isEntero($("#pag_mes").val())) {
									if (!isVacio($("#pag_anio").val())) {
										if (isEntero($("#pag_anio").val())) {
											_fecha = formateaFecha("01/"+$("#pag_mes").val()+"/"+$("#pag_anio").val());
											
											if (_fecha.length == 10) {
												$("#btnGuardar").hide();
												$("#mensajeTemporal").show();
												$(".pagar_payment_cuerpo").css({cursor:"wait"});
												
												Conekta.token.create($("#card-form"), conektaSuccessResponseHandler, conektaErrorResponseHandler);
											}
											else {
												$("#template_alertPersonalizado td").text("Fecha de expiración invalida.");
												template_alertPersonalizado();
											}
										}
										else {
											$("#template_alertPersonalizado td").text("El campo "+$("#pag_anio").attr("placeholder")+" debe ser númerico");
											template_alertPersonalizado();
										}
									}
									else {
										$("#template_alertPersonalizado td").text("El campo "+$("#pag_anio").attr("placeholder")+" es obligatorio");
										template_alertPersonalizado();
									}
								}
								else {
									$("#template_alertPersonalizado td").text("El campo "+$("#pag_mes").attr("placeholder")+" debe ser númerico");
									template_alertPersonalizado();
								}
							}
							else {
								$("#template_alertPersonalizado td").text("El campo "+$("#pag_mes").attr("placeholder")+" es obligatorio");
								template_alertPersonalizado();
							}
						}
						else {
							$("#template_alertPersonalizado td").text("El campo "+$("#pag_cvc").attr("placeholder")+" debe ser númerico");
							template_alertPersonalizado();
						}
					}
					else {
						$("#template_alertPersonalizado td").text("El campo "+$("#pag_cvc").attr("placeholder")+" es obligatorio");
						template_alertPersonalizado();
					}
				}
				else {
					$("#template_alertPersonalizado td").text("El campo "+$("#pag_tarjeta").attr("placeholder")+" debe ser númerico");
					template_alertPersonalizado();
				}
			}
			else {
				$("#template_alertPersonalizado td").text("El campo "+$("#pag_tarjeta").attr("placeholder")+" es obligatorio");
				template_alertPersonalizado();
			}
		}
		else {
			$("#template_alertPersonalizado td").text("El campo "+$("#pag_nombre").attr("placeholder")+" es obligatorio");
			template_alertPersonalizado();
		}
	}
	else {//pago mediante oxxo
		conektaSuccessResponseHandler({id: -1});
	}
}


/*
	Manda llamar a esta funcion, cuando los datos de la tarjeta fueron validos y no se produjo un error en el token
	
	sandbox: tok_test_visa_4242
*/
conektaSuccessResponseHandler = function(token) {
	$.ajax({
		url: "lib_php/updCarrito.php",
		type: "POST",
		dataType: "json",
		data: {
			addCharge: 1,
			token: token.id,
			formaPago: $("input[name='formaPago']:checked").val(),
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			switch($("input[name='formaPago']:checked").val()) {
				case "card":
					gotoURL("completed.php?reference_id="+respuesta_json.reference_id);
					break;
				case "oxxo":
					gotoURL("pagar_efectivo.php?reference_id="+respuesta_json.reference_id+"&barcode="+respuesta_json.barcode+"&total="+respuesta_json.total);
					break;
			}
		}
		else {
			$(".card-errors").text(respuesta_json.mensaje);
			$(".card-errors").show();
		}
	});
};


/*
	Manda llamar a esta funcion, cuando se producto un error al crear el token o los datos de la tarjeta son invalidos
*/
conektaErrorResponseHandler = function(response) {
	$(".card-errors").text(response.message);
	$(".card-errors").show();
	
	$("#btnGuardar").show();
	$("#mensajeTemporal").hide();
	$(".pagar_payment_cuerpo").css({cursor:"default"});
};
/**/