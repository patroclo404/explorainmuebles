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
	$chgPassword = isset($_POST["chgPassword"]) ? 1 : 0;
	$validarOldPass = isset($_POST["validarOldPass"]) ? 1 : 0;
	
	
	/*
		Si entra a esta opcion; unicamente valida el email y devuelve un resultado si es unico
		o si esta utilizado por el mismo o diferente administrador
	*/
	if ($validarEmail) {
		$email = $_POST["email"];
		$isLibre = 1;
		
		$consulta = "SELECT ADM_ID FROM ADMINISTRADOR WHERE ADM_EMAIL = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($email));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {//si existe
			$row = $res[0];
		
			if ($row["ADM_ID"] != $id) {//pertenece a otro administrador
				$mensaje = "El email ya existe, intente con uno diferente.";
				$isLibre = 0;
			}
		}
		
		$arrayRespuesta = array(
			"mensaje"		=>	$mensaje,
			"isLibre"		=>	$isLibre
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	if($borrar){
		$consulta = "DELETE FROM ADMINISTRADOR WHERE ADM_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
	}
	else{
		$nombre = $_POST["nombre"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		
		
		if($id != -1){
			if ($chgPassword) {
				if ($validarOldPass) {
					$oldPass = $_POST["oldPass"];
					
					$consulta = 
						"SELECT IF(
							ADM_PASSWORD = :oldPass,
							1, 0
						) AS CONS_OLD_PASS
						FROM ADMINISTRADOR
						WHERE ADM_ID = :id;";
					$pdo = $conexion->prepare($consulta);
					$pdo->execute(array(":oldPass" => $oldPass, ":id" => $id));
					$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
					$row = $res[0];
					if ($row["CONS_OLD_PASS"] == 0) {//la contraseña anterior no es la misma que esta actualmente
						$arrayRespuesta = array(
							"mensaje"		=>	"La contraseña anterior no es la misma.",
							"isExito"		=>	0
						);
						
						echo json_encode($arrayRespuesta);
						return;
					}
				}
				
				$consulta = "UPDATE ADMINISTRADOR SET ADM_PASSWORD = :password WHERE ADM_ID = :id;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":password" => $password, ":id" => $id));
			}
			else {
				$consulta = "UPDATE ADMINISTRADOR SET ADM_NOMBRE = :nombre, ADM_EMAIL = :email WHERE ADM_ID = :id;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":id" => $id));
			}
		}
		else {
			$consulta = "INSERT INTO ADMINISTRADOR(ADM_NOMBRE, ADM_EMAIL, ADM_PASSWORD) VALUES(:nombre, :email, :password);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":password" => $password));
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