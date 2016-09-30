<?php


	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	$urlArchivos = "../images/images/";
	
	
	if($borrar){
		//borrar
	}
	
	
	if ($nuevo) {
		$titulo = $_POST["titulo"];
		$tipo = $_POST["tipo"];
		$unidades = $_POST["unidades"] != "" ? $_POST["unidades"] : NULL;
		$entrega = $_POST["entrega"] != "" ? $_POST["entrega"] : NULL;
		$latitud = $_POST["latitud"];
		$longitud = $_POST["longitud"];
		$descripcion = $_POST["descripcion"];
		$inmobiliaria = $_SESSION[userInmobiliaria];
		
		
		$imagen = $_POST["imagen"];
		$imagenPrincipal = $_POST["imagenPrincipal"];
		
		
		$consulta = "INSERT INTO DESARROLLO(DES_TITULO, DES_TIPO, DES_ENTREGA, DES_UNIDADES, DES_LATITUD, DES_LONGITUD, DES_DESCRIPCION, DES_INMOBILIARIA) VALUES(:titulo, :tipo, :entrega, :unidades, :latitud, :longitud, :descripcion, :inmobiliaria);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":titulo" => $titulo, ":tipo" => $tipo, ":entrega" => $entrega, ":unidades" => $unidades, ":latitud" => $latitud, ":longitud" => $longitud, ":descripcion" => $descripcion, ":inmobiliaria" => $inmobiliaria));
		$id_creado = $conexion->lastInsertId();
		
		
		//sube una o varias imagenes para el inmueble
		if ($imagen != "") {
			$imagenes = explode(",", $imagen);
			$imagenPrincipal = explode(",", $imagenPrincipal);
			$urlArchivosTemp = $urlArchivos."temp/";
			
			for ($x = 0; $x < count($imagenes); $x++) {
				rename($urlArchivosTemp.$imagenes[$x], $urlArchivos.$imagenes[$x]);
				
				$consulta = "INSERT INTO IMAGEN_DESARROLLO(IDE_DESARROLLO, IDE_IMAGEN, IDE_ORDEN) VALUES(:desarrollo, :imagen, :orden);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":desarrollo" => $id_creado, ":imagen" => $imagenes[$x], ":orden" => $imagenPrincipal[$x]));
			}
		}
	}
	
	
	if ($modificar) {
		$titulo = $_POST["titulo"];
		$tipo = $_POST["tipo"];
		$unidades = $_POST["unidades"] != "" ? $_POST["unidades"] : NULL;
		$entrega = $_POST["entrega"] != "" ? $_POST["entrega"] : NULL;
		$latitud = $_POST["latitud"];
		$longitud = $_POST["longitud"];
		$descripcion = $_POST["descripcion"];
		$inmobiliaria = $_SESSION[userInmobiliaria];
		
		
		$imagen = $_POST["imagen"];
		$imagenPrincipal = $_POST["imagenPrincipal"];
		$idImagenPrincipal = $_POST["idImagenPrincipal"];
		
		
		$consulta = "UPDATE DESARROLLO SET DES_TITULO = :titulo, DES_TIPO = :tipo, DES_ENTREGA = :entrega, DES_UNIDADES = :unidades, DES_LATITUD = :latitud, DES_LONGITUD = :longitud, DES_DESCRIPCION = :descripcion, DES_INMOBILIARIA = :inmobiliaria WHERE DES_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":titulo" => $titulo, ":tipo" => $tipo, ":entrega" => $entrega, ":unidades" => $unidades, ":latitud" => $latitud, ":longitud" => $longitud, ":descripcion" => $descripcion, ":inmobiliaria" => $inmobiliaria, ":id" => $id));
		
		
		//deja en limpio la imagen principal (luego se seleccionara por id o por una de las nuevas)
		$consulta = "UPDATE IMAGEN_DESARROLLO SET IDE_ORDEN = 0 WHERE IDE_DESARROLLO = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		
		
		//cambio de imagen principal por una de las imagenes que ya estan arriba
		if ($idImagenPrincipal != "") {
			$consulta = "UPDATE IMAGEN_DESARROLLO SET IDE_ORDEN = :orden WHERE IDE_ID = :idImagen;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":orden" => 1, ":idImagen" => $idImagenPrincipal));
		}
		
		
		//sube una o varias imagenes para el inmueble
		if ($imagen != "") {
			$imagenes = explode(",", $imagen);
			$imagenPrincipal = explode(",", $imagenPrincipal);
			$urlArchivosTemp = $urlArchivos."temp/";
			
			for ($x = 0; $x < count($imagenes); $x++) {
				rename($urlArchivosTemp.$imagenes[$x], $urlArchivos.$imagenes[$x]);
				
				$consulta = "INSERT INTO IMAGEN_DESARROLLO(IDE_DESARROLLO, IDE_IMAGEN, IDE_ORDEN) VALUES(:desarrollo, :imagen, :orden);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":desarrollo" => $id, ":imagen" => $imagenes[$x], ":orden" => $imagenPrincipal[$x]));
			}
		}
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>