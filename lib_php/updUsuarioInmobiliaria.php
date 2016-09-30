<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	$validarEmail = isset($_POST["validarEmail"]) ? 1 : 0;
	$changePass = isset($_POST["changePass"]) ? 1 : 0;
	$urlArchivos = "../images/images/";
	$conexion = crearConexionPDO();
	
	
	/*
		Valida que el email no partenecezca a ningun otro usuario
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
	}
	
	
	/*
		Si el password actual es el que se recibe, realiza el cambio del nuevo password
	*/
	if ($changePass) {
		$password = $_POST["password"];
		
		
		$consulta = "UPDATE USUARIO SET USU_PASSWORD = :password WHERE USU_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":password" => $password, ":id" => $id));
	}
	
	
	/*
		Borra un usuario y cambiar los anuncios que le pertenecen, al administrador de la inmobiliaria a la que pertenece
	*/
	if ($borrar) {
		$consulta = "UPDATE INMUEBLE SET IMU_USUARIO = :userId WHERE IMU_USUARIO = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":userId" => $_SESSION[userId], ":id" => $id));

		
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
	
	
	/*
		Crea nuevo usuario
	*/
	if ($nuevo) {
		$nombre = $_POST["nombre"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		$sexo = $_POST["sexo"];
		$fechaNac = $_POST["fechaNac"] != "" ? getDateSQL($_POST["fechaNac"]) : NULL;
		$telefono1 = $_POST["telefono1"];
		$telefono2 = $_POST["telefono2"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"] != "-1" ? $_POST["estado"] : NULL;
		$ciudad = $_POST["ciudad"] != "-1" ? $_POST["ciudad"] : NULL;
		$colonia = $_POST["colonia"] != "-1" ? $_POST["colonia"] : NULL;
		$cp = $_POST["cp"] != "" ? $_POST["cp"] : NULL;
		$notificaciones = isset($_POST["notificaciones"]) ? 1 : 0;
		$imagen = "imagen";
		$newNombreImagen = "";
		
		
		$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos));
		if ($subirImagenesServidor->isExito == 1)
			$newNombreImagen = $subirImagenesServidor->imagen;
		
		
		$consulta = "INSERT INTO USUARIO(USU_NOMBRE, USU_EMAIL, USU_PASSWORD, USU_SEXO, USU_FECHANACIMIENTO, USU_TELEFONO1, USU_TELEFONO2, USU_CALLE_NUMERO, USU_ESTADO, USU_CIUDAD, USU_COLONIA, USU_CP, USU_INMOBILIARIA, USU_IMAGEN, USU_CREATE, USU_VALIDADO, USU_NOTIFICACIONES) VALUES(:nombre, :email, :password, :sexo, :fechaNac, :telefono1, :telefono2, :calleNumero, :estado, :ciudad, :colonia, :cp, :userInmobiliaria, :imagen, NOW(), :validado, :notificaciones);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":password" => $password, ":sexo" => $sexo, ":fechaNac" => $fechaNac, ":telefono1" => $telefono1, ":telefono2" => $telefono2, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":userInmobiliaria" => $_SESSION[userInmobiliaria], ":imagen" => $newNombreImagen, ":validado" => 1, ":notificaciones" => $notificaciones));
	}
	
	
	/*
		Modifica los datos de usuario
	*/
	if ($modificar) {
		$nombre = $_POST["nombre"];
		$email = $_POST["email"];
		$sexo = $_POST["sexo"];
		$fechaNac = $_POST["fechaNac"] != "" ? getDateSQL($_POST["fechaNac"]) : NULL;
		$telefono1 = $_POST["telefono1"];
		$telefono2 = $_POST["telefono2"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"] != "-1" ? $_POST["estado"] : NULL;
		$ciudad = $_POST["ciudad"] != "-1" ? $_POST["ciudad"] : NULL;
		$colonia = $_POST["colonia"] != "-1" ? $_POST["colonia"] : NULL;
		$cp = $_POST["cp"] != "" ? $_POST["cp"] : NULL;
		$notificaciones = isset($_POST["notificaciones"]) ? 1 : 0;
		$imagen = "imagen";
		$newNombreImagen = "";
		
		
		$consulta = "SELECT USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$_imagen = $row["USU_IMAGEN"] != NULL ? $row["USU_IMAGEN"] : "";
		
		$subirImagenesServidor = json_decode(template_subirImagenesServidor($imagen, $urlArchivos, $newNombreImagen));
		if ($subirImagenesServidor->isExito == 1) {
			$newNombreImagen = $subirImagenesServidor->imagen;
		}
		
		
		$consulta = "UPDATE USUARIO SET USU_NOMBRE = :nombre, USU_EMAIL = :email, USU_SEXO = :sexo, USU_FECHANACIMIENTO = :fechaNac, USU_TELEFONO1 = :telefono1, USU_TELEFONO2 = :telefono2, USU_CALLE_NUMERO = :calleNumero, USU_ESTADO = :estado, USU_CIUDAD = :ciudad, USU_COLONIA = :colonia, USU_CP = :cp, USU_IMAGEN = :imagen, USU_NOTIFICACIONES = :notificaciones WHERE USU_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":sexo" => $sexo, ":fechaNac" => $fechaNac, ":telefono1" => $telefono1, ":telefono2" => $telefono2, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":imagen" => $newNombreImagen, ":notificaciones" => $notificaciones, ":id" => $id));
	}
	
	
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	
	echo json_encode($respuesta_json);

?>