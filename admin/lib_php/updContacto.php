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
		$email = $_POST["email"];
		$telefono = $_POST["telefono"];
		$whatsapp = $_POST["whatsapp"];
		
		
		if($id != -1){
			$consulta = "UPDATE CONTACTO SET CON_EMAIL = :email, CON_TELEFONO = :telefono, CON_WHATSAPP = :whatsapp WHERE CON_ID = :id";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":email" => $email, ":telefono" => $telefono, ":whatsapp" => $whatsapp, ":id" => $id));
		}
		else{
			$consulta = "INSERT INTO CONTACTO(CON_EMAIL, CON_TELEFONO, CON_WHATSAPP) VALUES(:email, :telefono, :whatsapp);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":email" => $email, ":telefono" => $telefono, ":whatsapp" => $whatsapp));
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