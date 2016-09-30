<?php

	require_once("template.php");
	$conexion = crearConexionPDO();
	
	
	$body = @file_get_contents('php://input');
	$event_json = json_decode($body, true);
	
	
	if ($event_json["data"]["object"]["status"] == "paid") {
		$tipo_idPago = $event_json["data"]["object"]["reference_id"];
		$partes = explode("_", $tipo_idPago);
		$tipo = $partes[0];
		$idPago = $partes[1];
		
		
		$datosFacturacion = array();
		$tituloEmail = "";
		$textoEmail = "";
		
		
		if ($tipo == "INM") {//inmobiliaria
			$consulta = "UPDATE INMOBILIARIA, PAGO_INMOBILIARIA SET INM_VALIDEZ = PIN_VALIDEZ, INM_CREDITOS = PIN_CREDITOS WHERE PIN_INMOBILIARIA = INM_ID AND PIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			
			
			$consulta = "UPDATE PAGO_INMOBILIARIA SET PIN_IS_PAGADO = 1 WHERE PIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			
			
			$consulta = "SELECT PIN_CREDITOS, PIN_INMOBILIARIA, PIN_VALIDEZ FROM PAGO_INMOBILIARIA WHERE PIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$arrayInmobiliaria = array(
				"id"		=>	$row["PIN_INMOBILIARIA"],
				"validez"	=>	$row["PIN_VALIDEZ"],
				"creditos"	=>	$row["PIN_CREDITOS"]
			);
			
			
			$consulta =
				"SELECT COUNT(IMU_ID) AS CONS_ACTIVOS
				FROM INMUEBLE, USUARIO
				WHERE IMU_USUARIO = USU_ID
				AND USU_INMOBILIARIA = ".$arrayInmobiliaria["id"]."
				AND IMU_LIMITE_VIGENCIA > CURDATE();";
			$pdo = $conexion->query($consulta);
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$_numActivos = $row["CONS_ACTIVOS"];
			$_numDesactivar = 0;
			$_contDesactivar = 0;
			
			if ($arrayInmobiliaria["creditos"] < $_numActivos) {
				$_numDesactivar = $_numActivos - $arrayInmobiliaria["creditos"];
				$_numDesactivar = $_numDesactivar < 0 ? 0 : $_numDesactivar;
			}
			
			
			$timeStamp_hoy1 = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
			$consulta =
				"SELECT IMU_ID
				FROM INMUEBLE, USUARIO
				WHERE IMU_USUARIO = USU_ID
				AND USU_INMOBILIARIA = ".$arrayInmobiliaria["id"]."
				AND IMU_LIMITE_VIGENCIA > CURDATE()
				ORDER BY IMU_CREATE DESC;";
			foreach($conexion->query($consulta) as $row) {
				if ($_contDesactivar < $_numDesactivar) {
					$consulta2 = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
					$pdo2 = $conexion->prepare($consulta2);
					$pdo2->execute(array(":limiteVigencia" => date("Y-m-d", $timeStamp_hoy1), ":id" => $row["IMU_ID"]));
					$_contDesactivar++;
				}
			}
			
			
			//datos para el email
			$consulta =
				"SELECT PIN_CREDITOS, PIN_TOTAL, PIN_VALIDEZ, INM_NOMBRE_EMPRESA, USU_EMAIL
				FROM PAGO_INMOBILIARIA, INMOBILIARIA, USUARIO
				WHERE PIN_INMOBILIARIA = INM_ID
				AND INM_USUARIO = USU_ID
				AND PIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			
			$datosFacturacion = array(
				"nombre"	=>	$row["INM_NOMBRE_EMPRESA"],
				"email"		=>	$row["USU_EMAIL"],
				"creditos"	=>	$row["PIN_CREDITOS"],
				"total"		=>	$row["PIN_TOTAL"],
				"validez"	=>	getDateNormal($row["PIN_VALIDEZ"])
			);
			
			
			$tituloEmail = "Activación de tu plan";
			$textoEmail =
				"Hola ".$datosFacturacion["nombre"]." tu plan ha sido activado exitosamente.<br />Tienes posibilidad de publicar ".$datosFacturacion["creditos"]." anuncios simultáneamente y<br />la fecha de vencimiento de tu plan es del día ".$datosFacturacion["validez"]."<br />por un costo de $".$datosFacturacion["total"].".<br /><br />Gracias por anunciarte en Explora Inmuebles.";
		}
		else {//usuario - inmueble
			$timestamp_fecha = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
		
		
			$consulta =
				"SELECT IMU_LIMITE_VIGENCIA
				FROM PAGO_INMUEBLE, INMUEBLE
				WHERE PIM_INMUEBLE = IMU_ID
				AND PIM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			
			
			if ($row["IMU_LIMITE_VIGENCIA"] != "2000-01-01") {
				$partes = explode("-", $row["IMU_LIMITE_VIGENCIA"]);
				$limiteVigenciaInmueble = mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
				
				if ($limiteVigenciaInmueble > mktime(0, 0, 0, date("m"), date("d"), date("Y")))
					$timestamp_fecha = mktime(0, 0, 0, $partes[1], $partes[2]+30, $partes[0]);
			}
		
			$consulta = "UPDATE PAGO_INMUEBLE SET PIM_IS_PAGADO = 1 WHERE PIM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			
			
			$consulta = "UPDATE INMUEBLE, PAGO_INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE PIM_INMUEBLE = IMU_ID AND PIM_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":limiteVigencia" => date("Y-m-d", $timestamp_fecha), ":id" => $idPago));
			
			
			//datos para el email
			$consulta =
				"SELECT IMU_TITULO, IMU_LIMITE_VIGENCIA, USU_NOMBRE, USU_EMAIL
				FROM PAGO_INMUEBLE, INMUEBLE, USUARIO
				WHERE PIM_INMUEBLE = IMU_ID
				AND IMU_USUARIO = USU_ID
				AND PIM_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			
			$datosFacturacion = array(
				"nombre"	=>	$row["IMU_TITULO"],
				"email"		=>	$row["USU_EMAIL"],
				"usuario"	=>	$row["USU_NOMBRE"],
				"validez"	=>	getDateNormal($row["IMU_LIMITE_VIGENCIA"])
			);
			
			
			$tituloEmail = "Tu anuncio ".$datosFacturacion["nombre"]." ha sido publicado exitosamente";
			$textoEmail = 
				"Hola ".$datosFacturacion["usuario"]." tu anuncio ".$datosFacturacion["nombre"]." ha sido<br />publicado exitosamente y vencerá el día ".$datosFacturacion["validez"].".<br /><br />Agradecemos que hayas publicado tu anuncio en Explora Inmuebles.";
		}
		
		
		///////////////////////////////////////////////////////////////////////////////////////////////
		//						envio de email de notificacion de pago recibido
		//////////////////////////////////////////////////////////////////////////////////////////////
		$cadenaEmail = 
			"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
				<div style='padding:20px;'>
					<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
				</div>
				<div style='background-color:#f6f6f6; padding:20px;'>
					<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
						<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>".$tituloEmail."</h1>
						".$textoEmail."
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
			
			
		$to = $datosFacturacion["email"];
		$subject = "Pago Recibido";
		$message = $cadenaEmail;
		$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
		$header.= "Bcc: ventas@explorainmuebles.com"."\r\n";
		$header.= "X-Mailer:PHP/".phpversion()."\r\n";
		$header.= "Mime-Version: 1.0"."\r\n";
		$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
		mail($to, $subject, $message, $header);
	}
	
?>