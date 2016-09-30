// JavaScript Document


/*
	Funciones que se inicializan al cargar el html en todas las interfaces
*/
$(document).ready(function(){
	template_inicializarBotones();
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
		
		$(this).on({
			click: function() {
				var isOpen = false;
				
				if (elemento.find("li.lista").css("display") != "none")
					isOpen = true;
					
				if (isOpen)
					elemento.find("li.lista").hide();
				else
					elemento.find("li.lista").show();
			}
		});
		
		
		$(this).find("li.lista li").on({
			click: function() {
				elemento.find("p").attr("data-value", $(this).attr("data-value"));
				elemento.find("p").text($(this).text());
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
	Actualiza las ciudades para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
*/
function template_actualizar_ciudad(nomElemento) {
	var indexEstado = -1;
	var arrayEstados = Array("template_venta_estado", "template_renta_estado", "template_rentaVac_estado");
	var arrayCiudades = Array("template_venta_municipio", "template_renta_municipio", "template_rentaVac_municipio");
	var arrayColonias = Array("template_venta_colonia", "template_renta_colonia", "template_rentaVac_colonia");
	
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
			}
		});
	}
}


/*
	Actualiza las colonias para el select enviado por parametro
	
		* nomElemento:	String, es el nombre del elemento
*/
function template_actualizar_colonia(nomElemento) {
	var indexMunicipio = -1;
	var arrayCiudades = Array("template_venta_municipio", "template_renta_municipio", "template_rentaVac_municipio");
	var arrayColonias = Array("template_venta_colonia", "template_renta_colonia", "template_rentaVac_colonia");
	
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
	
	indexTransaccion = parseInt(transaccion) - 1;
	
	var datos = {
		transaccion: transaccion,
		tipoInmueble: $("#"+arrayTipoInmueble[indexTransaccion]).find("p").attr("data-value"),
		estado: $("#"+arrayEstados[indexTransaccion]).find("p").attr("data-value"),
		ciudad: $("#"+arrayCiudades[indexTransaccion]).find("p").attr("data-value"),
		colonia: $("#"+arrayColonias[indexTransaccion]).find("p").attr("data-value"),
		codigo: $("#"+arrayCodigos[indexTransaccion]).val()
	};
	
	gotoURLPOST("catalogo.php", datos);
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
											$("#template_login_email").val($("#template_count_email").val());
											$("#template_login_pass").val($("#template_count_password").val());
											
											template_validaCampos_login();
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
/**/