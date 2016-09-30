<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0));
	
	
	$palabra = isset($_POST["palabra"]) ? $_POST["palabra"] : "";
	

	$consulta = "SELECT VEN_ID, VEN_NOMBRE, VEN_EMAIL, VEN_SEXO, VEN_TELEFONO1, VEN_TELEFONO2, VEN_CALLE_NUMERO, VEN_ESTADO, VEN_CIUDAD, VEN_COLONIA, VEN_CP FROM VENDEDOR ".($palabra != "" ? ("WHERE (VEN_NOMBRE LIKE '%".$palabra."%' OR VEN_EMAIL LIKE '%".$palabra."%')") : "")." ORDER BY VEN_ID DESC;";
	$res = crearConsulta($consulta);
	$arrayCampos = array();
	
	while($row = mysql_fetch_array($res)){
		$arrayCampos[] = array(
			"campo1"	=>	$row["VEN_ID"],
			"campo2"	=>	$row["VEN_NOMBRE"],
			"campo3"	=>	$row["VEN_EMAIL"],
			"campo4"	=>	$row["VEN_SEXO"],
			"campo5"	=>	$row["VEN_TELEFONO1"] != NULL ? $row["VEN_TELEFONO1"] : "",
			"campo6"	=>	$row["VEN_TELEFONO2"] != NULL ? $row["VEN_TELEFONO2"] : "",
			"campo7"	=>	$row["VEN_CALLE_NUMERO"] != NULL ? $row["VEN_CALLE_NUMERO"] : "",
			"campo8"	=>	$row["VEN_ESTADO"] != NULL ? $row["VEN_ESTADO"] : "",
			"campo9"	=>	$row["VEN_CIUDAD"] != NULL ? $row["VEN_CIUDAD"] : "",
			"campo10"	=>	$row["VEN_COLONIA"] != NULL ? $row["VEN_COLONIA"] : "",
			"campo11"	=>	$row["VEN_CP"] != NULL ? $row["VEN_CP"] : ""
		);
	}
	
	echo json_encode($arrayCampos);
	
?>