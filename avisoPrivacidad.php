<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$conexion = crearConexionPDO();
	$consulta = "SELECT PAG_CONTENIDO FROM PAGINA WHERE PAG_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(2));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$contenido = $row["PAG_CONTENIDO"];
	
	
	CabeceraHTML("avisoPrivacidad_ver2.css,avisoPrivacidad.js");
	CuerpoHTML();
?>
<div class="avisoPrivacidad_cuerpo container"><?php
	echo $contenido;
?></div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>