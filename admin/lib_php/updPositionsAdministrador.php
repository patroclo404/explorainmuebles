<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	

	$conexion = crearConexionPDO();
	$arrayCampos = array();
	$consulta = "SELECT ADM_ID, ADM_NOMBRE, ADM_EMAIL FROM ADMINISTRADOR ORDER BY ADM_ID DESC;";
	foreach($conexion->query($consulta) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["ADM_ID"],
			"campo2"	=>	$row["ADM_NOMBRE"],
			"campo3"	=>	$row["ADM_EMAIL"]
		);
	}
	
	echo json_encode($arrayCampos);
	
?>