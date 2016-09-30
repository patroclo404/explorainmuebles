<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$urlArchivos = "../../images/images/";
	
	
	$id = $_POST["id"];
	
	
	$consulta = 
		"SELECT IMU_ID, IMU_TITULO
		FROM INMUEBLE
		WHERE IMU_USUARIO = ?
		ORDER BY IMU_ID DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($id));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["IMU_ID"],
			"campo2"	=>	$row["IMU_TITULO"]
		);
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"datos"		=>	$arrayCampos
	);
	
	echo json_encode($arrayRespuesta);
	
?>