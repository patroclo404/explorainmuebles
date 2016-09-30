<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	$palabra = isset($_POST["palabra"]) ? $_POST["palabra"] : "";
	$idInmobiliaria = isset($_POST["idInmobiliaria"]) ? $_POST["idInmobiliaria"] : -1;
	$vencidos = isset($_POST["vencidos"]) ? $_POST["vencidos"] : "";
	

	$consulta =
		"SELECT
			INM_ID AS CONS_ID,
			INM_NOMBRE_EMPRESA,
			INM_RFC,
			INM_LOGOTIPO,
			INM_USUARIO,
			INM_VALIDEZ,
			INM_CREDITOS,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE, USUARIO, INMOBILIARIA AS INMO
				WHERE USU_ID = IMU_USUARIO
				AND INMO.INM_ID = CONS_ID
				AND USU_INMOBILIARIA = INMO.INM_ID
				AND IMU_LIMITE_VIGENCIA >= CURDATE() 
				AND INMO.INM_VALIDEZ >= CURDATE()
			) AS CONS_PUBLICADOS,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE, USUARIO
				WHERE USU_ID = IMU_USUARIO
				AND USU_INMOBILIARIA = CONS_ID
			) AS CONS_GUARDADOS
		FROM INMOBILIARIA
		WHERE INM_ID > 0 ".(
			$palabra != ""
			? (" AND INM_NOMBRE_EMPRESA LIKE :palabra ")
			: ""
		).(
			$idInmobiliaria != -1
			? (" AND INM_ID = ".$idInmobiliaria." ")
			: ""
		).(
			$vencidos != ""
			? " AND INM_VALIDEZ < CURDATE() "
			: ""
		)." ORDER BY INM_ID DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":palabra" => "%".$palabra."%"));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"id"			=>	$row["CONS_ID"],
			"nombreEmpresa"	=>	$row["INM_NOMBRE_EMPRESA"],
			"rfc"			=>	$row["INM_RFC"] != NULL ? $row["INM_RFC"] : "",
			"logotipo"		=>	$row["INM_LOGOTIPO"] != NULL ? $row["INM_LOGOTIPO"] : "",
			"usuario"		=>	$row["INM_USUARIO"],
			"validez"		=>	getDateNormal($row["INM_VALIDEZ"]),
			"creditos"		=>	$row["INM_CREDITOS"],
			"contPublicados"=>	$row["CONS_PUBLICADOS"],
			"contGuardados"	=>	$row["CONS_GUARDADOS"]
		);
	}
	
	
	$consultaEstadisticos =
		"SELECT
			(
				SELECT COUNT(INM_ID)
				FROM INMOBILIARIA
			) AS CONS_TOTAL,
			(
				SELECT COUNT(INM_ID)
				FROM INMOBILIARIA
				WHERE INM_VALIDEZ < CURDATE()
			) AS CONS_VENCIDOS";
	$pdo = $conexion->query($consultaEstadisticos);
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	
	$arrayRespuesta = array(
		"datos"			=>	$arrayCampos,
		"contadores"	=>	array(
			"total"		=>	$row["CONS_TOTAL"],
			"vencidos"	=>	$row["CONS_VENCIDOS"]
		)
	);
	
	echo json_encode($arrayRespuesta);
	
?>