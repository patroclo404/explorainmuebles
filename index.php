<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");


	$variables = "";

	if (isset($_GET["mapa"])) {
		$variables.= "post_mapa=1,post_transaccion=".$_GET["transaccion"];
	}

	if (isset($_GET["validar"])) {
		$variables.= ($variables != "" ? "," : "")."post_validar='".$_GET["validar"]."'";
        $id = $_GET["validar"];
        $id = before( '_', $id );
        $con = "UPDATE USUARIO SET USU_VALIDADO = 1 WHERE USU_ID = :id";
        $conexion = crearConexionPDO();
        $pdo = $conexion->prepare($con);
        $pdo->execute(array(":id" => $id) );
	}
	if (isset($_GET["validadoTrue"])) {
		if (!isset($_SESSION[userId]))
			$variables.= ($variables != "" ? "," : "")."post_validadoTrue=1";
	}

    function before ($this, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $this));
    };


	$paramsMetasPage = array(
		"keywords"		=>	"casa en venta, casa en renta, departamento en renta, departamento en renta, Guadalajara, inmuebles en venta, inmuebles en renta",
		"descripcion"	=>	"Compra, venta y renta de propiedades e inmuebles. Busca y encuentra cualquier propiedad: casas, departamentos, terrenos, oficinas, locales, bodegas, edificios."
	);


	CabeceraHTML("index_responsive.css,index_ver24.js", $variables, NULL, $paramsMetasPage);
    ?><link rel='stylesheet' type='text/css' href='css/flexslider.css' /><?php
    bodyIndex();


	$urlArchivos = "images/images/";
    $y=0;

	$arrayImagenes = array();
    $arrayTextos = array(
             array(
                'id'         => '1',
                'texto'        => 'Uno'
            ),
            array(
                'id'         => '2',
                'texto'        => 'Dos'
            ),
             array(
                'id'         => '3',
                'texto'        => 'Tres'
            ),
            array(
                'id'         => '4',
                'texto'        => 'Cuatro'
            )
        );
	$conexion = crearConexionPDO();
	$consulta =
		"SELECT
			IMP_IMAGEN,
			IMP_TEXTO,
			(
				IF (
					IMP_ORDEN > 0,
					IMP_ORDEN,
					1000000000
				)
			) AS CONS_ORDEN
		FROM IMAGEN_PORTADA
		ORDER BY CONS_ORDEN;";
	foreach($conexion->query($consulta) as $row) {
		$arrayImagenes[] = array(
			"texto"		=>	$row["IMP_TEXTO"],
			"imagen"	=>	$urlArchivos.$row["IMP_IMAGEN"]
		);
	}


	$arrayTipoInmueble = array();

	$consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayTipoInmueble[] = array(
			"id"	=>	$row["TIN_ID"],
			"nombre"=>	$row["TIN_NOMBRE"]
		);
	}

	$medidas = getimagesize($arrayImagenes[0]["imagen"]);
?>
<div class="index_cuerpo">
	<div class="galeria" data-width="<?php echo $medidas[0]; ?>" data-height="650<?php //echo $medidas[1]; ?>">
        <?php templateBuscadorResponsive() ?>
    	<div class="desplazamiento-cycle hidden-xs"><?php
			for ($x = 0; $x < count($arrayImagenes); $x++) {
				//echo "<div class='bloque-cycle'><img src='".$arrayImagenes[$x]["imagen"]."' class='indexFondo' alt='".$arrayImagenes[$x]["texto"]."' /></div>";
                echo "<img style='width:100%;position: relative;' class='desplazamiento-slide slider-home' src='".$arrayImagenes[$x]["imagen"]."' alt='".$arrayImagenes[$x]["texto"]."' />";
                   
               /*echo "<h2 class='text-banner'><span>Prueba<span>&nbsp;</span></h2>";*/
			}

			if (count($arrayImagenes) >= 3) {
				//echo "<div class='bloque'><img src='".$arrayImagenes[0]."' class='indexFondo' alt='".$arrayImagenes[0]["texto"]."' /></div>";
			}
            
            echo "<h2 class='text-banner'><span style='font-size: 77px;'>Explora Inmuebles</span><span>&nbsp;<br/>Tu propiedad en guadalajara</span></h2>";
    	?></div>
    	<div class="mobile-only">
    		<img src="images/explora-inmuebles-banner-home-mobile.jpg" class="indexFondo" alt="Explora Inmuebles en renta o venta guadalajara" />
    	</div>

    </div>

    <div class="controlesMapa">
        <a class="otrosBotones paloma" href="javascript:index_buscarEnMapa();">Buscar Propiedades</a>
        <a class="otrosBotones borrar" href="javascript:index_definirArea_mapa();">Limpiar Mapa</a>
    </div>
    <div id="indexContenedorMapa" class="contenedorMapa"></div>
    <div class="filtrosMapa">
    	<div>
            <ul id="index_filtros_transaccion" class="template_campos">
                Transacción<?php var_dump($x) ?><span></span>
                <li class="lista">
                    <ul>
                        <li data-value="1">Renta</li>
                        <li data-value="3">Renta Vacacional</li>
                        <li data-value="2">Venta</li>
                    </ul>
                </li>
                <p data-value="-1"></p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
            </ul>
        </div>
        <div>
            <ul id="index_filtros_tipoInmueble" class="template_campos">
                Tipo de Inmueble<span></span>
                <li class="lista">
                    <ul><?php
                        for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
                            echo "<li data-value='".$arrayTipoInmueble[$x]["id"]."' ".(in_array($arrayTipoInmueble[$x]["id"], array(1, 2)) ? "data-transaccion='3'" : "").">".$arrayTipoInmueble[$x]["nombre"]."</li>";
                        }
                    ?></ul>
                </li>
                <p data-value="-1"></p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
            </ul>
        </div>
        <p class="botones"><a class="otrosBotones paloma" href="javascript:index_buscarEnMapa();">Aplicar</a></p>
    </div>

</div>

<section class="container main">
    <div class="destacados hidden-xs">

        <div class="row-eq-height header">
            <h2 class="col-lg-10 col-xs-8">Propiedades Destacadas</h2>

        </div>

        <div class="row body">
            <?php

            $inmuebles = getDestacados();
			$titulo_str = "";
			$colonia_ciudad= "";
			$colonia_ciudad_str = "";

            foreach ($inmuebles as $inmueble):
			$titulo_str = template_cortarCadena($inmueble["titulo"],20);
			$colonia_ciudad = $inmueble["coloniaNombre"]. " | " . $inmueble["ciudadNombre"] ;
			$colonia_ciudad_str =  template_cortarCadena($colonia_ciudad,40);
            ?>
            <div class="item col-lg-3 col-sm-3 col-xs-6"  onclick="catalogo_redirecciona_regresar('<?php echo $inmueble['url'];?>');">
                <div class="img-wrapper">
                    <img src="<?php echo $inmueble["imagenes"][0]; ?>" alt="<?php echo $titulo_str ?>" class="img-responsive">
                </div>
                <div class="info">
                    <h2 class="property header"><?php echo $titulo_str; ?></h2>
                    <h3 class="property subheader"><?php echo $colonia_ciudad_str; ?></h3>
                    <div class="information property">
                    	<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
                    		<div><i class="flaticon-beds2"></i> CUARTOS <br>
                    			<a><?php echo template_cambiarVacio ($inmueble['recamaras']); ?></a>
	                    	</div>
	                    </div>
	                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
		                    <div><i class="flaticon-shower15"></i> BAÑOS <br>
		                    	<a><?php echo template_cambiarVacio ($inmueble['wcs']); ?></a>
		                    </div>
	                   	</div>
	                </div>
	                <div class="property price">
	                	<span class="precio">$ <?php echo number_format($inmueble["precio"], 0, ".", ","); ?> MXN</span>
	                </div>
                    
                </div>
            </div>
            <?php endforeach; ?>


        </div>
    </div>
    
    <div class="destacados mobile-only">

        <div class="row-eq-height header">
            <h2 class="col-lg-10 col-xs-8">Propiedades Destacadas</h2>
        </div>
        <div class="row body flexslider">
            <ul class="slides">
            <?php
            $inmuebles = getDestacados();
			$titulo_str = "";
			$colonia_ciudad= "";
			$colonia_ciudad_str = "";

            foreach ($inmuebles as $inmueble):
            $titulo_str = template_cortarCadena($inmueble["titulo"],20);
			$colonia_ciudad = $inmueble["coloniaNombre"]. " | " . $inmueble["ciudadNombre"] ;
			$colonia_ciudad_str =  template_cortarCadena($colonia_ciudad,40);
            ?>
            <li>
            <div class="item col-lg-3 col-sm-3 col-xs-6"  onclick="catalogo_redirecciona_regresar('<?php echo $inmueble['url'];?>');">
                <div class="img-wrapper">
                    <img src="<?php echo $inmueble["imagenes"][0]; ?>" alt="<?php echo $titulo_str ?>" class="img-responsive">
                </div>
                <div class="info">
                    <h2 class="property header"><?php echo $titulo_str; ?></h2>
                    <h3 class="property subheader"><?php echo $colonia_ciudad_str; ?></h3>
                    <div class="information property">
                    	<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
                    		<div><i class="flaticon-beds2"></i> CUARTOS <br>
                    			<a><?php echo template_cambiarVacio ($inmueble['recamaras']); ?></a>
	                    	</div>
	                    </div>
	                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
		                    <div><i class="flaticon-shower15"></i> BAÑOS <br>
		                    	<a><?php echo template_cambiarVacio ($inmueble['wcs']); ?></a>
		                    </div>
	                   	</div>
	                </div>
	                <div class="property price">
	                	<span class="precio">$ <?php echo number_format($inmueble["precio"], 0, ".", ","); ?> MXN</span>
	                </div>
                    
                </div>
            </div>
            </li>
            <?php endforeach; ?>

            </ul>
        </div>
    </div>

    <div class="destacados hidden-xs">
        <div class="row-eq-height header">
            <h2 class="col-lg-10 col-xs-8">&Uacute;ltimas Propiedades</h2>
            <div class="col-lg-2 col-xs-4"> <div class="more" onclick="catalogo_redirecciona_regresar('/venta/todos-los-tipos/todo-mexico/todas-las-ciudades');"> ver m&aacute;s ></div> </div>
        </div>
        <div class="row body">
            <?php

            $inmuebles = getFirstFour();
			$titulo_str = "";
			$colonia_ciudad= "";
			$colonia_ciudad_str = "";
			
            foreach ($inmuebles as $inmueble):
            $titulo_str = template_cortarCadena($inmueble["titulo"],20);
			$colonia_ciudad = $inmueble["coloniaNombre"]. " | " . $inmueble["ciudadNombre"] ;
			$colonia_ciudad_str =  template_cortarCadena($colonia_ciudad,40);
            ?>
            <div class="item col-lg-3 col-sm-3 col-xs-6"  onclick="catalogo_redirecciona_regresar('<?php echo $inmueble['url'];?>');">
                <div class="img-wrapper">
                    <img src="<?php echo $inmueble["imagenes"][0]; ?>" alt="<?php echo $titulo_str ?>" class="img-responsive">
                </div>
                <div class="info">
                    <h2 class="property header"><?php echo $titulo_str; ?></h2>
                    <h3 class="property subheader"><?php echo $colonia_ciudad_str; ?></h3>
                    <div class="information property">
                    	<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
                    		<div><i class="flaticon-beds2"></i> CUARTOS <br>
                    			<a><?php echo template_cambiarVacio ($inmueble['recamaras']); ?></a>
	                    	</div>
	                    </div>
	                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
		                    <div><i class="flaticon-shower15"></i> BAÑOS <br>
		                    	<a><?php echo template_cambiarVacio ($inmueble['wcs']); ?></a>
		                    </div>
	                   	</div>
	                </div>
	                <div class="property price">
	                	<span class="precio">$ <?php echo number_format($inmueble["precio"], 0, ".", ","); ?> MXN</span>
	                </div>
                    
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="destacados mobile-only">
        <div class="row-eq-height header">
            <h2 class="col-lg-10 col-xs-8">&Uacute;ltimas Propiedades</h2>
            <div class="col-lg-2 col-xs-4 hidden-xs"> <div class="more" onclick="catalogo_redirecciona_regresar('/venta/todos-los-tipos/todo-mexico/todas-las-ciudades');"> ver m&aacute;s ></div> </div>
        </div>
        <div class="row body flexslider">
            <ul class="slides">
            <?php

            $inmuebles = getFirstFour();
            $titulo_str = "";
			$colonia_ciudad= "";
			$colonia_ciudad_str = "";
			
            foreach ($inmuebles as $inmueble):
            $titulo_str = template_cortarCadena($inmueble["titulo"],20);
			$colonia_ciudad = $inmueble["coloniaNombre"]. " | " . $inmueble["ciudadNombre"] ;
			$colonia_ciudad_str =  template_cortarCadena($colonia_ciudad,40);
            ?>
            <li>
            <div class="item col-lg-3 col-sm-3 col-xs-6 slide"  onclick="catalogo_redirecciona_regresar('<?php echo $inmueble['url'];?>');">
                <div class="img-wrapper">
                    <img src="<?php echo $inmueble["imagenes"][0]; ?>" alt="<?php echo $titulo_str ?>" class="img-responsive">
                </div>
                <div class="info">
                    <h2 class="property header"><?php echo $titulo_str; ?></h2>
                    <h3 class="property subheader"><?php echo $colonia_ciudad_str; ?></h3>
                    <div class="information property">
                    	<div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
                    		<div><i class="flaticon-beds2"></i> CUARTOS <br>
                    			<a><?php echo template_cambiarVacio ($inmueble['recamaras']); ?></a>
	                    	</div>
	                    </div>
	                    <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6 property main-info">
		                    <div><i class="flaticon-shower15"></i> BAÑOS <br>
		                    	<a><?php echo template_cambiarVacio ($inmueble['wcs']); ?></a>
		                    </div>
	                   	</div>
	                </div>
	                <div class="property price">
	                	<span class="precio">$ <?php echo number_format($inmueble["precio"], 0, ".", ","); ?> MXN</span>
	                </div>
                    
                </div>
            </div>
            </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
</section>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyBlYGs9bCTLNLYemvkULvJUaQR_vA7S9k4"></script>

<script type="text/javascript">
    $('.cycle').cycle({fx:'scrollHorz',pager:'.slider-control-nav',pause:1,speed:800,slides:'.slide',swipe:true,timeout:6000});
    
    $('.desplazamiento-cycle').cycle({fx:'scrollHorz',pager:'.slider-control-nav',pause:1,speed:800,slides:'.desplazamiento-slide',swipe:true,timeout:6900});
    
</script>
<?php
	getFooter();
	PopUpGenerales("index_cerrarPopUp");
?>
<div id="index_mensajesAlerts" class="templatePopUp index_mensajesAlerts">
    <span class="btnCerrar" onclick="template_principalCerrarPopUp(index_cerrarPopUp);">x</span>
    <table>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<script src="js/jquery.flexslider-min.js"></script>
<script>
$(window).load(function() {
  $('.flexslider').flexslider({
    animation: "slide",
    controlNav:false,
    directionNav:false
  });
});
</script>
<?php
	FinHTML();
?>
