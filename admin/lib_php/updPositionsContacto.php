<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	

	$consulta = "SELECT CON_ID, CON_EMAIL, CON_TELEFONO, CON_WHATSAPP FROM CONTACTO ORDER BY CON_ID DESC;";
	$arrayCampos = array();
	
	foreach($conexion->query($consulta) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["CON_ID"],
			"campo2"	=>	$row["CON_EMAIL"],
			"campo3"	=>	$row["CON_TELEFONO"] != NULL ? $row["CON_TELEFONO"] : "",
			"campo4"	=>	$row["CON_WHATSAPP"] != NULL ? $row["CON_WHATSAPP"] : ""
		);
	}
	
	echo json_encode($arrayCampos);
	
?>