// JavaScript Document


$(document).ready(function(){
	//evento al boton de buscar
	$(".misAnuncios_contenedorBuscador span").on({
		click:function () {
			misAnuncios_buscar();
		}
	});
	
	//evento al textfield para la busqueda
	$("#misAnuncios_buscador").on({
		keyup: function(evt) {
			template_displayUnicode(evt, misAnuncios_buscar);
		}
	});
});


/*
	Busca por titulo o nombre de colonia
*/
function misAnuncios_buscar() {
	if (!isVacio($("#misAnuncios_buscador").val()))
		gotoURL("misAnuncios.php?palabra="+$("#misAnuncios_buscador").val());
	else 
		gotoURL("misAnuncios.php");
}


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function misAnuncios_cerrarPopup() {
	$("#misAnuncios_popupRenovar").hide();
}


/*
	Muestra un popup con las opciones para renovar el inmueble
	
		* idInmueble:	Integer, es el id del inmueble a renovar
		* tipo:			String, es una cadena del tipo a realizar (publicar/renovar)
*/
function misAnuncios_popupRenovar(idInmueble, tipo) {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#misAnuncios_popupRenovar");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	$("#_enviar").attr("onclick", "gotoURL('pagoInmueble.php?idInmueble="+idInmueble+"');");
	$("#_tipo").text(tipo);
}


/*
	Elimina el anuncion al que se la da click en la "x", despues de la confirmación
	
		* idInmueble:	Integer, es el id del inmueble a borrar
*/
function misAnuncios_borrar(idInmueble) {
	if (confirm("¿Está seguro de eliminar el inmueble?")) {
		$.ajax({
			url: "lib_php/updInmueble.php",
			type: "POST",
			dataType: "json",
			data: {
				borrar: 1,
				id: idInmueble
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				gotoURL("misAnuncios.php");
			}
		});
	}
}


/*
	Desactiva el anuncio, esto es que regresa el limite vigencia al default: 2000-01-01
	
		* idInmueble:	Integer, es el id del inmueble a desactivar 
*/
function misAnuncios_desactivar(idInmueble) {
	if (confirm("¿Está seguro de desactivar el inmueble?")) {
		$.ajax({
			url: "lib_php/updInmueble.php",
			type: "POST",
			dataType: "json",
			data: {
				desactivar: 1,
				id: idInmueble
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				gotoURL("misAnuncios.php");
			}
		});
	}
}
/**/