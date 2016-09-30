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
	else{
		$precio = $_POST["precio"];
		$promocion = $_POST["promocion"];
		
		
		if($id != -1){
			$consulta = "UPDATE PROMOCION SET PRO_PRECIO = :precio, PRO_PROMOCION = :promocion WHERE PRO_ID = :id";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":precio" => $precio, ":promocion" => $promocion, ":id" => $id));
		}
		else{
			$consulta = "INSERT INTO PROMOCION(PRO_PRECIO, PRO_PROMOCION) VALUES(:precio, :promocion);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":precio" => $precio, ":promocion" => $promocion));
			$id_creado = $conexion->lastInsertId();
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>