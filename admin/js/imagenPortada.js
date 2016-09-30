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
		
		
		divImagen.innerHTML = 
			"<table>"+
				"<tr>"+
					"<td><a href='"+urlArchivos+positions[pos][1]+"' target='_blank'>Imágen: "+positions[pos][1]+"</a></td>"+
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
function imagenPortada_inicializarBotones() {
	consultarTuplasExistentes("updPositionsImagenPortada.php", true);
}


/*
	Cierra todos los poups de la interfaz actual
*/
function imagenPortada_cerrarPopUp(){
	$("#backImage").hide();
}


/*
	Muestra el primer poup que es el que tiene los datos a modificar o crear una nueva tupla
*/
function abrirModificarCampos(posit){
	if(!bool_borrar){
		$("#mascaraPrincipal").show();
		var objDiv = $("#backImage");
		objDiv.find("input[type='file']").val("");
		
		
		if(posit >= 0){
			pos_comp = posit;
			$("#tituloEmergente").text("Modificar Imágen de Portada");
		}
		else{
			pos_comp = -1;
			$("#tituloEmergente").text("Nueva Imágen de Portada");
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
		if (confirm('\u00BFEsta seguro de eliminar la imágen de portada: "'+positions[posit][1]+'"?')) {
			datos = {
				id: positions[posit][0],
				borrar: 1
			};
			
			$.ajax({
				url: "lib_php/updImagenPortada.php",
				type: "POST",
				dataType: "json",
				data: datos
			}).always(function(respuesta_json){
				$("#resultados").text(respuesta_json.mensaje);
				
				if (respuesta_json.isExito == 1) {
					principalCerrarPopUp(imagenPortada_cerrarPopUp);
					consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
				}
				else
					alert(respuesta_json.mensaje);
			});
		}
	}
}

	
/*
	Valida los campos ya sea para crear una nueva tupla o para modificarlo
*/
function validarCampos () {
	var	continua = false;
	var id = pos_comp;
	
	if (pos_comp == -1) {
		if (!vacio($("#imagen").val(), "Imágen")) {
			continua = true;
		}
	}
	else
		id = positions[pos_comp][0];
					
				
	if (continua) {
		save();
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
	
	
	$("#idImagenPortada").val(id);
	
	
	$("#subirImagenPortada").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			$("#btnGuardar").show();
			$("#mensajeTemporal").hide();
			$("#backImage").css({cursor:"default"});
			
			$("#resultados").text(respuesta_json.mensaje);
			principalCerrarPopUp(imagenPortada_cerrarPopUp);
			consultarTuplasExistentes(nombrePHPConsultar, isBorrarTuplas, arrayCamposConsulta);
		}
	});
}
/**/