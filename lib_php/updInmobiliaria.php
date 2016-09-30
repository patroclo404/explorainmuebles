<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$id = $_POST["id"];
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	$validarNombreEmpresa = isset($_POST["validarNombreEmpresa"]) ? 1 : 0;
	$validarRFC = isset($_POST["validarRFC"]) ? 1 : 0;
	$urlArchivos = "../images/images/";
	
	
	/*
		Si entra a esta opcion; unicamente valida el nombre de la empresa y devuelve un resultado si es unico
		o si esta utilizado por el mismo o diferente inmobiliaria
	*/
	if ($validarNombreEmpresa) {
		$nombreEmpresa = $_POST["nombreEmpresa"];

		
		$consulta = "SELECT INM_ID FROM INMOBILIARIA WHERE INM_NOMBRE_EMPRESA = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($nombreEmpresa));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		if ($row["INM_ID"] != "") {//si existe
			if ($row["INM_ID"] != $id) {//pertenece a otra inmobiliaria
				$mensaje = "El nombre de la empresa ya existe, intente con uno diferente.";
				$isExito = 0;
			}
		}
		
		$arrayRespuesta = array(
			"mensaje"		=>	$mensaje,
			"isExito"		=>	$isExito
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	/*
		Valida el rfc
	*/
	if ($validarRFC) {
		$rfc = $_POST["rfc"];

		
		$consulta = "SELECT INM_ID FROM INMOBILIARIA WHERE INM_RFC = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($rfc));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		if ($row["INM_ID"] != "") {//si existe
			if ($row["INM_ID"] != $id) {//pertenece a otra inmobiliaria
				$mensaje = "El rfc ya existe, intente con uno diferente.";
				$isExito = 0;
			}
		}
		
		$arrayRespuesta = array(
			"mensaje"		=>	$mensaje,
			"isExito"		=>	$isExito
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	/*
		Modifica los datos de usuario
	*/
	if ($modificar) {
		$nombreEmpresa = $_POST["nombreEmpresa"];
		$rfc = $_POST["rfc"] != "" ? "'".$_POST["rfc"]."'" : "null";
		$logotipo = "logotipo";
		$newNombreLogotipo = "";
		
		
		$consulta = "SELECT INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$_imagen = $row["INM_LOGOTIPO"];
		
		$subirImagenesServidor = json_decode(template_subirImagenesServidor($logotipo, $urlArchivos, $_imagen));
		if ($subirImagenesServidor->isExito == 1) {
			$newNombreLogotipo = $subirImagenesServidor->imagen;
		}
		
		
		$consulta = "UPDATE INMOBILIARIA SET INM_NOMBRE_EMPRESA = :nombreEmpresa, INM_RFC = :rfc, INM_LOGOTIPO = :logotipo WHERE INM_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":nombreEmpresa" => $nombreEmpresa, ":rfc" => $rfc, ":logotipo" => $newNombreLogotipo, ":id" => $id));
	}
	
	
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	
	echo json_encode($respuesta_json);

?>