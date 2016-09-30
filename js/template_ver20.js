// JavaScript Document


/*
	Funciones que se inicializan al cargar el html en todas las interfaces
*/
$(document).ready(function(){
	template_inicializarBotones();
	template_eventosFavoritos();
	
	//agrega eventos en los selects ciudades, cuando se hace click en ellos y no se ha seleccionado un estado previamente
	$("#template_venta_municipio,#template_renta_municipio,#template_rentaVac_municipio,#template_busqueda_municipio").each(function(index){
		$(this).on({
			click: function() {
				var arrayEstados = Array("template_venta_estado", "template_renta_estado", "template_rentaVac_estado", "template_busqueda_estado");
				if (parseInt($("#"+arrayEstados[index]+" p").attr("data-value")) == -1) {
					template_errorSelectMunicipio();
				}
			}
		});
	});
	
	
	//agrega eventos en los selects colonias, cuando se hace click en ellos y no se ha seleccionado una ciudad previamente
	$("#template_venta_colonia,#template_renta_colonia,#template_rentaVac_colonia,#template_busqueda_colonia").each(function(index){
		$(this).on({
			click: function() {
				var arrayCiudades = Array("template_venta_municipio", "template_renta_municipio", "template_rentaVac_municipio", "template_busqueda_municipio");
				if (parseInt($("#"+arrayCiudades[index]+" p").attr("data-value")) == -1) {
					template_errorSelectColonia();
				}
			}
		});
	});
});


/*
	Inicializa los botones comunes en todas las interfaces
*/
function template_inicializarBotones() {
	//estilos para las barras en el menu superior
	$(".template_cabecera .contenedorLogin table td.texto").each(function(){
		$(this).find(".opcionLogin .arregloBorder").css("width", $(this).innerWidth()+"px");
	});
	
	//todos los "nuevos selects" tiene esta misma funcionalidad
	$("ul.template_campos").each(function(){
		var elemento = $(this);
		var _evtBuscar;
		
		$(this).on({
			click: function() {
				var isOpen = false;
				
				if (elemento.find("li.lista").css("display") != "none")
					isOpen = true;
					
				if (isOpen) {
					elemento.find("li.lista").hide();
					elemento.find("input").val("");
				}
				else {
					if (elemento.find("li.lista ul li").length > 0) {
						$("ul.template_campos li.lista").hide();//oculta los demas si hubieras mas abiertos
						elemento.find("li.lista").show();
						elemento.find("input").focus();
					}
				}
			}
		});
		
		//agrega evento para simular cuando se escribe y hacer busqueda de acuerdo las opciones disponibles
		$(this).find("input").on({
			keyup: function(evt) {//simula el evento de buscar conforme se escribe
				clearTimeout(_evtBuscar);
				_evtBuscar = setTimeout(function(){
					var altura = 0;
			
					elemento.find("li.lista ul li").each(function(){
						if ($(this).text().toUpperCase().indexOf(elemento.find("input").val().toUpperCase()) == 0)
							return false;
						else
							altura += $(this).height();
					});
					
					elemento.find("li.lista ul").animate({
						scrollTop: altura
					}, 100);
					
					elemento.find("input").val("");
				}, 200);
			}
		});
		
		//agrega evento para marcar como selecciona la opcion que se le dio click
		$(this).find("li.lista li").on({
			click: function() {
				elemento.find("p").attr("data-value", $(this).attr("data-value"));
				elemento.find("p").text($(this).text());
				elemento.find("input").val("");
			}
		});
	});
	
	
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#template_venta_estado,#template_renta_estado,#template_rentaVac_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				template_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	
	//evento para login al presionar enter
	$("#template_login_pass").on({
		keyup: function(evt) {
			template_displayUnicode(evt, template_validaCampos_login);
		}
	});
	
	//evento para registrarse al presionar enter
	$("#template_count_confPassword").on({
		keyup: function(evt) {
			template_displayUnicode(evt, template_validaCampos_count);
		}
	});
	
	//eventos para los botones generales de share
	//facebook y twitter
	$(".template_btnsShare").on({
		click: function() {
			if ($(this).hasClass("facebook"))
				template_fb_share($(this).attr("data-url"));
			if ($(this).hasClass("twitter"))
				template_tw_share($(this).attr("data-url"), $(this).attr("data-titulo"));
		}
	});
	
	//evento para apagar los selects cuando se pierde el over en el menu
	$(".wrapperMenu2 td.texto .opcionRenta,.wrapperMenu2 td.texto2 .opcionRenta").each(function(){
		var elemento = $(this);
		
		$(this).on({
			mouseleave: function() {
				$("ul.template_campos li.lista").hide();//oculta los demas si hubieras abiertos
			}
		});
	});
	
	//agrega los eventos para el mundo
	$(".contenedorMenu2 a.btnBotones.mundo").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				if ($("#indexContenedorMapa").length == 1) {
					index_mostrarMapa(elemento.attr("data-transaccion"));
				}
				else
					gotoURL("index.php?mapa=1&transaccion="+elemento.attr("data-transaccion"));
			}
		});
	});
}


/*
	redirecciona a otra interfaz
	
		* url:	String, es la url a la que se redirecciona. Es posible pasar parametros GET
*/
function gotoURL(url){
	window.location=url;
}


/*
	Funcion de redireccionar pero con envio de parametros con POST
	Parametros:
	
		* page:		String, la pagina a la que se redireccionara
		* params:	String,	es el envio de los nombres de los parametros, seguido del valor. Primero empieza con apertura de
					llaves, seguido por grupo de parametros con valores separados por comas, en cada grupo comienza con el
					nombre del parametro encerrado en apostrofes, seguido de ":" y el valor de la variable encerrado en
					apostrofes. 
					Example: 	gotoURLPOST('file.php',{'var1':'hola','var2':'mundo'});
*/
function gotoURLPOST(page,params) {
	var body = document.body;
	form=document.createElement('form'); 
	form.method = 'POST'; 
	form.action = page;
	form.name = 'jsform';
	for (index in params) {
		var input = document.createElement('input');
		input.type='hidden';
		input.name=index;
		input.id=index;
		input.value=params[index];
		form.appendChild(input);
	}	  		  			  
	body.appendChild(form);
	form.submit();
}


/*
	Al capturar el evento de enter, entonces ejecuta la funcion recibida por parametro
	
		* evento:	Event, es el evento a capturar
		* fcn:		String, es el nombre de la funcion (sin parentesis y sin parametros)
*/
function template_displayUnicode(evento, fcn) {
	var unicode = evento.keyCode;
	
	if (unicode == 13)
		fcn();
}


/*
	Devuelve el nombre base de la ruta recibida por parametro
	
		* url:	String, es la ruta completa de la cual se quiere obtener solo el nombre del archivo final
*/
function basename(url) {
	return url.replace(/\\/g,'/').replace( /.*\//, '' );
}


/*
	Cierra todos los popups en general. Tambien ejecuta una funcion si esta se recibe por parametro.
	Esto es para personalizar el cerrado de popups
	
		* fcn:	String, nombre de la funcion a realizar despues de completar el cerrado de la pricipal
*/
function template_principalCerrarPopUp(fcn) {
	$("#template_mascaraPrincipal").hide();
	$("#template_linkNuevoAnuncio").hide();
	$("#template_errorSelectMunicipio").hide();
	$("#template_errorSelectColonia").hide();
	$("#template_alertPersonalizado").hide();
	if (fcn != null) {
		fcn();
	}
}


/*
	Muestra un popup donde indica que no es posible crear un nuevo anuncio
*/
function template_linkNuevoAnuncio() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_linkNuevoAnuncio");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Muestra un popup donde indica que debe seleccionar primero un estado
*/
function template_errorSelectMunicipio() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_errorSelectMunicipio");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Muestra un popup donde indica que debe seleccionar primero un municipio
*/
function template_errorSelectColonia() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_errorSelectColonia");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Muestra un popup donde tiene un texto personalizado antes de mostrarse dicho popup
*/
function template_alertPersonalizado() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#template_alertPersonalizado");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idCiudad:		[Integer], es el id de la ciudad ya precargado
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function template_actualizar_ciudad(nomElemento, idCiudad, idColonia) {
	var indexEstado = -1;
	var arrayEstados = Array("template_venta_estado", "template_renta_estado", "template_rentaVac_estado", "template_busqueda_estado");
	var arrayCiudades = Array("template_venta_municipio", "template_renta_municipio", "template_rentaVac_municipio", "template_busqueda_municipio");
	var arrayColonias = Array("template_venta_colonia", "template_renta_colonia", "template_rentaVac_colonia", "template_busqueda_colonia");
	
	indexEstado = arrayEstados.indexOf(nomElemento);
	
	if (indexEstado > -1) {
		objEstado = $("#"+nomElemento);
		objMunicipio = $("#"+arrayCiudades[indexEstado]);
		objColonia = $("#"+arrayColonias[indexEstado]);
		
		
		objMunicipio.find("li.lista ul").html("");
		objMunicipio.find("p").attr("data-value", -1);
		objMunicipio.find("p").text("");
		objColonia.find("li.lista ul").html("");
		objColonia.find("p").attr("data-value", -1);
		objColonia.find("p").text("");
		
		
		$.ajax({
			url: "admin/lib_php/consDireccion.php",
			type: "POST",
			dataType: "json",
			data: {
				consCiudad: 1,
				estado: objEstado.find("p").attr("data-value")
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					objMunicipio.find("li.lista ul").append("<li data-value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</li>");
				}
				
				objMunicipio.find("li.lista li").on({
					click: function() {
						objMunicipio.find("p").attr("data-value", $(this).attr("data-value"));
						objMunicipio.find("p").text($(this).text());
						template_actualizar_colonia(objMunicipio.prop("id"));
					}
				});
				
				if ((idCiudad != null) && (idCiudad != -1)) {
					objMunicipio.find("li.lista li[data-value='"+idCiudad+"']").click();
					objMunicipio.find("li.lista").hide();
					
					if ((idColonia != null) && (idColonia != -1)) {
						template_actualizar_colonia(objMunicipio.prop("id"), idColonia);
					}
				}
			}
		});
	}
}


/*
	Actualiza las colonias para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
		* idColonia:	[Integer], es el id de la colonia ya precargado
*/
function template_actualizar_colonia(nomElemento, idColonia) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("template_venta_municipio", "template_renta_municipio", "template_rentaVac_municipio", "template_busqueda_municipio");
	var arrayColonias = Array("template_venta_colonia", "template_renta_colonia", "template_rentaVac_colonia", "template_busqueda_colonia");
	
	indexMunicipio = arrayCiudades.indexOf(nomElemento);
	
	if (indexMunicipio > -1) {
		objMunicipio = $("#"+arrayCiudades[indexMunicipio]);
		objColonia = $("#"+arrayColonias[indexMunicipio]);
		
		objColonia.find("li.lista ul").html("");
		objColonia.find("p").attr("data-value", -1);
		objColonia.find("p").text("");
		
		
		$.ajax({
			url: "admin/lib_php/consDireccion.php",
			type: "POST",
			dataType: "json",
			data: {
				consColonia: 1,
				conResultados: 1,
				ciudad: objMunicipio.find("p").attr("data-value")
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					objColonia.find("li.lista ul").append("<li data-value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</li>");
				}
				
				objColonia.find("li.lista li").on({
					click: function() {
						objColonia.find("p").attr("data-value", $(this).attr("data-value"));
						objColonia.find("p").text($(this).text());
					}
				});
				
				if ((idColonia != null) && (idColonia != -1)) {
					objColonia.find("li.lista li[data-value='"+idColonia+"']").click();
					objColonia.find("li.lista").hide();
				}
			}
		});
	}
}


/*
	Realiza la busqueda de acuerdo al tipo de transaccion.
	
		transaccion:	Integer, es el tipo de transaccion a realizar. Dependiendo de este valor, tambien seran los campos a enviar
*/
function template_buscar_transaccion(transaccion) {
	var indexTransaccion = -1
	var arrayTipoInmueble = Array("template_renta_tipoInmueble", "template_venta_tipoInmueble", "template_rentaVac_tipoInmueble");
	var arrayEstados = Array("template_renta_estado", "template_venta_estado", "template_rentaVac_estado");
	var arrayCiudades = Array("template_renta_municipio", "template_venta_municipio", "template_rentaVac_municipio");
	var arrayColonias = Array("template_renta_colonia", "template_venta_colonia", "template_rentaVac_colonia");
	var arrayCodigos = Array("template_renta_codigo", "template_venta_codigo", "template_rentaVac_codigo");
	var arrayPrecios = Array("template_renta_precio", "template_venta_precio", "template_rentaVac_precio");
	
	indexTransaccion = parseInt(transaccion) - 1;
	
	var datos = {
		transaccion: transaccion,
		tipoInmueble: $("#"+arrayTipoInmueble[indexTransaccion]).find("p").attr("data-value"),
		estado: $("#"+arrayEstados[indexTransaccion]).find("p").attr("data-value"),
		ciudad: $("#"+arrayCiudades[indexTransaccion]).find("p").attr("data-value"),
		colonia: -1,
		codigo: $("#"+arrayCodigos[indexTransaccion]).val()
	};
	
	var urlGET = "?transaccion="+datos.transaccion+"&tipoInmueble="+datos.tipoInmueble+"&estado="+datos.estado+"&ciudad="+datos.ciudad+"&colonia="+datos.colonia+"&codigo="+datos.codigo;
	
	if (parseInt($("#"+arrayPrecios[indexTransaccion]).find("p").attr("data-value")) != -1) {
		datos["preciosMin2"] = $("#"+arrayPrecios[indexTransaccion]).find("p").attr("data-value").split("-")[0];
		datos["preciosMax2"] = $("#"+arrayPrecios[indexTransaccion]).find("p").attr("data-value").split("-")[1];
		urlGET += "&preciosMin2="+datos.preciosMin2+"&preciosMax2="+datos.preciosMax2;
	}
	
	gotoURL("catalogo.php"+urlGET);
}


/*
	Agrega los eventos para los elementos de busqueda normal y busqueda avanzada
	
		* fcnBuscar:		Function, es el nombre de la funcion a realizar, cuando se da click en buscar
		* fcnBuscarPost:	Function, es el nombre de la funcion a realizar despues de que se cargaron datos por POST
*/
function template_addListener_busquedas(fcnBuscar, fcnBuscarPOST) {
	//eventos para mostrar las opciones de busqueda avanzada
	$(".template_contenedorBusquedaAvanzada .textBusquedaAvanzada").on({
		click: function() {
			if ($(".template_contenedorBusquedaAvanzada .busquedaAvanzada").eq(0).css("display") == "none") {
				$(".template_contenedorBusquedaAvanzada .busquedaAvanzada").show();
			}
			else {
				$(".template_contenedorBusquedaAvanzada .busquedaAvanzada").hide();
			}
			
			$(".template_contenedorBusquedaAvanzada .opcionBusquedaAvanzada2").hide();
		}
	});
	
	//eventos para los submenus de la busqueda avanzada (muestra y oculta)
	$(".template_contenedorBusquedaAvanzada .opcionBusquedaAvanzada").each(function(index){
		$(this).on({
			click: function() {
				var isActivo = $(".template_contenedorBusquedaAvanzada .opcionBusquedaAvanzada2").eq(index).css("display") == "none" ? false : true;
				$(".template_contenedorBusquedaAvanzada .opcionBusquedaAvanzada2").hide();
				
				if (!isActivo)
					$(".template_contenedorBusquedaAvanzada .opcionBusquedaAvanzada2").eq(index).show();
			}
		});
	});
	
	//oculta todos las opciones de precios minimos y maximos hasta que se seleccione una transaccion
	$("#template_busqueda_precios_min li.lista li,#template_busqueda_precios_max li.lista li").hide();
	
	//agrega evento para cuando se cambie de transaccion; y ajusta los valores de precios maximos y minimos
	$("#template_busqueda_transaccion").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				_transaccion = $(this).attr("data-value");
				$("#template_busqueda_precios_min li.lista li,#template_busqueda_precios_max li.lista li").hide();
				$("#template_busqueda_precios_min p,#template_busqueda_precios_max p,#template_busqueda_tipoInmueble p").attr("data-value", -1);
				$("#template_busqueda_precios_min p,#template_busqueda_precios_max p,#template_busqueda_tipoInmueble p").text("");
				
				$("#template_busqueda_precios_min li.lista li,#template_busqueda_precios_max li.lista li").each(function(){
					tempArray = $(this).attr("data-transaccion").split(",");
					
					if ($.inArray(_transaccion, tempArray) > -1)
						$(this).show();
				});
				
				$("#template_busqueda_tipoInmueble li.lista li").show();
				if (parseInt(_transaccion) == 3) {
					$("#template_busqueda_tipoInmueble li.lista li").hide();
					$("#template_busqueda_tipoInmueble li.lista li[data-transaccion='3']").show();
				}
			}
		});
	});
	
	
	//agrega evento para cuando se cambie de estado, actualizar la ciudad
	$("#template_busqueda_estado").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				template_actualizar_ciudad(elemento.prop("id"));
			}
		});
	});
	
	
	//agrega evento cuando se cambian elementos flotantes
	$("#template_busqueda_cuotaMantenimiento").on({
		change: function() {
			if (!isVacio($(this).val())) {
				if (!flotante($(this).val(), $(this).attr("placeholder"))) {
					$(this).val("");
					return false;
				}
			}
		}
	});
	
	
	//agrega evento cuando se cambian elementos enteros
	$("#template_busqueda_elevador,#template_busqueda_estacionamientoVisitas,#template_busqueda_numeroOficinas").on({
		change: function() {
			if (!isVacio($(this).val())) {
				if (!entero($(this).val(), $(this).attr("placeholder"))) {
					$(this).val("");
					return false;
				}
			}
		}
	});
	
	
	//agrega el evento para realizar la busqueda
	$(".template_contenedorBusquedaAvanzada p.buscar,.template_contenedorBusquedaAvanzada p.titulo").on({
		click: function() {
			if (parseInt($("#template_busqueda_transaccion").find("p").attr("data-value")) > 0) {
				fcnBuscar();
			}
			else {
				alert("Seleccione un tipo de transacción de busqueda");
				return false;
			}
		}
	});
	
	
	//se recibieron parametros por post; por lo tanto se hacen los ajustes en los selects
	if (typeof post_tipoInmueble !== 'undefined') {
		if (parseInt(post_transaccion) != -1) {
			$("#template_busqueda_transaccion li.lista li[data-value='"+post_transaccion+"']").click();
			$("#template_busqueda_transaccion li.lista").hide();
		}
		if (parseInt(post_preciosMin) != -1) {
			$("#template_busqueda_precios_min li.lista li[data-value='"+post_preciosMin+"']").click();
			$("#template_busqueda_precios_min li.lista").hide();
		}
		if (parseInt(post_preciosMax) != -1) {
			$("#template_busqueda_precios_max li.lista li[data-value='"+post_preciosMax+"']").click();
			$("#template_busqueda_precios_max li.lista").hide();
		}
		if (parseInt(post_wcs) != -1) {
			$("#template_busqueda_wcs li.lista li[data-value='"+post_wcs+"']").click();
			$("#template_busqueda_wcs li.lista").hide();
		}
		if (parseInt(post_recamaras) != -1) {
			$("#template_busqueda_recamaras li.lista li[data-value='"+post_recamaras+"']").click();
			$("#template_busqueda_recamaras li.lista").hide();
		}
		
		
		//selects opcionales
		if (typeof post_antiguedad !== 'undefined') {
			if (parseInt(post_antiguedad) != -1) {
				$("#template_busqueda_antiguedad li.lista li[data-value='"+post_antiguedad+"']").click();
				$("#template_busqueda_antiguedad li.lista").hide();
			}
		}
		if (typeof post_estadoConservacion !== 'undefined') {
			if (parseInt(post_estadoConservacion) != -1) {
				$("#template_busqueda_estadoConservacion li.lista li[data-value='"+post_estadoConservacion+"']").click();
				$("#template_busqueda_estadoConservacion li.lista").hide();
			}
		}
		if (typeof post_amueblado !== 'undefined') {
			if (parseInt(post_amueblado) != -1) {
				$("#template_busqueda_amueblado li.lista li[data-value='"+post_amueblado+"']").click();
				$("#template_busqueda_amueblado li.lista").hide();
			}
		}
		if (typeof post_dimensionTotalMin !== 'undefined') {
			if (parseInt(post_dimensionTotalMin) != -1) {
				$("#template_busqueda_dimensionTotalMin li.lista li[data-value='"+post_dimensionTotalMin+"']").click();
				$("#template_busqueda_dimensionTotalMin li.lista").hide();
			}
		}
		if (typeof post_dimensionTotalMax !== 'undefined') {
			if (parseInt(post_dimensionTotalMax) != -1) {
				$("#template_busqueda_dimensionTotalMax li.lista li[data-value='"+post_dimensionTotalMax+"']").click();
				$("#template_busqueda_dimensionTotalMax li.lista").hide();
			}
		}
		if (typeof post_dimensionConstruidaMin !== 'undefined') {
			if (parseInt(post_dimensionConstruidaMin) != -1) {
				$("#template_busqueda_dimensionConstruidaMin li.lista li[data-value='"+post_dimensionConstruidaMin+"']").click();
				$("#template_busqueda_dimensionConstruidaMin li.lista").hide();
			}
		}
		if (typeof post_dimensionConstruidaMax !== 'undefined') {
			if (parseInt(post_dimensionConstruidaMax) != -1) {
				$("#template_busqueda_dimensionConstruidaMax li.lista li[data-value='"+post_dimensionConstruidaMax+"']").click();
				$("#template_busqueda_dimensionConstruidaMax li.lista").hide();
			}
		}
		
		//continua con la asignacion de post para luego hacer la busqueda de acuerdo a los parametros recibidos
		if (parseInt(post_tipoInmueble) != -1) {
			$("#template_busqueda_tipoInmueble li.lista li[data-value='"+post_tipoInmueble+"']").click();
			$("#template_busqueda_tipoInmueble li.lista").hide();
			
			//en caso de mandar parametros de estado, ciudad y colonia
			if (parseInt(post_estado) != -1) {
				$("#template_busqueda_estado p").attr("data-value", post_estado);
				$("#template_busqueda_estado p").text($("#template_busqueda_estado li.lista li[data-value='"+post_estado+"']").text());
				template_actualizar_ciudad("template_busqueda_estado", post_ciudad, post_colonia);
				
				setTimeout(function(){
					if (fcnBuscarPOST != null)
						fcnBuscarPOST();
					else
						$(".template_contenedorBusquedaAvanzada p.buscar").click();
				}, 1800);
			}
			else {
				if (fcnBuscarPOST != null)
					fcnBuscarPOST();
				else
					$(".template_contenedorBusquedaAvanzada p.buscar").click();
			}
		}
	}
}


/*
	Obtiene y devuelve los datos obligatorios y opcionales de la busqueda y busqueda avanzada para realizar una consulta de inmuebles
*/
function template_busquedas_getData() {
	var datos = {
		transaccion: $("#template_busqueda_transaccion").find("p").attr("data-value"),
		tipoInmueble: $("#template_busqueda_tipoInmueble").find("p").attr("data-value"),
		estado: $("#template_busqueda_estado").find("p").attr("data-value"),
		ciudad: $("#template_busqueda_municipio").find("p").attr("data-value"),
		colonia: $("#template_busqueda_colonia").find("p").attr("data-value"),
		codigo: $("#template_busqueda_codigo").val(),
		preciosMin: $("#template_busqueda_precios_min").find("p").attr("data-value"),
		preciosMax: $("#template_busqueda_precios_max").find("p").attr("data-value"),
		wcs: $("#template_busqueda_wcs").find("p").attr("data-value"),
		recamaras: $("#template_busqueda_recamaras").find("p").attr("data-value")
	}
	
	
	var _tempArraySelects = Array("template_busqueda_antiguedad", "template_busqueda_estadoConservacion", "template_busqueda_amueblado", "template_busqueda_dimensionTotalMin", "template_busqueda_dimensionTotalMax", "template_busqueda_dimensionConstruidaMin", "template_busqueda_dimensionConstruidaMax");
	//agregar opcionales selects
	for (var x = 0; x < _tempArraySelects.length; x++) {
		nombreElemento = _tempArraySelects[x].replace("template_busqueda_", "");
		if (parseInt($("#"+_tempArraySelects[x]+" p").attr("data-value")) != -1) {
			datos[nombreElemento] = $("#"+_tempArraySelects[x]+" p").attr("data-value");
		}
	}
	
	
	var _tempArrayInputs = Array("template_busqueda_cuotaMantenimiento", "template_busqueda_elevador", "template_busqueda_estacionamientoVisitas", "template_busqueda_numeroOficinas");
	//agregar opcionales flotantes y enteros
	for (var x = 0; x < _tempArrayInputs.length; x++) {
		nombreElemento = _tempArrayInputs[x].replace("template_busqueda_", "");
		if (!isVacio($("#"+_tempArrayInputs[x]).val())) {
			datos[nombreElemento] = $("#"+nombreElemento).val();
		}
	}
	
	
	//agrega opcionales checkbox
	$(".template_contenedorBusquedaAvanzada .opcionBusquedaAvanzada2 input[type='checkbox']").each(function(){
		if ($(this).prop("checked")) {
			nombreElemento = $(this).prop("id").replace("template_busqueda_", "");
			datos[nombreElemento] = 1;
		}
	});
	
	return datos;
}


/*
	Valida los campos para hacer login
*/
function template_validaCampos_login() {
	if (!vacio($("#template_login_email").val(), $("#template_login_email").attr("placeholder"))) {
		if (correoValido($("#template_login_email").val())) {
			if (!vacio($("#template_login_pass").val(), $("#template_login_pass").attr("placeholder"))) {
				$.ajax({
					url: "lib_php/login.php",
					type: "POST",
					dataType: "json",
					data: {
						FBId: $("#template_login_FBId").val(),
						nombre: $("#template_login_nombre").val(),
						email: $("#template_login_email").val(),
						password: md5Script($("#template_login_pass").val())
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						urlActual = basename(String(window.location));
						gotoURL(urlActual);
					}
					else
						alert(respuesta_json.mensaje);
				});
			}
		}
	}
}


/*
	Valida campos para crear una nueva cuenta
*/
function template_validaCampos_count() {
	if (!vacio($("#template_count_nombre").val(), $("#template_count_nombre").attr("placeholder"))) {
		if (!vacio($("#template_count_email").val(), $("#template_count_email").attr("placeholder"))) {
			if (correoValido($("#template_count_email").val())) {
				if (!vacio($("#template_count_password").val(), $("#template_count_password").attr("placeholder"))) {
					if (!vacio($("#template_count_confPassword").val(), $("#template_count_confPassword").attr("placeholder"))) {
						if ($("#template_count_password").val() == $("#template_count_confPassword").val()) {
							$.ajax({
								url: "lib_php/updUsuario.php",
								type: "POST",
								dataType: "json",
								data: {
									id: -1,
									validarEmail: 1,
									email: $("#template_count_email").val()
								}
							}).always(function(respuesta_json){
								if (respuesta_json.isExito == 1) {
									$.ajax({
										url: "lib_php/updUsuario.php",
										type: "POST",
										dataType: "json",
										data: {
											id: -1,
											nuevo: 1,
											nombre: $("#template_count_nombre").val(),
											email: $("#template_count_email").val(),
											password: md5Script($("#template_count_password").val()),
											FBId: $("#template_count_FBId").val()
										}
									}).always(function(respuesta_json2){
										if (respuesta_json2.isExito == 1) {
											if ($("#template_count_FBId").val() != "") {
												$("#template_login_email").val($("#template_count_email").val());
												$("#template_login_pass").val($("#template_count_password").val());
												
												template_validaCampos_login();
											}
											else {
												$("#template_count_nombre").val("");
												$("#template_count_email").val("");
												$("#template_count_password").val("");
												$("#template_count_FBId").val("");
												$("#template_count_confPassword").val("");
												$("#template_alertPersonalizado td").html("Gracias por registrarte!.<br /><br />Te enviamos un email para que valides tu cuenta y puedas comenzar.");
												template_alertPersonalizado();
											}
										}
									});
								}
								else {
									alert(respuesta_json.mensaje);
								}
							});
						}
						else {
							alert("Las contraseñas son diferentes, vuelva a intentarlo.");
						}
					}
				}
			}
		}
	}
}


/*
	Inicia session con FB y completa los datos para registrarse con FB
*/
function template_validaCampos_countFB() {
	FB.login(function(respuesta_json){
		if (respuesta_json.authResponse) {
			FB.api('/me', function(respuesta2_json) {
				$("#template_count_FBId").val(respuesta2_json.id);
				$("#template_count_nombre").val(respuesta2_json.name);
				$("#template_count_email").val(respuesta2_json.email);
				$("#template_count_password").val(respuesta2_json.email);
				$("#template_count_confPassword").val(respuesta2_json.email);
				
				template_validaCampos_count();
			});
		}
	},{
		scope:'email,public_profile,user_birthday'
	});
}


/*
	Inicia session con FB y completa los datos para iniciar session en explora inmuebles
*/
function template_validaCampos_loginFB() {
	FB.login(function(respuesta_json){
		if (respuesta_json.authResponse) {
			FB.api('/me', function(respuesta2_json) {
				$("#template_login_FBId").val(respuesta2_json.id);
				$("#template_login_nombre").val(respuesta2_json.name);
				$("#template_login_email").val(respuesta2_json.email);
				$("#template_login_pass").val(respuesta2_json.email);
				
				template_validaCampos_login();
			});
		}
	},{
		scope:'email,public_profile,user_birthday'
	});
}


/*
	Muestra el dialog de fb para el share
	
		* urlObject:	String, es la url a la que apunta el share
*/
function template_fb_share (urlObject) {
	FB.ui({
		method: 'share',
		href: 'http://www.explorainmuebles.com/'+urlObject
	}, function(response){});
}


/*
	Muestra un popup con la info para el share de twitter
	
		* urlObject:	String, es la url a la que apunta el share
		* dataText:		String, es el titulo de la pagina
*/
function template_tw_share(urlObject, dataText) {
	var intentRegex = /twitter\.com(\:\d{2,4})?\/intent\/(\w+)/,
      windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
      width = 550,
      height = 420,
      winHeight = screen.height,
      winWidth = screen.width;
	  
	left = Math.round((winWidth / 2) - (width / 2));
	top = 0;

	if (winHeight > height) {
		top = Math.round((winHeight / 2) - (height / 2));
	}

	window.open("https://twitter.com/share?text="+dataText+"&url=http://www.explorainmuebles.com/"+urlObject, 'intent', windowOptions + ',width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
}


/*
	Reinicia eventos a elementos favoritos
*/
function template_eventosFavoritos() {
	$(".btnBotones.estrella").unbind();
	$(".btnBotones.estrella").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				var tempUrl = "";
				var datos;
				
				if (typeof elemento.attr("data-inmueble") !== 'undefined') {
					tempUrl = "lib_php/updFavoritoInmueble.php";
					datos = {
						id: elemento.attr("data-id"),
						inmueble: elemento.attr("data-inmueble")
					};
				}
				else {
					tempUrl = "lib_php/updFavoritoDesarrollo.php";
					datos = {
						id: elemento.attr("data-id"),
						desarrollo: elemento.attr("data-desarrollo")
					};
				}
				
				
				$.ajax({
					url: tempUrl,
					type: "POST",
					dataType: "json",
					data: datos
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						if (parseInt(respuesta_json.id) == 0)
							elemento.removeClass("activo");
						else
							elemento.addClass("activo");
						elemento.attr("data-id", respuesta_json.id);
					}
					else {
						$("#template_alertPersonalizado td").text("Inicia sesión o regístrate para agregar tus inmuebles/desarrollos favoritos.");
						template_alertPersonalizado();
					}
				});
			}
		});
	});
}
/**/