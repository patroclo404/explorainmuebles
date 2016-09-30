<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$validarNombreEmpresa = isset($_POST["validarNombreEmpresa"]) ? 1 : 0;
	$validarRFC = isset($_POST["validarRFC"]) ? 1 : 0;
	$urlArchivos = "../../images/images/";
	
	
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
		
		if (count($res) > 0) {//si existe
			$row = $res[0];
			
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
		
		if (count($res) > 0) {//si existe
			$row = $res[0];
		
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
	
	
	if($borrar){
		$consulta = "SELECT COUNT(PIN_ID) AS CONS_CONTADOR FROM PAGO_INMOBILIARIA WHERE PIN_INMOBILIARIA = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		
		if ($row["CONS_CONTADOR"] == 0) {
			$consulta = "SELECT INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			if ($row["INM_LOGOTIPO"] != NULL) {
				$_imagen = $row["INM_LOGOTIPO"];
				unlink($urlArchivos.$_imagen);
			}
			
			
			$consulta = "UPDATE USUARIO SET USU_INMOBILIARIA = null WHERE USU_INMOBILIARIA = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			
			
			$consulta = "DELETE FROM INMOBILIARIA WHERE INM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
		}
		else {
			$isExito = 0;
			$mensaje = "La Inmobiliaria cuenta con pagos, por lo tanto no se puede borrar.";
		}
	}
	else{
		$nombreEmpresa = $_POST["nombreEmpresa"];
		$rfc = $_POST["rfc"] != "" ? $_POST["rfc"] : NULL;
		$usuario = $_POST["usuario"];
		$validez = getDateSQL($_POST["validez"]);
		$creditos = $_POST["creditos"];
		$logotipo = "logotipo";
		$newNombreLogotipo = "";
		
		
		if($id != -1){
			$consulta = "SELECT INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$_imagen = $row["INM_LOGOTIPO"];
			$newNombreLogotipo = $_imagen;

			$subirImagenesServidor = json_decode(template_subirImagenesServidor($logotipo, $urlArchivos, $_imagen));
			if ($subirImagenesServidor->isExito == 1)
				$newNombreLogotipo = $subirImagenesServidor->imagen;
			
			
			$consulta = "UPDATE USUARIO SET USU_INMOBILIARIA = :inmobiliaria WHERE USU_ID = :usuario;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":inmobiliaria" => $id, ":usuario" => $usuario));
			
			
			$consulta = "UPDATE INMOBILIARIA SET INM_NOMBRE_EMPRESA = :nombreEmpresa, INM_RFC = :rfc, INM_LOGOTIPO = :logotipo, INM_USUARIO = :usuario, INM_VALIDEZ = :validez, INM_CREDITOS = :creditos WHERE INM_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":nombreEmpresa" => $nombreEmpresa, ":rfc" => $rfc, ":logotipo" => $newNombreLogotipo, ":usuario" => $usuario, ":validez" => $validez, ":creditos" => $creditos, ":id" => $id));
		}
		else{
			$subirImagenesServidor = json_decode(template_subirImagenesServidor($logotipo, $urlArchivos));
			if ($subirImagenesServidor->isExito == 1)
				$newNombreLogotipo = $subirImagenesServidor->imagen;
			
			
			$consulta = "INSERT INTO INMOBILIARIA(INM_NOMBRE_EMPRESA, INM_RFC, INM_LOGOTIPO, INM_USUARIO, INM_CREATE, INM_VALIDEZ, INM_CREDITOS) VALUES(:nombreEmpresa, :rfc, :logotipo, :usuario, NOW(), :validez, :creditos);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":nombreEmpresa" => $nombreEmpresa, ":rfc" => $rfc, ":logotipo" => $newNombreLogotipo, ":usuario" => $usuario, ":validez" => $validez, ":creditos" => $creditos));
			$id_creado = $conexion->lastInsertId();
			
			
			$consulta = "UPDATE USUARIO SET USU_INMOBILIARIA = :inmobiliaria WHERE USU_ID = :usuario;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":inmobiliaria" => $id_creado, ":usuario" => $usuario));
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>