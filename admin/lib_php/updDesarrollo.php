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
	$urlArchivos = "../../images/images/";
	
	
	if($borrar){
		$consulta = "SELECT IDE_IMAGEN FROM IMAGEN_DESARROLLO WHERE IDE_DESARROLLO = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			unlink($urlArchivos.$row["IDE_IMAGEN"]);
		}
		
		
		$consulta = "UPDATE INMUEBLE SET IMU_DESARROLLO = null WHERE IMU_DESARROLLO = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		
		
		$consulta = "DELETE FROM DESARROLLO WHERE DES_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
	}
	else{
		$titulo = $_POST["titulo"];
		$tipo = $_POST["tipo"];
		$entrega = $_POST["entrega"] != "" ? $_POST["entrega"] : NULL;
		$unidades = $_POST["unidades"] != "" ? $_POST["unidades"] : NULL;
		$latitud = $_POST["latitud"];
		$longitud = $_POST["longitud"];
		$descripcion = $_POST["descripcion"];
		$inmobiliaria = $_POST["inmobiliaria"];
		
		
		if($id != -1){
			$consulta = "UPDATE DESARROLLO SET DES_TITULO = :titulo, DES_TIPO = :tipo, DES_ENTREGA = :entrega, DES_UNIDADES = :unidades, DES_LATITUD = :latitud, DES_LONGITUD = :longitud, DES_DESCRIPCION = :descripcion, DES_INMOBILIARIA = :inmobiliaria WHERE DES_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":titulo" => $titulo, ":tipo" => $tipo, ":entrega" => $entrega, ":unidades" => $unidades, ":latitud" => $latitud, ":longitud" => $longitud, ":descripcion" => $descripcion, ":inmobiliaria" => $inmobiliaria, ":id" => $id));
		}
		else{
			$consulta = "INSERT INTO DESARROLLO(DES_TITULO, DES_TIPO, DES_ENTREGA, DES_UNIDADES, DES_LATITUD, DES_LONGITUD, DES_DESCRIPCION, DES_INMOBILIARIA) VALUES(:titulo, :tipo, :entrega, :unidades, :latitud, :longitud, :descripcion, :inmobiliaria);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":titulo" => $titulo, ":tipo" => $tipo, ":entrega" => $entrega, ":unidades" => $unidades, ":latitud" => $latitud, ":longitud" => $longitud, ":descripcion" => $descripcion, ":inmobiliaria" => $inmobiliaria));
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