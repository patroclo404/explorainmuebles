<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	$validarEmail = isset($_POST["validarEmail"]) ? 1 : 0;
	$chgPassword = isset($_POST["chgPassword"]) ? 1 : 0;
	
	
	/*
		Si entra a esta opcion; unicamente valida el email y devuelve un resultado si es unico
		o si esta utilizado por el mismo o diferente vendedor
	*/
	if ($validarEmail) {
		$email = $_POST["email"];

		
		$consulta = "SELECT VEN_ID FROM VENDEDOR WHERE VEN_EMAIL = '".$email."';";
		$res = crearConsulta($consulta);
		$row = mysql_fetch_row($res);
		if ($row[0] != "") {//si existe
			if ($row[0] != $id) {//pertenece a otro usuario
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
	
	
	if($borrar){
		$consulta = "DELETE FROM VENDEDOR WHERE VEN_ID = ".$id.";";
		crearConsulta($consulta);
	}
	else{
		$nombre = $_POST["nombre"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		$sexo = $_POST["sexo"];
		$telefono1 = $_POST["telefono1"];
		$telefono2 = $_POST["telefono2"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"] != "-1" ? $_POST["estado"] : "null";
		$ciudad = $_POST["ciudad"] != "-1" ? $_POST["ciudad"] : "null";
		$colonia = $_POST["colonia"] != "-1" ? $_POST["colonia"] : "null";
		$cp = $_POST["cp"] != "" ? $_POST["cp"] : "null";
		
		
		if($id != -1){
			if ($chgPassword) {
				$consulta = "UPDATE VENDEDOR SET VEN_PASSWORD = '".$password."' WHERE VEN_ID = ".$id.";";
				crearConsulta($consulta);
			}
			else {
				$consulta = "UPDATE VENDEDOR SET VEN_NOMBRE = '".$nombre."', VEN_EMAIL = '".$email."', VEN_SEXO = '".$sexo."', VEN_TELEFONO1 = '".$telefono1."', VEN_TELEFONO2 = '".$telefono2."', VEN_CALLE_NUMERO = '".$calleNumero."', VEN_ESTADO = ".$estado.", VEN_CIUDAD = ".$ciudad.", VEN_COLONIA = ".$colonia.", VEN_CP = ".$cp." WHERE VEN_ID = ".$id.";";
				crearConsulta($consulta);
			}
		}
		else{
			$consulta = "INSERT INTO VENDEDOR(VEN_NOMBRE, VEN_EMAIL, VEN_PASSWORD, VEN_SEXO, VEN_TELEFONO1, VEN_TELEFONO2, VEN_CALLE_NUMERO, VEN_ESTADO, VEN_CIUDAD, VEN_COLONIA, VEN_CP) VALUES('".$nombre."', '".$email."', '".$password."', '".$sexo."', '".$telefono1."', '".$telefono2."', '".$calleNumero."', ".$estado.", ".$ciudad.", ".$colonia.", ".$cp.");";
			crearConsulta($consulta);
			$id_creado = mysql_insert_id();
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>