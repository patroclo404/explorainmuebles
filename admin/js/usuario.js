// JavaScript Document

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 30;
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
	
	$("#template_buscador").attr("placeholder", "Buscar por nombre o email");
	
	$("#template_buscador").on({
		keyup: function(ev) {
			var unicode = ev.keyCode;
	
			if (unicode == 13) {
				consultarTuplasExistentes("updPositionsUsuario.php", true, {palabra: $("#template_buscador").val()});
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
		$("#celdaPassword").hide();
		$("#celdaConfPassword").hide();
		
		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Usuario");
			$("#nombre").val(positions[posit][1]);
			$("#email").val(positions[posit][2]);
			$("#FBid").val(positions[posit][3]);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Usuario");
			$("#nombre").val("");
			$("#email").val("");
			$("#password").val("");
			$("#confPassword").val("");
			$("#FBid").val("");
			
			$("#celdaPassword").show();
			$("#celdaConfPassword").show();
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


	$.ajax({
		url: "lib_php/updUsuario.php",
		type: "POST",
		dataType: "json",
		data: {
			id: id,
			nombre: $("#nombre").val(),
			email: $("#email").val(),
			password: md5Script($("#password").val()),
			FBid: $("#FBid").val()
		}
	}).always(function(respuesta_json){
		$("#btnGuardar").show();
		$("#mensajeTemporal").hide();
		$("#backImage").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(usuario_cerrarPopUp);
		consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
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
		nombre: "",
		email: "",
		password: md5Script($("#password2").val()),
		FBid: ""
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