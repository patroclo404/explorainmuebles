<?php

	require_once("template.php");
	$conexion = crearConexionPDO();
	
	
	$arrayInmuebles = array();
	$consulta =
		"SELECT IMU_ID, IMU_TITULO, IMU_LIMITE_VIGENCIA, IMU_USUARIO, USU_NOMBRE, USU_EMAIL
		FROM INMUEBLE, USUARIO
		WHERE IMU_USUARIO = USU_ID
		AND IMU_LIMITE_VIGENCIA > ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y")))));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$partes = explode("-", $row["IMU_LIMITE_VIGENCIA"]);
		$timeStamp_hoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$timeStamp_inmueble_3dias = mktime(0, 0, 0, $partes[1], $partes[2]+3, $partes[0]);
		$timeStamp_inmueble_6dias = mktime(0, 0, 0, $partes[1], $partes[2]+6, $partes[0]);
		
		if ((date("Y-m-d") == $row["IMU_LIMITE_VIGENCIA"]) || ($timeStamp_hoy == $timeStamp_inmueble_3dias) || ($timeStamp_hoy == $timeStamp_inmueble_6dias)) {
			$arrayInmuebles[] = array(
				"id"			=>	$row["IMU_ID"],
				"titulo"		=>	$row["IMU_TITULO"],
				"validez"		=>	$row["IMU_LIMITE_VIGENCIA"],
				"usuario"		=>	array(
					"id"		=>	$row["IMU_USUARIO"],
					"nombre"	=>	$row["USU_NOMBRE"],
					"email"		=>	$row["USU_EMAIL"]
				),
			);
		}
	}
	
	
	for($x = 0; $x < count($arrayInmuebles); $x++) {
		$cadenaEmail = 
			"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
				<div style='padding:20px;'>
					<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
				</div>
				<div style='background-color:#f6f6f6; padding:20px;'>
					<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
						<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Renueva tu anuncio ".$arrayInmuebles[$x]["titulo"]."</h1>
						 Hola ".$arrayInmuebles[$x]["usuario"]["nombre"]." tu anuncio ".$arrayInmuebles[$x]["titulo"]."<br />venció el día ".getDateNormal($arrayInmuebles[$x]["validez"])."<br /><br />Para renovar tu anuncio haz click <a href='http://www.explorainmuebles.com/misAnuncios.php' target='_blank' style='color:#852c2b; font-weight:bold; text-decoration:none;'>aquí</a>.
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
			
			
		$to = $arrayInmuebles[$x]["usuario"]["email"];
		$subject = "Inmueble ha vencido";
		$message = $cadenaEmail;
		$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
		$header.= "Bcc: ventas@explorainmuebles.com"."\r\n";
		$header.= "X-Mailer:PHP/".phpversion()."\r\n";
		$header.= "Mime-Version: 1.0"."\r\n";
		$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
		mail($to, $subject, $message, $header);
	}

?>