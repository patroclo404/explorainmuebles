<?php


	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$urlArchivos = "../images/images/";
	
	
	
	
	
	if($borrar){
		$idImagen = $_POST["idImagen"];
		
		
		$consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idImagen));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		unlink($urlArchivos.$row["IIN_IMAGEN"]);
		
		$consulta = "DELETE FROM IMAGEN_INMUEBLE WHERE IIN_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idImagen));
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	echo json_encode($arrayRespuesta);
	
?>