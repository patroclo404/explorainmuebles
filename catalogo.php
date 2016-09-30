<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	$conexion = crearConexionPDO();
	
	
	$variables = "";
	$arrayDatosPost = array();
	
	
	$transaccion = -1;
	$tipoInmueble = -1;
	$estado = -1;
	$ciudad = -1;
	$pagina = 0;
	$elem = 10;

	if ($_GET["estado"] == 'Estado'){
		$_GET["estado"] = 'todo-mexico';
	}

	if ($_GET["ciudad"] == 'Municipio'){
		$_GET["ciudad"] = 'todas-las-ciudades';
	}

	if ($_GET["tipoInmueble"] == 'inmuebles'){
		$_GET["tipoInmueble"] = 'todos-los-tipos';
	}

	//if (isset($_GET["tipoInmueble"])) {
		//ide de transaccion
		$transaccion = $_GET["transaccion"];
		$tipoInmueble = $_GET["tipoInmueble"] != "todos-los-tipos" ? $_GET["tipoInmueble"] : -1;
		if ($tipoInmueble != -1) {
			$_terminacion = $tipoInmueble{strlen($tipoInmueble) - 2}.$tipoInmueble{strlen($tipoInmueble) - 1};
			$tipoInmueble = ucfirst(substr($tipoInmueble, 0, ($_terminacion == "es" ? (strlen($tipoInmueble) - 2) : (strlen($tipoInmueble) - 1))));
		}
		$estado = $_GET["estado"] != "todo-mexico"   ? $_GET["estado"] : -1;
		$ciudad = $_GET["ciudad"] != "todas-las-ciudades" ? $_GET["ciudad"] : -1;
		$pagina = isset($_SESSION[userFiltros]["pagina"]) ? $_SESSION[userFiltros]["pagina"] : 0;
		$elem = isset($_SESSION[userFiltros]["elem"]) ? $_SESSION[userFiltros] : $elem;

		
		//id de tipo inmueble
		if ($tipoInmueble != -1) {
			$consulta = "SELECT TIN_ID FROM TIPO_INMUEBLE WHERE TIN_NOMBRE = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($tipoInmueble));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$tipoInmueble = $row["TIN_ID"];
		}
		
		//id de estado
		if ($estado != -1) {
			$consulta = "SELECT EST_ID FROM ESTADO WHERE EST_NOMBRE = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($estado));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$estado = $row["EST_ID"];
		}
		
		//id de ciudad
		if ($ciudad != -1) {
			$consulta = "SELECT CIU_ID FROM CIUDAD WHERE CIU_NOMBRE = :ciudad AND CIU_ESTADO = :estado;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array(":ciudad" => $ciudad, ":estado" => $estado));
			$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
			$row = $res[0];
			$ciudad = $row["CIU_ID"];
		}
		
		
		$preciosMin = -1;
		$preciosMax = -1;
		$wcs = -1;
		$recamaras = -1;
		$orden = 3;
		
		if (isset($_SESSION[userFiltros])) {
			if (isset($_SESSION[userFiltros]["preciosMin"])) {
				$preciosMin = $_SESSION[userFiltros]["preciosMin"];
				$preciosMax = $_SESSION[userFiltros]["preciosMax"];
			}

			$wcs = $_SESSION[userFiltros]["wcs"];
			$recamaras = $_SESSION[userFiltros]["recamaras"];
			
			if (isset($_SESSION[userFiltros]["orden"]))
				$orden = $_SESSION[userFiltros]["orden"];	
				
			if (isset($_SESSION[userFiltros]["elem"]))
				$elem = $_SESSION[userFiltros]["elem"];
		}




		$colonia = 	$_SESSION[userFiltros]["colonia"]? $_SESSION[userFiltros]["colonia"]: -1;
		$variables.= "post_transaccion=".$transaccion.",post_tipoInmueble=".$tipoInmueble.",post_estado=".$estado.",post_ciudad=".$ciudad.",post_colonia=".$colonia.",post_preciosMin=".$preciosMin.",post_preciosMax=".$preciosMax.",post_wcs=".$wcs.",post_recamaras=".$recamaras.",post_orden=".$orden.",post_elem=".$elem.",post_pagina=".$pagina;
		

		if (isset($_SESSION[userFiltros])) {
			if (isset($_SESSION[userFiltros]["preciosMin2"])) {
				$variables.= ",post_preciosMin2=".$_SESSION[userFiltros]["preciosMin2"].",post_preciosMax2=".$_SESSION[userFiltros]["preciosMax2"];
			}
		}
		
		//agrega los selectes temporales por post
		$_tempArraysSelects = array("antiguedad", "estadoConservacion", "amueblado", "dimensionTotalMin", "dimensionTotalMax", "dimensionConstruidaMin", "dimensionConstruidaMax");
		
		for ($x = 0; $x < count($_tempArraysSelects); $x++) {
			if (isset($_GET[$_tempArraysSelects[$x]])) {
				$variables.= ",post_".$_tempArraysSelects[$x]."=".$_GET[$_tempArraysSelects[$x]];
			}
		}
		
		//agrega todos los demas inputs
		$_tempArrayInputs = array(
			"codigo", "cuotaMantenimiento", "elevador", "estacionamientoVisitas", "numeroOficinas", "cocinaEquipada", "estudio", "cuartoServicio", "cuartoTV", "bodega",
			"terraza", "jardin", "areaJuegosInfantiles", "comedor", "serviciosBasicos", "gas", "lineaTelefonica", "internetDisponible", "aireAcondicionado", "calefaccion",
			"casetaVigilancia", "seguridad", "alberca", "casaClub", "canchaTenis", "vistaMar", "jacuzzi", "permiteMascotas", "gimnasio", "centrosComerciales",
			"escuelasCercanas", "fumadoresPermitidos");
			
		for ($x = 0; $x < count($_tempArrayInputs); $x++) {
			if (isset($_GET[$_tempArrayInputs[$x]])) {
				$arrayDatosPost[$_tempArrayInputs[$x]] = $_GET[$_tempArrayInputs[$x]];
			}
		}

		$_SESSION[userFiltros]['transaccion'] = $transaccion;
		$_SESSION[userFiltros]['tipoInmueble'] = $tipoInmueble;

		$arrayDatosPost['transaccion'] = $transaccion;
		$arrayDatosPost['tipoInmueble'] = $tipoInmueble;

	//}

	$colonia = (isset($_SESSION[userFiltros]["colonia"]))?$_SESSION[userFiltros]["colonia"]:-1;

?>

<?php
	CabeceraHTML("catalogo-responsive.css,catalogo-responsive.js", $variables);
	bodyIndex();
?>

<div class="container control-filtro mobile-only">
	<div class="col-xs-6 btn btn-inmueble btn-lg button-filtro">
		Filtrar
	</div>
</div>

<div class="banner inmueble hidden-xs">
	<?php templateBuscadorResponsive(); ?>
	<img src="images/images/0451438001436838815.jpg" class="img-responsive" alt="Casas en venta en guadalajara">

</div>
<br>
<div class="container catalogo_cuerpo">
	<aside class="col-md-12  col-lg-3  columna1 hidden-sm hidden-md filtro-avanzado collapse">
		<?php template_busquedaAvanzadaResponsive($arrayDatosPost); ?>
	</aside>
	<section class="col-md-12 col-lg-9 columna2">
		<div class="opcionesBusqueda valign row">
			<div class="col-md-5 col-sm-5 col-xs-12 title">
				<h1 class="cadenaResultados"></h1>
			</div>
			<div class="col-md-5 col-sm-5 col-xs-12 opciones">
				<div class="campo1">
					Resultados por página <p id="catalogo_paginacion_elem" class="opcionesPaginacion"><?php
						$arrayPaginacion = array(10, 30, 50);

						for ($x = 0; $x < count($arrayPaginacion); $x++) {
							echo "<span ".($arrayPaginacion[$x] == $elem ? "class='active'" : "").">".$arrayPaginacion[$x]."</span>";
						}
						?></p>
				</div><div class="campo2"></div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-12">
				<ul id="catalogo_orden" class="template_campos">
					<p data-value='-1'></p>
					<li class="lista">
						<ul>
							<li data-value='1'>Mayor Precio</li>
							<li data-value='2'>Menor Precio</li>
							<li data-value='3'>Relevancia</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<div class="resultadosConsulta"></div>
		<div class="paginacion_otros">
			<div class="paginacionNumeracion"></div>

		</div>
	</section>
</div>
<!-- <div class="catalogo_cuerpo">
	<div class="columna1"><?php
    	//template_busquedaAvanzada($arrayDatosPost);
    ?></div><div class="columna2">
    	<div class="opcionesBusqueda">
        	<h1 class="cadenaResultados">
            </h1><div class="opciones">
                <div class="campo1">
                    Resultados por página <p id="catalogo_paginacion_elem" class="opcionesPaginacion"><?php
						$arrayPaginacion = array(10, 30, 50);

						for ($x = 0; $x < count($arrayPaginacion); $x++) {
							echo "<span ".($arrayPaginacion[$x] == $elem ? "class='active'" : "").">".$arrayPaginacion[$x]."</span>";
						}
					?></p>
                </div><div class="campo2">

                </div>
            </div>
        </div>

    </div>
</div> -->

<script type="text/javascript">
    $('.button-filtro').click(function(){
    	$( '.filtro-avanzado' ).toggleClass( "collapse" );
    });
</script>
<?php
	getFooter();
	getCatalogjsFuntions();
	PopUpGenerales();
	FinHTML();
?>