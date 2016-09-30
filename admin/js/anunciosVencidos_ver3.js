// JavaScript Document
var urlArchivos = "../images/images/";


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
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(1).width()+"' style='text-align:center;'><a href='javascript:anunciosVencidos_abrirModificarUsuario("+pos+");'>"+textNombre+"</a></td>"+
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
	$("#template_buscador").attr("placeholder", "Buscar por título o anunciante");
	
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
	
	$("#usu_estado").on({
		change: function() {
			if ($(this).val() != -1) {
				$.ajax({
					url: "lib_php/consDireccion.php",
					type: "POST",
					dataType: "json",
					data: {
						consCiudad: 1,
						estado: $(this).val()
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						$("#usu_ciudad").prop("disabled", false);
						$("#usu_ciudad option[value!='-1']").remove();
						$("#usu_colonia").val("-1");
						$("#usu_colonia").prop("disabled", true);
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#usu_ciudad").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#usu_ciudad").val("-1");
				$("#usu_colonia").val("-1");
				
				$("#usu_ciudad").prop("disabled", true);
				$("#usu_colonia").prop("disabled", true);
			}
		}
	});
	
	
	$("#usu_ciudad").on({
		change: function() {
			if ($(this).val() != -1) {
				$.ajax({
					url: "lib_php/consDireccion.php",
					type: "POST",
					dataType: "json",
					data: {
						consColonia: 1,
						ciudad: $(this).val()
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						$("#usu_colonia").prop("disabled", false);
						$("#usu_colonia option[value!='-1']").remove();
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#usu_colonia").append("<option value='"+respuesta_json.datos[x].id+"' data-cp='"+respuesta_json.datos[x].cp+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#usu_colonia").val("-1");
				$("#usu_colonia").prop("disabled", true);
			}
		}
	});
}


/*
	Cierra todos los poups de la interfaz actual
*/
function anunciosVencidos_cerrarPopUp(){
	$("#backImage").hide();
	$("#anunciosVencidos_abrirModificarUsuario").hide();
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
	Muestra un popup con los datos del usuario propietario del inmueble
	
		* posit:		Integer, es el id del inmueble
*/
function anunciosVencidos_abrirModificarUsuario(posit, idImagen) {
	$("#mascaraPrincipal").show();
	
	var objDiv = $("#anunciosVencidos_abrirModificarUsuario");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	pos_comp = posit;
	objDiv.find("input.ObjFocusBlur").val("");
	objDiv.find("select").val("-1");
	objDiv.find("a").hide();
	
	$.ajax({
		url: "lib_php/updPositionsUsuario.php",
		type: "POST",
		dataType: "json",
		data:{
			idUsuario: positions[posit].usuario
		}
	}).always(function(respuesta_json){
		_tempDatos = respuesta_json.datos[0];
		
		$("#idUsuario").val(_tempDatos.id);
		$("#usu_nombre").val(_tempDatos.nombre);
		$("#usu_email").val(_tempDatos.email);
		$("#usu_FBid").val(_tempDatos.FBId);
		$("input[name='sexo'][value='"+_tempDatos.sexo+"']").prop("checked", true);
		$("#usu_fechaNac").val(_tempDatos.fechaNac);
		$("#usu_telefono1").val(_tempDatos.telefono1);
		$("#usu_telefono2").val(_tempDatos.telefono2);
		$("#usu_calleNumero").val(_tempDatos.calleNumero);
		$("#usu_notificaciones").prop("checked", (_tempDatos.notificaciones == 1 ? true : false));
		
		anunciosVencidos_onChange_estado_ciudad_colonia(_tempDatos.estado, _tempDatos.ciudad, _tempDatos.colonia, "usu_estado", "usu_ciudad", "usu_colonia");
		
		if (_tempDatos.imagen != "") {
			$("#usu_imagenActual").prop("href", urlArchivos+_tempDatos.imagen);
			$("#usu_imagenActual").show();
		}
	});
}


/*
	Actualiza los campos de: estado, ciudad, colonia
	
		* estado:				[integer], es el id del estado
		* ciudad:				[integer], es el id de la ciudad
		* colonia:				[integer], es el id de la colonia
		* _nombreCampoEstado:	[String], es el nombre del campo para estado (por default: estado)
		* _nombreCampoCiudad:	[String], es el nombre del campo para ciudad (por default: ciudad)
		* _nombreCamposColonia:	[String], es el nombre del campo para colonia (por default: colonia)
*/
function anunciosVencidos_onChange_estado_ciudad_colonia(estado, ciudad, colonia, _nombreCampoEstado, _nombreCampoCiudad, _nombreCamposColonia) {
	nombreCampoEstado = _nombreCampoEstado == null ? "estado" : _nombreCampoEstado;
	nombreCampoCiudad = _nombreCampoCiudad == null ? "ciudad" : _nombreCampoCiudad;
	nombreCamposColonia = _nombreCamposColonia == null ? "colonia" : _nombreCamposColonia;
	$("#"+nombreCampoEstado).val(estado);
	
	if (estado != "") {
		$.ajax({
			url: "lib_php/consDireccion.php",
			type: "POST",
			dataType: "json",
			data: {
				consCiudad: 1,
				estado: estado
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				$("#"+nombreCampoCiudad).prop("disabled", false);
				$("#"+nombreCampoCiudad+" option[value!='-1']").remove();
				
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					$("#"+nombreCampoCiudad).append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
				}
				
				$("#"+nombreCampoCiudad).val(ciudad);
				
				if (ciudad != "") {
					$.ajax({
						url: "lib_php/consDireccion.php",
						type: "POST",
						dataType: "json",
						data: {
							consColonia: 1,
							ciudad: ciudad
						}
					}).always(function(respuesta_json2){
						if (respuesta_json2.isExito == 1) {
							$("#"+nombreCamposColonia).prop("disabled", false);
							$("#"+nombreCamposColonia+" option[value!='-1']").remove();
							
							for (var x = 0; x < respuesta_json2.datos.length; x++) {
								$("#"+nombreCamposColonia).append("<option value='"+respuesta_json2.datos[x].id+"' data-cp='"+respuesta_json2.datos[x].cp+"'>"+respuesta_json2.datos[x].nombre+"</option>");
							}
							
							$("#"+nombreCamposColonia).val(colonia);
						}
					});
				}
				else {
					$("#"+nombreCamposColonia).val("-1");
					$("#"+nombreCamposColonia).prop("disabled", true);
				}
			}
		});
	}
	else {
		$("#"+nombreCampoCiudad).val("-1");
		$("#"+nombreCamposColonia).val("-1");
		
		$("#"+nombreCampoCiudad).prop("disabled", true);
		$("#"+nombreCamposColonia).prop("disabled", true);
	}
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	save();
}


/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos2 () {
	if (!vacio($("#usu_nombre").val(), $("#usu_nombre").attr("placeholder"))) {
		if (!vacio($("#usu_email").val(), $("#usu_email").attr("placeholder"))) {
			if (correoValido($("#usu_email").val())) {
				var continua = true;
				var id = parseInt($("#idUsuario").val());
					
				
				if (!isVacio($("#usu_FBid").val())) {
					continua = false;
					
					if (entero($("#usu_FBid").val(), $("#usu_FBid").attr("placeholder"))) {
						continua = true;
					}
				}
				
				if (continua) {
					$.ajax({
						url: "lib_php/updUsuario.php",
						type: "POST",
						dataType: "json",
						data: {
							id: id,
							validarEmail: 1,
							email: $("#usu_email").val()
						}
					}).always(function(respuesta_json) {
						if (respuesta_json.isExito == 1) {
							if (!isVacio($("#usu_FBid").val())) {
								$.ajax({
									url: "lib_php/updUsuario.php",
									type: "POST",
									dataType: "json",
									data: {
										id: id,
										validarFBid: 1,
										FBid: $("#usu_FBid").val()
									}
								}).always(function(respuesta_json2){
									if (respuesta_json2.isExito == 1) {
										saveUsuario();
									}
									else
										alert(respuesta_json2.mensaje);
								});
							}
							else
								saveUsuario();
						}
						else
							alert(respuesta_json.mensaje);
					});
				}
			}
		}
	}
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


/*
	Guarda el usuario y cierra el popup
*/
function saveUsuario() {
	$("#btnGuardar2").hide();
	$("#mensajeTemporal2").show();
	$("#anunciosVencidos_abrirModificarUsuario").css({cursor:"wait"});
	
	
	$("#usu_cp").val(($("#usu_colonia").val() != -1 ? $("#usu_colonia option:selected").attr("data-cp") : ""));
	
	
	$("#subirUsuario").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar2").show();
			$("#mensajeTemporal2").hide();
			$("#anunciosVencidos_abrirModificarUsuario").css({cursor:"default"});
			
			principalCerrarPopUp(anunciosVencidos_cerrarPopUp);
			consultarTuplasExistentesPaginacion(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
			$("#resultados").text(respuesta_json.mensaje);
		}
	});
}

/**/