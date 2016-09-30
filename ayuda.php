<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$conexion = crearConexionPDO();
	$consulta = "SELECT PAG_CONTENIDO FROM PAGINA WHERE PAG_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(1));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$contenido = $row["PAG_CONTENIDO"];
	
	
	CabeceraHTML("ayuda_ver2.css,ayuda.js");
	CuerpoHTML();
?>
<div class="ayuda_cuerpo container"><?php
	echo $contenido;
?></div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>