<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	$conexion = crearConexionPDO();
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	$palabra = isset($_GET["palabra"]) ? $_GET["palabra"] : "";
	
	
	$arrayInmuebles = array();
	$arrayUsuarios = array();
	$arrayCondiciones = array(":userId" => $_SESSION[userId]);
	if ($palabra != "") {
		$arrayCondiciones[":palabra"] = "%".$palabra."%";
	}
	
	
	if ($_SESSION[userAdminInmobiliaria] == 1) {
		$consulta = "SELECT USU_ID FROM USUARIO WHERE USU_INMOBILIARIA = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($_SESSION[userInmobiliaria]));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$arrayUsuarios[] = $row["USU_ID"];
		}
	}
	
	
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
			IMU_LIMITE_VIGENCIA,
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
				AND FIN_INMUEBLE = IMU_ID
			) AS CONS_LIKE
		FROM INMUEBLE, TRANSACCION_INMUEBLE
		WHERE TRI_INMUEBLE = IMU_ID
		AND IMU_USUARIO ".($_SESSION[userAdminInmobiliaria] == 1 ? (" IN (".implode(",", $arrayUsuarios).")") : (" = ".$_SESSION[userId]))."
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
		ORDER BY IMU_LIMITE_VIGENCIA";
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
			"limiteVigencia"	=>	getDateNormal($row["IMU_LIMITE_VIGENCIA"]),
			"transaccion"		=>	$row["TRI_TRANSACCION"],
			"imagen"			=>	$row["CONS_IMAGEN"],
			"estadoNombre"		=>	$row["CONS_ESTADO"],
			"ciudadNombre"		=>	$row["CONS_CIUDAD"],
			"coloniaNombre"		=>	$row["CONS_COLONIA"],
			"cpNombre"			=>	$row["CONS_CP"],
			"like"				=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"]
		);
	}
	
	
	$consulta = "SELECT PRO_PRECIO, PRO_PROMOCION FROM PROMOCION WHERE PRO_ID = 1;";
	$pdo = $conexion->query($consulta);
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$total = $row["PRO_PRECIO"];
	$textoPromocion = $row["PRO_PROMOCION"];
	
	
	$variables = "_precioPublicar=".$total;
	
	
	CabeceraHTML("misAnuncios_ver7.css,misAnuncios_ver5.js", $variables);
	CuerpoHTML();
?>
<div class="misAnuncios_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
        <div>
        	<p class="titulo">Mis Anuncios</p>
            <p class="misAnuncios_contenedorBuscador">
            	<input type="text" id="misAnuncios_buscador" class="template_campos" placeholder="Busca por título o por colonia" />
                <span class="btnBuscar">Buscar</span>
            </p>
            <?php
				$maxCadena = 30;
			
				for ($x = 0; $x < count($arrayInmuebles); $x++) {
					$textTiulo = strlen($arrayInmuebles[$x]["titulo"]) > $maxCadena ? (substr($arrayInmuebles[$x]["titulo"], 0, ($maxCadena - 3))."...") : $arrayInmuebles[$x]["titulo"];
					$etiquetaPrecio = $arrayInmuebles[$x]["transaccion"] != 3 ? "Precio" : "Precio por noche";

					/*
					echo
						"<div class='template_catalogo_contenedorInfo'>
							<table>
								<tbody>
									<tr>
										<td class='imagen'>
											<div style='background:url(".$urlArchivos.$arrayInmuebles[$x]["imagen"].") no-repeat center center / 100% auto;' onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'></div>";
											
					
					if ($_SESSION[userInmobiliaria] == 0) {//usuario
						$mensajeRenovacion = "No publicado";
						$mensajeBoton = "Publicar";
						
						
						if ($arrayInmuebles[$x]["limiteVigencia"] != "01/01/2000") {
							$partes = explode("/", $arrayInmuebles[$x]["limiteVigencia"]);
							$timeStamp_inmueble = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
							$timeStamp_hoy_5dias = mktime(0, 0, 0, date("m"), date("d")+5, date("Y"));
							$timeStamp_hoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
							
							if ($timeStamp_inmueble >= $timeStamp_hoy_5dias) {
								$mensajeRenovacion = "Vigente hasta el ".$arrayInmuebles[$x]["limiteVigencia"];
								$mensajeBoton = "";
							}
							else {
								if ($timeStamp_inmueble < $timeStamp_hoy)
									$mensajeRenovacion = "Vencido desde el ".$arrayInmuebles[$x]["limiteVigencia"];
								else
									$mensajeRenovacion = "Vence el ".$arrayInmuebles[$x]["limiteVigencia"];
									
								$mensajeBoton = "Renovar";
							}
						}
						
						
						echo
							"<div class='mensajeRenovacion'>
								<p>".$mensajeRenovacion."</p><br />
								<p><a href='javascript:misAnuncios_popupRenovar(".$arrayInmuebles[$x]["id"].", \"".$mensajeBoton."\");'>".$mensajeBoton."</a></p>
							</div>";
					}
					else {//inmobiliaria
						//consulta la validez de la inmobiliaria
						$consulta = "SELECT INM_VALIDEZ FROM INMOBILIARIA WHERE INM_ID = ?;";
						$pdo = $conexion->prepare($consulta);
						$pdo->execute(array($_SESSION[userInmobiliaria]));
						$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
						$row = $res[0];
						$partes = explode("-", $row["INM_VALIDEZ"]);
						$timeStamp_inmobiliaria = mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
						$timeStamp_hoy = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
						
					
						$mensajeRenovacion =
							"<p>No publicado</p>
							<p onclick='gotoURL(\"opcionPagoInmobiliaria.php?idInmobiliaria=".$_SESSION[userInmobiliaria]."&inmueble=".$arrayInmuebles[$x]["id"]."\");' style='cursor:pointer; text-decoration:underline;'>Activar Anuncio</p>";
							
						
						if ($timeStamp_inmobiliaria >= $timeStamp_hoy) {
							if ($arrayInmuebles[$x]["limiteVigencia"] != "01/01/2000") {
								$partes = explode("/", $arrayInmuebles[$x]["limiteVigencia"]);
								$timeStamp_inmueble = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
								
								if ($timeStamp_inmueble >= $timeStamp_hoy) {
									$mensajeRenovacion =
										"<p>Vigente hasta el ".$arrayInmuebles[$x]["limiteVigencia"]."</p>
										<p onclick='misAnuncios_desactivar(".$arrayInmuebles[$x]["id"].");' style='cursor:pointer; text-decoration:underline;'>Desactivar Anuncio</p>";
								}
								else
									$mensajeRenovacion =
										"<p>Vencido desde el ".$arrayInmuebles[$x]["limiteVigencia"]."</p>
										<p onclick='gotoURL(\"opcionPagoInmobiliaria.php?idInmobiliaria=".$_SESSION[userInmobiliaria]."&inmueble=".$arrayInmuebles[$x]["id"]."\");' style='cursor:pointer; text-decoration:underline;'>Activar Anuncio</p>";
							}
						}
						
						
						echo
							"<div class='mensajeRenovacion'>
								".$mensajeRenovacion."
							</div>";
					}
					
					
					echo
										"</td>
										<td class='descripcion'>
											<div class='like'>
												<h2 onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'>".$textTiulo."</h2><a class='btnBotones estrella ".($arrayInmuebles[$x]["like"] != 0 ? "activo" : "")."' data-id='".$arrayInmuebles[$x]["like"]."' data-inmueble='".$arrayInmuebles[$x]["id"]."'>Like</a>
											</div>
											<p class='btns'>".
												($arrayInmuebles[$x]["dimensionTotal"] != "" ? "<a class='otrosBotones dimensionTotal' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]["dimensionTotal"]." m<sup>2</sup></a>" : "").
												($arrayInmuebles[$x]["dimensionConstruida"] != "" ? "<a class='otrosBotones dimensionConstruida' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."&regresar=1'>".$arrayInmuebles[$x]["dimensionConstruida"]." m<sup>2</sup></a>" : "").
												($arrayInmuebles[$x]["wcs"] != "" ? "<a class='otrosBotones wcs' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".(fmod($arrayInmuebles[$x]["wcs"], 1) == 0 ? (int)$arrayInmuebles[$x]["wcs"] : number_format($arrayInmuebles[$x]["wcs"], 1))."</a>" : "").
												($arrayInmuebles[$x]["recamaras"] != "" ? "<a class='otrosBotones recamaras' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]["recamaras"]."</a>" : "").
												"<a href='javascript:misAnuncios_borrar(".$arrayInmuebles[$x]["id"].");' class='btnBotones borrar' style='float:right;' title='Borrar'>x</a>
												<a href='javascript:gotoURLPOST(\"nuevoAnuncio.php\", {edit: 1, id: ".$arrayInmuebles[$x]["id"]."});' class='btnBotones editar' style='float:right; margin-right:2px;' title='Editar'></a>
											</p>
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
						</div>";
					*/
					echo
						'<div class="row property items template_catalogo_contenedorInfo" onclick="gotoURL(\'inmueble.php?id='.$arrayInmuebles[$x]["id"].'\');">'.
						'<div class="col-lg-4 col-sm-4 col-xs-12"><img class="img-responsive" src="'.$urlArchivos.$arrayInmuebles[$x]['imagen'].'"></div>'.
						'<div class="col-lg-8 col-sm-8 col-xs-12">'.

						'<div>'.
						'<div class="col-lg-8 col-sm-8 col-xs-12"> <h2 class="property header" >'.$textTiulo.'</h2></div>'.
						'<div class="col-lg-4 col-sm-4 col-xs-12 btns like">'.
						"<a class='btnBotones estrella ".($arrayInmuebles[$x]["like"] != 0 ? "activo" : "")."' data-id='".$arrayInmuebles[$x]["like"]."' data-inmueble='".$arrayInmuebles[$x]["id"]."'>Like</a>".
						"<a href='javascript:misAnuncios_borrar(".$arrayInmuebles[$x]["id"].");' class='btnBotones borrar' style='float:right;' title='Borrar'>x</a>
						 <a href='javascript:gotoURLPOST(\"nuevoAnuncio.php\", {edit: 1, id: ".$arrayInmuebles[$x]["id"]."});' class='btnBotones editar' style='float:right; margin-right:2px;' title='Editar'></a>".
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
				}
			?>
        </div>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales("misAnuncios_cerrarPopup");
?>
<div id="misAnuncios_popupRenovar" class="templatePopUp misAnuncios_popupRenovar">
    <span class="btnCerrar" onclick="template_principalCerrarPopUp(misAnuncios_cerrarPopup);">x</span>
    <table>
        <tbody>
            <tr>
                <td>
                	<p>El precio para <span id="_tipo">Publicar/Renovar</span> tu anuncio es de $<?php echo $total; ?></p><br />
                    <?php
						if ($total > 0) {
					?>
                    <p><?php echo $textoPromocion; ?></p><br />
                    <p id="_enviar" class="guardar" onclick=""><a class="btnBotones palomita">Guardar</a>Pagar ahora</p><p class="guardar" style="margin-left:100px;" onclick="template_principalCerrarPopUp(misAnuncios_cerrarPopup);"><a class="btnBotones palomita">Guardar</a>Ahora no</p>
                    <?php
						}
						else {
					?>
                     <p><?php echo $textoPromocion; ?></p><br />
                    <p id="_enviar" class="guardar" onclick=""><a class="btnBotones palomita">Guardar</a>Publicar</p>
                    <?php
						}
					?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	FinHTML();
?>