<?php  


	require_once("template.php");
	$conexion = crearConexionPDO();
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	$transaccion = $_POST["transaccion"];
	$tipoInmueble = $_POST["tipoInmueble"];

	$consultaCondiciones =
		"FROM INMUEBLE, TRANSACCION_INMUEBLE, USUARIO
		WHERE TRI_INMUEBLE = IMU_ID
		AND TRI_TRANSACCION = :transaccion
		AND IMU_USUARIO = USU_ID
		AND(
			SELECT COUNT(IIN_ID)
			FROM IMAGEN_INMUEBLE
			WHERE IIN_INMUEBLE = IMU_ID
		) > 0 
		AND IMU_LIMITE_VIGENCIA >= :vigencia
		AND (
			IF (
				USU_INMOBILIARIA IS NOT NULL,
				(
					SELECT INM_VALIDEZ
					FROM INMOBILIARIA
					WHERE INM_ID = USU_INMOBILIARIA
				) >= :vigencia,
				1
			)
		) ".
		(
			$tipoInmueble != -1
			? " AND IMU_TIPO_INMUEBLE = ".$tipoInmueble
			: ""
		).
		(
			$estado != -1
			? " AND IMU_ESTADO = ".$estado
			: ""
		).
		(
			$ciudad != -1
			? " AND IMU_CIUDAD = ".$ciudad
			: ""	
		).
		(
			$colonia != -1
			? " AND IMU_COLONIA = ".$colonia
			: ""
		).
		(
			$preciosMin != -1
			? " AND IMU_PRECIO >= ".$preciosMin
			: ""
		).
		(
			$preciosMax != -1
			? " AND IMU_PRECIO <= ".$preciosMax
			: ""
		).
		(
			$wcs != -1
			? " AND IMU_WCS >= ".$wcs
			: ""
		).
		(
			$recamaras != -1
			? " AND IMU_RECAMARAS >= ".$recamaras
			: ""
		).
		(
			$antiguedad != -1
			? " AND IMU_ANTIGUEDAD = ".$antiguedad
			: ""	
		).
		(
			$estadoConservacion != -1
			? " AND IMU_ESTADO_CONSERVACION = ".$estadoConservacion
			: ""	
		).
		(
			$amueblado != -1
			? " AND IMU_AMUEBLADO = ".$amueblado
			: ""	
		).
		(
			$dimensionTotalMin != -1
			? " AND IMU_DIMENSION_TOTAL >= ".$dimensionTotalMin
			: ""	
		).
		(
			$dimensionTotalMax != -1
			? " AND IMU_DIMENSION_TOTAL <= ".$dimensionTotalMax
			: ""	
		).
		(
			$dimensionConstruidaMin != -1
			? " AND IMU_DIMENSION_CONSTRUIDA >= ".$dimensionConstruidaMin
			: ""	
		).
		(
			$dimensionConstruidaMax != -1
			? " AND IMU_DIMENSION_CONSTRUIDA <= ".$dimensionConstruidaMax
			: ""	
		).
		(
			$cuotaMantenimiento != ""
			? " AND IMU_CUOTA_MANTENIMIENTO <= ".$cuotaMantenimiento
			: ""	
		).
		(
			$elevador != ""
			? " AND IMU_ELEVADOR >= ".$elevador
			: ""	
		).
		(
			$estacionamientoVisitas != ""
			? " AND IMU_ESTACIONAMIENTO_VISITAS >= ".$estacionamientoVisitas
			: ""	
		).
		(
			$numeroOficinas != ""
			? " AND IMU_NUMERO_OFICINAS >= ".$numeroOficinas
			: ""	
		).
		(
			$cocinaEquipada != ""
			? " AND IMU_COCINA_EQUIPADA = 1"
			: ""	
		).
		(
			$estudio != ""
			? " AND IMU_ESTUDIO = 1"
			: ""	
		).
		(
			$cuartoServicio != ""
			? " AND IMU_CUARTO_SERVICIO = 1"
			: ""	
		).
		(
			$cuartoTV != ""
			? " AND IMU_CUARTO_TV = 1"
			: ""	
		).
		(
			$bodega != ""
			? " AND IMU_BODEGA = 1"
			: ""	
		).
		(
			$terraza != ""
			? " AND IMU_TERRAZA = 1"
			: ""	
		).
		(
			$jardin != ""
			? " AND IMU_JARDIN = 1"
			: ""	
		).
		(
			$areaJuegosInfantiles != ""
			? " AND IMU_AREA_JUEGOS_INFANTILES = 1"
			: ""	
		).
		(
			$comedor != ""
			? " AND IMU_COMEDOR = 1"
			: ""	
		).
		(
			$serviciosBasicos != ""
			? " AND IMU_SERVICIOS_BASICOS = 1"
			: ""	
		).
		(
			$gas != ""
			? " AND IMU_GAS = 1"
			: ""	
		).
		(
			$lineaTelefonica != ""
			? " AND IMU_LINEA_TELEFONICA = 1"
			: ""	
		).
		(
			$internetDisponible != ""
			? " AND IMU_INTERNET_DISPONIBLE = 1"
			: ""	
		).
		(
			$aireAcondicionado != ""
			? " AND IMU_AIRE_ACONDICIONADO = 1"
			: ""	
		).
		(
			$calefaccion != ""
			? " AND IMU_CALEFACCION = 1"
			: ""	
		).
		(
			$casetaVigilancia != ""
			? " AND IMU_CASETA_VIGILANCIA = 1"
			: ""	
		).
		(
			$seguridad != ""
			? " AND IMU_SEGURIDAD = 1"
			: ""	
		).
		(
			$alberca != ""
			? " AND IMU_ALBERCA = 1"
			: ""	
		).
		(
			$casaClub != ""
			? " AND IMU_CASA_CLUB = 1"
			: ""	
		).
		(
			$canchaTenis != ""
			? " AND IMU_CANCHA_TENIS = 1"
			: ""	
		).
		(
			$vistaMar != ""
			? " AND IMU_VISTA_MAR = 1"
			: ""	
		).
		(
			$jacuzzi != ""
			? " AND IMU_JACUZZI = 1"
			: ""	
		).
		(
			$permiteMascotas != ""
			? " AND IMU_PERMITE_MASCOTAS = 1"
			: ""	
		).
		(
			$gimnasio != ""
			? " AND IMU_GIMNASIO = 1"
			: ""	
		).
		(
			$centrosComerciales != ""
			? " AND IMU_CENTROS_COMERCIALES_CERCANOS = 1"
			: ""	
		).
		(
			$iglesias != ""
			? " AND IMU_IGLESIAS_CERCANAS = 1"
			: ""	
		).
		(
			$hospitales != ""
			? " AND IMU_HOSPITALES_CERCANOS = 1"
			: ""	
		).
		(
			$escuelasCercanas != ""
			? " AND IMU_ESCUELAS_CERCANAS = 1"
			: ""	
		).
		(
			$fumadoresPermitidos != ""
			? " AND IMU_FUMADORES_PERMITIDOS = 1"
			: ""	
		);

	$arrayCondiciones = array(
		":transaccion"	=> $transaccion,
		":vigencia"		=>	date("Y-m-d")
	);

	$consulta = "SELECT * FROM inmuebles";

	$pdo = $conexion->prepare($consulta);
	$pdo->execute($arrayCondiciones);
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"id"						=>	$row["IMU_ID"],
			"titulo"					=>	$row["IMU_TITULO"],
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
			"codigo"					=>	$row["IMU_CODIGO"] != NULL ? $row["IMU_CODIGO"] : "",
			"dimensionTotal"			=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
			"dimensionConstruida"		=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
			"wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : "",
			"recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
			"transaccion"				=>	$row["TRI_TRANSACCION"],
			"imagen"					=>	$row["CONS_IMAGEN"],
			"estadoNombre"				=>	$row["CONS_ESTADO"],
			"ciudadNombre"				=>	$row["CONS_CIUDAD"],
			"coloniaNombre"				=>	$row["CONS_COLONIA"],
			"cpNombre"					=>	$row["CONS_CP"],
			"like"						=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"],
			"transaccion"				=>	$transaccion,
			"tipoInmueble"				=>	$tipoInmueble
		);
	}
	
	$arrayRespuesta = array(
		"datos"				=>	$arrayCampos,
		"pagina"			=>	$pagina,
		"elem"				=>	$elem,
		"numeroElementos"	=>	$pdo->rowCount(),
		"maxPaginas"		=>	ceil($pdo->rowCount() / $elem)
	);

	echo json_encode($arrayRespuesta);


?>