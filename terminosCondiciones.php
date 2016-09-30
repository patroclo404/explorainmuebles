<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	

	$conexion = crearConexionPDO();	
	$consulta = "SELECT PAG_CONTENIDO FROM PAGINA WHERE PAG_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(3));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$contenido = $row["PAG_CONTENIDO"];
	
	
	CabeceraHTML("terminosCondiciones_ver2.css,terminosCondiciones.js");
	CuerpoHTML();
?>
<div class="terminosCondiciones_cuerpo container"><?php
	echo $contenido;
?></div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>