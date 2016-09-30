<?php

	require_once("template.php");
	
	
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	$transaccion = $_POST["transaccion"];
	$tipoInmueble = $_POST["tipoInmueble"];
	$estado = $_POST["estado"];
	$ciudad = $_POST["ciudad"];
	$colonia = $_POST["colonia"];
	$codigo = $_POST["codigo"];
	$preciosMin = $_POST["preciosMin"];
	$preciosMax = $_POST["preciosMax"];
	$wcs = $_POST["wcs"];
	$recamaras = $_POST["recamaras"];
	
	
	//elementos de busqueda avanzada
	$antiguedad = isset($_POST["antiguedad"]) ? $_POST["antiguedad"] : -1;
	$estadoConservacion = isset($_POST["estadoConservacion"]) ? $_POST["estadoConservacion"] : -1;
	$amueblado = isset($_POST["amueblado"]) ? $_POST["amueblado"] : -1;
	
	$dimensionTotalMin = isset($_POST["dimensionTotalMin"]) ? $_POST["dimensionTotalMin"] : -1;
	$dimensionTotalMax = isset($_POST["dimensionTotalMax"]) ? $_POST["dimensionTotalMax"] : -1;
	$dimensionConstruidaMin = isset($_POST["dimensionConstruidaMin"]) ? $_POST["dimensionConstruidaMin"] : -1;
	$dimensionConstruidaMax = isset($_POST["dimensionConstruidaMax"]) ? $_POST["dimensionConstruidaMax"] : -1;
	$cuotaMantenimiento = isset($_POST["cuotaMantenimiento"]) ? $_POST["cuotaMantenimiento"] : "";
	
	$elevador = isset($_POST["elevador"]) ? $_POST["elevador"] : "";
	$estacionamientoVisitas = isset($_POST["estacionamientoVisitas"]) ? $_POST["estacionamientoVisitas"] : "";
	$numeroOficinas = isset($_POST["numeroOficinas"]) ? $_POST["numeroOficinas"] : "";
	
	$cocinaEquipada = isset($_POST["cocinaEquipada"]) ? $_POST["cocinaEquipada"] : "";
	$estudio = isset($_POST["estudio"]) ? $_POST["estudio"] : "";
	$cuartoServicio = isset($_POST["cuartoServicio"]) ? $_POST["cuartoServicio"] : "";
	$cuartoTV = isset($_POST["cuartoTV"]) ? $_POST["cuartoTV"] : "";
	$bodega = isset($_POST["bodega"]) ? $_POST["bodega"] : "";
	$terraza = isset($_POST["terraza"]) ? $_POST["terraza"] : "";
	$jardin = isset($_POST["jardin"]) ? $_POST["jardin"] : "";
	$areaJuegosInfantiles = isset($_POST["areaJuegosInfantiles"]) ? $_POST["areaJuegosInfantiles"] : "";
	$comedor = isset($_POST["comedor"]) ? $_POST["comedor"] : "";
	$serviciosBasicos = isset($_POST["serviciosBasicos"]) ? $_POST["serviciosBasicos"] : "";
	$gas = isset($_POST["gas"]) ? $_POST["gas"] : "";
	$lineaTelefonica = isset($_POST["lineaTelefonica"]) ? $_POST["lineaTelefonica"] : "";
	$internetDisponible = isset($_POST["internetDisponible"]) ? $_POST["internetDisponible"] : "";
	$aireAcondicionado = isset($_POST["aireAcondicionado"]) ? $_POST["aireAcondicionado"] : "";
	$calefaccion = isset($_POST["calefaccion"]) ? $_POST["calefaccion"] : "";
	$casetaVigilancia = isset($_POST["casetaVigilancia"]) ? $_POST["casetaVigilancia"] : "";
	$seguridad = isset($_POST["seguridad"]) ? $_POST["seguridad"] : "";
	$alberca = isset($_POST["alberca"]) ? $_POST["alberca"] : "";
	$casaClub = isset($_POST["casaClub"]) ? $_POST["casaClub"] : "";
	$canchaTenis = isset($_POST["canchaTenis"]) ? $_POST["canchaTenis"] : "";
	$vistaMar = isset($_POST["vistaMar"]) ? $_POST["vistaMar"] : "";
	$jacuzzi = isset($_POST["jacuzzi"]) ? $_POST["jacuzzi"] : "";
	$permiteMascotas = isset($_POST["permiteMascotas"]) ? $_POST["permiteMascotas"] : "";
	$gimnasio = isset($_POST["gimnasio"]) ? $_POST["gimnasio"] : "";
	$centrosComerciales = isset($_POST["centrosComerciales"]) ? $_POST["centrosComerciales"] : "";
	$escuelasCercanas = isset($_POST["escuelasCercanas"]) ? $_POST["escuelasCercanas"] : "";
	$fumadoresPermitidos = isset($_POST["fumadoresPermitidos"]) ? $_POST["fumadoresPermitidos"] : "";
	

	$consulta =
		"SELECT
			IMU_ID,
			IMU_TITULO, ".
			/*IMU_USUARIO,
			IMU_CATEGORIA_INMUEBLE,*/
			"IMU_TIPO_INMUEBLE,
			IMU_PRECIO,
			IMU_CALLE_NUMERO,
			IMU_ESTADO,
			IMU_CIUDAD,
			IMU_COLONIA,
			IMU_CP,
			IMU_LATITUD,
			IMU_LONGITUD,
			IMU_DESCRIPCION, ".
			//IMU_ANTIGUEDAD,
			"IMU_CODIGO,
			IMU_DIMENSION_TOTAL, ".
			/*IMU_DIMENSION_CONSTRUIDA,
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
			IMU_NUMERO_OFICINAS,*/
			"IMU_WCS,
			IMU_RECAMARAS,
			(
				SELECT IIN_IMAGEN
				FROM IMAGEN_INMUEBLE
				WHERE IIN_INMUEBLE = IMU_ID
				ORDER BY IIN_ORDEN DESC LIMIT 1
			) AS CONS_IMAGEN,
			(
				SELECT EST_NOMBRE
				FROM ESTADO
				WHERE EST_ID = IMU_ESTADO
			) AS CONS_ESTADO,
			(
				SELECT CIU_NOMBRE
				FROM CIUDAD
				WHERE CIU_ID = IMU_CIUDAD
			) AS CONS_CIUDAD,
			(
				SELECT COL_NOMBRE
				FROM COLONIA
				WHERE COL_ID = IMU_COLONIA
			) AS CONS_COLONIA,
			(
				SELECT CP_CP
				FROM CP
				WHERE CP_ID = IMU_CP
			) AS CONS_CP,
			( ".(
				$usuario != -1
				? (
					"SELECT FIN_ID
					FROM FAVORITO_INMUEBLE
					WHERE FIN_USUARIO = ".$usuario."
					AND FIN_INMUEBLE = IMU_ID"
				) : -1
			)." ) AS CONS_LIKE
		FROM INMUEBLE, TRANSACCION_INMUEBLE
		WHERE TRI_INMUEBLE = IMU_ID
		AND TRI_TRANSACCION = ".$transaccion."
		AND(
			SELECT COUNT(IIN_ID)
			FROM IMAGEN_INMUEBLE
			WHERE IIN_INMUEBLE = IMU_ID
		) > 0 ".
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
			$escuelasCercanas != ""
			? " AND IMU_ESCUELAS_CERCANAS = 1"
			: ""	
		).
		(
			$fumadoresPermitidos != ""
			? " AND IMU_FUMADORES_PERMITIDOS = 1"
			: ""	
		).
		" ORDER BY IMU_ID DESC";
	$res = crearConsulta($consulta);
	$arrayCampos = array();
	
	while($row = mysql_fetch_array($res)){
		$arrayCampos[] = array(
			"id"						=>	$row["IMU_ID"],
			"titulo"					=>	$row["IMU_TITULO"],
			/*"usuario"					=>	$row["IMU_USUARIO"],
			"categoria"					=>	$row["IMU_CATEGORIA_INMUEBLE"],*/
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
			//"antiguedad"				=>	$row["IMU_ANTIGUEDAD"] != NULL ? $row["IMU_ANTIGUEDAD"] : "",
			"codigo"					=>	$row["IMU_CODIGO"] != NULL ? $row["IMU_CODIGO"] : "",
			"dimensionTotal"			=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
			/*"dimensionConstruida"		=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
			"estadoConservacion"		=>	$row["IMU_ESTADO_CONSERVACION"],
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
			"centrosComerciales"		=>	$row["IMU_CENTROS_COMERCIALES_CERCANOS"],
			"escuelasCercanas"			=>	$row["IMU_ESCUELAS_CERCANAS"],
			"fumadoresPermitidos"		=>	$row["IMU_FUMADORES_PERMITIDOS"],
			"numeroOficinas"			=>	$row["IMU_NUMERO_OFICINAS"] != NULL ? $row["IMU_NUMERO_OFICINAS"] : "",*/
			"wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : "",
			"recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
			"imagen"					=>	$row["CONS_IMAGEN"],
			"estadoNombre"				=>	$row["CONS_ESTADO"],
			"ciudadNombre"				=>	$row["CONS_CIUDAD"],
			"coloniaNombre"				=>	$row["CONS_COLONIA"],
			"cpNombre"					=>	$row["CONS_CP"],
			"like"						=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"]
		);
	}
	
	$arrayRespuesta = array(
		"datos"	=>	$arrayCampos
	);
	
	echo json_encode($arrayRespuesta);
	
?>