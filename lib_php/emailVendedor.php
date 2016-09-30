<?php

	require_once("template.php");
	$conexion = crearConexionPDO();
	
	
	$nombre = $_POST["nombre"];
	$email = $_POST["email"];
	$telefono = $_POST["telefono"];
	$mensaje = htmlspecialchars($_POST["mensaje"]);
	$inmueble = $_POST["inmueble"];
	
	
	$consulta = "UPDATE INMUEBLE SET IMU_CONT_CONTACTADO = IMU_CONT_CONTACTADO + 1 WHERE IMU_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($inmueble));
	
	
	$cadenaEmail = 
		"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
			<div style='padding:20px;'>
				<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
			</div>
			<div style='background-color:#f6f6f6; padding:20px;'>
				<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
					<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Contacto Vendedor</h1><br />
					Nombre: ".$nombre."<br />Email: ".$email."<br />Teléfono: ".$telefono."<br />Mensaje: ".$mensaje.
				"</div>
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
	
	
	$consulta =
		"SELECT USU_ID, USU_EMAIL, USU_INMOBILIARIA, USU_NOTIFICACIONES
		FROM INMUEBLE, USUARIO
		WHERE IMU_USUARIO = USU_ID
		AND IMU_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($inmueble));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$to = $row["USU_EMAIL"];
	
	
	if ($row["USU_INMOBILIARIA"] != NULL) {
		if ($row["USU_NOTIFICACIONES"] == 0)
			$to = "";
		
		$consulta2 =
			"SELECT USU_ID, USU_EMAIL
			FROM INMOBILIARIA, USUARIO
			WHERE INM_USUARIO = USU_ID
			AND INM_ID = ?;";
		$pdo2 = $conexion->prepare($consulta2);
		$pdo2->execute(array($row["USU_INMOBILIARIA"]));
		$res2 = $pdo2->fetchAll(PDO::FETCH_ASSOC);
		$row2 = $res2[0];
		if ($row2["USU_ID"] != $row["USU_ID"]) {
			if ($to != "")
				$to.= ",".$row2["USU_EMAIL"];
			else
				$to = $row2["USU_EMAIL"];
		}
	}
	
	
	
	$subject = "Contacto Vendedor";
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

?>