<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$id = -1;
	$nuevo = 1;
	
	
	if ($nuevo) {
		$inmueble = $_POST["inmueble"] != -1 ? $_POST["inmueble"] : NULL;
		$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
		$razonReporte = $_POST["razonReporte"];
		$comentarios = $_POST["comentarios"];
		$desarrollo = $_POST["desarrollo"] != -1 ? $_POST["desarrollo"] : NULL;
		
		
		if ($usuario != -1) {
			$consulta = "INSERT INTO REPORTE(REP_INMUEBLE, REP_USUARIO, REP_RAZON_REPORTE, REP_COMENTARIOS, REP_DESARROLLO) VALUES(:inmueble, :usuario, :razonReporte, :comentarios, :desarrollo);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":inmueble" => $inmueble, ":usuario" => $usuario, ":razonReporte" => $razonReporte, ":comentarios" => $comentarios, ":desarrollo" => $desarrollo));
		}
		else {
			$isExito = 0;
			$mensaje = "Inicia sesión o regístrate para enviar tu reporte del anuncio.";
		}
	}
	
	
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	
	echo json_encode($respuesta_json);

?>