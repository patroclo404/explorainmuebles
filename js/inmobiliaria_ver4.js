// JavaScript Document


$(document).ready(function(){
	template_addListener_busquedas(inmobiliaria_fcnBuscar);
});


/*
	funcion a realizar cuando se da click en el boton de buscar
*/
function inmobiliaria_fcnBuscar() {
	var datos = template_busquedas_getData();
	var datos2 = datos;
	var params = "";
	
	for (var key in datos) {
		if ((key == "transaccion") || (key == "tipoInmueble") || (key == "estado") || (key == "ciudad")) {
			if (params == "")
				params += "?"
			else
				params += "&";
				
			params += key+"="+datos[key];
		}
	}
	
	
	params2 =
		(datos2["transaccion"] == 1 ? "renta" : (datos2["transaccion"] == 2 ? "venta" : "renta-vacacional"))+
		"/"+($("#template_busqueda_tipoInmueble p").text() != "" ? ($("#template_busqueda_tipoInmueble p").text().toLowerCase()+($("#template_busqueda_tipoInmueble p").attr("data-value") != 6 ? "s" : "es")) : "todos-los-tipos")+
		"/"+($("#template_busqueda_estado p").text() != "" ? $("#template_busqueda_estado p").text() : "todo-mexico")+
		"/"+($("#template_busqueda_municipio p").text() != "" ? $("#template_busqueda_municipio p").text() : "todas-las-ciudades");
		
	
	$.ajax({
		url: "lib_php/updFiltros.php",
		type: "POST",
		dataType: "json",
		data: {
			set: 1,
			parametros: datos2
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1)
			gotoURL(""+params2);
	});
	//gotoURL("catalogo.php"+params);
}

/*
 Valida los campos para enviar informacion al vendedor
 */
function inmueble_validarContacto() {
	if (!vacio($("#contacto_nombre").val(), $("#contacto_nombre").attr("placeholder"))) {
		if (!vacio($("#contacto_email").val(), $("#contacto_email").attr("placeholder"))) {
			if (correoValido($("#contacto_email").val())) {
				if (!vacio($("#contacto_mensaje").val(), $("#contacto_mensaje").attr("placeholder"))) {
					$.ajax({
						url: "lib_php/emailVendedor.php",
						type: "POST",
						dataType: "text",
						data: {
							nombre: $("#contacto_nombre").val(),
							email: $("#contacto_email").val(),
							telefono: $("#contacto_telefono").val(),
							mensaje: $("#contacto_mensaje").val(),
							inmueble: $("table.inmueble_contacto span.btnEnviar").attr("data-inmueble")
						}
					}).always(function(){
						$("#template_alertPersonalizado td").text("Hemos enviado un mensaje con tus datos al anunciante.");
						template_alertPersonalizado();
						$("#contacto_nombre").val("");
						$("#contacto_email").val("");
						$("#contacto_telefono").val("");
						$("#contacto_mensaje").val("");
					});
				}
			}
		}
	}
}

function gotoComment(){
	$(document).scrollTop( $(".inmueble_contacto").offset().top );
}