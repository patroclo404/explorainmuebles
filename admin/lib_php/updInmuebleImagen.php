<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$urlArchivos = "../../images/images/";
	
	
	$id = $_POST["id"];
	$nuevo = isset($_POST["nuevo"]) ? $_POST["nuevo"] : 0;
	$modificar = isset($_POST["modificar"]) ? $_POST["modificar"] : 0;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	
	
	if ($borrar) {
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
	
	
	if ($nuevo) {
		$imagenPrincipal = isset($_POST["imagenPrincipal"]) ? 1 : 0;
		$imagen = "imagen";
		$newNombreImagen = "";
		
		
		if ($imagenPrincipal == 1) {
			$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_ORDEN = 0 WHERE IIN_INMUEBLE = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
		}
		

		$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos));
		if ($subirImagenesServidor->isExito == 1)
			$newNombreImagen = $subirImagenesServidor->imagen;

		
		$consulta = "INSERT INTO IMAGEN_INMUEBLE(IIN_INMUEBLE, IIN_IMAGEN, IIN_ORDEN) VALUES(:inmueble, :imagen, :orden);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":inmueble" => $id, ":imagen" => $newNombreImagen, ":orden" => $imagenPrincipal));
	}
	
	
	if ($modificar) {
		$idImagen = $_POST["idImagen"];
		$imagenPrincipal = isset($_POST["imagenPrincipal"]) ? 1 : 0;
		$imagen = "imagen";
		$newNombreImagen = "";
		
		
		$consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_ID = ".$idImagen.";";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idImagen));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];

		$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos, $row["IIN_IMAGEN"]));
		if ($subirImagenesServidor->isExito == 1)
			$newNombreImagen = $subirImagenesServidor->imagen;
		
		
		if ($imagenPrincipal == 1) {
			$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_ORDEN = 0 WHERE IIN_INMUEBLE = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
		}
		
		
		$consulta = "UPDATE IMAGEN_INMUEBLE SET IIN_IMAGEN = :imagen, IIN_ORDEN = :orden WHERE IIN_ID = :idImagen;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":imagen" => $newNombreImagen, ":orden" => $imagenPrincipal, ":idImagen" => $idImagen));
	}
	
	
	$consulta = 
		"SELECT IIN_ID, IIN_IMAGEN, IIN_ORDEN
		FROM IMAGEN_INMUEBLE
		WHERE IIN_INMUEBLE = ?
		ORDER BY IIN_ID DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($id));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["IIN_ID"],
			"campo2"	=>	$row["IIN_IMAGEN"],
			"campo3"	=>	$row["IIN_ORDEN"]
		);
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"datos"		=>	$arrayCampos
	);
	
	echo json_encode($arrayRespuesta);
	
?>