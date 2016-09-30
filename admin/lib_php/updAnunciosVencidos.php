<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	
	
	
	if($borrar){
	}
	else {
		$limiteVigencia = getDateSQL($_POST["limiteVigencia"]);
		
		
		if($id != -1){
			$consulta = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":limiteVigencia" => $limiteVigencia, ":id" => $id));
		}
		else {
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>