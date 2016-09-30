<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$conexion = crearConexionPDO();
	$consulta = "SELECT PAG_CONTENIDO FROM PAGINA WHERE PAG_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(4));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$contenido = $row["PAG_CONTENIDO"];
	
	
	CabeceraHTML("reglasPublicacion_ver2.css,reglasPublicacion.js");
	CuerpoHTML();
?>
<div class="reglasPublicacion_cuerpo container"><?php
	echo $contenido;
?></div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>