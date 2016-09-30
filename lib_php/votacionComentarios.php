<?php

	require_once("template.php");
	$conexion = crearConexionPDO();
	
	
	$inmobiliaria = isset($_POST["inmobiliaria"]) ? $_POST["inmobiliaria"] : -1;
	$usuarioCalificado = isset($_POST["usuarioCalificado"]) ? $_POST["usuarioCalificado"] : -1;
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	
	
	$arrayInformativo = array();
	$arrayComentarios = array();
	$arrayCondiciones = array();
	$consulta = "";
	$urlArchivos = "images/images/";
	
	
	if ($inmobiliaria != -1) {//inmobiliaria
		$consulta = "SELECT INM_NOMBRE_EMPRESA, INM_CREATE, INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($inmobiliaria));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		
		
		$arrayInformativo = array(
			"id"			=>	$inmobiliaria,
			"nombre"		=>	$row["INM_NOMBRE_EMPRESA"],
			"create"		=>	getDateNormal($row["INM_CREATE"]),
			"imagen"		=>	$row["INM_LOGOTIPO"] != NULL ? $urlArchivos.$row["INM_LOGOTIPO"] : "",
			"calificaciones"=>	0
		);
	
	
		$consulta =
			"SELECT
				VIN_ID AS CONS_ID,
				VIN_USUARIO AS CONS_USUARIO_ID,
				VIN_CALIFICACION AS CONS_CALIFICACION,
				VIN_COMENTARIO AS CONS_COMENTARIO,
				USU_NOMBRE AS CONS_USUARIO_NOMBRE,
				USU_IMAGEN AS CONS_USUARIO_IMAGEN,
				USU_FBID AS CONS_USUARIO_FBID
			FROM VOTACION_INMOBILIARIA, USUARIO
			WHERE VIN_INMOBILIARIA = ?
			AND USU_ID = VIN_USUARIO
			ORDER BY VIN_ID DESC;";
			
		$arrayCondiciones = array($inmobiliaria);
	}
	else {//usuario
		$consulta = "SELECT USU_NOMBRE, USU_CREATE, USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($usuarioCalificado));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		
		
		$arrayInformativo = array(
			"id"			=>	$usuarioCalificado,
			"nombre"		=>	$row["USU_NOMBRE"],
			"create"		=>	getDateNormal($row["USU_CREATE"]),
			"imagen"		=>	$row["USU_IMAGEN"] != NULL ? $urlArchivos.$row["USU_IMAGEN"] : "",
			"calificaciones"=>	0
		);
		
	
		$consulta =
			"SELECT
				VUS_ID AS CONS_ID,
				VUS_USUARIO_CALIFICADOR AS CONS_USUARIO_ID,
				VUS_CALIFICACION AS CONS_CALIFICACION,
				VUS_COMENTARIO AS CONS_COMENTARIO,
				USU_NOMBRE AS CONS_USUARIO_NOMBRE,
				USU_IMAGEN AS CONS_USUARIO_IMAGEN,
				USU_FBID AS CONS_USUARIO_FBID
			FROM VOTACION_USUARIO, USUARIO
			WHERE VUS_USUARIO_CALIFICADO = ?
			AND USU_ID = VUS_USUARIO_CALIFICADOR
			ORDER BY VUS_ID DESC;";
			
		$arrayCondiciones = array($usuarioCalificado);
	}
	$pdo = $conexion->prepare($consulta);
	$pdo->execute($arrayCondiciones);
	foreach ($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayComentarios[] = array(
			"id"			=>	$row["CONS_ID"],
			"calificacion"	=>	$row["CONS_CALIFICACION"],
			"comentario"	=>	$row["CONS_COMENTARIO"],
			"usuario"		=>	array(
				"id"		=>	$row["CONS_USUARIO_ID"],
				"nombre"	=>	$row["CONS_USUARIO_NOMBRE"],
				"imagen"	=>	$row["CONS_USUARIO_FBID"] != NULL ? "https://graph.facebook.com/".$row["CONS_USUARIO_FBID"]."/picture?type=large" : ($row["CONS_USUARIO_IMAGEN"] != NULL ? $urlArchivos.$row["CONS_USUARIO_IMAGEN"] : "images/userSinFoto.png"),
				"FBId"		=>	$row["CONS_USUARIO_FBID"] != NULL ? $row["CONS_USUARIO_FBID"] : ""
			)
		);
	}
	
	
	$arrayInformativo["calificaciones"] = count($arrayComentarios);
	
	
	$cadena =
		"<h1>Calificaciones de".($inmobiliaria != -1 ? " la Inmobiliaria" : "l Usuario")."</h1>
		<div class='infoDescriptivo'>
			<table>
				<tbody>
					<tr>
						".($arrayInformativo["imagen"] != "" ? ("<td class='imagen'><img src='".$arrayInformativo["imagen"]."' alt='".$arrayInformativo["nombre"]."' /></td>") : "")."
						<td>".$arrayInformativo["nombre"]."<br /><strong>Miembro desde: </strong>".$arrayInformativo["create"]."<br />".$arrayInformativo["calificaciones"]." Calificacion".($arrayInformativo["calificaciones"] > 1 ? "es" : "")."</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='contenedorMensajes'>";
	
	
	for ($x = 0; $x < count($arrayComentarios); $x++) {
		$cadena.=
			"<div class='contenedorMensaje' data-id='".$arrayComentarios[$x]["id"]."' data-tipo='".($inmobiliaria != -1 ? "inmobiliaria" : "usuario")."'>
				<table>
					<tbody>
						<tr>
							<td class='imagen'><img src='".$arrayComentarios[$x]["usuario"]["imagen"]."' /></td>
							<td class='nombre'>".$arrayComentarios[$x]["usuario"]["nombre"]."</td>
							<td class='contenedorEstrellas' data-calificacion='".$arrayComentarios[$x]["calificacion"]."'>";
							
							
		for ($y = 0; $y < 5; $y++) {
			$cadena.= "<a class='_estrella ".(($y + 1) <= $arrayComentarios[$x]["calificacion"] ? "_100" : "")."' data-value='".($y + 1)."'>".($y + 1)."</a>";
		}
		
						
		$cadena.=	
						"</tr>
						<tr>
							<td class='texto' colspan='3'>".$arrayComentarios[$x]["comentario"]."</td>
						</tr>";
						
						
		if ($usuario == $arrayComentarios[$x]["usuario"]["id"]) {
			$cadena.=
						"<tr>
							<td class='eliminar' colspan='3'><span>Eliminar Calificación</span></td>
						</tr>";
		}
						
		$cadena.=
					"</tbody>
				</table>
			</div>";
	}
	
	
	$cadena.=
		"</div>";
	
	
	$arrayRespuesta = array(
		"html"	=>	$cadena,
	);
	
	echo json_encode($arrayRespuesta);
	
?>