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
		mostrarCamposExistentes();//definir en cada js de la interfaz
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
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 30;
		var textTitulo = (positions[pos].titulo).length > maxCadena ? ((positions[pos].titulo).substr(0, (maxCadena - 3))+"...") : positions[pos].titulo;
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textTitulo+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(1).width()+"' style='text-align:center;'>"+$("#categoria option[value='"+positions[pos].categoria+"']").text()+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(2).width()+"' style='text-align:center;'>"+$("#tipo option[value='"+positions[pos].tipo+"']").text()+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(3).width()+"' style='text-align:center;'>"+positions[pos].codigo+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(4).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarTransacciones("+pos+");'>Transacción</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(5).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarImagenes("+pos+");'>Imágenes</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(6).width()+"' style='text-align:center;'><a href='javascript:inmueble_abrirModificarVideos("+pos+");'>Videos</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
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
					"<td style='text-align:left;'><a href='"+urlArchivos+datos_json[x].campo2+"' target='_blank'>Imágen "+(x + 1)+"</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; inmueble_abrirModificarImagenes("+posit+", "+datos_json[x].campo1+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>";
			
		contenedorDatos.append(divImagen);
		inmueble_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2));
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
	consultarTuplasExistentesPaginacion("updPositionsInmueble.php", true);
	$("#template_buscador").attr("placeholder", "Buscar por título, descripción o código");
	
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
				consultarTuplasExistentesPaginacion("updPositionsInmueble.php", true, {palabra: $("#template_buscador").val()});
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
	
	
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkNumeroOficinas").on({
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
}


/*
	Cierra todos los poups de la interfaz actual
*/
function inmueble_cerrarPopUp(){
	$("#backImage").hide();
	$("#inmueble_abrirModificarImagenes").hide();
	$("#inmueble_abrirModificarVideos").hide();
	$("#inmueble_abrirModificarTransacciones").hide();
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
		$("#vendedor").show();
		$("#inmobiliaria").hide();

		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Inmueble");
			
			var arrayTodosCampos = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "descripcion", "antiguedad", "codigo", "dimensionTotal", "dimensionConstruida", "dimensionUtil", "estadoConservacion", "amueblado", "cocinaEquipada", "estudio", "cuartoServicio", "cuartoTV", "bodega", "terraza", "jardin", "areaJuegosInfantiles", "comedor", "serviciosBasicos", "gas", "lineaTelefonica", "internetDisponible", "aireAcondicionado", "calefaccion", "cuotaMantenimiento", "casetaVigilancia", "elevador", "seguridad", "alberca", "casaClub", "canchaTenis", "visitaMar", "jacuzzi", "estacionamientoVisitas", "permiteMascotas", "gimnasio", "centrosComercialesCercanos", "escuelasCercanas", "fumadoresPermitidos", "numeroOficinas");
			
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
			
			inmueble_onChange_estado_ciudad_colonia(positions[pos_comp].estado, positions[pos_comp].ciudad, positions[pos_comp].colonia);
			$("#tipo").change();
			inmueble_onChange_chkCamposNumericos(positions[pos_comp].cuotaMantenimiento, positions[pos_comp].elevador, positions[pos_comp].estacionamientoVisitas, positions[pos_comp].numeroOficinas);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Inmueble");
			objDiv.find("input[type='text']").val("");
			objDiv.find("select").val(-1);
			objDiv.find("textarea").val("");
			objDiv.find("input[type='checkbox']").prop("checked", false);
			
			$("#categoria").change();
			$("#estado").change();
			$("#tipo").change();
			$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkNumeroOficinas").change();
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
			map: map
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
	}
	else {
		$("#tituloEmergenteImagenes").html("Nueva Imágen");
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
	
		* estado:	[integer], es el id del estado
		* ciudad:	[integer], es el id de la ciudad
		* colonia:	[integer], es el id de la colonia
*/
function inmueble_onChange_estado_ciudad_colonia(estado, ciudad, colonia) {
	$("#estado").val(estado);
	
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
				$("#ciudad").prop("disabled", false);
				$("#ciudad option[value!='-1']").remove();
				
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					$("#ciudad").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
				}
				
				$("#ciudad").val(ciudad);
				
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
							$("#colonia").prop("disabled", false);
							$("#colonia option[value!='-1']").remove();
							
							for (var x = 0; x < respuesta_json2.datos.length; x++) {
								$("#colonia").append("<option value='"+respuesta_json2.datos[x].id+"' data-cp='"+respuesta_json2.datos[x].cp+"'>"+respuesta_json2.datos[x].nombre+"</option>");
							}
							
							$("#colonia").val(colonia);
						}
					});
				}
				else {
					$("#colonia").val("-1");
					$("#colonia").prop("disabled", true);
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


/*
	Muestra u oculta los campos de tipo numerico dependiendo del valor recibido
	
		* cuotaMantenimiento:		[Float], es el valor para la cuotaMantenimiento
		* elevador:					[integer], es el valor para el elevador
		* estacionamientoVisitas:	[integer], es el valor para el estacionamientoVisitas
		* numeroOficinas:			[Integer], es el valor para el numeroOficinas
*/
function inmueble_onChange_chkCamposNumericos(cuotaMantenimiento, elevador, estacionamientoVisitas, numeroOficinas) {
	$("#chkCuotaMantenimiento").prop("checked", (cuotaMantenimiento != "" ? true : false));
	$("#chkElevador").prop("checked", (elevador != "" ? true : false));
	$("#chkEstacionamientoVisitas").prop("checked", (estacionamientoVisitas != "" ? true : false));
	$("#chkNumeroOficinas").prop("checked", (numeroOficinas != "" ? true : false));
	$("#chkCuotaMantenimiento,#chkElevador,#chkEstacionamientoVisitas,#chkNumeroOficinas").change();
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
		map: map
	});
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	var arrayCamposEvaluar = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "codigo", "dimensionTotal", "dimensionConstruida", "dimensionUtil", "estadoConservacion", "amueblado", "cuotaMantenimiento", "elevador", "estacionamientoVisitas", "numeroOficinas");
	var arrayCamposObligatorios = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "codigo", "estadoConservacion");
	var arrayFlotantes = new Array("precio", "dimensionTotal", "dimensionConstruida", "dimensionUtil", "cuotaMantenimiento");
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
				if (!flotante($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
					return false;
			}
			
			//evalua los enteros
			if ($.inArray(arrayCamposEvaluar[x], arrayEnteros) > -1) {
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
					if (!flotante($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
						return false;
				}
				
				//evalua los enteros
				if ($.inArray(arrayCamposEvaluar[x], arrayEnteros) > -1) {
					if (!entero($("#"+arrayCamposEvaluar[x]).val(), $("#"+arrayCamposEvaluar[x]).attr("placeholder")))
						return false;
				}
			}
		}
	}
	
	
	var id = pos_comp;
	
	if (pos_comp != -1)
		id = positions[pos_comp].id;
		
	
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
	
	
	var arrayTodosCampos = new Array("titulo", "usuario", "categoria", "tipo", "precio", "calleNumero", "estado", "ciudad", "colonia", "latitud", "longitud", "descripcion", "antiguedad", "codigo", "dimensionTotal", "dimensionConstruida", "dimensionUtil", "estadoConservacion", "amueblado", "cocinaEquipada", "estudio", "cuartoServicio", "cuartoTV", "bodega", "terraza", "jardin", "areaJuegosInfantiles", "comedor", "serviciosBasicos", "gas", "lineaTelefonica", "internetDisponible", "aireAcondicionado", "calefaccion", "cuotaMantenimiento", "casetaVigilancia", "elevador", "seguridad", "alberca", "casaClub", "canchaTenis", "visitaMar", "jacuzzi", "estacionamientoVisitas", "permiteMascotas", "gimnasio", "centrosComercialesCercanos", "escuelasCercanas", "fumadoresPermitidos", "numeroOficinas");
	
	
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
	$("#nuevo").val("1");
	
	
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
/**/