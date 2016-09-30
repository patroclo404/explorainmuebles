<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	
	
	/*
		Genera un nuevo pago con precio cero en el pago del inmueble para usuario
	*/
	if ($nuevo) {
		$idInmueble = $_POST["idInmueble"];
		$timestamp_fecha = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
		
		
		$consulta = "INSERT INTO PAGO_INMUEBLE(PIM_TOTAL, PIM_IS_PAGADO, PIM_TIPO, PIM_FECHA_HORA, PIM_FACTURACION, PIM_INMUEBLE) VALUES(:total, 1, 1, NOW(), :facturacion, :inmueble);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":total" => 0, ":facturacion" => "", ":inmueble" => $idInmueble));
		
		
		$consulta = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":limiteVigencia" => date("Y-m-d", $timestamp_fecha), ":id" => $idInmueble));
	}
	
	
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	
	echo json_encode($respuesta_json);

?>