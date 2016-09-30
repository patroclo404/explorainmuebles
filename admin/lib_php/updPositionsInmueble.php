<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	
	
	$conexion = crearConexionPDO();
	$elem = 40;
	$pagina = isset($_POST["pagina"]) ? $_POST["pagina"] : 0;
	
	
	$palabra = isset($_POST["palabra"]) ? $_POST["palabra"] : "";
	$publicados = isset($_POST["publicados"]) ? $_POST["publicados"] : "";
	$nopublicados = isset($_POST["nopublicados"]) ? $_POST["nopublicados"] : "";
	$vencidos = isset($_POST["vencidos"]) ? $_POST["vencidos"] : "";
	$nopagados = isset($_POST["nopagados"]) ? $_POST["nopagados"] : "";
	$idInmobiliaria = isset($_POST["idInmobiliaria"]) ? $_POST["idInmobiliaria"] : -1;
	$idUsuario = isset($_POST["idUsuario"]) ? $_POST["idUsuario"] : -1;
	
	
	$consultaContadorElementos = "";
	$consulta = "";

	$consultaContadorElementos =
		"(
			SELECT
				IMU_ID,
				IMU_TITULO,
				IMU_DESTACADO,
				IMU_USUARIO,
				IMU_CATEGORIA_INMUEBLE,
				IMU_TIPO_INMUEBLE,
				IMU_PRECIO,
				IMU_CALLE_NUMERO,
				IMU_ESTADO,
				IMU_CIUDAD,
				IMU_COLONIA,
				IMU_CP,
				IMU_LATITUD,
				IMU_LONGITUD,
				IMU_DESCRIPCION,
				IMU_ANTIGUEDAD,
				IMU_CODIGO,
				IMU_DIMENSION_TOTAL,
				IMU_DIMENSION_CONSTRUIDA,
				IMU_ESTADO_CONSERVACION,
				IMU_AMUEBLADO,
				IMU_COCINA_EQUIPADA,
				IMU_ESTUDIO,
				IMU_CUARTO_SERVICIO,
				IMU_CUARTO_TV,
				IMU_BODEGA,
				IMU_TERRAZA,
				IMU_JARDIN,
				IMU_AREA_JUEGOS_INFANTILES,
				IMU_COMEDOR,
				IMU_SERVICIOS_BASICOS,
				IMU_GAS,
				IMU_LINEA_TELEFONICA,
				IMU_INTERNET_DISPONIBLE,
				IMU_AIRE_ACONDICIONADO,
				IMU_CALEFACCION,
				IMU_CUOTA_MANTENIMIENTO,
				IMU_CASETA_VIGILANCIA,
				IMU_ELEVADOR,
				IMU_SEGURIDAD,
				IMU_ALBERCA,
				IMU_CASA_CLUB,
				IMU_CANCHA_TENIS,
				IMU_VISTA_MAR,
				IMU_JACUZZI,
				IMU_ESTACIONAMIENTO_VISITAS,
				IMU_PERMITE_MASCOTAS,
				IMU_GIMNASIO,
				IMU_CENTROS_COMERCIALES_CERCANOS,
				IMU_ESCUELAS_CERCANAS,
				IMU_FUMADORES_PERMITIDOS,
				IMU_NUMERO_OFICINAS,
				IMU_WCS,
				IMU_RECAMARAS,
				IMU_HOSPITALES_CERCANOS,
				IMU_IGLESIAS_CERCANAS,
				IMU_AMUEBLADO2,
				IMU_SEMIAMUEBLADO,
				IMU_ZONA_INDUSTRIAL,
				IMU_ZONA_TURISTICA,
				IMU_ZONA_COMERCIAL,
				IMU_ZONA_RESIDENCIAL,
				IMU_BARES_CERCANOS,
				IMU_SUPERMERCADOS_CERCANOS,
				IMU_EXCELENTE_UBICACION,
				IMU_CISTERNA,
				IMU_CALENTADOR,
				IMU_CAMARAS,
				IMU_ANDEN,
				IMU_ASADOR,
				IMU_VAPOR,
				IMU_SAUNA,
				IMU_PLAYA,
				IMU_CLUB_PLAYA,
				IMU_PORTON_ELECTRICO,
				IMU_CHIMENEA,
				IMU_AREAS_VERDES,
				IMU_VISTA_PANORAMICA,
				IMU_CANCHA_SQUASH,
				IMU_CANCHA_BASKET,
				IMU_SALA_CINE,
				IMU_CANCHA_FUT,
				IMU_FAMILY_ROOM,
				IMU_CAMPO_GOLF,
				IMU_CABLETV,
				IMU_BIBLIOTECA,
				IMU_USOS_MULTIPLES,
				IMU_SALA,
				IMU_RECIBIDOR,
				IMU_VESTIDOR,
				IMU_ORATORIO,
				IMU_CAVA,
				IMU_PATIO,
				IMU_BALCON,
				IMU_LOBBY,
				IMU_METROS_FRENTE,
				IMU_METROS_FONDO,
				IMU_CAJONES_ESTACIONAMIENTO,
				IMU_DESARROLLO,
				IMU_CREATE,
				IMU_LIMITE_VIGENCIA,
				IMU_CONT_VISITAS,
				IMU_CONT_CONTACTADO,
				USU_INMOBILIARIA
			FROM INMUEBLE, USUARIO, ESTADO
			WHERE IMU_USUARIO = USU_ID
			AND IMU_ESTADO = EST_ID
			AND USU_INMOBILIARIA IS NULL ".
			(
				$palabra != ""
				? ("AND (
						IMU_TITULO LIKE :palabra
						OR IMU_ID = :codigo
						OR USU_NOMBRE LIKE :palabra
						OR EST_NOMBRE LIKE :palabra
					)")
				: ""
			).(
				$publicados != ""
				? " AND IMU_LIMITE_VIGENCIA >= CURDATE() "
				: ""
			).(
				$nopublicados != ""
				? " AND IMU_ID = 0 "
				: ""
			).(
				$vencidos != ""
				? " AND (IMU_LIMITE_VIGENCIA < CURDATE() AND IMU_LIMITE_VIGENCIA != '2000-01-01') "
				: ""
			).(
				$nopagados != ""
				? " AND IMU_LIMITE_VIGENCIA = '2000-01-01' "
				: ""
			).(
				$idInmobiliaria != -1
				? (
					" AND IMU_ID = 0 "
				) : ""
			).(
				$idUsuario != -1
				? (
					" AND IMU_USUARIO = ".$idUsuario." "
				) : ""
			)."
		) UNION (
			SELECT
				IMU_ID,
				IMU_TITULO,
				IMU_DESTACADO,
				IMU_USUARIO,
				IMU_CATEGORIA_INMUEBLE,
				IMU_TIPO_INMUEBLE,
				IMU_PRECIO,
				IMU_CALLE_NUMERO,
				IMU_ESTADO,
				IMU_CIUDAD,
				IMU_COLONIA,
				IMU_CP,
				IMU_LATITUD,
				IMU_LONGITUD,
				IMU_DESCRIPCION,
				IMU_ANTIGUEDAD,
				IMU_CODIGO,
				IMU_DIMENSION_TOTAL,
				IMU_DIMENSION_CONSTRUIDA,
				IMU_ESTADO_CONSERVACION,
				IMU_AMUEBLADO,
				IMU_COCINA_EQUIPADA,
				IMU_ESTUDIO,
				IMU_CUARTO_SERVICIO,
				IMU_CUARTO_TV,
				IMU_BODEGA,
				IMU_TERRAZA,
				IMU_JARDIN,
				IMU_AREA_JUEGOS_INFANTILES,
				IMU_COMEDOR,
				IMU_SERVICIOS_BASICOS,
				IMU_GAS,
				IMU_LINEA_TELEFONICA,
				IMU_INTERNET_DISPONIBLE,
				IMU_AIRE_ACONDICIONADO,
				IMU_CALEFACCION,
				IMU_CUOTA_MANTENIMIENTO,
				IMU_CASETA_VIGILANCIA,
				IMU_ELEVADOR,
				IMU_SEGURIDAD,
				IMU_ALBERCA,
				IMU_CASA_CLUB,
				IMU_CANCHA_TENIS,
				IMU_VISTA_MAR,
				IMU_JACUZZI,
				IMU_ESTACIONAMIENTO_VISITAS,
				IMU_PERMITE_MASCOTAS,
				IMU_GIMNASIO,
				IMU_CENTROS_COMERCIALES_CERCANOS,
				IMU_ESCUELAS_CERCANAS,
				IMU_FUMADORES_PERMITIDOS,
				IMU_NUMERO_OFICINAS,
				IMU_WCS,
				IMU_RECAMARAS,
				IMU_HOSPITALES_CERCANOS,
				IMU_IGLESIAS_CERCANAS,
				IMU_AMUEBLADO2,
				IMU_SEMIAMUEBLADO,
				IMU_ZONA_INDUSTRIAL,
				IMU_ZONA_TURISTICA,
				IMU_ZONA_COMERCIAL,
				IMU_ZONA_RESIDENCIAL,
				IMU_BARES_CERCANOS,
				IMU_SUPERMERCADOS_CERCANOS,
				IMU_EXCELENTE_UBICACION,
				IMU_CISTERNA,
				IMU_CALENTADOR,
				IMU_CAMARAS,
				IMU_ANDEN,
				IMU_ASADOR,
				IMU_VAPOR,
				IMU_SAUNA,
				IMU_PLAYA,
				IMU_CLUB_PLAYA,
				IMU_PORTON_ELECTRICO,
				IMU_CHIMENEA,
				IMU_AREAS_VERDES,
				IMU_VISTA_PANORAMICA,
				IMU_CANCHA_SQUASH,
				IMU_CANCHA_BASKET,
				IMU_SALA_CINE,
				IMU_CANCHA_FUT,
				IMU_FAMILY_ROOM,
				IMU_CAMPO_GOLF,
				IMU_CABLETV,
				IMU_BIBLIOTECA,
				IMU_USOS_MULTIPLES,
				IMU_SALA,
				IMU_RECIBIDOR,
				IMU_VESTIDOR,
				IMU_ORATORIO,
				IMU_CAVA,
				IMU_PATIO,
				IMU_BALCON,
				IMU_LOBBY,
				IMU_METROS_FRENTE,
				IMU_METROS_FONDO,
				IMU_CAJONES_ESTACIONAMIENTO,
				IMU_DESARROLLO,
				IMU_CREATE,
				IMU_LIMITE_VIGENCIA,
				IMU_CONT_VISITAS,
				IMU_CONT_CONTACTADO,
				USU_INMOBILIARIA
			FROM INMUEBLE, USUARIO, ESTADO, INMOBILIARIA
			WHERE IMU_USUARIO = USU_ID
			AND IMU_ESTADO = EST_ID
			AND USU_INMOBILIARIA = INM_ID ".
			(
				$palabra != ""
				? ("AND (
						IMU_TITULO LIKE :palabra
						OR IMU_ID = :codigo
						OR USU_NOMBRE LIKE :palabra
						OR EST_NOMBRE LIKE :palabra
					)")
				: ""
			).(
				$publicados != ""
				? " AND IMU_LIMITE_VIGENCIA >= CURDATE() 
					AND INM_VALIDEZ >= CURDATE() "
				: ""
			).(
				$nopublicados != ""
				? " AND IMU_LIMITE_VIGENCIA < CURDATE() 
				 	AND INM_VALIDEZ >= CURDATE() "
				: ""
			).(
				$vencidos != ""
				? " AND INM_VALIDEZ < CURDATE() "
				: ""
			).(
				$nopagados != ""
				? " AND IMU_ID = 0 "
				: ""
			).(
				$idInmobiliaria != -1
				? (
					" AND INM_ID = ".$idInmobiliaria
				) : ""
			).(
				$idUsuario != -1
				? (
					" AND IMU_USUARIO = ".$idUsuario." "
				) : ""
			)."
		) ORDER BY IMU_ID DESC";
		
	
	$consulta =
		$consultaContadorElementos."
		LIMIT ".($elem * $pagina).", ".$elem.";";
		
	
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":palabra" => "%".$palabra."%", ":codigo" => $palabra));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$leyendaEstado = "";
		$timeStamp_fechaHoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$partes = explode("-", $row["IMU_LIMITE_VIGENCIA"]);
		$timeStamp_inmueble = mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
		
		//para usuarios
		if ($row["USU_INMOBILIARIA"] == NULL) {
			if ($timeStamp_inmueble >= $timeStamp_fechaHoy)
				$leyendaEstado = "Publicado";
			if ($timeStamp_inmueble < $timeStamp_fechaHoy) {
				if ($row["IMU_LIMITE_VIGENCIA"] != "2000-01-01")
					$leyendaEstado = "Vencido";
			}
			if ($row["IMU_LIMITE_VIGENCIA"] == "2000-01-01")
				$leyendaEstado = "No Pagado";
		}
		else {//para inmobiliaria
			$consulta2 = "SELECT INM_VALIDEZ FROM INMOBILIARIA WHERE INM_ID = ".$row["USU_INMOBILIARIA"].";";
			$pdo2 = $conexion->query($consulta2);
			$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
			$row2 = $res2[0];
			
			$partes2 = explode("-", $row2["INM_VALIDEZ"]);
			$timeStamp_inmobiliaria = mktime(0, 0, 0, $partes2[1], $partes2[2], $partes2[0]);
			
			if ($timeStamp_inmueble >= $timeStamp_fechaHoy) {
				if ($timeStamp_inmobiliaria >= $timeStamp_fechaHoy)
					$leyendaEstado = "Publicado";
			}
			if ($timeStamp_inmueble < $timeStamp_fechaHoy) {
				if ($timeStamp_inmobiliaria >= $timeStamp_fechaHoy)
					$leyendaEstado = "No Publicado";
			}
			if ($timeStamp_inmobiliaria < $timeStamp_fechaHoy)
				$leyendaEstado = "Vencido";
		}
		
		
		$transaccion = -1;
		//consulta la primera transaccion del inmueble
		
		
		$consulta2 = "SELECT TRI_TRANSACCION FROM TRANSACCION_INMUEBLE WHERE TRI_INMUEBLE = ? ORDER BY TRI_ID LIMIT 1;";
		$pdo2 = $conexion->prepare($consulta2);
		$pdo2->execute(array($row["IMU_ID"]));
		$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
		$row2 = $res2[0];
		$transaccion = $row2["TRI_TRANSACCION"];
		
		
		//consulta las imagenes
		$arrayImagenes = array();
		
		$consulta2 = "SELECT IIN_ID, IIN_IMAGEN, IIN_ORDEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ? ORDER BY IIN_ID;";
		$pdo2 = $conexion->prepare($consulta2);
		$pdo2->execute(array($row["IMU_ID"]));
		foreach($pdo2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$arrayImagenes[] = array(
				"id"		=>	$row2["IIN_ID"],
				"imagen"	=>	$row2["IIN_IMAGEN"],
				"principal"	=>	$row2["IIN_ORDEN"]
			);
		}
		
		
		//consulta los videos
		$arrayVideos = array();
		
		$consulta2 = "SELECT VIN_ID, VIN_VIDEO FROM VIDEO_INMUEBLE WHERE VIN_INMUEBLE = ? ORDER BY VIN_ID;";
		$pdo2 = $conexion->prepare($consulta2);
		$pdo2->execute(array($row["IMU_ID"]));
		foreach($pdo2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$arrayVideos[] = array(
				"id"	=>	$row2["VIN_ID"],
				"video"	=>	$row2["VIN_VIDEO"]
			);
		}
		
		
		$arrayCampos[] = array(
			"id"						=>	$row["IMU_ID"],
			"titulo"					=>	$row["IMU_TITULO"],
			"propiedadDestacada"		=>  $row['IMU_DESTACADO'],
			"usuario"					=>	$row["IMU_USUARIO"],
			"categoria"					=>	$row["IMU_CATEGORIA_INMUEBLE"],
			"tipo"						=>	$row["IMU_TIPO_INMUEBLE"],
			"precio"					=>	$row["IMU_PRECIO"],
			"calleNumero"				=>	$row["IMU_CALLE_NUMERO"],
			"estado"					=>	$row["IMU_ESTADO"],
			"ciudad"					=>	$row["IMU_CIUDAD"],
			"colonia"					=>	$row["IMU_COLONIA"],
			"cp"						=>	$row["IMU_CP"],
			"latitud"					=>	$row["IMU_LATITUD"],
			"longitud"					=>	$row["IMU_LONGITUD"],
			"descripcion"				=>	$row["IMU_DESCRIPCION"] != NULL ? $row["IMU_DESCRIPCION"] : "",
			"antiguedad"				=>	$row["IMU_ANTIGUEDAD"] != NULL ? $row["IMU_ANTIGUEDAD"] : "",
			"codigo"					=>	$row["IMU_CODIGO"] != NULL ? $row["IMU_CODIGO"] : "",
			"dimensionTotal"			=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
			"dimensionConstruida"		=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
			"estadoConservacion"		=>	$row["IMU_ESTADO_CONSERVACION"] != NULL ? $row["IMU_ESTADO_CONSERVACION"] : "",
			"amueblado"					=>	$row["IMU_AMUEBLADO"],
			"cocinaEquipada"			=>	$row["IMU_COCINA_EQUIPADA"],
			"estudio"					=>	$row["IMU_ESTUDIO"],
			"cuartoServicio"			=>	$row["IMU_CUARTO_SERVICIO"],
			"cuartoTV"					=>	$row["IMU_CUARTO_TV"],
			"bodega"					=>	$row["IMU_BODEGA"],
			"terraza"					=>	$row["IMU_TERRAZA"],
			"jardin"					=>	$row["IMU_JARDIN"],
			"areaJuegosInfantiles"		=>	$row["IMU_AREA_JUEGOS_INFANTILES"],
			"comedor"					=>	$row["IMU_COMEDOR"],
			"serviciosBasicos"			=>	$row["IMU_SERVICIOS_BASICOS"],
			"gas"						=>	$row["IMU_GAS"],
			"lineaTelefonica"			=>	$row["IMU_LINEA_TELEFONICA"],
			"internetDisponible"		=>	$row["IMU_INTERNET_DISPONIBLE"],
			"aireAcondicionado"			=>	$row["IMU_AIRE_ACONDICIONADO"],
			"calefaccion"				=>	$row["IMU_CALEFACCION"],
			"cuotaMantenimiento"		=>	$row["IMU_CUOTA_MANTENIMIENTO"] != NULL ? $row["IMU_CUOTA_MANTENIMIENTO"] : "",
			"casetaVigilancia"			=>	$row["IMU_CASETA_VIGILANCIA"],
			"elevador"					=>	$row["IMU_ELEVADOR"] != NULL ? $row["IMU_ELEVADOR"] : "",
			"seguridad"					=>	$row["IMU_SEGURIDAD"],
			"alberca"					=>	$row["IMU_ALBERCA"],
			"casaClub"					=>	$row["IMU_CASA_CLUB"],
			"canchaTenis"				=>	$row["IMU_CANCHA_TENIS"],
			"vistaMar"					=>	$row["IMU_VISTA_MAR"],
			"jacuzzi"					=>	$row["IMU_JACUZZI"],
			"estacionamientoVisitas"	=>	$row["IMU_ESTACIONAMIENTO_VISITAS"] != NULL ? $row["IMU_ESTACIONAMIENTO_VISITAS"] : "",
			"permiteMascotas"			=>	$row["IMU_PERMITE_MASCOTAS"],
			"gimnasio"					=>	$row["IMU_GIMNASIO"],
			"centrosComercialesCercanos"=>	$row["IMU_CENTROS_COMERCIALES_CERCANOS"],
			"escuelasCercanas"			=>	$row["IMU_ESCUELAS_CERCANAS"],
			"fumadoresPermitidos"		=>	$row["IMU_FUMADORES_PERMITIDOS"],
			"numeroOficinas"			=>	$row["IMU_NUMERO_OFICINAS"] != NULL ? $row["IMU_NUMERO_OFICINAS"] : "",
			"wcs"						=>	$row["IMU_WCS"] != NULL ? ((int)$row["IMU_WCS"]) : "",
			"recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
			"hospitalesCercanos"		=>	$row["IMU_HOSPITALES_CERCANOS"],
			"iglesiasCercanas"			=>	$row["IMU_IGLESIAS_CERCANAS"],
			"amueblado2"				=>	$row["IMU_AMUEBLADO2"],
			"semiAmueblado"				=>	$row["IMU_SEMIAMUEBLADO"],
			"zonaIndustrial"			=>	$row["IMU_ZONA_INDUSTRIAL"],
			"zonaTuristica"				=>	$row["IMU_ZONA_TURISTICA"],
			"zonaComercial"				=>	$row["IMU_ZONA_COMERCIAL"],
			"zonaResidencial"			=>	$row["IMU_ZONA_RESIDENCIAL"],
			"baresCercanos"				=>	$row["IMU_BARES_CERCANOS"],
			"supermercadosCercanos"		=>	$row["IMU_SUPERMERCADOS_CERCANOS"],
			"excelenteUbicacion"		=>	$row["IMU_EXCELENTE_UBICACION"],
			"cisterna"					=>	$row["IMU_CISTERNA"],
			"calentador"				=>	$row["IMU_CALENTADOR"],
			"camaras"					=>	$row["IMU_CAMARAS"],
			"anden"						=>	$row["IMU_ANDEN"],
			"asador"					=>	$row["IMU_ASADOR"],
			"vapor"						=>	$row["IMU_VAPOR"],
			"sauna"						=>	$row["IMU_SAUNA"],
			"playa"						=>	$row["IMU_PLAYA"],
			"clubPlaya"					=>	$row["IMU_CLUB_PLAYA"],
			"portonElectrico"			=>	$row["IMU_PORTON_ELECTRICO"],
			"chimenea"					=>	$row["IMU_CHIMENEA"],
			"areasVerdes"				=>	$row["IMU_AREAS_VERDES"],
			"vistaPanoramica"			=>	$row["IMU_VISTA_PANORAMICA"],
			"canchaSquash"				=>	$row["IMU_CANCHA_SQUASH"],
			"canchaBasket"				=>	$row["IMU_CANCHA_BASKET"],
			"salaCine"					=>	$row["IMU_SALA_CINE"],
			"canchaFut"					=>	$row["IMU_CANCHA_FUT"],
			"familyRoom"				=>	$row["IMU_FAMILY_ROOM"],
			"campoGolf"					=>	$row["IMU_CAMPO_GOLF"],
			"cableTV"					=>	$row["IMU_CABLETV"],
			"biblioteca"				=>	$row["IMU_BIBLIOTECA"],
			"usosMultiples"				=>	$row["IMU_USOS_MULTIPLES"],
			"sala"						=>	$row["IMU_SALA"],
			"recibidor"					=>	$row["IMU_RECIBIDOR"],
			"vestidor"					=>	$row["IMU_VESTIDOR"],
			"oratorio"					=>	$row["IMU_ORATORIO"],
			"cava"						=>	$row["IMU_CAVA"],
			"patio"						=>	$row["IMU_PATIO"],
			"balcon"					=>	$row["IMU_BALCON"],
			"lobby"						=>	$row["IMU_LOBBY"],
			"metrosFrente"				=>	$row["IMU_METROS_FRENTE"] != NULL ? $row["IMU_METROS_FRENTE"] : "",
			"metrosFondo"				=>	$row["IMU_METROS_FONDO"] != NULL ? $row["IMU_METROS_FONDO"] : "",
			"cajonesEstacionamiento"	=>	$row["IMU_CAJONES_ESTACIONAMIENTO"] != NULL ? $row["IMU_CAJONES_ESTACIONAMIENTO"] : "",
			"desarrollo"				=>	$row["IMU_DESARROLLO"] != NULL ? $row["IMU_DESARROLLO"] : "",
			"create"					=>	getDateNormal($row["IMU_CREATE"]),
			"limiteVigencia"			=>	getDateNormal($row["IMU_LIMITE_VIGENCIA"]),
			"contVisitas"				=>	$row["IMU_CONT_VISITAS"],
			"contContactado"			=>	$row["IMU_CONT_CONTACTADO"],
			"leyendaEstado"				=>	$leyendaEstado,
			"transaccion"				=>	$transaccion,
			"imagenes"					=>	$arrayImagenes,
			"videos"					=>	$arrayVideos
		);
	}
	

	$pdo = $conexion->prepare($consultaContadorElementos);
	$pdo->execute(array(":palabra" => "%".$palabra."%", ":codigo" => $palabra));
	$numeroElementos = $pdo->rowCount();
	
	
	$consultaContadores =
		"SELECT
			(
				SELECT (
					SELECT COUNT(IMU_ID)
					FROM INMUEBLE, USUARIO
					WHERE IMU_USUARIO = USU_ID
					AND USU_INMOBILIARIA IS NULL
					AND IMU_LIMITE_VIGENCIA >= CURDATE()
				) + (
					SELECT COUNT(IMU_ID)
					FROM INMUEBLE, USUARIO, INMOBILIARIA
					WHERE IMU_USUARIO = USU_ID
					AND USU_INMOBILIARIA = INM_ID
					AND IMU_LIMITE_VIGENCIA >= CURDATE()
					AND INM_VALIDEZ >= CURDATE()
				)
			) AS CONS_PUBLICADOS,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE, USUARIO, INMOBILIARIA
				WHERE IMU_USUARIO = USU_ID
				AND USU_INMOBILIARIA = INM_ID
				AND IMU_LIMITE_VIGENCIA < CURDATE()
				AND INM_VALIDEZ >= CURDATE()
			) AS CONS_NOPUBLICADOS,
			(
				SELECT (
					SELECT COUNT(IMU_ID)
					FROM INMUEBLE, USUARIO
					WHERE IMU_USUARIO = USU_ID
					AND USU_INMOBILIARIA IS NULL
					AND (IMU_LIMITE_VIGENCIA < CURDATE() AND IMU_LIMITE_VIGENCIA != '2000-01-01')
				) + (
					SELECT COUNT(IMU_ID)
					FROM INMUEBLE, USUARIO, INMOBILIARIA
					WHERE IMU_USUARIO = USU_ID
					AND USU_INMOBILIARIA = INM_ID
					AND INM_VALIDEZ < CURDATE()
				)
			) AS CONS_VENCIDOS,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE, USUARIO
				WHERE IMU_USUARIO = USU_ID
				AND USU_INMOBILIARIA IS NULL
				AND IMU_LIMITE_VIGENCIA = '2000-01-01'
			) AS CONS_NOPAGADOS;";
	$pdo = $conexion->query($consultaContadores);
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$arrayContadores = array(
		"publicados"	=>	$row["CONS_PUBLICADOS"],
		"noPublicados"	=>	$row["CONS_NOPUBLICADOS"],
		"vencidos"		=>	$row["CONS_VENCIDOS"],
		"noPagados"		=>	$row["CONS_NOPAGADOS"],
		"total"			=>	$row["CONS_PUBLICADOS"] + $row["CONS_VENCIDOS"] + $row["CONS_NOPUBLICADOS"] + $row["CONS_NOPAGADOS"]
	);
	

	$arrayRespuesta = array(
		"datos"				=>	$arrayCampos,
		"page"				=>	$pagina,
		"elem"				=>	$elem,
		"numeroElementos"	=>	$numeroElementos,
		"maxPaginas"		=>	ceil($numeroElementos / $elem),
		"maxPaginacion"		=>	10,
		"contadores"		=>	$arrayContadores
	);
	
	echo json_encode($arrayRespuesta);
	
?>