<?php

	require_once("template.php");
	$conexion = crearConexionPDO();
	
	
	$arrayInmobiliaria = array();
	$consulta = "SELECT INM_ID, INM_NOMBRE_EMPRESA, INM_VALIDEZ FROM INMOBILIARIA WHERE INM_VALIDEZ > ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-8, date("Y")))));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$partes = explode("-", $row["INM_VALIDEZ"]);
		$timeStamp_hoy7dias = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
		$timeStamp_inmobiliaria = mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
		
		if ($timeStamp_inmobiliaria == $timeStamp_hoy7dias) {
			$arrayInmobiliaria[] = array(
				"id"			=>	$row["INM_ID"],
				"titulo"		=>	$row["INM_NOMBRE_EMPRESA"]
			);
		}
	}
	
	
	if (count($arrayInmobiliaria) > 0) {
		$textoInmobiliariasVencer = "";
		
		for ($x = 0; $x < count($arrayInmobiliaria); $x++) {
			$textoInmobiliariasVencer.= $arrayInmobiliaria[$x]["id"]." / ".$arrayInmobiliaria[$x]["titulo"]."<br />";
		}
		
		$cadenaEmail = 
			"<div style='width:700px; margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif;'>
				<div style='padding:20px;'>
					<a href='http://www.explorainmuebles.com/'><img src='http://www.explorainmuebles.com/images/logo.png' alt='Explora Inmuebles' /></a>
				</div>
				<div style='background-color:#f6f6f6; padding:20px;'>
					<div style='border-width:4px; border-color:#852c2b; border-style:none solid; padding:10px 20px; color:#575756;'>
						<h1 style='font-size:22px; margin:0px 0px 15px 0px; color:#852c2b;'>Planes de inmobiliarias próximos a vencer</h1>
						 Los planes próximos a vencer son los siguientes:<br /><br />".$textoInmobiliariasVencer."
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
			
			
		$to = "ventas@explorainmuebles.com";
		$subject = "Inmobiliarias a Vencer";
		$message = $cadenaEmail;
		$header = "From: Explora Inmuebles <contacto@explorainmuebles.com>"."\r\n";
		$header.= "X-Mailer:PHP/".phpversion()."\r\n";
		$header.= "Mime-Version: 1.0"."\r\n";
		$header.= "Content-Type: text/html; charset=utf-8"."\r\n";
		mail($to, $subject, $message, $header);
	}

?>