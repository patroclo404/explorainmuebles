<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$urlArchivos = "images/images/";
	
	$conexion = crearConexionPDO();
	$arrayInmuebles = array();
	
	
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	
	
	$consulta =
		"SELECT
			INM_NOMBRE_EMPRESA,
			INM_LOGOTIPO,
			INM_CREATE,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE, USUARIO
				WHERE IMU_USUARIO = USU_ID
				AND USU_INMOBILIARIA = :inmobiliaria
			) AS CONT_INMUEBLES
		FROM INMOBILIARIA
		WHERE INM_ID = :inmobiliaria;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":inmobiliaria" => $_GET["id"]));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	
	
	$inmobiliaria = array(
		"id"		=>	$_GET["id"],
		"nombre"	=>	$row["INM_NOMBRE_EMPRESA"],
		"create"	=>	getDateNormal($row["INM_CREATE"]),
		"logotipo"	=>	$row["INM_LOGOTIPO"] != NULL ? $urlArchivos.$row["INM_LOGOTIPO"] : "",
		"cantidad"	=>	$row["CONT_INMUEBLES"]
	);
	
	
	$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 0;
	$elem = 10;
	
	
	$consultaPaginacion = "";
	$consultaCondiciones =
		"FROM INMUEBLE, USUARIO, TRANSACCION_INMUEBLE
		WHERE IMU_USUARIO = USU_ID
		AND USU_INMOBILIARIA = :inmobiliaria
		AND TRI_INMUEBLE = IMU_ID
		AND(
			SELECT COUNT(IIN_ID)
			FROM IMAGEN_INMUEBLE
			WHERE IIN_INMUEBLE = IMU_ID
		) > 0
		AND IMU_LIMITE_VIGENCIA >= :vigencia
		ORDER BY IMU_ID DESC";
		
		
	$arrayCondiciones = array(
		":inmobiliaria" => $inmobiliaria["id"],
		":vigencia" => date("Y-m-d")
	);
	
	
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
			( ".(
				$usuario != -1
				? (
					"SELECT FIN_ID
					FROM FAVORITO_INMUEBLE
					WHERE FIN_USUARIO = ".$usuario."
					AND FIN_INMUEBLE = IMU_ID"
				) : -1
			)." ) AS CONS_LIKE ".
		$consultaCondiciones;

		
	$consultaPaginacion = 
		"SELECT COUNT(IMU_ID) AS CONS_ELEM ".
		$consultaCondiciones.";";
		
	$consulta.= " LIMIT ".($elem * $pagina).",".$elem.";";
		
		
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
	
	
	$pdo = $conexion->prepare($consultaPaginacion);
	$pdo->execute($arrayCondiciones);
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$numeroElementos = $row["CONS_ELEM"];
	
	
	$arrayResultados = array(
		"pagina"			=>	$pagina,
		"elem"				=>	$elem,
		"numeroElementos"	=>	$numeroElementos,
		"maxPaginas"		=>	ceil($numeroElementos / $elem)
	);
	
	
	CabeceraHTML("inmobiliaria.css,inmobiliaria_ver4.js");
	CuerpoHTML();
?>
<div class="inmobiliaria_cuerpo">
	<div class="columna2 container">
    	<div class="datosInmobiliaria clearfix">
			<div class="col-md-3 col-xs-12">
				<h2 class="titulo">Anunciante:</h2>
				<?php

				$_maxNombre = 35;


				if ($inmobiliaria["id"] == 0) {
					$_nombre = strlen($inmobiliaria["nombre"]) > $_maxNombre ? substr($inmobiliaria["nombre"], 0, ($_maxNombre - 3))."..." : $inmobiliaria["nombre"];

					echo
						"
				<div class='user normal'>".$_nombre."</div>
				<h4>Miembro desde:</h4>
				<p class='member-since'>".$inmobiliaria["create"]."</p>
				";
				}
				else {
					$_nombre = strlen($inmobiliaria["nombre"]) > $_maxNombre ? substr($inmobiliaria["nombre"], 0, ($_maxNombre - 3))."..." : $inmobiliaria["nombre"];

					echo

						($inmobiliaria["logotipo"] != "" ? ("<img src='".$inmobiliaria["logotipo"]."' class='logotipo img-responsive' onclick='gotoURL(\"inmobiliaria.php?id=".$inmobiliaria["id"]."\");' />") : "").
						"<div class='user inmobiliaria'>".$_nombre."</div>
				<h4>Miembro desde:</h4>
				<p class='member-since'>".$inmobiliaria["create"]."</p>";
				}
				?>
			</div>
			<div class="col-md-9 col-xs-12">
				<a name="contacto-anunciante"></a>
				<div class="inmueble_contacto hidden-print">
					<h2>Contactar Anunciante</h2>
					<div>
						<div class="col-md-12"><input type="text" id="contacto_nombre" class="template_campos" placeholder="Nombre" /></div>
					</div>
					<div>
						<div class="col-md-6"><input type="text" id="contacto_email" class="template_campos" placeholder="E-mail" /></div>
						<div class="col-md-6"><input type="text" id="contacto_telefono" class="template_campos" placeholder="Teléfono" /></div>
					</div>

					<div>
						<div class="col-md-12"><textarea id="contacto_mensaje" class="template_campos" placeholder="Mensaje"></textarea></div>
					</div>
					<div>
						<div class="col-md-12"><span class="btn btn-inmueble btn-lg" data-inmueble="<?php echo $inmueble["id"]; ?>" onclick="inmueble_validarContacto();">Enviar</span></div>
					</div>

				</div>
			</div>


        </div>
        <div>
        	<h2 class="text-red">Propiedades de la inmobiliaria</h2>
            <?php
				$maxCadena = 30;
			
				for ($x = 0; $x < count($arrayInmuebles); $x++) {
					$textTitulo = strlen($arrayInmuebles[$x]["titulo"]) > $maxCadena ? (substr($arrayInmuebles[$x]["titulo"], 0, ($maxCadena - 3))."...") : $arrayInmuebles[$x]["titulo"];
					$etiquetaPrecio = $arrayInmuebles[$x]["transaccion"] != 3 ? "Precio" : "Precio por noche";
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
												<h2 onclick='gotoURL(\"inmueble.php?id=".$arrayInmuebles[$x]["id"]."\");'>".$textTitulo."</h2>".(
													$arrayInmuebles[$x]["like"] != -1
													? (
														"<a class='btnBotones estrella ".($arrayInmuebles[$x]["like"] != 0 ? "activo" : "")."' data-id='".$arrayInmuebles[$x]["like"]."' data-inmueble='".$arrayInmuebles[$x]["id"]."'>Like</a>"
													) : (
														"<a class='btnBotones estrella' data-id='".$arrayInmuebles[$x]["like"]."' data-inmueble='".$arrayInmuebles[$x]["id"]."'>Like</a>"
													)
											)."</div>
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
						</div>";
					*/
					echo
						'<div class="row property items" onclick="gotoURL(\'inmueble.php?id='.$arrayInmuebles[$x]["id"].'\');">'.
						'<div class="col-lg-4 col-sm-4 col-xs-12"><img class="img-responsive" src="'.$urlArchivos.$arrayInmuebles[$x]['imagen'].'"></div>'.
						'<div class="col-lg-8 col-sm-8 col-xs-12">'.
						'<h2 class="property header">'.$textTitulo.'</h2>'.
						'<span class="property subheader">'.$arrayInmuebles[$x]['coloniaNombre'].' | '.$arrayInmuebles[$x]['ciudadNombre'].", ".$arrayInmuebles[$x]['estadoNombre'].', México '.
						'C.P. '.$arrayInmuebles[$x]['cpNombre'].'</span>'.
						'<div class="information property">'.
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info hidden-xs">'.
						'<div><i class="flaticon-graphicseditor63"></i> TERRENO <br />'.
						($arrayInmuebles[$x]['dimensionTotal'] != "" ? "<a class='otrosBotones dimensionTotal' href='inmueble.php?id=".$arrayInmuebles[$x]["id"]."'>".$arrayInmuebles[$x]['dimensionTotal']." m<sup>2</sup></a>" : "").
						'</div>'.
						'</div>'.
						'<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info hidden-xs">'.
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
						'<div class="property descriptions hidden-xs">'.$arrayInmuebles[$x]['descripcion'].'</div>'.
						'<div class="property price"><span class="precio" href="inmueble.php?id='.$arrayInmuebles[$x]["id"].'">'.$etiquetaPrecio.': $'.number_format($arrayInmuebles[$x]["precio"], 0, ".", ",").' MXN</span></div>'.
						'</div>'.
						'</div>';
				}
			?>
            <div class="paginacionNumeracion">
            	<?php
				
					$numeroElementos = $arrayResultados["numeroElementos"];
					$maxPaginas = $arrayResultados["maxPaginas"];
					$numAntesDespues = 5;
	
	
					if ($pagina > 0) {
						echo "<a href='inmobiliaria.php?id=".$inmobiliaria["id"]."&pagina=".($pagina - 1)."' class='anterior'>&lt; Anterior</a>";
						
						$posIni = ($pagina - $numAntesDespues) < 0 ? 0 : ($pagina - $numAntesDespues);
						
						for ($x = $posIni; $x < $pagina; $x++) {
							echo "<a href='inmobiliaria.php?id=".$inmobiliaria["id"]."&pagina=".$x."'>".($x + 1)."</a>";
						}
					}
	
					echo "<a class='active' href='inmobiliaria.php?id=".$inmobiliaria["id"]."&pagina=".$pagina."'>".($pagina + 1)."</a>";
					
					
					if ($pagina < ($maxPaginas - 1)) {
						$posFin = ($pagina + $numAntesDespues) >= $maxPaginas ? ($maxPaginas - 1) : ($pagina + $numAntesDespues);
						
						for ($x = ($pagina + 1); $x <= $posFin; $x++) {
							echo "<a href='inmobiliaria.php?id=".$inmobiliaria["id"]."&pagina=".$x."'>".($x + 1)."</a>";
						}
						
						echo "<a href='inmobiliaria.php?id=".$inmobiliaria["id"]."&pagina=".($pagina + 1)."' class='siguiente'>Siguiente &gt;</a>";
					}
				?>
            </div>
        </div>
        <div class="col-md-12 col-xs-12 np">
				<div class="template_contenedorReputacionResponsive" data-tipo="inmobiliaria" data-id="<?php echo $inmobiliaria["id"] == 0 ? $inmobiliaria["id"] : $inmobiliaria["id"]; ?>" data-propietario="<?php echo $inmobiliaria["id"]; ?>"></div>
		</div>
		<br><br><br>
		<script>
			template_votacionComentariosResponsive({inmobiliaria: <?php echo $inmobiliaria["id"]; ?>});
		</script>
		<div class="col-md-12 col-xs-12 np">
			<div id="template_comentariosResponsive"></div>
		</div>
		
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>