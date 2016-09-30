// JavaScript Document
var map;
var marker;


/*
	Carga las funciones y estilos al terminar de cargar la interfaz
*/
$(document).ready(function(){
	//eventos para el mapa
	tempCenter = new google.maps.LatLng(20.650118, -103.422227);
	
	//define el google maps
	var mapaGoogle = document.getElementById("contenedorMapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 14,
		mapMaker: true,
		scrollwheel: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	map = new google.maps.Map(mapaGoogle, mapOptions);
	
	google.maps.event.addListener(map, "click", mapDefinirMarca);
	
	//actualiza los datos para editar el inmueble (campos selects)
	if (typeof post_tipo !== 'undefined') {
		if (parseInt(post_tipo) != -1) {
			$("#desarrollo_tipo li.lista li[data-value='"+post_tipo+"']").click();
			$("#desarrollo_tipo li.lista").hide();
		}
		
		tempCenter = new google.maps.LatLng(post_latitud, post_longitud);
		map.setCenter(tempCenter);
		mapDefinirMarca({latLng: tempCenter});
	}
	
	
	//agrega evento para borrar las imagenes de la galeria
	$("#galeriaImagenes .bloqueImagen").each(function(){
		var elemento = $(this);
		
		$(this).find("span.borrar").on({
			click: function() {
				if (confirm("¿Esta seguro de eliminar la imágen?")) {
					/*$.ajax({
						url: "lib_php/updInmuebleImagen.php",
						type: "POST",
						dataType: "json",
						data: {
							borrar: 1,
							idImagen: elemento.attr("data-imagen")
						}
					}).always(function(respuesta_json){
						if (respuesta_json.isExito == 1) {
							elemento.remove();
						}
					});*/
				}
			}
		});
	});
});


/*
	Asigna una marca en el mapa, ademas de los campos de latitud y longitud
	
		* evt:	Event, es el evento asignado en el mapa para llamar esta funcion
*/
function mapDefinirMarca(evt) {
	$("#_desarrolloLatitud").val(evt.latLng.lat());
	$("#_desarrolloLongitud").val(evt.latLng.lng());
	
	if (typeof marker !== 'undefined')
		marker.setMap(null);
	
	marker = new google.maps.Marker({
		position: evt.latLng,
		map: map,
		icon: "images/marcador.png"
	});
}


/*
	Se cargo una imagen nueva por medio del iframe
	
		* nombreArchivo:	String, es el nombre del archivo guardaro en temp
*/
function nuevoAnuncio_tempImagenCargada(nombreArchivo) {
	var urlArchivosTemporales = "images/images/temp/";
	var imagenesTemporales = $("#imagenesTemporales");
	var isPrimera = false;
	if ($("#galeriaImagenes").length == 0)
		isPrimera = imagenesTemporales.find(".bloqueImagen").length == 0 ? true : false;
	
	var elemento = 
		"<div class='bloqueImagen' data-imagen='"+nombreArchivo+"'>"+
			"<img src='"+urlArchivosTemporales+nombreArchivo+"' />"+
			"<span class='borrar'>X</span>"+
			"<p><input type='radio' name='radioImagenPrincipal' "+(isPrimera ? "checked='checked'" : "")+" /></p>"+
		"</div>";
	
	imagenesTemporales.append(elemento);
	imagenesTemporales.find(".bloqueImagen .borrar").unbind();
	imagenesTemporales.find(".bloqueImagen").each(function(){
		var elemento = $(this);
		
		$(this).find(".borrar").on({
			click: function() {
				$.ajax({
					url: "lib_php/tempSubirImagen2.php",
					type: "POST",
					dataType: "json",
					data: {
						borrar: 1,
						imagen: elemento.attr("data-imagen")
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						elemento.remove();
					}
				});
			}
		});
	});
	
	
	$("#iframeSubirImagen").html("");
	if (imagenesTemporales.find(".bloqueImagen").length < 20) {
		$("#iframeSubirImagen").html('<iframe src="lib_php/tempSubirImagen.php" frameborder="0" width="400" height="50"></iframe>');
	}
}


/*
	Valida los campos para guardar los datos de perfil de usuario
*/
function validarCampos() {
	if (!vacio($("#desarrollo_titulo").val(), $("#desarrollo_titulo").attr("placeholder"))) {
		if (!vacio((parseInt($("#desarrollo_tipo p").attr("data-value")) == -1 ? "" : $("#desarrollo_tipo p").attr("data-value")), "Tipo")) {
			if (!vacio($("#desarrollo_descripcion").val(), $("#desarrollo_descripcion").attr("placeholder"))) {
				if ($("#_desarrolloLatitud").val() != "") {
					if (parseInt($("#idDesarrollo").val()) == -1) {//nuevo
						if ($("#imagenesTemporales .bloqueImagen").length == 0) {
							alert("Ingrese al menos una imágen para el desarrollo");
							return false;
						}
						else {
							if($("input[name='radioImagenPrincipal']:checked").length == 0) {
								alert("Selecciona una imágen como la principal");
								return false;
							}
						}
					}
					else {//modificar
						if($("input[name='radioImagenPrincipal']:checked").length == 0) {
							alert("Selecciona una imágen como la principal");
							return false;
						}
					}
													
					if (!isVacio($("#desarrollo_unidades").val())) {
						if (!entero($("#desarrollo_unidades").val(), $("#desarrollo_unidades").attr("placeholder"))) {
							return false;
						}
					}
													
													
					save();
				}
				else {
					alert("Agrege la posición del desarrollo en el mapa.");
					return false;
				}
			}
		}
	}
}


/*
	Guarda un nuevo desarrollo
*/
function save() {
	$("#_desarrolloTipo").val($("#desarrollo_tipo p").attr("data-value"));
	
	//ajuste para imagenes
	if ($("#imagenesTemporales .bloqueImagen").length > 0) {
		var imagenes = Array();
		var _tempPrincipal = Array();
		
		$("#imagenesTemporales .bloqueImagen").each(function(){
			imagenes.push($(this).attr("data-imagen"));
			_tempPrincipal.push(($(this).find("input[name='radioImagenPrincipal']").prop("checked") ? 1 : 0));
		});
		
		$("#imagen").val(imagenes.toString());
		$("#imagenPrincipal").val(_tempPrincipal.toString());
	}
	
	
	if ($("#idImagenPrincipal").length == 1) {
		$("#idImagenPrincipal").val($("#galeriaImagenes input[name='radioImagenPrincipal']:checked").attr("data-id"));
	}
	
	
	$("#btnGuardar").hide();
	$("#mensajeTemporal").show();
	$("#lk_crearDesarrollo").css({cursor:"wait"});	
	
	
	$("#subirDesarrollo").ajaxSubmit({
		dataType: "json",
		success: function(respuesta_json){
			gotoURL("perfil.php");
		}
	});
}
/**/