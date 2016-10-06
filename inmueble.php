<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	$conexion = crearConexionPDO();


	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	$idInmueble = $_GET["id"];
	$regresar = isset($_SESSION[userFiltros]["regresar"]) ? 1 : 0;
	$codigo = "";
	$urlArchivos = "images/images/";


	$consulta = "UPDATE INMUEBLE SET IMU_CONT_VISITAS = IMU_CONT_VISITAS + 1 WHERE IMU_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idInmueble));


	$inmueble = array();
	$consulta =
		"SELECT
			IMU_ID,
			IMU_TITULO,
			IMU_USUARIO, ".
			//IMU_CATEGORIA_INMUEBLE,
			"IMU_TIPO_INMUEBLE,
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
			IMU_LIMITE_VIGENCIA,
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
				SELECT DES_TITULO
				FROM DESARROLLO
				WHERE DES_ID = IMU_DESARROLLO
			) AS CONS_DESARROLLO,
			( ".(
				$usuario != -1
				? (
					"SELECT FIN_ID
					FROM FAVORITO_INMUEBLE
					WHERE FIN_USUARIO = ".$usuario."
					AND FIN_INMUEBLE = IMU_ID"
				) : -1
			)." ) AS CONS_LIKE
		FROM INMUEBLE, TIPO_INMUEBLE
		WHERE IMU_ID = ?
		AND IMU_TIPO_INMUEBLE = TIN_ID;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idInmueble));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$inmueble = array(
		"id"						=>	$idInmueble,
		"titulo"					=>	$row["IMU_TITULO"],
		"usuario"					=>	$row["IMU_USUARIO"],
		//"categoria"					=>	$row["IMU_CATEGORIA_INMUEBLE"],
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
		"estadoConservacion"		=>	$row["IMU_ESTADO_CONSERVACION"] != NULL ? $row["IMU_ESTADO_CONSERVACION"] : "",
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
		"wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : "",
		"recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
		"metrosFrente"				=>	$row["IMU_METROS_FRENTE"] != NULL ? $row["IMU_METROS_FRENTE"] : "",
		"metrosFondo"				=>	$row["IMU_METROS_FONDO"] != NULL ? $row["IMU_METROS_FONDO"] : "",
		"cajonesEstacionamiento"	=>	$row["IMU_CAJONES_ESTACIONAMIENTO"] != NULL ? $row["IMU_CAJONES_ESTACIONAMIENTO"] : "",
		"limiteVigencia"			=>	getDateNormal($row["IMU_LIMITE_VIGENCIA"]),
		"estadoNombre"				=>	$row["CONS_ESTADO"],
		"ciudadNombre"				=>	$row["CONS_CIUDAD"],
		"coloniaNombre"				=>	$row["CONS_COLONIA"],
		"cpNombre"					=>	$row["CONS_CP"],
		"tipoNombre"				=>	$row["TIN_NOMBRE"],
		"like"						=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"],
		"desarrollo"				=>	$row["IMU_DESARROLLO"] != NULL ? $row["IMU_DESARROLLO"] : "",
		"desarrolloNombre"			=>	$row["CONS_DESARROLLO"] != NULL ? $row["CONS_DESARROLLO"] : "",
		"imagenes"					=>	array()
	);


	$consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ? ORDER BY IIN_ORDEN DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idInmueble));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$inmueble["imagenes"][] = $urlArchivos.$row["IIN_IMAGEN"];
	}


	$arrayTipoInmueble = array();

	$consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayTipoInmueble[] = array(
			"id"	=>	$row["TIN_ID"],
			"nombre"=>	$row["TIN_NOMBRE"]
		);
	}

	$arrayEstado = array();

	$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayEstado[] = array(
			"id"	=>	$row["EST_ID"],
			"nombre"=>	$row["EST_NOMBRE"]
		);
	}


	$arrayRazonReporte = array();

	$consulta = "SELECT RAR_ID, RAR_NOMBRE FROM RAZON_REPORTE ORDER BY RAR_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayRazonReporte[] = array(
			"id"		=>	$row["RAR_ID"],
			"nombre"	=>	$row["RAR_NOMBRE"]
		);
	}


	$metasFacebook = array(
		"titulo"		=>	$inmueble["titulo"],
		"imagen"		=>	$inmueble["imagenes"][0],
		"descripcion"	=>	$inmueble["descripcion"],
		"url"			=>	"inmueble.php?id=".$inmueble["id"]
	);


	$arrayUsuarios = array();
	if ($usuario != -1) {
		if ($_SESSION[userAdminInmobiliaria] == 1) {
			$consulta = "SELECT USU_ID FROM USUARIO WHERE USU_INMOBILIARIA = ?;";
			$pdo = $conexion->prepare($consulta);
			$pdo->execute(array($_SESSION[userInmobiliaria]));
			foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$arrayUsuarios[] = $row["USU_ID"];
			}
		}
	}


	$consulta = "SELECT USU_NOMBRE, USU_CREATE, USU_INMOBILIARIA, USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($inmueble["usuario"]));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$vendedor = array(
		"nombre"		=>	$row["USU_NOMBRE"],
		"create"		=>	getDateNormal($row["USU_CREATE"]),
		"inmobiliaria"	=>	array(
			"id"		=>	$row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : 0
		),
		"imagen"		=> $row['USU_IMAGEN']
	);

	if ($vendedor["inmobiliaria"]["id"] != 0) {
		$consulta = "SELECT INM_NOMBRE_EMPRESA, INM_CREATE, INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($vendedor["inmobiliaria"]["id"]));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$vendedor["inmobiliaria"]["nombre"] = $row["INM_NOMBRE_EMPRESA"];
		$vendedor["inmobiliaria"]["create"] = getDateNormal($row["INM_CREATE"]);
		$vendedor["inmobiliaria"]["logotipo"] = $row["INM_LOGOTIPO"] != NULL ? $urlArchivos.$row["INM_LOGOTIPO"] : "";
	}


	$usuarioActual = array();

	if ($usuario != -1) {
		$consulta = "SELECT USU_EMAIL FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($usuario));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$usuarioActual = array(
			"id"		=>	$usuario,
			"nombre"	=>	$_SESSION[userNombre],
			"email"		=>	$row["USU_EMAIL"]
		);
	}


	/*
		valida si el inmueble ya vencio;
		si pertenece a una inmobiliaria, lo manda a la interfaz para hacer el pago de la inmobiliaria
		si no pertenece a ningun inmobliaria, lo manda a la interfaz para hacer el pago del inmueble
	*/
	if (isset($_GET["create"])) {
		if ($vendedor["inmobiliaria"]["id"] == 0) {//usuario
			$partes = explode("/", $inmueble["limiteVigencia"]);

			if (mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]) < mktime(0, 0, 0, date("m"), date("d"), date("Y")))
				header("location: opcionPagoInmueble.php?idInmueble=".$inmueble["id"]);
		}
		else//inmobiliaria
			header("location: opcionPagoInmobiliaria.php?idInmobiliaria=".$vendedor["inmobiliaria"]["id"]."&inmueble=".$inmueble["id"]);
	}

$variables = "";
$arrayDatosPost = array();


$transaccion = -1;
$tipoInmueble = -1;
$estado = -1;
$ciudad = -1;
$pagina = 0;
$elem = 10;

$transaccion = $_SESSION[userFiltros]['transaccion'];
$tipoInmueble = $_SESSION[userFiltros]['tipoInmueble'];
$estado = $_SESSION[userFiltros]['estado'];
$ciudad = $_SESSION[userFiltros]['ciudad'];

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
//$transaccion = $_GET["transaccion"];
//$tipoInmueble = $_GET["tipoInmueble"] != "todos-los-tipos" ? $_GET["tipoInmueble"] : -1;


if ($tipoInmueble != -1) {
	$_terminacion = $tipoInmueble{strlen($tipoInmueble) - 2}.$tipoInmueble{strlen($tipoInmueble) - 1};
	$tipoInmueble = ucfirst(substr($tipoInmueble, 0, ($_terminacion == "es" ? (strlen($tipoInmueble) - 2) : (strlen($tipoInmueble) - 1))));
}
//$estado = $_GET["estado"] != "todo-mexico"   ? $_GET["estado"] : -1;
//$ciudad = $_GET["ciudad"] != "todas-las-ciudades" ? $_GET["ciudad"] : -1;
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
		$wcs = $_SESSION[userFiltros]["wcs"];
		$recamaras = $_SESSION[userFiltros]["recamaras"];
	}

	if (isset($_SESSION[userFiltros]["orden"]))
		$orden = $_SESSION[userFiltros]["orden"];

	if (isset($_SESSION[userFiltros]["elem"]))
		$elem = $_SESSION[userFiltros]["elem"];
}





$variables.= "post_transaccion=".$transaccion.",post_tipoInmueble=".$tipoInmueble.",post_estado=".$estado.",post_ciudad=".$ciudad.",post_colonia=".$_SESSION[userFiltros]["colonia"].",post_preciosMin=".$preciosMin.",post_preciosMax=".$preciosMax.",post_wcs=".$wcs.",post_recamaras=".$recamaras.",post_orden=".$orden.",post_elem=".$elem.",post_pagina=".$pagina;


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



$arrayDatosPost['transaccion'] = $transaccion;
$arrayDatosPost['tipoInmueble'] = $tipoInmueble;

//}

$colonia = (isset($_SESSION[userFiltros]["colonia"]))?$_SESSION[userFiltros]["colonia"]:-1;


CabeceraHTML("inmueble-responsive.css,inmueble_ver24.js", $variables, $metasFacebook);
	bodyIndex();
?>
	<div class="banner inmueble hidden-xs hidden-print">
		<?php templateBuscadorResponsive(); ?>
		<img src="images/images/0451438001436838815.jpg" class="img-responsive" alt="Casas en venta en guadalajara">

	</div>
	<br>
<div class="container mobile-only">
	<a href="#" class="btn btn-inmueble btn-lg btn-block" onclick="window.history.back();" >Regresar a Propiedades</a>
</div>
<div class="container catalogo_cuerpo propiedad">
	<section class="col-lg-9 col-md-12">
		<h1 class="titulo"><?php echo $inmueble["titulo"]; ?></h1>
		<span class="address">
			<?php echo $inmueble['coloniaNombre'].', '.$inmueble['ciudadNombre'].', '.$inmueble['estadoNombre'].', C.P. '.$inmueble['cpNombre']; ?>
		</span>

		<div class="gallery">
			<img class="imagenPrincipal img-responsive" src="<?php echo $inmueble["imagenes"][0]; ?>" alt="<?php echo $inmueble["titulo"]; ?>" data-pos="0" />
			<div class="galeria hidden-xs hidden-print">
				<div class="contenedorFlechas">
					<a class="flechas prev">Prev</a>
				</div><div class="contenedorDesplazamiento">
					<div class="desplazamiento"><?php
						for ($x = 0; $x < count($inmueble["imagenes"]); $x++) {
							echo
								"<div class='bloque'>
												<img src='".$inmueble["imagenes"][$x]."' alt='".$inmueble["titulo"]."' />
											</div>";
						}
						?></div>
				</div><div class="contenedorFlechas">
					<a class="flechas next">Next</a>
				</div>
			</div>
		</div>

		<div class="row gallery visible-print-block">
			<?php
			$gallerySize = (count($inmueble["imagenes"])<=4)?count($inmueble["imagenes"]):4;
			for ($x = 0; $x < count($inmueble["imagenes"]) ; $x++) {
			echo
			"<div class='col-lg-3 col-sm-6 col-xs-4 gallery-item' >
				<img class=\"img-responsive\" src='".$inmueble["imagenes"][$x]."' alt='".$inmueble["titulo"]."' />
			</div>";
			}
			?>
		</div>
		<br>
		<span class="price">$<?php echo number_format($inmueble["precio"], 0, ".", ","); ?> MXN</span><br />
		<span class="code">C&Oacute;DIGO DEL INMUEBLE: <?php echo $inmueble["id"]; ?></span>

		<div class="information property row">
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-graphicseditor63"></i> TERRENO <br />
					<span><?php echo (!empty($inmueble['dimensionTotal']))?$inmueble['dimensionTotal'].'m<sup>2</sup>':'-'; ?></span>
				</div>
			</div>
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-house158"></i> CONSTRUCCI&Oacute;N <br />
					<span>
						<?php echo (!empty($inmueble['dimensionConstruida']))?$inmueble['dimensionConstruida'].'m<sup>2</sup>':'-'; ?>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-beds2"></i> CUARTOS <br />
					<span><?php echo (!empty($inmueble['recamaras']))?$inmueble['recamaras']:"-";?></span>
				</div>
			</div>
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-shower15"></i> BA&Ntilde;OS <br />
					<span>
						<?php echo (!empty($inmueble['wcs']))?number_format($inmueble['wcs']):'-'; ?>
					</span>
				</div>
			</div>

		</div>

		<div class="information property row">
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-garage13"></i> COCHERA <br />
					<span>
						<?php echo ($inmueble['cajonesEstacionamiento']>0)?$inmueble['cajonesEstacionamiento']:'-'; ?>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-arrow159"></i> FRENTE <br />
					<span>
						<?php echo ($inmueble['metrosFrente']>0)?$inmueble['metrosFrente'].'m':'-'; ?>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-double97"></i> FONDO <br />
					<span>
						<?php echo ($inmueble['metrosFondo']>0)?$inmueble['metrosFondo'].'m':'-'; ?>
					</span>
				</div>
			</div>
			<div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 property main-info">
				<div><i class="flaticon-clock140"></i> ANTIGUEDAD <br />
					<span>
						<?php echo ($inmueble['antiguedad']>0)?$inmueble['antiguedad'].' a&ntilde;os':'-'; ?>
					</span>
				</div>
			</div>

		</div>

		<div class="descripcion">
			<h3>Descripci&oacute;n:</h3>
			<p><?php echo $inmueble["descripcion"]; ?></p>
		</div>

		<div class="list-info row">
		<?php
		$arrayAmbientes = array();
		$tempCampos = array("cocinaEquipada", "estudio", "cuartoServicio", "cuartoTV", "bodega", "terraza", "jardin", "areaJuegosInfantiles", "comedor", "wcs", "recamaras", "biblioteca", "usosMultiples", "sala", "recibidor", "vestidor", "oratorio", "cava", "patio", "balcon", "lobby", "metrosFrente", "metrosFondo");
		$tempCadenas = array("Cocina Equipada", "Estudio", "Cuarto de Servicio", "Cuarto de TV", "Bodega", "Terraza", "Jardín", "Área de Juegos Infantiles", "Comedor", "Baños", "Recamaras", "Biblioteca", "Salón de Usos Múltiples", "Sala", "Recibidor", "Vestidor", "Oratorio", "Cava", "Patio", "Balcón", "Lobby", "Metros de Frente", "Metros de Fondo");

		for ($x = 0; $x < count($tempCampos); $x++) {
			$flag = false;

			if (($tempCampos[$x] == "wcs") || ($tempCampos[$x] == "recamaras") || ($tempCampos[$x] == "metrosFrente") || ($tempCampos[$x] == "metrosFondo")) {
				$flag = true;

				if ($inmueble[$tempCampos[$x]] != "") {
					$_temp = $tempCadenas[$x].": ".($tempCampos[$x] == "wcs" ? (fmod($inmueble[$tempCampos[$x]], 1) == 0 ? (int)$inmueble[$tempCampos[$x]] : number_format($inmueble[$tempCampos[$x]], 1)) : $inmueble[$tempCampos[$x]]);

					if (($tempCampos[$x] == "metrosFrente") || ($tempCampos[$x] == "metrosFondo")) {
						$_temp.= " mts.";
					}

					$arrayAmbientes[] = $_temp;
				}
			}

			if (!$flag) {
				if ($inmueble[$tempCampos[$x]] == 1)
					$arrayAmbientes[] = $tempCadenas[$x];
			}
		}


		$arrayServicios = array();
		$tempCampos = array("serviciosBasicos", "gas", "lineaTelefonica", "internetDisponible", "aireAcondicionado", "calefaccion", "cuotaMantenimiento", "casetaVigilancia", "elevador", "seguridad", "cisterna", "calentador", "camaras", "anden");
		$tempCadenas = array("Servicios Básicos", "Gas", "Linea Telefónica", "Internet Disponible", "Aire Acondicionado", "Calefacción", "Cuota Mantenimiento", "Caseta de Vigilancia", "Elevador", "Seguridad", "Cisterna", "Calentador de Agua", "Cámaras de Vigilancia", "Andén");



		for ($x = 0; $x < count($tempCampos); $x++) {
			$flag = false;

			if ($tempCampos[$x] == "cuotaMantenimiento") {
				$flag = true;

				if ($inmueble[$tempCampos[$x]] != "")
					$arrayServicios[] = $tempCadenas[$x].": $".number_format($inmueble[$tempCampos[$x]], 0, ".", ",");
			}

			if ($tempCampos[$x] == "elevador") {
				$flag = true;

				if ($inmueble[$tempCampos[$x]] != "")
					$arrayServicios[] = $tempCadenas[$x].": ".$inmueble[$tempCampos[$x]];
			}

			if (!$flag) {
				if ($inmueble[$tempCampos[$x]] == 1)
					$arrayServicios[] = $tempCadenas[$x];
			}
		}


		$arrayAmenidades = array();
		$tempCampos = array("alberca", "casaClub", "canchaTenis", "vistaMar", "jacuzzi", "estacionamientoVisitas", "permiteMascotas", "gimnasio", "asador", "vapor", "sauna", "playa", "clubPlaya","portonElectrico", "chimenea", "areasVerdes", "vistaPanoramica", "canchaSquash", "canchaBasket", "salaCine", "canchaFut", "familyRoom", "campoGolf", "cableTV", "cajonesEstacionamiento");
		$tempCadenas = array("Alberca", "Casa Club", "Cancha de Tenis", "Vista al Mar", "Jacuzzi", "Estacionamiento para Visitas", "Se permite mascotas", "Gimnasio", "Asador", "Vapor", "Sauna", "Playa", "Club de Playa", "Portón Eléctrico", "Chimenea", "Áreas Verdes", "Vista Panorámica", "Cancha de Squash", "Cancha de Basketball", "Sala de Cine", "Cancha de Futbol", "Family Room", "Campo de Golf", "Televisión por Cable", "Cajones de Estacionamiento");


		for ($x = 0; $x < count($tempCampos); $x++) {
			$flag = false;

			if (($tempCampos[$x] == "estacionamientoVisitas") || ($tempCampos[$x] == "cajonesEstacionamiento")) {
				$flag = true;

				if ($inmueble[$tempCampos[$x]] != "")
					$arrayAmenidades[] = $tempCadenas[$x].": ".$inmueble[$tempCampos[$x]];
			}

			if (!$flag) {
				if ($inmueble[$tempCampos[$x]] == 1)
					$arrayAmenidades[] = $tempCadenas[$x];
			}
		}


		$arrayOtros = array();
		$tempCampos = array("centrosComercialesCercanos", "iglesiasCercanas", "hospitalesCercanos", "escuelasCercanas", "fumadoresPermitidos", "amueblado2", "semiamueblado", "zonaIndustrial", "zonaTuristica", "zonaComercial", "zonaResidencial","supermercadosCercanos",  "baresCercanos", "excelenteUbicacion", "numeroOficinas");
		$tempCadenas = array("Centros Comerciales Cercanos","Iglesias Cercanas", "Hospitales Cercanos", "Escuelas Cercanas", "Fumadores Permitidos", "Amueblado", "Semi Amueblado", "Zona Industrial", "Zona Turistica", "Zona Comercial", "Zona Residencial","Supermercados Cercanos",  "Bares Cercanos", "Excelente Ubicacion",  "Numero de Oficinas");


		for ($x = 0; $x < count($tempCampos); $x++) {
			$flag = false;

			if ($tempCampos[$x] == "numeroOficinas") {
				$flag = true;

				if ($inmueble[$tempCampos[$x]] != "")
					$arrayOtros[] = $tempCadenas[$x].": ".$inmueble[$tempCampos[$x]];
			}

			if (!$flag) {
				if ($inmueble[$tempCampos[$x]] == 1)
					$arrayOtros[] = $tempCadenas[$x];
			}
		}


		if (count($arrayAmbientes) > 0) {
			echo
			"<div class=\"col-md-3\">
											<h4>Ambientes</h4>
											<ul>";


			for ($x = 0; $x < count($arrayAmbientes); $x++) {
				echo "<li><span>".$arrayAmbientes[$x]."</span></li>";
			}


			echo
			"</ul>
										</div>";
		}


		if (count($arrayServicios) > 0) {
			echo
			"<div class=\"col-md-3\">
											<h4>Servicios</h4>
											<ul>";


			for ($x = 0; $x < count($arrayServicios); $x++) {
				echo "<li><span>".$arrayServicios[$x]."</span></li>";
			}


			echo
			"</ul>
										</div>";
		}


		if (count($arrayAmenidades) > 0) {
			echo
			"<div class=\"col-md-3\">
											<h4>Amenidades</h4>
											<ul>";


			for ($x = 0; $x < count($arrayAmenidades); $x++) {
				echo "<li><span>".$arrayAmenidades[$x]."</span></li>";
			}


			echo
			"</ul>
										</div>";
		}


		if (count($arrayOtros) > 0) {
			echo
			"<div class=\"col-md-3\">
											<h4>Otros</h4>
											<ul>";


			for ($x = 0; $x < count($arrayOtros); $x++)	 {
				echo "<li><span>".$arrayOtros[$x]."</span></li>";
			}

			echo
			"</ul>
										</div>";
		}
		?>
		</div>
		<h2 class="titulo">UBICACION:</h2>
		<span class="address">
			<?php echo $inmueble['coloniaNombre'].', '.$inmueble['ciudadNombre'].', '.$inmueble['estadoNombre'].', C.P. '.$inmueble['cpNombre']; ?>
		</span>
		<div id="inmueble_mapa" class="inmueble_mapa" data-latitud="<?php echo $inmueble["latitud"] ?>" data-longitud="<?php echo $inmueble["longitud"]; ?>"></div>

		<h2 id="pregunta-inmueble" class="titulo hidden-print">PREGUNTA POR ESTE INMUEBLE</h2>
		<div class="col-md-9 col-sm-12 np">


		<div class="inmueble_contacto hidden-print">

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
				<div class="col-md-12"><span class="btn btn-inmueble btn-lg" data-inmueble="<?php echo $inmueble["id"]; ?>" onclick="inmueble_validarContacto();">Enviar</span><br /><br /></div> 
			</div>

		</div>
		</div>
	</section>


	<aside class="col-md-12 col-lg-3 hidden-print">
		<a href="#" class="btn btn-inmueble btn-lg  hidden-xs col-md-6 col-lg-12 " onclick="window.history.back();" >Regresar a Propiedades</a><br /><br /><br />

		<!-- <div class="add-toFavorites otrosBotones">
			<a class="btnBotones estrella" data-id="<?php echo $inmueble["like"]; ?>" data-inmueble="<?php echo $inmueble["id"]; ?>">
				<i class="fa <?php echo $inmueble["like"] > 0 ? "fa-heart" : "fa-heart-o"; ?>"></i>
				<span class="estrellaTexto"><?php echo $inmueble["like"] > 0 ? "Quitar de Favoritos" : "Marcar como Favorito"; ?></span>
			</a>
		</div> -->

		<!--<h2 class="titulo">Anunciante:</h2>-->
		<h2 class="titulo">Pregunta por este inmueble:</h2>
		<?php
		$_maxNombre = 35;

		if ($vendedor["inmobiliaria"]["id"] == 0) {
			/*$_nombre = strlen($vendedor["nombre"]) > $_maxNombre ? substr($vendedor["nombre"], 0, ($_maxNombre - 3))."..." : $vendedor["nombre"];

			
			echo
				"
				<a href='usuario.php?id=".$inmueble['usuario']."'><img class='img-responsive' src='images/images/".$vendedor['imagen']."'/></a>
				<div class='user normal'><a href='usuario.php?id=".$inmueble["usuario"]."'>".$_nombre."</a></div>
				<h4>Miembro desde:</h4>
				<p class='member-since'>".$vendedor["create"]."</p>
				";*/
		?>
			<div class="inmueble_contacto hidden-print">

				<div>
					<div class="col-md-12"><input type="text" id="contacto_nombre_2" class="template_campos" placeholder="Nombre" /></div>
				</div>
				<div>
					<div class="col-md-6"><input type="text" id="contacto_email_2" class="template_campos" placeholder="E-mail" /></div>
					<div class="col-md-6"><input type="text" id="contacto_telefono_2" class="template_campos" placeholder="Teléfono" /></div>
				</div>

				<div>
					<div class="col-md-12"><textarea id="contacto_mensaje_2" class="template_campos" placeholder="Mensaje"></textarea></div>
				</div>
				<div>
					<div class="col-md-12"><span class="btn btn-inmueble btn-lg" data-inmueble="<?php echo $inmueble["id"]; ?>" onclick="inmueble_validarContacto_2();">Enviar</span><br /><br /></div> 
				</div>

			</div>
		<?php
		}
		else {
			/*$_nombre = strlen($vendedor["inmobiliaria"]["nombre"]) > $_maxNombre ? substr($vendedor["inmobiliaria"]["nombre"], 0, ($_maxNombre - 3))."..." : $vendedor["inmobiliaria"]["nombre"];

			if( $vendedor['inmobiliaria']['logotipo'] != ''){
				echo "<a href='inmobiliaria.php?id=".$vendedor["inmobiliaria"]["id"]."'><img src='".$vendedor["inmobiliaria"]["logotipo"]."' class='logotipo img-responsive' />";
			}
			echo "<div class='user inmobiliaria'><a href='inmobiliaria.php?id=".$vendedor["inmobiliaria"]["id"]."'>".$_nombre."</a></div>
				<h4>Miembro desde:</h4>
				<p class='member-since'>".$vendedor["inmobiliaria"]["create"]."</p>"; */?>
			<div class="inmueble_contacto hidden-print">

				<div>
					<div class="col-md-12"><input type="text" id="contacto_nombre_2" class="template_campos" placeholder="Nombre" /></div>
				</div>
				<div>
					<div class="col-md-6"><input type="text" id="contacto_email_2" class="template_campos" placeholder="E-mail" /></div>
					<div class="col-md-6"><input type="text" id="contacto_telefono_2" class="template_campos" placeholder="Teléfono" /></div>
				</div>

				<div>
					<div class="col-md-12"><textarea id="contacto_mensaje_2" class="template_campos" placeholder="Mensaje"></textarea></div>
				</div>
				<div>
					<div class="col-md-12"><span class="btn btn-inmueble btn-lg" data-inmueble="<?php echo $inmueble["id"]; ?>" onclick="inmueble_validarContacto_2();">Enviar</span><br /><br /></div> 
				</div>

			</div>

		<?php 
			}
		?>

		<div class="template_contenedorReputacion" data-tipo="<?php echo $vendedor["inmobiliaria"]["id"] == 0 ? "usuario" : "inmobiliaria"; ?>" data-id="<?php echo $vendedor["inmobiliaria"]["id"] == 0 ? $inmueble["usuario"] : $vendedor["inmobiliaria"]["id"]; ?>" data-propietario="<?php echo $inmueble["usuario"]; ?>"></div>
		<br><br>

		<?php if ($vendedor['inmobiliaria']['id'] == 0) : ?>
			<p class="col-md-6 col-lg-12"><a href='usuario.php?id=<?php echo $inmueble["usuario"]; ?>' class="otrosBotones btn-block btn-lg btn btn-inmueble" data-label="inmueble_contacto">Contactar al anunciante</a></p>
		<?php else : ?>
			<p class="col-md-6 col-lg-12"><a href='inmobiliaria.php?id=<?php echo $vendedor['inmobiliaria']['id']; ?>' class="otrosBotones btn-block btn-lg btn btn-inmueble" data-label="inmueble_contacto">Contactar al anunciante</a></p>
		<?php endif; ?>

		<p class="hidden-xs col-md-6 col-lg-12"><a class="otrosBotones btn btn-block btn-lg btn-inmueble" onclick="window.print();">Guardar en PDF</a></p>
		<br><br>

		<p class="descripcionBotones inmueble-btn-secundario hidden-xs">
			<a class="btnBotones estrella" data-id="<?php echo $inmueble["like"]; ?>" data-inmueble="<?php echo $inmueble["id"]; ?>">
				<i class="fa <?php echo $inmueble["like"] > 0 ? "fa-star" : "fa-star-o"; ?>"></i>
				<span class="estrellaTexto"><?php echo $inmueble["like"] > 0 ? "Quitar de " : "Agregar a "; ?>Favoritos</span>
			</a>
		</p>
		<p class="descripcionBotones inmueble-btn-secundario hidden-xs"> <a class="otrosBotones" href="javascript:inmueble_botonesCompartir();"> <i class="fa fa-share-alt"></i> Compartir este anuncio</a></p>
		<div id="inmueble_botonesCompartir" class="contenedorCompartir">
			<a class="template_btnsShare facebook" data-img="<?php echo $inmueble["imagenes"][0]; ?>" data-url="inmueble.php?id=<?php echo $inmueble["id"]; ?>">Facebook</a>
			<a class="template_btnsShare twitter" data-url="inmueble.php?id=<?php echo $inmueble["id"]; ?>" data-titulo="<?php echo $inmueble["titulo"]; ?>">Twitter</a>
			<a class="template_btnsShare email" data-url="inmueble.php?id=<?php echo $inmueble["id"]; ?>" onclick="inmueble_compartir_email();">Email</a>
		</div>
		<p class="descripcionBotones inmueble-btn-secundario hidden-xs">
			<a class="otrosBotones" href="javascript:inmueble_reportarAnuncio();"> <i class="fa fa-ban"></i> Reportar este anuncio</a>
			<div id="inmueble_reportarAnuncio" class="reportarAnuncio" style="display:none">
			    <ul id="reportar_motivo" class="template_campos">
			        Motivo<span></span>
			        <li class="lista">
			            <ul><li data-value="3">Datos de Contacto Incorrectos</li><li data-value="2">Dirección Incorrecta</li><li data-value="5">Inmueble Duplicado</li><li data-value="6">Inmueble Fraudulento</li><li data-value="1">Inmueble Vendido</li><li data-value="4">Publicación Inmoral</li></ul>
			        </li>
			        <p data-value="-1"></p>
			        <input type="text" value="" style="position:absolute; top:0px; left:0px; z-index:-1;">
			    </ul>
			    <span class="btnEnviar" onclick="inmueble_validarReporte();" data-inmueble="255">Enviar</span>
			</div>
		</p>
		<?php
		if ($usuario != -1) {
			if ($usuario == $inmueble["usuario"]) {
				echo "<p class=\"descripcionBotones inmueble-btn-secundario\">
						<a href='javascript:gotoURLPOST(\"nuevoAnuncio.php\", {edit: 1, id: ".$inmueble["id"]."});' class='otrosBotones'> <i class='fa fa-pencil'></i> Editar este anuncio</a>
					</p>";
			}
			else {//si pertenece a uno de los usuarios de la inmobiliaria; y el usuario actual es el admin
				if (in_array($inmueble["usuario"], $arrayUsuarios)) {
					echo "<p class=\"descripcionBotones inmueble-btn-secundario\">
					<a href='javascript:gotoURLPOST(\"nuevoAnuncio.php\", {edit: 1, id: ".$inmueble["id"]."});' class='otrosBotones'><i class='fa fa-pencil'></i> Editar este anuncio</a>
					</p>";
				}
			}
		}
		?>

		<div class="destacados hidden-xs">
			<div class="row-eq-height header">
				<h2 class="col-lg-12">Propiedades Destacadas</h2>

			</div>
			<div class="row body">
				<?php

				$inmuebles = getDestacados();
				foreach ($inmuebles as $inmueble):
					?>
					<div class="item col-lg-12 col-md-3 col-sm-6" onclick="catalogo_redirecciona_regresar('<?php echo $inmueble['url'];?>');">
						<img src="<?php echo $inmueble["imagenes"][0]; ?>" alt="<?php echo $inmueble["titulo"]; ?>" class="img-responsive">
						<div class="info">
							<h3><?php echo $inmueble["titulo"]; ?></h3>
                    <span> <?php echo (!empty($inmueble['recamaras']))?$inmueble['recamaras']." Rec&aacute;maras":"";
						echo (!empty($inmueble['dimensionTotal']))?' | '.$inmueble['dimensionTotal']. 'm<sup>2</sup>':'';?></span>
							<p>$ <?php echo number_format($inmueble["precio"], 0, ".", ","); ?> MXN</p>
						</div>
					</div>
				<?php endforeach; ?>

			</div>
		</div>
		<div class="container mobile-only">
			<a href="#" class="btn btn-inmueble btn-lg btn-block" onclick="window.history.back();" >Regresar a Propiedades</a>
		</div>
	</aside>
</div>

<?php
getFooter();
PopUpGenerales("inmueble_cerrarPopup");
?>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<?php
if (isset($_GET["create"])) {
	echo
	"<div id='inmueble_exitoNuevoAnuncio' class='templatePopUp inmueble_exitoNuevoAnuncio'>
						<span class='btnCerrar' onclick='template_principalCerrarPopUp(inmueble_cerrarPopup);'>x</span>
						<table>
							<tbody>
								<tr>
									<td>El anuncio se creó exitosamente</td>
								</tr>
							</tbody>
						</table>
					</div>";
}
?>
	<div id="inmueble_mascara2" class="template_mascaraPrincipal inmueble_mascara2" onclick="template_principalCerrarPopUp(inmueble_cerrarPopup);"></div>
	<div id="inmueble_mostrarImagen" class="templatePopUp inmueble_mostrarImagen">
		<span class="btnCerrar" onclick="template_principalCerrarPopUp(inmueble_cerrarPopup);">x</span>
		<table>
			<tbody>
			<tr>
				<td>
					<img src="" alt="Imágen" data-pos="0" />
					<a class="flechas prev">Prev</a>
					<a class="flechas next">Next</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div id="inmueble_compartir_email" class="templatePopUp inmueble_compartir_email">
		<span class="btnCerrar" onclick="template_principalCerrarPopUp(inmueble_cerrarPopup);">x</span>
		<table>
			<tbody>
			<tr>
				<td>
					<h1>Compartir a un amigo</h1>
					<p>Llena el formulario para recomendar esta propiedad a un amigo</p><br />
					<p><input type="text" id="inmueble_compartir_tuNombre" class="template_campos" placeholder="Tu Nombre" data-value="<?php echo $usuario != -1 ? $usuarioActual["nombre"] : ""; ?>" /></p>
					<p><input type="text" id="inmueble_compartir_tuEmail" class="template_campos" placeholder="Tu E-mail" data-value="<?php echo $usuario != -1 ? $usuarioActual["email"] : ""; ?>" /></p>
					<p><input type="text" id="inmueble_compartir_amigoEmail" class="template_campos" placeholder="E-mail de tu amigo" /></p>
					<p><textarea id="inmueble_compartir_mensaje" class="template_campos" placeholder="Mensaje"></textarea></p>
					<p style="text-align:right; padding-top:10px;"><span class="btnEnviar" onclick="inmueble_validar_compartirEmail();">Enviar</span></p>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
<?php

	FinHTML();
?>
