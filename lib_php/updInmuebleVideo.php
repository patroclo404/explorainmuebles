<?php


	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	
	
	if($borrar){
		$idVideo = $_POST["idVideo"];
		
		
		$consulta = "DELETE FROM VIDEO_INMUEBLE WHERE VIN_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idVideo));
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	echo json_encode($arrayRespuesta);
	
?>