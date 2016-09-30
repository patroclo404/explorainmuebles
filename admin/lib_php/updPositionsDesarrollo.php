<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	$consulta = "SELECT DES_ID, DES_TITULO, DES_TIPO, DES_ENTREGA, DES_UNIDADES, DES_LATITUD, DES_LONGITUD, DES_DESCRIPCION, DES_INMOBILIARIA FROM DESARROLLO ORDER BY DES_ID DESC;";
	$arrayCampos = array();
	
	foreach($conexion->query($consulta) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["DES_ID"],
			"campo2"	=>	$row["DES_TITULO"],
			"campo3"	=>	$row["DES_TIPO"],
			"campo4"	=>	$row["DES_ENTREGA"] != NULL ? $row["DES_ENTREGA"] : "",
			"campo5"	=>	$row["DES_UNIDADES"] != NULL ? $row["DES_UNIDADES"] : "",
			"campo6"	=>	$row["DES_LATITUD"],
			"campo7"	=>	$row["DES_LONGITUD"],
			"campo8"	=>	$row["DES_DESCRIPCION"],
			"campo9"	=>	$row["DES_INMOBILIARIA"]
		);
	}

	echo json_encode($arrayCampos);
	
?>