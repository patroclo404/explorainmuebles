<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	$conexion = crearConexionPDO();
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$validarEmail = isset($_POST["validarEmail"]) ? 1 : 0;
	$validarFBid = isset($_POST["validarFBid"]) ? 1 : 0;
	$chgPassword = isset($_POST["chgPassword"]) ? 1 : 0;
	$urlArchivos = "../../images/images/";
	
	
	/*
		Si entra a esta opcion; unicamente valida el email y devuelve un resultado si es unico
		o si esta utilizado por el mismo o diferente usuario
	*/
	if ($validarEmail) {
		$email = $_POST["email"];

		
		$consulta = "SELECT USU_ID FROM USUARIO WHERE USU_EMAIL = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($email));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {//si existe
			$row = $res[0];
		
			if ($row["USU_ID"] != $id) {//pertenece a otro usuario
				$mensaje = "El email ya existe, intente con uno diferente.";
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
		Si entra a esta opcion; unicamente valida el fbid y devuelve un resultado si es unico
		o si esta utilizado por el mismo o diferente usuario
	*/
	if ($validarFBid) {
		$FBid = $_POST["FBid"];

		
		$consulta = "SELECT USU_ID FROM USUARIO WHERE USU_FBID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($FBid));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {//si existe
			$row = $res[0];
		
			if ($row["USU_ID"] != $id) {//pertenece a otro usuario
				$mensaje = "El FBid ya existe, intente con uno diferente.";
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
		Cambia el password
	*/
	if ($chgPassword) {
		$password = $_POST["password"];

		
		$consulta = "UPDATE USUARIO SET USU_PASSWORD = :password WHERE USU_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":password" => $password, ":id" => $id));
		
		$arrayRespuesta = array(
			"mensaje"		=>	$mensaje,
			"isExito"		=>	$isExito
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	if($borrar){
		$consulta = "SELECT INM_ID FROM INMOBILIARIA WHERE INM_USUARIO = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		if (count($res) > 0) {//no puede borrar porque es administrador de una inmobiliaria
			$isExito = 0;
			$mensaje = "No se puede borrar el usuario, ya que es administrador de una Inmobiliaria.";
		}
		else {
			//si el usuario pertenece a una inmobiliaria; cambias sus anunciones para que pertenezcan al administrador de la inmobiliaria
			$consulta = "SELECT USU_INMOBILIARIA FROM USUARIO WHERE USU_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			
			if ($row["USU_INMOBILIARIA"] != NULL) {
				
				$consulta2 = "SELECT INM_USUARIO FROM INMOBILIARIA WHERE INM_ID = ?;";
				$pdo2 = $conexion->prepare($consulta2);
				$pdo2->execute(array($row["USU_INMOBILIARIA"]));
				$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
				$row2 = $res2[0];
				$adminInmobiliaria = $row2["INM_USUARIO"];
				
				$consulta2 = "UPDATE INMUEBLE SET IMU_USUARIO = :adminInmobiliaria WHERE IMU_USUARIO = :id;";
				$pdo2 = $conexion->prepare($consulta2);
				$pdo2->execute(array(":adminInmobiliaria" => $adminInmobiliaria, ":id" => $id));
			}
			
			$consulta = "SELECT USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$_imagen = $row["USU_IMAGEN"] != NULL ? $row["USU_IMAGEN"] : "";
			if ($_imagen != "") {
				unlink($urlArchivos.$_imagen);
			}
			
			
			$consulta = "DELETE FROM USUARIO WHERE USU_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
		}
	}
	else{
		$nombre = $_POST["nombre"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		$FBid = $_POST["FBid"] != "" ? $_POST["FBid"] : NULL;
		$sexo = $_POST["sexo"];
		$fechaNac = $_POST["fechaNac"] != "" ? getDateSQL($_POST["fechaNac"]) : NULL;
		$telefono1 = $_POST["telefono1"];
		$telefono2 = $_POST["telefono2"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"] != "-1" ? $_POST["estado"] : NULL;
		$ciudad = isset($_POST["ciudad"]) ? ($_POST["ciudad"] != -1 ? $_POST["ciudad"] : NULL) : NULL;
		$colonia = isset($_POST["colonia"]) ? ($_POST["ciudad"] != -1 ? $_POST["colonia"] : NULL) : NULL;
		$cp = $_POST["cp"] != "" ? $_POST["cp"] : NULL;
		$notificaciones = isset($_POST["notificaciones"]) ? 1 : 0;
		$imagen = "imagen";
		$newNombreImagen = "";
		$inmobiliaria = isset($_POST["inmobiliaria"]) ? $_POST["inmobiliaria"] : NULL;
		
		
		if($id != -1){
			$consulta = "SELECT USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($id));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$_imagen = $row["USU_IMAGEN"] != NULL ? $row["USU_IMAGEN"] : "";
			$newNombreImagen = $_imagen;
			
			$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos, $_imagen));
			if ($subirImagenesServidor->isExito == 1)
				$newNombreImagen = $subirImagenesServidor->imagen;
			
			
			$consulta = "UPDATE USUARIO SET USU_NOMBRE = :nombre, USU_EMAIL = :email, USU_FBID = :FBId, USU_SEXO = :sexo, USU_FECHANACIMIENTO = :fechaNac, USU_TELEFONO1 = :telefono1, USU_TELEFONO2 = :telefono2, USU_CALLE_NUMERO = :calleNumero, USU_ESTADO = :estado, USU_CIUDAD = :ciudad, USU_COLONIA = :colonia, USU_CP = :cp, USU_IMAGEN = :imagen, USU_NOTIFICACIONES = :notificaciones WHERE USU_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":FBId" => $FBid, ":sexo" => $sexo, ":fechaNac" => $fechaNac, ":telefono1" => $telefono1, ":telefono2" => $telefono2, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":imagen" => $newNombreImagen, ":notificaciones" => $notificaciones, ":id" => $id));
		}
		else{
			$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos));
			if ($subirImagenesServidor->isExito == 1)
				$newNombreImagen = $subirImagenesServidor->imagen;
			
			
			$consulta = "INSERT INTO USUARIO(USU_NOMBRE, USU_EMAIL, USU_PASSWORD, USU_FBID, USU_SEXO, USU_FECHANACIMIENTO, USU_TELEFONO1, USU_TELEFONO2, USU_CALLE_NUMERO, USU_ESTADO, USU_CIUDAD, USU_COLONIA, USU_CP, USU_INMOBILIARIA, USU_IMAGEN, USU_CREATE, USU_VALIDADO, USU_NOTIFICACIONES) VALUES(:nombre, :email, :password, :FBId, :sexo, :fechaNac, :telefono1, :telefono2, :calleNumero, :estado, :ciudad, :colonia, :cp, :inmobiliaria, :imagen, NOW(), :validado, :notificaciones);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":password" => $password, ":FBId" => $FBid, ":sexo" => $sexo, ":fechaNac" => $fechaNac, ":telefono1" => $telefono1, ":telefono2" => $telefono2, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":inmobiliaria" => $inmobiliaria, ":imagen" => $newNombreImagen, ":validado" => 1, ":notificaciones" => $notificaciones));
			$id_creado = $conexion->lastInsertId();
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>