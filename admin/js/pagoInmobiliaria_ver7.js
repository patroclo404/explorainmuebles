// JavaScript Document
var minimoConekta = 30;

	
/*
	Muestra los campos existentes en el div "contenedorConsulta"
*/
function mostrarCamposExistentes() {
	$("#contenedorConsulta").html("");
	
	for (var pos = 0; pos < positions.length; pos++) {
		divImagen = document.createElement("div");
		divImagen.className = "thumbImg";
		
		var maxCadena = 30;
		var textInmobiliaria = $("#inmobiliaria option[value='"+positions[pos][8]+"']").text();
		textInmobiliaria = textInmobiliaria.length > maxCadena ? (textInmobiliaria.substr(0, (maxCadena - 3))+"...") : textInmobiliaria;
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='javascript:abrirModificarCampos("+pos+");'>"+textInmobiliaria+"</a></td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(1).width()+"' style='text-align:center;'>$"+positions[pos][2]+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(2).width()+"' style='text-align:center;'>"+positions[pos][6]+" - "+positions[pos][7]+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(3).width()+"' style='text-align:center;'>"+$("#tipo option[value='"+positions[pos][5]+"']").text()+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(4).width()+"' style='text-align:center;'>"+(positions[pos][4] == 1 ? "Si" : "No")+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(5).width()+"' style='text-align:center;'>"+positions[pos][1]+"</td>"+
					"<td width='"+$("#template_nombreCampos").find("td").eq(6).width()+"' style='text-align:center;'>"+positions[pos][3]+"</td>"+
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
function pagoInmobiliaria_inicializarBotones() {
	consultarTuplasExistentes("updPositionsPagoInmobiliaria.php", false);
	
	$("#validez").pickadate({
		clear: "",
		format: "dd/mm/yyyy",
		selectMonths: true,
		selectYears: 30
	});
}


/*
	Cierra todos los poups de la interfaz actual
*/
function pagoInmobiliaria_cerrarPopUp(){
	$("#backImage").hide();
}


/*
	Muestra el primer poup que es el que tiene los datos a modificar o crear una nueva tupla
*/
function abrirModificarCampos(posit){
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		var objDiv = $("#backImage");

		lPos = ($(window).width() - objDiv.width())/2;
		tPos = ($(window).height() - objDiv.height())/2;

		objDiv.css({
			"display": "block",
			"left": lPos+"px",
			"top": tPos+"px"
		});
		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Pago de Inmobiliaria");
			$("#creditos").val(positions[pos_comp][1]);
			$("#total").val(positions[pos_comp][2]);
			$("#tipo").val(positions[pos_comp][5]);
			$("#inmobiliaria").val(positions[pos_comp][8]);
			$("#isPagado").prop("checked", (positions[pos_comp][4] == 1 ? true : false));
			if (positions[pos_comp][4] == 1) {
				$("#isPagado").prop("disabled", true);
			}
			$("#notificar").prop("checked", true);
			
			_fecha = new Date();
			_fecha.setFullYear(parseInt(positions[pos_comp][3].split("/")[2]), parseInt(positions[pos_comp][3].split("/")[1]) - 1, parseInt(positions[pos_comp][3].split("/")[0]));
			$("#validez").pickadate().pickadate("picker").set("select", [_fecha.getFullYear(), _fecha.getMonth(), _fecha.getDate()]);
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nuevo Pago de Inmobiliaria");
			objDiv.find("input[type='text']").val("");
			objDiv.find("select").val(-1);
			$("#isPagado").prop("checked", false);
			$("#isPagado").prop("disabled", false);
			$("#notificar").prop("checked", true);
			
			_fecha = new Date();
			$("#validez").pickadate().pickadate("picker").set("select", [_fecha.getFullYear(), _fecha.getMonth(), _fecha.getDate()]);
		}
	}
	else{
		bool_borrar=false;
	}
}
	
	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos() {
	if (!vacio($("#creditos").val(), $("#creditos").attr("placeholder"))) {
		if (entero($("#creditos").val(), $("#creditos").attr("placeholder"))) {
			if (!vacio($("#total").val(), $("#total").attr("placeholder"))) {
				if (!vacio($("#validez").val(), $("#validez").attr("placeholder"))) {
					if (!vacio(($("#tipo").val() != -1 ? $("#tipo").val() : ""), $("#tipo option[value='-1']").text())) {
						if (!vacio(($("#inmobiliaria").val() != -1 ? $("#inmobiliaria").val() : ""), $("#inmobiliaria option[value='-1']").text())) {
							if (flotante($("#total").val(), $("#total").attr("placeholder"))) {
								if ($("#tipo").val() == 1) {
									if (parseFloat($("#total").val()) < minimoConekta) {
										alert("EL precio mínimo es: $"+minimoConekta);
										return false;
									}
								}
								
								save();
							}
						}
					}
				}
			}
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
		url: "lib_php/updPagoInmobiliaria.php",
		type: "POST",
		dataType: "json",
		data: {
			id: id,
			creditos: $("#creditos").val(),
			total: $("#total").val(),
			validez: $("#validez").val(),
			tipo: $("#tipo").val(),
			inmobiliaria: $("#inmobiliaria").val(),
			isPagado: $("#isPagado").prop("checked") ? 1 : 0,
			notificar: $("#notificar").prop("checked") ? 1 : 0
		}
	}).always(function(respuesta_json){
		$("#btnGuardar").show();
		$("#mensajeTemporal").hide();
		$("#backImage").css({cursor:"default"});
		
		$("#resultados").text(respuesta_json.mensaje);
		principalCerrarPopUp(pagoInmobiliaria_cerrarPopUp);
		consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
	});
}
/**/