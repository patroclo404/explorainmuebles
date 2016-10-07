// JavaScript Document
var urlArchivos = "images/images/";


$(document).ready(function(){
	if (typeof post_orden !== 'undefined') {//orden recibido por get
		$("#catalogo_orden p").attr("data-value", post_orden);
		$("#catalogo_orden p").text($("#catalogo_orden li.lista li[data-value='"+post_orden+"']").text());
	}
	else {//orden por default
		$("#catalogo_orden p").attr("data-value", 3);
		$("#catalogo_orden p").text($("#catalogo_orden li.lista li[data-value='"+3+"']").text());
	}


	template_addListener_busquedas(catalogo_fcnBuscarRedireccionar, catalogo_fcnBuscar);

	//agrega evento para cuando se cambie de orden de busqueda
	$("#catalogo_orden").each(function(){
		var elemento = $(this);

		$(this).find("li.lista li").on({
			click: function() {
				catalogo_fcnBuscarRedireccionar(false);
			}
		});
	});

	//agrega evento para cuando se cambie de numero de paginacion
	$("#catalogo_paginacion_elem span").each(function(){
		$(this).on({
			click: function() {
				$("#catalogo_paginacion_elem span").removeClass("active");
				$(this).addClass("active");
				catalogo_fcnBuscarRedireccionar();
			}
		});
	});


});


/*
	funcion a realizar cuando se da click en el boton de buscar
*/
function catalogo_fcnBuscar() {
	/*
		Obtiene de la url, la pagina actual (de la busqueda) y el elem para la paginacion
	*/
	_length = (window.location+"").indexOf("?") + 1;
	urlParametros = (window.location+"").substr(_length, (window.location+"").length);
	tempArray = urlParametros.split("&");

	busquedaElementos_pagina = (typeof post_pagina !== 'undefined' ? post_pagina : 0);
	busquedaElementos_elem = (typeof post_elem !== 'undefined' ? post_elem : 10);

	for (var x = 0; x < tempArray.length; x++) {
		_partes = tempArray[x].split("=");
		tipoFiltro = _partes[0];
		valores = _partes[1];

		switch(tipoFiltro) {
			case "pagina":
				busquedaElementos_pagina = parseInt(valores);
				break;
			case "elem":
				busquedaElementos_elem = parseInt(valores);
				break;
		}
	}


	$(".catalogo_cuerpo .columna2 .cadenaResultados").html("<span style='color:#852c2b; font-family:\"GothamNarrowBlack\",sans-serif;'>Buscando ...</span>");
	$(".paginacion_otros .otros").hide();
	$(".paginacion_otros .paginacionNumeracion").html("");


	var datos = template_busquedas_getData();

	if (typeof post_preciosMin2 !== 'undefined') {
		if (parseInt(datos.preciosMin) == -1) {
			datos.preciosMin = post_preciosMin;
		}
		if (parseInt(datos.preciosMax) == -1) {
			datos.preciosMax = post_preciosMax;
		}
	}

	//orden a mostrar los resultados
	if (parseInt($("#catalogo_orden p").attr("data-value")) != -1) {
		switch(parseInt($("#catalogo_orden p").attr("data-value"))) {
			case 1://mayor precio
				datos["orderPrecio"] = 1;
				break;
			case 2://menor precio
				datos["orderPrecio"] = 0;
				break;
			case 3://mayor precio
				datos["orderNuevo"] = 1;
				break;
		}
	}


	datos["pagina"] = busquedaElementos_pagina;
	datos["elem"] = busquedaElementos_elem;


	$.ajax({
		url: "lib_php/consInmueble.php",
		type: "POST",
		dataType: "json",
		data: datos
	}).always(function(respuesta_json){
		catalogo_mostrarInmuebles(respuesta_json);
	});
}


/*
	redirecciona con los parametros get

		* _setPagina:	[Boolean], por default inicializa la pagina en 0
*/
function catalogo_fcnBuscarRedireccionar(_setPagina) {
	setPagina = true;

	if (_setPagina != null)
		setPagina = _setPagina;

	/*
		Obtiene de la url, la pagina actual (de la busqueda) y el elem para la paginacion
	*/
	_length = (window.location+"").indexOf("?") + 1;
	if (_length > 0) {
		urlParametros = (window.location+"").substr(_length, (window.location+"").length);
		tempArray = urlParametros.split("&");
		_keyOrden = -1;
		_keyElem = -1;
		_valElem = 10;
		_keyPagina = -1;
		_valPagina = 0;

		for (var x = 0; x < tempArray.length; x++) {
			_partes = tempArray[x].split("=");
			tipoFiltro = _partes[0];
			valores = _partes[1];

			switch(tipoFiltro) {
				case "orden":
					_keyOrden = parseInt(x);
					break;
				case "elem":
					_keyElem = parseInt(x);
					break;
				case "pagina":
					_keyPagina = parseInt(x);
					_valPagina = valores;
					break;
			}
		}


		$(".catalogo_cuerpo .columna2 .resultadosConsulta").html("<span style='color:#852c2b; font-family:\"GothamNarrowBlack\",sans-serif;'>Buscando ...</span>");
		$(".paginacion_otros .otros").hide();
		$(".paginacion_otros .paginacionNumeracion").html("");


		//ordenamiento
		if (_keyOrden == -1) {
			_keyOrden = tempArray.length;
			tempArray.push("orden="+$("#catalogo_orden p").attr("data-value"));
		}
		else
			tempArray[_keyOrden] = "orden="+$("#catalogo_orden p").attr("data-value");

		//paginacion
		if (_keyElem == -1) {
			_keyElem = tempArray.length;
			tempArray.push("elem="+$("#catalogo_paginacion_elem span.active").text());
			_valElem = $("#catalogo_paginacion_elem span.active").text();
		}
		else
			tempArray[_keyElem] = "elem="+$("#catalogo_paginacion_elem span.active").text();


		if ((setPagina) && (_keyPagina != -1)) {
			tempArray[_keyPagina] = "pagina=0";
			_valPagina = 0;
		}


		var datos = template_busquedas_getData();
		var params = "";


		datos["orden"] = $("#catalogo_orden p").attr("data-value");
		datos["elem"] = $("#catalogo_paginacion_elem span.active").text();
		datos["pagina"] = _valPagina;
		datos["elem"] = _valElem;

		var datos2 = datos;


		for (var key in datos) {
			if ((key == "transaccion") || (key == "tipoInmueble") || (key == "estado") || (key == "ciudad")) {
				if (params == "")
					params += "?"
				else
					params += "&";

				params += key+"="+datos[key];
			}
		}


		if (typeof post_preciosMin2 !== 'undefined') {
			//params += "&preciosMin2="+post_preciosMin2+"&preciosMax2="+post_preciosMax2;
			//datos2["preciosMin2"] = post_preciosMin2;
			//datos2["preciosMax2"] = post_preciosMax2;
		}


		params2 =
			(datos2["transaccion"] == 1 ? "renta" : (datos2["transaccion"] == 2 ? "venta" : "renta-vacacional"))+
			"/"+($("#template_busqueda_tipoInmueble p").text() != "" ? ($("#template_busqueda_tipoInmueble p").text().toLowerCase()+($("#template_busqueda_tipoInmueble p").attr("data-value") != 6 ? "s" : "es")) : "todos-los-tipos")+
			"/"+($("#template_busqueda_estado p").text() != "" ? $("#template_busqueda_estado p").text() : "todo-mexico")+
			"/"+($("#template_busqueda_municipio p").text() != "" ? $("#template_busqueda_municipio p").text() : "todas-las-ciudades");


		$.ajax({
			url: "lib_php/updFiltros.php",
			type: "POST",
			dataType: "json",
			data: {
				set: 1,
				parametros: datos2
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1)
				gotoURL(""+params2);
		});
		//gotoURL("catalogo.php"+params);
	}
	else {
		var datos = template_busquedas_getData();
		var datos2 = datos;
		var params = "";

		for (var key in datos) {
			if ((key == "transaccion") || (key == "tipoInmueble") || (key == "estado") || (key == "ciudad")) {
				if (params == "")
					params += "?"
				else
					params += "&";

				params += key+"="+datos[key];
			}
		}


		if (typeof post_preciosMin2 !== 'undefined') {
			datos2["preciosMin2"] = 0;
			datos2["preciosMax2"] = 0;
			//params += "&preciosMin2="+post_preciosMin2+"&preciosMax2="+post_preciosMax2;
		}
		var precio_min = parseInt($("#template_busqueda_precios_min p").attr('data-value'));
		var precio_max = parseInt($("#template_busqueda_precios_max p").attr('data-value'));
		console.log(precio_min, precio_max);

		if (precio_min > -1 ){
			datos2["preciosMin2"] = precio_min;
		}

		if (precio_max > -1 ){
			datos2["preciosMax2"] = precio_max;
		}

		_valElem = parseInt($("#catalogo_paginacion_elem span.active").text());

		if (parseInt($("#catalogo_paginacion_elem span.active").text()) != 10) {
			datos2["elem"] = _valElem;
		}


		params2 =
			(datos2["transaccion"] == 1 ? "renta" : (datos2["transaccion"] == 2 ? "venta" : "renta-vacacional"))+
			"/"+($("#template_busqueda_tipoInmueble p").text() != "" ? ($("#template_busqueda_tipoInmueble p").text().toLowerCase()+($("#template_busqueda_tipoInmueble p").attr("data-value") != 6 ? "s" : "es")) : "todos-los-tipos")+
			"/"+($("#template_busqueda_estado p").text() != "" ? $("#template_busqueda_estado p").text() : "todo-mexico")+
			"/"+($("#template_busqueda_municipio p").text() != "" ? $("#template_busqueda_municipio p").text() : "todas-las-ciudades");


		$.ajax({
			url: "lib_php/updFiltros.php",
			type: "POST",
			dataType: "json",
			data: {
				set: 1,
				parametros: datos2
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1)
				gotoURL(""+params2);
		});
	}
}


/*
	Muestra los elementos encontrados por la busqueda
*/
function catalogo_mostrarInmuebles(json_datos) {
	var objContenedor = $(".catalogo_cuerpo .columna2 .resultadosConsulta");
	objContenedor.html("");

	var datos = template_busquedas_getData();
	var cadenaResultados = "";

	if (parseInt(datos.tipoInmueble) != -1) {
		cadenaResultados += $("#template_busqueda_tipoInmueble li.lista li[data-value='"+datos.tipoInmueble+"']").text();
		cadenaResultados += parseInt(datos.tipoInmueble) != 6 ? "s" : "es";
	}
	if (parseInt(datos.transaccion) != -1) {
		cadenaResultados += "propiedades en "+$("#template_busqueda_transaccion li.lista li[data-value='"+datos.transaccion+"']").text();
	}
	if (parseInt(datos.colonia) != -1) {
		cadenaResultados += " en la colonia "+$("#template_busqueda_colonia li.lista li[data-value='"+datos.colonia+"']").text();
	}
	if (parseInt(datos.ciudad) != -1) {
		cadenaResultados += " en "+$("#template_busqueda_municipio li.lista li[data-value='"+datos.ciudad+"']").text();
	}
	if (parseInt(datos.estado) != -1) {
		cadenaResultados += ", "+$("#template_busqueda_estado li.lista li[data-value='"+datos.estado+"']").text();
	}

	$(".catalogo_cuerpo .columna2 .cadenaResultados").html(cadenaResultados);
	maxCadena = 30;


	for (var x = 0; x < json_datos.datos.length; x++) {
		moneda = new NumeroFormato(json_datos.datos[x].precio);
		textTitulo = (json_datos.datos[x].titulo).length > maxCadena ? ((json_datos.datos[x].titulo).substr(0, (maxCadena - 3))+"...") : json_datos.datos[x].titulo;
		textDescription = (json_datos.datos[x].descripcion).length > 135 ? ((json_datos.datos[x].descripcion).substr(0, (135 - 3))+"...") : json_datos.datos[x].descripcion;
		etiquetaPrecio = json_datos.datos[x].transaccion != 3 ? "Precio" : "Precio por noche";
		_newUrl =
			(json_datos.datos[x].transaccion == 1 ? "renta" : (json_datos.datos[x].transaccion == 2 ? "venta" : "renta-vacacional"))+"/"+
			(json_datos.datos[x].tipoInmueble == -1 ? "todos-los-tipos" : ($("#template_busqueda_tipoInmueble li.lista li[data-value='"+json_datos.datos[x].tipoInmueble+"']").text()))+"/"+
			json_datos.datos[x].estadoNombre+"/"+
			json_datos.datos[x].ciudadNombre+"/"+
			json_datos.datos[x].id;

		elemento =
			"<div class='template_catalogo_contenedorInfo row'>"+
				"<table>"+
					"<tbody>"+
						"<tr>"+
							"<td class='imagen'>"+
								"<div style='background:url("+urlArchivos+json_datos.datos[x].imagen+") no-repeat center center / 100% auto;' onclick='catalogo_redirecciona_regresar(\""+_newUrl+"\");'></div>"+
							"</td>"+
							"<td class='descripcion'>"+
								"<div class='like'>"+
									"<h2 onclick='catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+textTitulo+"</h2>"+(parseInt(json_datos.datos[x].like) != -1 ? ("<a class='btnBotones estrella "+(parseInt(json_datos.datos[x].like) != 0 ? "activo" : "")+"' data-id='"+json_datos.datos[x].like+"' data-inmueble='"+json_datos.datos[x].id+"'>Like</a>") : "<a class='btnBotones estrella' data-id='"+json_datos.datos[x].like+"' data-inmueble='"+json_datos.datos[x].id+"'>Like</a>")+
								"</div>"+
								"<p class='btns'>"+
									(json_datos.datos[x].dimensionTotal != "" ? "<a class='otrosBotones dimensionTotal' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+json_datos.datos[x].dimensionTotal+" m<sup>2</sup></a>" : "")+
									(json_datos.datos[x].dimensionConstruida != "" ? "<a class='otrosBotones dimensionConstruida' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+json_datos.datos[x].dimensionConstruida+" m<sup>2</sup></a>" : "")+
									(json_datos.datos[x].wcs != "" ? "<a class='otrosBotones wcs' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+(json_datos.datos[x].wcs % 1 == 0 ? parseInt(json_datos.datos[x].wcs) : parseFloat(json_datos.datos[x].wcs).toFixed(1))+"</a>" : "")+
									(json_datos.datos[x].recamaras != "" ? "<a class='otrosBotones recamaras' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+json_datos.datos[x].recamaras+"</a>" : "")+
								"</p>"+
								"<div class='info' onclick='catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+
									"<h3>"+json_datos.datos[x].coloniaNombre+"</h3>"+
									"<p>"+json_datos.datos[x].ciudadNombre+", "+json_datos.datos[x].estadoNombre+", México</p>"+
									"<p>C.P. "+json_datos.datos[x].cpNombre+"</p><br />"+
									"<p class='descripcion'>"+textDescription+"</p>"+
								"</div>"+
								"<div class='precioVerMas'><span class='precio' onclick='catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+etiquetaPrecio+": $"+(moneda.formato(2, true).replace(".00", ""))+" MXN</span><span class='verMas' onclick='catalogo_redirecciona_regresar(\""+_newUrl+"\");'>Ver más</span></div>"+
							"</td>"+
						"</tr>"+
					"</tbody>"+
				"</table>"+
			"</div>";

		elemento =
			'<div class="row property items" id='+json_datos.datos[x].id+' onclick="catalogo_redirecciona_regresar(\''+_newUrl+'\');">'+
				'<div class="col-lg-6 col-sm-6 col-xs-12"><img class="img-responsive" src="'+urlArchivos+json_datos.datos[x].imagen+'"></div>'+
				'<div class="col-lg-6 col-sm-6 col-xs-12">'+
					'<h2 class="property header" onclick="catalogo_redirecciona_regresar("'+_newUrl+'");">'+textTitulo+'</h2>'+
					'<span class="property subheader">'+json_datos.datos[x].coloniaNombre+' | '+json_datos.datos[x].ciudadNombre+", "+json_datos.datos[x].estadoNombre+', México '+
					'C.P. '+json_datos.datos[x].cpNombre+'</span>'+
					'<div class="information property">'+
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info hidden-xs">'+
							'<div><i class="flaticon-graphicseditor63"></i> TERRENO <br />'+
							(json_datos.datos[x].dimensionTotal != "" ? "<a class='otrosBotones dimensionTotal' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+json_datos.datos[x].dimensionTotal+" m<sup>2</sup></a>" : "-")+
							'</div>'+
						'</div>'+
						'<div class="col-md-4 col-lg-4 col-sm-6 col-xs-6 property main-info hidden-xs">'+
							'<div><i class="flaticon-house158"></i> CONSTRUCCI&Oacute;N <br />'+
							(json_datos.datos[x].dimensionConstruida != "" ? "<a class='otrosBotones dimensionConstruida' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+json_datos.datos[x].dimensionConstruida+" m<sup>2</sup></a>" : "-")+
							'</div>'+
						'</div>'+
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">'+
							'<div><i class="flaticon-beds2"></i> CUARTOS <br />'+
							(json_datos.datos[x].recamaras != "" ? "<a class='otrosBotones recamaras' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+json_datos.datos[x].recamaras+"</a>" : "-")+
							'</div>'+
						'</div>'+
						'<div class="col-md-2 col-lg-2 col-sm-6 col-xs-6 property main-info">'+
							'<div><i class="flaticon-shower15"></i> BA&Ntilde;OS <br />'+
							(json_datos.datos[x].wcs != "" ? "<a class='otrosBotones wcs' href='javascript:catalogo_redirecciona_regresar(\""+_newUrl+"\");'>"+(json_datos.datos[x].wcs % 1 == 0 ? parseInt(json_datos.datos[x].wcs) : parseFloat(json_datos.datos[x].wcs).toFixed(1))+"</a>" : "-")+
							'</div>'+
						'</div>'+

					'</div>'+
					'<div class="property descriptions hidden-xs">'+textDescription+'</div>'+
					'<div class="property price"><span class="precio" onclick="catalogo_redirecciona_regresar(\''+_newUrl+'\');">'+etiquetaPrecio+': $'+(moneda.formato(2, true).replace(".00", ""))+' MXN</span></div>'+
				'</div>'+
			'</div>';

		objContenedor.append(elemento);
	}

	if (json_datos.datos.length == 0)
		objContenedor.html("No tenemos resultados para tu búsqueda. Explora Inmuebles es un nuevo portal con las mejores opciones para rentar o comprar inmuebles. Pronto tendremos muchos resultados cerca de ti.");
	else {
		template_eventosFavoritos();
		/*
		Quitar cuando se haga el css de la paginacion.
		 */

		if (parseInt(json_datos.maxPaginas) > 1) {
			catalogo_sistemaPaginacion(json_datos.pagina, json_datos.elem, json_datos.numeroElementos, json_datos.maxPaginas);
		}
	}
}


/*
	Genera y actualiza el sistema de paginacion.

		* pagina:			Integer, es el numero de pagina actual (empezando en cero)
		* elem:				Integer, es el numero de elementos a mostrar por cada pagina
		* numeroElementos:	Integer, es el numero de elementos totaltes por la busqueda
		* maxPaginas:		Integer, es el numero maximo de paginas a mostrar (empezando en cero cuando existe al menos un resultado; 0 cuando no hay resultados)
*/
function catalogo_sistemaPaginacion(pagina, elem, numeroElementos, maxPaginas) {
	var objDiv = $(".paginacion_otros .paginacionNumeracion");
	objDiv.html("");
	pagina = Number(pagina);
	elem = Number(elem);
	numeroElementos = Number(numeroElementos);
	maxPaginas = Number(maxPaginas);
	numAntesDespues = 5;


	/*
		Obtiene de la url, la pagina actual (de la busqueda) y el elem para la paginacion
	*/
	_length = (window.location+"").indexOf("?") + 1;
	urlParametros = (window.location+"").substr(_length, (window.location+"").length);
	tempArray = urlParametros.split("&");
	_keyPagina = -1;
	_keyElem = -1;

	for (var x = 0; x < tempArray.length; x++) {
		_partes = tempArray[x].split("=");
		tipoFiltro = _partes[0];
		valores = _partes[1];

		switch(tipoFiltro) {
			case "pagina":
				_keyPagina = parseInt(x);
				break;
			case "elem":
				_keyElem = parseInt(x);
				break;
		}
	}


	if (_keyPagina == -1) {
		_keyPagina = tempArray.length;
		tempArray.push("pagina=0");
	}


	var cadena = "";

	if (pagina > 0) {
		tempArray[_keyPagina] = "pagina="+(pagina - 1);
		cadena += "<a data-pagina='"+(pagina - 1)+"' class='anterior'>&lt; Anterior</a>";

		posIni = (pagina - numAntesDespues) < 0 ? 0 : (pagina - numAntesDespues);

		for (var x = posIni; x < pagina; x++) {
			tempArray[_keyPagina] = "pagina="+x;
			cadena += "<a data-pagina='"+x+"'>"+(x + 1)+"</a>";
		}
	}


	tempArray[_keyPagina] = "pagina="+pagina;
	cadena += "<a class='active' data-pagina='"+pagina+"'>"+(pagina + 1)+"</a>";


	if (pagina < (maxPaginas - 1)) {
		posFin = (pagina + numAntesDespues) >= maxPaginas ? (maxPaginas - 1) : (pagina + numAntesDespues);

		for (var x = (pagina + 1); x <= posFin; x++) {
			tempArray[_keyPagina] = "pagina="+x;
			cadena += "<a data-pagina='"+x+"'>"+(x+ 1)+"</a>";
		}

		tempArray[_keyPagina] = "pagina="+(pagina + 1);
		cadena += "<a data-pagina='"+(pagina + 1)+"' class='siguiente'>Siguiente &gt;</a>";
	}

	objDiv.html(cadena);
	$(".paginacion_otros .otros").show();

	objDiv.find("a").each(function(){
		var elemento = $(this);

		$(this).on({
			click: function() {
				$.ajax({
					url: "lib_php/updFiltros.php",
					type: "POST",
					dataType: "json",
					data: {
						updateParam: 1,
						nombre : "pagina",
						valor: elemento.attr("data-pagina")
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1)
						gotoURL(window.location);
				});
			}
		});
	});
}


/*
	Redireccion a la url, pero antes, asigna a la seccion el parametro de regresar
*/
function catalogo_redirecciona_regresar(newUrl) {
	$.ajax({
		url: "lib_php/updFiltros.php",
		type: "POST",
		dataType: "json",
		data: {
			updateParam: 1,
			nombre : "regresar",
			valor: 1
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1)
			gotoURL(newUrl);
	});
}


/**/
