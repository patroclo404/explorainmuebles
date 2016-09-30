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
		$titulo = $_POST["titulo"];
		$contenido = $_POST["contenido"];
		
		
		if($id != -1){
			$consulta = "UPDATE PAGINA SET PAG_TITULO = :titulo, PAG_CONTENIDO = :contenido WHERE PAG_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":titulo" => $titulo, ":contenido" => $contenido, ":id" => $id));
		}
		else{
			$consulta = "INSERT INTO PAGINA(PAG_TITULO, PAG_CONTENIDO) VALUES(:titulo, :contenido);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":titulo" => $titulo, ":contenido" => $contenido));
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