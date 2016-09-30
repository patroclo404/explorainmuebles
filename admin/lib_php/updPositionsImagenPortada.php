<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	

	$consulta =
		"SELECT
			IMP_ID,
			IMP_IMAGEN,
			IMP_ORDEN,
			IMP_TEXTO,
			IMP_TEXTO_PRINCIPAL,
			IMP_TEXTO_SECUNDARIO,
			(
				IF (
					IMP_ORDEN > 0,
					IMP_ORDEN,
					1000000000
				)
			) AS CONS_ORDEN
		FROM IMAGEN_PORTADA
		ORDER BY CONS_ORDEN;";
	$arrayCampos = array();
	
	foreach($conexion->query($consulta) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["IMP_ID"],
			"campo2"	=>	$row["IMP_IMAGEN"],
			"campo3"	=>	$row["IMP_ORDEN"],
			"campo4"	=>	$row["IMP_TEXTO"],
			"campo5"	=>	$row["IMP_TEXTO_PRINCIPAL"] != NULL ? $row["IMP_TEXTO_PRINCIPAL"] : "",
			"campo6"	=>	$row["IMP_TEXTO_SECUNDARIO"] != NULL ? $row["IMP_TEXTO_SECUNDARIO"] : ""
		);
	}
	
	echo json_encode($arrayCampos);
	
?>