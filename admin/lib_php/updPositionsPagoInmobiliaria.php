<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	

	$consulta = "SELECT PIN_ID, PIN_CREDITOS, PIN_TOTAL, PIN_VALIDEZ, PIN_IS_PAGADO, PIN_TIPO, PIN_FECHA_HORA, PIN_INMOBILIARIA FROM PAGO_INMOBILIARIA ORDER BY PIN_ID DESC;";
	$arrayCampos = array();
	
	foreach($conexion->query($consulta) as $row) {
		$partes = explode(" ", $row["PIN_FECHA_HORA"]);
		
		$arrayCampos[] = array(
			"campo1"	=>	$row["PIN_ID"],
			"campo2"	=>	$row["PIN_CREDITOS"],
			"campo3"	=>	$row["PIN_TOTAL"],
			"campo4"	=>	getDateNormal($row["PIN_VALIDEZ"]),
			"campo5"	=>	$row["PIN_IS_PAGADO"],
			"campo6"	=>	$row["PIN_TIPO"],
			"campo7"	=>	getDateNormal($partes[0]),
			"campo8"	=>	$partes[1],
			"campo9"	=>	$row["PIN_INMOBILIARIA"]
		);
	}
	
	echo json_encode($arrayCampos);
	
?>