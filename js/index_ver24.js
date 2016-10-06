// JavaScript Document
var map;
var objetoLineas;
var objetoPoligono;
var arrayMarcas = new Array();
var arrayMarcasPuntos = new Array();
var listenerCrearLineasPoligono = null;
var arrayPuntos;
var urlArchivos = "images/images/";
var index_galeria_timer = 0;
var index_galeria_desp = 7000;


/*
	funciones principales al cargar la pagina
*/
$(document).ready(function(){
	index_inicializarGaleria();
	
	//agrega evento para cuando se cambie de transaccion en los filtros del mapa
	$("#index_filtros_transaccion").each(function(){
		var elemento = $(this);
		
		$(this).find("li.lista li").on({
			click: function() {
				_transaccion = $(this).attr("data-value");
				$("#index_filtros_tipoInmueble p").attr("data-value", -1);
				$("#index_filtros_tipoInmueble p").text("");
				
				$("#index_filtros_tipoInmueble li.lista li").show();
				if (parseInt(_transaccion) == 3) {
					$("#index_filtros_tipoInmueble li.lista li").hide();
					$("#index_filtros_tipoInmueble li.lista li[data-transaccion='3']").show();
				}
			}
		});
	});
	
	
	if (typeof post_mapa !== 'undefined') {
		index_mostrarMapa(post_transaccion);
	}
	
	if (typeof post_validar !== 'undefined') {
		$.ajax({
			url: "lib_php/updUsuario.php",
			type: "POST",
			dataType: "json",
			data: {
				id: -1,
				validarCuenta: 1,
				validar: post_validar
			}
		}).always(function(respuesta_json){
			if (respuesta_json.isExito == 1) {
				gotoURL("index.php?validadoTrue=1");
			}
			else
				alert(respuesta_json.mensaje);
		});
	}
	if (typeof post_validadoTrue !== 'undefined') {
		$("#template_alertPersonalizado td").html("Tu cuenta ha sido validada, has login para comenzar tu cuenta.");
		template_alertPersonalizado();
	}
});


/*
	Agrega los estilos de la galeria, y comienza el desplazamiento por timer
*/
function index_inicializarGaleria() {
	var wCliente = $(window).width();
	var altura = ($(window).width() * parseInt($(".galeria").attr("data-height"))) / parseInt($(".galeria").attr("data-width"));
	$(".galeria").css("height", altura.toFixed(2)+"px");
	$(".galeria .desplazamiento").css("width", $(".galeria .desplazamiento .bloque").length+"00%");
	$(".galeria .desplazamiento .bloque").css("width", (100 / $(".galeria .desplazamiento .bloque").length)+"%");
	$(".galeria .desplazamiento").attr("data-pos", 0);
	
	index_galeria_timer = setTimeout("index_desplazamiento_direccion(1)", index_galeria_desp);
}


/*
	Realiza el desplazamiento por direccion:
	
		direccion:	Integer, es la direccion a desplazar, 0 a la izquierda, 1 a la derecha
*/
function index_desplazamiento_direccion(direccion) {
	clearTimeout(index_galeria_timer);
	var padre = $(".galeria");
	var hijos = padre.find(".desplazamiento .bloque").length;
	var posActual = parseInt(padre.find(".desplazamiento").attr("data-pos"));
	var posSiguiente = posActual;
	direccion = parseInt(direccion);
	
	if (direccion == 0)//izquierda
		posSiguiente = posActual == 0 ? (hijos - 1) : (posActual - 1);
	else//derecha
		posSiguiente = posActual == (hijos - 1) ? 0 : (posActual + 1);
	
	padre.find(".desplazamiento").animate({
		left: "-"+(posSiguiente * 100)+"%"
	}, 500);
	
	padre.find(".desplazamiento").attr("data-pos", posSiguiente);
	
	if (posSiguiente == (hijos - 1)) {
		padre.find(".desplazamiento").promise().done(function(){
			padre.find(".desplazamiento").css("left", "0%");
			padre.find(".desplazamiento").attr("data-pos", "0");
		});
	}
	
	index_galeria_timer = setTimeout("index_desplazamiento_direccion(1)", index_galeria_desp);
}


/*
	Cierra los popups de esta interfaz de nivel 1
*/
function index_cerrarPopUp() {
	$("#index_mensajesAlerts").hide();
}


/*
	Muestra un popup con los alerts del mapa
*/
function index_mensajesAlerts() {
	$("#template_mascaraPrincipal").show();
	
	var objDiv = $("#index_mensajesAlerts");
	lPos = ($(window).width() - objDiv.width())/2;
	tPos = ($("#template_mascaraPrincipal").height() - objDiv.height())/2;
		
	objDiv.css({
		"display": "block",
		"left": lPos+"px",
		"top": tPos+"px"
	});
}


/*
	Muestra el mapa de google maps
	
		* transaccion:	Integer, es la transaccion a buscar en el mapa
*/
function index_mostrarMapa(transaccion) {
	$("#index_filtros_transaccion li.lista li[data-value='"+transaccion+"']").click();
	$("#index_filtros_transaccion li.lista").hide();
	$('.galeria').css('width',0);
	
	
	if ($("#indexContenedorMapa").css("display") == "none") {
		$("#indexContenedorMapa").show();
		$(".controlesMapa").show();
		$(".filtrosMapa").show();
		tempCenter = new google.maps.LatLng(20.673792, -103.3354131);
		
		//define el google maps
		var mapaGoogle = document.getElementById("indexContenedorMapa");
		var mapOptions = {
			center: tempCenter,
			zoom: 13,
			mapMaker: true,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};//SATELLITE,ROADMAP
		
		map = new google.maps.Map(mapaGoogle, mapOptions);
		
		objetoLineas = new google.maps.Polyline({
			path: [],
			geodesic: true,
			strokeColor: "#852c2b",
			strokeOpacity: 1.0,
			strokeWeight: 2
		});
		
		objetoLineas.setMap(map);
		
		cadenaInfo =
			"<div class='template_infoWindow'>"+
				"<p>Instrucciones del mapa</p>"+
				"- Haga  click dentro del mapa para agregar puntos y delimitar el área de búsqueda.<br />"+
				"- A partir del tercer click se mostrara el área de búsqueda.<br />"+
				"- Puedes arrastras los puntos del mapa para modificar el área.<br />"+
				"- Al finalizar el área de búsqueda da click en buscar propiedades.<br />"+
				"- Puedes hacer click en limpiar mapa para comenzar de nuevo.<br />"+
				"- Para filtrar su búsqueda puede utilizar el cuadro ubicado en el lado derecho del mapa."+
			"</div>";
		var infoWindow = new google.maps.InfoWindow({
			content: cadenaInfo,
			position: tempCenter
		});
		infoWindow.open(map);
	}
	
	//comienza la definicion de puntos en el mapa
	index_definirArea_mapa();
}


/*
	Comienza la definicion del area en el mapa para crear el circulo o el poligono; segun sea el caso
*/
function index_definirArea_mapa() {
	index_limpiarMapa();
	if (listenerCrearLineasPoligono == null)
		listenerCrearLineasPoligono = google.maps.event.addListener(map, 'click', index_crearLineasPoligono);
}


/*
	Limpia las posiciones marcadas en el mapa
*/
function index_limpiarMapa() {
	objetoLineas.setPath([]);
	objetoLineas.setMap(map);
	arrayPuntos = new Array();
	
	if (arrayMarcas.length > 0) {
		for (var x = 0; x < arrayMarcas.length; x++) {
			arrayMarcas[x].setMap(null);
		}
	}
		
	arrayMarcas = new Array();
	
	for (var x = 0; x < arrayMarcasPuntos.length; x++) {
		arrayMarcasPuntos[x].setMap(null);
	}
	
	arrayMarcasPuntos = new Array();
	if (typeof objetoPoligono !== 'undefined') {
		objetoPoligono.setMap(null);
	}
}


/*
	Captura el evento de hacer click en el mapa y va agregando los puntos del poligono en el mapa
*/
function index_crearLineasPoligono(event) {
	var path = objetoLineas.getPath();
	arrayPuntos.push(event.latLng);
	
	index_terminar();
}


/*
	Termina la definicion de puntos para el poligono en el mapa
*/
function index_terminar() {
	if (arrayPuntos.length > 0) {
		var path = objetoLineas.getPath();
		
		switch (arrayPuntos.length) {
			case 1://dibuja primeramente un circulo con un radio de 500mts
				path.push(arrayPuntos[0]);
				objetoLineas.setPath(path);
				
				//agrega marca en la posicion marcada para busqueda
				var marca = new google.maps.Marker({
					id: 0,
					position : arrayPuntos[0],
					map : map,
					draggable: true,
					icon: "images/marcadorVertice.png"
				});
				
				arrayMarcasPuntos.push(marca);
				google.maps.event.addListener(marca, 'drag', function(){
					tempId = parseInt(this.get("id"));
					tempPosition = this.getPosition();
					var path = objetoLineas.getPath();
					path.setAt(tempId, tempPosition);
					arrayPuntos[tempId] = tempPosition;
				});
				break;
			case 2://defino el radio en el segundo punto para el circulo
				path.push(arrayPuntos[1]);
				objetoLineas.setPath(path);
				
				//agrega marca en la posicion marcada para busqueda
				var marca = new google.maps.Marker({
					id: 1,
					position : arrayPuntos[1],
					map : map,
					draggable: true,
					icon: "images/marcadorVertice.png"
				});
				
				arrayMarcasPuntos.push(marca);
				google.maps.event.addListener(marca, 'drag', function(){
					tempId = parseInt(this.get("id"));
					tempPosition = this.getPosition();
					var path = objetoLineas.getPath();
					path.setAt(tempId, tempPosition);
					arrayPuntos[tempId] = tempPosition;
				});
				break;
			default://dibuja las lineas del poligono
				if (arrayPuntos.length == 3) {
					path.push(arrayPuntos[2]);
					objetoLineas.setMap(null);
					
					objetoPoligono = new google.maps.Polygon({
						paths: path,
						strokeColor: "#852c2b",
						strokeOpacity: 0.8,
						strokeWeight: 3,
						fillColor: "#852c2b",
						fillOpacity: 0.35
					});
				
					objetoPoligono.setMap(map);
					
					
					//agrega marca en la posicion marcada para busqueda
					var marca = new google.maps.Marker({
						id: 2,
						position : arrayPuntos[2],
						map : map,
						draggable: true,
						icon: "images/marcadorVertice.png"
					});
					
					arrayMarcasPuntos.push(marca);
					google.maps.event.addListener(marca, 'drag', function(){
						tempId = parseInt(this.get("id"));
						tempPosition = this.getPosition();
						var path = objetoLineas.getPath();
						path.setAt(tempId, tempPosition);
						arrayPuntos[tempId] = tempPosition;
					});
				}
				else {
					path.push(arrayPuntos[arrayPuntos.length - 1]);
					
					
					var marca = new google.maps.Marker({
						id: arrayPuntos.length - 1,
						position : arrayPuntos[arrayPuntos.length - 1],
						map : map,
						draggable: true,
						icon: "images/marcadorVertice.png"
					});
					
					arrayMarcasPuntos.push(marca);
					google.maps.event.addListener(marca, 'drag', function(){
						tempId = parseInt(this.get("id"));
						tempPosition = this.getPosition();
						var path = objetoLineas.getPath();
						path.setAt(tempId, tempPosition);
						arrayPuntos[tempId] = tempPosition;
					});
					
					objetoPoligono.setPaths(path);
				}
				break;
		}
	}
}


function index_buscarEnMapa() {
	if (arrayPuntos.length >= 3) {
		if (arrayMarcas.length > 0) {
			for (var x = 0; x < arrayMarcas.length; x++) {
				arrayMarcas[x].setMap(null);
			}
		}
			
		arrayMarcas = new Array();
	
		$tra = $("#index_filtros_transaccion p").attr("data-value");
		if( $tra == -1 ) $tra = 2;
		$.ajax({
			url: "lib_php/consultamapa.php",
			type: "POST",
			dataType: "json",
			data: {
				transaccion: $tra,
				tipoInmueble: $("#index_filtros_tipoInmueble p").attr("data-value"),
				estado: -1,
				ciudad: -1,
				colonia: -1,
				codigo: "",
				preciosMin: -1,
				preciosMax: -1,
				wcs: -1,
				recamaras: -1
			},
		}).always(function(respuesta_json){
			var maxCadena = 200;
			
			for (var x = 0; x < respuesta_json.datos.length; x++) {
				googlePunto = new google.maps.LatLng(respuesta_json.datos[x].latitud, respuesta_json.datos[x].longitud);
				console.log(googlePunto);
				titulo = respuesta_json.datos[x].titulo;
				conMarca = false;
				
				if (arrayPuntos.length < 3) {//circulo
					bounds = objetoCirculo.getBounds();
					if (bounds.contains(googlePunto))
						conMarca = true;
				}
				else {//puntos
					if (google.maps.geometry.poly.containsLocation(googlePunto, objetoPoligono))
						conMarca = true;
				}
				
				if (conMarca) {
					descripcion = (respuesta_json.datos[x].descripcion).length > maxCadena ? ((respuesta_json.datos[x].descripcion).substr(0, (maxCadena - 3))+"...") : respuesta_json.datos[x].descripcion;
					
					moneda = new NumeroFormato(respuesta_json.datos[x].precio);
					
					_newUrl =
						(respuesta_json.datos[x].transaccion == 1 ? "renta" : (respuesta_json.datos[x].transaccion == 2 ? "venta" : "renta-vacacional"))+"/"+
						(respuesta_json.datos[x].tipoInmueble == -1 ? "todos-los-tipos" : ($("#index_filtros_tipoInmueble p").text().toLowerCase()+($("#index_filtros_tipoInmueble p").attr("data-value") != 6 ? "s" : "es")))+"/"+
						respuesta_json.datos[x].estadoNombre+"/"+
						respuesta_json.datos[x].ciudadNombre+"/"+
						respuesta_json.datos[x].id;
								
					cadenaInfo =
						'<div class="template_infoWindow" onclick="gotoURL(\''+_newUrl+'\');">'+
							'<p>'+respuesta_json.datos[x].titulo+'</p>'+
							'<table>'+
								'<tbody>'+
									'<tr>'+
										'<td class="imagen"><img src="'+urlArchivos+respuesta_json.datos[x].imagen+'" /></td>'+
										'<td>'+descripcion+'<br /><span>Precio: $'+(moneda.formato(2, true).replace(".00", ""))+' MXN</span></td>'+
									'</tr>'+
								'</tbody>'+
							'</table>'+
						'</div>';
						
					var infoWindow = new google.maps.InfoWindow({
						content: cadenaInfo,
					});
					
					var marca = new google.maps.Marker({
						id: respuesta_json.datos[x].id,
						isActived: false,
						title: titulo,
						position : googlePunto ,
						map : map,
						infowindow : infoWindow,
						icon: "images/marcador3.png"
					});
					
					arrayMarcas.push(marca);
					
					google.maps.event.addListener(marca, 'mouseover', function(){
						if (!this.get("isActived"))
							this.infowindow.open(map, this);
					});
					google.maps.event.addListener(marca, 'mouseout', function(){
						if (!this.get("isActived"))
							this.infowindow.close();
					});
					google.maps.event.addListener(marca, 'click', function(){
						this.set("isActived", !this.get("isActived"));
						
						if (this.get("isActived"))
							this.infowindow.open(map, this);
						else
							this.infowindow.close();
					});
				}
			}
			
			if (arrayMarcas.length == 0) {
				$("#index_mensajesAlerts td").text("No se encontraron inmuebles dentro del area designada.");
				index_mensajesAlerts();
			}
		});
	}
	else {
		$("#index_mensajesAlerts td").text("Ingrese una ubicacion de busqueda.");
		index_mensajesAlerts();
	}
}

/*
 Redireccion a la url, pero antes, asigna a la seccion el parametro de regresar
 */
function catalogo_redirecciona_regresar(newUrl) {
	$.ajax({
		url: "lib_php/updFiltros.php",
		type: "POST",
		dataType: "json",
		data: {
			updateParam: 1,
			nombre : "regresar",
			valor: 1
		}
	}).always(function(respuesta_json){
		if (respuesta_json.isExito == 1)
			gotoURL(newUrl);
	});
}
/**/
/**/