<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	$validarCodigo = isset($_POST["validarCodigo"]) ? 1 : 0;
	$urlArchivos = "../../images/images/";
	
	
	/*
		Valida el codigo sea unico entre los usuarios para el inmueble
	*/
	if ($validarCodigo) {
		$usuario = $_POST["usuario"];
		$codigo = $_POST["codigo"];

		
		$consulta = "SELECT IMU_ID FROM INMUEBLE WHERE IMU_USUARIO = :usuario AND IMU_CODIGO = :codigo;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":usuario" => $usuario, ":codigo" => $codigo));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {//si existe
			$row = $res[0];
			
			if ($row["IMU_ID"] != $id) {//pertenece a otra usuario
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
	
	
	/*
		borrar inmueble
	*/
	if($borrar){
		$consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			unlink($urlArchivos.$row["IIN_IMAGEN"]);
		}
		
		$consulta = "DELETE FROM INMUEBLE WHERE IMU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
	}
	
	
	/*
		Nuevo inmueble
	*/
	if ($nuevo) {
		$titulo = $_POST["titulo"];
		$usuario = $_POST["usuario"];
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
		$descripcion = $_POST["descripcion"];
		$antiguedad = $_POST["antiguedad"] != "" ? $_POST["antiguedad"] : NULL;
		$codigo = $_POST["codigo"] != "" ? $_POST["codigo"] : NULL;
		$dimensionTotal = $_POST["dimensionTotal"] != "" ? $_POST["dimensionTotal"] : NULL;
		$dimensionConstruida = $_POST["dimensionConstruida"] != "" ? $_POST["dimensionConstruida"] : NULL;
		$estadoConservacion = $_POST["estadoConservacion"] != "" ? $_POST["estadoConservacion"] : NULL;
		$amueblado = -1;
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
		$cuotaMantenimiento = $_POST["cuotaMantenimiento"] != "" ? $_POST["cuotaMantenimiento"] : NULL;
		$casetaVigilancia = isset($_POST["casetaVigilancia"]) ? 1 : 0;
		$elevador = $_POST["elevador"] != "" ? $_POST["elevador"] : NULL;
		$seguridad = isset($_POST["seguridad"]) ? 1 : 0;
		$alberca = isset($_POST["alberca"]) ? 1 : 0;
		$casaClub = isset($_POST["casaClub"]) ? 1 : 0;
		$canchaTenis = isset($_POST["canchaTenis"]) ? 1 : 0;
		$vistaMar = isset($_POST["vistaMar"]) ? 1 : 0;
		$jacuzzi = isset($_POST["jacuzzi"]) ? 1 : 0;
		$estacionamientoVisitas = $_POST["estacionamientoVisitas"] != "" ? $_POST["estacionamientoVisitas"] : NULL;
		$permiteMascotas = isset($_POST["permiteMascotas"]) ? 1 : 0;
		$gimnasio = isset($_POST["gimnasio"]) ? 1 : 0;
		$centrosComercialesCercanos = isset($_POST["centrosComercialesCercanos"]) ? 1 : 0;
		$escuelasCercanas = isset($_POST["escuelasCercanas"]) ? 1 : 0;
		$iglesiasCercanas = isset($_POST["iglesiasCercanas"]) ? 1 : 0;		
		$hospitalesCercanos = isset($_POST["hospitalesCercanos"]) ? 1 : 0;				
		$amueblado2 = isset($_POST["amueblado2"]) ? 1 : 0;
		$semiAmueblado = isset($_POST["semiAmueblado"]) ? 1 : 0;
		$zonaIndustrial = isset($_POST["zonaIndustrial"]) ? 1 : 0;
		$zonaTuristica = isset($_POST["zonaTuristica"]) ? 1 : 0;
		$zonaComercial = isset($_POST["zonaComercial"]) ? 1 : 0;
		$zonaResidencial = isset($_POST["zonaResidencial"]) ? 1 : 0;
		$baresCercanos = isset($_POST["baresCercanos"]) ? 1 : 0;
		$supermercadosCercanos = isset($_POST["supermercadosCercanos"]) ? 1 : 0;
		$excelenteUbicacion = isset($_POST["excelenteUbicacion"]) ? 1 : 0;
		$cisterna = isset($_POST["cisterna"]) ? 1 : 0;
		$calentador = isset($_POST["calentador"]) ? 1 : 0;
		$camaras = isset($_POST["camaras"]) ? 1 : 0;
		$anden = isset($_POST["anden"]) ? 1 : 0;
		$asador = isset($_POST["asador"]) ? 1 : 0;
		$vapor = isset($_POST["vapor"]) ? 1 : 0;
		$sauna = isset($_POST["sauna"]) ? 1 : 0;
		$playa = isset($_POST["playa"]) ? 1 : 0;
		$clubPlaya = isset($_POST["clubPlaya"]) ? 1 : 0;
		$portonElectrico = isset($_POST["portonElectrico"]) ? 1 : 0;
		$chimenea = isset($_POST["chimenea"]) ? 1 : 0;
		$areasVerdes = isset($_POST["areasVerdes"]) ? 1 : 0;
		$vistaPanoramica = isset($_POST["vistaPanoramica"]) ? 1 : 0;
		$canchaSquash = isset($_POST["canchaSquash"]) ? 1 : 0;
		$canchaBasket = isset($_POST["canchaBasket"]) ? 1 : 0;
		$salaCine = isset($_POST["salaCine"]) ? 1 : 0;
		$canchaFut = isset($_POST["canchaFut"]) ? 1 : 0;
		$familyRoom = isset($_POST["familyRoom"]) ? 1 : 0;
		$campoGolf = isset($_POST["campoGolf"]) ? 1 : 0;
		$cableTV = isset($_POST["cableTV"]) ? 1 : 0;
		$biblioteca = isset($_POST["biblioteca"]) ? 1 : 0;
		$usosMultiples = isset($_POST["usosMultiples"]) ? 1 : 0;
		$sala = isset($_POST["sala"]) ? 1 : 0;
		$recibidor = isset($_POST["recibidor"]) ? 1 : 0;
		$vestidor = isset($_POST["vestidor"]) ? 1 : 0;
		$oratorio = isset($_POST["oratorio"]) ? 1 : 0;
		$cava = isset($_POST["cava"]) ? 1 : 0;
		$patio = isset($_POST["patio"]) ? 1 : 0;
		$balcon = isset($_POST["balcon"]) ? 1 : 0;
		$lobby = isset($_POST["lobby"]) ? 1 : 0;
		$fumadoresPermitidos = isset($_POST["fumadoresPermitidos"]) ? 1 : 0;
		$numeroOficinas = $_POST["numeroOficinas"] != "" ? $_POST["numeroOficinas"] : NULL;
		$wcs = $_POST["wcs"] != -1 ? $_POST["wcs"] : NULL;
		$recamaras = $_POST["recamaras"] != -1 ? $_POST["recamaras"] : NULL;
		$metrosFrente = $_POST["metrosFrente"] != "" ? $_POST["metrosFrente"] : NULL;
		$metrosFondo = $_POST["metrosFondo"] != "" ? $_POST["metrosFondo"] : NULL;
		$cajonesEstacionamiento = $_POST["cajonesEstacionamiento"] != "" ? $_POST["cajonesEstacionamiento"] : NULL;
		$limiteVigencia = "2000-01-01";//date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+30, date("Y")));
		$desarrollo = $_POST["desarrollo"] != -1 ? $_POST["desarrollo"] : NULL;
		
		$transaccion = $_POST["transaccion"];
		$imagen = $_POST["imagen"];
		$imagenPrincipal = $_POST["imagenPrincipal"];
		$videos = $_POST["videos"];
		$destacado = (isset($_POST["propiedadDestacada"]) && $_POST["propiedadDestacada"] == 'on' )?1:0;
		
		
		$consulta = "INSERT INTO INMUEBLE(IMU_TITULO, IMU_USUARIO, IMU_CATEGORIA_INMUEBLE, IMU_TIPO_INMUEBLE, IMU_PRECIO, IMU_CALLE_NUMERO, IMU_ESTADO, IMU_CIUDAD, IMU_COLONIA, IMU_CP, IMU_LATITUD, IMU_LONGITUD, IMU_DESCRIPCION, IMU_ANTIGUEDAD, IMU_CODIGO, IMU_DIMENSION_TOTAL, IMU_DIMENSION_CONSTRUIDA, IMU_ESTADO_CONSERVACION, IMU_AMUEBLADO, IMU_COCINA_EQUIPADA, IMU_ESTUDIO, IMU_CUARTO_SERVICIO, IMU_CUARTO_TV, IMU_BODEGA, IMU_TERRAZA, IMU_JARDIN, IMU_AREA_JUEGOS_INFANTILES, IMU_COMEDOR, IMU_SERVICIOS_BASICOS, IMU_GAS, IMU_LINEA_TELEFONICA, IMU_INTERNET_DISPONIBLE, IMU_AIRE_ACONDICIONADO, IMU_CALEFACCION, IMU_CUOTA_MANTENIMIENTO, IMU_CASETA_VIGILANCIA, IMU_ELEVADOR, IMU_SEGURIDAD, IMU_ALBERCA, IMU_CASA_CLUB, IMU_CANCHA_TENIS, IMU_VISTA_MAR, IMU_JACUZZI, IMU_ESTACIONAMIENTO_VISITAS, IMU_PERMITE_MASCOTAS, IMU_GIMNASIO, IMU_CENTROS_COMERCIALES_CERCANOS, IMU_IGLESIAS_CERCANAS, IMU_HOSPITALES_CERCANOS, IMU_ESCUELAS_CERCANAS, IMU_AMUEBLADO2, IMU_SEMIAMUEBLADO, IMU_ZONA_INDUSTRIAL, IMU_ZONA_TURISTICA, IMU_ZONA_COMERCIAL, IMU_ZONA_RESIDENCIAL, IMU_BARES_CERCANOS, IMU_SUPERMERCADOS_CERCANOS, IMU_EXCELENTE_UBICACION, IMU_CISTERNA, IMU_CALENTADOR, IMU_CAMARAS, IMU_ANDEN, IMU_ASADOR, IMU_VAPOR, IMU_SAUNA, IMU_PLAYA, IMU_CLUB_PLAYA, IMU_PORTON_ELECTRICO, IMU_CHIMENEA, IMU_AREAS_VERDES, IMU_VISTA_PANORAMICA, IMU_CANCHA_SQUASH, IMU_CANCHA_BASKET, IMU_SALA_CINE, IMU_CANCHA_FUT, IMU_FAMILY_ROOM, IMU_CAMPO_GOLF, IMU_CABLETV, IMU_BIBLIOTECA, IMU_USOS_MULTIPLES, IMU_SALA,IMU_RECIBIDOR, IMU_VESTIDOR, IMU_ORATORIO, IMU_CAVA, IMU_PATIO, IMU_BALCON, IMU_LOBBY, IMU_FUMADORES_PERMITIDOS, IMU_NUMERO_OFICINAS, IMU_WCS, IMU_RECAMARAS, IMU_CREATE, IMU_METROS_FRENTE, IMU_METROS_FONDO, IMU_CAJONES_ESTACIONAMIENTO, IMU_DESARROLLO, IMU_LIMITE_VIGENCIA, IMU_DESTACADO) VALUES(:titulo, :usuario, :categoria, :tipo, :precio, :calleNumero, :estado, :ciudad, :colonia, :cp, :latitud, :longitud, :descripcion, :antiguedad, :codigo, :dimensionTotal, :dimensionConstruida, :estadoConservacion, :amueblado, :cocinaEquipada, :estudio, :cuartoServicio, :cuartoTV, :bodega, :terraza, :jardin, :areaJuegosInfantiles, :comedor, :serviciosBasicos, :gas, :lineaTelefonica, :internetDisponible, :aireAcondicionado, :calefaccion, :cuotaMantenimiento, :casetaVigilancia, :elevador, :seguridad, :alberca, :casaClub, :canchaTenis, :vistaMar, :jacuzzi, :estacionamientoVisitas, :permiteMascotas, :gimnasio, :centrosComercialesCercanos, :iglesiasCercanas, :hospitalesCercanos, :escuelasCercanas, :amueblado2, :semiAmueblado, :zonaIndustrial, :zonaTuristica, :zonaComercial, :zonaResidencial, :baresCercanos, :supermercadosCercanos, :excelenteUbicacion, :cisterna, :calentador, :camaras, :anden, :asador, :vapor, :sauna, :playa, :clubPlaya, :portonElectrico, :chimenea, :areasVerdes, :vistaPanoramica, :canchaSquash, :canchaBasket, :salaCine, :canchaFut, :familyRoom, :campoGolf, :cableTV, :biblioteca, :usosMultiples, :sala, :recibidor, :vestidor, :oratorio, :cava, :patio, :balcon, :lobby, :fumadoresPermitidos, :numeroOficinas, :wcs, :recamaras, NOW(), :metrosFrente, :metrosFondo, :cajonesEstacionamiento, :desarrollo, :limiteVigencia, :destacado);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":titulo" => $titulo, ":usuario" => $usuario, ":categoria" => $categoria, ":tipo" => $tipo, ":precio" => $precio, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":latitud" => $latitud, ":longitud" => $longitud, ":descripcion" => $descripcion, ":antiguedad" => $antiguedad, ":codigo" => $codigo, ":dimensionTotal" => $dimensionTotal, ":dimensionConstruida" => $dimensionConstruida, ":estadoConservacion" => $estadoConservacion, ":amueblado" => $amueblado, ":cocinaEquipada" => $cocinaEquipada, ":estudio" => $estudio, ":cuartoServicio" => $cuartoServicio, ":cuartoTV" => $cuartoTV, ":bodega" => $bodega, ":terraza" => $terraza, ":jardin" => $jardin, ":areaJuegosInfantiles" => $areaJuegosInfantiles, ":comedor" => $comedor, ":serviciosBasicos" => $serviciosBasicos, ":gas" => $gas, ":lineaTelefonica" => $lineaTelefonica, ":internetDisponible" => $internetDisponible, ":aireAcondicionado" => $aireAcondicionado, ":calefaccion" => $calefaccion, ":cuotaMantenimiento" => $cuotaMantenimiento, ":casetaVigilancia" => $casetaVigilancia, ":elevador" => $elevador, ":seguridad" => $seguridad, ":alberca" => $alberca, ":casaClub" => $casaClub, ":canchaTenis" => $canchaTenis, ":vistaMar" => $vistaMar, ":jacuzzi" => $jacuzzi, ":estacionamientoVisitas" => $estacionamientoVisitas, ":permiteMascotas" => $permiteMascotas, ":gimnasio" => $gimnasio, ":centrosComercialesCercanos" => $centrosComercialesCercanos, ":iglesiasCercanas" => $iglesiasCercanas, ":hospitalesCercanos" => $hospitalesCercanos, ":escuelasCercanas" => $escuelasCercanas, ":amueblado2" => $amueblado2, ":semiAmueblado" => $semiAmueblado, ":zonaIndustrial" => $zonaIndustrial, ":zonaTuristica" => $zonaTuristica, ":zonaComercial" => $zonaComercial, ":zonaResidencial" => $zonaResidencial, ":baresCercanos" => $baresCercanos, ":supermercadosCercanos" => $supermercadosCercanos, ":excelenteUbicacion" => $excelenteUbicacion, ":cisterna" => $cisterna, ":calentador" => $calentador, ":camaras" => $camaras, ":anden" => $anden, ":asador" => $asador, ":vapor" => $vapor, ":sauna" => $sauna, ":playa" => $playa, ":clubPlaya" => $clubPlaya, ":portonElectrico" => $portonElectrico, ":chimenea" => $chimenea, ":areasVerdes" => $areasVerdes, ":vistaPanoramica" => $vistaPanoramica, ":canchaSquash" => $canchaSquash, ":canchaBasket" => $canchaBasket, ":salaCine" => $salaCine, ":canchaFut" => $canchaFut, ":familyRoom" => $familyRoom, ":campoGolf" => $campoGolf, ":cableTV" => $cableTV, ":biblioteca" => $biblioteca, ":usosMultiples" => $usosMultiples, ":sala" => $sala, ":recibidor" => $recibidor, ":vestidor" => $vestidor, ":oratorio" => $oratorio, ":cava" => $cava, ":patio" => $patio, ":balcon" => $balcon, ":lobby" => $lobby, ":fumadoresPermitidos" => $fumadoresPermitidos, ":numeroOficinas" => $numeroOficinas, ":wcs" => $wcs, ":recamaras" => $recamaras, ":metrosFrente" => $metrosFrente, ":metrosFondo" => $metrosFondo, ":cajonesEstacionamiento" => $cajonesEstacionamiento, ":desarrollo" => $desarrollo, ":limiteVigencia" => $limiteVigencia, ":destacadp" => $destacado));
		$id_creado = $conexion->lastInsertId();
		
		
		//crea la transaccion para el inmueble
		$consulta = "INSERT INTO TRANSACCION_INMUEBLE(TRI_TRANSACCION, TRI_INMUEBLE) VALUES(:transaccion, :idInmueble);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":transaccion" => $transaccion, ":idInmueble" => $id_creado));
		
		
		//sube una o varias imagenes para el inmueble
		if ($imagen != "") {
			$imagenes = explode(",", $imagen);
			$imagenPrincipal = explode(",", $imagenPrincipal);
			$urlArchivosTemp = $urlArchivos."temp/";
			
			for ($x = 0; $x < count($imagenes); $x++) {
				rename($urlArchivosTemp.$imagenes[$x], $urlArchivos.$imagenes[$x]);
				
				$consulta = "INSERT INTO IMAGEN_INMUEBLE(IIN_INMUEBLE, IIN_IMAGEN, IIN_ORDEN) VALUES(:inmueble, :imagen, :orden);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":inmueble" => $id_creado, ":imagen" => $imagenes[$x], ":orden" => $imagenPrincipal[$x]));
			}
		}
		
		
		//sube uno o varios videos para el inmueble
		if ($videos != "") {
			$urlVideos = explode(",", $videos);
			
			for ($x = 0; $x < count($urlVideos); $x++) {
				$consulta = "INSERT INTO VIDEO_INMUEBLE(VIN_INMUEBLE, VIN_VIDEO) VALUES(:inmueble, :video);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":inmueble" => $id_creado, ":video" => $urlVideos[$x]));
			}
		}
		
		
		//Si un usuario pertenece a una inmobliaria, y esta aun tiene creditos disponibles, marca como vigente el inmueble
		$consulta = "SELECT USU_INMOBILIARIA FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($usuario));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		
		if ($row["USU_INMOBILIARIA"] != NULL) {
			$arrayInmobiliaria = array(
				"id"	=>	$row["USU_INMOBILIARIA"]
			);
			
			
			$consulta = "SELECT INM_VALIDEZ, INM_CREDITOS FROM INMOBILIARIA WHERE INM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($arrayInmobiliaria["id"]));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$arrayInmobiliaria["validez"] = $row["INM_VALIDEZ"];
			$arrayInmobiliaria["creditos"] = $row["INM_CREDITOS"];
			
			$partes = explode("-", $arrayInmobiliaria["validez"]);
			$timeStamp_inmobiliaria = mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
			$timeStamp_fechaHoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			
			
			//verifica si aun exta vigente
			if ($timeStamp_inmobiliaria >= $timeStamp_fechaHoy) {
				//consulta cuantos inmuebles tiene activos
				$consulta =
					"SELECT COUNT(IMU_ID) AS CONS_ACTIVOS
					FROM INMUEBLE, USUARIO
					WHERE IMU_USUARIO = USU_ID
					AND USU_INMOBILIARIA = ?
					AND IMU_LIMITE_VIGENCIA > CURDATE();";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array($arrayInmobiliaria["id"]));
				$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
				$row = $res[0];
				$arrayInmobiliaria["activos"] = $row["CONS_ACTIVOS"];
				
				
				//si tiene aun creditos lo marca como activo
				if ($arrayInmobiliaria["activos"] < $arrayInmobiliaria["creditos"]) {
					$consulta = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
					$pdo = $conexion->prepare($consulta);
					$pdo->execute(array(":limiteVigencia" => date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+30, date("Y"))), ":id" => $id_creado));
				}
			}
		}
	}
	
	
	/*
		Modifica inmueble
	*/
	if ($modificar) {
		$titulo = $_POST["titulo"];
		$usuario = $_POST["usuario"];
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
		$descripcion = $_POST["descripcion"];
		$antiguedad = $_POST["antiguedad"] != "" ? $_POST["antiguedad"] : NULL;
		$codigo = $_POST["codigo"] != "" ? $_POST["codigo"] : NULL;
		$dimensionTotal = $_POST["dimensionTotal"] != "" ? $_POST["dimensionTotal"] : NULL;
		$dimensionConstruida = $_POST["dimensionConstruida"] != "" ? $_POST["dimensionConstruida"] : NULL;
		$estadoConservacion = $_POST["estadoConservacion"] != "" ? $_POST["estadoConservacion"] : NULL;
		$amueblado = -1;
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
		$cuotaMantenimiento = $_POST["cuotaMantenimiento"] != "" ? $_POST["cuotaMantenimiento"] : NULL;
		$casetaVigilancia = isset($_POST["casetaVigilancia"]) ? 1 : 0;
		$elevador = $_POST["elevador"] != "" ? $_POST["elevador"] : NULL;
		$seguridad = isset($_POST["seguridad"]) ? 1 : 0;
		$alberca = isset($_POST["alberca"]) ? 1 : 0;
		$casaClub = isset($_POST["casaClub"]) ? 1 : 0;
		$canchaTenis = isset($_POST["canchaTenis"]) ? 1 : 0;
		$vistaMar = isset($_POST["vistaMar"]) ? 1 : 0;
		$jacuzzi = isset($_POST["jacuzzi"]) ? 1 : 0;
		$estacionamientoVisitas = $_POST["estacionamientoVisitas"] != "" ? $_POST["estacionamientoVisitas"] : NULL;
		$permiteMascotas = isset($_POST["permiteMascotas"]) ? 1 : 0;
		$gimnasio = isset($_POST["gimnasio"]) ? 1 : 0;
		$centrosComercialesCercanos = isset($_POST["centrosComercialesCercanos"]) ? 1 : 0;
		$escuelasCercanas = isset($_POST["escuelasCercanas"]) ? 1 : 0;
		$iglesiasCercanas = isset($_POST["iglesiasCercanas"]) ? 1 : 0;		
		$hospitalesCercanos = isset($_POST["hospitalesCercanos"]) ? 1 : 0;				
		$amueblado2 = isset($_POST["amueblado2"]) ? 1 : 0;
		$semiAmueblado = isset($_POST["semiAmueblado"]) ? 1 : 0;
		$zonaIndustrial = isset($_POST["zonaIndustrial"]) ? 1 : 0;
		$zonaTuristica = isset($_POST["zonaTuristica"]) ? 1 : 0;
		$zonaComercial = isset($_POST["zonaComercial"]) ? 1 : 0;
		$zonaResidencial = isset($_POST["zonaResidencial"]) ? 1 : 0;
		$baresCercanos = isset($_POST["baresCercanos"]) ? 1 : 0;
		$supermercadosCercanos = isset($_POST["supermercadosCercanos"]) ? 1 : 0;
		$excelenteUbicacion = isset($_POST["excelenteUbicacion"]) ? 1 : 0;
		$cisterna = isset($_POST["cisterna"]) ? 1 : 0;
		$calentador = isset($_POST["calentador"]) ? 1 : 0;
		$camaras = isset($_POST["camaras"]) ? 1 : 0;
		$anden = isset($_POST["anden"]) ? 1 : 0;
		$asador = isset($_POST["asador"]) ? 1 : 0;
		$vapor = isset($_POST["vapor"]) ? 1 : 0;
		$sauna = isset($_POST["sauna"]) ? 1 : 0;
		$playa = isset($_POST["playa"]) ? 1 : 0;
		$clubPlaya = isset($_POST["clubPlaya"]) ? 1 : 0;
		$portonElectrico = isset($_POST["portonElectrico"]) ? 1 : 0;
		$chimenea = isset($_POST["chimenea"]) ? 1 : 0;
		$areasVerdes = isset($_POST["areasVerdes"]) ? 1 : 0;
		$vistaPanoramica = isset($_POST["vistaPanoramica"]) ? 1 : 0;
		$canchaSquash = isset($_POST["canchaSquash"]) ? 1 : 0;
		$canchaBasket = isset($_POST["canchaBasket"]) ? 1 : 0;
		$salaCine = isset($_POST["salaCine"]) ? 1 : 0;
		$canchaFut = isset($_POST["canchaFut"]) ? 1 : 0;
		$familyRoom = isset($_POST["familyRoom"]) ? 1 : 0;
		$campoGolf = isset($_POST["campoGolf"]) ? 1 : 0;
		$cableTV = isset($_POST["cableTV"]) ? 1 : 0;
		$biblioteca = isset($_POST["biblioteca"]) ? 1 : 0;
		$usosMultiples = isset($_POST["usosMultiples"]) ? 1 : 0;
		$sala = isset($_POST["sala"]) ? 1 : 0;
		$recibidor = isset($_POST["recibidor"]) ? 1 : 0;
		$vestidor = isset($_POST["vestidor"]) ? 1 : 0;
		$oratorio = isset($_POST["oratorio"]) ? 1 : 0;
		$cava = isset($_POST["cava"]) ? 1 : 0;
		$patio = isset($_POST["patio"]) ? 1 : 0;
		$balcon = isset($_POST["balcon"]) ? 1 : 0;
		$lobby = isset($_POST["lobby"]) ? 1 : 0;		
		$fumadoresPermitidos = isset($_POST["fumadoresPermitidos"]) ? 1 : 0;
		$numeroOficinas = $_POST["numeroOficinas"] != "" ? $_POST["numeroOficinas"] : NULL;
		$wcs = $_POST["wcs"] != -1 ? $_POST["wcs"] : NULL;
		$recamaras = $_POST["recamaras"] != -1 ? $_POST["recamaras"] : NULL;
		$metrosFrente = $_POST["metrosFrente"] != "" ? $_POST["metrosFrente"] : NULL;
		$metrosFondo = $_POST["metrosFondo"] != "" ? $_POST["metrosFondo"] : NULL;
		$cajonesEstacionamiento = $_POST["cajonesEstacionamiento"] != "" ? $_POST["cajonesEstacionamiento"] : NULL;
		$desarrollo = $_POST["desarrollo"] != -1 ? $_POST["desarrollo"] : NULL;
		
		
		$transaccion = $_POST["transaccion"];
		$imagen = $_POST["imagen"];
		$imagenPrincipal = $_POST["imagenPrincipal"];
		$idImagenPrincipal = $_POST["idImagenPrincipal"];
		$videos = $_POST["videos"];
		$destacado = (isset($_POST["propiedadDestacada"]) && $_POST["propiedadDestacada"] == 'on' )?1:0;
		
		
		
		$consulta = "UPDATE INMUEBLE SET IMU_TITULO = :titulo, IMU_USUARIO = :usuario, IMU_CATEGORIA_INMUEBLE = :categoria, IMU_TIPO_INMUEBLE = :tipo, IMU_PRECIO = :precio, IMU_CALLE_NUMERO = :calleNumero, IMU_ESTADO = :estado, IMU_CIUDAD = :ciudad, IMU_COLONIA = :colonia, IMU_CP = :cp, IMU_LATITUD = :latitud, IMU_LONGITUD = :longitud, IMU_DESCRIPCION = :descripcion, IMU_ANTIGUEDAD = :antiguedad, IMU_CODIGO = :codigo, IMU_DIMENSION_TOTAL = :dimensionTotal, IMU_DIMENSION_CONSTRUIDA = :dimensionConstruida, IMU_ESTADO_CONSERVACION = :estadoConservacion, IMU_AMUEBLADO = :amueblado, IMU_COCINA_EQUIPADA = :cocinaEquipada, IMU_ESTUDIO = :estudio, IMU_CUARTO_SERVICIO = :cuartoServicio, IMU_CUARTO_TV = :cuartoTV, IMU_BODEGA = :bodega, IMU_TERRAZA = :terraza, IMU_JARDIN = :jardin, IMU_AREA_JUEGOS_INFANTILES = :areaJuegosInfantiles, IMU_COMEDOR = :comedor, IMU_SERVICIOS_BASICOS = :serviciosBasicos, IMU_GAS = :gas, IMU_LINEA_TELEFONICA = :lineaTelefonica, IMU_INTERNET_DISPONIBLE = :internetDisponible, IMU_AIRE_ACONDICIONADO = :aireAcondicionado, IMU_CALEFACCION = :calefaccion, IMU_CUOTA_MANTENIMIENTO = :cuotaMantenimiento, IMU_CASETA_VIGILANCIA = :casetaVigilancia, IMU_ELEVADOR = :elevador, IMU_SEGURIDAD = :seguridad, IMU_ALBERCA = :alberca, IMU_CASA_CLUB = :casaClub, IMU_CANCHA_TENIS = :canchaTenis, IMU_VISTA_MAR = :vistaMar, IMU_JACUZZI = :jacuzzi, IMU_ESTACIONAMIENTO_VISITAS = :estacionamientoVisitas, IMU_PERMITE_MASCOTAS = :permiteMascotas, IMU_GIMNASIO = :gimnasio, IMU_CENTROS_COMERCIALES_CERCANOS = :centrosComercialesCercanos, IMU_ESCUELAS_CERCANAS = :escuelasCercanas, IMU_HOSPITALES_CERCANOS = :hospitalesCercanos, IMU_IGLESIAS_CERCANAS = :iglesiasCercanas, IMU_AMUEBLADO2 = :amueblado2, IMU_SEMIAMUEBLADO = :semiAmueblado, IMU_ZONA_INDUSTRIAL = :zonaIndustrial, IMU_ZONA_TURISTICA = :zonaTuristica, IMU_ZONA_COMERCIAL = :zonaComercial, IMU_ZONA_RESIDENCIAL = :zonaResidencial, IMU_BARES_CERCANOS = :baresCercanos, IMU_SUPERMERCADOS_CERCANOS = :supermercadosCercanos, IMU_EXCELENTE_UBICACION = :excelenteUbicacion, IMU_CISTERNA = :cisterna, IMU_CALENTADOR = :calentador, IMU_CAMARAS = :camaras, IMU_ANDEN = :anden, IMU_ASADOR = :asador, IMU_VAPOR = :vapor, IMU_SAUNA = :sauna, IMU_PLAYA = :playa, IMU_CLUB_PLAYA = :clubPlaya, IMU_PORTON_ELECTRICO = :portonElectrico, IMU_CHIMENEA = :chimenea, IMU_AREAS_VERDES = :areasVerdes, IMU_VISTA_PANORAMICA = :vistaPanoramica, IMU_CANCHA_SQUASH = :canchaSquash, IMU_CANCHA_BASKET = :canchaBasket, IMU_SALA_CINE = :salaCine, IMU_CANCHA_FUT = :canchaFut, IMU_FAMILY_ROOM = :familyRoom, IMU_CAMPO_GOLF = :campoGolf, IMU_CABLETV = :cableTV, IMU_BIBLIOTECA = :biblioteca, IMU_USOS_MULTIPLES = :usosMultiples, IMU_SALA = :sala, IMU_RECIBIDOR = :recibidor, IMU_VESTIDOR = :vestidor, IMU_ORATORIO = :oratorio, IMU_CAVA = :cava, IMU_PATIO = :patio, IMU_BALCON = :balcon, IMU_LOBBY = :lobby, IMU_FUMADORES_PERMITIDOS = :fumadoresPermitidos, IMU_NUMERO_OFICINAS = :numeroOficinas, IMU_WCS = :wcs, IMU_RECAMARAS = :recamaras, IMU_METROS_FRENTE = :metrosFrente, IMU_METROS_FONDO = :metrosFondo, IMU_CAJONES_ESTACIONAMIENTO = :cajonesEstacionamiento, IMU_DESARROLLO = :desarrollo, IMU_DESTACADO = :destacado WHERE IMU_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":titulo" => $titulo, ":usuario" => $usuario, ":categoria" => $categoria, ":tipo" => $tipo, ":precio" => $precio, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":latitud" => $latitud, ":longitud" => $longitud, ":descripcion" => $descripcion, ":antiguedad" => $antiguedad, ":codigo" => $codigo, ":dimensionTotal" => $dimensionTotal, ":dimensionConstruida" => $dimensionConstruida, ":estadoConservacion" => $estadoConservacion, ":amueblado" => $amueblado, ":cocinaEquipada" => $cocinaEquipada, ":estudio" => $estudio, ":cuartoServicio" => $cuartoServicio, ":cuartoTV" => $cuartoTV, ":bodega" => $bodega, ":terraza" => $terraza, ":jardin" => $jardin, ":areaJuegosInfantiles" => $areaJuegosInfantiles, ":comedor" => $comedor, ":serviciosBasicos" => $serviciosBasicos, ":gas" => $gas, ":lineaTelefonica" => $lineaTelefonica, ":internetDisponible" => $internetDisponible, ":aireAcondicionado" => $aireAcondicionado, ":calefaccion" => $calefaccion, ":cuotaMantenimiento" => $cuotaMantenimiento, ":casetaVigilancia" => $casetaVigilancia, ":elevador" => $elevador, ":seguridad" => $seguridad, ":alberca" => $alberca, ":casaClub" => $casaClub, ":canchaTenis" => $canchaTenis, ":vistaMar" => $vistaMar, ":jacuzzi" => $jacuzzi, ":estacionamientoVisitas" => $estacionamientoVisitas, ":permiteMascotas" => $permiteMascotas, ":gimnasio" => $gimnasio, ":centrosComercialesCercanos" => $centrosComercialesCercanos, ":iglesiasCercanas" => $iglesiasCercanas, ":hospitalesCercanos" => $hospitalesCercanos, ":escuelasCercanas" => $escuelasCercanas, ":amueblado2" => $amueblado2, ":semiAmueblado" => $semiAmueblado, ":zonaIndustrial" => $zonaIndustrial, ":zonaTuristica" => $zonaTuristica, ":zonaComercial" => $zonaComercial, ":zonaResidencial" => $zonaResidencial, ":baresCercanos" => $baresCercanos, ":supermercadosCercanos" => $supermercadosCercanos, ":excelenteUbicacion" => $excelenteUbicacion, ":cisterna" => $cisterna, ":calentador" => $calentador, ":camaras" => $camaras, ":anden" => $anden, ":asador" => $asador, ":vapor" => $vapor, ":sauna" => $sauna, ":playa" => $playa, ":clubPlaya" => $clubPlaya, ":portonElectrico" => $portonElectrico, ":chimenea" => $chimenea, ":areasVerdes" => $areasVerdes, ":vistaPanoramica" => $vistaPanoramica, ":canchaSquash" => $canchaSquash, ":canchaBasket" => $canchaBasket, ":salaCine" => $salaCine, ":canchaFut" => $canchaFut, ":familyRoom" => $familyRoom, ":campoGolf" => $campoGolf, ":cableTV" => $cableTV, ":biblioteca" => $biblioteca, ":usosMultiples" => $usosMultiples, ":sala" => $sala, ":recibidor" => $recibidor, ":vestidor" => $vestidor, ":oratorio" => $oratorio, ":cava" => $cava, ":patio" => $patio, ":balcon" => $balcon, ":lobby" => $lobby, ":fumadoresPermitidos" => $fumadoresPermitidos, ":numeroOficinas" => $numeroOficinas, ":wcs" => $wcs, ":recamaras" => $recamaras, ":metrosFrente" => $metrosFrente, ":metrosFondo" => $metrosFondo, ":cajonesEstacionamiento" => $cajonesEstacionamiento, ":desarrollo" => $desarrollo, ":id" => $id, ":destacado" => $destacado));
		
		
		//modifica la transaccion para el inmueble
		$consulta = "DELETE FROM TRANSACCION_INMUEBLE WHERE TRI_INMUEBLE = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$consulta = "INSERT INTO TRANSACCION_INMUEBLE(TRI_TRANSACCION, TRI_INMUEBLE) VALUES(:transaccion, :id);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":transaccion" => $transaccion, ":id" => $id));
		
		
		//deja en limpio la imagen principal (luego se seleccionara por id o por una de las nuevas)
		$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_ORDEN = :orden WHERE IIN_INMUEBLE = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":orden" => 0, ":id" => $id));
		
		
		//cambio de imagen principal por una de las imagenes que ya estan arriba
		if ($idImagenPrincipal != "") {
			$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_ORDEN = :orden WHERE IIN_ID = :idImagen;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":orden" => 1, ":idImagen" => $idImagenPrincipal));
		}
		
		
		//sube una o varias imagenes para el inmueble
		if ($imagen != "") {
			$imagenes = explode(",", $imagen);
			$imagenPrincipal = explode(",", $imagenPrincipal);
			$urlArchivosTemp = $urlArchivos."temp/";
			
			for ($x = 0; $x < count($imagenes); $x++) {
				rename($urlArchivosTemp.$imagenes[$x], $urlArchivos.$imagenes[$x]);
				
				$consulta = "INSERT INTO IMAGEN_INMUEBLE(IIN_INMUEBLE, IIN_IMAGEN, IIN_ORDEN) VALUES(:inmueble, :imagen, :orden);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":inmueble" => $id, ":imagen" => $imagenes[$x], ":orden" => $imagenPrincipal[$x]));
			}
		}
		
		
		//sube uno o varios videos para el inmueble
		if ($videos != "") {
			$urlVideos = explode(",", $videos);
			
			for ($x = 0; $x < count($urlVideos); $x++) {
				$consulta = "INSERT INTO VIDEO_INMUEBLE(VIN_INMUEBLE, VIN_VIDEO) VALUES(:inmueble, :video);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":inmueble" => $id, ":video" => $urlVideos[$x]));
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