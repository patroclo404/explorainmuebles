<?php

	require_once("template.php");


	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$FBId = $_POST["FBId"];
	$isFBId = $FBId != "" ? 1 : 0;
	
	
	$email = $_POST["email"];
	$password = $_POST["password"];
	
	
	/*
		El inicio de session fue con facebook, por lo tanto, valida si existe, si no lo crea.
		Si existe el usuario con el mismo email actualiza el FBId
	*/
	if ($isFBId) {
		$nombre = $_POST["nombre"];
		
		
		//busco si existe el usuario por fbid
		$conexion = crearConexionPDO();
		$consulta = "SELECT USU_ID, USU_NOMBRE, USU_INMOBILIARIA FROM USUARIO WHERE USU_FBID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($FBId));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {//si existe por lo tanto hace login
			$row = $res[0];
			$isAdmin = 0;
		
			if ($row["USU_INMOBILIARIA"] != NULL) {//consulta si es el admin de la inmobiliaria
				$consulta2 = "SELECT INM_USUARIO FROM INMOBILIARIA WHERE INM_ID = ?;";
				$pdo2 = $conexion->prepare($consulta2);
				$pdo2->execute(array($row["USU_INMOBILIARIA"]));
				$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
				$row2 = $res2[0];
				$isAdmin = $row["USU_ID"] == $row2["INM_USUARIO"] ? 1 : 0;
			}
		
			$_SESSION[userId] = $row["USU_ID"];
			$_SESSION[userNombre] = $row["USU_NOMBRE"];
			$_SESSION[userImagen] = "https://graph.facebook.com/".$FBId."/picture?type=large";
			$_SESSION[userInmobiliaria] = $row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : 0;
			$_SESSION[userAdminInmobiliaria] = $isAdmin;
		}
		else {//no existe por fbid
			$consulta = "SELECT USU_ID, USU_NOMBRE, USU_INMOBILIARIA FROM USUARIO WHERE USU_EMAIL = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($email));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			
			if (count($res) > 0) {//si existe por email, por lo tango actualiza el fbid con el email y hace login
				$row = $res[0];
				$consulta = "UPDATE USUARIO SET USU_FBID = :FBId WHERE USU_ID = :usu_id;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":FBId" => $FBId, ":usu_id" => $row["USU_ID"]));
				
				$isAdmin = 0;
		
				if ($row["USU_INMOBILIARIA"] != NULL) {//consulta si es el admin de la inmobiliaria
					$consulta2 = "SELECT INM_USUARIO FROM INMOBILIARIA WHERE INM_ID = ?;";
					$pdo2 = $conexion->prepare($consulta2);
					$pdo2->execute(array($row["USU_INMOBILIARIA"]));
					$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
					$row2 = $res2[0];
					$isAdmin = $row["USU_ID"] == $row2["INM_USUARIO"] ? 1 : 0;
				}
			
				
				$_SESSION[userId] = $row["USU_ID"];
				$_SESSION[userNombre] = $row["USU_NOMBRE"];
				$_SESSION[userImagen] = "https://graph.facebook.com/".$FBId."/picture?type=large";
				$_SESSION[userInmobiliaria] = $row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : 0;
				$_SESSION[userAdminInmobiliaria] = $isAdmin;
			}
			else {//no existe ni por fbid ni por email; por lo tanto manda al registro
				$consulta = "INSERT INTO USUARIO(USU_NOMBRE, USU_EMAIL, USU_PASSWORD, USU_FBID, USU_CREATE, USU_VALIDADO) VALUES(:nombre, :email, :password, :FBId, NOW(), :validado);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":password" => $password, ":FBId" => $FBId, ":validado" => 1));
				$id_creado = $conexion->lastInsertId();
				
				$_SESSION[userId] = $id_creado;
				$_SESSION[userNombre] = $nombre;
				$_SESSION[userImagen] = "https://graph.facebook.com/".$FBId."/picture?type=large";
				$_SESSION[userInmobiliaria] = 0;
				$_SESSION[userAdminInmobiliaria] = 0;
			}
		}
		
		
		$respuesta_json = array(
			"isExito"	=>	$isExito,
			"mensaje"	=>	$mensaje
		);
		
		echo json_encode($respuesta_json);
		return;
	}

	
	/*
		Inicio de session normal: user, pass
	*/
	$conexion = crearConexionPDO();
	$consulta = "SELECT USU_ID, USU_NOMBRE, USU_PASSWORD, USU_INMOBILIARIA, USU_IMAGEN, USU_VALIDADO FROM USUARIO WHERE USU_EMAIL = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($email));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
	if (count($res) > 0) {
		$row = $res[0];
		
		if ($row["USU_ID"] != "") {
			if ($row["USU_VALIDADO"] == "1") {
				if ($row["USU_PASSWORD"] == $password) {
					$isAdmin = 0;
				
					if ($row["USU_INMOBILIARIA"] != NULL) {//consulta si es el admin de la inmobiliaria
						$consulta2 = "SELECT INM_USUARIO FROM INMOBILIARIA WHERE INM_ID = ?;";
						$pdo2 = $conexion->prepare($consulta2);
						$pdo2->execute(array($row["USU_INMOBILIARIA"]));
						$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
						$row2 = $res2[0];
						$isAdmin = $row["USU_ID"] == $row2["INM_USUARIO"] ? 1 : 0;
					}
					
					
					$_SESSION[userId] = $row["USU_ID"];
					$_SESSION[userNombre] = $row["USU_NOMBRE"];
					$_SESSION[userInmobiliaria] = $row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : 0;
					$_SESSION[userAdminInmobiliaria] = $isAdmin;
					if ($row["USU_IMAGEN"] != "")
						$_SESSION[userImagen] = "images/images/".$row["USU_IMAGEN"];
				}
				else {
					$isExito = 0;
					$mensaje = "La información introducida no es correcta.";
				}
			}
			else {
				$isExito = 0;
				$mensaje = "Es necesario validar su cuenta, consulte su cuenta de correo e ingrese por medio del link de validación.";
			}
		}
		else {
			$isExito = 0;
			$mensaje = "No tiene los permisos necesarios para acceder a esta página.";
		}
	}
	else {
		$isExito = 0;
		$mensaje = "La información introducida no es correcta.";
	}
		
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	echo json_encode($respuesta_json);
	
	
?>