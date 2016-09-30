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
/**/