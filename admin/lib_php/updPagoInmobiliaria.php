<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	/**********************************************************************************************************
		Envia email para link de conecta
	**********************************************************************************************************/
	
	
	/*
		Enviar el email para link de conekta a la inmoviliaria
	*/
	function _enviarEmailConekta($idPago) {
		$isExito = 1;
		$mensaje = "Los datos se han actualizado de manera correcta.";
		$conexion = crearConexionPDO();
		
		
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
		$nombreInmobiliaria = $row["INM_NOMBRE_EMPRESA"];
		$creditos = $row["PIN_CREDITOS"];
		$total = $row["PIN_TOTAL"];
		$validez = getDateNormal($row["PIN_VALIDEZ"]);
		$to = $row["USU_EMAIL"];
		
		$partes = explode("/", $validez);
		$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		
		
		$cadenaEmail = 
			"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
				<div style='padding:20px;'>
					<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
				</div>
				<div style='background-color:#f6f6f6; padding:20px;'>
					<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
						<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Pago de plan de anuncios</h1><br />
						Hola ".$nombreInmobiliaria." hemos generado una orden de pago para<br />tu plan en Explora Inmuebles. Con este plan podrás publicar ".$creditos." anuncios simultáneamente y la fecha de vencimiento de tu plan será el<br />día ".$partes[0]." de ".$arrayMeses[(int)$partes[1] - 1]." del ".$partes[2]." por un costo de $".$total.".<br /><br />Para completar el pago de tu plan, haz click en el siguiente botón<br /><br /><a href='http://www.explorainmuebles.com/pagoInmobiliaria.php?idPago=".$idPago."' target='_blank' style='color:#852c2b; font-weight:bold; text-decoration:none;'>PAGAR</a>
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
				
				
		$subject = "Pago de Inmobiliaria";
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
		
		
		$arrayRespuesta = array(
			"mensaje"		=>	$mensaje,
			"isExito"		=>	$isExito
		);
		
		echo json_encode($arrayRespuesta);
		return;
	}
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$id_creado = -1;
	$borrar = isset($_POST["borrar"]) ? 1 : 0;
	
	
	if($borrar){
	}
	else {
		$creditos = $_POST["creditos"];
		$total = $_POST["total"];
		$validez = getDateSQL($_POST["validez"]);
		$tipo = $_POST["tipo"];
		$inmobiliaria = $_POST["inmobiliaria"];
		$isPagado = $_POST["isPagado"];
		$notificar = $_POST["notificar"];
		
		
		if($id != -1){
			$consulta = "UPDATE PAGO_INMOBILIARIA SET PIN_CREDITOS = :creditos, PIN_TOTAL = :total, PIN_VALIDEZ = :validez, PIN_IS_PAGADO = :isPagado, PIN_TIPO = :tipo, PIN_INMOBILIARIA = :inmobiliaria WHERE PIN_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":creditos" => $creditos, ":total" => $total, ":validez" => $validez, ":isPagado" => $isPagado, ":tipo" => $tipo, ":inmobiliaria" => $inmobiliaria, ":id" => $id));
			
			
			if ($tipo == 1) {//enviar email para link de conekta
				_enviarEmailConekta($id);
			}
			else {//pago manual
				$consulta = "UPDATE INMOBILIARIA, PAGO_INMOBILIARIA SET INM_VALIDEZ = PIN_VALIDEZ, INM_CREDITOS = PIN_CREDITOS WHERE PIN_INMOBILIARIA = INM_ID AND PIN_ID = ?;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array($id));
				
				
				$consulta = "SELECT PIN_CREDITOS, PIN_INMOBILIARIA, PIN_VALIDEZ FROM PAGO_INMOBILIARIA WHERE PIN_ID = ?;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array($id));
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
				$arrayInmobiliaria["activos"] = $_numActivos;
				$_numDesactivar = 0;
				$_contDesactivar = 0;
				
				if ($creditos < $_numActivos) {
					$_numDesactivar = $_numActivos - $creditos;
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
				
				
				//ahora para activar en caso de que los creditos aumentaron, y tienen inmuebles disponibles todavia
				$consulta =
					"SELECT COUNT(IMU_ID) AS CONS_INACTIVOS
					FROM INMUEBLE, USUARIO
					WHERE USU_ID = IMU_USUARIO
					AND USU_INMOBILIARIA = ".$arrayInmobiliaria["id"]."
					AND IMU_LIMITE_VIGENCIA < CURDATE();";
				$pdo = $conexion->query($consulta);
				$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
				$row = $res[0];
				$_numInactivos = $row["CONS_INACTIVOS"];
				$arrayInmobiliaria["inactivos"] = $_numInactivos;
				
				
				if ($arrayInmobiliaria["creditos"] > $arrayInmobiliaria["inactivos"]) {
					//activa 
					$_numActivar = $arrayInmobiliaria["creditos"] - $arrayInmobiliaria["activos"];
					$_contActivar = 0;
					
					$timeStamp_hoy30 = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
					
					$consulta =
						"SELECT IMU_ID, IMU_CREATE
						FROM INMUEBLE, USUARIO
						WHERE IMU_USUARIO = USU_ID
						AND USU_INMOBILIARIA = ".$arrayInmobiliaria["id"]."
						AND IMU_LIMITE_VIGENCIA < CURDATE()
						ORDER BY IMU_CREATE DESC;";
					foreach($conexion->query($consulta) as $row) {
						if ($_contActivar < $_numActivar) {
							$consulta2 = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
							$pdo2 = $conexion->prepare($consulta2);
							$pdo2->execute(array(":limiteVigencia" => date("Y-m-d", $timeStamp_hoy30), ":id" => $row["IMU_ID"]));
							$_contActivar++;
						}
					}
				}
				
				
				if (($isPagado == 1) && ($notificar == 1)) {
					$consulta =
						"SELECT INM_NOMBRE_EMPRESA, USU_EMAIL
						FROM PAGO_INMOBILIARIA, INMOBILIARIA, USUARIO
						WHERE PIN_INMOBILIARIA = INM_ID
						AND INM_USUARIO = USU_ID
						AND PIN_ID = ?;";
					$pdo = $conexion->prepare($consulta);
					$pdo->execute(array($id));
					$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
					$row = $res[0];
					
					$inmobiliaria_nombre = $row["INM_NOMBRE_EMPRESA"];
					$email = $row["USU_EMAIL"];
					
					
					$partes = explode("-", $validez);
					$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
					
					
					$cadenaEmail = 
						"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
							<div style='padding:20px;'>
								<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
							</div>
							<div style='background-color:#f6f6f6; padding:20px;'>
								<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
									<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Modificación de tu plan</h1><br />
									Hola ".$inmobiliaria_nombre." tu plan ha sido modificado exitosamente.<br />Tienes posibilidad de publicar ".$creditos." anuncios simultáneamente<br />y la fecha de vencimiento de tu plan es del día ".$partes[2]." de ".$arrayMeses[(int)$partes[1] - 1]." del ".$partes[0]."<br />por un costo de $".$total.".<br /><br />Gracias por anunciarte en Explora Inmuebles.
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
					$subject = "Modificación de tu plan";
					$message = $cadenaEmail;
					$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
					$header.= "Bcc: ventas@explorainmuebles.com"."\r\n";
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
			}
		}
		else {
			$consulta = "INSERT INTO PAGO_INMOBILIARIA(PIN_CREDITOS, PIN_TOTAL, PIN_VALIDEZ, PIN_IS_PAGADO, PIN_TIPO, PIN_FECHA_HORA, PIN_INMOBILIARIA) VALUES(:creditos, :total, :validez, :isPagado, :tipo, NOW(), :inmobiliaria);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":creditos" => $creditos, ":total" => $total, ":validez" => $validez, ":isPagado" => $isPagado, ":tipo" => $tipo, ":inmobiliaria" => $inmobiliaria));
			$id_creado = $conexion->lastInsertId();
			
			
			if ($tipo == 1) {//enviar email para link de conekta
				_enviarEmailConekta($id_creado);
			}
			else {//pago manual
				$consulta = "UPDATE INMOBILIARIA, PAGO_INMOBILIARIA SET INM_VALIDEZ = PIN_VALIDEZ, INM_CREDITOS = PIN_CREDITOS WHERE PIN_INMOBILIARIA = INM_ID AND PIN_ID = ?;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array($id_creado));
				
				
				$consulta = "SELECT PIN_CREDITOS, PIN_INMOBILIARIA, PIN_VALIDEZ FROM PAGO_INMOBILIARIA WHERE PIN_ID = ?;";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array($id_creado));
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
				$arrayInmobiliaria["activos"] = $_numActivos;
				$_numDesactivar = 0;
				$_contDesactivar = 0;
				
				if ($creditos < $_numActivos) {
					$_numDesactivar = $_numActivos - $creditos;
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
				
				
				//ahora para activar en caso de que los creditos aumentaron, y tienen inmuebles disponibles todavia
				$consulta =
					"SELECT COUNT(IMU_ID) AS CONS_INACTIVOS
					FROM INMUEBLE, USUARIO
					WHERE USU_ID = IMU_USUARIO
					AND USU_INMOBILIARIA = ".$arrayInmobiliaria["id"]."
					AND IMU_LIMITE_VIGENCIA < CURDATE();";
				$pdo = $conexion->query($consulta);
				$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
				$row = $res[0];
				$_numInactivos = $row["CONS_INACTIVOS"];
				$arrayInmobiliaria["inactivos"] = $_numInactivos;
				
				
				if ($arrayInmobiliaria["creditos"] > $arrayInmobiliaria["inactivos"]) {
					//activa 
					$_numActivar = $arrayInmobiliaria["creditos"] - $arrayInmobiliaria["activos"];
					$_contActivar = 0;
					
					$timeStamp_hoy30 = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
					
					$consulta =
						"SELECT IMU_ID, IMU_CREATE
						FROM INMUEBLE, USUARIO
						WHERE IMU_USUARIO = USU_ID
						AND USU_INMOBILIARIA = ".$arrayInmobiliaria["id"]."
						AND IMU_LIMITE_VIGENCIA < CURDATE()
						ORDER BY IMU_CREATE DESC;";
					foreach($conexion->query($consulta) as $row) {
						if ($_contActivar < $_numActivar) {
							$consulta2 = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
							$pdo2 = $conexion->prepare($consulta2);
							$pdo2->execute(array(":limiteVigencia" => date("Y-m-d", $timeStamp_hoy30), ":id" => $row["IMU_ID"]));
							$_contActivar++;
						}
					}
				}
				
				
				if (($isPagado == 1) && ($notificar == 1)) {
					$consulta =
						"SELECT INM_NOMBRE_EMPRESA, USU_EMAIL
						FROM PAGO_INMOBILIARIA, INMOBILIARIA, USUARIO
						WHERE PIN_INMOBILIARIA = INM_ID
						AND INM_USUARIO = USU_ID
						AND PIN_ID = ?;";
					$pdo = $conexion->prepare($consulta);
					$pdo->execute(array($id_creado));
					$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
					$row = $res[0];
					
					$inmobiliaria_nombre = $row["INM_NOMBRE_EMPRESA"];
					$email = $row["USU_EMAIL"];
					
					
					$partes = explode("-", $validez);
					$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
					
					
					$cadenaEmail = 
						"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
							<div style='padding:20px;'>
								<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
							</div>
							<div style='background-color:#f6f6f6; padding:20px;'>
								<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
									<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Activación de tu plan</h1><br />
									Hola ".$inmobiliaria_nombre." tu plan ha sido activado exitosamente.<br />Tienes posibilidad de publicar ".$creditos." anuncios simultáneamente<br />y la fecha de vencimiento de tu plan es del día ".$partes[2]." de ".$arrayMeses[(int)$partes[1] - 1]." del ".$partes[0]."<br />por un costo de $".$total.".<br /><br />Gracias por anunciarte en Explora Inmuebles.
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
					$subject = "Activación de tu plan";
					$message = $cadenaEmail;
					$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
					$header.= "Bcc: ventas@explorainmuebles.com"."\r\n";
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
			}
		}
	}
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"id"		=>	$id_creado
	);
	
	echo json_encode($arrayRespuesta);
	
?>