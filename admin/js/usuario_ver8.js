// JavaScript Document
var urlArchivos = "../images/images/";

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 25;
		var textNombre = (positions[pos][1]).length > maxCadena ? ((positions[pos][1]).substr(0, (maxCadena - 3))+"...") : positions[pos][1];
		var textEmail = (positions[pos][2]).length > maxCadena ? ((positions[pos][2]).substr(0, (maxCadena - 3))+"...") : positions[pos][2];
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textNombre+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(1).width()+"' style='text-align:center;'>"+textEmail+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(2).width()+"' style='text-align:center;'><a href='javascript:usuario_abrirModificarPassword("+pos+");'>Contraseña</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function usuario_inicializarBotones() {
	consultarTuplasExistentes("updPositionsUsuario.php", true);
	
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
				consultarTuplasExistentes("updPositionsUsuario.php", true, {palabra: $("#template_buscador").val()});
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
			$("#nombre").val(positions[posit][1]);
			$("#email").val(positions[posit][2]);
			$("#FBid").val(positions[posit][3]);
			$("input[name='sexo'][value='"+positions[posit][4]+"']").prop("checked", true);
			$("#fechaNac").val(positions[posit][5]);
			$("#telefono1").val(positions[posit][6]);
			$("#telefono2").val(positions[posit][7]);
			$("#calleNumero").val(positions[posit][8]);
			$("#notificaciones").prop("checked", (positions[posit][15] == 1 ? true : false));
			
			usuario_onChange_estado_ciudad_colonia(positions[posit][9], positions[posit][10], positions[posit][11]);
			
			if (positions[posit][14] != "") {
				$("#imagenActual").prop("href", urlArchivos+positions[posit][14]);
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
		if (confirm('\u00BFEsta seguro de eliminar al usuario: "'+positions[posit][1]+'"?')) {
			datos = {
				id: positions[posit][0],
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
					id = positions[pos_comp][0];
					
				
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
		id = positions[pos_comp][0];
		
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
		id: positions[pos_comp][0],
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