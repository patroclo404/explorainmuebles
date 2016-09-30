// JavaScript Document
var map;
var objetoLineas;
var objetoPoligono;
var objetoCirculo;
var arrayMarcas = new Array();
var listenerCrearLineasPoligono = null;
var arrayPuntos;
var urlArchivos = "images/images/";


/*
	funciones principales al cargar la pagina
*/
$(document).ready(function(){
	$(".contenedorMenu2 a.btnBotones.mundo").on({
		click: function() {
			index_mostrarMapa();
		}
	});
});


/*
	Muestra el mapa de google maps
*/
function index_mostrarMapa() {
	$("#indexContenedorMapa").show();
	$(".controlesMapa").show();
	tempCenter = new google.maps.LatLng(20.673792, -103.3354131);
	
	//define el google maps
	var mapaGoogle = document.getElementById("indexContenedorMapa");
	var mapOptions = {
		center: tempCenter,
		zoom: 13,
		mapMaker: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};//SATELLITE,ROADMAP
	
	map = new google.maps.Map(mapaGoogle, mapOptions);
	
	objetoLineas = new google.maps.Polyline({
		path: [],
		geodesic: true,
		strokeColor: "#FF0000",
		strokeOpacity: 1.0,
		strokeWeight: 2
	});
	
	objetoLineas.setMap(map);
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
	arrayPuntos = new Array();
	if (typeof objetoPoligono !== 'undefined') {
		objetoPoligono.setMap(null);
			
		if (arrayMarcas.length > 0) {
			for (var x = 0; x < arrayMarcas.length; x++) {
				arrayMarcas[x].setMap(null);
			}
		}
			
		arrayMarcas = new Array();
	}
	if (typeof objetoCirculo !== 'undefined') {
		objetoCirculo.setMap(null);
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
		switch (arrayPuntos.length) {
			case 1://dibuja primeramente un circulo con un radio de 500mts
				objetoCirculo = new google.maps.Circle({
					strokeColor: "#FF0000",
					strokeOpacity: 0.8,
					strokeWeight: 3,
					fillColor: "#FF0000",
					fillOpacity: 0.35,
					center: arrayPuntos[0],
					radius: 500
				});
			
				objetoCirculo.setMap(map);
				break;
			case 2://defino el radio en el segundo punto para el circulo
				objetoCirculo.setRadius(google.maps.geometry.spherical.computeDistanceBetween(arrayPuntos[0], arrayPuntos[1]));
				break;
			default://dibuja las lineas del poligono
				var path = objetoLineas.getPath();
				
				if (arrayPuntos.length == 3) {
					objetoCirculo.setMap(null);
					path.push(arrayPuntos[0]);
					path.push(arrayPuntos[1]);
					path.push(arrayPuntos[2]);
					path.push(arrayPuntos[0]);
					
					objetoPoligono = new google.maps.Polygon({
						paths: path,
						strokeColor: "#FF0000",
						strokeOpacity: 0.8,
						strokeWeight: 3,
						fillColor: "#FF0000",
						fillOpacity: 0.35
					});
				
					objetoPoligono.setMap(map);
				}
				else {
					path.pop();
					path.push(arrayPuntos[arrayPuntos.length - 1]);
					
					ultimaCoordenada = path.getAt(0);
					path.push(ultimaCoordenada);
					
					objetoPoligono.setPaths(path);
				}
				break;
		}
	}
}


function index_buscarEnMapa() {
	if (arrayPuntos.length > 0) {
		objetoLineas.setPath([]);
		google.maps.event.removeListener(listenerCrearLineasPoligono);
		listenerCrearLineasPoligono = null;
	
		
		$.ajax({
			url: "lib_php/consInmueble.php",
			type: "POST",
			dataType: "json",
			data: {
				transaccion: 1,
				tipoInmueble: -1,
				estado: -1,
				ciudad: -1,
				colonia: -1,
				codigo: "",
				preciosMin: "",
				preciosMax: "",
				wcs: "",
				recamaras: ""
			}
		}).always(function(respuesta_json){
			var maxCadena = 200;
			
			for (var x = 0; x < respuesta_json.datos.length; x++) {
				googlePunto = new google.maps.LatLng(respuesta_json.datos[x].latitud, respuesta_json.datos[x].longitud);
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
					descripcion = (respuesta_json.datos[x].descripcion).length > maxCadena ? ((respuesta_json.datos[x].descripcion).substr(0, (maxCadena - 3)+"...")) : respuesta_json.datos[x].descripcion;
								
					cadenaInfo =
						'<div class="template_infoWindow">'+
							'<p>'+respuesta_json.datos[x].titulo+'</p>'+
							'<table>'+
								'<tbody>'+
									'<tr>'+
										'<td class="imagen"><img src="'+urlArchivos+respuesta_json.datos[x].imagen+'" /></td>'+
										'<td>'+descripcion+'</td>'+
									'</tr>'+
								'</tbody>'+
							'</table>'+
						'</div>';
						
					var infoWindow = new google.maps.InfoWindow({
						content: cadenaInfo
					});
					
					var marca = new google.maps.Marker({
						id: respuesta_json.datos[x].id,
						title: titulo,
						position : googlePunto,
						map : map,
						infowindow : infoWindow
					});
					
					arrayMarcas.push(marca);
					
					google.maps.event.addListener(marca, 'click', function(){
						this.infowindow.open(map, this);
					});
				}
			}
		});
	}
	else
		alert("Ingrese una ubicacion de busqueda.");
}
/**/