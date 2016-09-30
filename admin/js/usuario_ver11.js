// JavaScript Document
var urlArchivos = "../images/images/";
var usuario_positions = Array();
var usuario_pos_comp = -1;


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
		mostrarCamposExistentes(respuesta_json.contadores);//definir en cada js de la interfaz
	});
}

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
	
		* arrayContadores:	Array Object, contiene los contadores
*/
function mostrarCamposExistentes(arrayContadores) {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 25;
		var textNombre = (positions[pos].nombre).length > maxCadena ? ((positions[pos].nombre).substr(0, (maxCadena - 3))+"...") : positions[pos].nombre;
		var textEmail = (positions[pos].email).length > maxCadena ? ((positions[pos].email).substr(0, (maxCadena - 3))+"...") : positions[pos].email;
		var textInmobiliaria = (positions[pos].inmobiliariaNombre).length > maxCadena ? ((positions[pos].inmobiliariaNombre).substr(0, (maxCadena - 3))+"...") : positions[pos].inmobiliariaNombre;
		maxCadena = 35;
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textNombre+"</a></td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(1).width()+"' style='text-align:center;'>"+textEmail+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(2).width()+"' style='text-align:center;'><a href='javascript:usuario_abrirModificarPassword("+pos+");'>Contraseña</a></td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(3).width()+"' style='text-align:center;'>"+positions[pos].id+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(4).width()+"' style='text-align:center;'>"+(parseInt(positions[pos].contAnuncios) > 0 ? ("<a href='javascript:usuario_abrirModificarInmuebles("+pos+");'>"+positions[pos].contAnuncios+"</a>") : positions[pos].contAnuncios)+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(5).width()+"' style='text-align:center;'>"+positions[pos].create+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(6).width()+"' style='text-align:center;'>"+(textInmobiliaria != "" ? ("<a href='inmobiliaria.php?idInmobiliaria="+positions[pos].inmobiliaria+"'>"+textInmobiliaria+"</a>") : "")+"</td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
	
	if (positions.length > 0) {
		$("#_numUsuarios").text(arrayContadores.total);
		$("#_numPendientes").text(arrayContadores.pendientes);
	}
}


/*
	Muestra los inmuebles del usuario en el div "contenedorUsuarioInmuebles"
	
		* posit:		Integer, es el id del usuario
		* datos_json:	Array JSON, es un arreglo de datos decodificado con JSON
*/
function usuario_inmuebleUsuarioCampos(posit, datos_json) {
	var contenedorDatos = $("#contenedorUsuarioInmuebles");
	contenedorDatos.html("");
	
	usuario_positions = Array();
	usuario_pos_comp = -1;
			
	for (var x = 0; x < datos_json.length; x++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 50;
		var textTitulo = (datos_json[x].campo2).length > maxCadena ? ((datos_json[x].campo2).substr(0, (maxCadena - 3))+"...") : datos_json[x].campo2;
		
		
		divImagen.innerHTML =
			"<table>"+
				"<tr>"+
					"<td style='text-align:left;'><a href='../inmueble.php?id="+datos_json[x].campo1+"' target='_blank'>"+textTitulo+"</a></td>"+
				"</tr>"+
			"</table>";
			
		contenedorDatos.append(divImagen);
		usuario_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2));
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function usuario_inicializarBotones() {
	$("#template_celdaBuscador").find("td").eq(0).append("<p style='padding-top:15px; padding-bottom:15px;'><a href='usuario.php' style='margin-right:100px;'>Total de usuarios: <span id='_numUsuarios'>0</span></a><a href='usuario.php?pendientes=1'>Pendientes por validar su correo: <span id='_numPendientes'>0</span></a></p>");
	
	
	arrayCamposConsulta = template_getURL_keyValue();
	arrayCamposConsulta["palabra"] = $("#template_buscador").val();
	
	
	consultarTuplasExistentes("updPositionsUsuario.php", true, arrayCamposConsulta);
	
	$("#fechaNac").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		max: true,
		selectMonths: true,
		selectYears: 30
	});
	
	$("#template_buscador").attr("placeholder", "Buscar por nombre o email");
	
	$("#template_buscador").on({
		keyup: function(ev) {
			var unicode = ev.keyCode;
	
			if (unicode == 13) {
				arrayCamposConsulta = template_getURL_keyValue();
				arrayCamposConsulta["palabra"] = $("#template_buscador").val();
	
				consultarTuplasExistentes("updPositionsUsuario.php", true, arrayCamposConsulta);
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
}


/*
	Cierra todos los poups de la interfaz actual
*/
function usuario_cerrarPopUp(){
	$("#backImage").hide();
	$("#usuario_abrirModificarPassword").hide();
	$("#usuario_abrirModificarInmuebles").hide();
}


/*
	Muestra el primer poup que es el que tiene los datos a modificar o crear una nueva tupla
*/
function abrirModificarCampos(posit){
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		var objDiv = $("#backImage");
		objDiv.find("a").hide();
		objDiv.find("input[type='file']").val("");
		$("#celdaPassword").hide();
		$("#celdaConfPassword").hide();
		
		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Usuario");
			$("#nombre").val(positions[posit].nombre);
			$("#email").val(positions[posit].email);
			$("#FBid").val(positions[posit].FBId);
			$("input[name='sexo'][value='"+positions[posit].sexo+"']").prop("checked", true);
			$("#fechaNac").val(positions[posit].fechaNac);
			$("#telefono1").val(positions[posit].telefono1);
			$("#telefono2").val(positions[posit].telefono2);
			$("#calleNumero").val(positions[posit].calleNumero);
			$("#notificaciones").prop("checked", (positions[posit].notificaciones == 1 ? true : false));
			
			usuario_onChange_estado_ciudad_colonia(positions[posit].estado, positions[posit].ciudad, positions[posit].colonia);
			
			if (positions[posit].imagen != "") {
				$("#imagenActual").prop("href", urlArchivos+positions[posit].imagen);
				$("#imagenActual").show();
			}
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Usuario");
			$("#nombre").val("");
			$("#email").val("");
			$("#password").val("");
			$("#confPassword").val("");
			$("#FBid").val("");
			$("input[name='sexo'][value='H']").prop("checked", true);
			$("#fechaNac").val("");
			$("#telefono1").val("");
			$("#telefono2").val("");
			$("#calleNumero").val("");
			$("#estado").val(-1);
			$("#ciudad").val(-1);
			$("#colonia").val(-1);
			$("#notificaciones").prop("checked", false);
			
			$("#celdaPassword").show();
			$("#celdaConfPassword").show();
			
			$("#estado").change();
		}


		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;

		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": "20px",
			"position": "absolute"
		});
		
		$("body,html").animate({
			scrollTop: "0px"
		}, 500);
	}
	else{
		bool_borrar=false;
		if (confirm('\u00BFEsta seguro de eliminar al usuario: "'+positions[posit].nombre+'"?')) {
			datos = {
				id: positions[posit].id,
				borrar: 1
			};
			
			$.ajax({
				url: "lib_php/updUsuario.php",
				type: "POST",
				dataType: "json",
				data: datos
			}).always(function(respuesta_json){
				$("#resultados").text(respuesta_json.mensaje);
				
				if (respuesta_json.isExito == 1) {
					principalCerrarPopUp(usuario_cerrarPopUp);
					consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}


/*
	Muestra un popup que permite modificar los passwords
	
		* posit:	Integer, es el id del usuario
*/
function usuario_abrirModificarPassword(posit) {
	$("#mascaraPrincipal").show();
	
	var objDiv = $("#usuario_abrirModificarPassword");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	pos_comp = posit;
	
	$("#password2").val("");
	$("#confPassword2").val("");
}


/*
	Muestra un popup con las inmuebles del usuario
	
		* posit:	Integer, es el id del usuario
*/
function usuario_abrirModificarInmuebles(posit) {
	$("#mascaraPrincipal").show();
	
	var objDiv = $("#usuario_abrirModificarInmuebles");

	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($(window).height() - objDiv.height())/2;

	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
	
	pos_comp = posit;
	
	$.ajax({
		url: "lib_php/updUsuarioInmueble.php",
		type: "POST",
		dataType: "json",
		data:{
			id: positions[posit].id
		}
	}).always(function(respuesta_json){
		usuario_inmuebleUsuarioCampos(posit, respuesta_json.datos);
	});
}


/*
	Actualiza los campos de: estado, ciudad, colonia
	
		* estado:	[integer], es el id del estado
		* ciudad:	[integer], es el id de la ciudad
		* colonia:	[integer], es el id de la colonia
*/
function usuario_onChange_estado_ciudad_colonia(estado, ciudad, colonia) {
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
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	if (!vacio($("#nombre").val(), $("#nombre").attr("placeholder"))) {
		if (!vacio($("#email").val(), $("#email").attr("placeholder"))) {
			if (correoValido($("#email").val())) {
				var continua = true;
				var id = pos_comp;
				
				if (pos_comp == -1) {
					continua = false;
					
					if (!vacio($("#password").val(), $("#password").attr("placeholder"))) {
						if (!vacio($("#confPassword").val(), $("#confPassword").attr("placeholder"))) {
							if ($("#password").val() == $("#confPassword").val())
								continua = true;
							else
								alert("Las contraseñas son distintas. Vuelva a intentarlo.");
						}
					}
				}
				else
					id = positions[pos_comp].id;
					
				
				if (!isVacio($("#FBid").val())) {
					continua = false;
					
					if (entero($("#FBid").val(), $("#FBid").attr("placeholder"))) {
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
							email: $("#email").val()
						}
					}).always(function(respuesta_json) {
						if (respuesta_json.isExito == 1) {
							if (!isVacio($("#FBid").val())) {
								$.ajax({
									url: "lib_php/updUsuario.php",
									type: "POST",
									dataType: "json",
									data: {
										id: id,
										validarFBid: 1,
										FBid: $("#FBid").val()
									}
								}).always(function(respuesta_json2){
									if (respuesta_json2.isExito == 1) {
										save();
									}
									else
										alert(respuesta_json2.mensaje);
								});
							}
							else
								save();
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
	Valida los campos para la nueva contraseña del usuario
*/
function validarCampos2() {
	if (!vacio($("#password2").val(), $("#password2").attr("placeholder"))) {
		if (!vacio($("#confPassword2").val(), $("#confPassword2").attr("placeholder"))) {
			if ($("#password2").val() == $("#confPassword2").val())
				savePassword();
			else
				alert("Las contraseñas son distintas. Vuelva a intentarlo.");
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
	
	
	$("#idUsuario").val(id);
	$("#password").val(md5Script($("#password").val()));
	$("#cp").val(($("#colonia").val() != -1 ? $("#colonia option:selected").attr("data-cp") : ""));
	
	
	$("#subirUsuario").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar").show();
			$("#mensajeTemporal").hide();
			$("#backImage").css({cursor:"default"});
			
			$("#resultados").text(respuesta_json.mensaje);
			principalCerrarPopUp(usuario_cerrarPopUp);
			consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
		}
	});
}


/*
	Guarda el nuevo password y vuelve a refrescar la consulta de los usuario
*/
function savePassword() {
	$("#btnGuardar2").hide();
	$("#mensajeTemporal2").show();
	$("#usuario_abrirModificarPassword").css({cursor:"wait"});
	
	datos = {
		id: positions[pos_comp].id,
		chgPassword: 1,
		password: md5Script($("#password2").val())
	};
	
	$.ajax({
		url: "lib_php/updUsuario.php",
		type: "POST",
		dataType: "json",
		data: datos
	}).always(function(respuesta_json){
		$("#btnGuardar2").show();
		$("#mensajeTemporal2").hide();
		$("#usuario_abrirModificarPassword").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(usuario_cerrarPopUp);
	});
}
/**/