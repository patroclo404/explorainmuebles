<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$urlArchivos = "images/images/";
	
	$conexion = crearConexionPDO();
	$arrayInmuebles = array();
	
	
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	
	
	$consulta =
		"SELECT
			USU_NOMBRE,
			USU_IMAGEN,
			USU_CREATE,
			(
				SELECT COUNT(IMU_ID)
				FROM INMUEBLE
				WHERE IMU_USUARIO = :usuario
			) AS CONT_INMUEBLES
		FROM USUARIO
		WHERE USU_ID = :usuario;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":usuario" => $_GET["id"]));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	
	
	$vendedor = array(
		"id"		=>	$_GET["id"],
		"nombre"	=>	$row["USU_NOMBRE"],
		"create"	=>	getDateNormal($row["USU_CREATE"]),
		"imagen"	=>	$row["USU_IMAGEN"] != NULL ? $urlArchivos.$row["USU_IMAGEN"] : "",
		"cantidad"	=>	$row["CONT_INMUEBLES"]
	);

	$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 0;
	$elem = 10;
	
	
	$consultaPaginacion = "";
	$consultaCondiciones =
		"FROM INMUEBLE, TRANSACCION_INMUEBLE
		WHERE IMU_USUARIO = :vendedor
		AND TRI_INMUEBLE = IMU_ID
		AND(
			SELECT COUNT(IIN_ID)
			FROM IMAGEN_INMUEBLE
			WHERE IIN_INMUEBLE = IMU_ID
		) > 0
		AND IMU_LIMITE_VIGENCIA >= :vigencia
		ORDER BY IMU_ID DESC";
		
		
	$arrayCondiciones = array(
		":vendedor" => $vendedor["id"],
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
	
	
	CabeceraHTML("usuario.css,usuario_ver3.js");
	CuerpoHTML();

?>
<div class="usuario_cuerpo container">
	<section class="columna2">
    	<div class="datosUsuario clearfix">
			<div class="col-md-3">
				<h2 class="titulo">Anunciante:</h2>
				<?php

				$_maxNombre = 35;

				if ($vendedor["imagen"]){
					echo "<img class='img-responsive' src='".$vendedor['imagen']."'>";
				}

				if ($vendedor["inmobiliaria"]["id"] == 0) {
					$_nombre = strlen($vendedor["nombre"]) > $_maxNombre ? substr($vendedor["nombre"], 0, ($_maxNombre - 3))."..." : $vendedor["nombre"];

					echo
						"
				<div class='user normal'>".$_nombre."</div>
				<h4>Miembro desde:</h4>
				<p class='member-since'>".$vendedor["create"]."</p>
				";
				}
				else {
					$_nombre = strlen($vendedor["inmobiliaria"]["nombre"]) > $_maxNombre ? substr($vendedor["inmobiliaria"]["nombre"], 0, ($_maxNombre - 3))."..." : $vendedor["inmobiliaria"]["nombre"];

					echo

						($vendedor["inmobiliaria"]["logotipo"] != "" ? ("<img src='".$vendedor["inmobiliaria"]["logotipo"]."' class='logotipo img-responsive' onclick='gotoURL(\"inmobiliaria.php?id=".$vendedor["inmobiliaria"]["id"]."\");' />") : "").
						"<div class='user inmobiliaria'><a href='inmobiliaria.php?id=".$vendedor["inmobiliaria"]["id"]."'>".$_nombre."</a></div>
				<h4>Miembro desde:</h4>
				<p class='member-since'>".$vendedor["inmobiliaria"]["create"]."</p>";
				}
				?>
			</div>
			<div class="col-md-9">
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
        	<h2 class="text-red">Propiedades del anunciante</h2>
            <?php
				$maxCadena = 30;
			
				for ($x = 0; $x < count($arrayInmuebles); $x++) {
					$textTitulo = strlen($arrayInmuebles[$x]["titulo"]) > $maxCadena ? (substr($arrayInmuebles[$x]["titulo"], 0, ($maxCadena - 3))."...") : $arrayInmuebles[$x]["titulo"];
					$etiquetaPrecio = $arrayInmuebles[$x]["transaccion"] != 3 ? "Precio" : "Precio por noche";
					$url= $arrayInmuebles[$x]["transaccion"]=1 ? 'renta':($arrayInmuebles[$x]["transaccion"]=2?'venta':'renta-vacacional').'/'.
					$arrayInmuebles[$x]['tipoInmueble'].'/'.$arrayInmuebles[$x]['estadoNombre'].'/'.$arrayInmuebles[$x]['ciudadNombre'].'/'
					.$arrayInmuebles[$x]['id'];
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
						echo "<a href='usuario.php?id=".$vendedor["id"]."&pagina=".($pagina - 1)."' class='anterior'>&lt; Anterior</a>";
						
						$posIni = ($pagina - $numAntesDespues) < 0 ? 0 : ($pagina - $numAntesDespues);
						
						for ($x = $posIni; $x < $pagina; $x++) {
							echo "<a href='usuario.php?id=".$vendedor["id"]."&pagina=".$x."'>".($x + 1)."</a>";
						}
					}
	
					echo "<a class='active' href='usuario.php?id=".$vendedor["id"]."&pagina=".$pagina."'>".($pagina + 1)."</a>";
					
					
					if ($pagina < ($maxPaginas - 1)) {
						$posFin = ($pagina + $numAntesDespues) >= $maxPaginas ? ($maxPaginas - 1) : ($pagina + $numAntesDespues);
						
						for ($x = ($pagina + 1); $x <= $posFin; $x++) {
							echo "<a href='usuario.php?id=".$vendedor["id"]."&pagina=".$x."'>".($x + 1)."</a>";
						}
						
						echo "<a href='usuario.php?id=".$vendedor["id"]."&pagina=".($pagina + 1)."' class='siguiente'>Siguiente &gt;</a>";
					}
				?>
            </div>
        </div>
		<div class="col-md-12 col-xs-12 np">
			<div class="template_contenedorReputacionResponsive" data-tipo="usuario" data-id="<?php echo $vendedor["id"] == 0 ? $inmueble["usuario"] : $vendedor["id"]; ?>" data-propietario="<?php echo $vendedor["id"]; ?>"></div>
		</div>
		<br><br><br>
		<script>
			template_votacionComentariosResponsive({usuarioCalificado: <?php echo $vendedor["id"]; ?>});
		</script>
		<div class="col-md-12 col-xs-12 np">
			<div id="template_comentariosResponsive"></div>
		</div>
		
    </section>


</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>