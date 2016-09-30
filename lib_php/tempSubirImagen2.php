<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$urlArchivos = "../images/images/temp/";
	
	
	$newNombreImagen = array();
	
	
	/*
		Borra una imagen de temporal
	*/
	if ($borrar) {
		$imagen = $_POST["imagen"];
		unlink($urlArchivos.$imagen);
	}
	
	
	/*
		Guarda una nueva imagen
	*/
	if ($nuevo) {
		$imagen = "imagen";
		
		for ($x = 0; $x < count($_FILES[$imagen]["name"]); $x++) {
			$_nombreInput = $imagen;
			$_rutaImagen = $urlArchivos;
			
			$_fileArchivo = $_FILES[$_nombreInput]["tmp_name"][$x];  
			$_fileTamanio = $_FILES[$_nombreInput]["size"][$x];
			$_fileTipo    = $_FILES[$_nombreInput]["type"][$x];
			
			$_fp = fopen($_fileArchivo, "r");
			$_contenido = fread($_fp, $_fileTamanio);
			$_contenido = addslashes($_contenido);
					
			$_nombreImagen = basename($_FILES[$_nombreInput]["name"][$x]);
			$_extension = template_extension($_nombreImagen);
			$_newNombreImagen = str_replace(array(" ", "."), "", microtime());
			$_newNombreImagen.= ".".$_extension;
			$_newNombreImagen = strtolower($_newNombreImagen);
				
			move_uploaded_file($_fileArchivo, $_rutaImagen.$_newNombreImagen);
			fclose($_fp);
			
			$newNombreImagen[] = $_newNombreImagen;
		}
	}
	
	
	
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"imagen"	=>	implode(",", $newNombreImagen)
	);
	
	
	echo json_encode($respuesta_json);

?>