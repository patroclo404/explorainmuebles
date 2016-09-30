<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	$conexion = crearConexionPDO();
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
		
	$idInmobiliaria = $_GET["idInmobiliaria"];
	$inmueble = isset($_GET["inmueble"]) ? $_GET["inmueble"] : -1;
	
	$consulta =
		"SELECT
			INM_ID,
			INM_NOMBRE_EMPRESA,
			INM_VALIDEZ,
			INM_CREDITOS,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE, USUARIO
				WHERE IMU_USUARIO = USU_ID
				AND USU_INMOBILIARIA = INM_ID
				AND IMU_LIMITE_VIGENCIA >= CURDATE()
			) AS CONS_DISPONIBLES
		FROM INMOBILIARIA
		WHERE INM_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idInmobiliaria));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$arrayInmobiliaria = array(
		"id"			=>	$idInmobiliaria,
		"titulo"		=>	$row["INM_NOMBRE_EMPRESA"],
		"validez"		=>	getDateNormal($row["INM_VALIDEZ"]),
		"creditos"		=>	$row["INM_CREDITOS"],
		"disponibles"	=>	$row["CONS_DISPONIBLES"]
	);
	
	
	$partes = explode("/", $arrayInmobiliaria["validez"]);
	$timeStamp_inmobiliaria = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
	$timeStamp_hoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	
	
	if ($_SESSION[userInmobiliaria] != $idInmobiliaria)
		header("location: index.php");
	
	
	CabeceraHTML("opcionPagoInmobiliaria.css");
	CuerpoHTML();
?>
<div class="opcionPagoInmobiliaria_cuerpo">
	<p class="titulo">Tu anuncio ha sido guardado</p><br /><br />
    <?php
		if ($timeStamp_inmobiliaria < $timeStamp_hoy) {
			echo 
				"<p>Actualmente no cuentas con ningún plan de anuncios activo en Explora Inmuebles. Ponte en contacto con nosotros para configurar un plan a tu medida.</p><br /><br />
				<p class='guardar' onclick='gotoURL(\"contacto.php\");'><a class='btnBotones palomita'>Guardar</a>Contactar</p><p class='guardar' style='margin-left:100px;' onclick='gotoURL(\"misAnuncios.php\");'><a class='btnBotones palomita'>Guardar</a>No Gracias</p>";
		}
		else {
			if ($arrayInmobiliaria["disponibles"] < $arrayInmobiliaria["creditos"]) {
				if ($timeStamp_inmobiliaria >= $timeStamp_hoy) {
					//se publico (activo) un anuncio
					if ($inmueble != -1) {
						if ($arrayInmobiliaria["disponibles"] < $arrayInmobiliaria["creditos"]) {
							$timeStamp_limiteVigencia = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
							
							$consulta = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
							$pdo = $conexion->prepare($consulta);
							$pdo->execute(array(":limiteVigencia" => date("Y-m-d", $timeStamp_limiteVigencia), ":id" => $inmueble));
							
							$arrayInmobiliaria["disponibles"] = $arrayInmobiliaria["disponibles"] + 1;
						}
					}
				}
	
				
				echo 
					"<p>Tu anuncio ha sido publicado. Estas utilizando ".$arrayInmobiliaria["disponibles"]." anuncios. El plan con el que cuentas actualmente es de ".$arrayInmobiliaria["creditos"]." anuncios simultaneos.</p><br /><br />
					<p class='guardar' onclick='gotoURL(\"misAnuncios.php\");'><a class='btnBotones palomita'>Guardar</a>Aceptar</p>";
			}
			else {
				if (($arrayInmobiliaria["disponibles"] == $arrayInmobiliaria["creditos"]) && ($inmueble != -1)) {
					echo 
						"<p>El plan con el que cuentas actualmente en Explora Inmuebles solo te permite publicar ".$arrayInmobiliaria["creditos"]." anuncios. Por favor desactiva uno de tus anuncios, o ponte en contacto con nosotros para reconfigurar tu plan.</p><br /><br />
						<p class='guardar' onclick='gotoURL(\"contacto.php\");'><a class='btnBotones palomita'>Guardar</a>Contactar</p><p class='guardar' style='margin-left:100px;' onclick='gotoURL(\"misAnuncios.php\");'><a class='btnBotones palomita'>Guardar</a>No Gracias</p>";
				}
				else {
					if ($timeStamp_inmobiliaria >= $timeStamp_hoy) {
						//se publico (activo) un anuncio
						if ($inmueble != -1) {
							if ($arrayInmobiliaria["disponibles"] < $arrayInmobiliaria["creditos"]) {
								$timeStamp_limiteVigencia = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
								
								$consulta = "UPDATE INMUEBLE SET IMU_LIMITE_VIGENCIA = :limiteVigencia WHERE IMU_ID = :id;";
								$pdo = $conexion->prepare($consulta);
								$pdo->execute(array(":limiteVigencia" => date("Y-m-d", $timeStamp_limiteVigencia), ":id" => $inmueble));
								
								$arrayInmobiliaria["disponibles"] = $arrayInmobiliaria["disponibles"] + 1;
							}
						}
					}
					
					echo 
						"<p>Tu anuncio ha sido publicado. Estas utilizando ".$arrayInmobiliaria["disponibles"]." anuncios. El plan con el que cuentas actualmente es de ".$arrayInmobiliaria["creditos"]." anuncios simultaneos.</p><br /><br />
						<p class='guardar' onclick='gotoURL(\"misAnuncios.php\");'><a class='btnBotones palomita'>Guardar</a>Aceptar</p>";
				}
			}
		}
    ?>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>