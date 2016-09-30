<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
		
	$consulta = "SELECT USU_TELEFONO1, USU_TELEFONO2, USU_CALLE_NUMERO, USU_ESTADO, USU_CIUDAD, USU_COLONIA FROM USUARIO WHERE USU_ID = ".$_SESSION[userId].";";
	$res = crearConsulta($consulta);
	$row = mysql_fetch_array($res);
	$usuario = array(
			"id"			=>	$_SESSION[userId],
			"telefono1"		=>	$row["USU_TELEFONO1"] != NULL ? $row["USU_TELEFONO1"] : "",
			"telefono2"		=>	$row["USU_TELEFONO2"] != NULL ? $row["USU_TELEFONO2"] : "",
			"calleNumero"	=>	$row["USU_CALLE_NUMERO"] != NULL ? $row["USU_CALLE_NUMERO"] : "",
			"estado"		=>	$row["USU_ESTADO"] != NULL ? $row["USU_ESTADO"] : "",
			"ciudad"		=>	$row["USU_CIUDAD"] != NULL ? $row["USU_CIUDAD"] : "",
			"colonia"		=>	$row["USU_COLONIA"] != NULL ? $row["USU_COLONIA"] : ""
	);
	
	
	$urlArchivos = "images/images/";
	$variables = "";
	
	
	$arrayEstado = array();
	
	$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
	$res = crearConsulta($consulta);
	while($row = mysql_fetch_array($res)) {
		$arrayEstado[] = array(
			"id"	=>	$row["EST_ID"],
			"nombre"=>	$row["EST_NOMBRE"]
		);
	}
	
	
	$arrayCategoria = array();
	
	$consulta = "SELECT CIN_ID, CIN_NOMBRE FROM CATEGORIA_INMUEBLE ORDER BY CIN_NOMBRE;";
	$res = crearConsulta($consulta);
	while($row = mysql_fetch_array($res)) {
		$arrayCategoria[] = array(
			"id"		=>	$row["CIN_ID"],
			"nombre"	=>	$row["CIN_NOMBRE"]
		);
	}
	
	
	$arrayTipoCategoria = array();
	
	$consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
	$res = crearConsulta($consulta);
	while($row = mysql_fetch_array($res)) {
		$_arrayCategorias = array();
		$consulta2 = "SELECT TCA_CATEGORIA_INMUEBLE FROM TIPO_INMUEBLE_CATEGORIA_INMUEBLE WHERE TCA_TIPO_INMUEBLE = ".$row["TIN_ID"]." ORDER BY TCA_ID;";
		$res2 = crearConsulta($consulta2);
		while($row2 = mysql_fetch_row($res2)) {
			$_arrayCategorias[] = $row2[0];
		}
		
		$arrayTipoCategoria[] = array(
			"id"		=>	$row["TIN_ID"],
			"nombre"	=>	$row["TIN_NOMBRE"],
			"categorias"=>	$_arrayCategorias
		);
	}
	
	
	$arrayTransaccion = array();
	
	$consulta = "SELECT TRA_ID, TRA_NOMBRE FROM TRANSACCION ORDER BY TRA_NOMBRE;";
	$res = crearConsulta($consulta);
	while($row = mysql_fetch_array($res)) {
		$arrayTransaccion[] = array(
			"id"		=>	$row["TRA_ID"],
			"nombre"	=>	$row["TRA_NOMBRE"]
		);
	}
	
	
	$edit = 0;
	$inmueble = array();
	
	if (isset($_POST["edit"])) {
		$edit = 1;
		
		$inmueble = array();
		$consulta =
			"SELECT
				IMU_ID,
				IMU_TITULO,
				IMU_USUARIO,
				IMU_CATEGORIA_INMUEBLE,
				IMU_TIPO_INMUEBLE,
				IMU_PRECIO,
				IMU_CALLE_NUMERO,
				IMU_ESTADO,
				IMU_CIUDAD,
				IMU_COLONIA,
				IMU_CP,
				IMU_LATITUD,
				IMU_LONGITUD,
				IMU_DESCRIPCION,
				IMU_ANTIGUEDAD,
				IMU_CODIGO,
				IMU_DIMENSION_TOTAL,
				IMU_DIMENSION_CONSTRUIDA,
				IMU_ESTADO_CONSERVACION,
				IMU_AMUEBLADO,
				IMU_COCINA_EQUIPADA,
				IMU_ESTUDIO,
				IMU_CUARTO_SERVICIO,
				IMU_CUARTO_TV,
				IMU_BODEGA,
				IMU_TERRAZA,
				IMU_JARDIN,
				IMU_AREA_JUEGOS_INFANTILES,
				IMU_COMEDOR,
				IMU_SERVICIOS_BASICOS,
				IMU_GAS,
				IMU_LINEA_TELEFONICA,
				IMU_INTERNET_DISPONIBLE,
				IMU_AIRE_ACONDICIONADO,
				IMU_CALEFACCION,
				IMU_CUOTA_MANTENIMIENTO,
				IMU_CASETA_VIGILANCIA,
				IMU_ELEVADOR,
				IMU_SEGURIDAD,
				IMU_ALBERCA,
				IMU_CASA_CLUB,
				IMU_CANCHA_TENIS,
				IMU_VISTA_MAR,
				IMU_JACUZZI,
				IMU_ESTACIONAMIENTO_VISITAS,
				IMU_PERMITE_MASCOTAS,
				IMU_GIMNASIO,
				IMU_CENTROS_COMERCIALES_CERCANOS,
				IMU_ESCUELAS_CERCANAS,
				IMU_FUMADORES_PERMITIDOS,
				IMU_NUMERO_OFICINAS,
				IMU_WCS,
				IMU_RECAMARAS,
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
				TIN_NOMBRE,
				(
					SELECT FIN_ID
					FROM FAVORITO_INMUEBLE
					WHERE FIN_USUARIO = ".$usuario["id"]."
					AND FIN_INMUEBLE = IMU_ID
				) AS CONS_LIKE
			FROM INMUEBLE, TIPO_INMUEBLE
			WHERE IMU_ID = ".$_POST["id"]."
			AND IMU_TIPO_INMUEBLE = TIN_ID;";
		$res = crearConsulta($consulta);
		$row = mysql_fetch_array($res);
		$inmueble = array(
			"id"						=>	$row["IMU_ID"],
			"titulo"					=>	$row["IMU_TITULO"],
			"usuario"					=>	$row["IMU_USUARIO"],
			"categoria"					=>	$row["IMU_CATEGORIA_INMUEBLE"],
			"tipo"						=>	$row["IMU_TIPO_INMUEBLE"],
			"precio"					=>	$row["IMU_PRECIO"],
			"calleNumero"				=>	$row["IMU_CALLE_NUMERO"],
			"estado"					=>	$row["IMU_ESTADO"],
			"ciudad"					=>	$row["IMU_CIUDAD"],
			"colonia"					=>	$row["IMU_COLONIA"],
			"cp"						=>	$row["IMU_CP"],
			"latitud"					=>	$row["IMU_LATITUD"],
			"longitud"					=>	$row["IMU_LONGITUD"],
			"descripcion"				=>	$row["IMU_DESCRIPCION"] != NULL ? $row["IMU_DESCRIPCION"] : "",
			"antiguedad"				=>	$row["IMU_ANTIGUEDAD"] != NULL ? $row["IMU_ANTIGUEDAD"] : "",
			"codigo"					=>	$row["IMU_CODIGO"] != NULL ? $row["IMU_CODIGO"] : "",
			"dimensionTotal"			=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
			"dimensionConstruida"		=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
			"estadoConservacion"		=>	$row["IMU_ESTADO_CONSERVACION"],
			"amueblado"					=>	$row["IMU_AMUEBLADO"],
			"cocinaEquipada"			=>	$row["IMU_COCINA_EQUIPADA"],
			"estudio"					=>	$row["IMU_ESTUDIO"],
			"cuartoServicio"			=>	$row["IMU_CUARTO_SERVICIO"],
			"cuartoTV"					=>	$row["IMU_CUARTO_TV"],
			"bodega"					=>	$row["IMU_BODEGA"],
			"terraza"					=>	$row["IMU_TERRAZA"],
			"jardin"					=>	$row["IMU_JARDIN"],
			"areaJuegosInfantiles"		=>	$row["IMU_AREA_JUEGOS_INFANTILES"],
			"comedor"					=>	$row["IMU_COMEDOR"],
			"serviciosBasicos"			=>	$row["IMU_SERVICIOS_BASICOS"],
			"gas"						=>	$row["IMU_GAS"],
			"lineaTelefonica"			=>	$row["IMU_LINEA_TELEFONICA"],
			"internetDisponible"		=>	$row["IMU_INTERNET_DISPONIBLE"],
			"aireAcondicionado"			=>	$row["IMU_AIRE_ACONDICIONADO"],
			"calefaccion"				=>	$row["IMU_CALEFACCION"],
			"cuotaMantenimiento"		=>	$row["IMU_CUOTA_MANTENIMIENTO"] != NULL ? $row["IMU_CUOTA_MANTENIMIENTO"] : "",
			"casetaVigilancia"			=>	$row["IMU_CASETA_VIGILANCIA"],
			"elevador"					=>	$row["IMU_ELEVADOR"] != NULL ? $row["IMU_ELEVADOR"] : "",
			"seguridad"					=>	$row["IMU_SEGURIDAD"],
			"alberca"					=>	$row["IMU_ALBERCA"],
			"casaClub"					=>	$row["IMU_CASA_CLUB"],
			"canchaTenis"				=>	$row["IMU_CANCHA_TENIS"],
			"vistaMar"					=>	$row["IMU_VISTA_MAR"],
			"jacuzzi"					=>	$row["IMU_JACUZZI"],
			"estacionamientoVisitas"	=>	$row["IMU_ESTACIONAMIENTO_VISITAS"] != NULL ? $row["IMU_ESTACIONAMIENTO_VISITAS"] : "",
			"permiteMascotas"			=>	$row["IMU_PERMITE_MASCOTAS"],
			"gimnasio"					=>	$row["IMU_GIMNASIO"],
			"centrosComercialesCercanos"=>	$row["IMU_CENTROS_COMERCIALES_CERCANOS"],
			"escuelasCercanas"			=>	$row["IMU_ESCUELAS_CERCANAS"],
			"fumadoresPermitidos"		=>	$row["IMU_FUMADORES_PERMITIDOS"],
			"numeroOficinas"			=>	$row["IMU_NUMERO_OFICINAS"] != NULL ? $row["IMU_NUMERO_OFICINAS"] : "",
			"wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : -1,
			"recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : -1,
			"estadoNombre"				=>	$row["CONS_ESTADO"],
			"ciudadNombre"				=>	$row["CONS_CIUDAD"],
			"coloniaNombre"				=>	$row["CONS_COLONIA"],
			"cpNombre"					=>	$row["CONS_CP"],
			"tipoNombre"				=>	$row["TIN_NOMBRE"],
			"like"						=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"],
			"imagenes"					=>	array(),
			"transacciones"				=>	array()
		);
		
		
		$consulta = "SELECT IIN_ID, IIN_IMAGEN, IIN_ORDEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ".$_POST["id"]." ORDER BY IIN_ID;";
		$res = crearConsulta($consulta);
		while($row = mysql_fetch_array($res)) {
			$inmueble["imagenes"][] = array(
				"id"		=>	$row["IIN_ID"],
				"imagen"	=>	$urlArchivos.$row["IIN_IMAGEN"],
				"principal"	=>	$row["IIN_ORDEN"]
			);
		}
		
		$consulta = "SELECT TRI_TRANSACCION FROM TRANSACCION_INMUEBLE WHERE TRI_INMUEBLE = ".$_POST["id"]." ORDER BY TRI_ID;";
		$res = crearConsulta($consulta);
		while($row = mysql_fetch_row($res)) {
			$inmueble["transacciones"][] = $row[0];
		}
		
		$variables = "post_categoria=".$inmueble["categoria"].",post_tipo=".$inmueble["tipo"].",post_transaccion=".$inmueble["transacciones"][0].",post_estado=".$inmueble["estado"].",post_ciudad=".$inmueble["ciudad"].",post_colonia=".$inmueble["colonia"].",post_latitud='".$inmueble["latitud"]."',post_longitud='".$inmueble["longitud"]."',post_antiguedad=".$inmueble["antiguedad"].",post_estadoConservacion=".$inmueble["estadoConservacion"].",post_amueblado=".$inmueble["amueblado"].",post_wcs=".$inmueble["wcs"].",post_recamaras=".$inmueble["recamaras"];
	}
	
	
	CabeceraHTML("nuevoAnuncio_ver5.css,nuevoAnuncio_ver10.js", $variables);
	CuerpoHTML();
?>
<div class="nuevoAnuncio_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
        <div id="lk_crearAnuncio">
        	<p class="titulo"><?php echo $edit == 0 ? "Crear" : "Modificar"; ?> Anuncio</p>
            <form id="subirAnuncio" method="post" enctype="multipart/form-data" action="lib_php/updInmueble.php">
            	<input type="text" name="<?php echo $edit == 0 ? "nuevo" : "modificar"; ?>" value="1" style="display:none;" />
                <input type="text" id="idInmueble" name="id" value="<?php echo $edit == 0 ? "-1" : $inmueble["id"]; ?>" style="display:none;" />
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <p class="subtitulo">Características</p>
                                <div class="contenedorCampos">
                                    <p>Título del Anuncio*</p>
                                    <input type="text" id="crearAnuncio_titulo" name="titulo" class="template_campos" placeholder="Título del Anuncio" maxlength="256" value="<?php echo $edit == 1 ? $inmueble["titulo"] : ""; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_categoria" class="template_campos">
                                        Categoría*<span></span>
                                        <li class="lista">
                                            <ul><?php
                                                for ($x = 0; $x < count($arrayCategoria); $x++) {
                                                    echo "<li data-value='".$arrayCategoria[$x]["id"]."'>".$arrayCategoria[$x]["nombre"]."</li>";
                                                }
                                            ?></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioCategoria" name="categoria" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_transaccion" class="template_campos">
                                        Tipo de Transacción*<span></span>
                                        <li class="lista">
                                            <ul><?php
                                                for ($x = 0; $x < count($arrayTransaccion); $x++) {
                                                    echo "<li data-value='".$arrayTransaccion[$x]["id"]."'>".$arrayTransaccion[$x]["nombre"]."</li>";
                                                }
                                            ?></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioTransaccion" name="transaccion" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <p>Calle y Número*</p>
                                    <input type="text" id="crearAnuncio_calleNumero" name="calleNumero" class="template_campos" placeholder="Calle y Número" maxlength="64" value="<?php echo $edit == 1 ? $inmueble["calleNumero"] : ""; ?>" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_ciudad" class="template_campos">
                                        Ciudad / Municipio / Delegación*<span></span>
                                        <li class="lista">
                                            <ul></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioCiudad" name="ciudad" value="" style="display:none;" />
                                </div>
                            </td>
                            <td>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_tipo" class="template_campos">
                                        Tipo de Inmueble*<span></span>
                                        <li class="lista">
                                            <ul><?php
                                                for ($x = 0; $x < count($arrayTipoCategoria); $x++) {
                                                    echo "<li data-value='".$arrayTipoCategoria[$x]["id"]."' data-categorias='".implode(",", $arrayTipoCategoria[$x]["categorias"])."'>".$arrayTipoCategoria[$x]["nombre"]."</li>";
                                                }
                                            ?></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioTipo" name="tipo" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <p>Precio*</p>
                                    <input type="text" id="crearAnuncio_precio" name="precio" class="template_campos" placeholder="Precio" maxlength="18" value="<?php echo $edit == 1 ? $inmueble["precio"] : ""; ?>" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_estado" class="template_campos">
                                        Estado*<span></span>
                                        <li class="lista">
                                            <ul><?php
                                                for ($x = 0; $x < count($arrayEstado); $x++) {
                                                    echo "<li data-value='".$arrayEstado[$x]["id"]."'>".$arrayEstado[$x]["nombre"]."</li>";
                                                }
                                            ?></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioEstado" name="estado" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_colonia" class="template_campos">
                                        Colonia*<span></span>
                                        <li class="lista">
                                            <ul></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioColonia" name="colonia" value="" style="display:none;" />
                                    <input type="text" id="_crearAnuncioCP" name="cp" value="" style="display:none;" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="contenedorCampos">
                                    <p>Ubicación en el mapa*<a class="encontrarUbicacion" href="javascript:nuevoAnuncio_encontrarUbicacion();">Haz Click aquí para encontrar tu ubicación</a></p>
                                    <div id="contenedorMapa" class="contenedorMapa"></div>
                                    <input type="text" id="_crearAnuncioLatitud" name="latitud" value="" style="display:none;" />
                                    <input type="text" id="_crearAnuncioLongitud" name="longitud" value="" style="display:none;" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="contenedorCampos">
                                    <p>Descripción*</p>
                                    <textarea id="crearAnuncio_descripcion" name="descripcion" class="template_campos" placeholder="Descripción"><?php echo $edit == 1 ? $inmueble["descripcion"] : ""; ?></textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            	<div class="contenedorCampos">
                                    <ul id="crearAnuncio_wcs" class="template_campos">
                                    	Baños<span></span>
                                        <li class="lista">
                                        	<ul>
												<?php
													for ($x = 1; $x <= 10; $x++) {
														echo "<li data-value='".$x."'>".$x."</li>";
													}
												?>
                                                <li data-value="11">Más de 10</li>
                                            </ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioWcs" name="wcs" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_antiguedad" class="template_campos">
                                        Antig&uuml;edad<span></span>
                                        <li class="lista">
                                            <ul>
                                                <li data-value="1">0 Años</li>
                                                <li data-value="2">1 Año</li>
                                                <li data-value="3">2 Años</li>
                                                <li data-value="4">3 Años</li>
                                                <li data-value="5">4 Años</li>
                                                <li data-value="6">5 - 9 Años</li>
                                                <li data-value="7">10 - 19 Años</li>
                                                <li data-value="8">20 - 29 Años</li>
                                                <li data-value="9">30 - 39 Años</li>
                                                <li data-value="10">40 - 49 Años</li>
                                                <li data-value="11">50 Años ó mas</li>
                                            </ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioAntiguedad" name="antiguedad" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <p>Dimensión de Terreno</p>
                                    <input type="text" id="crearAnuncio_dimesionTotal" name="dimensionTotal" class="template_campos" placeholder="Dimensión de Terreno" maxlength="12" value="<?php echo $edit == 1 ? $inmueble["dimensionTotal"] : ""; ?>" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_amueblado" class="template_campos">
                                        Está Amueblado*<span></span>
                                        <li class="lista">
                                            <ul>
                                                <li data-value="1">Amueblado</li>
                                                <li data-value="2">Semi-Amueblado</li>
                                                <li data-value="3">No</li>
                                            </ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioEstaAmueblado" name="amueblado" value="" style="display:none;" />
                                </div>
                            </td>
                            <td>
                            	<div class="contenedorCampos">
                                    <ul id="crearAnuncio_recamaras" class="template_campos">
                                    	Recamaras<span></span>
                                        <li class="lista">
                                        	<ul>
												<?php
													for ($x = 1; $x <= 10; $x++) {
														echo "<li data-value='".$x."'>".$x."</li>";
													}
												?>
                                                <li data-value="11">Más de 10</li>
                                            </ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioRecamaras" name="recamaras" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="crearAnuncio_estadoConservacion" class="template_campos">
                                        Estado de Conservación*<span></span>
                                        <li class="lista">
                                            <ul>
                                                <li data-value="1">Excelente</li>
                                                <li data-value="2">Bueno</li>
                                                <li data-value="3">Regular</li>
                                                <li data-value="4">Malo</li>
                                                <li data-value="5">Muy Malo</li>
                                            </ul>
                                        </li>
                                        <p data-value="-1"></p>
                                    </ul>
                                    <input type="text" id="_crearAnuncioEstadoConservacion" name="estadoConservacion" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <p>Dimensión de Construcción</p>
                                    <input type="text" id="crearAnuncio_dimensionConstruida" name="dimensionConstruida" class="template_campos" placeholder="Dimensión de Construcción" maxlength="12" value="<?php echo $edit == 1 ? $inmueble["dimensionConstruida"] : ""; ?>" />
                                </div>
                                <div class="contenedorCampos" <?php echo ($_SESSION[userInmobiliaria] != 0) ? "" : "style='display:none;'"; ?>>
                                    <p>Código*</p>
                                    <input type="text" id="crearAnuncio_codigo" name="codigo" data-inmueble="<?php echo $_SESSION[userInmobiliaria]; ?>" class="template_campos" placeholder="Código" maxlength="64" value="<?php echo $edit == 1 ? $inmueble["codigo"] : ""; ?>" />
                                </div>
                            </td>
                        </tr>
                        <?php
							if ($edit == 1) {
								echo
									"<tr>
										<td colspan='2'>
											<div class='contenedorCampos'>
			                                    <p>Galeria de Imagenes</p>
												<div id='galeriaImagenes' class='imagenesTemporales'>";
									
								$tempPrincipal = "";
								
								for ($x = 0; $x < count($inmueble["imagenes"]); $x++) {
									echo
													"<div class='bloqueImagen' data-imagen='".$inmueble["imagenes"][$x]["id"]."'>
														<img src='".$inmueble["imagenes"][$x]["imagen"]."' />
														<span class='borrar'>X</span>
														<p><input type='radio' name='radioImagenPrincipal' ".($inmueble["imagenes"][$x]["principal"] == 1 ? "checked='checked'" : "")." data-id=".$inmueble["imagenes"][$x]["id"]." /></p>
													</div>";
													
									if ($inmueble["imagenes"][$x]["principal"] == 1)
										$tempPrincipal = $inmueble["imagenes"][$x]["id"];
								}
									
												
								echo
												"</div>
												<p>Selecciona tu imagen principal</p>
												<input type='text' id='idImagenPrincipal' name='idImagenPrincipal' value='".$tempPrincipal."' style='display:none;' />
											</div>
										</td>
									</tr>";
							}
						?>
                        <tr>
                        	<td colspan="2">
                            	<div class="contenedorCampos">
                                    <p>Imágen</p>
                                    <div id="imagenesTemporales" class="imagenesTemporales"></div>
                                    <p>Selecciona tu imagen principal</p>
                                    <div id="iframeSubirImagen">
                                    	<iframe src="lib_php/tempSubirImagen.php" frameborder="0" width="400" height="50"></iframe>
                                    </div>
                                    <input type="text" name="imagen" id="imagen" value="" style="display:none;" />
                                    <input type="text" name="imagenPrincipal" id="imagenPrincipal" value="" style="display:none;" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="subtitulo">Ambientes</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="cocinaEquipada" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["cocinaEquipada"] == 1 ? "checked='checked'" : "") : ""; ?> />Cocina Equipada
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="estudio" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["estudio"] == 1 ? "checked='checked'" : "") : ""; ?> />Estudio
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="bodega" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["bodega"] == 1 ? "checked='checked'" : "") : ""; ?> />Bodega
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="terraza" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["terraza"] == 1 ? "checked='checked'" : "") : ""; ?> />Terraza
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="comedor" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["comedor"] == 1 ? "checked='checked'" : "") : ""; ?> />Comedor
                                </div>
                            </td>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="cuartoServicio" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["cuartoServicio"] == 1 ? "checked='checked'" : "") : ""; ?> />Cuarto de Servicio
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="cuartoTV" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["cuartoTV"] == 1 ? "checked='checked'" : "") : ""; ?> />Cuarto de TV
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="jardin" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["jardin"] == 1 ? "checked='checked'" : "") : ""; ?> />Jardín
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="areaJuegosInfatiles" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["areaJuegosInfantiles"] == 1 ? "checked='checked'" : "") : ""; ?> />Área de Juegos Infantiles
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="subtitulo">Servicios</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="serviciosBasicos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["serviciosBasicos"] == 1 ? "checked='checked'" : "") : ""; ?> />Servicios Básicos
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="gas" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["gas"] == 1 ? "checked='checked'" : "") : ""; ?> />Gas
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="aireAcondicionado" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["aireAcondicionado"] == 1 ? "checked='checked'" : "") : ""; ?> />Aire Acondicionado
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="calefaccion" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["calefaccion"] == 1 ? "checked='checked'" : "") : ""; ?> />Calefacción
                                </div>
                                <div class="contenedorCampos">
                                    <p>Cuota Mantenimiento</p>
                                    <input type="text" id="crearAnuncio_cuotaMantenimiento" name="cuotaMantenimiento" class="template_campos" placeholder="Cuota Mantenimiento" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["cuotaMantenimiento"] : ""; ?>" />
                                </div>
                            </td>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="lineaTelefonica" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["lineaTelefonica"] == 1 ? "checked='checked'" : "") : ""; ?> />Línea Telefónica
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="internetDisponible" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["internetDisponible"] == 1 ? "checked='checked'" : "") : ""; ?> />Internet Disponible
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="casetaVigilancia" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["casetaVigilancia"] == 1 ? "checked='checked'" : "") : ""; ?> />Caseta de Vigilancia
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="seguridad" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["seguridad"] == 1 ? "checked='checked'" : "") : ""; ?> />Seguridad
                                </div>
                                <div class="contenedorCampos">
                                    <p>Elevador</p>
                                    <input type="text" id="crearAnuncio_elevador" name="elevador" class="template_campos" placeholder="Elevador" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["elevador"] : ""; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="subtitulo">Amenidades</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="alberca" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["alberca"] == 1 ? "checked='checked'" : "") : ""; ?> />Alberca
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="casaClub" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["casaClub"] == 1 ? "checked='checked'" : "") : ""; ?> />Casa Club
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="jacuzzi" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["jacuzzi"] == 1 ? "checked='checked'" : "") : ""; ?> />Jacuzzi
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="permiteMascotas" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["permiteMascotas"] == 1 ? "checked='checked'" : "") : ""; ?> />Se Permite Mascotas
                                </div>
                                <div class="contenedorCampos">
                                    <p>Estacionamiento para Visitas</p>
                                    <input type="text" id="crearAnuncio_estacionamientoVisitas" name="estacionamientoVisitas" class="template_campos" placeholder="Estacionamiento para Visitas" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["estacionamientoVisitas"] : ""; ?>" />
                                </div>
                            </td>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="canchaTenis" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["canchaTenis"] == 1 ? "checked='checked'" : "") : ""; ?> />Cancha de Tenis
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="vistaMar" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["vistaMar"] == 1 ? "checked='checked'" : "") : ""; ?> />Vista al Mar
                                </div>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="gimnasio" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["gimnasio"] == 1 ? "checked='checked'" : "") : ""; ?> />Gimnasio
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <p class="subtitulo">Otras Características</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="centrosComercialesCercanos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["centrosComercialesCercanos"] == 1 ? "checked='checked'" : "") : ""; ?> />Centros Comerciales Cercanos
                                </div><div class="contenedorCampos columnas">
                                    <input type="checkbox" name="escuelasCercanas" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["escuelasCercanas"] == 1 ? "checked='checked'" : "") : ""; ?> />Escuelas Cercanas
                                </div>
                                <div class="contenedorCampos">
                                    <p>Número de Oficinas</p>
                                    <input type="text" id="crearAnuncio_numeroOficinas" name="numeroOficinas" class="template_campos" placeholder="Número de Oficinas" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["numeroOficinas"] : ""; ?>" />
                                </div>
                            </td>
                            <td>
                                <div class="contenedorCampos columnas">
                                    <input type="checkbox" name="fumadoresPermitos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["fumadoresPermitidos"] == 1 ? "checked='checked'" : "") : ""; ?> />Fumadores Permitidos
                                </div>
                                <div class="contenedorCampos">
                                    <p id="btnGuardar2" class="subtitulo guardar" onclick="nuevoAnuncio_validarCampos_inmueble();" style="padding-top:40px;"><a class="btnBotones guardar">Buscar</a>Guardar Cambios</p>
                                    <p id="mensajeTemporal2" style="display:none;">Espere un momento...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<?php
	FinHTML();
?>