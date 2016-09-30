<?php

	require_once("template.php");
	require_once("conekta/lib/Conekta.php");
	$conexion = crearConexionPDO();
	
	
	/*
		tarjetas: 4242424242424242, 4000000000000002, 4000000000000127
		token de pruebas: tok_test_visa_4242, tok_test_card_declined, tok_test_insufficient_funds
	*/
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$addCarrito = isset($_POST["addCarrito"]) ? 1 : 0;
	$addDireccion = isset($_POST["addDireccion"]) ? 1 : 0;
	$addCharge = isset($_POST["addCharge"]) ? 1 : 0;
	$clearCarrito = isset($_POST["clearCarrito"]) ? 1 : 0;
	
	
	/*
		Actualiza la informacion de la direccion de facturacion y envio
	*/
	if ($addDireccion) {
		$nombre = $_POST["nombre"];
		$calleNumero = $_POST["calleNumero"];
		$estado = $_POST["estado"];
		$estadoValue = $_POST["estadoValue"];
		$ciudad = $_POST["ciudad"];
		$ciudadValue = $_POST["ciudadValue"];
		$colonia = $_POST["colonia"];
		$coloniaValue = $_POST["coloniaValue"];
		$cp = $_POST["cp"];
		$cpValue = $_POST["cpValue"];
		$telefono = $_POST["telefono"];
		$idPago = isset($_POST["idPago"]) ? $_POST["idPago"] : -1;
		$idInmueble = isset($_POST["idInmueble"]) ? $_POST["idInmueble"] : -1;
		
		
		//agrego a la session la direccion
		$_SESSION[userCarrito]["direccion"] = array(
			"nombre"		=>	$nombre,
			"calleNumero"	=>	$calleNumero,
			"estado"		=>	$estado,
			"estadoValue"	=>	$estadoValue,
			"ciudad"		=>	$ciudad,
			"ciudadValue"	=>	$ciudadValue,
			"colonia"		=>	$colonia,
			"coloniaValue"	=>	$coloniaValue,
			"cp"			=>	$cp,
			"cpValue"		=>	$cpValue,
			"telefono"		=>	$telefono
		);
		
		
		if ($idPago != -1) {//es pago por medio de inmobiliaria
			$consulta =
				"SELECT PIN_CREDITOS, PIN_TOTAL, PIN_VALIDEZ
				FROM PAGO_INMOBILIARIA
				WHERE PIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($idPago));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$creditos = $row["PIN_CREDITOS"];
			$total = $row["PIN_TOTAL"];
			$validez = getDateNormal($row["PIN_VALIDEZ"]);
			
			$partes = explode("/", $validez);
			$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
			
			
			$listaCarrito = array(
				"id"			=>	$idPago,
				"descripcion"	=>	"Plan de Inmobiliaria con ".$creditos." créditos y vigencia al ".$partes[0]." de ".$arrayMeses[((int)$partes[1])-1]." del ".$partes[2],
				"precio"		=>	$total
			);
			
			
			//agrego a la session el articulo a cobrar
			$_SESSION[userCarrito]["lista"] = $listaCarrito;
			$_SESSION[userCarrito]["tipo"] = "INM";//Inmobiliaria
		}
		
		
		if ($idInmueble != -1) {//pago por usuario que no pertenece a inmobiliaria
			$consulta = "SELECT PRO_PRECIO FROM PROMOCION WHERE PRO_ID = 1;";
			$pdo = $conexion->query($consulta);
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$total = $row["PRO_PRECIO"];
			
			
			$timestamp_fecha = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
			$partes = explode("/", date("d/m/Y", $timestamp_fecha));
			$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
			
			
			$listaCarrito = array(
				"id"			=>	$idInmueble,
				"descripcion"	=>	"Plan de Inmueble con vigencia al ".$partes[0]." de ".$arrayMeses[((int)$partes[1])-1]." del ".$partes[2],
				"precio"		=>	$total
			);
			
			
			//agrego a la session el articulo a cobrar
			$_SESSION[userCarrito]["lista"] = $listaCarrito;
			$_SESSION[userCarrito]["tipo"] = "IMU";//Inmueble
		}
		
		
		
		$arrayRespuesta = array(
			"isExito"	=>	$isExito,
			"mensaje"	=>	$mensaje
		);
		
		echo json_encode($arrayRespuesta);
	}
	
	
	/*
		Realiza el cobro al cliente
	*/
	if ($addCharge) {
		Conekta::setApiKey("key_QLzLqroc1KriZxyrothgBw");//Llave privada
		$token = $_POST["token"];
		$formaPago = $_POST["formaPago"];
		
		
		$listaCarrito = $_SESSION[userCarrito]["lista"];
		$direccion = $_SESSION[userCarrito]["direccion"];
		$reference_id = "";
		$reference_barcode = "";
		$barcode_url = "";
		
		
		$tituloEmail = "";
		$textoEmail = "";
		
		
		$datosFacturacion = array();
		$facturacion = implode("|", $_SESSION[userCarrito]["direccion"]);
		
		
		if ($_SESSION[userCarrito]["tipo"] == "INM") {//inmobiliaria
			$consulta =
				"SELECT INM_ID, INM_NOMBRE_EMPRESA, USU_EMAIL
				FROM INMOBILIARIA, USUARIO
				WHERE INM_USUARIO = USU_ID
				AND USU_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($_SESSION[userId]));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$datosFacturacion = array(
				"id"		=>	$row["INM_ID"],
				"nombre"	=>	$row["INM_NOMBRE_EMPRESA"],
				"email"		=>	$row["USU_EMAIL"]
			);
			
			
			$consulta = "UPDATE PAGO_INMOBILIARIA SET PIN_FACTURACION = :facturacion WHERE PIN_ID = :id;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":facturacion" => $facturacion, ":id" => $listaCarrito["id"]));
			
			
			$reference_id = $_SESSION[userCarrito]["tipo"]."_".$listaCarrito["id"];
			
			
			$consulta = "SELECT PIN_CREDITOS, PIN_TOTAL, PIN_VALIDEZ FROM PAGO_INMOBILIARIA WHERE PIN_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($listaCarrito["id"]));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			
			$datosFacturacion["creditos"] = $row["PIN_CREDITOS"];
			$datosFacturacion["total"] = $row["PIN_TOTAL"];
			$datosFacturacion["validez"] = getDateNormal($row["PIN_VALIDEZ"]);
			
			
			$tituloEmail = "Pago de plan de anuncios";
			$textoEmail =
			"Hola ".$datosFacturacion["nombre"]." has completado el proceso para<br />activar tu plan de anuncios. Tan pronto como recibamos la confirmación<br />del pago, tu plan será activado y te enviaremos una notificación.<br /><br />Con este plan podrás publicar ".$datosFacturacion["creditos"]." anuncios simultáneamente y<br />la fecha de vencimiento de tu plan será el día ".$datosFacturacion["validez"]." por<br />un costo de $".$datosFacturacion["total"].".<br /><br />Agradecemos tu preferencia en Explora Inmuebles.";
		}
		else {//inmueble
			$consulta =
				"SELECT IMU_ID, IMU_TITULO, USU_EMAIL
				FROM INMUEBLE, USUARIO
				WHERE IMU_USUARIO = USU_ID
				AND IMU_ID = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($listaCarrito["id"]));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$datosFacturacion = array(
				"id"		=>	$row["IMU_ID"],
				"nombre"	=>	$row["IMU_TITULO"],
				"email"		=>	$row["USU_EMAIL"]
			);
			
			
			$consulta = "INSERT INTO PAGO_INMUEBLE(PIM_TOTAL, PIM_TIPO, PIM_FECHA_HORA, PIM_FACTURACION, PIM_INMUEBLE) VALUES(:total, 1, NOW(), :facturacion, :inmueble);";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":total" => $listaCarrito["precio"], ":facturacion" => $facturacion, ":inmueble" => $listaCarrito["id"]));
			
			
			$listaCarrito["id"] = $conexion->lastInsertId();
			$_SESSION[userCarrito]["lista"]["id"] = $listaCarrito["id"];
			$reference_id = $_SESSION[userCarrito]["tipo"]."_".$listaCarrito["id"];
			
			
			$tituloEmail = "Se generó la orden para publicar tu anuncio ".$datosFacturacion["nombre"];
			$textoEmail = 
			"Hola ".$direccion["nombre"]." se ha generado exitosamente la orden de pago<br />para tu anuncio ".$datosFacturacion["nombre"].".<br />Tan pronto como recibamos la confirmación del pago, tu anuncio será<br />publicado y te enviaremos una notificación.<br /><br />Agradecemos tu preferencia en Explora Inmuebles.";
		}
		
		
		$descripcionConekta = "Pago del Inmueble: ".$datosFacturacion["nombre"]." (".$datosFacturacion["id"].")";
		
		
		try {
			///////////////////////////////////////////////////////////////////////////////////////////////
			//									creacion de array para conekta
			//////////////////////////////////////////////////////////////////////////////////////////////
			$arrayCargo = array(
				"amount"		=>	0,
				"currency"		=>	"MXN",
				"description"	=>	$descripcionConekta,
				"reference_id"	=>	$reference_id,
				"details"		=>	array(
					"name"			=>	$direccion["nombre"],
					"email"			=>	$datosFacturacion["email"],
					"phone"			=>	$direccion["telefono"],
					"billing_address"	=>	array(
						"company_name"	=>	$datosFacturacion["nombre"],
						"street1"		=>	$direccion["calleNumero"],
						"street2"		=>	$direccion["coloniaValue"],
						"street3"		=>	"",
						"city"			=>	$direccion["ciudadValue"],
						"state"			=>	$direccion["estadoValue"],
						"zip"			=>	$direccion["cpValue"]
					),
					"line_items"	=>	array()
				)
			);
			
			
			switch ($formaPago){
				case "card":
					$arrayCargo["card"] = $token;
					break;
				case "oxxo":
					$arrayCargo["cash"] = array(
						"type"			=>	"oxxo",
						"expires_at"	=>	date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+3, date("Y")))
					);
					break;
			}
			
			
			///////////////////////////////////////////////////////////////////////////////////////////////
			//									asignacion de precios a conekta
			//////////////////////////////////////////////////////////////////////////////////////////////
			$total = $arrayCargo["amount"];
			$precio = $listaCarrito["precio"] * 100;//se expresa en centavos
			
			
			$arrayCargo["details"]["line_items"][] = array(
				"name"			=>	$listaCarrito["descripcion"],
				"sku"			=>	$listaCarrito["id"],
				"unit_price"	=>	$precio,
				"description"	=>	$listaCarrito["descripcion"],
				"quantity"		=>	1
			);
			
					
			$total += $precio;
			$arrayCargo["amount"] = $total;
			$arrayCargo["card"] = $token;
			
			
			///////////////////////////////////////////////////////////////////////////////////////////////
			//									generar el cargo con conekta
			//////////////////////////////////////////////////////////////////////////////////////////////
			$charge = Conekta_Charge::create($arrayCargo);
			
			
			switch($formaPago) {
				case "card":
					if ($charge->status == "paid") {//no hace nada; ya que lo hace el webHook
					}
					break;
				case "oxxo":
					$reference_barcode = $charge->payment_method->barcode;
					$barcode_url = $charge->payment_method->barcode_url;
					break;
			}
			
			
			///////////////////////////////////////////////////////////////////////////////////////////////
			//									envio de email de notificacion de proceso de pago
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
			$subject = "Proceso de Pago";
			$message = $cadenaEmail;
			$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
			$header.= "Bcc: ventas@explorainmuebles.com"."\r\n";
			$header.= "X-Mailer:PHP/".phpversion()."\r\n";
			$header.= "Mime-Version: 1.0"."\r\n";
			$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
			mail($to, $subject, $message, $header);
		} catch (Conekta_Error $e){
			$isExito = 0;
			$mensaje = $e->getMessage(); //el pago no pudo ser procesado
		}
		
		
		$arrayRespuesta = array(
			"isExito"			=>	$isExito,
			"mensaje"			=>	$mensaje,
			"reference_id"		=>	$reference_id,
			"reference_barcode"	=>	$reference_barcode,
			"barcode"			=>	$barcode_url,
			"total"				=>	$total / 100
		);
		
		echo json_encode($arrayRespuesta);
	}
	
	
?>