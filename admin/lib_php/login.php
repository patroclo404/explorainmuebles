<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");


	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$email = $_POST["email"];
	$password = $_POST["password"];

	
	$consulta = "SELECT ADM_ID, ADM_NOMBRE, ADM_PASSWORD FROM ADMINISTRADOR WHERE ADM_EMAIL = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($email));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	
	if (count($res) > 0) {
		$row = $res[0];
		
		if ($row["ADM_PASSWORD"] == $password) {
			$_SESSION[adminId] = $row["ADM_ID"];
			$_SESSION[adminLogin] = $row["ADM_NOMBRE"];
			$_SESSION[adminTipo] = 0;//super admin: 0, admin normal, 1
		}
		else {
			$isExito = 0;
			$mensaje = "La información introducida no es correcta.";
		}
	}
	else {
		$isExito = 0;
		$mensaje = "El administrador no existe, verifique la información.";
	}
		
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	echo json_encode($respuesta_json);
	
	
?>