<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	$idInmueble = $_GET["id"];
	$urlArchivos = "../images/images/";
	$variables = "";
	
	
	$arrayEstado = array();
	
	$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayEstado[] = array(
			"id"	=>	$row["EST_ID"],
			"nombre"=>	$row["EST_NOMBRE"]
		);
	}
	
	
	$arrayCategoria = array();
	
	$consulta = "SELECT CIN_ID, CIN_NOMBRE FROM CATEGORIA_INMUEBLE ORDER BY CIN_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayCategoria[] = array(
			"id"		=>	$row["CIN_ID"],
			"nombre"	=>	$row["CIN_NOMBRE"]
		);
	}
	
	
	$arrayTipoCategoria = array();
	
	$consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$_arrayCategorias = array();
		$consulta2 = "SELECT TCA_CATEGORIA_INMUEBLE FROM TIPO_INMUEBLE_CATEGORIA_INMUEBLE WHERE TCA_TIPO_INMUEBLE = ? ORDER BY TCA_ID;";
		$pdo2 = $conexion->prepare($consulta2);
		$pdo2->execute(array($row["TIN_ID"]));
		foreach($pdo2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$_arrayCategorias[] = $row2["TCA_CATEGORIA_INMUEBLE"];
		}
		
		$arrayTipoCategoria[] = array(
			"id"		=>	$row["TIN_ID"],
			"nombre"	=>	$row["TIN_NOMBRE"],
			"categorias"=>	$_arrayCategorias
		);
	}
	
	
	$arrayTransaccion = array();
	
	$consulta = "SELECT TRA_ID, TRA_NOMBRE FROM TRANSACCION ORDER BY TRA_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayTransaccion[] = array(
			"id"		=>	$row["TRA_ID"],
			"nombre"	=>	$row["TRA_NOMBRE"]
		);
	}
	
	
	$arrayUsuario = array();
	
	$consulta = "SELECT USU_ID, USU_NOMBRE, USU_INMOBILIARIA FROM USUARIO ORDER BY USU_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayUsuario[] = array(
			"id"			=>	$row["USU_ID"],
			"nombre"		=>	$row["USU_NOMBRE"],
			"inmobiliaria"	=>	$row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : -1
		);
	}
	
	
	$edit = 0;
	$inmueble = array();
	
	if ($idInmueble != -1) {
		$edit = 1;
		
		$inmueble = array();
		$consulta =
			"SELECT
				IMU_ID,
				IMU_TITULO,
				IMU_DESTACADO,
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
				IMU_IGLESIAS_CERCANAS,
				IMU_HOSPITALES_CERCANOS,				
				IMU_ESCUELAS_CERCANAS,
				IMU_FUMADORES_PERMITIDOS,
				IMU_AMUEBLADO2,
				IMU_SEMIAMUEBLADO,
				IMU_ZONA_INDUSTRIAL,
				IMU_ZONA_TURISTICA,
				IMU_ZONA_COMERCIAL,
				IMU_ZONA_RESIDENCIAL,
				IMU_BARES_CERCANOS,
				IMU_SUPERMERCADOS_CERCANOS,
				IMU_EXCELENTE_UBICACION,
				IMU_CISTERNA,
				IMU_CALENTADOR,
				IMU_CAMARAS,
				IMU_ANDEN,
				IMU_ASADOR,
				IMU_VAPOR,
				IMU_SAUNA,
				IMU_PLAYA,
				IMU_CLUB_PLAYA,
				IMU_PORTON_ELECTRICO,
				IMU_CHIMENEA,
				IMU_AREAS_VERDES,
				IMU_VISTA_PANORAMICA,
				IMU_CANCHA_SQUASH,
				IMU_CANCHA_BASKET,
				IMU_SALA_CINE,
				IMU_CANCHA_FUT,
				IMU_FAMILY_ROOM,
				IMU_CAMPO_GOLF,
				IMU_CABLETV,
				IMU_BIBLIOTECA,
				IMU_USOS_MULTIPLES,
				IMU_SALA,
				IMU_RECIBIDOR,
				IMU_VESTIDOR,
				IMU_ORATORIO,
				IMU_CAVA,
				IMU_PATIO,
				IMU_BALCON,
				IMU_LOBBY,						
				IMU_NUMERO_OFICINAS,
				IMU_WCS,
				IMU_RECAMARAS,
				IMU_METROS_FRENTE,
				IMU_METROS_FONDO,
				IMU_CAJONES_ESTACIONAMIENTO,
				IMU_DESARROLLO,

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
				TIN_NOMBRE
			FROM INMUEBLE, TIPO_INMUEBLE
			WHERE IMU_ID = :id
			AND IMU_TIPO_INMUEBLE = TIN_ID;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array(":id" => $idInmueble));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$inmueble = array(
			"id"						=>	$row["IMU_ID"],
			"titulo"					=>	$row["IMU_TITULO"],
			"propiedadDestacada"		=>  $row['IMU_DESTACADO'],
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
			"estadoConservacion"		=>	$row["IMU_ESTADO_CONSERVACION"] != NULL ? $row["IMU_ESTADO_CONSERVACION"] : -1,
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
			"iglesiasCercanas"			=>	$row["IMU_IGLESIAS_CERCANAS"],
			"hospitalesCercanos"		=>	$row["IMU_HOSPITALES_CERCANOS"],			
			"amueblado2"				=>	$row["IMU_AMUEBLADO2"],
			"semiamueblado"				=>	$row["IMU_SEMIAMUEBLADO"],
			"zonaIndustrial"			=>	$row["IMU_ZONA_INDUSTRIAL"],
			"zonaTuristica"				=>	$row["IMU_ZONA_TURISTICA"],
			"zonaComercial"				=>	$row["IMU_ZONA_COMERCIAL"],
			"zonaResidencial"			=>	$row["IMU_ZONA_RESIDENCIAL"],
			"baresCercanos"				=>	$row["IMU_BARES_CERCANOS"],
			"supermercadosCercanos"		=>	$row["IMU_SUPERMERCADOS_CERCANOS"],
			"excelenteUbicacion"		=>	$row["IMU_EXCELENTE_UBICACION"],
			"cisterna"					=>	$row["IMU_CISTERNA"],
			"calentador"				=>	$row["IMU_CALENTADOR"],
			"camaras"					=>	$row["IMU_CAMARAS"],
			"anden"						=>	$row["IMU_ANDEN"],
			"asador"					=>	$row["IMU_ASADOR"],
			"vapor"						=>	$row["IMU_VAPOR"],
			"sauna"						=>	$row["IMU_SAUNA"],
			"playa"						=>	$row["IMU_PLAYA"],
			"clubPlaya"					=>	$row["IMU_CLUB_PLAYA"],
			"portonElectrico"			=>	$row["IMU_PORTON_ELECTRICO"],
			"chimenea"					=>	$row["IMU_CHIMENEA"],
			"areasVerdes"				=>	$row["IMU_AREAS_VERDES"],
			"vistaPanoramica"			=>	$row["IMU_VISTA_PANORAMICA"],
			"canchaSquash"				=>	$row["IMU_CANCHA_SQUASH"],
			"canchaBasket"				=>	$row["IMU_CANCHA_BASKET"],
			"salaCine"					=>	$row["IMU_SALA_CINE"],
			"canchaFut"					=>	$row["IMU_CANCHA_FUT"],
			"familyRoom"				=>	$row["IMU_FAMILY_ROOM"],
			"campoGolf"					=>	$row["IMU_CAMPO_GOLF"],
			"cableTV"					=>	$row["IMU_CABLETV"],
			"biblioteca"				=>	$row["IMU_BIBLIOTECA"],
			"usosMultiples"				=>	$row["IMU_USOS_MULTIPLES"],
			"sala"						=>	$row["IMU_SALA"],
			"recibidor"					=>	$row["IMU_RECIBIDOR"],
			"vestidor"					=>	$row["IMU_VESTIDOR"],
			"oratorio"					=>	$row["IMU_ORATORIO"],
			"cava"						=>	$row["IMU_CAVA"],
			"patio"						=>	$row["IMU_PATIO"],
			"balcon"					=>	$row["IMU_BALCON"],
			"lobby"						=>	$row["IMU_LOBBY"],			
			"escuelasCercanas"			=>	$row["IMU_ESCUELAS_CERCANAS"],			
			"fumadoresPermitidos"		=>	$row["IMU_FUMADORES_PERMITIDOS"],
			"numeroOficinas"			=>	$row["IMU_NUMERO_OFICINAS"] != NULL ? $row["IMU_NUMERO_OFICINAS"] : "",
			"wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : -1,
			"recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : -1,
			"metrosFrente"				=>	$row["IMU_METROS_FRENTE"] != NULL ? $row["IMU_METROS_FRENTE"] : "",
			"metrosFondo"				=>	$row["IMU_METROS_FONDO"] != NULL ? $row["IMU_METROS_FONDO"] : "",
			"cajonesEstacionamiento"	=>	$row["IMU_CAJONES_ESTACIONAMIENTO"] != NULL ? $row["IMU_CAJONES_ESTACIONAMIENTO"] : "",
			"desarrollo"				=>	$row["IMU_DESARROLLO"] != NULL ? $row["IMU_DESARROLLO"] : -1,
			"estadoNombre"				=>	$row["CONS_ESTADO"],
			"ciudadNombre"				=>	$row["CONS_CIUDAD"],
			"coloniaNombre"				=>	$row["CONS_COLONIA"],
			"cpNombre"					=>	$row["CONS_CP"],
			"tipoNombre"				=>	$row["TIN_NOMBRE"],
			"imagenes"					=>	array(),
			"transacciones"				=>	array(),
			"videos"					=>	array()
		);
		
		
		$consulta = "SELECT IIN_ID, IIN_IMAGEN, IIN_ORDEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ? ORDER BY IIN_ID;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idInmueble));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$inmueble["imagenes"][] = array(
				"id"		=>	$row["IIN_ID"],
				"imagen"	=>	$urlArchivos.$row["IIN_IMAGEN"],
				"principal"	=>	$row["IIN_ORDEN"]
			);
		}
		
		$consulta = "SELECT TRI_TRANSACCION FROM TRANSACCION_INMUEBLE WHERE TRI_INMUEBLE = ? ORDER BY TRI_ID;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idInmueble));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$inmueble["transacciones"][] = $row["TRI_TRANSACCION"];
		}
		
		$consulta = "SELECT VIN_ID, VIN_VIDEO FROM VIDEO_INMUEBLE WHERE VIN_INMUEBLE = ? ORDER BY VIN_ID;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($idInmueble));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$inmueble["videos"][] = array(
				"id"	=>	$row["VIN_ID"],
				"video"	=>	$row["VIN_VIDEO"]
			);
		}
		
		
		$variables = "post_categoria=".$inmueble["categoria"].",post_tipo=".$inmueble["tipo"].",post_transaccion=".$inmueble["transacciones"][0].",post_estado=".$inmueble["estado"].",post_ciudad=".$inmueble["ciudad"].",post_colonia=".$inmueble["colonia"].",post_latitud='".$inmueble["latitud"]."',post_longitud='".$inmueble["longitud"]."',post_antiguedad=".$inmueble["antiguedad"].",post_estadoConservacion=".$inmueble["estadoConservacion"].",post_amueblado=".$inmueble["amueblado"].",post_wcs=".$inmueble["wcs"].",post_recamaras=".$inmueble["recamaras"].",post_usuario=".$inmueble["usuario"];
	}
	
	
	adminCabeceraHTML("formularioInmueble_ver2.css,formularioInmueble_ver2.js", $variables);
?>
<body>
	<div class='template_principal'>
    	<div class='template_contenedorCuerpo'>
            <div class='template_cuerpo'>
                <div class='lineaIzq'></div>
                <div class="formularioInmueble_cuerpo">
                    <p class="titulo"><?php echo $edit == 0 ? "Crear" : "Modificar"; ?> Anuncio</p>
                    <form id="subirAnuncio" method="post" enctype="multipart/form-data" action="lib_php/updInmueble.php">
                        <input type="text" name="<?php echo $edit == 0 ? "nuevo" : "modificar"; ?>" value="1" style="display:none;" />
                        <input type="text" id="idInmueble" name="id" value="<?php echo $edit == 0 ? "-1" : $inmueble["id"]; ?>" style="display:none;" />
                        <table class="conMargen">
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioTransaccion" name="transaccion" value="" style="display:none;" />
                                            <div class="mascara"></div>
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioTipo" name="tipo" value="" style="display:none;" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p id="etiquetaPrecio">Precio*</p>
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioColonia" name="colonia" value="" style="display:none;" />
                                            <input type="text" id="_crearAnuncioCP" name="cp" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="contenedorCampos">
                                            <p>Ubicación en el mapa*<a class="encontrarUbicacion" href="javascript:nuevoAnuncio_encontrarUbicacion();">Haz click aquí para encontrar tu ubicación en el mapa</a></p>
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioAntiguedad" name="antiguedad" value="" style="display:none;" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Dimensión de Terreno (m<sup style="font-size:8px;">2</sup>)</p>
                                            <input type="text" id="crearAnuncio_dimesionTotal" name="dimensionTotal" class="template_campos" placeholder="Dimensión de Terreno" maxlength="12" value="<?php echo $edit == 1 ? $inmueble["dimensionTotal"] : ""; ?>" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Metros de Frente (mts.)</p>
                                            <input type="text" id="crearAnuncio_metrosFrente" name="metrosFrente" class="template_campos" placeholder="Metros de Frente" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["metrosFrente"] : ""; ?>" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <ul id="crearAnuncio_usuario" class="template_campos">
                                                Usuario*<span></span>
                                                <li class="lista">
                                                    <ul><?php
														for ($x = 0; $x < count($arrayUsuario); $x++) {
                                                        	echo "<li data-value=".$arrayUsuario[$x]["id"]." data-inmobiliaria='".$arrayUsuario[$x]["inmobiliaria"]."'>".$arrayUsuario[$x]["nombre"]."</li>";
														}
                                                    ?></ul>
                                                </li>
                                                <p data-value="-1"></p>
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioUsuario" name="usuario" value="" style="display:none;" />
                                        </div>
                                        <div class="contenedorCampos" style="display:none;">
                                            <input type="text" id="_crearAnuncioDesarrollo" name="desarrollo" value="<?php echo $edit == 1 ? $inmueble["desarrollo"] : -1; ?>" />
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioRecamaras" name="recamaras" value="" style="display:none;" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <ul id="crearAnuncio_estadoConservacion" class="template_campos">
                                                Estado de Conservación<span></span>
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
                                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                            </ul>
                                            <input type="text" id="_crearAnuncioEstadoConservacion" name="estadoConservacion" value="" style="display:none;" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Dimensión de Construcción (m<sup style="font-size:8px;">2</sup>)</p>
                                            <input type="text" id="crearAnuncio_dimensionConstruida" name="dimensionConstruida" class="template_campos" placeholder="Dimensión de Construcción" maxlength="12" value="<?php echo $edit == 1 ? $inmueble["dimensionConstruida"] : ""; ?>" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Metros de Fondo (mts.)</p>
                                            <input type="text" id="crearAnuncio_metrosFondo" name="metrosFondo" class="template_campos" placeholder="Metros de Fondo" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["metrosFondo"] : ""; ?>" />
                                        </div>
                                        <div class="contenedorCampos" id="celdaCodigo" style="display:none;">
                                            <p>Código</p>
                                            <input type="text" id="crearAnuncio_codigo" name="codigo" data-inmueble="" class="template_campos" placeholder="Código" maxlength="64" value="<?php echo $edit == 1 ? $inmueble["codigo"] : ""; ?>" />
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
                                                <iframe src="../lib_php/tempSubirImagen.php" frameborder="0" width="400" height="50"></iframe>
                                            </div>
                                            <input type="text" name="imagen" id="imagen" value="" style="display:none;" />
                                            <input type="text" name="imagenPrincipal" id="imagenPrincipal" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    if ($edit == 1) {
                                        echo
                                            "<tr>
                                                <td colspan='2'>
                                                    <div class='contenedorCampos'>
                                                        <p>Galeria de Videos</p>
                                                        <div id='galeriaVideos' class='videosTemporales'>";
                                        
                                        for ($x = 0; $x < count($inmueble["videos"]); $x++) {
                                            echo
                                                            "<div class='bloqueVideo' data-video='".$inmueble["videos"][$x]["id"]."'>
                                                                <object>
                                                                    <param name='movie' value='".str_replace("watch?v=", "v/", $inmueble["videos"][$x]["video"])."?version=3&feature=player_detailpage'>
                                                                    <param name='allowFullScreen' value='true'>
                                                                    <param name='allowScriptAccess' value='always'>
                                                                    <embed src='".str_replace("watch?v=", "v/", $inmueble["videos"][$x]["video"])."?version=3&feature=player_detailpage&showinfo=0&autohide=1&rel=0' type='application/x-shockwave-flash' allowfullscreen='true' allowScriptAccess='always' wmode=transparent width='60' height='60' showinfo=0>
                                                                </object>
                                                                <span class='borrar'>X</span>
                                                            </div>";
                                        }
                                            
                                                        
                                        echo
                                                        "</div>
                                                    </div>
                                                </td>
                                            </tr>";
                                    }
                                ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="contenedorCampos">
                                            <p>Videos</p>
                                            <div id="videosTemporales" class="videosTemporales"></div>
                                            <p><input type="text" id="nuevoAnuncio_urlVideo" class="template_campos" placeholder="Ingresa una url de Youtube y enseguida presiona enter" /></p>
                                            <input type="text" name="videos" id="videos" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
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
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="sala" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["sala"] == 1 ? "checked='checked'" : "") : ""; ?> />Sala
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="recibidor" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["recibidor"] == 1 ? "checked='checked'" : "") : ""; ?> />Recibidor
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vestidor" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["vestidor"] == 1 ? "checked='checked'" : "") : ""; ?> />Vestidor
                                        </div>     
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="patio" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["patio"] == 1 ? "checked='checked'" : "") : ""; ?> />Patio
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="balcon" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["balcon"] == 1 ? "checked='checked'" : "") : ""; ?> />Balcón
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
                                            <input type="checkbox" name="areaJuegosInfantiles" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["areaJuegosInfantiles"] == 1 ? "checked='checked'" : "") : ""; ?> />Área Infantil
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="biblioteca" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["biblioteca"] == 1 ? "checked='checked'" : "") : ""; ?> />Biblioteca
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="usosMultiples" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["usosMultiples"] == 1 ? "checked='checked'" : "") : ""; ?> />Salón de Usos Múltiples
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="oratorio" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["oratorio"] == 1 ? "checked='checked'" : "") : ""; ?> />Oratorio
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cava" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["cava"] == 1 ? "checked='checked'" : "") : ""; ?> />Cava
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="lobby" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["lobby"] == 1 ? "checked='checked'" : "") : ""; ?> />Lobby
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
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cisterna" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["cisterna"] == 1 ? "checked='checked'" : "") : ""; ?> />Cisterna
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="calentador" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["calentador"] == 1 ? "checked='checked'" : "") : ""; ?> />Calentador
                                        </div>                                
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkCuotaMantenimiento" style="margin-right:10px;" />Cuota Mantenimiento
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
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="camaras" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["camaras"] == 1 ? "checked='checked'" : "") : ""; ?> />Cámaras de Vigilancia
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="anden" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["anden"] == 1 ? "checked='checked'" : "") : ""; ?> />Andén
                                        </div>                                
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkElevador" style="margin-right:10px;" />Elevador
                                            <input type="text" id="crearAnuncio_elevador" name="elevador" class="template_campos" placeholder="Cantidad de Elevador" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["elevador"] : ""; ?>" />
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
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="sauna" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["sauna"] == 1 ? "checked='checked'" : "") : ""; ?> />Sauna
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="asador" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["asador"] == 1 ? "checked='checked'" : "") : ""; ?> />Asador
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="portonElectrico" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["portonElectrico"] == 1 ? "checked='checked'" : "") : ""; ?> />Portón Eléctrico
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="chimenea" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["chimenea"] == 1 ? "checked='checked'" : "") : ""; ?> />Chimenea
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaSquash" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["canchaSquash"] == 1 ? "checked='checked'" : "") : ""; ?> />Cancha de Squash
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaBasket" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["canchaBasket"] == 1 ? "checked='checked'" : "") : ""; ?> />Cancha de Basketball
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="familyRoom" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["familyRoom"] == 1 ? "checked='checked'" : "") : ""; ?> />Family Room
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="campoGolf" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["campoGolf"] == 1 ? "checked='checked'" : "") : ""; ?> />Campo de Golf
                                        </div>                                                                                                
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkEstacionamientoVisitas" style="margin-right:10px;" />Estacionamiento para Visitas
                                            <input type="text" id="crearAnuncio_estacionamientoVisitas" name="estacionamientoVisitas" class="template_campos" placeholder="Cantidad de Estacionamiento para Visitas" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["estacionamientoVisitas"] : ""; ?>" />
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
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vapor" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["vapor"] == 1 ? "checked='checked'" : "") : ""; ?> />Vapor
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="playa" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["playa"] == 1 ? "checked='checked'" : "") : ""; ?> />Playa
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="clubPlaya" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["clubPlaya"] == 1 ? "checked='checked'" : "") : ""; ?> />Club de Playa
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="areasVerdes" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["areasVerdes"] == 1 ? "checked='checked'" : "") : ""; ?> />Áreas Verdes
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vistaPanoramica" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["vistaPanoramica"] == 1 ? "checked='checked'" : "") : ""; ?> />Vista Panorámica
                                        </div>      
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaFut" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["canchaFut"] == 1 ? "checked='checked'" : "") : ""; ?> />Cancha de Fútbol
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="salaCine" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["salaCine"] == 1 ? "checked='checked'" : "") : ""; ?> />Sala de Cine
                                        </div>                                                                                          
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cableTV" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["cableTV"] == 1 ? "checked='checked'" : "") : ""; ?> />Televisión por Cable
                                        </div>
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkCajonesEstacionamiento" style="margin-right:10px;" />Cajones de Estacionamiento
                                            <input type="text" id="crearAnuncio_cajonesEstacionamiento" name="cajonesEstacionamiento" class="template_campos" placeholder="Cantidad de Cajones de Estacionamiento" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["cajonesEstacionamiento"] : ""; ?>" />
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
                                            <input type="checkbox" name="amueblado2" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["amueblado2"] == 1 ? "checked='checked'" : "") : ""; ?> />Amueblado 
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="semiamueblado" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["semiamueblado"] == 1 ? "checked='checked'" : "") : ""; ?> />Semi Amueblado
                                        </div>                            
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="centrosComercialesCercanos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["centrosComercialesCercanos"] == 1 ? "checked='checked'" : "") : ""; ?> />Centros Comerciales 
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="escuelasCercanas" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["escuelasCercanas"] == 1 ? "checked='checked'" : "") : ""; ?> />Escuelas Cercanas
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="iglesiasCercanas" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["iglesiasCercanas"] == 1 ? "checked='checked'" : "") : ""; ?> />Iglesias Cercanas
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="supermercadosCercanos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["supermercadosCercanos"] == 1 ? "checked='checked'" : "") : ""; ?> />Supermercados Cercanos
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaComercial" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["zonaComercial"] == 1 ? "checked='checked'" : "") : ""; ?> />Zona Comercial
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaResidencial" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["zonaResidencial"] == 1 ? "checked='checked'" : "") : ""; ?> />Zona Residencial
                                        </div>                                                                                                             
                                    </td>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="fumadoresPermitidos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["fumadoresPermitidos"] == 1 ? "checked='checked'" : "") : ""; ?> />Fumadores Permitidos
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="excelenteUbicacion" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["excelenteUbicacion"] == 1 ? "checked='checked'" : "") : ""; ?> />Excelente Ubicación
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="hospitalesCercanos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["hospitalesCercanos"] == 1 ? "checked='checked'" : "") : ""; ?> />Hospitales Cercanos
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="baresCercanos" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["baresCercanos"] == 1 ? "checked='checked'" : "") : ""; ?> />Bares y Restaurantes
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaIndustrial" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["zonaIndustrial"] == 1 ? "checked='checked'" : "") : ""; ?> />Zona Industrial
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaTuristica" style="margin-right:10px;" <?php echo $edit == 1 ? ($inmueble["zonaTuristica"] == 1 ? "checked='checked'" : "") : ""; ?> />Zona Turística
                                        </div>      
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkNumeroOficinas" style="margin-right:10px;" />Oficinas
                                            <input type="text" id="crearAnuncio_numeroOficinas" name="numeroOficinas" class="template_campos" placeholder="Cantidad de Oficinas" maxlength="11" value="<?php echo $edit == 1 ? $inmueble["numeroOficinas"] : ""; ?>" />
                                        </div>                                                                                          
                                        <div class="contenedorCampos">
                                            <p id="btnGuardar2" class="subtitulo guardar" onClick="nuevoAnuncio_validarCampos_inmueble();" style="padding-top:40px;"><a class="btnBotones <?php echo $edit == 0 ? "palomita" : "guardar"; ?>">Guardar</a><?php echo $edit == 0 ? "Publicar Anuncio" : "Guardar Cambios"; ?></p>
                                            <p id="mensajeTemporal2" style="display:none;">Espere un momento...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class='lineaDer'></div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyBlYGs9bCTLNLYemvkULvJUaQR_vA7S9k4"></script>
<?php
	adminPopUpsGenerales("formularioInmueble_cerrarPopup");
?>
<div id='template_mascaraPrincipal' class='template_mascaraPrincipal' onclick='principalCerrarPopUp(formularioInmueble_cerrarPopup);'></div>
<div id='template_errorSelectMunicipio' class='templatePopUp template_errorSelectMunicipio'>
    <span class='btnCerrar' onclick='template_principalCerrarPopUp(formularioInmueble_cerrarPopup);'>x</span>
    <table>
        <tbody>
            <tr>
                <td>Debes seleccionar primero un Estado</td>
            </tr>
        </tbody>
    </table>
</div>
<div id='template_errorSelectColonia' class='templatePopUp template_errorSelectMunicipio'>
    <span class='btnCerrar' onclick='template_principalCerrarPopUp(formularioInmueble_cerrarPopup);'>x</span>
    <table>
        <tbody>
            <tr>
                <td>Debes seleccionar primero un Municipio</td>
            </tr>
        </tbody>
    </table>
</div>
<div id='template_alertPersonalizado' class='templatePopUp template_alertPersonalizado'>
    <span class='btnCerrar' onclick='template_principalCerrarPopUp(formularioInmueble_cerrarPopup);'>x</span>
    <table>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<?php
?>
</body>
<?php
	adminFinHTML();
?>