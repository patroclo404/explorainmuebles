<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
		
	unset($_SESSION[userCarrito]);
		
		
	$reference_id = $_GET["reference_id"];
	$partes = explode("_", $reference_id);
	$idPago = $partes[1];
	
	
	CabeceraHTML("completed.css");
	CuerpoHTML();
?>
<div class="completed_cuerpo">
	<p class="titulo">Gracias por tu pago.</p><br /><br />
    <p>Tu orden ya ha sido procesada.</p>
    <p>Tu número de orden de pago es: #<?php echo $idPago; ?></p>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>