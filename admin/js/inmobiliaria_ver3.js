// JavaScript Document
var urlArchivos = "../images/images/";
var inmobiliaria_positions = Array();
var inmobiliaria_pos_comp = -1;

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 25;
		var textNombreEmpresa = (positions[pos][1]).length > maxCadena ? ((positions[pos][1]).substr(0, (maxCadena - 3))+"...") : positions[pos][1];
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textNombreEmpresa+"</a></td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(1).width()+"' style='text-align:center;'>"+positions[pos][2]+"</td>"+
					"<td width='"+$("#main table.main_table tr").eq(2).find("td").eq(2).width()+"' style='text-align:center;'><a href='javascript:inmobiliaria_abrirModificarUsuarios("+pos+");'>Usuarios</a></td>"+
					"<td width='15'>"+
						(isBorrarTuplas ? ("<img src='images/btnCerrar.png' width='12' style='cursor:pointer; position:relative;' onclick='bool_borrar = true; abrirModificarCampos("+pos+");' />") : "")+
					"</td>"+
				"</tr>"+
			"</table>"; 
		
		$("#contenedorConsulta").append(divImagen);
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
	$("#validez").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		selectMonths: true,
		selectYears: 30
	});
	
	
	consultarTuplasExistentes("updPositionsInmobiliaria.php", true);
	
	$("#template_buscador").on({
		keyup: function(ev) {
			var unicode = ev.keyCode;
	
			if (unicode == 13) {
				consultarTuplasExistentes("updPositionsInmobiliaria.php", true, {palabra: $("#template_buscador").val()});
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
			$("#nombreEmpresa").val(positions[posit][1]);
			$("#rfc").val(positions[posit][2]);
			if (positions[posit][3] != "") {
				$("#imagenLogoTipo").prop("href", urlArchivos+positions[posit][3]);
				$("#imagenLogoTipo").show();
			}
			inmobiliaria_campoUsuario(positions[posit][4]);
			$("#usuario").val(positions[posit][4]);
			partes = (positions[posit][5]).split("/");
			_fecha = new Date(parseInt(partes[2]), parseInt(partes[1])-1, parseInt(partes[0]));
			$("#validez").pickadate().pickadate("picker").set("select", [_fecha.getFullYear(), _fecha.getMonth(), _fecha.getDate()]);
			$("#creditos").val(positions[posit][6]);
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
		if (confirm('\u00BFEsta seguro de eliminar la inmobiliaria: "'+positions[posit][1]+'"?')) {
			datos = {
				id: positions[posit][0],
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
				id: positions[posit][0]
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
		$("#usuario option[data-inmobiliaria='"+positions[pos_comp][0]+"']").show();
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
							id = positions[pos_comp][0];
							
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
		id = positions[pos_comp][0];
		
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
			id: positions[pos_comp][0],
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