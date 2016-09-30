// JavaScript Document

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 30;
		var textNombre = (positions[pos][1]).length > maxCadena ? ((positions[pos][1]).substr(0, (maxCadena - 3))+"...") : positions[pos][1];
		var textEmail = (positions[pos][2]).length > maxCadena ? ((positions[pos][2]).substr(0, (maxCadena - 3))+"...") : positions[pos][2];
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='../inmueble.php?id="+positions[pos][0]+"' target='_blank'>"+textNombre+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(1).find("td").eq(1).width()+"' style='text-align:center;'>"+positions[pos][2]+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(1).find("td").eq(2).width()+"' style='text-align:center;'><a href='javascript:abrirModificarCampos("+pos+");'>Renovar</a></td>"+
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
function anunciosVencidos_inicializarBotones() {
	consultarTuplasExistentes("updPositionsAnunciosVencidos.php", false);
	
	$("#limiteVigencia").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		selectMonths: true,
		selectYears: 30
	});
}


/*
	Cierra todos los poups de la interfaz actual
*/
function anunciosVencidos_cerrarPopUp(){
	$("#backImage").hide();
}


/*
	Muestra el primer poup que es el que tiene los datos a modificar o crear una nueva tupla
*/
function abrirModificarCampos(posit){
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		var objDiv = $("#backImage");

		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;

		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		if(posit >= 0){
			pos_comp = posit;
			var fechaActual = new Date();
			fechaActual.setDate(fechaActual.getDate() + 30);//agrega 30 dias a la fecha actual
			
			$("#tituloEmergente").text("Nuevo Límite de Vigencia");
			$("#limiteVigencia").pickadate().pickadate("picker").set("select", [fechaActual.getFullYear(), fechaActual.getMonth(), fechaActual.getDate()]);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Límite de Vigencia");
			$("#limiteVigencia").val("");
		}
	}
	else{
		bool_borrar=false;
	}
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	save();
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


	$.ajax({
		url: "lib_php/updAnunciosVencidos.php",
		type: "POST",
		dataType: "json",
		data: {
			id: id,
			limiteVigencia: $("#limiteVigencia").val()
		}
	}).always(function(respuesta_json){
		$("#btnGuardar").show();
		$("#mensajeTemporal").hide();
		$("#backImage").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(anunciosVencidos_cerrarPopUp);
		consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
	});
}
/**/