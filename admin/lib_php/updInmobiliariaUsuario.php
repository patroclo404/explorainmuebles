<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	
	
	if ($modificar) {
		$usuarios = $_POST["usuarios"];
		
		
		$consulta = "UPDATE USUARIO SET USU_INMOBILIARIA = null WHERE USU_INMOBILIARIA = :id AND (SELECT INM_USUARIO FROM INMOBILIARIA WHERE INM_ID = :id) != USU_ID;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":id" => $id));
		
		
		if ($usuarios != "") {
			$usuarios = explode(",", $usuarios);
			
			for ($x = 0; $x < count($usuarios); $x++) {
				$consulta = "UPDATE USUARIO SET USU_INMOBILIARIA = :inmobiliaria WHERE USU_ID = :usuId;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":inmobiliaria" => $id, ":usuId" => $usuarios[$x]));
			}
		}
	}
	
	
	$consulta = 
		"SELECT
			USU_ID,
			USU_NOMBRE,
			USU_EMAIL,
			USU_FBID,
			USU_SEXO,
			USU_FECHANACIMIENTO,
			USU_TELEFONO1,
			USU_TELEFONO2,
			USU_CALLE_NUMERO,
			USU_ESTADO,
			USU_CIUDAD,
			USU_COLONIA,
			USU_CP,
			USU_IMAGEN,
			USU_NOTIFICACIONES
		FROM USUARIO
		WHERE USU_INMOBILIARIA = ?
		ORDER BY USU_NOMBRE;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($id));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"id"					=>	$row["USU_ID"],
			"nombre"				=>	$row["USU_NOMBRE"],
			"email"					=>	$row["USU_EMAIL"],
			"FBId"					=>	$row["USU_FBID"] != NULL ? $row["USU_FBID"] : "",
			"sexo"					=>	$row["USU_SEXO"],
			"fechaNac"				=>	$row["USU_FECHANACIMIENTO"] != NULL ? getDateNormal($row["USU_FECHANACIMIENTO"]) : "",
			"telefono1"				=>	$row["USU_TELEFONO1"] != NULL ? $row["USU_TELEFONO1"] : "",
			"telefono2"				=>	$row["USU_TELEFONO2"] != NULL ? $row["USU_TELEFONO2"] : "",
			"calleNumero"			=>	$row["USU_CALLE_NUMERO"] != NULL ? $row["USU_CALLE_NUMERO"] : "",
			"estado"				=>	$row["USU_ESTADO"] != NULL ? $row["USU_ESTADO"] : "",
			"ciudad"				=>	$row["USU_CIUDAD"] != NULL ? $row["USU_CIUDAD"] : "",
			"colonia"				=>	$row["USU_COLONIA"] != NULL ? $row["USU_COLONIA"] : "",
			"cp"					=>	$row["USU_CP"] != NULL ? $row["USU_CP"] : "",
			"imagen"				=>	$row["USU_IMAGEN"] != NULL ? $row["USU_IMAGEN"] : "",
			"notificaciones"		=>	$row["USU_NOTIFICACIONES"]
		);
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"datos"		=>	$arrayCampos
	);
	
	echo json_encode($arrayRespuesta);
	
?>