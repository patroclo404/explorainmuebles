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
/**/