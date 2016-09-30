<?php

	require_once("template.php");
	
	
	$isExito = 1;
	
	
	$set = isset($_POST["set"]) ? 1 : 0;
	$updateParam = isset($_POST["updateParam"]) ? 1 : 0;
	
	
	/*
		modifica todos los filtros
	*/
	if ($set) {
		$arrayParametros = $_POST["parametros"];
		
		
		$_SESSION[userFiltros] = $arrayParametros;
	}
	
	
	/*
		agrega/modifica un parametro en especifico 
	*/
	if ($updateParam) {
		$nombre = $_POST["nombre"];
		$valor = $_POST["valor"];
		$arrayParametros = isset($_SESSION[userFiltros]) ? $_SESSION[userFiltros] : array();
		
		$arrayParametros[$nombre] = $valor;
		
		$_SESSION[userFiltros] = $arrayParametros;
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito
	);
	
	echo json_encode($arrayRespuesta);
	
?>