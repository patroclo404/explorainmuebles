// JavaScript Document


$(document).ready(function(){
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#pago_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				pago_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	
	//agrega eventos en los selects ciudades, cuando se hace click en ellos y no se ha seleccionado un estando previamente
	$("#pago_ciudad").each(function(index){
		$(this).on({
			click: function() {
				var arrayEstados = Array("pago_estado");
				if (parseInt($("#"+arrayEstados[index]+" p").attr("data-value")) == -1) {
					template_errorSelectMunicipio();
				}
			}
		});
	});
	
	
	//agrega eventos en los selects colonias, cuando se hace click en ellos y no se ha seleccionado una ciudad previamente
	$("#pago_colonia").each(function(index){
		$(this).on({
			click: function() {
				var arrayCiudades = Array("pago_ciudad");
				if (parseInt($("#"+arrayCiudades[index]+" p").attr("data-value")) == -1) {
					template_errorSelectColonia();
				}
			}
		});
	});
	
	
	if (typeof _post_estado !== 'undefined') {
		$("#pago_estado p").attr("data-value", _post_estado);
		$("#pago_estado p").text($("#pago_estado li.lista li[data-value='"+_post_estado+"']").text());
		pago_actualizar_ciudad("pago_estado", _post_ciudad, _post_colonia);
	}
});


/*
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idCiudad:		[Integer], es el id de la ciudad ya precargado
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function pago_actualizar_ciudad(nomElemento, idCiudad, idColonia) {
	var indexEstado = -1;
	var arrayEstados = Array("pago_estado");
	var arrayCiudades = Array("pago_ciudad");
	var arrayColonias = Array("pago_colonia");
	
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
						pago_actualizar_colonia(objMunicipio.prop("id"));
					}
				});
				
				
				if ((idCiudad != null) && (idCiudad != -1)) {
					objMunicipio.find("li.lista li[data-value='"+idCiudad+"']").click();
					objMunicipio.find("li.lista").hide();
					
					if ((idColonia != null) && (idColonia != -1)) {
						pago_actualizar_colonia(objMunicipio.prop("id"), idColonia);
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
function pago_actualizar_colonia(nomElemento, idColonia) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("pago_ciudad");
	var arrayColonias = Array("pago_colonia");
	
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
					objColonia.find("li.lista ul").append("<li data-value='"+respuesta_json.datos[x].id+"' data-cp='"+respuesta_json.datos[x].cp+"' data-cp-value='"+respuesta_json.datos[x].cpValue+"'>"+respuesta_json.datos[x].nombre+"</li>");
				}
				
				objColonia.find("li.lista li").on({
					click: function() {
						objColonia.find("p").attr("data-value", $(this).attr("data-value"));
						objColonia.find("p").text($(this).text());
						
						
						$("#pago_cp").val($(this).attr("data-cp-value"));
					}
				});
				
				if ((idColonia != null) && (idColonia != -1)) {
					objColonia.find("li.lista li[data-value='"+idColonia+"']").click();
					objColonia.find("li.lista").hide();
					
					$("#pago_cp").val(objColonia.find("li.lista li[data-value='"+idColonia+"']").attr("data-cp-value"));
				}
			}
		});
	}
}


/*
	Valida los campos para realizar el pago por medio de conekta
	
		* idInmueble:	Integer, es el id del inmueble a realizar el pago
*/
function pago_validarCampos(idInmueble) {
	if (!isVacio($("#pago_nombre").val())) {
		if (!isVacio($("#pago_calleNumero").val())) {
			if (!isVacio($("#pago_cp").val())) {
				if (isEntero($("#pago_cp").val())) {
					if (!isVacio((parseInt($("#pago_estado p").attr("data-value")) == -1 ? "" : $("#pago_estado p").attr("data-value")))) {
						if (!isVacio((parseInt($("#pago_ciudad p").attr("data-value")) == -1 ? "" : $("#pago_ciudad p").attr("data-value")))) {
							if (!isVacio((parseInt($("#pago_colonia p").attr("data-value")) == -1 ? "" : $("#pago_colonia p").attr("data-value")))) {
								if (!isVacio($("#pago_telefono").val())) {
									$.ajax({
										url: "lib_php/updCarrito.php",
										type: "POST",
										dataType: "json",
										data: {
											addDireccion: 1,
											nombre: $("#pago_nombre").val(),
											calleNumero: $("#pago_calleNumero").val(),
											estado: $("#pago_estado p").attr("data-value"),
											estadoValue: $("#pago_estado p").text(),
											ciudad: $("#pago_ciudad p").attr("data-value"),
											ciudadValue: $("#pago_ciudad p").text(),
											colonia: $("#pago_colonia p").attr("data-value"),
											coloniaValue: $("#pago_colonia p").text(),
											cp: $("#pago_colonia li.lista li[data-value='"+$("#pago_colonia p").attr("data-value")+"']").attr("data-cp"),
											cpValue: $("#pago_cp").val(),
											telefono: $("#pago_telefono").val(),
											idInmueble: idInmueble
										}
									}).always(function(respuesta_json){
										if (respuesta_json.isExito == 1) {
											gotoURL("pagar_payment.php");
										}
									});
								}
								else {
									$("#template_alertPersonalizado td").text("El campo "+$("#pago_telefono").attr("placeholder")+" es obligatorio");
									template_alertPersonalizado();
								}
							}
							else {
								$("#template_alertPersonalizado td").text("El campo Colonia es obligatorio");
								template_alertPersonalizado();
							}
						}
						else {
							$("#template_alertPersonalizado td").text("El campo Ciudad es obligatorio");
							template_alertPersonalizado();
						}
					}
					else {
						$("#template_alertPersonalizado td").text("El campo Estado es obligatorio");
						template_alertPersonalizado();
					}
				}
				else {
					$("#template_alertPersonalizado td").text("El campo "+$("#pago_cp").attr("placeholder")+" debe ser númerico");
					template_alertPersonalizado();
				}
			}
			else {
				$("#template_alertPersonalizado td").text("El campo "+$("#pago_cp").attr("placeholder")+" es obligatorio");
				template_alertPersonalizado();
			}
		}
		else {
			$("#template_alertPersonalizado td").text("El campo "+$("#pago_calleNumero").attr("placeholder")+" es obligatorio");
			template_alertPersonalizado();
		}
	}
	else {
		$("#template_alertPersonalizado td").text("El campo "+$("#pago_nombre").attr("placeholder")+" es obligatorio");
		template_alertPersonalizado();
	}
}
/**/