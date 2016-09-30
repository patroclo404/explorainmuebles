<?php

	require_once("../../lib_php/template.php");
	require_once("template.php");
	validar_credenciales(array(0, 1));
	$conexion = crearConexionPDO();
	
	
	$isExito = 1;
	$mensaje = "Los datos se han actualizado de manera correcta.";
	
	
	$id = $_POST["id"];
	$modificar = isset($_POST["modificar"]) ? 1 : 0;
	
	
	if ($modificar) {
		$transacciones = $_POST["transacciones"];
		
		$consulta = "DELETE FROM TRANSACCION_INMUEBLE WHERE TRI_INMUEBLE = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($id));
		
		if ($transacciones != "") {
			$transacciones = explode(",", $transacciones);
			
			for ($x = 0; $x < count($transacciones); $x++) {
				$consulta = "INSERT INTO TRANSACCION_INMUEBLE(TRI_TRANSACCION, TRI_INMUEBLE) VALUES(:transaccion, :id);";
				$pdo = $conexion->prepare($consulta);
				$pdo->execute(array(":transaccion" => $transacciones[$x], ":id" => $id));
			}
		}
	}
	
	
	$consulta = 
		"SELECT
			TRA_ID,
			TRA_NOMBRE,
			(
				SELECT TRI_ID
				FROM TRANSACCION_INMUEBLE
				WHERE TRI_TRANSACCION = TRA_ID
				AND TRI_INMUEBLE = ".$id."
			) AS CONS_IS_TIENE
		FROM TRANSACCION
		ORDER BY TRA_NOMBRE;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($id));
	$arrayCampos = array();
	
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayCampos[] = array(
			"campo1"	=>	$row["TRA_ID"],
			"campo2"	=>	$row["TRA_NOMBRE"],
			"campo3"	=>	$row["CONS_IS_TIENE"] != NULL ? $row["CONS_IS_TIENE"] : 0
		);
	}
	
	
	$arrayRespuesta = array(
		"isExito"	=>	$isExito,
		"mensaje"	=>	$mensaje,
		"datos"		=>	$arrayCampos
	);
	
	echo json_encode($arrayRespuesta);
	
?>