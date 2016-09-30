<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	$palabra = isset($_POST["palabra"]) ? $_POST["palabra"] : "";
	

	$consulta =
		"SELECT
			IMU_ID,
			IMU_TITULO,
			IMU_LIMITE_VIGENCIA,
			IMU_USUARIO,
			USU_NOMBRE
		FROM INMUEBLE, USUARIO
		WHERE IMU_LIMITE_VIGENCIA < CURDATE()
		AND IMU_USUARIO = USU_ID ".
		(
			$palabra != ""
			? (" AND (IMU_TITULO LIKE :palabra OR USU_NOMBRE LIKE :palabra)")
			: ""
		)." ORDER BY IMU_LIMITE_VIGENCIA, IMU_TITULO;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":palabra" => "%".$palabra."%"));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"id"				=>	$row["IMU_ID"],
			"titulo"			=>	$row["IMU_TITULO"],
			"limiteVigencia"	=>	getDateNormal($row["IMU_LIMITE_VIGENCIA"]),
			"usuario"			=>	$row["IMU_USUARIO"],
			"usuarioNombre"		=>	$row["USU_NOMBRE"]
		);
	}
	
	
	$arrayRespuesta = array(
		"datos"			=>	$arrayCampos,
	);
	
	echo json_encode($arrayRespuesta);
	
?>