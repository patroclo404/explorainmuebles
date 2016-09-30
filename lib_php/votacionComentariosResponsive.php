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
		"<div class='calificaciones-usuarios'><h1>Calificaciones de Usuarios:</h1>

		<div class='contenedorMensajes'>";
	
	
	for ($x = 0; $x < count($arrayComentarios); $x++) {
		$cadena.=
			"<div class='contenedorMensaje' data-id='".$arrayComentarios[$x]["id"]."' data-tipo='".($inmobiliaria != -1 ? "inmobiliaria" : "usuario")."'>
				<div class='row comment'>


							<div class='imagen col-md-1 col-sm-2 col-xs-4'><img class='img-responsive' src='".$arrayComentarios[$x]["usuario"]["imagen"]."' /></div>
							<div class='nombre col-md-10 col-sm-10'>".$arrayComentarios[$x]["usuario"]["nombre"]."</div>
							<div class='contenedorEstrellas col-md-10 col-sm-10' data-calificacion='".$arrayComentarios[$x]["calificacion"]."'>";
							
							
		for ($y = 0; $y < 5; $y++) {
			$cadena.= "<a class='_estrella ".(($y + 1) <= $arrayComentarios[$x]["calificacion"] ? "_100" : "")."' data-value='".($y + 1)."'>".($y + 1)."</a>";
		}
		
						
		$cadena.=	
						"
						</div>
							<div class='texto col-md-7 col-sm-12' >".$arrayComentarios[$x]["comentario"]."</div>
							
						</div>";
						
						
		/*if ($usuario == $arrayComentarios[$x]["usuario"]["id"]) {
			$cadena.=
						"<tr>
							<td class='eliminar' colspan='3'><span>Eliminar Calificación</span></td>
						</tr>";
		}*/
						
		$cadena.=
					"
			</div>";
	}
	
	
	$cadena.=
		"</div></div>";

	if (! count($arrayComentarios)) $cadena ='';

	$arrayRespuesta = array(
		"html"	=>	$cadena,
	);
	
	echo json_encode($arrayRespuesta);
	
?>