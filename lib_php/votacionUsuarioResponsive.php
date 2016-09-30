<?php

require_once("template.php");
$conexion = crearConexionPDO();


$isExito = 1;
$borrar = isset($_POST["borrar"]) ? 1 : 0;
$getVotacion = isset($_POST["getVotacion"]) ? 1 : 0;


$id = $_POST["id"];
$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;


/*
    Obtiene la votacion del usuario como contenido html
*/
if ($getVotacion) {
    $propietario = $_POST["propietario"];


    $arrayVotacion = array(
        "reputacion"	=>	array(
            "sum_calificacion"	=>	0,
            "cont_usuarios"		=>	0,
            "calificacion"		=>	array(
                "total"		=>	0,
                "entero"	=>	0,
                "flotante"	=>	0
            )
        ),
        "tuCalificacion"	=>	array(
            "id"			=>	-1,
            "votacion"		=>	0,
            "comentario"	=>	""
        )
    );


    $consulta =
        "SELECT
				SUM(VUS_CALIFICACION) AS CONS_CALIFICACION,
				COUNT(VUS_ID) AS CONT_CONTADOR
			FROM VOTACION_USUARIO
			WHERE VUS_USUARIO_CALIFICADO = ?;";
    $pdo = $conexion->prepare($consulta);
    $pdo->execute(array($id));
    $res = $pdo->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) > 0) {
        $row = $res[0];
        $_calificacion = json_decode(template_getValorVotacion($row["CONS_CALIFICACION"], $row["CONT_CONTADOR"]), true);

        $arrayVotacion["reputacion"] = array(
            "sum_calificacion"	=>	$row["CONS_CALIFICACION"],
            "cont_usuarios"		=>	$row["CONT_CONTADOR"],
            "calificacion"		=>	array(
                "total"		=>	$_calificacion["total"],
                "entero"	=>	$_calificacion["entero"],
                "flotante"	=>	$_calificacion["flotante"]
            )
        );
    }


    if ($usuario != -1) {
        $consulta =
            "SELECT
					VUS_ID AS CONS_ID,
					VUS_CALIFICACION AS CONS_VOTACION,
					VUS_COMENTARIO AS CONS_COMENTARIO
				FROM VOTACION_USUARIO
				WHERE VUS_USUARIO_CALIFICADO = :usuarioCalificado
				AND VUS_USUARIO_CALIFICADOR = :usuario;";

        $arrayCondiciones = array(
            ":usuarioCalificado"	=>	$id,
            ":usuario"				=>	$usuario
        );


        $pdo = $conexion->prepare($consulta);
        $pdo->execute($arrayCondiciones);
        $res = $pdo->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) > 0) {
            $row = $res[0];

            $arrayVotacion["tuCalificacion"] = array(
                "id"			=>	$row["CONS_ID"],
                "votacion"		=>	$row["CONS_VOTACION"],
                "comentario"	=>	$row["CONS_COMENTARIO"]
            );
        }
    }


    $html =
        "<h3 class='_titulo'>Reputación</h3>
			<ul class='_contenedorEstrellas reputacion' data-calificacion='".$arrayVotacion["reputacion"]["calificacion"]["entero"].".".$arrayVotacion["reputacion"]["calificacion"]["flotante"]."'>
				<li>";


    $flotante = true;

    for ($x = 0; $x < 5; $x++) {
        $html.= "<a class='_estrella ".(($x + 1) <= $arrayVotacion["reputacion"]["calificacion"]["entero"] ? "_100" : ($flotante ? "_".$arrayVotacion["reputacion"]["calificacion"]["flotante"] : ""))."' data-value='".($x + 1)."'>".($x + 1)."</a>";
        if (($x + 1) > $arrayVotacion["reputacion"]["calificacion"]["entero"])
            $flotante = false;
    }


    $html.=
        "</li>
				<li ".($arrayVotacion["reputacion"]["cont_usuarios"] != 0 ? ("style='cursor:pointer;' onclick='template_votacionComentarios({usuarioCalificado: ".$id."});'") : "style='line-height:15px;'").">".($arrayVotacion["reputacion"]["cont_usuarios"] != 0 ? "Ver Comentarios" : "Este usuario no<br />ha sido calificado")."</li>
			</ul>";


    if ($usuario != -1) {
        if ($usuario != $propietario) {
            $html.=
                "<p class='_titulo'>".($arrayVotacion["reputacion"]["cont_usuarios"] == 0 ? "Sé el primero en calificar" : ($arrayVotacion["tuCalificacion"]["id"] == -1 ? "Califica a este usuario" : "Tu calificación"))."</p>
					<ul class='_contenedorEstrellas active' data-id='".$arrayVotacion["tuCalificacion"]["id"]."' data-calificacion='".$arrayVotacion["tuCalificacion"]["votacion"]."' data-usuariocalificado='".$id."'>
						<li>";


            for ($x = 0; $x < 5; $x++) {
                $html.= "<a class='_estrella ".(($x + 1) <= $arrayVotacion["tuCalificacion"]["votacion"] ? "_100" : "")."' data-value='".($x + 1)."'>".($x + 1)."</a>";
            }


            $html.=
                "</li>
					</ul>
					<p class='_caja'><textarea class='template_campos' id='template_calificar_comentario' placeholder='Comentario'>".$arrayVotacion["tuCalificacion"]["comentario"]."</textarea></p>
					<p class='_btnBoton'><span class='btnEnviar' onclick='template_eventosVotacion_validarCampos();'>Enviar</span></p>";
        }
    }


    $html = '
        <div class="col-md-8 comentarios">
            <h2 class="text-red">Comenta sobre el anunciante:</h2>
            <p class="_caja"><textarea class="template_campos" id="template_calificar_comentario" placeholder="Comentarios">'.$arrayVotacion["tuCalificacion"]["comentario"].'</textarea></p>
			<p class="_btnBoton"><span class="btnEnviar btn-inmueble btn" onclick="template_eventosVotacionResponsive_validarCampos();">Calificar</span></p>
        </div>
    ';

    $html .= '
        <div class="col-md-4 reputacion">
            <h2>Calificar:</h2>';

    if ($usuario != $propietario) {
        $html.=
            "<ul class='_contenedorEstrellas active calificacion' data-id='".$arrayVotacion["tuCalificacion"]["id"]."' data-calificacion='".$arrayVotacion["tuCalificacion"]["votacion"]."' data-usuariocalificado='".$id."'>
						<li>";


        for ($x = 0; $x < 5; $x++) {
            $html.= "<a class='_estrella ".(($x + 1) <= $arrayVotacion["tuCalificacion"]["votacion"] ? "_100" : "")."' data-value='".($x + 1)."'>".($x + 1)."</a>";
        }


        $html.=
            "</li>
					</ul>
			";
    }
    $html .= '<h2>Reputaci&oacute;n:</h2>';

    $html .= "<ul class='_contenedorEstrellas reputacion' data-calificacion='".$arrayVotacion["reputacion"]["calificacion"]["entero"].".".$arrayVotacion["reputacion"]["calificacion"]["flotante"]."'>
				<li>";


    $flotante = true;

    for ($x = 0; $x < 5; $x++) {
        $html.= "<a class='_estrella ".(($x + 1) <= $arrayVotacion["reputacion"]["calificacion"]["entero"] ? "_100" : ($flotante ? "_".$arrayVotacion["reputacion"]["calificacion"]["flotante"] : ""))."' data-value='".($x + 1)."'>".($x + 1)."</a>";
        if (($x + 1) > $arrayVotacion["reputacion"]["calificacion"]["entero"])
            $flotante = false;
    }


    $html.=
        "</li>
				<li ".($arrayVotacion["reputacion"]["cont_usuarios"] != 0 ? ("style='cursor:pointer; display:none;' onclick='template_votacionComentarios({usuarioCalificado: ".$id."});'") : "style='line-height:15px; display:none;'").">".($arrayVotacion["reputacion"]["cont_usuarios"] != 0 ? "Ver Comentarios" : "Este usuario no<br />ha sido calificado")."</li>
			</ul>";

    $html .='
        <!--a href="javascript:void(false);" class="contactar-usuario" onclick="gotoComment()"> <i class="fa fa-envelope"></i> Contactar Anunciante</a-->
    </div>
    ';


    $arrayRespuesta = array(
        "isExito"	=>	$isExito,
        "html"		=>	$html
    );

    echo json_encode($arrayRespuesta);
    return;
}


/*
    Borra el comentario
*/
if ($borrar) {
    $consulta = "DELETE FROM VOTACION_USUARIO WHERE VUS_ID = ?;";
    $pdo = $conexion->prepare($consulta);
    $pdo->execute(array($id));


    $arrayRespuesta = array(
        "isExito"	=>	$isExito,
        "id"		=>	$id
    );

    echo json_encode($arrayRespuesta);
    return;
}


$usuarioCalificado = $_POST["usuarioCalificado"];
$calificacion = $_POST["calificacion"];
$comentario = $_POST["comentario"];


if ($id == -1) {//inserta
    $consulta = "INSERT INTO VOTACION_USUARIO(VUS_USUARIO_CALIFICADOR, VUS_USUARIO_CALIFICADO, VUS_CALIFICACION, VUS_COMENTARIO) VALUES(:usuario, :usuarioCalificado, :calificacion, :comentario);";
    $pdo = $conexion->prepare($consulta);
    $pdo->execute(array(":usuario" => $usuario, ":usuarioCalificado" => $usuarioCalificado, ":calificacion" => $calificacion, ":comentario" => $comentario));
    $id = $conexion->lastInsertId();
}
else {//actualiza
    $consulta = "UPDATE VOTACION_USUARIO SET VUS_CALIFICACION = :calificacion, VUS_COMENTARIO = :comentario WHERE VUS_ID = :id;";
    $pdo = $conexion->prepare($consulta);
    $pdo->execute(array(":calificacion" => $calificacion, ":comentario" => $comentario, ":id" => $id));
}


$arrayRespuesta = array(
    "isExito"	=>	$isExito,
    "id"		=>	$id
);

echo json_encode($arrayRespuesta);

?>