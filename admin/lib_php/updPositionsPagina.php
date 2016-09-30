<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	

	$consulta = "SELECT PAG_ID, PAG_TITULO, PAG_CONTENIDO FROM PAGINA ORDER BY PAG_ID DESC;";
	$arrayCampos = array();
	
	foreach($conexion->query($consulta) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["PAG_ID"],
			"campo2"	=>	$row["PAG_TITULO"],
			"campo3"	=>	$row["PAG_CONTENIDO"] != NULL ? $row["PAG_CONTENIDO"] : ""
		);
	}
	
	echo json_encode($arrayCampos);
	
?>