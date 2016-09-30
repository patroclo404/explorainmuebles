// JavaScript Document
var urlArchivos = "../images/images/";
var inmobiliaria_positions = Array();
var inmobiliaria_pos_comp = -1;


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
*/
function mostrarCamposExistentes(otrosDatos) {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 25;
		var textNombreEmpresa = (positions[pos].nombreEmpresa).length > maxCadena ? ((positions[pos].nombreEmpresa).substr(0, (maxCadena - 3))+"...") : positions[pos].nombreEmpresa;
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textNombreEmpresa+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(1).width()+"' style='text-align:center;'><a href='javascript:inmobiliaria_abrirModificarUsuarios("+pos+");'>Usuarios</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(2).width()+"' style='text-align:center;'>"+positions[pos].id+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(3).width()+"' style='text-align:center;'>"+positions[pos].validez+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(4).width()+"' style='text-align:center;'>"+(parseInt(positions[pos].contPublicados) > 0 ? ("<a href='inmueble.php?publicados=1&idInmobiliaria="+positions[pos].id+"'>"+positions[pos].contPublicados+"</a>") : positions[pos].contPublicados)+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(5).width()+"' style='text-align:center;'>"+(parseInt(positions[pos].contGuardados) > 0 ? ("<a href='inmueble.php?idInmobiliaria="+positions[pos].id+"'>"+positions[pos].contGuardados+"</a>") : positions[pos].contGuardados)+"</td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
	}
	
	if (positions.length > 0) {
		$("#_numInmobiliarias").text(otrosDatos.total);
		$("#_numVencidos").text(otrosDatos.vencidos);
	}
}


/*
	Muestra los usuarios de la inmobiliaria en el div "contenedorInmobiliariaUsuarios"
	
		* posit:		Integer, es el id de la inmobiliaria
		* datos_json:	Array JSON, es un arreglo de datos decodificado con JSON
*/
function inmobiliaria_usuarioInmobiliariaCampos(posit, datos_json) {
	var contenedorDatos = $("#contenedorInmobiliariaUsuarios");
	contenedorDatos.html("");
	
	inmobiliaria_positions = Array();
	inmobiliaria_pos_comp = -1;
			
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
		inmobiliaria_positions.push(Array(datos_json[x].campo1, datos_json[x].campo2, datos_json[x].campo3));
	}
}


/*
	inicializa los campos y les da los efectos de objFocus y objBlur
*/
function inmobiliaria_inicializarBotones() {
	$("#template_celdaBuscador").find("td").eq(0).append("<p style='padding-top:15px; padding-bottom:15px;'><a href='inmobiliaria.php' style='margin-right:100px;'>Total de inmobiliarias: <span id='_numInmobiliarias'>0</span></a><a href='inmobiliaria.php?vencidos=1'>Inmobiliarias sin plan activo: <span id='_numVencidos'>0</span></a></p>");
	
	
	$("#validez").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		selectMonths: true,
		selectYears: 30
	});
	
	
	arrayCamposConsulta = template_getURL_keyValue();
	arrayCamposConsulta["palabra"] = $("#template_buscador").val();
	
	
	consultarTuplasExistentes("updPositionsInmobiliaria.php", true, arrayCamposConsulta);
	$("#template_buscador").attr("placeholder", "Buscar por nombre");
	
	$("#template_buscador").on({
		keyup: function(ev) {
			var unicode = ev.keyCode;
	
			if (unicode == 13) {
				arrayCamposConsulta = template_getURL_keyValue();
				arrayCamposConsulta["palabra"] = $("#template_buscador").val();
				
				consultarTuplasExistentes("updPositionsInmobiliaria.php", true, arrayCamposConsulta);
			}
		}
	});
}


/*
	Cierra todos los poups de la interfaz actual
*/
function inmobiliaria_cerrarPopUp(){
	$("#backImage").hide();
	$("#inmobiliaria_abrirModificarUsuarios").hide();
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
		
		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Inmobiliaria");
			$("#nombreEmpresa").val(positions[posit].nombreEmpresa);
			$("#rfc").val(positions[posit].rfc);
			if (positions[posit].logotipo != "") {
				$("#imagenLogoTipo").prop("href", urlArchivos+positions[posit].logotipo);
				$("#imagenLogoTipo").show();
			}
			inmobiliaria_campoUsuario(positions[posit].usuario);
			$("#usuario").val(positions[posit].usuario);
			partes = (positions[posit].validez).split("/");
			_fecha = new Date(parseInt(partes[2]), parseInt(partes[1])-1, parseInt(partes[0]));
			$("#validez").pickadate().pickadate("picker").set("select", [_fecha.getFullYear(), _fecha.getMonth(), _fecha.getDate()]);
			$("#creditos").val(positions[posit].creditos);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nueva Inmobiliaria");
			$("#nombreEmpresa").val("");
			$("#rfc").val("");
			inmobiliaria_campoUsuario(-1);
			$("#usuario").val(-1);
			$("#validez").val("");
			$("#creditos").val("");
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
		if (confirm('\u00BFEsta seguro de eliminar la inmobiliaria: "'+positions[posit].nombreEmpresa+'"?')) {
			datos = {
				id: positions[posit].id,
				borrar: 1
			};
			
			$.ajax({
				url: "lib_php/updInmobiliaria.php",
				type: "POST",
				dataType: "json",
				data: datos
			}).always(function(respuesta_json){
				$("#resultados").text(respuesta_json.mensaje);
				
				if (respuesta_json.isExito == 1) {
					gotoURL("inmobiliaria.php");
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}


/*
	Muestra un popup con los usuarios de la inmobiliaria
	
		* posit:		Integer, es el id del inmueble
*/
function inmobiliaria_abrirModificarUsuarios(posit) {
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		
		var objDiv = $("#inmobiliaria_abrirModificarUsuarios");
	
		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;
	
		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		pos_comp = posit;
		
		$.ajax({
			url: "lib_php/updInmobiliariaUsuario.php",
			type: "POST",
			dataType: "json",
			data:{
				id: positions[posit].id
			}
		}).always(function(respuesta_json){
			inmobiliaria_usuarioInmobiliariaCampos(posit, respuesta_json.datos);
		});
	}
}


/*
	Muestra u oculta los usuarios que pertenecen a una inmobiliaria
	
		idUsuario:	Integer, es el id del usuario que pertenece a una inmobiliara
*/
function inmobiliaria_campoUsuario(idUsuario) {
	$("#usuario option[data-inmobiliaria]").hide();//oculta todos los usuarios que pertenecen a una inmobiliaria
	if (parseInt(idUsuario) != -1) {
		$("#usuario option[data-inmobiliaria='"+positions[pos_comp].id+"']").show();
	}
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	if (!vacio($("#nombreEmpresa").val(), $("#nombreEmpresa").attr("placeholder"))) {
		if (!vacio(($("#usuario").val() != -1 ? $("#usuario").val() : ""), $("#usuario option[value='-1']").text())) {
			if (!vacio($("#validez").val(), $("#validez").attr("placeholder"))) {
				if (!vacio($("#creditos").val(), $("#creditos").attr("placeholder"))) {
					if (entero($("#creditos").val(), $("#creditos").attr("placeholder"))) {
						var id = pos_comp;
						
						if (pos_comp != -1)
							id = positions[pos_comp].id;
							
						$.ajax({
							url: "lib_php/updInmobiliaria.php",
							type: "POST",
							dataType: "json",
							data: {
								id: id,
								validarNombreEmpresa: 1,
								nombreEmpresa: $("#nombreEmpresa").val()
							}
						}).always(function(respuesta_json) {
							if (respuesta_json.isExito == 1) {
								if (!isVacio($("#rfc").val())) {
									$.ajax({
										url: "lib_php/updInmobiliaria.php",
										type: "POST",
										dataType: "json",
										data: {
											id: id,
											validarRFC: 1,
											rfc: $("#rfc").val()
										}
									}).always(function(respuesta_json2){
										if (respuesta_json2.isExito == 1)
											save();
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
}


/*
	Valida los campos para agregar o modificar los usuarios que pertenecen a la inmobiliaria
*/
function validarCampos2() {
	saveUsuarios();
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
	
	
	$("#idInmobiliaria").val(id);
	
	
	$("#subirInmobiliaria").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar").show();
			$("#mensajeTemporal").hide();
			$("#backImage").css({cursor:"default"});
			
			gotoURL("inmobiliaria.php");
		}
	});
}


/*
	Guarda los usuarios y vuelve a refrescar la consulta
*/
function saveUsuarios() {
	$("#btnGuardar2").hide();
	$("#mensajeTemporal2").show();
	$("#inmobiliaria_abrirModificarUsuarios").css({cursor:"wait"});
	
	
	var arrayUsuarios = Array();
	
	$("#contenedorInmobiliariaUsuarios input[type='checkbox']:checked").each(function(){
		arrayUsuarios.push($(this).val());
	});
	
	
	$.ajax({
		url: "lib_php/updInmobiliariaUsuario.php",
		type: "POST",
		dataType: "json",
		data: {
			id: positions[pos_comp].id,
			modificar: 1,
			usuarios: arrayUsuarios.toString()
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1) {
			$("#btnGuardar2").show();
			$("#mensajeTemporal2").hide();
			$("#inmobiliaria_abrirModificarUsuarios").css({cursor:"default"});
			
			gotoURL("inmobiliaria.php");
		}
	});
}
/**/