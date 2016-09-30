// JavaScript Document
var map;
var marker;
var inmueble_positions = Array();
var inmueble_pos_comp = -1;
var urlArchivos = "../images/images/";


/*
	Consulta las tuplas existentes en la base de datos y las muestra  en el div "contenedorConsulta"
	Tambien agrega el sistema de paginacion
	
		* nombrePHPCons:	String, es el nombre del php a consultar los campos
		* isBorrar:			Boolean, si esta en true entonces se pueden borrar la tuplas, false no se pueden eliminar.
							Por default es true
		* arrayCamposCons:	[Array String], es un arreglo de tipo "String", con los nombres de los campos para realizar
								la consulta. Por default es NULL (sin campos)
*/
function consultarTuplasExistentesPaginacion(nombrePHPCons, isBorrar, arrayCamposCons) {
	isBorrar = isBorrar == null ? false : isBorrar;
	nombrePHPConsultar = nombrePHPCons;
	isBorrarTuplas = isBorrar;
	arrayCamposConsulta = arrayCamposCons == null ? {} : arrayCamposCons;
	
	consultarTuplasExistentesPaginacion_pagina(0);
}


/*
	Consulta tuplas existentes, preparado para datos de paginacion
	
		* pagina:	Integer, es el numero de pagina a consultar
*/
function consultarTuplasExistentesPaginacion_pagina(pagina) {
	$("#template_celdaPaginacion").hide();
	$("#contenedorConsulta").html("Cargando...");
	
	arrayCamposConsulta["pagina"] = pagina;
	
	var ajax = $.ajax({
		url: "lib_php/"+nombrePHPConsultar,
		type: "POST",
		dataType: "json",
		data: arrayCamposConsulta
	}).always(function(respuesta_json){
		positions = respuesta_json.datos;
		mostrarCamposExistentes(respuesta_json.contadores);//definir en cada js de la interfaz
		if (respuesta_json.numeroElementos > 0) {
			$("#template_celdaPaginacion").show();
			template_sistemaPaginacion(respuesta_json.page, respuesta_json.elem, respuesta_json.numeroElementos, respuesta_json.maxPaginas, respuesta_json.maxPaginacion);
		}
	});
}


/*
	Genera y actualiza el sistema de paginacion.
	
		* page:				Integer, es el numero de pagina actual (empezando en cero)
		* elem:				Integer, es el numero de elementos a mostrar por cada pagina
		* numeroElementos:	Integer, es el numero de elementos totales por la busqueda
		* maxPaginas:		Integer, es el numero maximo de paginas a mostrar (empezando en cero cuando existe al menos un resultado; 0 cuando no hay resultados),
		* maxPaginacion:	Integer, es el numero maximo de numero de pagina a mostrar por "paginación"
*/
function template_sistemaPaginacion(page, elem, numeroElementos, maxPaginas, maxPaginacion) {
	var objDiv = $("#sistemaPaginacion");
	objDiv.html("");
	page = Number(page);
	elem = Number(elem);
	numeroElementos = Number(numeroElementos);
	maxPaginas = Number(maxPaginas);
	maxPaginacion = Number(maxPaginacion);
	
	var cadena = "<a href='javascript:consultarTuplasExistentesPaginacion_pagina(0);'>&lt;&lt;</a>";
	
	if (page > 0) {
		cadena += "<a href='javascript:consultarTuplasExistentesPaginacion_pagina("+(page - 1)+");'>&lt;</a>";
	}
	
	var posIni = page < maxPaginacion ? 0 : (Math.floor(page / maxPaginacion) * maxPaginacion);
	var posFin = (posIni + maxPaginacion) > maxPaginas ? maxPaginas : (posIni + maxPaginacion);
	
	for (var x = posIni; x < posFin; x++) {
		cadena += "<a "+(x == page ? "class='activo'" : "")+" href='javascript:consultarTuplasExistentesPaginacion_pagina("+x+");'>"+(x + 1)+"</a>";
	}
	
	if (page < (maxPaginas - 1))
		cadena += "<a href='javascript:consultarTuplasExistentesPaginacion_pagina("+(page + 1)+");'>&gt;</a>";
	
	cadena += "<a href='javascript:consultarTuplasExistentesPaginacion_pagina("+(maxPaginas - 1)+");'>&gt;&gt;</a>";
	
	objDiv.html(cadena);
}

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes(arrayContadores) {
	$("#contenedorConsulta").html("");
	var _fecha = new Date();
	
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 30;
		var textTitulo = (positions[pos].titulo).length > maxCadena ? ((positions[pos].titulo).substr(0, (maxCadena - 3))+"...") : positions[pos].titulo;
		var textAnunciante = $("#usuario option[value='"+positions[pos].usuario+"']").text();
		maxCadena = 20;
		textAnunciante = textAnunciante.length > maxCadena ? (textAnunciante.substr(0, (maxCadena - 3))+"...") : textAnunciante;
		maxCadena = 10;
		var textCodigo = (positions[pos].codigo).length > maxCadena ? ((positions[pos].codigo).substr(0, (maxCadena - 3))+"...") : positions[pos].codigo;
		
		fechaPrueba = new Date();
		fechaPrueba.setDate();
		fechaPrueba.setFullYear(parseInt(positions[pos].limiteVigencia.split("/")[2]), parseInt(positions[pos].limiteVigencia.split("/")[1])-1, parseInt(positions[pos].limiteVigencia.split("/")[0]));
		
		textEstado = fechaPrueba >= _fecha ? "Publicado" : "Vencido";
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:inmueble_popupIframe("+pos+");'>"+textTitulo+"</a></td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(1).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarUsuario("+pos+");'>"+textAnunciante+"</a></td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(2).width()+"' style='text-align:center;'>"+positions[pos].id+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(3).width()+"' style='text-align:center;'>"+positions[pos].create+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(4).width()+"' style='text-align:center;'>"+positions[pos].limiteVigencia+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(5).width()+"' style='text-align:center;'>"+positions[pos].contVisitas+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(6).width()+"' style='text-align:center;'>"+positions[pos].contContactado+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(7).width()+"' style='text-align:center;'>"+positions[pos].leyendaEstado+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(8).width()+"' style='text-align:center;'>"+$("#categoria option[value='"+positions[pos].categoria+"']").text()+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(9).width()+"' style='text-align:center;'>"+$("#tipo option[value='"+positions[pos].tipo+"']").text()+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(10).width()+"' style='text-align:center;'>"+positions[pos].codigo+"</td>"+
					//"<td width='"+$("#template_nombreCampos").find("td").eq(11).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarTransacciones("+pos+");'>Transacción</a></td>"+
					//"<td width='"+$("#template_nombreCampos").find("td").eq(12).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarImagenes("+pos+");'>Imágenes</a></td>"+
					//"<td width='"+$("#template_nombreCampos").find("td").eq(13).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarVideos("+pos+");'>Videos</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
	
	if (positions.length > 0) {
		$("#_numPublicados").text(arrayContadores.publicados);
		$("#_numNoPublicados").text(arrayContadores.noPublicados);
		$("#_numVencidos").text(arrayContadores.vencidos);
		$("#_numNoPagados").text(arrayContadores.noPagados);
		$("#_total").text(arrayContadores.total);
	}
}


/*
	Muestra las imagenes del inmueble en el div "contenedorInmuebleImagenes"
	
		* posit:		Integer, es el id del inmueble
		* datos_json:	Array JSON, es un arreglo de datos decodificado con JSON
*/
function inmueble_imagenInmuebleCampos(posit, datos_json) {
	var contenedorDatos = $("#contenedorInmuebleImagenes");
	contenedorDatos.html("");
	
	inmueble_positions = Array();
	inmueble_pos_comp = -1;
			
	for (var x = 0; x < datos_json.length; x++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		divImagen.innerHTML =
			"<table>"+
				"<tr>"+
					"<td style='text-align:left;'><a href='javascript:inmueble_subirImagen("+x+");'>Modificar</a></td>"+
					"<td width='"+$("#inmueble_abrirModificarImagenes table tr").eq(1).find("td").eq(1).width()+"' style='text-align:center;'><a href='"+urlArchivos+datos_json[x].campo2+"' target='_blank'>Imágen "+(x + 1)+"</a></td>"+
					"<td width='"+$("#inmueble_abrirModificarImagenes table tr").eq(1).find("td").eq(2).width()+"' style='text-align:center;'>"+(datos_json[x].campo3 == 0 ? "No" : "Si")+"</td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; inmueble_abrirModificarImagenes("+posit+", "+datos_json[x].campo1+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>";
			
		contenedorDatos.append(divImagen);
		inmueble_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2, datos_json[x].campo3));
	}
}


/*
	Muestra los videos del inmueble en el div "contenedorInmuebleVideos"
	
		* posit:		Integer, es el id del inmueble
		* datos_json:	Array JSON, es un arreglo de datos decodificado con JSON
*/
function inmueble_videoInmuebleCampos(posit, datos_json) {
	var contenedorDatos = $("#contenedorInmuebleVideos");
	contenedorDatos.html("");
	
	inmueble_positions = Array();
	inmueble_pos_comp = -1;
			
	for (var x = 0; x < datos_json.length; x++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		divImagen.innerHTML =
			"<table>"+
				"<tr>"+
					"<td style='text-align:left;'><a href='"+datos_json[x].campo2+"' target='_blank'>"+datos_json[x].campo2+"</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; inmueble_abrirModificarVideos("+posit+", "+datos_json[x].campo1+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>";
			
		contenedorDatos.append(divImagen);
		inmueble_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2));
	}
}


/*
	Muestra las transacciones del inmueble en el div "contenedorInmuebleTransacciones"
	
		* posit:		Integer, es el id del inmueble
		* datos_json:	Array JSON, es un arreglo de datos decodificado con JSON
*/
function inmueble_transaccionInmuebleCampos(posit, datos_json) {
	var contenedorDatos = $("#contenedorInmuebleTransacciones");
	contenedorDatos.html("");
	
	inmueble_positions = Array();
	inmueble_pos_comp = -1;
			
	for (var x = 0; x < datos_json.length; x++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		
		divImagen.innerHTML =
			"<table>"+
				"<tr>"+
					"<td width='15' style='text-align:right; padding-right:0px;'><input type='checkbox' value='"+datos_json[x].campo1+"' "+(datos_json[x].campo3 != 0 ? "checked='checked'" : "")+" /></td>"+
					"<td style='text-align:left;'>"+datos_json[x].campo2+"</td>"+
				"</tr>"+
			"</table>";
			
		contenedorDatos.append(divImagen);
		inmueble_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2, datos_json[x].campo3));
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function inmueble_inicializarBotones() {
	$("#template_celdaBuscador").find("td").eq(0).append("<p style='padding-top:15px; padding-bottom:15px;'><a href='inmueble.php?publicados=1' style='margin-right:100px;'>Inmuebles publicados: <span id='_numPublicados'>0</span></a><a href='inmueble.php?nopublicados=1' style='margin-right:100px;'>Inmuebles no publicados: <span id='_numNoPublicados'>0</span></a><a href='inmueble.php?vencidos=1' style='margin-right:100px;'>Inmuebles vencidos: <span id='_numVencidos'>0</span></a><a href='inmueble.php?nopagados=1' style='margin-right:100px;'>Inmuebles no pagados: <span id='_numNoPagados'>0</span></a><a href='inmueble.php'>Total de inmuebles: <span id='_total'>0</span></a></p>");
	
	$("#main table.main_table tr").eq(2).find("td").eq(0).find("img").attr("onclick", "inmueble_popupIframe(-1);");
	
	
	arrayCamposConsulta = template_getURL_keyValue();
	arrayCamposConsulta["palabra"] = $("#template_buscador").val();
	
	
	consultarTuplasExistentesPaginacion("updPositionsInmueble.php", true, arrayCamposConsulta);
	$("#template_buscador").attr("placeholder", "Buscar por título, anunciante, estado o código");
	
	
	$("#backImage div.template_contenedorCeldas").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				var objDiv = $("#backImage");
				objDiv.find("tr[name='caracteristicas']").hide();
				objDiv.find("tr[name='ambientes']").hide();
				objDiv.find("tr[name='servicios']").hide();
				objDiv.find("tr[name='amenidades']").hide();
				objDiv.find("tr[name='otros']").hide();
				
				objDiv.find("tr[name='"+elemento.attr("data-pestana")+"']").show();
				
				$("body,html").animate({
					scrollTop: "0px",
					scrollLeft: "0px"
				}, 500);
			}
		});
	});
	
	
	$("#template_buscador").on({
		keyup: function(ev) {
			var unicode = ev.keyCode;
	
			if (unicode == 13) {
				arrayCamposConsulta = template_getURL_keyValue();
				arrayCamposConsulta["palabra"] = $("#template_buscador").val();
				
				consultarTuplasExistentesPaginacion("updPositionsInmueble.php", true, arrayCamposConsulta);
			}
		}
	});
	
	
	$("#estado").on({
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
						$("#ciudad").prop("disabled", false);
						$("#ciudad option[value!='-1']").remove();
						$("#colonia").val("-1");
						$("#colonia").prop("disabled", true);
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#ciudad").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#ciudad").val("-1");
				$("#colonia").val("-1");
				
				$("#ciudad").prop("disabled", true);
				$("#colonia").prop("disabled", true);
			}
		}
	});
	
	
	$("#ciudad").on({
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
						$("#colonia").prop("disabled", false);
						$("#colonia option[value!='-1']").remove();
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#colonia").append("<option value='"+respuesta_json.datos[x].id+"' data-cp='"+respuesta_json.datos[x].cp+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#colonia").val("-1");
				$("#colonia").prop("disabled", true);
			}
		}
	});
	
	
	$("#latitud,#longitud").on({
		click: function() {
			inmueble_obtenerCoordenadas();
		}
	});
	
	
	$("#categoria").on({
		change: function() {
			if ($(this).val() != -1) {
				$("#tipo option[value!='-1']").each(function(){
					tempArray = $(this).attr("data-categorias").split(",");
					
					if ($.inArray($("#categoria").val(), tempArray) > -1)
						$(this).show();
					else
						$(this).hide();
				});
				$("#tipo").val(-1);
			}
			else {
				$("#tipo option[value!='-1']").hide();
				$("#tipo").val(-1);
			}
		}
	});
	
	
	$("#tipo").on({
		change: function() {
			$("#backImage input[data-campos='1']").prop("disabled", true);
			$("#backImage select[data-campos='1']").prop("disabled", true);
			
			if ($(this).val() != -1) {
				var key1 = $(this).val();
				var key2 = $("#categoria").val();
				var campos = (tipoCategoriaCampos[key1][key2]+"").split(",");
				
				for (var x = 0; x < campos.length; x++) {
					$("#"+campos[x]).prop("disabled", false);
				}
			}
		}
	});
	
	
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkNumeroOficinas,#chkCajonesEstacionamiento").on({
		change: function() {
			var padre = $(this).parent();
			
			if ($(this).prop("checked")) {
				padre.find("span").hide();
				padre.find("input[type='text']").show();
			}
			else {
				padre.find("input[type='text']").hide();
				padre.find("input[type='text']").val("");
				padre.find("span").show();
			}
		}
	});
	
	
	$("#usuario").on({
		change: function() {
			if ($(this).val() != -1) {
				if ($("#usuario option:selected").attr("data-inmobiliaria") != "") {
					$("#celdaCodigo").show();
				}
				else {
					$("#celdaCodigo").hide();
					$("#codigo").val("");
				}
			}
			else {
				$("#celdaCodigo").hide();
				$("#codigo").val("");
			}
		}
	});
	
	
	$("#usu_fechaNac").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		max: true,
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
function inmueble_cerrarPopUp(){
	$("#backImage").hide();
	$("#inmueble_abrirModificarImagenes").hide();
	$("#inmueble_abrirModificarVideos").hide();
	$("#inmueble_abrirModificarTransacciones").hide();
	$("#inmueble_abrirModificarUsuario").hide();
	$("#inmueble_popupIframe").hide();
}


/*
	Cierra todos los poups de la interfaz actual de nivel 2
*/
function inmueble_cerrarPopUp2(){
	$("#mascaraPrincipalNivel2").hide();
	$("#inmueble_obtenerCoordenadas").hide();
	$("#inmueble_subirImagen").hide();
	$("#inmueble_subirVideo").hide();
}


/*
	Muestra el primer poup que es el que tiene los datos a modificar o crear una nueva tupla
*/
function abrirModificarCampos(posit){
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		var objDiv = $("#backImage");
		objDiv.find("tr[name='caracteristicas']").show();
		objDiv.find("tr[name='ambientes']").hide();
		objDiv.find("tr[name='servicios']").hide();
		objDiv.find("tr[name='amenidades']").hide();
		objDiv.find("tr[name='otros']").hide();

		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Inmueble");
			
			var arrayTodosCampos = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "descripcion", "antiguedad", "codigo", "dimensionTotal", "dimensionConstruida", "estadoConservacion", "cocinaEquipada", "estudio", "cuartoServicio", "cuartoTV", "bodega", "terraza", "jardin", "areaJuegosInfantiles", "comedor", "serviciosBasicos", "gas", "lineaTelefonica", "internetDisponible", "aireAcondicionado", "calefaccion", "cuotaMantenimiento", "casetaVigilancia", "elevador", "seguridad", "alberca", "casaClub", "canchaTenis", "vistaMar", "jacuzzi", "estacionamientoVisitas", "permiteMascotas", "gimnasio", "centrosComercialesCercanos", "escuelasCercanas", "fumadoresPermitidos", "numeroOficinas", "wcs", "recamaras", "hospitalesCercanos", "iglesiasCercanas", "amueblado2", "semiAmueblado", "zonaIndustrial", "zonaTuristica", "zonaComercial", "zonaResidencial", "baresCercanos", "supermercadosCercanos", "excelenteUbicacion", "cisterna", "calentador", "camaras", "anden", "asador", "vapor", "sauna", "playa", "clubPlaya", "portonElectrico", "chimenea", "areasVerdes", "vistaPanoramica", "canchaSquash", "canchaBasket", "salaCine", "canchaFut", "familyRoom", "campoGolf", "cableTV", "biblioteca", "usosMultiples", "sala", "recibidor", "vestidor", "oratorio", "cava", "patio", "balcon", "lobby", "metrosFrente", "metrosFondo", "cajonesEstacionamiento", "desarrollo");
			
			for (var x = 0; x < arrayTodosCampos.length; x++) {
				switch($("#"+arrayTodosCampos[x]).prop("tagName")) {
					case "INPUT":
						if ($("#"+arrayTodosCampos[x]).prop("type") == "text")//text
							$("#"+arrayTodosCampos[x]).val(positions[pos_comp][arrayTodosCampos[x]]);
						else//checkbox
							$("#"+arrayTodosCampos[x]).prop("checked", (positions[pos_comp][arrayTodosCampos[x]] == 1 ? true : false));
						break;
					case "SELECT":
						$("#"+arrayTodosCampos[x]).val(positions[pos_comp][arrayTodosCampos[x]]);
						break;
					case "TEXTAREA":
						$("#"+arrayTodosCampos[x]).val(positions[pos_comp][arrayTodosCampos[x]]);
						break;
				}
			}
			
			$("#usuario").change();
			inmueble_onChange_estado_ciudad_colonia(positions[pos_comp].estado, positions[pos_comp].ciudad, positions[pos_comp].colonia);
			$("#tipo").change();
			inmueble_onChange_chkCamposNumericos(positions[pos_comp].cuotaMantenimiento, positions[pos_comp].elevador, positions[pos_comp].estacionamientoVisitas, positions[pos_comp].numeroOficinas, positions[pos_comp].cajonesEstacionamiento);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Inmueble");
			objDiv.find("input[type='text']").val("");
			objDiv.find("select").val(-1);
			objDiv.find("textarea").val("");
			objDiv.find("input[type='checkbox']").prop("checked", false);
			
			$("#usuario").change();
			$("#categoria").change();
			$("#estado").change();
			$("#tipo").change();
			$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkNumeroOficinas,#chkCajonesEstacionamiento").change();
		}


		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;
		tPos = 20;

		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px",
			"position": "absolute"
		});
		
		$("body,html").animate({
			scrollTop: "0px",
			scrollLeft: "0px"
		}, 500);
	}
	else{
		bool_borrar=false;
		if (confirm('\u00BFEsta seguro de eliminar el inmueble: "'+positions[posit].titulo+'"?')) {
			datos = {
				id: positions[posit].id,
				borrar: 1
			};
			
			$.ajax({
				url: "lib_php/updInmueble.php",
				type: "POST",
				dataType: "json",
				data: datos
			}).always(function(respuesta_json){
				$("#resultados").text(respuesta_json.mensaje);
				
				if (respuesta_json.isExito == 1) {
					principalCerrarPopUp(inmueble_cerrarPopUp);
					consultarTuplasExistentesPaginacion(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}


/*
	Muestra un popup con las imagenes del inmueble
	
		* posit:		Integer, es el id del inmueble
		* idImagen:		Integer, es el id de la imagen
*/
function inmueble_abrirModificarImagenes(posit, idImagen) {
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		
		var objDiv = $("#inmueble_abrirModificarImagenes");
	
		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;
	
		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		pos_comp = posit;
		
		$.ajax({
			url: "lib_php/updInmuebleImagen.php",
			type: "POST",
			dataType: "json",
			data:{
				id: positions[posit].id
			}
		}).always(function(respuesta_json){
			inmueble_imagenInmuebleCampos(posit, respuesta_json.datos);
		});
	}
	else {
		bool_borrar=false;
		if (confirm('\u00BFEsta seguro de eliminar la imágen?')) {
			$.ajax({
				url: "lib_php/updInmuebleImagen.php",
				type: "POST",
				dataType: "json",
				data: {
					id: positions[posit].id,
					borrar: 1,
					idImagen: idImagen
				}
			}).always(function(respuesta_json){
				if (respuesta_json.isExito == 1)
					inmueble_imagenInmuebleCampos(posit, respuesta_json.datos);
				else {
					$("#resultados").text(respuesta_json.mensaje);
					alert(respuesta_json.mensaje);
				}
			});
		}
	}
}


/*
	Muestra un popup con los videos del inmueble
	
		* posit:		Integer, es el id del inmueble
		* idVideo:		Integer, es el id del video
*/
function inmueble_abrirModificarVideos(posit, idVideo) {
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		
		var objDiv = $("#inmueble_abrirModificarVideos");
	
		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;
	
		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		pos_comp = posit;
		
		$.ajax({
			url: "lib_php/updInmuebleVideo.php",
			type: "POST",
			dataType: "json",
			data:{
				id: positions[posit].id
			}
		}).always(function(respuesta_json){
			inmueble_videoInmuebleCampos(posit, respuesta_json.datos);
		});
	}
	else {
		bool_borrar=false;
		if (confirm('\u00BFEsta seguro de eliminar el video?')) {
			$.ajax({
				url: "lib_php/updInmuebleVideo.php",
				type: "POST",
				dataType: "json",
				data: {
					id: positions[posit].id,
					borrar: 1,
					idVideo: idVideo
				}
			}).always(function(respuesta_json){
				if (respuesta_json.isExito == 1)
					inmueble_videoInmuebleCampos(posit, respuesta_json.datos);
				else {
					$("#resultados").text(respuesta_json.mensaje);
					alert(respuesta_json.mensaje);
				}
			});
		}
	}
}


/*
	Muestra un popup con las transacciones del inmueble
	
		* posit:		Integer, es el id del inmueble
		* idTransaccion:Integer, es el id de la transaccion
*/
function inmueble_abrirModificarTransacciones(posit, idTransaccion) {
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		
		var objDiv = $("#inmueble_abrirModificarTransacciones");
	
		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;
	
		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		pos_comp = posit;
		
		$.ajax({
			url: "lib_php/updInmuebleTransaccion.php",
			type: "POST",
			dataType: "json",
			data:{
				id: positions[posit].id
			}
		}).always(function(respuesta_json){
			inmueble_transaccionInmuebleCampos(posit, respuesta_json.datos);
		});
	}
}


/*
	Muestra un popup con los datos del usuario propietario del inmueble
	
		* posit:		Integer, es el id del inmueble
*/
function inmueble_abrirModificarUsuario(posit, idImagen) {
	$("#mascaraPrincipal").show();
	
	var objDiv = $("#inmueble_abrirModificarUsuario");

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
		
		inmueble_onChange_estado_ciudad_colonia(_tempDatos.estado, _tempDatos.ciudad, _tempDatos.colonia, "usu_estado", "usu_ciudad", "usu_colonia");
		
		if (_tempDatos.imagen != "") {
			$("#usu_imagenActual").prop("href", urlArchivos+_tempDatos.imagen);
			$("#usu_imagenActual").show();
		}
	});
}


/*
	Muestra un popup con el iframe para el formulario de inmueble
	
		* posit:	Integer, es el id del inmueble
*/
function inmueble_popupIframe(posit) {
	$("#mascaraPrincipal").show();
		
	var objDiv = $("#inmueble_popupIframe");
	$("#celdaImagenesExistentes").hide();
	$("#celdaVideosExistentes").hide();
	$("#contenedorGaleriasImagenesImagenPrincipal").html("");
	objDiv.find("input[type='checkbox']").prop("checked", false);
	
	//inicializa cada vez ocultando los campos
	$("#chkCuotaMantenimiento2,#chkElevador2,#chkEstacionamientoVisitas2,#chkCajonesEstacionamiento2,#chkNumeroOficinas2").change();
	
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;
	
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": "20px",
		"position": "absolute"
	});
	
	pos_comp = posit;
	
	//eventos para el mapa
	tempCenter = new google.maps.LatLng(20.650118, -103.422227);
	
	//define el google maps
	var mapaGoogle = document.getElementById("contenedorMapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 14,
		mapMaker: true,
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	map = new google.maps.Map(mapaGoogle, mapOptions);
	
	google.maps.event.addListener(map, "click", mapDefinirMarca2);
	
	
	if (parseInt(pos_comp) == -1) {//nuevo
		$("#_leyendaTitulo").text("Crear Anuncio");
		$("#subirAnuncioNuevoModificar").prop("name", "nuevo");
		$("#btnGuardar7").html("<a class='btnBotones palomita'>Guardar</a>Publicar Anuncio");
		
		$("#idInmueble2").val(-1);
		$("#crearAnuncio_titulo").val("");
		$("#crearAnuncio_categoria").val(-1);
		$("#crearAnuncio_categoria").change();
		$("#crearAnuncio_calleNumero").val("");
		$("#crearAnuncio_tipo").val(-1);
		$("#crearAnuncio_tipo").change();
		$("#crearAnuncio_transaccion").val(-1);
		$("#crearAnuncio_precio").val("");
		$("#crearAnuncio_estado").val(-1);
		$("#crearAnuncio_estado").change();
		$("#crearAnuncio_descripcion").val("");
		$("#crearAnuncio_wcs").val(-1);
		$("#crearAnuncio_antiguedad").val(-1);
		$("#crearAnuncio_dimesionTotal").val("");
		$("#crearAnuncio_metrosFrente").val("");
		$("#crearAnuncio_usuario").val(-1);
		$("#crearAnuncio_usuario").change();
		$("#_crearAnuncioDesarrollo").val(-1);
		$("#crearAnuncio_recamaras").val(-1);
		$("#crearAnuncio_estadoConservacion").val(-1);
		$("#crearAnuncio_dimensionConstruida").val("");
		$("#crearAnuncio_metrosFondo").val("");
		$("#crearAnuncio_codigo").val("");
	}
	else {//modificar
		$("#_leyendaTitulo").text("Modificar Anuncio");
		$("#subirAnuncioNuevoModificar").prop("name", "modificar");
		$("#btnGuardar7").html("<a class='btnBotones guardar'>Guardar</a>Guardar Cambios");
		
		$("#idInmueble2").val(positions[pos_comp].id);
		$("#crearAnuncio_titulo").val(positions[pos_comp].titulo);
		$("#crearAnuncio_categoria").val(positions[pos_comp].categoria);
		$("#crearAnuncio_categoria").change();
		$("#crearAnuncio_calleNumero").val(positions[pos_comp].calleNumero);
		$("#crearAnuncio_tipo").val(positions[pos_comp].tipo);
		$("#crearAnuncio_tipo").change();
		$("#crearAnuncio_transaccion").val(positions[pos_comp].transaccion);
		$("#crearAnuncio_precio").val(positions[pos_comp].precio);
		
		inmueble_onChange_estado_ciudad_colonia(positions[pos_comp].estado, positions[pos_comp].ciudad, positions[pos_comp].colonia, "crearAnuncio_estado", "crearAnuncio_ciudad", "crearAnuncio_colonia");
		tempCenter = new google.maps.LatLng(positions[pos_comp].latitud, positions[pos_comp].longitud);
		map.setCenter(tempCenter);
		mapDefinirMarca2({latLng: tempCenter});
		
		$("#crearAnuncio_descripcion").val(positions[pos_comp].descripcion);
		$("#crearAnuncio_wcs").val(positions[pos_comp].wcs);
		$("#crearAnuncio_antiguedad").val(positions[pos_comp].antiguedad);
		$("#crearAnuncio_dimesionTotal").val(positions[pos_comp].dimensionTotal);
		$("#crearAnuncio_metrosFrente").val(positions[pos_comp].metrosFrente);
		$("#crearAnuncio_usuario").val(positions[pos_comp].usuario);
		$("#crearAnuncio_usuario").change();
		$("#_crearAnuncioDesarrollo").val((positions[pos_comp].desarrollo == "" ? -1 : positions[pos_comp].desarrollo));
		$("#crearAnuncio_recamaras").val(positions[pos_comp].recamaras);
		$("#crearAnuncio_estadoConservacion").val(positions[pos_comp].estadoConservacion);
		$("#crearAnuncio_dimensionConstruida").val(positions[pos_comp].dimensionConstruida);
		$("#crearAnuncio_metrosFondo").val(positions[pos_comp].metrosFondo);
		$("#crearAnuncio_codigo").val(positions[pos_comp].codigo);
		
		//contenedor de imagenes
		var galeriaImagenes = $("#galeriaImagenes");
		galeriaImagenes.html("");
		var tempPrincipal = "";
                            
		for (var x = 0; x < positions[pos_comp].imagenes.length; x++) {
			galeriaImagenes.append(
				"<div class='bloqueImagen' data-imagen='"+positions[pos_comp].imagenes[x].id+"'>"+
					"<img src='"+urlArchivos+positions[pos_comp].imagenes[x].imagen+"' />"+
					"<span class='borrar'>X</span>"+
					"<p><input type='radio' name='radioImagenPrincipal' "+(positions[pos_comp].imagenes[x].principal == 1 ? "checked='checked'" : "")+" data-id="+positions[pos_comp].imagenes[x].id+" /></p>"+
				"</div>"
			);
							
			if (positions[pos_comp].imagenes[x].principal == 1)
				tempPrincipal = positions[pos_comp].imagenes[x].id;
		}
		
		
		if (positions[pos_comp].imagenes.length > 0) {
			$("#contenedorGaleriasImagenesImagenPrincipal").html(
				"<p>Selecciona tu imagen principal</p>"+
				"<input type='text' id='idImagenPrincipal' name='idImagenPrincipal' value='' style='display:none;' />"
			);
		}
		
		
		$("#idImagenPrincipal").val(tempPrincipal);
		$("#celdaImagenesExistentes").show();
		
		
		//agrega evento para borrar las imagenes de la galeria
		$("#galeriaImagenes .bloqueImagen").each(function(){
			var elemento = $(this);
			
			$(this).find("span.borrar").on({
				click: function() {
					if (confirm("¿Esta seguro de eliminar la imágen?")) {
						$.ajax({
							url: "lib_php/updInmuebleImagen.php",
							type: "POST",
							dataType: "json",
							data: {
								borrar: 1,
								id: -1,
								idImagen: elemento.attr("data-imagen")
							}
						}).always(function(respuesta_json){
							if (respuesta_json.isExito == 1) {
								elemento.remove();
							}
						});
					}
				}
			});
		});
		
		
		//contenedor de videos
		var galeriaVideos = $("#galeriaVideos");
		galeriaVideos.html("");
		
		for (x = 0; x < positions[pos_comp].videos.length; x++) {
			galeriaVideos.append(
				"<div class='bloqueVideo' data-video='"+positions[pos_comp].videos[x].id+"'>"+
					"<object>"+
						"<param name='movie' value='"+positions[pos_comp].videos[x].video.replace("watch?v=", "v/")+"?version=3&feature=player_detailpage'>"+
						"<param name='allowFullScreen' value='true'>"+
						"<param name='allowScriptAccess' value='always'>"+
						"<embed src='"+positions[pos_comp].videos[x].video.replace("watch?v=", "v/")+"?version=3&feature=player_detailpage&showinfo=0&autohide=1&rel=0' type='application/x-shockwave-flash' allowfullscreen='true' allowScriptAccess='always' wmode=transparent width='60' height='60' showinfo=0>"+
					"</object>"+
					"<span class='borrar'>X</span>"+
				"</div>"
			);
		}
		
		$("#celdaVideosExistentes").show();
		
		
		//agrega evento para borrar los videos de la galeria
		$("#galeriaVideos .bloqueVideo").each(function(){
			var elemento = $(this);
			
			$(this).find("span.borrar").on({
				click: function() {
					if (confirm("¿Esta seguro de eliminar la url del video?")) {
						$.ajax({
							url: "lib_php/updInmuebleVideo.php",
							type: "POST",
							dataType: "json",
							data: {
								borrar: 1,
								id: -1,
								idVideo: elemento.attr("data-video")
							}
						}).always(function(respuesta_json){
							if (respuesta_json.isExito == 1) {
								elemento.remove();
							}
						});
					}
				}
			});
		});
		
		
		//ahora los checkboxs
		//ambientes
		var arrayCamposAmbientes = new Array("cocinaEquipada", "estudio", "bodega", "terraza", "comedor", "sala", "recibidor", "vestidor", "patio", "balcon", "cuartoServicio", "cuartoTV", "jardin", "areaJuegosInfantiles", "biblioteca", "usosMultiples", "oratorio", "cava", "lobby");
		
		for (var x = 0; x < arrayCamposAmbientes.length; x++) {
			if (positions[pos_comp][arrayCamposAmbientes[x]] == 1)
				objDiv.find("input[type='checkbox'][name='"+arrayCamposAmbientes[x]+"']").prop("checked", true);
		}
		
		//servicios
		var arrayCamposServicios = new Array("serviciosBasicos", "gas", "aireAcondicionado", "calefaccion", "cisterna", "calentador", "lineaTelefonica", "internetDisponible", "casetaVigilancia", "seguridad", "camaras", "anden");
		
		for (var x = 0; x < arrayCamposServicios.length; x++) {
			if (positions[pos_comp][arrayCamposServicios[x]] == 1)
				objDiv.find("input[type='checkbox'][name='"+arrayCamposServicios[x]+"']").prop("checked", true);
		}
		
		//amenidades
		var arrayCamposAmenidades = new Array("alberca", "casaClub", "jacuzzi", "permiteMascotas", "sauna", "asador", "portonElectrico", "chimenea", "canchaSquash", "canchaBasket", "familyRoom", "campoGolf", "canchaTenis", "vistaMar", "gimnasio", "vapor", "playa", "clubPlaya", "areasVerdes", "vistaPanoramica", "canchaFut", "salaCine", "cableTV");
		
		for (var x = 0; x < arrayCamposAmenidades.length; x++) {
			if (positions[pos_comp][arrayCamposAmenidades[x]] == 1)
				objDiv.find("input[type='checkbox'][name='"+arrayCamposAmenidades[x]+"']").prop("checked", true);
		}
		
		//otras
		var arrayCamposOtras = new Array("amueblado2", "semiAmueblado", "centrosComercialesCercanos", "escuelasCercanas", "iglesiasCercanas", "supermercadosCercanos", "zonaComercial", "zonaResidencial", "fumadoresPermitidos", "excelenteUbicacion", "hospitalesCercanos", "baresCercanos", "zonaIndustrial", "zonaTuristica");
		
		for (var x = 0; x < arrayCamposOtras.length; x++) {
			if (positions[pos_comp][arrayCamposOtras[x]] == 1)
				objDiv.find("input[type='checkbox'][name='"+arrayCamposOtras[x]+"']").prop("checked", true);
		}
		
		
		//para los checkbox que tienen tambien un valor entero/flotante
		$("#crearAnuncio_cuotaMantenimiento").val(positions[pos_comp].cuotaMantenimiento);
		$("#crearAnuncio_elevador").val(positions[pos_comp].elevador);
		$("#crearAnuncio_estacionamientoVisitas").val(positions[pos_comp].estacionamientoVisitas);
		$("#crearAnuncio_cajonesEstacionamiento").val(positions[pos_comp].cajonesEstacionamiento);
		$("#crearAnuncio_numeroOficinas").val(positions[pos_comp].numeroOficinas);
		
		$("#crearAnuncio_cuotaMantenimiento,#crearAnuncio_elevador,#crearAnuncio_estacionamientoVisitas,#crearAnuncio_cajonesEstacionamiento,#crearAnuncio_numeroOficinas").each(function(){
			var elemento = $(this);
			
			if ($(this).val() != "") {
				elemento.parent().find("input[type='checkbox']").prop("checked", true);
			}
		});
		
		$("#chkCuotaMantenimiento2,#chkElevador2,#chkEstacionamientoVisitas2,#chkCajonesEstacionamiento2,#chkNumeroOficinas2").change();
	}
}


/*
	Muestra el popup para modificar o asignar las coordenadas en el mapa
*/
function inmueble_obtenerCoordenadas(){
	$("#mascaraPrincipalNivel2").show();
	
	var objDiv = $("#inmueble_obtenerCoordenadas");

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
	var mapaGoogle = document.getElementById("contenedorInmuebleMapa");
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
	
		* posImagen:	Integer, es la posicion del array "inmueble_positions" donde se encuentran los datos de las imagenes
*/
function inmueble_subirImagen(posImagen){
	$("#mascaraPrincipalNivel2").show();
	
	var objDiv = $("#inmueble_subirImagen");
	$("#imagen").val("");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	inmueble_pos_comp = posImagen;
	
	if (inmueble_pos_comp != -1) {
		$("#tituloEmergenteImagenes").html("Modificar Imágen");
		$("#imagenPrincipal").prop("checked", (inmueble_positions[inmueble_pos_comp][2] == 1 ? true : false));
	}
	else {
		$("#tituloEmergenteImagenes").html("Nueva Imágen");
		$("#imagenPrincipal").prop("checked", false);
	}
}


/*
	Muestra el popup para modificar o crear un video
	
		* posVideo:	Integer, es la posicion del array "inmueble_positions" donde se encuentran los datos del video
*/
function inmueble_subirVideo(posVideo){
	$("#mascaraPrincipalNivel2").show();
	
	var objDiv = $("#inmueble_subirVideo");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	inmueble_pos_comp = posVideo;
	
	if (inmueble_pos_comp != -1) {
		$("#tituloEmergenteVideos").html("Modificar Video");
		$("#video").val(inmueble_positions[inmueble_pos_comp][1]);
	}
	else {
		$("#tituloEmergenteVideos").html("Nuevo Video");
		$("#video").val("");
	}
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
function inmueble_onChange_estado_ciudad_colonia(estado, ciudad, colonia, _nombreCampoEstado, _nombreCampoCiudad, _nombreCamposColonia) {
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
	Muestra u oculta los campos de tipo numerico dependiendo del valor recibido
	
		* cuotaMantenimiento:		[Float], es el valor para la cuotaMantenimiento
		* elevador:					[integer], es el valor para el elevador
		* estacionamientoVisitas:	[integer], es el valor para el estacionamientoVisitas
		* numeroOficinas:			[Integer], es el valor para el numeroOficinas
		* cajonesEstacionamiento:	[Integer], es el numero de cajones de estacionamiento
*/
function inmueble_onChange_chkCamposNumericos(cuotaMantenimiento, elevador, estacionamientoVisitas, numeroOficinas, cajonesEstacionamiento) {
	$("#chkCuotaMantenimiento").prop("checked", (cuotaMantenimiento != "" ? true : false));
	$("#chkElevador").prop("checked", (elevador != "" ? true : false));
	$("#chkEstacionamientoVisitas").prop("checked", (estacionamientoVisitas != "" ? true : false));
	$("#chkNumeroOficinas").prop("checked", (numeroOficinas != "" ? true : false));
	$("#chkCajonesEstacionamiento").prop("checked", (cajonesEstacionamiento != "" ? true : false));
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkNumeroOficinas,#chkCajonesEstacionamiento").change();
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
	Trata de localizar en el mapa la direccion escrita en los campos
*/
function inmueble_encontrarUbicacion() {
	if (!vacio($("#calleNumero").val(), $("#calleNumero").attr("placeholder"))) {
		if (!vacio(($("#estado").val() != -1 ? $("#estado").val() : ""), $("#estado option[value='-1']").text())) {
			if (!vacio(($("#ciudad").val() != -1 ? $("#ciudad").val() : ""), $("#ciudad option[value='-1']").text())) {
				direCalle = "";
				direNumero = "";
				partes = $("#calleNumero").val().replace(/#/g, "");
				partes = partes.replace(/-/g, " ");
				partes = partes.split(" ");
				
				for (var x = 0; x < partes.length; x++) {
					if (isEntero(partes[x])) {
						direNumero = partes[x];
						break;
					}
					else
						direCalle += (x != 0 ? " " : "") + partes[x];
				}
				
				direccionBusqueda = (direNumero != "" ? direNumero+"+" : "")+direCalle+"+"+$("#ciudad option:selected").text()+",+"+$("#estado option:selected").text();
				
				$.ajax({
					url: "http://maps.googleapis.com/maps/api/geocode/json?address="+direccionBusqueda+"&sensor=true_or_false",
					dataType: "json"
				}).always(function(respuesta_json){
					if (respuesta_json.results.length > 0) {
						var obtenerPosicion = respuesta_json.results[0].geometry.location;
						tempPosicion = new google.maps.LatLng(obtenerPosicion.lat, obtenerPosicion.lng);
						map.setCenter(tempPosicion);
						mapDefinirMarca({latLng: tempPosicion});
					}
					else
						alert("No se encontro una posición en el mapa.");
				});
			}
		}
	}
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	var arrayCamposEvaluar = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "codigo", "dimensionTotal", "dimensionConstruida", "cuotaMantenimiento", "elevador", "estacionamientoVisitas", "numeroOficinas", "cajonesEstacionamiento", "metrosFrente", "metrosFondo");
	var arrayCamposObligatorios = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud");
	
	if ($("#celdaCodigo").css("display") != "none") {
		arrayCamposObligatorios.push("codigo");
	}
	
	var arrayFlotantes = new Array("precio", "dimensionTotal", "dimensionConstruida", "cuotaMantenimiento", "metrosFrente", "metrosFondo");
	var arrayEnteros = new Array("elevador", "estacionamientoVisitas", "numeroOficinas");
	
	
	for (var x = 0; x < arrayCamposEvaluar.length; x++) {
		//evalua los que son obligatorios
		if ($.inArray(arrayCamposEvaluar[x], arrayCamposObligatorios) > -1) {
			if ($("#"+arrayCamposEvaluar[x]).prop("tagName") == "SELECT") {//evalua los selects
				if (vacio(($("#"+arrayCamposEvaluar[x]).val() != -1 ? $("#"+arrayCamposEvaluar[x]).val() : ""), $("#"+arrayCamposEvaluar[x]+" option[value='-1']").text()))
					return false;
			}
			else {//todos aquellos que no son selects
				if (vacio($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
					return false;
			}
			
			//evalua los flotantes
			if ($.inArray(arrayCamposEvaluar[x], arrayFlotantes) > -1) {
				_campo = $("#"+arrayCamposEvaluar[x]).val();
				_campo = _campo.replace(/\$/g, "");
				_campo = _campo.replace(/,/g, "");
				$("#"+arrayCamposEvaluar[x]).val(_campo);
				
				if (!flotante($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
					return false;
			}
			
			//evalua los enteros
			if ($.inArray(arrayCamposEvaluar[x], arrayEnteros) > -1) {
				_campo = $("#"+arrayCamposEvaluar[x]).val();
				_campo = _campo.replace(/\$/g, "");
				_campo = _campo.replace(/,/g, "");
				$("#"+arrayCamposEvaluar[x]).val(_campo);
				
				if (!entero($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
					return false;
			}
		}
		
		//evalua todos aquellos que no son necesariamente obligatorios
		if ($.inArray(arrayCamposEvaluar[x], arrayCamposObligatorios) == -1) {
			var continua = false;
			
			//evalua primeramente que no esten vacios
			if ($("#"+arrayCamposEvaluar[x]).prop("tagName") == "SELECT") {//evalua los selects
				if (!isVacio(($("#"+arrayCamposEvaluar[x]).val() != -1 ? $("#"+arrayCamposEvaluar[x]).val() : "")))
					continua = true;
			}
			else {//todos aquellos que no son selects
				if (!isVacio($("#"+arrayCamposEvaluar[x]).val()))
					continua = true;
			}
			
			//si no lo estan
			if (continua) {
				//evalua los flotantes
				if ($.inArray(arrayCamposEvaluar[x], arrayFlotantes) > -1) {
					_campo = $("#"+arrayCamposEvaluar[x]).val();
					_campo = _campo.replace(/\$/g, "");
					_campo = _campo.replace(/,/g, "");
					$("#"+arrayCamposEvaluar[x]).val(_campo);
					
					if (!flotante($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
						return false;
				}
				
				//evalua los enteros
				if ($.inArray(arrayCamposEvaluar[x], arrayEnteros) > -1) {
					_campo = $("#"+arrayCamposEvaluar[x]).val();
					_campo = _campo.replace(/\$/g, "");
					_campo = _campo.replace(/,/g, "");
					$("#"+arrayCamposEvaluar[x]).val(_campo);
					
					if (!entero($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
						return false;
				}
			}
		}
	}
	
	
	var id = pos_comp;
	
	if (pos_comp != -1)
		id = positions[pos_comp].id;
		
	
	if (isVacio($("#codigo").val())) {
		save();
	}
	else {
		$.ajax({
			url: "lib_php/updInmueble.php",
			type: "POST",
			dataType: "json",
			data: {
				id: id,
				validarCodigo: 1,
				usuario: $("#usuario").val(),
				codigo: $("#codigo").val()
			}
		}).always(function(respuesta_json) {
			if (respuesta_json.isExito == 1) {
				save();
			}
			else
				alert(respuesta_json.mensaje);
		});
	}
}


/*
	Valida los campos para la posicion del inmueble en el mapa
*/
function validarCampos2() {
	inmueble_cerrarPopUp2();
}


/*
	Valida los campos para crear o modificar la imagen
*/
function validarCampos3 () {
	var id = inmueble_pos_comp;
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
	Valida los campos para crear o modificar urls de video
*/
function validarCampos4 () {
	if (!vacio($("#video").val(), $("#video").attr("placeholder"))) {
		if (validaURL($("#video").val(), $("#video").attr("placeholder"))) {
			saveVideo();
		}
	}
}


/*
	Valida los campos para crear o modificar las transaccione
*/
function validarCampos5 () {
	saveTransaccion();
}


/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos6 () {
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
	
	var datos = {
		id: id
	};
	
	
	var arrayTodosCampos = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "descripcion", "antiguedad", "codigo", "dimensionTotal", "dimensionConstruida", "estadoConservacion", "cocinaEquipada", "estudio", "cuartoServicio", "cuartoTV", "bodega", "terraza", "jardin", "areaJuegosInfantiles", "comedor", "serviciosBasicos", "gas", "lineaTelefonica", "internetDisponible", "aireAcondicionado", "calefaccion", "cuotaMantenimiento", "casetaVigilancia", "elevador", "seguridad", "alberca", "casaClub", "canchaTenis", "vistaMar", "jacuzzi", "estacionamientoVisitas", "permiteMascotas", "gimnasio", "centrosComercialesCercanos", "escuelasCercanas", "fumadoresPermitidos", "numeroOficinas", "wcs", "recamaras", "hospitalesCercanos", "iglesiasCercanas", "amueblado2", "semiAmueblado", "zonaIndustrial", "zonaTuristica", "zonaComercial", "zonaResidencial", "baresCercanos", "supermercadosCercanos", "excelenteUbicacion", "cisterna", "calentador", "camaras", "anden", "asador", "vapor", "sauna", "playa", "clubPlaya", "portonElectrico", "chimenea", "areasVerdes", "vistaPanoramica", "canchaSquash", "canchaBasket", "salaCine", "canchaFut", "familyRoom", "campoGolf", "cableTV", "biblioteca", "usosMultiples", "sala", "recibidor", "vestidor", "oratorio", "cava", "patio", "balcon", "lobby", "metrosFrente", "metrosFondo", "cajonesEstacionamiento", "desarrollo");
	
	
	for (var x = 0; x < arrayTodosCampos.length; x++) {
		var valor = "";
		
		switch($("#"+arrayTodosCampos[x]).prop("tagName")) {
			case "INPUT":
				if ($("#"+arrayTodosCampos[x]).prop("type") == "text")//text
					valor = $("#"+arrayTodosCampos[x]).val();
				else//checkbox
					valor = $("#"+arrayTodosCampos[x]).prop("checked") ? 1 : 0;
				break;
			case "SELECT":
				valor = $("#"+arrayTodosCampos[x]).val();
				break;
			case "TEXTAREA":
				valor = $("#"+arrayTodosCampos[x]).val();
				break;
		}
		
		datos[arrayTodosCampos[x]] = valor;
	}
	
	
	datos["cp"] = $("#colonia").val() != -1 ? $("#colonia option:selected").attr("data-cp") : "";


	$.ajax({
		url: "lib_php/updInmueble.php",
		type: "POST",
		dataType: "json",
		data: datos
	}).always(function(respuesta_json){
		$("#btnGuardar").show();
		$("#mensajeTemporal").hide();
		$("#backImage").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(inmueble_cerrarPopUp);
		consultarTuplasExistentesPaginacion(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
	});
}


/*
	Guarda la imagen y vuelve a refrescar la consulta
*/
function saveImagen() {
	$("#btnGuardar3").hide();
	$("#mensajeTemporal3").show();
	$("#inmueble_subirImagen").css({cursor:"wait"});
	
	
	$("#idInmueble").val(positions[pos_comp].id);
	if (inmueble_pos_comp == -1) {
		$("#nuevo").val("1");
		$("#modificar").val("0");
		$("#idInmuebleInmueble").val("0");
	}
	else {
		$("#nuevo").val("0");
		$("#modificar").val("1");
		$("#idInmuebleInmueble").val(inmueble_positions[inmueble_pos_comp][0]);
	}
	
	
	$("#subirImagen").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar3").show();
			$("#mensajeTemporal3").hide();
			$("#inmueble_subirImagen").css({cursor:"default"});
			
			inmueble_cerrarPopUp2();
			inmueble_imagenInmuebleCampos(pos_comp, respuesta_json.datos);
		}
	});
}


/*
	Guarda el video y vuelve a refrescar la consulta
*/
function saveVideo() {
	$("#btnGuardar4").hide();
	$("#mensajeTemporal4").show();
	$("#inmueble_subirVideo").css({cursor:"wait"});
	
	
	$.ajax({
		url: "lib_php/updInmuebleVideo.php",
		type: "POST",
		dataType: "json",
		data: {
			id: positions[pos_comp].id,
			nuevo: 1,
			video: $("#video").val()
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			$("#btnGuardar4").show();
			$("#mensajeTemporal4").hide();
			$("#inmueble_subirVideo").css({cursor:"default"});
			
			inmueble_cerrarPopUp2();
			inmueble_videoInmuebleCampos(pos_comp, respuesta_json.datos);
		}
	});
}


/*
	Guarda la transaccion y vuelve a refrescar la consulta
*/
function saveTransaccion() {
	$("#btnGuardar5").hide();
	$("#mensajeTemporal5").show();
	$("#inmueble_abrirModificarTransacciones").css({cursor:"wait"});
	
	
	var arrayTransacciones = Array();
	
	$("#contenedorInmuebleTransacciones input[type='checkbox']:checked").each(function(){
		arrayTransacciones.push($(this).val());
	});
	
	
	$.ajax({
		url: "lib_php/updInmuebleTransaccion.php",
		type: "POST",
		dataType: "json",
		data: {
			id: positions[pos_comp].id,
			modificar: 1,
			transacciones: arrayTransacciones.toString()
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			$("#btnGuardar5").show();
			$("#mensajeTemporal5").hide();
			$("#inmueble_abrirModificarTransacciones").css({cursor:"default"});
			
			inmueble_transaccionInmuebleCampos(pos_comp, respuesta_json.datos);
		}
	});
}


/*
	Guarda el usuario y cierra el popup
*/
function saveUsuario() {
	$("#btnGuardar6").hide();
	$("#mensajeTemporal6").show();
	$("#inmueble_abrirModificarUsuario").css({cursor:"wait"});
	
	
	$("#usu_cp").val(($("#usu_colonia").val() != -1 ? $("#usu_colonia option:selected").attr("data-cp") : ""));
	
	
	$("#subirUsuario").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar6").show();
			$("#mensajeTemporal6").hide();
			$("#inmueble_abrirModificarUsuario").css({cursor:"default"});
			
			principalCerrarPopUp(inmueble_cerrarPopUp);
			consultarTuplasExistentesPaginacion(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
			$("#resultados").text(respuesta_json.mensaje);
		}
	});
}
/**/


/********************************************************************************************************************
									formulario inmueble
********************************************************************************************************************/
$(document).ready(function(){
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#crearAnuncio_estado").on({
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
						$("#crearAnuncio_ciudad").prop("disabled", false);
						$("#crearAnuncio_ciudad option[value!='-1']").remove();
						$("#crearAnuncio_colonia").val("-1");
						$("#crearAnuncio_colonia").prop("disabled", true);
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#crearAnuncio_ciudad").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#crearAnuncio_ciudad").val("-1");
				$("#crearAnuncio_colonia").val("-1");
				
				$("#crearAnuncio_ciudad").prop("disabled", true);
				$("#crearAnuncio_colonia").prop("disabled", true);
			}
		}
	});
	
	
	//agrega evento para cuando se cambie de ciudad, actualizar la colonia
	$("#crearAnuncio_ciudad").on({
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
						$("#crearAnuncio_colonia").prop("disabled", false);
						$("#crearAnuncio_colonia option[value!='-1']").remove();
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#crearAnuncio_colonia").append("<option value='"+respuesta_json.datos[x].id+"' data-cp='"+respuesta_json.datos[x].cp+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#crearAnuncio_colonia").val("-1");
				$("#crearAnuncio_colonia").prop("disabled", true);
			}
		}
	});
	
	
	//agrega evento para cuando se cambie de categoria de inmueble
	$("#crearAnuncio_categoria").on({
		change: function() {
			if ($(this).val() != -1) {
				$("#crearAnuncio_tipo option[value!='-1']").each(function(){
					tempArray = $(this).attr("data-categorias").split(",");
					
					if ($.inArray($("#crearAnuncio_categoria").val(), tempArray) > -1)
						$(this).show();
					else
						$(this).hide();
						
					//si es renta vacacional, se oculta el tipo de transaccion y este por default se marca en vacional
					if (parseInt($(this).val()) == 3) {
						$("#crearAnuncio_transaccion").val(3);
						$("#etiquetaPrecio").text("Precio por noche*");
					}
					else {
						$("#crearAnuncio_transaccion").val(-1);
						$("#etiquetaPrecio").text("Precio*");
					}
				});
				
				$("#crearAnuncio_tipo").val(-1);
			}
			else {
				$("#crearAnuncio_tipo option[value!='-1']").hide();
				$("#crearAnuncio_tipo").val(-1);
			}
		}
	});
	
	
	//agrega evento para cuando se cambie tipo de inmueble
	$("#crearAnuncio_tipo").on({
		change: function() {
			if (parseInt($("#crearAnuncio_categoria").val()) != 3) {
				$("#crearAnuncio_transaccion").val(-1);
			}
			
			if ($(this).val() != -1) {
				$("#crearAnuncio_transaccion option[value='3']").hide();
				
				if ((parseInt($(this).val()) == 1) || (parseInt($(this).val()) == 2)) {
					$("#crearAnuncio_transaccion option[value='3']").show();
				}
			}
		}
	});
	
	
	//agrega evento para cuando se cambie de usuario, actualizar el codigo e inmobiliaria
	$("#crearAnuncio_usuario").on({
		change: function() {
			if ($(this).val() != -1) {
				if ($("#crearAnuncio_usuario option:selected").attr("data-inmobiliaria") != -1) {
					$("#celdaCodigo2").show();
				}
				else {
					$("#celdaCodigo2").hide();
					$("#crearAnuncio_codigo").val("");
				}
			}
			else {
				$("#celdaCodigo2").hide();
				$("#crearAnuncio_codigo").val("");
			}
		}
	});
	
	
	//evento para cuando se ingresa una url de youtube para los videos del inmueble
	$("#nuevoAnuncio_urlVideo").on({
		keyup: function(evt) {
			template_displayUnicode(evt, nuevoAnuncio_validarUrl);
		}
	});
	
	
	//eventos para los checkbox-inputs
	$("#chkCuotaMantenimiento2,#chkElevador2,#chkEstacionamientoVisitas2,#chkCajonesEstacionamiento2,#chkNumeroOficinas2").each(function(){
		var elemento = $(this);
		
		$(this).on({
			change: function() {
				if ($(this).prop("checked")) {
					elemento.parent().find("input[type='text']").show();
				}
				else {
					elemento.parent().find("input[type='text']").hide();
					elemento.parent().find("input[type='text']").val("");
				}
			}
		});
	});
});


/*
	Asigna una marca en el mapa, ademas de los campos de latitud y longitud
	
		* evt:	Event, es el evento asignado en el mapa para llamar esta funcion
*/
function mapDefinirMarca2(evt) {
	$("#_crearAnuncioLatitud").val(evt.latLng.lat());
	$("#_crearAnuncioLongitud").val(evt.latLng.lng());
	
	if (typeof marker !== 'undefined')
		marker.setMap(null);
	
	marker = new google.maps.Marker({
		position: evt.latLng,
		map: map,
		icon: "../images/marcador3.png"
	});
}


/*
	Trata de localizar en el mapa la direccion escritra en los campos del anuncio
*/
function nuevoAnuncio_encontrarUbicacion() {
	if (!vacio($("#crearAnuncio_calleNumero").val(), $("#crearAnuncio_calleNumero").attr("placeholder"))) {
		if (!vacio(($("#crearAnuncio_estado").val() != -1 ? $("#crearAnuncio_estado").val() : ""), $("#crearAnuncio_estado option[value='-1']").text())) {
			if (!vacio(($("#crearAnuncio_ciudad").val() != -1 ? $("#crearAnuncio_ciudad").val() : ""), $("#crearAnuncio_ciudad option[value='-1']").text())) {
				direCalle = "";
				direNumero = "";
				partes = $("#crearAnuncio_calleNumero").val().replace(/#/g, "");
				partes = partes.replace(/-/g, " ");
				partes = partes.split(" ");
				
				for (var x = 0; x < partes.length; x++) {
					if (isEntero(partes[x])) {
						direNumero = partes[x];
						break;
					}
					else
						direCalle += (x != 0 ? " " : "") + partes[x];
				}
				
				direccionBusqueda = (direNumero != "" ? direNumero+"+" : "")+direCalle+"+"+$("#crearAnuncio_ciudad option:selected").text()+",+"+$("#crearAnuncio_estado option:selected").text();
				
				$.ajax({
					url: "http://maps.googleapis.com/maps/api/geocode/json?address="+direccionBusqueda+"&sensor=true_or_false",
					dataType: "json"
				}).always(function(respuesta_json){
					if (respuesta_json.results.length > 0) {
						var obtenerPosicion = respuesta_json.results[0].geometry.location;
						tempPosicion = new google.maps.LatLng(obtenerPosicion.lat, obtenerPosicion.lng);
						map.setCenter(tempPosicion);
						mapDefinirMarca2({latLng: tempPosicion});
					}
					else
						alert("No se encontro una posición en el mapa.");
				});
			}
		}
	}
}


/*
	Se cargo una imagen nueva por medio del iframe
	
		* nombreArchivo:	Array String, es el nombre del/los archivo/s guardardo/s en temp
*/
function nuevoAnuncio_tempImagenCargada(nombreArchivo) {
	var urlArchivosTemporales = "../images/images/temp/";
	var imagenesTemporales = $("#imagenesTemporales");
	var isPrimera = false;
	if ($("#galeriaImagenes").length == 0)
		isPrimera = imagenesTemporales.find(".bloqueImagen").length == 0 ? true : false;
	var arrayArchivos = nombreArchivo.split(",");
	
	for (var x = 0; x < arrayArchivos.length; x++) {
		var elemento = 
			"<div class='bloqueImagen' data-imagen='"+arrayArchivos[x]+"'>"+
				"<img src='"+urlArchivosTemporales+arrayArchivos[x]+"' />"+
				"<span class='borrar'>X</span>"+
				"<p><input type='radio' name='radioImagenPrincipal' "+(isPrimera ? "checked='checked'" : "")+" /></p>"+
			"</div>";
		isPrimera = false;
		
		imagenesTemporales.append(elemento);
	}

	
	imagenesTemporales.find(".bloqueImagen .borrar").unbind();
	imagenesTemporales.find(".bloqueImagen").each(function(){
		var elemento = $(this);
		
		$(this).find(".borrar").on({
			click: function() {
				$.ajax({
					url: "../lib_php/tempSubirImagen2.php",
					type: "POST",
					dataType: "json",
					data: {
						borrar: 1,
						imagen: elemento.attr("data-imagen")
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						elemento.remove();
					}
				});
			}
		});
	});
	
	
	$("#iframeSubirImagen").html("");
	if (imagenesTemporales.find(".bloqueImagen").length < 20) {
		$("#iframeSubirImagen").html('<iframe src="../lib_php/tempSubirImagen.php" frameborder="0" width="400" height="50"></iframe>');
	}
}


/*
	Valida que la url sea una url valida y de youtube
*/
function nuevoAnuncio_validarUrl() {
	if (!isVacio($("#nuevoAnuncio_urlVideo").val())) {
		if (validaURL($("#nuevoAnuncio_urlVideo").val(), "Videos en Youtube")) {
			if ($("#nuevoAnuncio_urlVideo").val().indexOf("youtube") != -1) {
				var videosTemporales = $("#videosTemporales");
				_urlVideo = $("#nuevoAnuncio_urlVideo").val().replace("watch?v=", "v/");
				var elemento = 
					"<div class='bloqueVideo' data-video='"+$("#nuevoAnuncio_urlVideo").val()+"'>"+
						"<object>"+
							"<param name='movie' value='"+_urlVideo+"?version=3&feature=player_detailpage'>"+
							"<param name='allowFullScreen' value='true'>"+
							"<param name='allowScriptAccess' value='always'>"+
							"<embed src='"+_urlVideo+"?version=3&feature=player_detailpage&showinfo=0&autohide=1&rel=0' type='application/x-shockwave-flash' allowfullscreen='true' allowScriptAccess='always' wmode=transparent width='60' height='60' showinfo=0>"+
						"</object>"+
						"<span class='borrar'>X</span>"+
					"</div>";
				
				videosTemporales.append(elemento);
				videosTemporales.find(".bloqueVideo .borrar").unbind();
				videosTemporales.find(".bloqueVideo").each(function(){
					var elemento = $(this);
					
					$(this).find(".borrar").on({
						click: function() {
							elemento.remove();
						}
					});
				});
				
				
				$("#nuevoAnuncio_urlVideo").val("");
				if (videosTemporales.find(".bloqueVideo").length > 20) {
					$("#nuevoAnuncio_urlVideo").hide();
				}
			}
			else
				alert("La url debe ser de Youtube.");
		}
	}
}


/*
	Valida los campos para guardar los datos de perfil de usuario
*/
function nuevoAnuncio_validarCampos_inmueble() {
	if (!vacio($("#crearAnuncio_titulo").val(), $("#crearAnuncio_titulo").attr("placeholder"))) {
		if (!vacio(($("#crearAnuncio_categoria").val() != -1 ? $("#crearAnuncio_categoria").val() : ""), $("#crearAnuncio_categoria option[value='-1']").text())) {
			if (!vacio(($("#crearAnuncio_tipo").val() != -1 ? $("#crearAnuncio_tipo").val() : ""), $("#crearAnuncio_tipo option[value='-1']").text())) {
				if (!vacio(($("#crearAnuncio_transaccion").val() != -1 ? $("#crearAnuncio_transaccion").val() : ""), $("#crearAnuncio_transaccion option[value='-1']").text())) {
					if (!vacio($("#crearAnuncio_precio").val(), $("#crearAnuncio_precio").attr("placeholder"))) {
						_precio = $("#crearAnuncio_precio").val().replace(/\$/g, "").replace(/,/g, "");
						$("#crearAnuncio_precio").val(_precio);
						if (flotante($("#crearAnuncio_precio").val(), $("#crearAnuncio_precio").attr("placeholder"))) {
							if (!vacio($("#crearAnuncio_calleNumero").val(), $("#crearAnuncio_calleNumero").attr("placeholder"))) {
								if (!vacio(($("#crearAnuncio_estado").val() != -1 ? $("#crearAnuncio_estado").val() : ""), $("#crearAnuncio_estado option[value='-1']").text())) {
									if (!vacio(($("#crearAnuncio_ciudad").val() != -1 ? $("#crearAnuncio_ciudad").val() : ""), $("#crearAnuncio_ciudad option[value='-1']").text())) {
										if (!vacio(($("#crearAnuncio_colonia").val() != -1 ? $("#crearAnuncio_colonia").val() : ""), $("#crearAnuncio_colonia option[value='-1']").text())) {
											if ($("#_crearAnuncioLatitud").val() != "") {
												if (!vacio($("#crearAnuncio_descripcion").val(), $("#crearAnuncio_descripcion").attr("placeholder"))) {
													if (!vacio(($("#crearAnuncio_usuario").val() != -1 ? $("#crearAnuncio_usuario").val() : ""), $("#crearAnuncio_usuario option[value='-1']").text())) {
														var continuar = true;
															
														if (parseInt($("#idInmueble2").val()) == -1) {//nuevo
															if ($("#imagenesTemporales .bloqueImagen").length == 0) {
																alert("Ingrese al menos una imágen para el inmueble");
																return false;
															}
															else {
																if($("input[name='radioImagenPrincipal']:checked").length == 0) {
																	alert("Selecciona una imágen como la principal");
																	return false;
																}
															}
														}
														else {//modificar
															if($("input[name='radioImagenPrincipal']:checked").length == 0) {
																alert("Selecciona una imágen como la principal");
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_dimesionTotal").val())) {
															if (!flotante($("#crearAnuncio_dimesionTotal").val(), $("#crearAnuncio_dimesionTotal").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_dimensionConstruida").val())) {
															if (!flotante($("#crearAnuncio_dimensionConstruida").val(), $("#crearAnuncio_dimensionConstruida").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_cuotaMantenimiento").val())) {
															_precio = $("#crearAnuncio_cuotaMantenimiento").val().replace(/\$/g, "").replace(/,/g, "");
															$("#crearAnuncio_cuotaMantenimiento").val(_precio);
															
															if (!flotante($("#crearAnuncio_cuotaMantenimiento").val(), $("#crearAnuncio_cuotaMantenimiento").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_elevador").val())) {
															if (!entero($("#crearAnuncio_elevador").val(), $("#crearAnuncio_elevador").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_estacionamientoVisitas").val())) {
															if (!entero($("#crearAnuncio_estacionamientoVisitas").val(), $("#crearAnuncio_estacionamientoVisitas").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_numeroOficinas").val())) {
															if (!entero($("#crearAnuncio_numeroOficinas").val(), $("#crearAnuncio_numeroOficinas").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_metrosFrente").val())) {
															if (!flotante($("#crearAnuncio_metrosFrente").val(), $("#crearAnuncio_metrosFrente").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_metrosFondo").val())) {
															if (!flotante($("#crearAnuncio_metrosFondo").val(), $("#crearAnuncio_metrosFondo").attr("placeholder"))) {
																return false;
															}
														}
														
														if (!isVacio($("#crearAnuncio_cajonesEstacionamiento").val())) {
															if (!entero($("#crearAnuncio_cajonesEstacionamiento").val(), $("#crearAnuncio_cajonesEstacionamiento").attr("placeholder"))) {
																return false;
															}
														}
														
														//solo para casas y departamentos: wcs y recamaras son obligatorios
														if ((parseInt($("#crearAnuncio_tipo").val()) == 1) || (parseInt($("#crearAnuncio_tipo").val()) == 2)) {//casa o departamento
															if (!vacio(($("#crearAnuncio_wcs").val() != -1 ? $("#crearAnuncio_wcs").val() : ""), $("#crearAnuncio_wcs option[value='-1']").text())) {
																if (vacio(($("#crearAnuncio_recamaras").val() != -1 ? $("#crearAnuncio_recamaras").val() : ""), $("#crearAnuncio_recamaras option[value='-1']").text()))
																	return false;
															}
															else
																return false;
														}
														
														
														if (!isVacio($("#crearAnuncio_codigo").val())) {
															$.ajax({
																url: "lib_php/updInmueble.php",
																type: "POST",
																dataType: "json",
																data: {
																	id: $("#idInmueble2").val(),
																	validarCodigo: 1,
																	usuario: $("#crearAnuncio_usuario").val(),
																	codigo: $("#crearAnuncio_codigo").val()
																}
															}).always(function(respuesta_json){
																if (respuesta_json.isExito == 1) {
																	nuevoAnuncio_save();
																}
																else
																	alert(respuesta_json.mensaje);
															});
														}
														else {
															if (continuar) {
																nuevoAnuncio_save();
															}
														}
													}
												}
											}
											else {
												alert("Agrege la posición del inmueble en el mapa.");
												return false;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


/*
	Guarda un nuevo inmueble
*/
function nuevoAnuncio_save() {
	$("#_crearAnuncioCP").val($("#crearAnuncio_colonia option[value='"+$("#crearAnuncio_colonia").val()+"']").attr("data-cp"));
		
	
	//ajuste para imagenes
	if ($("#imagenesTemporales .bloqueImagen").length > 0) {
		var imagenes = Array();
		var _tempPrincipal = Array();
		
		$("#imagenesTemporales .bloqueImagen").each(function(){
			imagenes.push($(this).attr("data-imagen"));
			_tempPrincipal.push(($(this).find("input[name='radioImagenPrincipal']").prop("checked") ? 1 : 0));
		});
		
		$("#imagen2").val(imagenes.toString());
		$("#imagenPrincipal2").val(_tempPrincipal.toString());
	}
	
	
	if ($("#idImagenPrincipal").length == 1) {
		$("#idImagenPrincipal").val($("#galeriaImagenes input[name='radioImagenPrincipal']:checked").attr("data-id"));
	}
	
	//ajuste para videos
	if ($("#videosTemporales .bloqueVideo").length > 0) {
		var videos = Array();
		
		$("#videosTemporales .bloqueVideo").each(function(){
			videos.push($(this).attr("data-video"));
		});
		
		$("#videos").val(videos.toString());
	}
	
	//si estos checks estan activos (y no tienen un valor en el input, asignar automaticamente 1)
	$("#chkCuotaMantenimiento2,#chkElevador2,#chkEstacionamientoVisitas2,#chkCajonesEstacionamiento2,#chkNumeroOficinas2").each(function(){
		var elemento = $(this);
		
		if ($(this).prop("checked")) {
			if (elemento.parent().find("input[type='text']").val() == "")
				elemento.parent().find("input[type='text']").val("1");
		}
	});
	
	
	$("#btnGuardar7").hide();
	$("#mensajeTemporal7").show();
	$(".formularioInmueble_cuerpo").css({cursor:"wait"});	
	
	
	$("#subirAnuncio").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				gotoURL("inmueble.php");
			}
			else
				alert(respuesta_json.mensaje);
		}
	});
}
/**/