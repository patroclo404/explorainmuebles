<?php


	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	$validarCodigo = isset($_POST["validarCodigo"]) ? 1 : 0;
	$urlArchivos = "../images/images/";
	
	
	/*
		Valida el codigo sea unico entre los usuarios para el inmueble
	*/
	if ($validarCodigo) {
		$usuario = $_SESSION[userId];
		$codigo = $_POST["codigo"];

		
		$consulta = "SELECT IMU_ID FROM INMUEBLE WHERE IMU_USUARIO = ".$usuario." AND IMU_CODIGO = '".$codigo."';";
		$res = crearConsulta($consulta);
		$row = mysql_fetch_row($res);
		if ($row[0] != "") {//si existe
			if ($row[0] != $id) {//pertenece a otra usuario
				$mensaje = "El código ya existe, intente con uno diferente.";
				$isExito = 0;
			}
		}
		
		$arrayRespuesta = array(
			"mensaje"		=>	$mensaje,
			"isExito"		=>	$isExito
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	if($borrar){
		/*$consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ".$id.";";
		$res = crearConsulta($consulta);
		while($row = mysql_fetch_row($res)) {
			unlink($urlArchivos.$row[0]);
		}
		
		$consulta = "DELETE FROM INMUEBLE WHERE IMU_ID = ".$id.";";
		crearConsulta($consulta);*/
	}
	
	
	if ($nuevo) {
		$titulo = $_POST["titulo"];
		$usuario = $_SESSION[userId];
		$categoria = $_POST["categoria"];
		$tipo = $_POST["tipo"];
		$precio = $_POST["precio"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"];
		$ciudad = $_POST["ciudad"];
		$colonia = $_POST["colonia"];
		$cp = $_POST["cp"];
		$latitud = $_POST["latitud"];
		$longitud = $_POST["longitud"];
		$descripcion = htmlspecialchars($_POST["descripcion"]);
		$antiguedad = $_POST["antiguedad"] != "" ? $_POST["antiguedad"] : "null";
		$codigo = $_POST["codigo"] != "" ? "'".$_POST["codigo"]."'" : "null";
		$dimensionTotal = $_POST["dimensionTotal"] != "" ? $_POST["dimensionTotal"] : "null";
		$dimensionConstruida = $_POST["dimensionConstruida"] != "" ? $_POST["dimensionConstruida"] : "null";
		$estadoConservacion = $_POST["estadoConservacion"];
		$amueblado = $_POST["amueblado"];
		$cocinaEquipada = isset($_POST["cocinaEquipada"]) ? 1 : 0;
		$estudio = isset($_POST["estudio"]) ? 1 : 0;
		$cuartoServicio = isset($_POST["cuartoServicio"]) ? 1 : 0;
		$cuartoTV = isset($_POST["cuartoTV"]) ? 1 : 0;
		$bodega = isset($_POST["bodega"]) ? 1 : 0;
		$terraza = isset($_POST["terraza"]) ? 1 : 0;
		$jardin = isset($_POST["jardin"]) ? 1 : 0;
		$areaJuegosInfantiles = isset($_POST["areaJuegosInfantiles"]) ? 1 : 0;
		$comedor = isset($_POST["comedor"]) ? 1 : 0;
		$serviciosBasicos = isset($_POST["serviciosBasicos"]) ? 1 : 0;
		$gas = isset($_POST["gas"]) ? 1 : 0;
		$lineaTelefonica = isset($_POST["lineaTelefonica"]) ? 1 : 0;
		$internetDisponible = isset($_POST["internetDisponible"]) ? 1 : 0;
		$aireAcondicionado = isset($_POST["aireAcondicionado"]) ? 1 : 0;
		$calefaccion = isset($_POST["calefaccion"]) ? 1 : 0;
		$cuotaMantenimiento = $_POST["cuotaMantenimiento"] != "" ? $_POST["cuotaMantenimiento"] : "null";
		$casetaVigilancia = isset($_POST["casetaVigilancia"]) ? 1 : 0;
		$elevador = $_POST["elevador"] != "" ? $_POST["elevador"] : "null";
		$seguridad = isset($_POST["seguridad"]) ? 1 : 0;
		$alberca = isset($_POST["alberca"]) ? 1 : 0;
		$casaClub = isset($_POST["casaClub"]) ? 1 : 0;
		$canchaTenis = isset($_POST["canchaTenis"]) ? 1 : 0;
		$vistaMar = isset($_POST["vistaMar"]) ? 1 : 0;
		$jacuzzi = isset($_POST["jacuzzi"]) ? 1 : 0;
		$estacionamientoVisitas = $_POST["estacionamientoVisitas"] != "" ? $_POST["estacionamientoVisitas"] : "null";
		$permiteMascotas = isset($_POST["permiteMascotas"]) ? 1 : 0;
		$gimnasio = isset($_POST["gimnasio"]) ? 1 : 0;
		$centrosComercialesCercanos = isset($_POST["centrosComercialesCercanos"]) ? 1 : 0;
		$escuelasCercanas = isset($_POST["escuelasCercanas"]) ? 1 : 0;
		$fumadoresPermitidos = isset($_POST["fumadoresPermitidos"]) ? 1 : 0;
		$numeroOficinas = $_POST["numeroOficinas"] != "" ? $_POST["numeroOficinas"] : "null";
		$wcs = $_POST["wcs"] != -1 ? $_POST["wcs"] : "null";
		$recamaras = $_POST["recamaras"] != -1 ? $_POST["recamaras"] : "null";
		
		$transaccion = $_POST["transaccion"];
		$imagen = $_POST["imagen"];
		$imagenPrincipal = $_POST["imagenPrincipal"];
		
		
		$consulta = "INSERT INTO INMUEBLE(IMU_TITULO, IMU_USUARIO, IMU_CATEGORIA_INMUEBLE, IMU_TIPO_INMUEBLE, IMU_PRECIO, IMU_CALLE_NUMERO, IMU_ESTADO, IMU_CIUDAD, IMU_COLONIA, IMU_CP, IMU_LATITUD, IMU_LONGITUD, IMU_DESCRIPCION, IMU_ANTIGUEDAD, IMU_CODIGO, IMU_DIMENSION_TOTAL, IMU_DIMENSION_CONSTRUIDA, IMU_ESTADO_CONSERVACION, IMU_AMUEBLADO, IMU_COCINA_EQUIPADA, IMU_ESTUDIO, IMU_CUARTO_SERVICIO, IMU_CUARTO_TV, IMU_BODEGA, IMU_TERRAZA, IMU_JARDIN, IMU_AREA_JUEGOS_INFANTILES, IMU_COMEDOR, IMU_SERVICIOS_BASICOS, IMU_GAS, IMU_LINEA_TELEFONICA, IMU_INTERNET_DISPONIBLE, IMU_AIRE_ACONDICIONADO, IMU_CALEFACCION, IMU_CUOTA_MANTENIMIENTO, IMU_CASETA_VIGILANCIA, IMU_ELEVADOR, IMU_SEGURIDAD, IMU_ALBERCA, IMU_CASA_CLUB, IMU_CANCHA_TENIS, IMU_VISTA_MAR, IMU_JACUZZI, IMU_ESTACIONAMIENTO_VISITAS, IMU_PERMITE_MASCOTAS, IMU_GIMNASIO, IMU_CENTROS_COMERCIALES_CERCANOS, IMU_ESCUELAS_CERCANAS, IMU_FUMADORES_PERMITIDOS, IMU_NUMERO_OFICINAS, IMU_WCS, IMU_RECAMARAS, IMU_CREATE) VALUES('".$titulo."', ".$usuario.", ".$categoria.", ".$tipo.", ".$precio.", '".$calleNumero."', ".$estado.", ".$ciudad.", ".$colonia.", ".$cp.", '".$latitud."', '".$longitud."', '".$descripcion."', ".$antiguedad.", ".$codigo.", ".$dimensionTotal.", ".$dimensionConstruida.", ".$estadoConservacion.", ".$amueblado.", ".$cocinaEquipada.", ".$estudio.", ".$cuartoServicio.", ".$cuartoTV.", ".$bodega.", ".$terraza.", ".$jardin.", ".$areaJuegosInfantiles.", ".$comedor.", ".$serviciosBasicos.", ".$gas.", ".$lineaTelefonica.", ".$internetDisponible.", ".$aireAcondicionado.", ".$calefaccion.", ".$cuotaMantenimiento.", ".$casetaVigilancia.", ".$elevador.", ".$seguridad.", ".$alberca.", ".$casaClub.", ".$canchaTenis.", ".$vistaMar.", ".$jacuzzi.", ".$estacionamientoVisitas.", ".$permiteMascotas.", ".$gimnasio.", ".$centrosComercialesCercanos.", ".$escuelasCercanas.", ".$fumadoresPermitidos.", ".$numeroOficinas.", ".$wcs.", ".$recamaras.", NOW());";
		crearConsulta($consulta);
		$id_creado = mysql_insert_id();
		
		
		//crea la transaccion para el inmueble
		$consulta = "INSERT INTO TRANSACCION_INMUEBLE(TRI_TRANSACCION, TRI_INMUEBLE) VALUES(".$transaccion.", ".$id_creado.");";
		crearConsulta($consulta);
		
		
		//sube una o varias imagenes para el inmueble
		if ($imagen != "") {
			$imagenes = explode(",", $imagen);
			$imagenPrincipal = explode(",", $imagenPrincipal);
			$urlArchivosTemp = $urlArchivos."temp/";
			
			for ($x = 0; $x < count($imagenes); $x++) {
				rename($urlArchivosTemp.$imagenes[$x], $urlArchivos.$imagenes[$x]);
				
				$consulta = "INSERT INTO IMAGEN_INMUEBLE(IIN_INMUEBLE, IIN_IMAGEN, IIN_ORDEN) VALUES(".$id_creado.", '".$imagenes[$x]."', ".$imagenPrincipal[$x].");";
				crearConsulta($consulta);
			}
		}
		
		
		//envia correos al hacer publicacion de anunciones de acuerdo a las siguiente condiciones
		if (($transaccion == 2) && ($precio <= 500000)) {//venta
			$to = "javier@explorainmuebles.com";
			$cadena = "Se ha publicado un anuncio de venta menor a $500,000<br /><a href='http://www.explorainmuebles.com/inmueble.php?id=".$id_creado."'>Ver anuncio</a>";
			
			
			$subject = "Explora Inmuebles - Venta Anuncio Menor";
			$message = $cadena;
			$header = "From: contacto@explorainmuebles.com";
			mail($to, $subject, $message, $header);
		}
		if (($transaccion == 1) && ($precio <= 2500)) {//renta
			$to = "javier@explorainmuebles.com";
			$cadena = "Se ha publicado un anuncio de renta menor a $2,500<br /><a href='http://www.explorainmuebles.com/inmueble.php?id=".$id_creado."'>Ver anuncio</a>";
			
			
			$subject = "Explora Inmuebles - Venta Anuncio Menor";
			$message = $cadena;
			$header = "From: contacto@explorainmuebles.com";
			mail($to, $subject, $message, $header);
		}
	}
	
	
	if ($modificar) {
		$titulo = $_POST["titulo"];
		$categoria = $_POST["categoria"];
		$tipo = $_POST["tipo"];
		$precio = $_POST["precio"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"];
		$ciudad = $_POST["ciudad"];
		$colonia = $_POST["colonia"];
		$cp = $_POST["cp"];
		$latitud = $_POST["latitud"];
		$longitud = $_POST["longitud"];
		$descripcion = htmlspecialchars($_POST["descripcion"]);
		$antiguedad = $_POST["antiguedad"] != "" ? $_POST["antiguedad"] : "null";
		$codigo = $_POST["codigo"] != "" ? "'".$_POST["codigo"]."'" : "null";
		$dimensionTotal = $_POST["dimensionTotal"] != "" ? $_POST["dimensionTotal"] : "null";
		$dimensionConstruida = $_POST["dimensionConstruida"] != "" ? $_POST["dimensionConstruida"] : "null";
		$estadoConservacion = $_POST["estadoConservacion"];
		$amueblado = $_POST["amueblado"];
		$cocinaEquipada = isset($_POST["cocinaEquipada"]) ? 1 : 0;
		$estudio = isset($_POST["estudio"]) ? 1 : 0;
		$cuartoServicio = isset($_POST["cuartoServicio"]) ? 1 : 0;
		$cuartoTV = isset($_POST["cuartoTV"]) ? 1 : 0;
		$bodega = isset($_POST["bodega"]) ? 1 : 0;
		$terraza = isset($_POST["terraza"]) ? 1 : 0;
		$jardin = isset($_POST["jardin"]) ? 1 : 0;
		$areaJuegosInfantiles = isset($_POST["areaJuegosInfantiles"]) ? 1 : 0;
		$comedor = isset($_POST["comedor"]) ? 1 : 0;
		$serviciosBasicos = isset($_POST["serviciosBasicos"]) ? 1 : 0;
		$gas = isset($_POST["gas"]) ? 1 : 0;
		$lineaTelefonica = isset($_POST["lineaTelefonica"]) ? 1 : 0;
		$internetDisponible = isset($_POST["internetDisponible"]) ? 1 : 0;
		$aireAcondicionado = isset($_POST["aireAcondicionado"]) ? 1 : 0;
		$calefaccion = isset($_POST["calefaccion"]) ? 1 : 0;
		$cuotaMantenimiento = $_POST["cuotaMantenimiento"] != "" ? $_POST["cuotaMantenimiento"] : "null";
		$casetaVigilancia = isset($_POST["casetaVigilancia"]) ? 1 : 0;
		$elevador = $_POST["elevador"] != "" ? $_POST["elevador"] : "null";
		$seguridad = isset($_POST["seguridad"]) ? 1 : 0;
		$alberca = isset($_POST["alberca"]) ? 1 : 0;
		$casaClub = isset($_POST["casaClub"]) ? 1 : 0;
		$canchaTenis = isset($_POST["canchaTenis"]) ? 1 : 0;
		$vistaMar = isset($_POST["vistaMar"]) ? 1 : 0;
		$jacuzzi = isset($_POST["jacuzzi"]) ? 1 : 0;
		$estacionamientoVisitas = $_POST["estacionamientoVisitas"] != "" ? $_POST["estacionamientoVisitas"] : "null";
		$permiteMascotas = isset($_POST["permiteMascotas"]) ? 1 : 0;
		$gimnasio = isset($_POST["gimnasio"]) ? 1 : 0;
		$centrosComercialesCercanos = isset($_POST["centrosComercialesCercanos"]) ? 1 : 0;
		$escuelasCercanas = isset($_POST["escuelasCercanas"]) ? 1 : 0;
		$fumadoresPermitidos = isset($_POST["fumadoresPermitidos"]) ? 1 : 0;
		$numeroOficinas = $_POST["numeroOficinas"] != "" ? $_POST["numeroOficinas"] : "null";
		$wcs = $_POST["wcs"] != -1 ? $_POST["wcs"] : "null";
		$recamaras = $_POST["recamaras"] != -1 ? $_POST["recamaras"] : "null";
		
		$transaccion = $_POST["transaccion"];
		$imagen = $_POST["imagen"];
		$imagenPrincipal = $_POST["imagenPrincipal"];
		$idImagenPrincipal = $_POST["idImagenPrincipal"];
		
		
		
		$consulta = "UPDATE INMUEBLE SET IMU_TITULO = '".$titulo."', IMU_CATEGORIA_INMUEBLE = ".$categoria.", IMU_TIPO_INMUEBLE = ".$tipo.", IMU_PRECIO = ".$precio.", IMU_CALLE_NUMERO = '".$calleNumero."', IMU_ESTADO = ".$estado.", IMU_CIUDAD = ".$ciudad.", IMU_COLONIA = ".$colonia.", IMU_CP = ".$cp.", IMU_LATITUD = '".$latitud."', IMU_LONGITUD = '".$longitud."', IMU_DESCRIPCION = '".$descripcion."', IMU_ANTIGUEDAD = ".$antiguedad.", IMU_CODIGO = ".$codigo.", IMU_DIMENSION_TOTAL = ".$dimensionTotal.", IMU_DIMENSION_CONSTRUIDA = ".$dimensionConstruida.", IMU_ESTADO_CONSERVACION = ".$estadoConservacion.", IMU_AMUEBLADO = ".$amueblado.", IMU_COCINA_EQUIPADA = ".$cocinaEquipada.", IMU_ESTUDIO = ".$estudio.", IMU_CUARTO_SERVICIO = ".$cuartoServicio.", IMU_CUARTO_TV = ".$cuartoTV.", IMU_BODEGA = ".$bodega.", IMU_TERRAZA = ".$terraza.", IMU_JARDIN = ".$jardin.", IMU_AREA_JUEGOS_INFANTILES = ".$areaJuegosInfantiles.", IMU_COMEDOR = ".$comedor.", IMU_SERVICIOS_BASICOS = ".$serviciosBasicos.", IMU_GAS = ".$gas.", IMU_LINEA_TELEFONICA = ".$lineaTelefonica.", IMU_INTERNET_DISPONIBLE = ".$internetDisponible.", IMU_AIRE_ACONDICIONADO = ".$aireAcondicionado.", IMU_CALEFACCION = ".$calefaccion.", IMU_CUOTA_MANTENIMIENTO = ".$cuotaMantenimiento.", IMU_CASETA_VIGILANCIA = ".$casetaVigilancia.", IMU_ELEVADOR = ".$elevador.", IMU_SEGURIDAD = ".$seguridad.", IMU_ALBERCA = ".$alberca.", IMU_CASA_CLUB = ".$casaClub.", IMU_CANCHA_TENIS = ".$canchaTenis.", IMU_VISTA_MAR = ".$vistaMar.", IMU_JACUZZI = ".$jacuzzi.", IMU_ESTACIONAMIENTO_VISITAS = ".$estacionamientoVisitas.", IMU_PERMITE_MASCOTAS = ".$permiteMascotas.", IMU_GIMNASIO = ".$gimnasio.", IMU_CENTROS_COMERCIALES_CERCANOS = ".$centrosComercialesCercanos.", IMU_ESCUELAS_CERCANAS = ".$escuelasCercanas.", IMU_FUMADORES_PERMITIDOS = ".$fumadoresPermitidos.", IMU_NUMERO_OFICINAS = ".$numeroOficinas.", IMU_WCS = ".$wcs.", IMU_RECAMARAS = ".$recamaras." WHERE IMU_ID = ".$id.";";
		crearConsulta($consulta);
		
		
		//modifica la transaccion para el inmueble
		$consulta = "DELETE FROM TRANSACCION_INMUEBLE WHERE TRI_INMUEBLE = ".$id.";";
		crearConsulta($consulta);
		$consulta = "INSERT INTO TRANSACCION_INMUEBLE(TRI_TRANSACCION, TRI_INMUEBLE) VALUES(".$transaccion.", ".$id.");";
		crearConsulta($consulta);
		
		
		//deja en limpio la imagen principal (luego se seleccionara por id o por una de las nuevas)
		$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_ORDEN = 0 WHERE IIN_INMUEBLE = ".$id.";";
		crearConsulta($consulta);
		
		
		//cambio de imagen principal por una de las imagenes que ya estan arriba
		if ($idImagenPrincipal != "") {
			$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_ORDEN = 1 WHERE IIN_ID = ".$idImagenPrincipal.";";
			crearConsulta($consulta);
		}
		
		
		//sube una o varias imagenes para el inmueble
		if ($imagen != "") {
			$imagenes = explode(",", $imagen);
			$imagenPrincipal = explode(",", $imagenPrincipal);
			$urlArchivosTemp = $urlArchivos."temp/";
			
			for ($x = 0; $x < count($imagenes); $x++) {
				rename($urlArchivosTemp.$imagenes[$x], $urlArchivos.$imagenes[$x]);
				
				$consulta = "INSERT INTO IMAGEN_INMUEBLE(IIN_INMUEBLE, IIN_IMAGEN, IIN_ORDEN) VALUES(".$id.", '".$imagenes[$x]."', ".$imagenPrincipal[$x].");";
				crearConsulta($consulta);
			}
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>