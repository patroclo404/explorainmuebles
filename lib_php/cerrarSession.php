<?php

	require_once("template.php");
	
	$tipoCerrarSession = isset($_GET["tipo"]) ? $_GET["tipo"] : 2;//tipo 2 es el usuario final
	
	$_SESSION[cerrarSession] = $tipoCerrarSession;
	
	limpiarSesiones();
	
	switch($tipoCerrarSession) {
		case 0://super administrador
		case 1://administrador
			header("location: ../admin/index.php");
			break;
		case 2://user
			header("location: ../index.php");
			break;
	}

?>