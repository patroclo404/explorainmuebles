// JavaScript Document


$(document).ready(function(){
	//evento al boton de buscar
	$(".favoritos_contenedorBuscador span").on({
		click:function () {
			favoritos_buscar();
		}
	});
	
	//evento al textfield para la busqueda
	$("#favorito_buscador").on({
		keyup: function(evt) {
			template_displayUnicode(evt, favoritos_buscar);
		}
	});
});


/*
	Busca por titulo o nombre de colonia
*/
function favoritos_buscar() {
	if (!isVacio($("#favorito_buscador").val()))
		gotoURL("favoritos.php?palabra="+$("#favorito_buscador").val());
	else 
		gotoURL("favoritos.php");
}
/**/