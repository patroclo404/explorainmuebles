<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	
	
	$conexion = crearConexionPDO();
	$palabra = isset($_POST["palabra"]) ? $_POST["palabra"] : "";
	$pendientes = isset($_POST["pendientes"]) ? $_POST["pendientes"] : "";
	$idUsuario = isset($_POST["idUsuario"]) ? $_POST["idUsuario"] : -1;
	

	$consulta = 
		"SELECT
			USU_ID,
			USU_NOMBRE,
			USU_EMAIL,
			USU_FBID,
			USU_SEXO,
			USU_FECHANACIMIENTO,
			USU_TELEFONO1,
			USU_TELEFONO2,
			USU_CALLE_NUMERO,
			USU_ESTADO,
			USU_CIUDAD,
			USU_COLONIA,
			USU_CP,
			USU_INMOBILIARIA,
			USU_IMAGEN,
			USU_NOTIFICACIONES,
			USU_VALIDADO,
			USU_CREATE,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE
				WHERE IMU_USUARIO = USU_ID
			) AS CONS_ANUNCIOS,
			(
				SELECT INM_NOMBRE_EMPRESA
				FROM INMOBILIARIA
				WHERE INM_ID = USU_INMOBILIARIA
			) AS CONS_INMOBILIARIA
		FROM USUARIO
		WHERE ".(
			$idUsuario != -1
			? (" USU_ID = ".$idUsuario." ")
			: " USU_ID > 0 "
		).(
			$palabra != ""
			? (" AND (USU_NOMBRE LIKE :palabra OR USU_EMAIL LIKE :palabra) ")
			: ""
		).(
			$pendientes == 1
			? " AND USU_VALIDADO <> 1 "
			: ""
		).
		"ORDER BY USU_ID DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":palabra" => "%".$palabra."%"));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"id"					=>	$row["USU_ID"],
			"nombre"				=>	$row["USU_NOMBRE"],
			"email"					=>	$row["USU_EMAIL"],
			"FBId"					=>	$row["USU_FBID"] != NULL ? $row["USU_FBID"] : "",
			"sexo"					=>	$row["USU_SEXO"],
			"fechaNac"				=>	$row["USU_FECHANACIMIENTO"] != NULL ? getDateNormal($row["USU_FECHANACIMIENTO"]) : "",
			"telefono1"				=>	$row["USU_TELEFONO1"] != NULL ? $row["USU_TELEFONO1"] : "",
			"telefono2"				=>	$row["USU_TELEFONO2"] != NULL ? $row["USU_TELEFONO2"] : "",
			"calleNumero"			=>	$row["USU_CALLE_NUMERO"] != NULL ? $row["USU_CALLE_NUMERO"] : "",
			"estado"				=>	$row["USU_ESTADO"] != NULL ? $row["USU_ESTADO"] : "",
			"ciudad"				=>	$row["USU_CIUDAD"] != NULL ? $row["USU_CIUDAD"] : "",
			"colonia"				=>	$row["USU_COLONIA"] != NULL ? $row["USU_COLONIA"] : "",
			"cp"					=>	$row["USU_CP"] != NULL ? $row["USU_CP"] : "",
			"inmobiliaria"			=>	$row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : "",
			"imagen"				=>	$row["USU_IMAGEN"] != NULL ? $row["USU_IMAGEN"] : "",
			"notificaciones"		=>	$row["USU_NOTIFICACIONES"],
			"validado"				=>	$row["USU_VALIDADO"],
			"create"				=>	getDateNormal($row["USU_CREATE"]),
			"contAnuncios"			=>	$row["CONS_ANUNCIOS"],
			"inmobiliariaNombre"	=>	$row["CONS_INMOBILIARIA"] != NULL ? $row["CONS_INMOBILIARIA"] : ""
		);
	}
	
	
	$consulta =
		"SELECT
			(
				SELECT COUNT(USU_ID)
				FROM USUARIO
			) AS CONS_TOTAL,
			(
				SELECT COUNT(USU_ID)
				FROM USUARIO
				WHERE USU_VALIDADO <> 1
			) AS CONS_PENDIENTES;";
	$pdo = $conexion->query($consulta);
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	
	
	$arrayRespuesta = array(
		"datos"			=>	$arrayCampos,
		"contadores"	=>	array(
			"total"		=>	$row["CONS_TOTAL"],
			"pendientes"=>	$row["CONS_PENDIENTES"]
		)
	);
	
	echo json_encode($arrayRespuesta);
	
?>