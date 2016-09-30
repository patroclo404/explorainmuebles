// JavaScript Document
var pos_comp = -1;
var positions = new Array();
var bool_borrar = false;
var nombrePHPConsultar = "";
var isBorrarTuplas = false;
var arrayCamposConsulta = null;


/*
	Funciones que se inicializan en todas las interfaces despues de cargar en jquery y la pagina por completo
*/
$(document).ready(function(){
});


/*
	Muestra/Oculta el menu despleagable que tienen todos los usuarios en la parte superior derecha
*/
function generalMostrarOcultar_contenedorLogueadoDesplegable() {
	var obj = document.getElementById("contenedorLogueadoDesplegable");
	if (obj.style.visibility == "hidden")
		obj.style.visibility = "visible";
	else
		obj.style.visibility = "hidden";
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
	Cierra todos los popups en general. Tambien ejecuta una funcion si esta se recibe por parametro.
	Esto es para personalizar el cerrado de popups
	
		* fcn:	String, nombre de la funcion a realizar despues de completar el cerrado de la pricipal
*/
function principalCerrarPopUp(fcn) {
	$("#mascaraPrincipal").hide();
	$("#template_abrirModificarPassword").hide();
	if (fcn != null) {
		fcn();
	}
}


/*
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
	}).always(function(json_datos){
		positions = new Array();
		
		if (json_datos.length > 0) {
			camposJson = new Array();
			
			for(var prop in json_datos[0]) {
				camposJson.push(prop);
			}
			
			for (var x = 0; x < json_datos.length; x++) {
				campoTemp = Array();
				
				for (var y = 0; y < camposJson.length; y++) {
					nombCampo = camposJson[y];
					campoTemp.push(json_datos[x][nombCampo]);
				}
				
				positions.push(campoTemp);
			}
		}
		
		mostrarCamposExistentes();//definir en cada js de la interfaz*/
	});
}


/*
	Muestra un popup que permite modificar los passwords
	
		* posit:	Integer, es el id del administrador
*/
function template_abrirModificarPassword(posit) {
	$("#mascaraPrincipal").show();
	
	var objDiv = $("#template_abrirModificarPassword");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	pos_comp = posit;
	
	$("#template_oldPassword").val("");
	$("#template_newPassword").val("");
	$("#template_confPassword").val("");
}


/*
	Valida los campos para la nueva contraseña del administrador
*/
function template_validarCampos() {
	if (!vacio($("#template_oldPassword").val(), $("#template_oldPassword").attr("placeholder"))) {
		if (!vacio($("#template_newPassword").val(), $("#template_newPassword").attr("placeholder"))) {
			if (!vacio($("#template_confPassword").val(), $("#template_confPassword").attr("placeholder"))) {
				if ($("#template_newPassword").val() == $("#template_confPassword").val()) {
					template_savePassword();
				}
				else
					alert("Las contraseñas son distintas. Vuelva a intentarlo.");
			}
		}
	}
}


/*
	Guarda el nuevo password del administrador
*/
function template_savePassword() {
	$("#template_btnGuardar").hide();
	$("#template_mensajeTemporal").show();
	$("#template_abrirModificarPassword").css({cursor:"wait"});
	
	var datos = {
		id: pos_comp,
		chgPassword: 1,
		validarOldPass: 1,
		oldPass: md5Script($("#template_oldPassword").val()),
		password: md5Script($("#template_newPassword").val()),
		nombre: "",
		email: ""
	}
	
	$.ajax({
		url: "lib_php/updAdministrador.php",
		type: "POST",
		dataType: "json",
		data: datos
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			alert(respuesta_json.mensaje);
			principalCerrarPopUp();
		}
		else
			alert(respuesta_json.mensaje);
		
		$("#template_btnGuardar").show();
		$("#template_mensajeTemporal").hide();
		$("#template_abrirModificarPassword").css({cursor:"default"});
	});
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