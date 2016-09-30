<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	$conexion = crearConexionPDO();
	
	
	
	$id = $_POST["id"];
	$inmueble = $_POST["inmueble"];
	
	
	if ($usuario != -1) {
		if ($id == 0) {//inserta
			$consulta = "INSERT INTO FAVORITO_INMUEBLE(FIN_USUARIO, FIN_INMUEBLE) VALUES(:usuario, :inmueble);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":usuario" => $usuario, ":inmueble" => $inmueble));
			$id = $conexion->lastInsertId();
		}
		else {//borra
			$consulta = "DELETE FROM FAVORITO_INMUEBLE WHERE FIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$id = 0;
		}
	}
	else {
		$isExito = 0;
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"id"		=>	$id
	);
	
	echo json_encode($arrayRespuesta);
	
?>