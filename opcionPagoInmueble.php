<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	$conexion = crearConexionPDO();
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
		
	$idInmueble = $_GET["idInmueble"];
	
	$consulta =
		"SELECT IMU_USUARIO, USU_INMOBILIARIA
		FROM INMUEBLE, USUARIO
		WHERE IMU_USUARIO = USU_ID
		AND IMU_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idInmueble));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	
	if ($row["USU_INMOBILIARIA"] == NULL) {
		if ($_SESSION[userId] != $row["IMU_USUARIO"])
			header("location: index.php");
	}
	else
		header("location: index.php");
	
	
	
	$consulta = "SELECT PRO_PRECIO, PRO_PROMOCION FROM PROMOCION WHERE PRO_ID = 1;";
	$pdo = $conexion->query($consulta);
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$precio = $row["PRO_PRECIO"];
	$textoPromocion = $row["PRO_PROMOCION"];
	
	
	$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	
	
	CabeceraHTML("opcionPagoInmueble.css,opcionPagoInmueble_ver2.js");
	CuerpoHTML();
?>
<div class="opcionPagoInmueble_cuerpo">
	<p class="titulo">Tu anuncio ha sido guardado</p><br /><br />
	<?php
		if ($precio != 0) {
	?>
    <p>Para publicarlo debes realizar un pago de $<span id="precio"><?php echo $precio; ?></span>. Tu anuncio tendrá 30 días de vigencia a partir de la fecha de pago.</p><br /><br />
    <p><?php echo $textoPromocion; ?></p><br /><br />
    <p class="guardar" onclick="gotoURL('pagoInmueble.php?idInmueble=<?php echo $idInmueble; ?>');"><a class="btnBotones palomita">Guardar</a>Pagar ahora</p><p class="guardar" style="margin-left:100px;" onclick="pagarMasTarde(<?php echo $idInmueble; ?>);"><a class="btnBotones palomita">Guardar</a>Más tarde</p>
    <?php
		}
		else {
			$timeStamp_hoy30 = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
	?>
    <p>Por tiempo limitado, los anuncios en Explora Inmuebles tienen un precio de $<span id="precio"><?php echo $precio; ?></span>.<br />Haz click en <span class="guardar" onclick="opcionPago_realizarPago(<?php echo $idInmueble; ?>);">Publicar</span> para finalizar la publicación de tu anuncio.</p><br />
    <p>. Tu anuncio estará vigente hasta el <?php echo date("d", $timeStamp_hoy30); ?> de <?php echo $arrayMeses[(int)date("m", $timeStamp_hoy30)-1]; ?> de <?php echo date("Y", $timeStamp_hoy30); ?></p><br />
    <p><?php echo $textoPromocion; ?></p><br /><br />
    <?php
		}
	?>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>