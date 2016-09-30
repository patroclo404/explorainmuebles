// JavaScript Document


/*
	Redefino la funcion de consultar tuplas del template.js
	
	Consulta las tuplas existentes en la base de datos y las muestra  en el div "contenedorConsulta"
	
		* nombrePHPCons:	String, es el nombre del php a consultar los campos
		* isBorrar:			Boolean, si esta en true entonces se pueden borrar la tuplas, false no se pueden eliminar.
							Por default es true
		* arrayCamposCons:	[Array String], es un arreglo de tipo "String", con los nombres de los campos para realizar
								la consulta. Por default es NULL (sin campos)
*/
function consultarTuplasExistentes(nombrePHPCons, isBorrar, arrayCamposCons) {
	isBorrar = isBorrar == null ? false : isBorrar;
	nombrePHPConsultar = nombrePHPCons;
	isBorrarTuplas = isBorrar;
	arrayCamposConsulta = arrayCamposCons == null ? {} : arrayCamposCons;
	
	
	$("#contenedorConsulta").html("Cargando...");
	
	var ajax = $.ajax({
		url: "lib_php/"+nombrePHPConsultar,
		type: "POST",
		dataType: "json",
		data: arrayCamposConsulta
	}).always(function(respuesta_json){
		positions = respuesta_json.datos;
		mostrarCamposExistentes();//definir en cada js de la interfaz
	});
}

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		
		var maxCadena = 30;
		var textTitulo = (positions[pos].titulo).length > maxCadena ? ((positions[pos].titulo).substr(0, (maxCadena - 3))+"...") : positions[pos].titulo;
		var textNombre = (positions[pos].usuarioNombre).length > maxCadena ? ((positions[pos].usuarioNombre).substr(0, (maxCadena - 3))+"...") : positions[pos].usuarioNombre;
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='../inmueble.php?id="+positions[pos][0]+"' target='_blank'>"+textTitulo+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(1).width()+"' style='text-align:center;'>"+textNombre+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(2).width()+"' style='text-align:center;'>"+positions[pos].id+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(3).width()+"' style='text-align:center;'>"+positions[pos].limiteVigencia+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(4).width()+"' style='text-align:center;'><a href='javascript:abrirModificarCampos("+pos+");'>Renovar</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
	
	if (positions.length > 0) {
		$("#_numAnunciosVencidos").text(positions.length);
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function anunciosVencidos_inicializarBotones() {
	$("#template_celdaBuscador").find("td").eq(0).append("<p style='padding-top:15px; padding-bottom:15px;'><a href='anunciosVencidos.php'>Total de anuncios vencidos: <span id='_numAnunciosVencidos'>0</span></a></p>");
	
	
	arrayCamposConsulta = template_getURL_keyValue();
	arrayCamposConsulta["palabra"] = $("#template_buscador").val();
	
	
	consultarTuplasExistentes("updPositionsAnunciosVencidos.php", false, arrayCamposConsulta);
	$("#template_buscador").attr("placeholder", "Buscar por título");
	
	$("#template_buscador").on({
		keyup: function(ev) {
			var unicode = ev.keyCode;
	
			if (unicode == 13) {
				arrayCamposConsulta = template_getURL_keyValue();
				arrayCamposConsulta["palabra"] = $("#template_buscador").val();
				
				consultarTuplasExistentes("updPositionsAnunciosVencidos.php", false, arrayCamposConsulta);
			}
		}
	});
	
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
		id = positions[pos_comp].id;
		
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