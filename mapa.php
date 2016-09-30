<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<style>
	* {
		margin:0px auto;
		padding:0px;
	}
	
	.contenedorCompleto, .mapaGoogle, .contenedorCanvas {
		position:relative;
		width:100%;
		height:550px;
	}
	
	.contenedorCanvas {
		position:absolute;
		top:0px;
		overflow:hidden;
		display:none;
	}
	
	.controles, .posiciones {
		position:relative;
		width:100%;
	}
	
	.infoWindow {
		max-width:300px;
	}
	
	.infoWindow p {
		font-weight:bold;
		padding-bottom:10px;
	}
	
	.infoWindow table {
		border:none;
		border-spacing:0px;
		border-collapse:collapse;
		width:100%;
		height:100%;
	}
	
	.infoWindow table td.imagen {
		width:100px;
		padding-right:20px;
	}
	
	.infoWindow table td.imagen img {
		width:inherit;
	}
</style>
<script type="text/javascript" src="js/jQuery.js"></script>
<script type="text/javascript">
	var map;
	var objetoPoligono;
	var arrayPosiciones = new Array();
	var objetoLineas;
	var listenerCrearLineasPoligono;
	var listenerIsInsideArea;
	var arrayMarcas = new Array();
	var urlArchivos = "images/images/";

	$(document).ready(function(){
		$("#textPosiciones").val("");
		$("#txtPunto").val("");
		
		//define el google maps
		var mapaGoogle = document.getElementById("mapaGoogle");
		var mapOptions = {
			center: new google.maps.LatLng(20.650118, -103.422227),
			zoom: 16,
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
	});
	
	
	function limpiarMapa() {
		$("#textPosiciones").val("");
		objetoLineas.setPath([]);
		if (typeof objetoPoligono !== 'undefined') {
			objetoPoligono.setMap(null);
			
			if (arrayMarcas.length > 0) {
				for (var x = 0; x < arrayMarcas.length; x++) {
					arrayMarcas[x].setMap(null);
				}
			}
			
			arrayMarcas = new Array();
		}
	}
	
	
	function terminar() {
		var path = objetoLineas.getPath();
		
		if (path.length > 0) {
			ultimaCoordenada = path.getAt(0);
			path.push(ultimaCoordenada);
			
			cadena = "";
			
			for (var x = 0; x < path.length; x++) {
				if (x != 0)
					cadena += ", ";
				
				cadena += "("+path.getAt(x).lat()+", "+path.getAt(x).lng()+")";
			}
			
			$("#textPosiciones").val(cadena);
			
			objetoPoligono = new google.maps.Polygon({
				paths: path,
				strokeColor: "#FF0000",
				strokeOpacity: 0.8,
				strokeWeight: 3,
				fillColor: "#FF0000",
				fillOpacity: 0.35
			});
		
			objetoPoligono.setMap(map);
			objetoLineas.setPath([]);
			google.maps.event.removeListener(listenerCrearLineasPoligono);
		}
	}
	
	function definirArea() {
		limpiarMapa();
		listenerCrearLineasPoligono = google.maps.event.addListener(map, 'click', crearLineasPoligono);
	}
	
	function crearLineasPoligono(event) {
		var path = objetoLineas.getPath();
		path.push(event.latLng);
	}
	
	
	function buscarEnMapa() {
		if (typeof objetoPoligono !== 'undefined') {
			$.ajax({
				url: "lib_php/consInmueble.php",
				type: "POST",
				dataType: "json"
			}).always(function(respuesta_json){
				var maxCadena = 200;
				
				for (var x = 0; x < respuesta_json.datos.length; x++) {
					googlePunto = new google.maps.LatLng(respuesta_json.datos[x].latitud, respuesta_json.datos[x].longitud);
					titulo = respuesta_json.datos[x].titulo;
					
					if (google.maps.geometry.poly.containsLocation(googlePunto, objetoPoligono)) {
						descripcion = (respuesta_json.datos[x].descripcion).length > maxCadena ? ((respuesta_json.datos[x].descripcion).substr(0, (maxCadena - 3)+"...")) : respuesta_json.datos[x].descripcion;
									
						cadenaInfo =
							'<div class="infoWindow">'+
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
	}
</script>
</head>

<body>
	<div id="contenedorCompleto" class="contenedorCompleto">
		<div id="mapaGoogle" class="mapaGoogle"></div>
    </div>
    <div class="controles">
    	<input type="button" value="Definir Area" onclick="definirArea();" />
    	<input type="button" value="Limpiar" onclick="limpiarMapa();" />
        <input type="button" value="Terminar" onclick="terminar();" />
        <input type="button" value="Buscar" onclick="buscarEnMapa();" />
    </div>
    <div class="posiciones">Coordenadas: <input id="textPosiciones" type="text" value="" style="width:80%;" /></div>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js&key=AIzaSyBRA-KzdLEazYGt2JI83xzYpJegbTwKsI8?sensor=true"></script>
</body>
</html>