<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	

	$consulta = "SELECT PRO_ID, PRO_PRECIO, PRO_PROMOCION FROM PROMOCION ORDER BY PRO_ID DESC;";
	$arrayCampos = array();
	
	foreach($conexion->query($consulta) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["PRO_ID"],
			"campo2"	=>	$row["PRO_PRECIO"],
			"campo3"	=>	$row["PRO_PROMOCION"] != NULL ? $row["PRO_PROMOCION"] : ""
		);
	}
	
	echo json_encode($arrayCampos);
	
?>