<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$urlArchivos = "../../images/images/";
	
	
	$id = $_POST["id"];
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	
	
	if ($borrar) {
		$idVideo = $_POST["idVideo"];
		
		
		$consulta = "DELETE FROM VIDEO_INMUEBLE WHERE VIN_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idVideo));
	}
	
	
	if ($nuevo) {
		$video = $_POST["video"];
		
		$consulta = "INSERT INTO VIDEO_INMUEBLE(VIN_INMUEBLE, VIN_VIDEO) VALUES(:inmueble, :video);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":inmueble" => $id, ":video" => $video));
	}
	
	
	$consulta = 
		"SELECT VIN_ID, VIN_VIDEO
		FROM VIDEO_INMUEBLE
		WHERE VIN_INMUEBLE = ".$id."
		ORDER BY VIN_ID DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($id));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["VIN_ID"],
			"campo2"	=>	$row["VIN_VIDEO"]
		);
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"datos"		=>	$arrayCampos
	);
	
	echo json_encode($arrayRespuesta);
	
?>