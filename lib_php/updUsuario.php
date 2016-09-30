<?php

	require_once("template.php");
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = isset($_POST["id"]) ? $_POST["id"] : -1;
	$nuevo = isset($_POST["nuevo"]) ? 1 : 0;
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	$validarEmail = isset($_POST["validarEmail"]) ? 1 : 0;
	$changePass = isset($_POST["changePass"]) ? 1 : 0;
	$validarCuenta = isset($_POST["validarCuenta"]) ? 1 : 0;
	$solicitudRestablecer = isset($_POST["solicitudRestablecer"]) ? 1 : 0;
	$restablecerPass = isset($_POST["restablecerPass"]) ? 1 : 0;
	$urlArchivos = "../images/images/";
	$conexion = crearConexionPDO();
	
	
	//actualiza el id cuando hay inicio de session
	if (isset($_SESSION[userId]))
		$id = $_SESSION[userId];
	
	
	/*
		Valida que el email no partenecezca a ningun otro usuario
	*/
	if ($validarEmail) {
		$email = $_POST["email"];
		
		$consulta = "SELECT USU_ID FROM USUARIO WHERE USU_EMAIL = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($email));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {
			$isExito = 0;
			$mensaje = "El email ya existe, intente con uno diferente.";
		}
	}
	
	
	/*
		Si el password actual es el que se recibe, realiza el cambio del nuevo password
	*/
	if ($changePass) {
		$oldPass = $_POST["oldPass"];
		$newPass = $_POST["newPass"];
		
		
		$consulta = "SELECT USU_PASSWORD FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		
		if ($row["USU_PASSWORD"] == $oldPass) {
			$consulta = "UPDATE USUARIO SET USU_PASSWORD = :newPass WHERE USU_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":newPass" => $newPass, ":id" => $id));
		}
		else {
			$isExito = 0;
			$mensaje = "El password actual es incorrecto.";
		}
	}
	
	
	/*
		Valida la cuenta del usuario
	*/
	if ($validarCuenta) {
		$partes = explode("_", $_POST["validar"]);
		$id = $partes[0];
		$validado = $partes[1];
		
		
		$consulta = "SELECT USU_VALIDADO FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		if ($row["USU_VALIDADO"] == $validado) {
			$consulta = "UPDATE USUARIO SET USU_VALIDADO = :validado WHERE USU_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":validado" => 1, ":id" => $id));
		}
		else {
			$isExito = 0;
			$mensaje = "La clave es incorrecta, vuelva a consultar la clave de validación.";
		}
	}
	
	
	/*
		Envia un email para recuperacion de password de la cuenta
	*/
	if ($solicitudRestablecer) {
		$email = $_POST["email"];
		
		$consulta = "SELECT USU_ID FROM USUARIO WHERE USU_EMAIL = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($email));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {
			$row = $res[0];
			$codigo = template_generarCadenaAleatoria(8);
			
			$consulta = "UPDATE USUARIO SET USU_RECUPERAR_PW = :codigo WHERE USU_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":codigo" => $codigo, ":id" => $row["USU_ID"]));
			
			
			//enviar el email con el codigo para recuperar
			$cadenaEmail = 
				"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
					<div style='padding:20px;'>
						<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
					</div>
					<div style='background-color:#f6f6f6; padding:20px;'>
						<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
							<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Recuperar Contraseña</h1>
							Hemos recibido una solicitud para restablecer tu contraseña. Si no reconoces esta solicitud, ignorala. De lo contrario haz click en el siguiente link para restablecer tu contraseña.<br /><br />
							<a href='http://www.explorainmuebles.com/restablecerPass.php?recpass=".$codigo."' style='color:#852c2b; font-weight:bold; text-decoration:none;'>Restablecer mi contraseña</a>
						</div>
					</div>
					<div style='text-align:center; padding:20px 0px;'>
						<a href='https://www.facebook.com/explorainmueblesmx' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -30px 0 / 390px 30px;'></a>
						<a href='https://twitter.com/ExploraInmueble' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -60px 0 / 390px 30px;'></a>
						<a href='https://www.youtube.com/channel/UCRf7kJDrVb5-DiSgT3QL5eQ' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones2.png\") no-repeat scroll 0 0 / auto 30px;'></a>
						<a href='https://instagram.com/explora_inmuebles/' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones2.png\") no-repeat scroll -30px 0 / auto 30px;'></a>
						<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -150px 0 / 390px 30px;'></a>
						<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -120px 0 / 390px 30px;'></a>
					</div>
				</div>";
				
				
			$to = $email;
			$subject = "Recuperar Password";
			$message = $cadenaEmail;
			$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
			$header.= "X-Mailer:PHP/".phpversion()."\r\n";
			$header.= "Mime-Version: 1.0"."\r\n";
			$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
			//mail($to, $subject, $message, $header);
            /*** INTENTO DE ENVIO USANDO POSTMARK ***/
            $ch = curl_init();
            $headers = array('Accept: application/json', 'Content-Type: application/json', 'X-Postmark-Server-Token: 5303d5ed-d516-4952-965e-75fed079d160');
            $data = array(
                'From'      => 'contacto@explorainmuebles.com',
                'To'        => $to,
                'Subject'   => $subject,
                'HtmlBody'  => $message.'<br />Enviado desde Postmark'
                );
            $data = json_encode($data);

            curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
		}
		else {
			$isExito = 0;
			$mensaje = "No existe el email, vuelva a intentarlo.";
		}
	}
	
	
	/*
		Restablece el password del usuario que tiene el codigo recibido por email
	*/
	if ($restablecerPass) {
		$codigo = $_POST["codigo"];
		$newPass = $_POST["newPass"];
		
		
		$consulta = "SELECT USU_ID, USU_EMAIL FROM USUARIO WHERE USU_RECUPERAR_PW = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($codigo));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		
		if (count($res) > 0) {
			$row = $res[0];
			$consulta = "UPDATE USUARIO SET USU_PASSWORD = :newPass, USU_RECUPERAR_PW = :codigo WHERE USU_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":newPass" => md5($newPass), ":codigo" => NULL, ":id" => $row["USU_ID"]));
			
			
			//enviar un email con el nuevo password
			$cadenaEmail = 
				"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
					<div style='padding:20px;'>
						<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
					</div>
					<div style='background-color:#f6f6f6; padding:20px;'>
						<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
							<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Contraseña Restablecida</h1>
							Tu nueva contraseña es: ".$newPass."<br />Ahora ya puedes ingresar con tu cuenta de email y tu nueva contraseña a <a href='http://www.explorainmuebles.com/' style='color:#852c2b; font-weight:bold; text-decoration:none;'>Explora Inmuebles</a>
						</div>
					</div>
					<div style='text-align:center; padding:20px 0px;'>
						<a href='https://www.facebook.com/explorainmueblesmx' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -30px 0 / 390px 30px;'></a>
						<a href='https://twitter.com/ExploraInmueble' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -60px 0 / 390px 30px;'></a>
						<a href='https://www.youtube.com/channel/UCRf7kJDrVb5-DiSgT3QL5eQ' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones2.png\") no-repeat scroll 0 0 / auto 30px;'></a>
						<a href='https://instagram.com/explora_inmuebles/' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones2.png\") no-repeat scroll -30px 0 / auto 30px;'></a>
						<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -150px 0 / 390px 30px;'></a>
						<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -120px 0 / 390px 30px;'></a>
					</div>
				</div>";
				
				
			$to = $row["USU_EMAIL"];
			$subject = "Password Restablecido";
			$message = $cadenaEmail;
			$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
			$header.= "X-Mailer:PHP/".phpversion()."\r\n";
			$header.= "Mime-Version: 1.0"."\r\n";
			$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
			//mail($to, $subject, $message, $header);
            /*** INTENTO DE ENVIO USANDO POSTMARK ***/
            $ch = curl_init();
            $headers = array('Accept: application/json', 'Content-Type: application/json', 'X-Postmark-Server-Token: 5303d5ed-d516-4952-965e-75fed079d160');
            $data = array(
                'From'      => 'contacto@explorainmuebles.com',
                'To'        => $to,
                'Subject'   => $subject,
                'HtmlBody'  => $message.'<br />Enviado desde Postmark'
                );
            $data = json_encode($data);

            curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
		}
		else {
			$isExito = 0;
			$mensaje = "El código de recuperacion es incorrecto, vuelva a consultar su email.";
		}
	}
	
	
	/*
		Crea nuevo usuario
	*/
	if ($nuevo) {
		$nombre = $_POST["nombre"];
		$email = $_POST["email"];
		$password = $_POST["password"];
		$FBId = $_POST["FBId"] != "" ? $_POST["FBId"] : NULL;
		$validado = template_generarCadenaAleatoria(12);
		
		
		$consulta = "INSERT INTO USUARIO(USU_NOMBRE, USU_EMAIL, USU_PASSWORD, USU_FBID, USU_CREATE, USU_VALIDADO) VALUES(:nombre, :email, :password, :FBId, NOW(), :validado);";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":nombre" => $nombre, ":email" => $email, ":password" => $password, ":FBId" => $FBId, ":validado" => $validado));
		$id_creado = $conexion->lastInsertId();
		
		
		//se envia un email para que validen su cuenta con un link unico
		$cadenaEmail = 
			"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
				<div style='padding:20px;'>
					<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
				</div>
				<div style='background-color:#f6f6f6; padding:20px;'>
					<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
						<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Valida tu Cuenta</h1><br />
						Bienvenido(a): ".$nombre." a Explora Inmuebles, para poder validar tu cuenta, haz click en este <a href='http://www.explorainmuebles.com/index.php?validar=".$id_creado."_".$validado."' style='color:#852c2b; font-weight:bold; text-decoration:none;'>link</a> y enseguida haz login para comenzar con tu cuenta.
					</div>
				</div>
				<div style='text-align:center; padding:20px 0px;'>
					<a href='https://www.facebook.com/explorainmueblesmx' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -30px 0 / 390px 30px;'></a>
					<a href='https://twitter.com/ExploraInmueble' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -60px 0 / 390px 30px;'></a>
					<a href='https://www.youtube.com/channel/UCRf7kJDrVb5-DiSgT3QL5eQ' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones2.png\") no-repeat scroll 0 0 / auto 30px;'></a>
					<a href='https://instagram.com/explora_inmuebles/' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones2.png\") no-repeat scroll -30px 0 / auto 30px;'></a>
					<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -150px 0 / 390px 30px;'></a>
					<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' style='cursor: pointer; display: inline-block; height: 30px; overflow: hidden; text-align: left; vertical-align: top; width: 30px; margin: 0 10px; background: rgba(0, 0, 0, 0) url(\"http://www.explorainmuebles.com/images/botones.png\") no-repeat scroll -120px 0 / 390px 30px;'></a>
				</div>
			</div>";
		
		
		$to = $email;
		$subject = "Valida tu cuenta";
		$message = $cadenaEmail;
		$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
		$header.= "X-Mailer:PHP/".phpversion()."\r\n";
		$header.= "Mime-Version: 1.0"."\r\n";
		$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
		//mail($to, $subject, $message, $header);
        /*** INTENTO DE ENVIO USANDO POSTMARK ***/
        $ch = curl_init();
        $headers = array('Accept: application/json', 'Content-Type: application/json', 'X-Postmark-Server-Token: 5303d5ed-d516-4952-965e-75fed079d160');
        $data = array(
            'From'      => 'contacto@explorainmuebles.com',
            'To'        => $to,
            'Subject'   => $subject,
            'HtmlBody'  => $message.'<br />Enviado desde Postmark'
            );
        $data = json_encode($data);

        curl_setopt($ch, CURLOPT_URL, 'https://api.postmarkapp.com/email');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
	}
	
	
	/*
		Modifica los datos de usuario
	*/
	if ($modificar) {
		$nombre = $_POST["nombre"];
		$sexo = $_POST["sexo"];
		$fechaNac = $_POST["fechaNac"] != "" ? getDateSQL($_POST["fechaNac"]) : NULL;
		$telefono1 = $_POST["telefono1"];
		$telefono2 = $_POST["telefono2"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"] != "-1" ? $_POST["estado"] : NULL;
		$ciudad = $_POST["ciudad"] != "-1" ? $_POST["ciudad"] : NULL;
		$colonia = $_POST["colonia"] != "-1" ? $_POST["colonia"] : NULL;
		$cp = $_POST["cp"] != "" ? $_POST["cp"] : NULL;
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
		
		
		$consulta = "UPDATE USUARIO SET USU_NOMBRE = :nombre, USU_SEXO = :sexo, USU_FECHANACIMIENTO = :fechaNac, USU_TELEFONO1 = :telefono1, USU_TELEFONO2 = :telefono2, USU_CALLE_NUMERO = :calleNumero, USU_ESTADO = :estado, USU_CIUDAD = :ciudad, USU_COLONIA = :colonia, USU_CP = :cp, USU_IMAGEN = :imagen WHERE USU_ID = :id;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":nombre" => $nombre, ":sexo" => $sexo, ":fechaNac" => $fechaNac, ":telefono1" => $telefono1, ":telefono2" => $telefono2, ":calleNumero" => $calleNumero, ":estado" => $estado, ":ciudad" => $ciudad, ":colonia" => $colonia, ":cp" => $cp, ":imagen" => $newNombreImagen, ":id" => $id));
		
		
		$_SESSION[userNombre] = $nombre;
		if ($newNombreImagen != "")
			$_SESSION[userImagen] = str_replace("../", "", $urlArchivos).$newNombreImagen;
	}
	
	
	$respuesta_json = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje
	);
	
	
	echo json_encode($respuesta_json);

?>