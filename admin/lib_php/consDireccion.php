<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$consCiudad = isset($_POST["consCiudad"]) ? 1 : 0;
	$consColonia = isset($_POST["consColonia"]) ? 1 : 0;
	
	
	/*
		Consulta las ciudades a partir del estado recibido por parametro
	*/
	if ($consCiudad) {
		$estado = $_POST["estado"];
		$arrayCiudades = array();
		
		
		$conexion = crearConexionPDO();
		$consulta = "SELECT CIU_ID, CIU_NOMBRE FROM CIUDAD WHERE CIU_ESTADO = ? ORDER BY CIU_NOMBRE;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($estado));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$arrayCiudades[] = array(
				"id"		=>	$row["CIU_ID"],
				"nombre"	=>	$row["CIU_NOMBRE"]
			);
		}
		
		
		$arrayRespuesta = array(
			"isExito"	=>	$isExito,
			"mensaje"	=>	$mensaje,
			"datos"		=>	$arrayCiudades
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	/*
		Consulta las colonias a partir de la ciudad recibido por parametro
		
			* conResultados:	[Integer], muestra aquellas colonias que solamente tienen resultados para la ciudad recibida por parametros
	*/
	if ($consColonia) {
		$ciudad = $_POST["ciudad"];
		$conResultados = isset($_POST["conResultados"]) ? 1 : 0;
		$arrayColonias = array();
		$consulta = "";
		
		$conexion = crearConexionPDO();
		if ($conResultados == 1) {//solo aquellas que tienen resultados en esa colonia
			$consulta =
				"SELECT DISTINCT COL_ID, COL_NOMBRE, COL_CP, CP_CP
				FROM COLONIA, CP, INMUEBLE
				WHERE COL_CP = CP_ID
				AND CP_CIUDAD = ?
				AND IMU_COLONIA = COL_ID
				ORDER BY COL_NOMBRE;";
		}
		else {//todas las colonias de la ciudad
			$consulta =
				"SELECT COL_ID, COL_NOMBRE, COL_CP, CP_CP
				FROM COLONIA, CP
				WHERE COL_CP = CP_ID
				AND CP_CIUDAD = ?
				ORDER BY COL_NOMBRE;";
		}
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($ciudad));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$arrayColonias[] = array(
				"id"		=>	$row["COL_ID"],
				"nombre"	=>	$row["COL_NOMBRE"],
				"cp"		=>	$row["COL_CP"],
				"cpValue"	=>	$row["CP_CP"]
			);
		}
		
		
		$arrayRespuesta = array(
			"isExito"	=>	$isExito,
			"mensaje"	=>	$mensaje,
			"datos"		=>	$arrayColonias
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
?>