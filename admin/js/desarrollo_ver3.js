// JavaScript Document
var map;
var marker;
var desarrollo_positions = Array();
var desarrollo_pos_comp = -1;
var urlArchivos = "../images/images/";

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 30;
		var textTitulo = (positions[pos][1]).length > maxCadena ? ((positions[pos][1]).substr(0, (maxCadena - 3))+"...") : positions[pos][1];
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textTitulo+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(1).find("td").eq(1).width()+"' style='text-align:center;'>"+$("#tipo option[value='"+positions[pos][2]+"']").text()+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(1).find("td").eq(2).width()+"' style='text-align:center;'><a href='javascript:desarrollo_abrirModificarImagenes("+pos+");'>Imágenes</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
}


/*
	Muestra las imagenes del desarrollo en el div "contenedorDesarrolloImagenes"
	
		* posit:		Integer, es el id del desarrollo
		* datos_json:	Array JSON, es un arreglo de datos decodificado con JSON
*/
function desarrollo_imagenDesarrolloCampos(posit, datos_json) {
	var contenedorDatos = $("#contenedorDesarrolloImagenes");
	contenedorDatos.html("");
	
	desarrollo_positions = Array();
	desarrollo_pos_comp = -1;
			
	for (var x = 0; x < datos_json.length; x++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		divImagen.innerHTML =
			"<table>"+
				"<tr>"+
					"<td style='text-align:left;'><a href='javascript:desarrollo_subirImagen("+x+");'>Modificar</a></td>"+
					"<td width='"+$("#desarrollo_abrirModificarImagenes table tr").eq(1).find("td").eq(1).width()+"' style='text-align:center;'><a href='"+urlArchivos+datos_json[x].campo2+"' target='_blank'>Imágen "+(x + 1)+"</a></td>"+
					"<td width='"+$("#desarrollo_abrirModificarImagenes table tr").eq(1).find("td").eq(2).width()+"' style='text-align:center;'>"+(datos_json[x].campo3 == 0 ? "No" : "Si")+"</td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; desarrollo_abrirModificarImagenes("+posit+", "+datos_json[x].campo1+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>";
			
		contenedorDatos.append(divImagen);
		desarrollo_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2, datos_json[x].campo3));
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function desarrollo_inicializarBotones() {
	consultarTuplasExistentes("updPositionsDesarrollo.php", true);
	
	
	$("#latitud,#longitud").on({
		click: function() {
			desarrollo_obtenerCoordenadas();
		}
	});
}


/*
	Cierra todos los poups de la interfaz actual
*/
function desarrollo_cerrarPopUp(){
	$("#backImage").hide();
	$("#desarrollo_abrirModificarImagenes").hide();
}


/*
	Cierra todos los poups de la interfaz actual de nivel 2
*/
function desarrollo_cerrarPopUp2(){
	$("#mascaraPrincipalNivel2").hide();
	$("#desarrollo_obtenerCoordenadas").hide();
	$("#desarrollo_subirImagen").hide();
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
			$("#tituloEmergente").text("Modificar Desarrollo");
			$("#titulo").val(positions[pos_comp][1]);
			$("#tipo").val(positions[pos_comp][2]);
			$("#entrega").val(positions[pos_comp][3]);
			$("#unidades").val(positions[pos_comp][4]);
			$("#latitud").val(positions[pos_comp][5]);
			$("#longitud").val(positions[pos_comp][6]);
			$("#descripcion").val(positions[pos_comp][7]);
			$("#inmobiliaria").val(positions[pos_comp][8]);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Desarrollo");
			$("#titulo").val("");
			$("#tipo").val(-1);
			$("#entrega").val("");
			$("#unidades").val("");
			$("#latitud").val("");
			$("#longitud").val("");
			$("#descripcion").val("");
			$("#inmobiliaria").val(-1);
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
		if (confirm('\u00BFEsta seguro de eliminar el desarrollo: "'+positions[posit][1]+'"?')) {
			datos = {
				id: positions[posit][0],
				borrar: 1
			};
			
			$.ajax({
				url: "lib_php/updDesarrollo.php",
				type: "POST",
				dataType: "json",
				data: datos
			}).always(function(respuesta_json){
				$("#resultados").text(respuesta_json.mensaje);
				
				if (respuesta_json.isExito == 1) {
					principalCerrarPopUp(desarrollo_cerrarPopUp);
					consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}


/*
	Muestra un popup con las imagenes del desarrollo
	
		* posit:		Integer, es el id del desarrollo
		* idImagen:		Integer, es el id de la imagen
*/
function desarrollo_abrirModificarImagenes(posit, idImagen) {
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		
		var objDiv = $("#desarrollo_abrirModificarImagenes");
	
		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;
	
		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		pos_comp = posit;
		
		$.ajax({
			url: "lib_php/updDesarrolloImagen.php",
			type: "POST",
			dataType: "json",
			data:{
				id: positions[posit][0]
			}
		}).always(function(respuesta_json){
			desarrollo_imagenDesarrolloCampos(posit, respuesta_json.datos);
		});
	}
	else {
		bool_borrar=false;
		if (confirm('\u00BFEsta seguro de eliminar la imágen?')) {
			$.ajax({
				url: "lib_php/updDesarrolloImagen.php",
				type: "POST",
				dataType: "json",
				data: {
					id: positions[posit][0],
					borrar: 1,
					idImagen: idImagen
				}
			}).always(function(respuesta_json){
				if (respuesta_json.isExito == 1)
					desarrollo_imagenDesarrolloCampos(posit, respuesta_json.datos);
				else {
					$("#resultados").text(respuesta_json.mensaje);
					alert(respuesta_json.mensaje);
				}
			});
		}
	}
}


/*
	Muestra el popup para modificar o asignar las coordenadas en el mapa
*/
function desarrollo_obtenerCoordenadas(){
	$("#mascaraPrincipalNivel2").show();
	
	var objDiv = $("#desarrollo_obtenerCoordenadas");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	
	tempCenter = new google.maps.LatLng(20.650118, -103.422227);
	
	if (($("#latitud").val() != "") && ($("#longitud").val() != "")) {
		tempCenter = new google.maps.LatLng($("#latitud").val(), $("#longitud").val());
	}
	
	//define el google maps
	var mapaGoogle = document.getElementById("contenedorDesarrolloMapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 16,
		mapMaker: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	map = new google.maps.Map(mapaGoogle, mapOptions);
	
	google.maps.event.addListener(map, "click", mapDefinirMarca);
	
	if (($("#latitud").val() != "") && ($("#longitud").val() != "")) {
		marker = new google.maps.Marker({
			position: new google.maps.LatLng($("#latitud").val(), $("#longitud").val()),
			map: map,
			icon: "../images/marcador3.png"
		});
	}
}


/*
	Muestra el popup para modificar o crear una imagen
	
		* posImagen:	Integer, es la posicion del array "desarrollo_positions" donde se encuentran los datos de las imagenes
*/
function desarrollo_subirImagen(posImagen){
	$("#mascaraPrincipalNivel2").show();
	
	var objDiv = $("#desarrollo_subirImagen");
	$("#imagen").val("");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	desarrollo_pos_comp = posImagen;
	
	if (desarrollo_pos_comp != -1) {
		$("#tituloEmergenteImagenes").html("Modificar Imágen");
		$("#imagenPrincipal").prop("checked", (desarrollo_positions[desarrollo_pos_comp][2] == 1 ? true : false));
	}
	else {
		$("#tituloEmergenteImagenes").html("Nueva Imágen");
		$("#imagenPrincipal").prop("checked", false);
	}
}


/*
	Asigna una marca en el mapa, ademas de los campos de latitud y longitud
	
		* evt:	Event, es el evento asignado en el mapa para llamar esta funcion
*/
function mapDefinirMarca(evt) {
	$("#latitud").val(evt.latLng.lat());
	$("#longitud").val(evt.latLng.lng());
	
	if (typeof marker !== 'undefined')
		marker.setMap(null);
	
	marker = new google.maps.Marker({
		position: evt.latLng,
		map: map,
		icon: "../images/marcador3.png"
	});
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	if (!vacio($("#titulo").val(), $("#titulo").attr("placeholder"))) {
		if (!vacio(($("#tipo").val() != -1 ? $("#tipo").val() : ""), $("#tipo option[value='-1']").text())) {
			if (!vacio($("#latitud").val(), $("#latitud").attr("placeholder"))) {
				if (!vacio($("#descripcion").val(), $("#descripcion").attr("placeholder"))) {
					if (!vacio(($("#inmobiliaria").val() != -1 ? $("#inmobiliaria").val() : ""), $("#inmobiliaria option[value='-1']").text())) {
						if (!isVacio($("#unidades").val())) {
							if (!entero($("#unidades").val(), $("#unidades").attr("placeholder"))) {
								return false;
							}
						}
						
						save();
					}
				}
			}
		}
	}
}


/*
	Valida los campos para la posicion del inmueble en el mapa
*/
function validarCampos2() {
	desarrollo_cerrarPopUp2();
}


/*
	Valida los campos para crear o modificar la imagen
*/
function validarCampos3 () {
	var id = desarrollo_pos_comp;
	var continua = true;
		
	if (id == -1) {
		continua = false;
			
		if (!vacio($("#imagen").val(), "Imagen")) {
			continua = true;
		}
	}
		
	if (continua) {
		saveImagen();
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
	
	
	var datos = {
		id: id,
		titulo: $("#titulo").val(),
		tipo: $("#tipo").val(),
		entrega: $("#entrega").val(),
		unidades: $("#unidades").val(),
		latitud: $("#latitud").val(),
		longitud: $("#longitud").val(),
		descripcion: $("#descripcion").val(),
		inmobiliaria: $("#inmobiliaria").val()
	};


	$.ajax({
		url: "lib_php/updDesarrollo.php",
		type: "POST",
		dataType: "json",
		data: datos
	}).always(function(respuesta_json){
		$("#btnGuardar").show();
		$("#mensajeTemporal").hide();
		$("#backImage").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(desarrollo_cerrarPopUp);
		consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
	});
}


/*
	Guarda la imagen y vuelve a refrescar la consulta
*/
function saveImagen() {
	$("#btnGuardar3").hide();
	$("#mensajeTemporal3").show();
	$("#desarrollo_subirImagen").css({cursor:"wait"});
	
	
	$("#idDesarrollo").val(positions[pos_comp][0]);
	if (desarrollo_pos_comp == -1) {
		$("#nuevo").val("1");
		$("#modificar").val("0");
		$("#idImagen").val("0");
	}
	else {
		$("#nuevo").val("0");
		$("#modificar").val("1");
		$("#idImagen").val(desarrollo_positions[desarrollo_pos_comp][0]);
	}
	
	
	$("#subirImagen").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar3").show();
			$("#mensajeTemporal3").hide();
			$("#desarrollo_subirImagen").css({cursor:"default"});
			
			desarrollo_cerrarPopUp2();
			desarrollo_imagenDesarrolloCampos(pos_comp, respuesta_json.datos);
		}
	});
}
/**/