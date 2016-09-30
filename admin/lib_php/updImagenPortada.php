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
		$consulta = "SELECT IMP_IMAGEN FROM IMAGEN_PORTADA WHERE IMP_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$_imagen = $row["IMP_IMAGEN"];
		
		if ($_imagen != "") {
			unlink($urlArchivos.$_imagen);
		}
		
		
		$consulta = "DELETE FROM IMAGEN_PORTADA WHERE IMP_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
	}
	else{
		$orden = $_POST["orden"];
		$texto = $_POST["texto"];
		$textoPrincipal = $_POST["textoPrincipal"];
		$textoSecundario = $_POST["textoSecundario"];
		$imagen = "imagen";
		$newNombreImagen = "";
		
		
		if($id != -1){
			$consulta = "SELECT IMP_IMAGEN FROM IMAGEN_PORTADA WHERE IMP_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$newNombreImagen = $row["IMP_IMAGEN"];

			$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos, $newNombreImagen));
			if ($subirImagenesServidor->isExito == 1)
				$newNombreImagen = $subirImagenesServidor->imagen;
			
			
			$consulta = "UPDATE IMAGEN_PORTADA SET IMP_IMAGEN = :imagen, IMP_ORDEN = :orden, IMP_TEXTO = :texto, IMP_TEXTO_PRINCIPAL = :textoPrincipal, IMP_TEXTO_SECUNDARIO = :textoSecundario WHERE IMP_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":imagen" => $newNombreImagen, ":orden" => $orden, ":texto" => $texto, ":textoPrincipal" => $textoPrincipal, ":textoSecundario" => $textoSecundario, ":id" => $id));
		}
		else{
			$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos));
			if ($subirImagenesServidor->isExito == 1)
				$newNombreImagen = $subirImagenesServidor->imagen;
			
			
			$consulta = "INSERT INTO IMAGEN_PORTADA(IMP_IMAGEN, IMP_ORDEN, IMP_TEXTO, IMP_TEXTO_PRINCIPAL, IMP_TEXTO_SECUNDARIO) VALUES(:imagen, :orden, :texto, :textoPrincipal, :textoSecundario);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":imagen" => $newNombreImagen, ":orden" => $orden, ":texto" => $texto, ":textoPrincipal" => $textoPrincipal, ":textoSecundario" => $textoSecundario));
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