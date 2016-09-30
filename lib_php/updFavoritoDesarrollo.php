<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	$conexion = crearConexionPDO();

	
	$id = $_POST["id"];
	$desarrollo = $_POST["desarrollo"];
	
	
	if ($usuario != -1) {
		if ($id == 0) {//inserta
			$consulta = "INSERT INTO FAVORITO_DESARROLLO(FDE_USUARIO, FDE_DESARROLLO) VALUES(:usuario, :desarrollo);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":usuario" => $usuario, ":desarrollo" => $desarrollo));
			$id = $conexion->lastInsertId();
		}
		else {//borra
			$consulta = "DELETE FROM FAVORITO_DESARROLLO WHERE FDE_ID = ?;";
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