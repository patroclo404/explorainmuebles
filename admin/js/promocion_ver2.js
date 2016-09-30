// JavaScript Document
var minimoConekta = 30;

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		
		var maxCadena = 30;
		var textTitulo = (positions[pos][2]).length > maxCadena ? ((positions[pos][2]).substr(0, (maxCadena - 3))+"...") : positions[pos][2];
		textTitulo = textTitulo == "" ? "Sin título" : textTitulo;
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textTitulo+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(1).find("td").eq(1).width()+"' style='text-align:center;'>"+positions[pos][1]+"</td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function promocion_inicializarBotones() {
	consultarTuplasExistentes("updPositionsPromocion.php", false);
}


/*
	Cierra todos los poups de la interfaz actual
*/
function promocion_cerrarPopUp(){
	$("#backImage").hide();
}


/*
	Muestra el primer poup que es el que tiene los datos a modificar o crear una nueva tupla
*/
function abrirModificarCampos(posit){
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		var objDiv = $("#backImage");
		
		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Precio del Anuncio");
			$("#precio").val(positions[posit][1]);
			$("#promocion").val(positions[posit][2]);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Precio del Anuncio");
			objDiv.find("input").val("");
		}


		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;

		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
	}
	else{
		bool_borrar=false;
		if (confirm('\u00BFEsta seguro de eliminar el precio del anuncio: "'+positions[posit][1]+'"?')) {
			datos = {
				id: positions[posit][0],
				borrar: 1
			};
			
			$.ajax({
				url: "lib_php/updPromocion.php",
				type: "POST",
				dataType: "json",
				data: datos
			}).always(function(respuesta_json){
				$("#resultados").text(respuesta_json.mensaje);
				
				if (respuesta_json.isExito == 1) {
					principalCerrarPopUp(promocion_cerrarPopUp);
					consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	if (!vacio($("#precio").val(), $("#precio").attr("placeholder"))) {
		if (flotante($("#precio").val(), $("#precio").attr("placeholder"))) {
			if (parseFloat($("#precio").val()) >= minimoConekta) {
				save();
			}
			else
				alert("EL precio mínimo es: $"+minimoConekta);
		}
	}
}


/*
	Manda los campos para guardar o modificar la tupla en la base de datos
*/
function save(){
	id = -1;
	
	if (pos_comp != -1)//modificar
		id = positions[pos_comp][0];
		
	$("#btnGuardar").hide();
	$("#mensajeTemporal").show();
	$("#backImage").css({cursor:"wait"});
	
	
	datos = {
		id: id,
		precio: $("#precio").val(),
		promocion: $("#promocion").val()
	};
	
	$.ajax({
		url: "lib_php/updPromocion.php",
		type: "POST",
		dataType: "json",
		data: datos
	}).always(function(respuesta_json){
		$("#btnGuardar").show();
		$("#mensajeTemporal").hide();
		$("#backImage").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(promocion_cerrarPopUp);
		consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
	});
}
/**/