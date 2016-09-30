<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	$palabra = isset($_GET["palabra"]) ? $_GET["palabra"] : "";
	
	
	$arrayInmuebles = array();
	$arrayCondiciones = array(":userId" => $_SESSION[userId], ":vigencia" => date("Y-m-d"));
	if ($palabra != "") {
		$arrayCondiciones[":palabra"] = "%".$palabra."%";
	}
	

	$conexion = crearConexionPDO();
	$consulta =
		"SELECT
			IMU_ID,
			IMU_TITULO,
			IMU_PRECIO,
			IMU_ESTADO,
			IMU_CIUDAD,
			IMU_COLONIA,
			IMU_CP,
			IMU_DESCRIPCION,
			IMU_DIMENSION_TOTAL,
			IMU_DIMENSION_CONSTRUIDA,
			IMU_WCS,
			IMU_RECAMARAS,
			TRI_TRANSACCION,
			(
				SELECT IIN_IMAGEN
				FROM IMAGEN_INMUEBLE
				WHERE IIN_INMUEBLE = IMU_ID
				ORDER BY IIN_ORDEN DESC LIMIT 1
			) AS CONS_IMAGEN,
			(
				SELECT EST_NOMBRE
				FROM ESTADO
				WHERE EST_ID = IMU_ESTADO
			) AS CONS_ESTADO,
			(
				SELECT CIU_NOMBRE
				FROM CIUDAD
				WHERE CIU_ID = IMU_CIUDAD
			) AS CONS_CIUDAD,
			(
				SELECT COL_NOMBRE
				FROM COLONIA
				WHERE COL_ID = IMU_COLONIA
			) AS CONS_COLONIA,
			(
				SELECT CP_CP
				FROM CP
				WHERE CP_ID = IMU_CP
			) AS CONS_CP,
			(
				SELECT FIN_ID
				FROM FAVORITO_INMUEBLE
				WHERE FIN_USUARIO = :userId
				AND FIN_INMUEBLE = IMU_ID LIMIT 1
			) AS CONS_LIKE
		FROM INMUEBLE, FAVORITO_INMUEBLE, TRANSACCION_INMUEBLE
		WHERE IMU_ID = FIN_INMUEBLE
		AND FIN_USUARIO = :userId
		AND TRI_INMUEBLE = IMU_ID
		AND(
			SELECT COUNT(IIN_ID)
			FROM IMAGEN_INMUEBLE
			WHERE IIN_INMUEBLE = IMU_ID
		) > 0
		".(
			$palabra != "" ?
			("AND (
				IMU_TITULO LIKE :palabra
				OR (
					SELECT COL_NOMBRE
					FROM COLONIA
					WHERE COL_ID = IMU_COLONIA
				) LIKE :palabra
			)") :
			""
		)."
		AND IMU_LIMITE_VIGENCIA >= :vigencia
		ORDER BY IMU_ID DESC";

	$pdo = $conexion->prepare($consulta);
	$pdo->execute($arrayCondiciones);
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {

		$arrayInmuebles[] = array(
			"id"				=>	$row["IMU_ID"],
			"titulo"			=>	$row["IMU_TITULO"],
			"precio"			=>	$row["IMU_PRECIO"],
			"estado"			=>	$row["IMU_ESTADO"],
			"ciudad"			=>	$row["IMU_CIUDAD"],
			"colonia"			=>	$row["IMU_COLONIA"],
			"cp"				=>	$row["IMU_CP"],
			"descripcion"		=>	$row["IMU_DESCRIPCION"] != NULL ? $row["IMU_DESCRIPCION"] : "",
			"dimensionTotal"	=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
			"dimensionConstruida"=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
			"wcs"				=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : "",
			"recamaras"			=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
			"transaccion"		=>	$row["TRI_TRANSACCION"],
			"imagen"			=>	$row["CONS_IMAGEN"],
			"estadoNombre"		=>	$row["CONS_ESTADO"],
			"ciudadNombre"		=>	$row["CONS_CIUDAD"],
			"coloniaNombre"		=>	$row["CONS_COLONIA"],
			"cpNombre"			=>	$row["CONS_CP"],
			"like"				=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"]
		);
	}
	
	
	CabeceraHTML("favoritos_ver3.css,favoritos.js");
	CuerpoHTML();
?>
<div class="favoritos_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
        <div>
        	<p class="titulo">Mis Favoritos</p>
            <p class="favoritos_contenedorBuscador">
            	<input type="text" id="favorito_buscador" class="template_campos" placeholder="Busca por título o por colonia" />
                <span class="btnBuscar">Buscar</span>
            </p>
            <?php
				$maxCadena = 30;

				for ($x = 0; $x < count($arrayInmuebles); $x++) {
					$textTiulo = strlen($arrayInmuebles[$x]["titulo"]) > $maxCadena ? (substr($arrayInmuebles[$x]["titulo"], 0, ($maxCadena - 3))."...") : $arrayInmuebles[$x]["titulo"];
					$etiquetaPrecio = $arrayInmuebles[$x]["transaccion"] != 3 ? "Precio" : "Precio por noche";

					echo
						'<div class="row property items template_catalogo_contenedorInfo" onclick="gotoURL(\'inmueble.php?id='.$arrayInmuebles[$x]["id"].'\');">'.
						'<div class="col-lg-4 col-sm-4 col-xs-12"><img class="img-responsive" src="'.$urlArchivos.$arrayInmuebles[$x]['imagen'].'"></div>'.
						'<div class="col-lg-8 col-sm-8 col-xs-12">'.

						'<div>'.
						'<div class="col-lg-8 col-sm-8 col-xs-12"> <h2 class="property header" onclick="gotoURL("inmueble.php?id="'.$arrayInmuebles[$x]["id"].'");">'.$textTiulo.'</h2></div>'.
						'<div class="col-lg-4 col-sm-4 col-xs-12 btns like">'.
						"<a class='btnBotones estrella ".($arrayInmuebles[$x]["like"] != 0 ? "activo" : "")."' data-id='".$arrayInmuebles[$x]["like"]."' data-inmueble='".$arrayInmuebles[$x]["id"]."'>Like</a>".
						'</div>'.
						'</div>'.
						'<span class="property subheader">'.$arrayInmuebles[$x]['coloniaNombre'].' | '.$arrayInmuebles[$x]['ciudadNombre'].", ".$arrayInmuebles[$x]['estadoNombre'].', México '.
						'C.P. '.$arrayInmuebles[$x]['cpNombre'].'</span>'.
						'<div class="information property">'.
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">'.
						'<div><i class="flaticon-graphicseditor63"></i> TERRENO <br />'.
						($arrayInmuebles[$x]['dimensionTotal'] != "" ? "<a class='otrosBotones dimensionTotal' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]['dimensionTotal']." m<sup>2</sup></a>" : "").
						'</div>'.
						'</div>'.
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">'.
						'<div><i class="flaticon-house158"></i> CONSTRUCCI&Oacute;N <br />'.
						($arrayInmuebles[$x]['dimensionConstruida'] != "" ? "<a class='otrosBotones dimensionConstruida' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]['dimensionConstruida']." m<sup>2</sup></a>" : "").
						'</div>'.
						'</div>'.
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">'.
						'<div><i class="flaticon-beds2"></i> CUARTOS <br />'.
						($arrayInmuebles[$x]['recamaras'] != "" ? "<a class='otrosBotones recamaras' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]['recamaras']."</a>" : "").
						'</div>'.
						'</div>'.
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">'.
						'<div><i class="flaticon-shower15"></i> BA&Ntilde;OS <br />'.
						($arrayInmuebles[$x]['wcs'] != "" ? "<a class='otrosBotones wcs' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".($arrayInmuebles[$x]['wcs'] % 1 == 0 ?$arrayInmuebles[$x]['wcs'] : $arrayInmuebles[$x]['wcs'])."</a>" : "").
						'</div>'.
						'</div>'.

						'</div>'.
						'<div class="property descriptions">'.$arrayInmuebles[$x]['descripcion'].'</div>'.
						'<div class="property price"><span class="precio" href="inmueble.php?id='.$arrayInmuebles[$x]["id"].'">'.$etiquetaPrecio.': $'.number_format($arrayInmuebles[$x]["precio"], 0, ".", ",").' MXN</span></div>'.
						'</div>'.
						'</div>';

					/*
					echo
						"<div class='template_catalogo_contenedorInfo'>
							<table>
								<tbody>
									<tr>
										<td class='imagen'>
											<div style='background:url(".$urlArchivos.$arrayInmuebles[$x]["imagen"].") no-repeat center center / 100% auto;' onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'></div>
										</td>
										<td class='descripcion'>
											<div class='like'>
												<h2 onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'>".$textTiulo."</h2><a class='btnBotones estrella ".($arrayInmuebles[$x]["like"] != 0 ? "activo" : "")."' data-id='".$arrayInmuebles[$x]["like"]."' data-inmueble='".$arrayInmuebles[$x]["id"]."'>Like</a>
											</div>
											<p class='btns'>".
												($arrayInmuebles[$x]["dimensionTotal"] != "" ? "<a class='otrosBotones dimensionTotal' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]["dimensionTotal"]." m<sup>2</sup></a>" : "").
												($arrayInmuebles[$x]["dimensionConstruida"] != "" ? "<a class='otrosBotones dimensionConstruida' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."&regresar=1'>".$arrayInmuebles[$x]["dimensionConstruida"]." m<sup>2</sup></a>" : "").
												($arrayInmuebles[$x]["wcs"] != "" ? "<a class='otrosBotones wcs' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".(fmod($arrayInmuebles[$x]["wcs"], 1) == 0 ? (int)$arrayInmuebles[$x]["wcs"] : number_format($arrayInmuebles[$x]["wcs"], 1))."</a>" : "").
												($arrayInmuebles[$x]["recamaras"] != "" ? "<a class='otrosBotones recamaras' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]["recamaras"]."</a>" : "").
											"</p>
											<div class='info' onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'>
												<h3>".$arrayInmuebles[$x]["coloniaNombre"]."</h3>
												<p>".$arrayInmuebles[$x]["ciudadNombre"].", ".$arrayInmuebles[$x]["estadoNombre"].", México</p>
												<p>C.P. ".$arrayInmuebles[$x]["cpNombre"]."</p><br />
												<p class='descripcion'>".$arrayInmuebles[$x]["descripcion"]."</p>
											</div>
											<div class='precioVerMas'><span class='precio' onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'>".$etiquetaPrecio.": $".number_format($arrayInmuebles[$x]["precio"], 0, ".", ",")." MXN</span><span class='verMas' onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'>Ver más</span></div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>"; */
				}
			?>
        </div>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>