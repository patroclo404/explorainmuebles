<?php
error_reporting(false);
ini_set('display_errors', false);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set("America/Mexico_City");


if (!session_id()) {
    session_start();
}


/*********************************************************************************************
 * define nombre de constantes para las sessiones
 *********************************************************************************************/
define("cerrarSession", "cerrarSesionInmueble");

define("userId", "userInmuebleId");
define("userNombre", "userInmuebleNombre");
define("userImagen", "userInmuebleImagen");
define("userInmobiliaria", "userInmuebleInmobiliaria");
define("userAdminInmobiliaria", "userInmuebleAdministrador");
define("userCarrito", "userInmuebleCarrito");
define("userFiltros", "userInmuebleFiltros");

define("adminId", "adminInmuebleId");
define("adminLogin", "adminInmuebleLogin");
define("adminTipo", "adminInmuebleTipo");

$included_files = get_included_files();
$files = array();

$mapa=$_GET['mapa'];
if ($mapa == 1) {
    echo "<script> index_mostrarMapa(1); </script>";
}

foreach ($included_files as $filename) {
    $files[]= basename($filename);
}

//updFiltros

if(!in_array('catalogo.php', $files) &&
    !in_array('consInmueble.php', $files) &&
    !in_array('updFiltros.php', $files)){

    $_SESSION[userFiltros] = array();

}

if (in_array('catalogo.php', $files) && ($_GET["transaccion"] != $_SESSION[userFiltros]['transaccion'])){
    $_SESSION[userFiltros] = array();
}
/*
    Convierte una fecha con formato normal (dd/mm/aaaa), en uno de formato sql (aaaa/mm/dd)

        * date:	String, fecha con formato normal
*/
function getDateSQL($date)
{
    if ($date) {
        $arr = explode("/", $date);
        if (sizeof($arr) == 1)
            $arr = explode("-", $date);
        $date = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
    }

    return $date;
}


/*
    Convierte una fecha con formato sql (aaaa/mm/dd), en uno de formato normal (dd/mm/aaaa)

        * date:	String, fecha con formato sql
*/
function getDateNormal($date)
{
    if ($date) {
        $arr = explode("-", $date);
        if (sizeof($arr) == 1)
            $arr = explode("/", $date);
        $date = $arr[2] . "/" . $arr[1] . "/" . $arr[0];
    }

    return $date;
}


/*
    Crea la conexion con mysql_pdo
*/
function crearConexionPDO()
{
    /*$db = "inmueble_inmuebledb";
    $user = "root";
    $pass = "";
    $host = "localhost";*/

    /*$db = "explora2";
    $user = "root";
    $pass = "";
    $host = "localhost";*/

    $db = "inmueble_inmuebledb";
    $user = "explorainmuebles";/*inmueble_root*//*explorainmuebles*/
    $pass = "ExploraCasas2015";/*hsNdfcyh654ON*//*Casas2015*/
    $host = "mysql.explorainmuebles.com";/*localhost*//*mysql.explorainmuebles.com*/

        $db = "explora2";
    $user = "webstylemx";/*inmueble_root*//*explorainmuebles*/
    $pass = "Diego64.";/*hsNdfcyh654ON*//*Casas2015*/
    $host = "mysql.webstyle.mx";/*localhost*//*mysql.explorainmuebles.com*/

    $conexion = new PDO("mysql:host=" . $host . "; dbname=" . $db . "; charset=utf8", $user, $pass);
    return $conexion;
}


/*
    limpia todas las sessiones (login de usuario)
*/
function limpiarSesiones()
{
    //inicializa la session
    session_name();
    session_start();

    switch ($_SESSION[cerrarSession]) {
        case 0://super administrador
        case 1://administrador
            unset($_SESSION[adminId]);
            unset($_SESSION[adminLogin]);
            unset($_SESSION[adminTipo]);
            break;
        case 2://usuario
            unset($_SESSION[userId]);
            unset($_SESSION[userNombre]);
            unset($_SESSION[userImagen]);
            unset($_SESSION[userInmobiliaria]);
            unset($_SESSION[userAdminInmobiliaria]);
            unset($_SESSION[userCarrito]);
            break;
    }

    unset($_SESSION[cerrarSession]);
}


/*
    Obtengo la extension de un archivo; con o sin el punto de la extension.

        * nombreArchivo:	String, es el nombre del archivo el cual se obtendra el punto de la extension
        * conExtension:		[Boolean], si esta activo devuelve el punto de la extension; en caso de false
                            no devuelve el punto. Por default esta en false
*/
function  template_extension($nombreArchivo, $conExtension = false)
{
    $ext = substr($nombreArchivo, strrpos($nombreArchivo, '.'));// Extension natural
    $ext = strtolower($ext);
    if (!$conExtension) { // sin punto...
        $ext = substr($ext, 1);
        if (strlen($ext) >= 4)
            return "jpg";
    }
    if (strlen($ext) >= 5)
        $ext = ".jpg";

    return $ext;
}


/*
    Funcion que recibe el nombre del input (_FILES['nombre']) y que lo guarda en la base de datos, en la ruta especificada.
    Tambien puede borrar la imagen anteriormente; con motivos de actualizacion y liberacion de recursos (disco duro en
    el servidor).
    Nota:	Si se envia el nombre de un archivo a eliminar; pero no existe la imagen a reemplazar; no eliminara la imagen
            y ademas el nombre de archivo que devolvera, sera el del archivo que se queria eliminar anteriormente.

        * nombreInput:			String, es el nombre del input (atributo name), el cual lo toma para obtener la imagen y guardarla
        * rutaImagen:			String, es la ruta donde se almacenara las imagenes
        * nombreArchivoBorrar:	[String], es el nombre del archivo a borrar anteriormente; para luego actualizar el nuevo archivo.
                                Por default esta vacio (no se elimina ningun archivo anteriormente).
*/
function template_subirImagenesServidor($nombreInput, $rutaImagen, $nombreArchivoBorrar = "")
{
    $isExito = 1;
    $mensaje = "Los datos se han actualizado de manera correcta.";
    $newNombreImagen = "";
    $_maxSize = template_return_bytes(ini_get('upload_max_filesize'));//maximo tamaño permitido (8MB = 8 * 1024 * 1024)


    if (isset($_FILES[$nombreInput])) {
        if ($_FILES[$nombreInput]["name"] != "") {//se recibe nueva imagen
            if ($_FILES[$nombreInput]["size"] < $_maxSize) {
                $fileArchivo = $_FILES[$nombreInput]["tmp_name"];
                $fileTamanio = $_FILES[$nombreInput]["size"];
                $fileTipo = $_FILES[$nombreInput]["type"];

                $fp = fopen($fileArchivo, "r");
                $contenido = fread($fp, $fileTamanio);
                $contenido = addslashes($contenido);

                $nombreImagen = basename($_FILES[$nombreInput]["name"]);
                $extension = template_extension($nombreImagen);
                $newNombreImagen = str_replace(array(" ", "."), "", microtime());
                $newNombreImagen .= "." . $extension;
                $newNombreImagen = strtolower($newNombreImagen);

                move_uploaded_file($fileArchivo, $rutaImagen . $newNombreImagen);
                fclose($fp);

                if ($nombreArchivoBorrar != "") {//compruebo k exista la imagen anterior; en caso de ser asi; elimino la imagen anterior
                    if (file_exists($rutaImagen . $nombreArchivoBorrar))
                        unlink($rutaImagen . $nombreArchivoBorrar);
                }
            } else {
                $isExito = 0;
                $mensaje = "El archivo excede el tamaño permitido.";
                $newNombreImagen = $nombreArchivoBorrar;
            }
        } else {
            $isExito = 0;
            $mensaje = "No se recibio ninguna imagen.";
            $newNombreImagen = $nombreArchivoBorrar;
        }
    } else {
        $isExito = 0;
        $mensaje = "No se recibio ninguna imagen.";
        $newNombreImagen = $nombreArchivoBorrar;
    }


    $arrayRespuesta = array(
        "isExito" => $isExito,
        "mensaje" => $mensaje,
        "imagen" => $newNombreImagen
    );

    return json_encode($arrayRespuesta);
}


/*
    Devuelve el valor de K, M, G bytes a bytes
*/
function template_return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last) {
        // El modificador 'G' está disponble desde PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}


/*
    Genera una cadena aleatoria de la cantidad de caracteres recibida por parametros

        * longitud:	Integer, es la cantidad de caracteres a generar para la cadena aleatoria
*/
function template_generarCadenaAleatoria($longitud)
{
    $cadenaString = "abcdefghijklmnopqrstuvwxyZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $cadenaFinal = "";

    for ($x = 0; $x < $longitud; $x++) {
        $key = rand(0, strlen($cadenaString) - 1);
        $cadenaFinal .= $cadenaString{$key};
    }

    return $cadenaFinal;
}

/*
    Corta una cadena mayoral número de caracteres deseados y le agrega tres puentos suspensivos 

        * cadena:	cadena, Es la cadena original
 * 		* longitud:	Integer, es la cantidad de caracteres máximos requeridos
*/
function template_cortarCadena($cadena,$longitud)
{
    $cadenaFinal = "";
	
	if( (strlen ($cadena)) > $longitud ) {
        $cadenaFinal =  substr( $cadena, 0, $longitud );
		$cadenaFinal .=  "..." ;
		return $cadenaFinal;
    }
	return $cadena;
}

/*
    Cambiar vacio por un "-" 

        * cadena:	cadena, Es la cadena original
*/
function template_cambiarVacio($cadena)
{
    $cadenaFinal = "";
	
	if( (strlen ($cadena)) == "" ) {
        return "-";
    }
    return $cadena;
}


/*
    Devuelve un arreglo que contiene, el valor de la calificacion total, su parte entera y su parte flotante redondeado en multiplos de 25

        * suma:	Integer, es el valor de la suma de los votos para el inmueble/usuario
        * cont:	Integer, es el total de usuarios considerados para la suma de los votos
*/
function template_getValorVotacion($suma, $cont)
{
    $calif = ($suma * 5) / ((5 * $cont) > 0 ? (5 * $cont) : 1);
    $entero = intval($calif);
    $flotante = 0;


    if (strpos($calif, ".")) {
        $partes = explode(".", (string)$calif);
        $flotante = substr($partes[1], 0, 2);
        $flotante = strlen($flotante) == 1 ? ($flotante * 10) : $flotante;
    }

    if ($flotante > 0) {
        if ($flotante >= 88) {
            $flotante = 0;
            $entero++;
        } else if ($flotante >= 63)
            $flotante = 75;
        else if ($flotante >= 38)
            $flotante = 50;
        else if ($flotante >= 13)
            $flotante = 25;
        else
            $flotante = 0;
    }


    $arrayRespuesta = array(
        "total" => $calif,
        "entero" => $entero,
        "flotante" => $flotante
    );

    return json_encode($arrayRespuesta);
}


/*********************************************************************************************
 * funciones que imprimen el cuerpo general en todas las interfaces
 *********************************************************************************************/

/*
    Muestra las cabeceras de html de todas las interfaces.
    En todas las cabeceras no es necesario cargar los siguientes archivos:

        template.css, jQuery.js, template.js, validaciones.js

    Los valores recibidos por parametros son:

        * cssJs: 			[String], todos los estilos que se vayan agregando, deben ser separados por comas junto con
                            los archivos de script. Solo se pondran los nombres (sin ruta) de los archivos. Todos los
                            archivos de estilos van en /css y los de script en /js
        * varJs:			[String], son todas las variables que necesitamos inicializar desde el php. Se pueden recibir
                            varias variables con sus valores, separados con comas "," y debe ir el nombre de la variable
                            seguido del signo igual "=" y enseguida el valor a asignarle.
                            Ejemplo: cadena='hola',numero=8
        * paramsMetasFB:	[Array String], es un arreglo de metas para facebook, keys del array:
                                - titulo:		titulo para facebook
                                - imagen:		imagen para facebook
                                - descripcion:	descripcion para facebook
        * paramsMetasPage:	[Array String], es un arreglo de metas para la pagina web, keys del array:
                                - keywords:		palabras clave
                                - descripcion:	descripcion de la pagina o interface

*/
function CabeceraHTML($cssJs = NULL, $varJs = NULL, $paramsMetasFB = NULL, $paramsMetasPage = NULL)
{
    echo
    "<!DOCTYPE>
			<html xmlns='http://www.w3.org/1999/xhtml'>
			<head>";

    echo "<base href='http://www.explorainmuebles.com/'>";
    //"<base href='http://explora.zero-oneit.com/'>";/*http://www.explorainmuebles.com/*//*http://localhost/inmuebles/*/


    echo
    "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";


    if ($paramsMetasFB != NULL) {
        echo
            "<meta property='og:type' content='article' />
				<meta property='og:site_name' content='Explora Inmuebles' />
				<meta property='fb:app_id' content='" . $GLOBALS["facebook"]->getAppID() . "' />
				<meta property='og:url' content='" . (isset($paramsMetasFB["url"]) ? ("/" . $paramsMetasFB["url"]) : "") . "'/>";
        if (isset($paramsMetasFB["titulo"]))
            echo "<meta property='og:title' content='" . $paramsMetasFB["titulo"] . "' />";
        if (isset($paramsMetasFB["imagen"]))
            echo "<meta property='og:image' content='http://www.explorainmuebles.com/" . $paramsMetasFB["imagen"] . "' />";
        if (isset($paramsMetasFB["descripcion"]))
            echo "<meta property='og:description' content='" . $paramsMetasFB["descripcion"] . "' />";
    }

    if ($paramsMetasPage != NULL) {
        if (isset($paramsMetasPage["keywords"]))
            echo "<meta name='keywords' content='" . $paramsMetasPage["keywords"] . "' />";
        if (isset($paramsMetasPage["descripcion"]))
            echo "<meta name='description' content='" . $paramsMetasPage["descripcion"] . "' />";
    }


    echo
    "<title>Venta y Renta de Casas, Departamentos, Terrenos y más</title>
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
			<link rel='icon' type='image/png' href='images/logoIcon.png' />
			<link rel='stylesheet' type='text/css' href='css/bootstrap.min.css' />
			<link rel='stylesheet' type='text/css' href='css/font-awesome.min.css' />
			<link rel='stylesheet' type='text/css' href='css/flaticon.css' />
			<link rel='stylesheet' type='text/css' href='css/picker.css' />
			<link rel='stylesheet' type='text/css' href='css/pickerDate.css' />
			<link rel='stylesheet' type='text/css' href='css/pickerTime.css' />
			<link rel='stylesheet' type='text/css' href='css/template-reset.css' />
            <link href='https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900' rel='stylesheet'>

            <style type='text/css'>
                @-moz-document url-prefix() {
                    .item.signup.login .drop-down {
                        margin-top: 40px;
                    }
                    .item.signup.register .drop-down{
                       margin-top: 40px; 
                    }
                    .item.signup .drop-down{
                        margin-top: 40px;
                    }
                    .item.signup.data-user .drop-down{
                        margin-left: -231px;
                    }
                }
            </style>

            <script language='javascript' type='text/javascript' src='js/validaciones.js'></script>
			<script language='javascript' type='text/javascript' src='js/jQuery.js'></script>
			<script language='javascript' type='text/javascript' src='js/jQueryForm.js'></script>
			<script language='javascript' type='text/javascript' src='js/md5Script.js'></script>
			<script language='javascript' type='text/javascript' src='js/picker.js'></script>
			<script language='javascript' type='text/javascript' src='js/pickerDate.js'></script>
			<script language='javascript' type='text/javascript' src='js/pickerTime.js'></script>
			<script language='javascript' type='text/javascript' src='js/translations/es_ES.js'></script>
			<script language='javascript' type='text/javascript' src='js/template_ver39.js'></script>
			<script language='javascript' type='text/javascript' src='js/cycle2.js'></script>
			<script language=\"javascript\" type=\"text/javascript\" src=\"js/bootstrap.min.js\"></script>
			<script>
				window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src='https://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,'script','twitter-wjs'));
			</script>";


    echo
    "<script type='text/javascript'> try{";

    if ($varJs != NULL) {
        $partes = explode(",", $varJs);
        for ($x = 0; $x < count($partes); $x++) {
            $variables = explode("=", $partes[$x]);

            echo
                "var " . $variables[0] . " = '" . $variables[1] . "';";
        }
    }

    echo
    "}catch(e){}</script>
					<script>
						(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

						ga('create', 'UA-57313024-5', 'auto');
						ga('send', 'pageview');
					</script>
				</head>";

    if ($cssJs != NULL) {
        $archivos = explode(",", $cssJs);
        for ($x = 0; $x < count($archivos); $x++) {
            $extencion = explode(".", $archivos[$x]);
            switch ($extencion[1]) {
                case "css":
                    echo "<link rel='stylesheet' type='text/css' href='css/" . $archivos[$x] . "' />";
                    break;
                case "js":
                    echo "<script type='text/javascript' src='js/" . $archivos[$x] . "'></script>";
                    break;
            }
        }
    }

}

function bodyIndex(){
    $arrayTipoInmueble = array();
    $arrayEstado = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }

    echo "
			<body>
                <script>
                    window.fbAsyncInit = function() {
                            FB.init({
                            appId      : '" . $GLOBALS["facebook"]->getAppID() . "',
                            xfbml      : true,
                            version    : 'v2.2'
                        });
                    };

                    (function(d, s, id){
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) {return;}
                        js = d.createElement(s); js.id = id;
                        js.src = '//connect.facebook.net/en_US/sdk.js';
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                </script>
                <div class='template_principal'>
                <header class='hidden-print'>
                    <div class='container'>";
if(!isset($_SESSION[userId])) {
    echo "<div class='item signup' onclick='gotoURL(\"registro.php\");'>";

}else{
    echo "<div class='item signup' onclick='gotoURL(\"nuevoAnuncio.php\");'>";
}

echo                            "<span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span> Publica tu anuncio
                        </div>";

    if(!isset($_SESSION[userId])) {
        echo "
                        <div class='item signup register'>
                            <span>Reg&iacute;strate</span>
                            <div class='drop-down'>
                                <p class='arregloBorder'></p>
								<p class='titulo'>Ingresa tus Datos</p>
								<input type='text' id='template_count_FBId' style='display:none;' />
								<input type='text' id='template_count_nombre' class='template_campos' placeholder='Nombre' />
								<input type='text' id='template_count_email' class='template_campos' placeholder='Correo Electrónico' />
								<input type='password' id='template_count_password' class='template_campos' placeholder='Contraseña' />
								<input type='password' id='template_count_confPassword' class='template_campos' placeholder='Confirmar Contraseña' />
								<p class='btn'><span onclick='template_validaCampos_count();'><a class='paloma'></a>Registrarme</span></p>
								<p class='titulo'>Regístrate con Facebook</p>
								<p class='btn'><span onclick='template_validaCampos_countFB();'><a class='btnBotones facebook'>Facebook</a>Registrarme</span></p>
								<p class='terminosCondiciones'><input type='checkbox' id='template_count_check' />He leído y acepto los <a href='terminosCondiciones.php' target='_blank'>Términos y Condiciones</a></p>
                            </div>
                        </div>
                        <div class='item signup login'>
                            <span>Inicia Sesi&oacute;n</span>
                            <div class='drop-down'>
                                <div class='opcionLogin'>
									<p class='arregloBorder'></p>
                  <p class='titulo'>Iniciar Sesi&oacute;n</p>
									<input type='text' id='template_login_FBId' style='display:none;' />
									<input type='text' id='template_login_nombre' style='display:none;' />
									<input type='text' id='template_login_email' class='template_campos' placeholder='Correo Electrónico' />
									<input type='password' id='template_login_pass' class='template_campos' placeholder='Contraseña' />
									<p class='btn btn-inmueble'><span onclick='template_validaCampos_login();'><a class='paloma'></a>Entrar</span></p>
									<p class='btn'><span style='float:left; font-size:10px; padding-top:3px;' onclick='gotoURL(\"solicitudRestablecer.php\");'>¿Olvidaste tu contraseña?</span></p>
									<p class='btn'><span onclick='template_validaCampos_loginFB();'><a class='btnBotones facebook'>Facebook</a>Entrar con Facebook</span></p>
							    </div>
                            </div>
                        </div>";
    }else{
        echo "
            <div class='item signup data-user'><div class='user-wrapper'>".
            (isset($_SESSION[userImagen]) ? "<img src='" . $_SESSION[userImagen] . "' class='imagenLogin' />" : "<a class='btnBotones userSinFoto'>Sin Foto</a>")."
                            <span>".$_SESSION[userNombre]."</span></div>
                            <div class='drop-down'>
                                <a href='perfil.php' style='text-decoration:none;'><p class='titulo'>Mi Perfil</p></a>
							    <a href='lib_php/cerrarSession.php' style='text-decoration:none;'><p class='titulo'>Cerrar Sesión</p></a>
                            </div>
                        </div>
        ";
    }

    echo "
                        <div class='item' onclick='en_mapa()'>
                            Buscar en Mapa
                        </div>
                        <div class='item'>
                            <a href='http://www.explorainmuebles.com/renta-vacacional/todos-los-tipos/todo-mexico/todas-las-ciudades' >Renta Vacacional</a>
                        </div>
                        <div class='item'>
                            <a href='http://www.explorainmuebles.com/renta/todos-los-tipos/todo-mexico/todas-las-ciudades' >Propiedades en Renta</a>

                        </div>
                        <div class='item'>
                            <a href='http://www.explorainmuebles.com/venta/todos-los-tipos/todo-mexico/todas-las-ciudades' >Propiedades en Venta</a>

                        </div>

                        <div class='item'>
                            <a href='http://www.explorainmuebles.com/' >Inicio</a>

                        </div>
                        <div class='item logo'>
                            <a href='/'><img src='images/logo.png' class='logo img-responsive' alt='Explora Inmuebles' /></a>
                        </div>
                        <div class='item mobile-menu'>
                            <i class='fa fa-bars'></i>

                        </div>
                    </div>

                </header>
                <div class='responsive-menu'>
                    <a href='http://www.explorainmuebles.com/' ><div><div class='icon-responsive-menu' id='opcion-menu-home'></div>Inicio</div></a>
                    <a href='http://www.explorainmuebles.com/venta/todos-los-tipos/todo-mexico/todas-las-ciudades' ><div><div class='icon-responsive-menu' id='opcion-menu-venta'></div>Propiedades en Venta</div></a>
                    <a href='http://www.explorainmuebles.com/renta/todos-los-tipos/todo-mexico/todas-las-ciudades' ><div><div class='icon-responsive-menu' id='opcion-menu-renta'></div>Propiedades en Renta</div></a>
                    <a href='http://www.explorainmuebles.com/renta-vacacional/todos-los-tipos/todo-mexico/todas-las-ciudades' ><div><div class='icon-responsive-menu' id='opcion-menu-vacacional'></div>Renta Vacacional</div></a>";

                    if (isset($_SESSION[userImagen])) {
                        echo "<a href='http://www.explorainmuebles.com/lib_php/cerrarSession.php' style='text-decoration:none;'><p class='titulo'>Cerrar Sesión</p></a>";
                    }else{
                        echo "<a href='#' onclick='gotoURL(\"registro.php\");'><div><div class='icon-responsive-menu' id='opcion-menu-login'></div>Iniciar Sesion</div></a>";
                    }

          echo "</div>
                <div class='template_contenedorCuerpo'>
				    <div class='template_cuerpo'>
						<!-- <div class='lineaIzq'></div> -->
    ";
    echo "<script>
        function en_mapa(){
            window.location='http://www.explorainmuebles.com/?mapa=1';
        }
        
        </script>
    ";
}

function CuerpoHTML(){
    return bodyIndex();
}
/*
    Muestra el cuerpo que es igual en todas las interfaces.
*/
function CuerpoHTMLBack()
{
    $arrayTipoInmueble = array();
    $arrayEstado = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }

    echo
        "<body>
					<script>
						window.fbAsyncInit = function() {
							FB.init({
								appId      : '" . $GLOBALS["facebook"]->getAppID() . "',
								xfbml      : true,
								version    : 'v2.2'
							});
						};

						(function(d, s, id){
							var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) {return;}
							js = d.createElement(s); js.id = id;
							js.src = '//connect.facebook.net/en_US/sdk.js';
							fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
					</script>
					<div class='template_principal'>
						<div class='template_cabecera'>
							<div class='template_cuerpo'>
								<div class='contenedorLogin'>
									<table>
										<tbody>
											<tr>
												<td><span class='barra'></span></td>";

    if (!isset($_SESSION[userId])) {
        echo
            "<td class='texto'>
													<span class='texto'>Iniciar Sesión</span>
													<div class='opcionLogin'>
														<p class='arregloBorder'></p>
														<input type='text' id='template_login_FBId' style='display:none;' />
														<input type='text' id='template_login_nombre' style='display:none;' />
														<input type='text' id='template_login_email' class='template_campos' placeholder='Correo Electrónico' />
														<input type='password' id='template_login_pass' class='template_campos' placeholder='Contraseña' />
														<p class='btn'><span style='float:left; font-size:10px; padding-top:3px;' onclick='gotoURL(\"solicitudRestablecer.php\");'>¿Olvidaste tu contraseña?</span><span onclick='template_validaCampos_login();'><a class='paloma'></a>Entrar</span></p>
														<p class='btn'><span onclick='template_validaCampos_loginFB();'><a class='btnBotones facebook'>Facebook</a>Entrar con Facebook</span></p>
													</div>
												</td>
												<td><span class='barra'></span></td>
												<td class='texto'>
													<span class='texto'>Registrarse</span>" .
            "<div class='opcionLogin'>
														<p class='arregloBorder'></p>
														<p class='titulo'>Ingresa tus Datos</p>
														<input type='text' id='template_count_FBId' style='display:none;' />
														<input type='text' id='template_count_nombre' class='template_campos' placeholder='Nombre' />
														<input type='text' id='template_count_email' class='template_campos' placeholder='Correo Electrónico' />
														<input type='password' id='template_count_password' class='template_campos' placeholder='Contraseña' />
														<input type='password' id='template_count_confPassword' class='template_campos' placeholder='Confirmar Contraseña' />
														<p class='btn'><span onclick='template_validaCampos_count();'><a class='paloma'></a>Registrarme</span></p>
														<p class='titulo'>Regístrate con Facebook</p>
														<p class='btn'><span onclick='template_validaCampos_countFB();'><a class='btnBotones facebook'>Facebook</a>Registrarme</span></p>
														<p class='terminosCondiciones'><input type='checkbox' id='template_count_check' />He leído y acepto los <a href='terminosCondiciones.php' target='_blank'>Términos y Condiciones</a></p>
													</div>
												</td>";
    } else {
        echo
            "<td class='texto'>" .
            (isset($_SESSION[userImagen]) ? "<img src='" . $_SESSION[userImagen] . "' class='imagenLogin' />" : "<a class='btnBotones userSinFoto'>Sin Foto</a>") .
            "<span class='texto'>" . $_SESSION[userNombre] . "</span>
													<div class='opcionLogin'>
														<p class='arregloBorder'></p>
														<a href='perfil.php' style='text-decoration:none;'><p class='titulo'>Mi Perfil</p></a>
														<a href='lib_php/cerrarSession.php' style='text-decoration:none;'><p class='titulo'>Cerrar Sesión</p></a>
													</div>
												</td>";
    }

    echo
    "<td><span class='barra'></span></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class='contenedorMenu2'>
									<a href='index.php'><img src='images/logo.png' class='logo' alt='Explora Inmuebles' /></a>
									<div class='wrapperMenu2'>
										<table>
											<tbody>
												<tr>
													<td class='btn'><span class='texto'><a class='btnBotones buscador' style='cursor:default;'>Buscar</a></span></td>
													<td><span class='barra'></span></td>
													<td class='texto'>
														<span class='texto'>Venta</span>
														<div class='opcionRenta'>
															<p class='arregloBorder'></p>
															<ul id='template_venta_tipoInmueble' class='template_campos' style='width:150px;'>
																Tipo de inmueble<span></span>
																<li class='lista'>
																	<ul>";

    for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
        echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "'>" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
																</li>
																<p data-value='-1'>Tipo de Inmueble</p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><ul id='template_venta_estado' class='template_campos' style='width:120px;'>
																Estado<span></span>
																<li class='lista'>
																	<ul>";


    for ($x = 0; $x < count($arrayEstado); $x++) {
        echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><ul id='template_venta_municipio' class='template_campos' style='width:150px;'>
																Municipio<span></span>
																<li class='lista'>
																	<ul></ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><br />
															<ul id='template_venta_precio' class='template_campos' style='width:250px;'>
																Precio<span></span>
																<li class='lista'>
																	<ul>";

    $incre = 1000000;
    for ($x = 0; $x < 5000000; $x += $incre) {
        echo "<li data-value='" . $x . "-" . ($x + $incre) . "'>$" . number_format($x, 0, ".", ",") . " - $" . number_format(($x + $incre), 0, ".", ",") . "</li>";
    }


    echo
        "<li data-value='5000000-7000000'>$5,000,000 - $7,000,000</li>
																		<li data-value='7000000-9000000'>$7,000,000 - $9,000,000</li>
																		<li data-value='9000000-1000000000'>Más de $9,000,000</li>
																	</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul>" .
        "<a class='btnBotones mundo' data-transaccion='2' style='margin-right:10px; margin-top:27px;'>Explorar</a>" .
        "<input type='text' id='template_venta_codigo' class='template_campos' placeholder='Código' style='width:150px; margin-top:25px;' /><br />
															<p class='textBuscar' style='float:left;'>
																<a class='btnBotones mundo' data-transaccion='2' style='margin-right:10px;'>Explorar</a><span style='font-family:inherit; color:inherit;' onclick='$(this).parent().find(\"a.mundo\").click();'>Buscar con Mapa</span>
															</p><p class='textBuscar' onclick='template_buscar_transaccion(2);'>
																<a class='btnBotones buscador' style='margin-right:5px;'>Buscar</a>Buscar
															</p>
														</div>
													</td>
													<td><span class='barra'></span></td>
													<td class='texto'>
														<span class='texto'>Renta</span>
														<div class='opcionRenta'>
															<p class='arregloBorder'></p>
															<ul id='template_renta_tipoInmueble' class='template_campos' style='width:150px;'>
																Tipo de inmueble<span></span>
																<li class='lista'>
																	<ul>";


    for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
        echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "'>" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><ul  id='template_renta_estado' class='template_campos' style='width:120px;'>
																Estado<span></span>
																<li class='lista'>
																	<ul>";


    for ($x = 0; $x < count($arrayEstado); $x++) {
        echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><ul id='template_renta_municipio' class='template_campos' style='width:150px;'>
																Municipio<span></span>
																<li class='lista'>
																	<ul></ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><br />
															<ul id='template_renta_precio' class='template_campos' style='width:250px;'>
																Precio<span></span>
																<li class='lista'>
																	<ul>";


    $incre = 2500;

    for ($x = 0; $x < 5000; $x += $incre) {
        echo "<li data-value='" . $x . "-" . ($x + $incre) . "'>$" . number_format($x, 0, ".", ",") . " - $" . number_format(($x + $incre), 0, ".", ",") . "</li>";
    }

    $incre = 5000;

    for ($x = 5000; $x < 30000; $x += $incre) {
        echo "<li data-value='" . $x . "-" . ($x + $incre) . "'>$" . number_format($x, 0, ".", ",") . " - $" . number_format(($x + $incre), 0, ".", ",") . "</li>";
    }


    echo
        "<li data-value='30000-40000'>$30,000 - $40,000</li>
																		<li data-value='40000-50000'>$40,000 - $50,000</li>
																		<li data-value='50000-1000000000'>Más de $50,000</li>
																	</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul>" .
        "<a class='btnBotones mundo' data-transaccion='1' style='margin-right:10px; margin-top:27px;'>Explorar</a>" .
        "<input type='text' id='template_renta_codigo' class='template_campos' placeholder='Código' style='width:150px; margin-top:25px;' /><br />
															<p class='textBuscar' style='float:left;'>
																<a class='btnBotones mundo' data-transaccion='1' style='margin-right:10px;'>Explorar</a><span style='font-family:inherit; color:inherit;' onclick='$(this).parent().find(\"a.mundo\").click();'>Buscar con Mapa</span>
															</p><p class='textBuscar' onclick='template_buscar_transaccion(1);'>
																<a class='btnBotones buscador' style='margin-right:5px;'>Buscar</a>Buscar
															</p>
														</div>
													</td>
													<td><span class='barra'></span></td>
													<td class='texto2'>
														<span class='texto'>Renta Vacacional</span>
														<div class='opcionRenta vacacional'>
															<p class='arregloBorder'></p>
															<ul id='template_rentaVac_tipoInmueble' class='template_campos' style='width:150px;'>
																Tipo de inmueble<span></span>
																<li class='lista'>
																	<ul>";


    for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
        if (in_array($arrayTipoInmueble[$x]["id"], array(1, 2)))
            echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "'>" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><ul id='template_rentaVac_estado' class='template_campos' style='width:120px;'>
																Estado<span></span>
																<li class='lista'>
																	<ul>";


    for ($x = 0; $x < count($arrayEstado); $x++) {
        echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><ul id='template_rentaVac_municipio' class='template_campos' style='width:150px;'>
																Municipio<span></span>
																<li class='lista'>
																	<ul></ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul><br />
															<ul id='template_rentaVac_precio' class='template_campos' style='width:250px;'>
																Precio por noche<span></span>
																<li class='lista'>
																	<ul>";


    $incre = 1000;

    for ($x = 0; $x < 3000; $x += $incre) {
        echo "<li data-value='" . $x . "-" . ($x + $incre) . "'>$" . number_format($x, 0, ".", ",") . " - $" . number_format(($x + $incre), 0, ".", ",") . "</li>";
    }


    echo
        "<li data-value='3000-1000000000'>Más de $3,000</li>
																	</ul>
																</li>
																<p data-value='-1'></p>
																<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
															</ul>" .
        "<a class='btnBotones mundo' data-transaccion='3' style='margin-right:10px; margin-top:27px;'>Explorar</a>" .
        "<input type='text' id='template_rentaVac_codigo' class='template_campos' placeholder='Código' style='width:150px; margin-top:25px;' /><br />
															<p class='textBuscar' style='float:left;'>
																<a class='btnBotones mundo' data-transaccion='3' style='margin-right:10px;'>Explorar</a><span style='font-family:inherit; color:inherit;' onclick='$(this).parent().find(\"a.mundo\").click();'>Buscar con Mapa</span>
															</p><p class='textBuscar' onclick='template_buscar_transaccion(3);'>
																<a class='btnBotones buscador' style='margin-right:5px;'>Buscar</a>Buscar
															</p>
														</div>
													</td>
													<td><span class='barra'></span></td>
												</tr>
											</tbody>
										</table>
										<p class='wrapperP' onclick='" . (isset($_SESSION[userId]) ? "gotoURL(\"nuevoAnuncio.php\");" : "gotoURL(\"registro.php\");") . "'><a class='btnBotonesPublica'>Publica un Anuncio</a></p>
									</div>
								</div>
							</div>
						</div>
						<div class='template_contenedorCuerpo'>
							<div class='template_cuerpo'>
								<div class='lineaIzq'></div>";
}


function getFooter()
{
    $registerUrl = (!isset($_SESSION[userId]))?'/registro.php':'/nuevoAnuncio.php';
    echo
        "<!-- <div class='lineaDer'></div> -->
							</div>
						</div>
						<footer class='container-fluid hidden-print'>
                            <div class='container'>
                                <div class='col-lg-3 col-sm-7 col-xs-12 hidden-xs'>
                                    <img src='images/logo-explora-gris.png' class='img-responsive'><br>
                                    <ul class='list-unstyled'>
                                      <li><a href='/'>Inicio</a></li>
                                      <li><a href='/venta/todos-los-tipos/todo-mexico/todas-las-ciudades'>Propiedades en venta</a></li>
                                      <li><a href='/renta/todos-los-tipos/todo-mexico/todas-las-ciudades'>Propiedades en renta</a></li>
                                      <li><a href='/renta-vacacional/todos-los-tipos/todo-mexico/todas-las-ciudades'>Renta Vacacional</a></li>
                                      <!-- <li><a href='#'>Quienes Somos</a></li> -->
                                      <li><a href='contacto.php'>Contacto</a></li>
                                    </ul>
                                </div>
                                <div class='col-lg-3 col-sm-7 col-xs-12 hidden-xs'>
                                    <h3>Tu Inmueble</h3>
                                    <ul class='list-unstyled'>
                                      <li><a href='/reglas-publicacion'>Reglas de Publicaci&oacute;n</a></li>
                                      <li><a href='".$registerUrl."'>Publica tu inmueble</a></li>
                                      <li><a href='/ayuda'>Ayuda</a></li>
                                    </ul>
                                </div>
                                <div class='col-lg-3 col-sm-7 col-xs-12'>
                                    <h3 class='hidden-xs'>Contacto</h3>
                                    <p><a href='mailto:contacto@explorainmuebles.com'>contacto@explorainmuebles.com</a></p>
                                    <p>Tel: (33) 1920-6419</p>
                                    <p>Horario de 8 a.m a 7 p.m</p>
                                </div>
                                <div class='col-lg-3 col-sm-7 col-xs-12 contact-form hidden-xs'>
                                    <h3>Formulario</h3>
                                    <input type='text' id='contacto_nombre' class='template_campos' placeholder='Nombre' />
                                    <input type=\"text\" id=\"contacto_email\" class=\"template_campos\" placeholder=\"E-mail\" />
                                    <textarea id=\"contacto_mensaje\" class=\"template_campos\" placeholder=\"Mensaje\"></textarea>
                                    <span class=\"btn btn-inmueble btn-lg\" onclick=\"contacto_validarCampos();\" >Enviar</span>
                                </div>
                            </div>
                            <div class='container social-network'>
                                <div class='col-lg-4 col-sm-7 col-xs-12'>
                                Derechos Reservados | Explora Inmuebles&copy; 2015
                                </div>
                                <div class='col-lg-4 col-sm-7 col-xs-12 hidden-xs'>
                                    <a href='/aviso-privacidad'>Aviso de Privacidad</a> |
                                    <a href='/terminos-condiciones'>T&eacute;rminos y Condiciones</a> |
                                    <a href='".$registerUrl."'>Publica tu Anuncio </a>
                                </div>
                                <div class='col-lg-4 col-sm-7 col-xs-12 social-icons'>
                                    <a href='https://www.facebook.com/explorainmueblesmx'><span class='flaticon-facebook51'></span></a>
                                    <a href='https://twitter.com/ExploraInmueble'><span class='flaticon-twitter44'></span></a>
                                    <a href='https://instagram.com/explora_inmuebles/'><span class='flaticon-instagram3'></span></a>
                                    <a href='https://www.youtube.com/channel/UCRf7kJDrVb5-DiSgT3QL5eQ'><span class='flaticon-socialnetwork75'></span></a>
                                </div>
                            </div>
						</footer>
					</div>";
}

function FinCuerpo(){
    return getFooter();
}
/*
    Muestra el final del cuerpo y enseguida el footer
*/
function FinCuerpoBack()
{
    echo
        "<div class='lineaDer'></div>
							</div>
						</div>
						<div class='template_footer'>
							<p class='linea'></p>
							<div class='contenedorIconos'>
								<p>
									<a href='https://www.facebook.com/explorainmueblesmx' target='_blank' class='btnBotones facebook'>Facebook</a>
									<a href='https://twitter.com/ExploraInmueble' target='_blank' class='btnBotones twitter'>Twitter</a>
									<a href='https://www.youtube.com/channel/UCRf7kJDrVb5-DiSgT3QL5eQ' target='_blank' class='btnBotones youtube'>Youtube</a>
									<a href='https://instagram.com/explora_inmuebles/' target='_blank' class='btnBotones instagram'>Instagram</a>
									<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' class='btnBotones telefono'>Teléfonos</a>
									<a href='http://www.explorainmuebles.com/contacto.php' target='_blank' class='btnBotones email'>Email</a>
								</p>
							</div>
							<div class='ligas'>
								<a href='ayuda'>Ayuda</a><span>/</span>
								<a href='aviso-privacidad'>Aviso de Privacidad</a><span>/</span>
								<a href='terminos-condiciones'>Términos y condiciones</a><span>/</span>
								<a href='reglas-publicacion'>Reglas de publicación</a><span>/</span>
								<a href='contacto'>Contacto</a><span>/</span>
								<a href='" . (isset($_SESSION[userId]) ? "publica-anuncio" : "registro") . "'>Publica tu anuncio</a>
							</div>
						</div>
					</div>";
}


/*
    Muestra los popups generales en todas las interfaces
*/
function PopUpGenerales($fcnCerrarPopUp = NULL)
{
    echo
        "<div id='template_mascaraPrincipal' class='template_mascaraPrincipal' onclick='template_principalCerrarPopUp(" . ($fcnCerrarPopUp != NULL ? $fcnCerrarPopUp : "") . ");'></div>
				<div id='template_linkNuevoAnuncio' class='templatePopUp template_linkNuevoAnuncio'>
					<span class='btnCerrar' onclick='template_principalCerrarPopUp(" . ($fcnCerrarPopUp != NULL ? $fcnCerrarPopUp : "") . ");'>x</span>
					<table>
						<tbody>
							<tr>
								<td>Inicia sesión o regístrate para publicar un anuncio</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id='template_errorSelectMunicipio' class='templatePopUp template_errorSelectMunicipio'>
					<span class='btnCerrar' onclick='template_principalCerrarPopUp(" . ($fcnCerrarPopUp != NULL ? $fcnCerrarPopUp : "") . ");'>x</span>
					<table>
						<tbody>
							<tr>
								<td>Debes seleccionar primero un Estado</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id='template_errorSelectColonia' class='templatePopUp template_errorSelectMunicipio'>
					<span class='btnCerrar' onclick='template_principalCerrarPopUp(" . ($fcnCerrarPopUp != NULL ? $fcnCerrarPopUp : "") . ");'>x</span>
					<table>
						<tbody>
							<tr>
								<td>Debes seleccionar primero un Municipio</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id='template_alertPersonalizado' class='templatePopUp template_alertPersonalizado'>
					<span class='btnCerrar' onclick='template_principalCerrarPopUp(" . ($fcnCerrarPopUp != NULL ? $fcnCerrarPopUp : "") . ");'>x</span>
					<table>
						<tbody>
							<tr>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id='template_votacionComentarios' class='templatePopUp template_votacionComentarios'>
					<span class='btnCerrar' onclick='template_principalCerrarPopUp(" . ($fcnCerrarPopUp != NULL ? $fcnCerrarPopUp : "") . ");'>x</span>
					<table>
						<tbody>
							<tr>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>";
}


/*
    Imprime el fin de las etiquetas de body, html
*/
function FinHTML()
{
    echo
    "</body>
			</html>";
}


/*
    Imprime la busqueda o busqueda avanzada

        * arrayDatosPost:	[Array String], es una arreglo de datos que se envian por post, varios campos son opcionales
*/
function template_busquedaAvanzadaBack($arrayDatosPost = array())
{
    $arrayTipoInmueble = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }


    $arrayEstado = array();

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }


    echo
        "<div class='template_contenedorBusquedaAvanzada'>
				<p class='titulo'>
					Filtros
					<input type='text' id='template_busqueda_codigo' value='" . (isset($arrayDatosPost["codigo"]) ? $arrayDatosPost["codigo"] : "") . "' style='display:none;' />
				</p>
				<div class='template_campos_select'>
					<ul id='template_busqueda_transaccion' class='template_campos'>
						Transacción<span></span>
						<li class='lista'>
							<ul>
								<li data-value='1'>Renta</li>
								<li data-value='3'>Renta Vacacional</li>
								<li data-value='2'>Venta</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_tipoInmueble' class='template_campos'>
						Tipo de Inmueble<span></span>
						<li class='lista'>
							<ul>";


    for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
        echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "' " . (in_array($arrayTipoInmueble[$x]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'>Tipo de Inmueble</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_estado' class='template_campos'>
						Estado<span></span>
						<li class='lista'>
							<ul>";


    for ($x = 0; $x < count($arrayEstado); $x++) {
        echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_municipio' class='template_campos'>
						Municipio<span></span>
						<li class='lista'>
							<ul></ul>
						</li>
						<p data-value='-1'>Municipio</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_colonia' class='template_campos'>
						Colonia<span></span>
						<li class='lista'>
							<ul></ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_precios_min' class='template_campos'>
						Precio Mínimo<span></span>
						<li class='lista'>
							<ul>
								<li data-value='500' data-transaccion='3'>$500</li>
								<li data-value='1000' data-transaccion='3'>$1,000</li>
								<li data-value='2000' data-transaccion='1,3'>$2,000</li>
								<li data-value='3000' data-transaccion='1'>$3,000</li>
								<li data-value='4000' data-transaccion='1'>$4,000</li>
								<li data-value='5000' data-transaccion='1'>$5,000</li>
								<li data-value='6000' data-transaccion='1'>$6,000</li>
								<li data-value='7000' data-transaccion='1'>$7,000</li>
								<li data-value='8000' data-transaccion='1'>$8,000</li>
								<li data-value='9000' data-transaccion='1'>$9,000</li>
								<li data-value='10000' data-transaccion='1'>$10,000</li>
								<li data-value='11000' data-transaccion='1'>$11,000</li>
								<li data-value='12000' data-transaccion='1'>$12,000</li>
								<li data-value='13000' data-transaccion='1'>$13,000</li>
								<li data-value='14000' data-transaccion='1'>$14,000</li>
								<li data-value='15000' data-transaccion='1'>$15,000</li>
								<li data-value='500000' data-transaccion='2'>$500,000</li>
								<li data-value='600000' data-transaccion='2'>$600,000</li>
								<li data-value='700000' data-transaccion='2'>$700,000</li>
								<li data-value='800000' data-transaccion='2'>$800,000</li>
								<li data-value='900000' data-transaccion='2'>$900,000</li>
								<li data-value='1000000' data-transaccion='2'>$1,000,000</li>
								<li data-value='1500000' data-transaccion='2'>$1,500,000</li>
								<li data-value='2000000' data-transaccion='2'>$2,000,000</li>
								<li data-value='2500000' data-transaccion='2'>$2,500,000</li>
								<li data-value='3000000' data-transaccion='2'>$3,000,000</li>
								<li data-value='3500000' data-transaccion='2'>$3,500,000</li>
								<li data-value='4000000' data-transaccion='2'>$4,000,000</li>
								<li data-value='4500000' data-transaccion='2'>$4,500,000</li>
								<li data-value='5000000' data-transaccion='2'>$5,000,000</li>
								<li data-value='5500000' data-transaccion='2'>$5,500,000</li>
								<li data-value='6000000' data-transaccion='2'>$6,000,000</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_precios_max' class='template_campos'>
						Precio Máximo<span></span>
						<li class='lista'>
							<ul>
								<li data-value='5000' data-transaccion='3'>$1,000</li>
								<li data-value='5000' data-transaccion='3'>$2,000</li>
								<li data-value='5000' data-transaccion='3'>$3,000</li>
								<li data-value='5000' data-transaccion='1'>$5,000</li>
								<li data-value='6000' data-transaccion='1'>$6,000</li>
								<li data-value='7000' data-transaccion='1'>$7,000</li>
								<li data-value='8000' data-transaccion='1'>$8,000</li>
								<li data-value='9000' data-transaccion='1'>$9,000</li>
								<li data-value='10000' data-transaccion='1'>$10,000</li>
								<li data-value='11000' data-transaccion='1'>$11,000</li>
								<li data-value='12000' data-transaccion='1'>$12,000</li>
								<li data-value='13000' data-transaccion='1'>$13,000</li>
								<li data-value='14000' data-transaccion='1'>$14,000</li>
								<li data-value='15000' data-transaccion='1'>$15,000</li>
								<li data-value='16000' data-transaccion='1'>$16,000</li>
								<li data-value='17000' data-transaccion='1'>$17,000</li>
								<li data-value='18000' data-transaccion='1'>$18,000</li>
								<li data-value='19000' data-transaccion='1'>$19,000</li>
								<li data-value='20000' data-transaccion='1'>$20,000</li>
								<li data-value='21000' data-transaccion='1'>$21,000</li>
								<li data-value='22000' data-transaccion='1'>$22,000</li>
								<li data-value='23000' data-transaccion='1'>$23,000</li>
								<li data-value='24000' data-transaccion='1'>$24,000</li>
								<li data-value='25000' data-transaccion='1'>$25,000</li>
								<li data-value='26000' data-transaccion='1'>$26,000</li>
								<li data-value='27000' data-transaccion='1'>$27,000</li>
								<li data-value='28000' data-transaccion='1'>$28,000</li>
								<li data-value='29000' data-transaccion='1'>$29,000</li>
								<li data-value='30000' data-transaccion='1'>$30,000</li>
								<li data-value='700000' data-transaccion='2'>$700,000</li>
								<li data-value='800000' data-transaccion='2'>$800,000</li>
								<li data-value='900000' data-transaccion='2'>$900,000</li>
								<li data-value='1000000' data-transaccion='2'>$1,000,000</li>
								<li data-value='1500000' data-transaccion='2'>$1,500,000</li>
								<li data-value='2000000' data-transaccion='2'>$2,000,000</li>
								<li data-value='2500000' data-transaccion='2'>$2,500,000</li>
								<li data-value='3000000' data-transaccion='2'>$3,000,000</li>
								<li data-value='3500000' data-transaccion='2'>$3,500,000</li>
								<li data-value='4000000' data-transaccion='2'>$4,000,000</li>
								<li data-value='4500000' data-transaccion='2'>$4,500,000</li>
								<li data-value='5000000' data-transaccion='2'>$5,000,000</li>
								<li data-value='5500000' data-transaccion='2'>$5,500,000</li>
								<li data-value='6000000' data-transaccion='2'>$6,000,000</li>
								<li data-value='7000000' data-transaccion='2'>$7,000,000</li>
								<li data-value='8000000' data-transaccion='2'>$8,000,000</li>
								<li data-value='9000000' data-transaccion='2'>$9,000,000</li>
								<li data-value='10000000' data-transaccion='2'>$10,000,000</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_wcs' class='template_campos'>
						Baños<span></span>
						<li class='lista'>
							<ul>";


    for ($x = 1; $x < 10; $x++) {
        echo "<li data-value='" . $x . "'>" . $x . "</li>";
    }


    echo
    "<li data-value='10'>10 ó mas</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select'>
					<ul id='template_busqueda_recamaras' class='template_campos'>
						Recamaras<span></span>
						<li class='lista'>
							<ul>";


    for ($x = 1; $x < 10; $x++) {
        echo "<li data-value='" . $x . "'>" . $x . "</li>";
    }


    echo
    "<li data-value='10'>10 ó mas</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<!--p class='template_campos_select textBusquedaAvanzada'>Búsqueda Avanzada</p-->
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_antiguedad' class='template_campos'>
						Antig&uuml;edad<span></span>
						<li class='lista'>
							<ul>
								<li data-value='1'>0 Años</li>
								<li data-value='2'>1 Año</li>
								<li data-value='3'>2 Años</li>
								<li data-value='4'>3 Años</li>
								<li data-value='5'>4 Años</li>
								<li data-value='6'>5 - 9 Años</li>
								<li data-value='7'>10 - 19 Años</li>
								<li data-value='8'>20 - 29 Años</li>
								<li data-value='9'>30 - 39 Años</li>
								<li data-value='10'>40 - 49 Años</li>
								<li data-value='11'>50 Años ó mas</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_estadoConservacion' class='template_campos'>
						Estado de Conservación<span></span>
						<li class='lista'>
							<ul>
								<li data-value='1'>Excelente</li>
								<li data-value='2'>Bueno</li>
								<li data-value='3'>Regular</li>
								<li data-value='4'>Malo</li>
								<li data-value='5'>Muy Malo</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_dimensionTotalMin' class='template_campos'>
						Dimensión Total Min<span></span>
						<li class='lista'>
							<ul>";

    for ($x = 50; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1000; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_dimensionTotalMax' class='template_campos'>
						Dimensión Total Max<span></span>
						<li class='lista'>
							<ul>";

    for ($x = 100; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1500; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_dimensionConstruidaMin' class='template_campos'>
						Dimensión Construída Min<span></span>
						<li class='lista'>
							<ul>";

    for ($x = 50; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1000; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_dimensionConstruidaMax' class='template_campos'>
						Dimensión Construída Max<span></span>
						<li class='lista'>
							<ul>";

    for ($x = 100; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1500; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
        "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select busquedaAvanzada'>
					<ul id='template_busqueda_amueblado' class='template_campos'>
						Está Amueblado<span></span>
						<li class='lista'>
							<ul>
								<li data-value='1'>Amueblado</li>
								<li data-value='2'>Semi-Amueblado</li>
								<li data-value='3'>No</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Ambientes</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_cocinaEquipada' " . (isset($arrayDatosPost["cocinaEquipada"]) ? "checked='checked'" : "") . " />Cocina Equipada</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_estudio' " . (isset($arrayDatosPost["estudio"]) ? "checked='checked'" : "") . " />Estudio</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_cuartoServicio' " . (isset($arrayDatosPost["cuartoServicio"]) ? "checked='checked'" : "") . " />Cuarto de Servicio</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_cuartoTV' " . (isset($arrayDatosPost["cuartoTV"]) ? "checked='checked'" : "") . " />Cuarto de TV</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_bodega' " . (isset($arrayDatosPost["bodega"]) ? "checked='checked'" : "") . " />Bodega</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_terraza' " . (isset($arrayDatosPost["terraza"]) ? "checked='checked'" : "") . " />Terraza</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_jardin' " . (isset($arrayDatosPost["jardin"]) ? "checked='checked'" : "") . " />Jardín</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_areaJuegosInfantiles' " . (isset($arrayDatosPost["areaJuegosInfantiles"]) ? "checked='checked'" : "") . " />Área de Juegos Infantiles</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_comedor' " . (isset($arrayDatosPost["comedor"]) ? "checked='checked'" : "") . " />Comedor</p>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Servicios</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_serviciosBasicos' " . (isset($arrayDatosPost["serviciosBasicos"]) ? "checked='checked'" : "") . " />Servicios Básicos</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_gas' " . (isset($arrayDatosPost["gas"]) ? "checked='checked'" : "") . " />Gas</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_lineaTelefonica' " . (isset($arrayDatosPost["lineaTelefonica"]) ? "checked='checked'" : "") . " />Línea Telefónica</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_internetDisponible' " . (isset($arrayDatosPost["internetDisponible"]) ? "checked='checked'" : "") . " />Internet Disponible</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_aireAcondicionado' " . (isset($arrayDatosPost["aireAcondicionado"]) ? "checked='checked'" : "") . " />Aire Acondicionado</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_calefaccion' " . (isset($arrayDatosPost["calefaccion"]) ? "checked='checked'" : "") . " />Calefacción</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_casetaVigilancia' " . (isset($arrayDatosPost["casetaVigilancia"]) ? "checked='checked'" : "") . " />Caseta de Vigilancia</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_seguridad' " . (isset($arrayDatosPost["seguridad"]) ? "checked='checked'" : "") . " />Seguridad</p>
					<p class='checks'><input type='text' id='template_busqueda_cuotaMantenimiento' class='template_campos' placeholder='Cuota Mantenimiento' value='" . (isset($arrayDatosPost["cuotaMantenimiento"]) ? $arrayDatosPost["cuotaMantenimiento"] : "") . "' /></p>
					<p class='checks'><input type='text' id='template_busqueda_elevador' class='template_campos' placeholder='Elevador' value='" . (isset($arrayDatosPost["elevador"]) ? $arrayDatosPost["elevador"] : "") . "' /></p>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Amenidades</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_alberca' " . (isset($arrayDatosPost["alberca"]) ? "checked='checked'" : "") . " />Alberca</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_casaClub' " . (isset($arrayDatosPost["casaClub"]) ? "checked='checked'" : "") . " />Casa Club</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_canchaTenis' " . (isset($arrayDatosPost["canchaTenis"]) ? "checked='checked'" : "") . " />Cancha de Tenis</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_vistaMar' " . (isset($arrayDatosPost["vistaMar"]) ? "checked='checked'" : "") . " />Vista al Mar</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_jacuzzi' " . (isset($arrayDatosPost["jacuzzi"]) ? "checked='checked'" : "") . " />Jacuzzi</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_permiteMascotas' " . (isset($arrayDatosPost["permiteMascotas"]) ? "checked='checked'" : "") . " />Se Permite Mascotas</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_gimnasio' " . (isset($arrayDatosPost["gimnasio"]) ? "checked='checked'" : "") . " />Gimnasio</p>
					<p class='checks'><input type='text' id='template_busqueda_estacionamientoVisitas' class='template_campos' placeholder='Estacionamiento para Visitas' value='" . (isset($arrayDatosPost["estacionamientoVisitas"]) ? $arrayDatosPost["estacionamientoVisitas"] : "") . "' /></p>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Otras Características</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_centrosComerciales' " . (isset($arrayDatosPost["centrosComerciales"]) ? "checked='checked'" : "") . " />Centros Comerciales</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_escuelasCercanas' " . (isset($arrayDatosPost["escuelasCercanas"]) ? "checked='checked'" : "") . " />Escuelas Cercanas</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_fumadoresPermitidos' " . (isset($arrayDatosPost["fumadoresPermitidos"]) ? "checked='checked'" : "") . " />Fumadores Permitidos</p>
					<p class='checks'><input type='text' id='template_busqueda_numeroOficinas' class='template_campos' placeholder='Número de Oficinas' value='" . (isset($arrayDatosPost["numeroOficinas"]) ? $arrayDatosPost["numeroOficinas"] : "") . "' /></p>
				</div>
				<p class='template_campos_select buscar'><a class='btnBotones buscador' style='margin-right:5px;'>Buscar</a>Aplicar Filtros</p>
			</div>";
}


/*
    Imprime las opciones de mi perfil
*/
function template_opciones_miPerfil()
{
    echo
        "<div class='template_opciones_miPerfil'>
				<p class='titulo'><a class='btnBotones miPerfil' href='perfil.php'>Perfil</a><a href='perfil.php'>Mi Perfil</a></p>
				<p><a class='btnBotones misAnuncios' href='misAnuncios.php'>Anuncios</a><a href='misAnuncios.php'>Mis Anuncios</a></p>
				<p><a class='btnBotones publica' href='nuevoAnuncio.php'>Publicar</a><a href='nuevoAnuncio.php'>Crear Anuncio</a></p>" .
        //(($_SESSION[userInmobiliaria] != NULL ? "<p><a class='btnBotones publica' href='misDesarrollos.php'>Desarrollo</a><a href='misDesarrollos.php'>Desarrollos</a></p>" : "")).
        "<p><a class='btnBotones misFavoritos' href='favoritos.php'>Favoritos</a><a href='favoritos.php'>Favoritos</a></p>
				<!--p><a class='btnBotones buscador'>Recomendacion</a>Recomendaciones</p-->" .
        (($_SESSION[userAdminInmobiliaria] == 1 ? "<p><a class='btnBotones usuarios' href='usuarios.php'>Buscar</a><a href='usuarios.php'>Usuarios</a></p>" : "")) .
        "<p><a class='btnBotones candado' href='chgPassword.php'>ChgPass</a><a href='chgPassword.php'>Cambiar Contraseña</a></p>
				<p onclick='gotoURL(\"lib_php/cerrarSession.php\");'><a class='btnBotones puerta'>Salir</a>Cerrar sesión</p>
			</div>";
}

/**/


function template_busquedaAvanzadaResponsive($arrayDatosPost = array())
{
    $arrayTipoInmueble = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }


    $arrayEstado = array();

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }


    echo
    /* Buscador activo*/
        "<div class='template_contenedorBusquedaAvanzada'>
				<p class='titulo'>
					Filtros
					<input type='text' id='template_busqueda_codigo' value='" . (isset($arrayDatosPost["codigo"]) ? $arrayDatosPost["codigo"] : "") . "' style='display:none;' />
				</p>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_transaccion' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='1'>Renta</li>
								<li data-value='3'>Renta Vacacional</li>
								<li data-value='2'>Venta</li>
							</ul>
						</li>
						<p data-value='-1'>Tipo Transaccion</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_tipoInmueble' class='template_campos'>

						<li class='lista'>
							<ul>";


    for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
        echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "' " . (in_array($arrayTipoInmueble[$x]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'>Tipo de Inmueble</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true'  />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_estado' class='template_campos'>

						<li class='lista'>
							<ul>";


    for ($x = 0; $x < count($arrayEstado); $x++) {
        echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'>Estado</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_municipio' class='template_campos'>

						<li class='lista'>
							<ul></ul>
						</li>
						<p data-value='-1'>Municipio</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_colonia' class='template_campos'>

						<li class='lista'>
							<ul></ul>
						</li>
						<p data-value='-1'>Colonia</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_precios_min' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='500' data-transaccion='3'>$500</li>
								<li data-value='1000' data-transaccion='3'>$1,000</li>
								<li data-value='2000' data-transaccion='1,3'>$2,000</li>
								<li data-value='3000' data-transaccion='1'>$3,000</li>
								<li data-value='4000' data-transaccion='1'>$4,000</li>
								<li data-value='5000' data-transaccion='1'>$5,000</li>
								<li data-value='6000' data-transaccion='1'>$6,000</li>
								<li data-value='7000' data-transaccion='1'>$7,000</li>
								<li data-value='8000' data-transaccion='1'>$8,000</li>
								<li data-value='9000' data-transaccion='1'>$9,000</li>
								<li data-value='10000' data-transaccion='1'>$10,000</li>
								<li data-value='11000' data-transaccion='1'>$11,000</li>
								<li data-value='12000' data-transaccion='1'>$12,000</li>
								<li data-value='13000' data-transaccion='1'>$13,000</li>
								<li data-value='14000' data-transaccion='1'>$14,000</li>
								<li data-value='15000' data-transaccion='1'>$15,000</li>
								<li data-value='500000' data-transaccion='2'>$500,000</li>
								<li data-value='600000' data-transaccion='2'>$600,000</li>
								<li data-value='700000' data-transaccion='2'>$700,000</li>
								<li data-value='800000' data-transaccion='2'>$800,000</li>
								<li data-value='900000' data-transaccion='2'>$900,000</li>
								<li data-value='1000000' data-transaccion='2'>$1,000,000</li>
								<li data-value='1500000' data-transaccion='2'>$1,500,000</li>
								<li data-value='2000000' data-transaccion='2'>$2,000,000</li>
								<li data-value='2500000' data-transaccion='2'>$2,500,000</li>
								<li data-value='3000000' data-transaccion='2'>$3,000,000</li>
								<li data-value='3500000' data-transaccion='2'>$3,500,000</li>
								<li data-value='4000000' data-transaccion='2'>$4,000,000</li>
								<li data-value='4500000' data-transaccion='2'>$4,500,000</li>
								<li data-value='5000000' data-transaccion='2'>$5,000,000</li>
								<li data-value='5500000' data-transaccion='2'>$5,500,000</li>
								<li data-value='6000000' data-transaccion='2'>$6,000,000</li>
							</ul>
						</li>
						<p data-value='-1'>Precio Minimo</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_precios_max' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='5000' data-transaccion='3'>$1,000</li>
								<li data-value='5000' data-transaccion='3'>$2,000</li>
								<li data-value='5000' data-transaccion='3'>$3,000</li>
								<li data-value='5000' data-transaccion='1'>$5,000</li>
								<li data-value='6000' data-transaccion='1'>$6,000</li>
								<li data-value='7000' data-transaccion='1'>$7,000</li>
								<li data-value='8000' data-transaccion='1'>$8,000</li>
								<li data-value='9000' data-transaccion='1'>$9,000</li>
								<li data-value='10000' data-transaccion='1'>$10,000</li>
								<li data-value='11000' data-transaccion='1'>$11,000</li>
								<li data-value='12000' data-transaccion='1'>$12,000</li>
								<li data-value='13000' data-transaccion='1'>$13,000</li>
								<li data-value='14000' data-transaccion='1'>$14,000</li>
								<li data-value='15000' data-transaccion='1'>$15,000</li>
								<li data-value='16000' data-transaccion='1'>$16,000</li>
								<li data-value='17000' data-transaccion='1'>$17,000</li>
								<li data-value='18000' data-transaccion='1'>$18,000</li>
								<li data-value='19000' data-transaccion='1'>$19,000</li>
								<li data-value='20000' data-transaccion='1'>$20,000</li>
								<li data-value='21000' data-transaccion='1'>$21,000</li>
								<li data-value='22000' data-transaccion='1'>$22,000</li>
								<li data-value='23000' data-transaccion='1'>$23,000</li>
								<li data-value='24000' data-transaccion='1'>$24,000</li>
								<li data-value='25000' data-transaccion='1'>$25,000</li>
								<li data-value='26000' data-transaccion='1'>$26,000</li>
								<li data-value='27000' data-transaccion='1'>$27,000</li>
								<li data-value='28000' data-transaccion='1'>$28,000</li>
								<li data-value='29000' data-transaccion='1'>$29,000</li>
								<li data-value='30000' data-transaccion='1'>$30,000</li>
								<li data-value='700000' data-transaccion='2'>$700,000</li>
								<li data-value='800000' data-transaccion='2'>$800,000</li>
								<li data-value='900000' data-transaccion='2'>$900,000</li>
								<li data-value='1000000' data-transaccion='2'>$1,000,000</li>
								<li data-value='1500000' data-transaccion='2'>$1,500,000</li>
								<li data-value='2000000' data-transaccion='2'>$2,000,000</li>
								<li data-value='2500000' data-transaccion='2'>$2,500,000</li>
								<li data-value='3000000' data-transaccion='2'>$3,000,000</li>
								<li data-value='3500000' data-transaccion='2'>$3,500,000</li>
								<li data-value='4000000' data-transaccion='2'>$4,000,000</li>
								<li data-value='4500000' data-transaccion='2'>$4,500,000</li>
								<li data-value='5000000' data-transaccion='2'>$5,000,000</li>
								<li data-value='5500000' data-transaccion='2'>$5,500,000</li>
								<li data-value='6000000' data-transaccion='2'>$6,000,000</li>
								<li data-value='7000000' data-transaccion='2'>$7,000,000</li>
								<li data-value='8000000' data-transaccion='2'>$8,000,000</li>
								<li data-value='9000000' data-transaccion='2'>$9,000,000</li>
								<li data-value='10000000' data-transaccion='2'>$10,000,000</li>
							</ul>
						</li>
						<p data-value='-1'>Precio M&aacute;ximo</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_wcs' class='template_campos'>

						<li class='lista'>
							<ul>";


    for ($x = 1; $x < 10; $x++) {
        echo "<li data-value='" . $x . "'>" . $x . "</li>";
    }


    echo
    "<li data-value='10'>10 ó mas</li>
							</ul>
						</li>
						<p data-value='-1'>Ba&ntilde;os</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12'>
					<ul id='template_busqueda_recamaras' class='template_campos'>

						<li class='lista'>
							<ul>";


    for ($x = 1; $x < 10; $x++) {
        echo "<li data-value='" . $x . "'>" . $x . "</li>";
    }


    echo
    "<li data-value='10'>10 ó mas</li>
							</ul>
						</li>
						<p data-value='-1'>Rec&aacute;maras</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
					</ul>
				</div>
				<!--p class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 textBusquedaAvanzada'>Búsqueda Avanzada</p-->
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_antiguedad' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='1'>0 Años</li>
								<li data-value='2'>1 Año</li>
								<li data-value='3'>2 Años</li>
								<li data-value='4'>3 Años</li>
								<li data-value='5'>4 Años</li>
								<li data-value='6'>5 - 9 Años</li>
								<li data-value='7'>10 - 19 Años</li>
								<li data-value='8'>20 - 29 Años</li>
								<li data-value='9'>30 - 39 Años</li>
								<li data-value='10'>40 - 49 Años</li>
								<li data-value='11'>50 Años ó mas</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_estadoConservacion' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='1'>Excelente</li>
								<li data-value='2'>Bueno</li>
								<li data-value='3'>Regular</li>
								<li data-value='4'>Malo</li>
								<li data-value='5'>Muy Malo</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_dimensionTotalMin' class='template_campos'>

						<li class='lista'>
							<ul>";

    for ($x = 50; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1000; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_dimensionTotalMax' class='template_campos'>

						<li class='lista'>
							<ul>";

    for ($x = 100; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1500; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_dimensionConstruidaMin' class='template_campos'>

						<li class='lista'>
							<ul>";

    for ($x = 50; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1000; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_dimensionConstruidaMax' class='template_campos'>

						<li class='lista'>
							<ul>";

    for ($x = 100; $x <= 500; $x += 50) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }
    for ($x = 600; $x <= 1500; $x += 100) {
        echo "<li data-value='" . $x . "'>" . $x . " m<sup>2</sup></li>";
    }


    echo
        "</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select col-lg-12 col-md-3 col-sm-4 col-xs-12 busquedaAvanzada'>
					<ul id='template_busqueda_amueblado' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='1'>Amueblado</li>
								<li data-value='2'>Semi-Amueblado</li>
								<li data-value='3'>No</li>
							</ul>
						</li>
						<p data-value='-1'></p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Ambientes</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_cocinaEquipada' " . (isset($arrayDatosPost["cocinaEquipada"]) ? "checked='checked'" : "") . " />Cocina Equipada</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_estudio' " . (isset($arrayDatosPost["estudio"]) ? "checked='checked'" : "") . " />Estudio</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_cuartoServicio' " . (isset($arrayDatosPost["cuartoServicio"]) ? "checked='checked'" : "") . " />Cuarto de Servicio</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_cuartoTV' " . (isset($arrayDatosPost["cuartoTV"]) ? "checked='checked'" : "") . " />Cuarto de TV</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_bodega' " . (isset($arrayDatosPost["bodega"]) ? "checked='checked'" : "") . " />Bodega</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_terraza' " . (isset($arrayDatosPost["terraza"]) ? "checked='checked'" : "") . " />Terraza</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_jardin' " . (isset($arrayDatosPost["jardin"]) ? "checked='checked'" : "") . " />Jardín</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_areaJuegosInfantiles' " . (isset($arrayDatosPost["areaJuegosInfantiles"]) ? "checked='checked'" : "") . " />Área de Juegos Infantiles</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_comedor' " . (isset($arrayDatosPost["comedor"]) ? "checked='checked'" : "") . " />Comedor</p>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Servicios</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_serviciosBasicos' " . (isset($arrayDatosPost["serviciosBasicos"]) ? "checked='checked'" : "") . " />Servicios Básicos</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_gas' " . (isset($arrayDatosPost["gas"]) ? "checked='checked'" : "") . " />Gas</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_lineaTelefonica' " . (isset($arrayDatosPost["lineaTelefonica"]) ? "checked='checked'" : "") . " />Línea Telefónica</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_internetDisponible' " . (isset($arrayDatosPost["internetDisponible"]) ? "checked='checked'" : "") . " />Internet Disponible</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_aireAcondicionado' " . (isset($arrayDatosPost["aireAcondicionado"]) ? "checked='checked'" : "") . " />Aire Acondicionado</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_calefaccion' " . (isset($arrayDatosPost["calefaccion"]) ? "checked='checked'" : "") . " />Calefacción</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_casetaVigilancia' " . (isset($arrayDatosPost["casetaVigilancia"]) ? "checked='checked'" : "") . " />Caseta de Vigilancia</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_seguridad' " . (isset($arrayDatosPost["seguridad"]) ? "checked='checked'" : "") . " />Seguridad</p>
					<p class='checks'><input type='text' id='template_busqueda_cuotaMantenimiento' class='template_campos' placeholder='Cuota Mantenimiento' value='" . (isset($arrayDatosPost["cuotaMantenimiento"]) ? $arrayDatosPost["cuotaMantenimiento"] : "") . "' /></p>
					<p class='checks'><input type='text' id='template_busqueda_elevador' class='template_campos' placeholder='Elevador' value='" . (isset($arrayDatosPost["elevador"]) ? $arrayDatosPost["elevador"] : "") . "' /></p>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Amenidades</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_alberca' " . (isset($arrayDatosPost["alberca"]) ? "checked='checked'" : "") . " />Alberca</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_casaClub' " . (isset($arrayDatosPost["casaClub"]) ? "checked='checked'" : "") . " />Casa Club</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_canchaTenis' " . (isset($arrayDatosPost["canchaTenis"]) ? "checked='checked'" : "") . " />Cancha de Tenis</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_vistaMar' " . (isset($arrayDatosPost["vistaMar"]) ? "checked='checked'" : "") . " />Vista al Mar</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_jacuzzi' " . (isset($arrayDatosPost["jacuzzi"]) ? "checked='checked'" : "") . " />Jacuzzi</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_permiteMascotas' " . (isset($arrayDatosPost["permiteMascotas"]) ? "checked='checked'" : "") . " />Se Permite Mascotas</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_gimnasio' " . (isset($arrayDatosPost["gimnasio"]) ? "checked='checked'" : "") . " />Gimnasio</p>
					<p class='checks'><input type='text' id='template_busqueda_estacionamientoVisitas' class='template_campos' placeholder='Estacionamiento para Visitas' value='" . (isset($arrayDatosPost["estacionamientoVisitas"]) ? $arrayDatosPost["estacionamientoVisitas"] : "") . "' /></p>
				</div>
				<p class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada'>Otras Características</p>
				<div class='template_campos_select busquedaAvanzada opcionBusquedaAvanzada2'>
					<p class='checks'><input type='checkbox' id='template_busqueda_centrosComerciales' " . (isset($arrayDatosPost["centrosComerciales"]) ? "checked='checked'" : "") . " />Centros Comerciales</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_escuelasCercanas' " . (isset($arrayDatosPost["escuelasCercanas"]) ? "checked='checked'" : "") . " />Escuelas Cercanas</p>
					<p class='checks'><input type='checkbox' id='template_busqueda_fumadoresPermitidos' " . (isset($arrayDatosPost["fumadoresPermitidos"]) ? "checked='checked'" : "") . " />Fumadores Permitidos</p>
					<p class='checks'><input type='text' id='template_busqueda_numeroOficinas' class='template_campos' placeholder='Número de Oficinas' value='" . (isset($arrayDatosPost["numeroOficinas"]) ? $arrayDatosPost["numeroOficinas"] : "") . "' /></p>
				</div>
				<p class='template_campos_select buscar text-center'><a class='buscador btn btn-inmueble btn-lg'> Aplicar Filtros</a></p>
			</div>";
}

function template_busquedaAvanzada($arrayDatosPost = array()){
    return template_busquedaAvanzadaResponsive($arrayDatosPost);
}

function getDestacados(){
    $query = 'SELECT *,
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
        FROM INMUEBLE, TIPO_INMUEBLE, USUARIO
        WHERE IMU_DESTACADO = 1 AND IMU_TIPO_INMUEBLE = TIN_ID
        AND IMU_LIMITE_VIGENCIA >= \''.date("Y-m-d").'\'
        AND IMU_USUARIO = USU_ID

        AND (
						IF (
							USU_INMOBILIARIA IS NOT NULL,
							(
								SELECT INM_VALIDEZ
								FROM INMOBILIARIA
								WHERE INM_ID = USU_INMOBILIARIA
							) >= '.date("Y-m-d").',
							1
						)
					)
        ORDER BY RAND()
        LIMIT 4;';


    $conexion = crearConexionPDO();
    $inmuebles = array();
    $urlArchivos = "images/images/";

    foreach ($conexion->query($query) as $row) {
        $inmueble = array(
            "id"						=>	$row["IMU_ID"],
            "titulo"					=>	$row["IMU_TITULO"],
            "tipo"						=>	$row["IMU_TIPO_INMUEBLE"],
            "precio"					=>	$row["IMU_PRECIO"],
            "calleNumero"				=>	$row["IMU_CALLE_NUMERO"],
            "estado"					=>	$row["IMU_ESTADO"],
            "ciudad"					=>	$row["IMU_CIUDAD"],
            "colonia"					=>	$row["IMU_COLONIA"],
            "cp"						=>	$row["IMU_CP"],
            "descripcion"				=>	$row["IMU_DESCRIPCION"] != NULL ? $row["IMU_DESCRIPCION"] : "",
            "dimensionTotal"			=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
            "dimensionConstruida"		=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
            "wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : "",
            "recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
            "metrosFrente"				=>	$row["IMU_METROS_FRENTE"] != NULL ? $row["IMU_METROS_FRENTE"] : "",
            "metrosFondo"				=>	$row["IMU_METROS_FONDO"] != NULL ? $row["IMU_METROS_FONDO"] : "",
            "cajonesEstacionamiento"	=>	$row["IMU_CAJONES_ESTACIONAMIENTO"] != NULL ? $row["IMU_CAJONES_ESTACIONAMIENTO"] : "",
            "estadoNombre"				=>	$row["CONS_ESTADO"],
            "ciudadNombre"				=>	$row["CONS_CIUDAD"],
            "coloniaNombre"				=>	$row["CONS_COLONIA"],
            "cpNombre"					=>	$row["CONS_CP"],
            "tipoNombre"				=>	$row["TIN_NOMBRE"],

            "desarrollo"				=>	$row["IMU_DESARROLLO"] != NULL ? $row["IMU_DESARROLLO"] : "",

            "imagenes"					=>	array(),
            "url"                       => '/venta/todos-los-tipos/'.$row["CONS_ESTADO"].'/'.$row["CONS_CIUDAD"].'/'.$row["IMU_ID"]
        );

        $consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ? ORDER BY IIN_ORDEN DESC;";
        $pdo = $conexion->prepare($consulta);
        $pdo->execute(array($inmueble['id']));
        foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $inmueble["imagenes"][] = $urlArchivos.$row["IIN_IMAGEN"];
        }

        $inmuebles[] = $inmueble;
    }

    return $inmuebles;
}


function getFirstFour(){
    $query = 'SELECT *,
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
    FROM INMUEBLE, TIPO_INMUEBLE, USUARIO
    WHERE IMU_TIPO_INMUEBLE = TIN_ID
    AND IMU_LIMITE_VIGENCIA >= \''.date("Y-m-d").'\'
    AND IMU_USUARIO = USU_ID
    AND (
						IF (
							USU_INMOBILIARIA IS NOT NULL,
							(
								SELECT INM_VALIDEZ
								FROM INMOBILIARIA
								WHERE INM_ID = USU_INMOBILIARIA
							) >= '.date("Y-m-d").',
							1
						)
					)
    ORDER BY IMU_CREATE DESC
    LIMIT 4';

    $conexion = crearConexionPDO();
    $inmuebles = array();
    $urlArchivos = "images/images/";

    foreach ($conexion->query($query) as $row) {
        $inmueble = array(
            "id"						=>	$row["IMU_ID"],
            "titulo"					=>	$row["IMU_TITULO"],
            "tipo"						=>	$row["IMU_TIPO_INMUEBLE"],
            "precio"					=>	$row["IMU_PRECIO"],
            "calleNumero"				=>	$row["IMU_CALLE_NUMERO"],
            "estado"					=>	$row["IMU_ESTADO"],
            "ciudad"					=>	$row["IMU_CIUDAD"],
            "colonia"					=>	$row["IMU_COLONIA"],
            "cp"						=>	$row["IMU_CP"],
            "descripcion"				=>	$row["IMU_DESCRIPCION"] != NULL ? $row["IMU_DESCRIPCION"] : "",
            "dimensionTotal"			=>	$row["IMU_DIMENSION_TOTAL"] != NULL ? $row["IMU_DIMENSION_TOTAL"] : "",
            "dimensionConstruida"		=>	$row["IMU_DIMENSION_CONSTRUIDA"] != NULL ? $row["IMU_DIMENSION_CONSTRUIDA"] : "",
            "wcs"						=>	$row["IMU_WCS"] != NULL ? $row["IMU_WCS"] : "",
            "recamaras"					=>	$row["IMU_RECAMARAS"] != NULL ? $row["IMU_RECAMARAS"] : "",
            "metrosFrente"				=>	$row["IMU_METROS_FRENTE"] != NULL ? $row["IMU_METROS_FRENTE"] : "",
            "metrosFondo"				=>	$row["IMU_METROS_FONDO"] != NULL ? $row["IMU_METROS_FONDO"] : "",
            "cajonesEstacionamiento"	=>	$row["IMU_CAJONES_ESTACIONAMIENTO"] != NULL ? $row["IMU_CAJONES_ESTACIONAMIENTO"] : "",
            "estadoNombre"				=>	$row["CONS_ESTADO"],
            "ciudadNombre"				=>	$row["CONS_CIUDAD"],
            "coloniaNombre"				=>	$row["CONS_COLONIA"],
            "cpNombre"					=>	$row["CONS_CP"],
            "tipoNombre"				=>	$row["TIN_NOMBRE"],

            "desarrollo"				=>	$row["IMU_DESARROLLO"] != NULL ? $row["IMU_DESARROLLO"] : "",

            "imagenes"					=>	array(),
            "url"                       => '/venta/todos-los-tipos/'.$row["CONS_ESTADO"].'/'.$row["CONS_CIUDAD"].'/'.$row["IMU_ID"]
        );

        $consulta = "SELECT IIN_IMAGEN FROM IMAGEN_INMUEBLE WHERE IIN_INMUEBLE = ? ORDER BY IIN_ORDEN DESC;";
        $pdo = $conexion->prepare($consulta);
        $pdo->execute(array($inmueble['id']));
        foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $inmueble["imagenes"][] = $urlArchivos.$row["IIN_IMAGEN"];
        }

        $inmuebles[] = $inmueble;
    }

    return $inmuebles;
}


function templateBusqueda($arrayDatosPost = array())
{
    $arrayTipoInmueble = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }


    $arrayEstado = array();

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }


    echo
        "<div class='template_contenedorBusquedaHeader template_contenedorBusquedaAvanzada'>
				<div class='template_campos_select renta'>
					<ul id='template_busqueda_header_transaccion' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='1'>Renta</li>
								<li data-value='3'>Renta Vacacional</li>
								<li data-value='2'>Venta</li>
							</ul>
						</li>
						<p data-value='1'>Renta</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select tipoInmueble'>
					<ul id='template_busqueda_header_tipoInmueble' class='template_campos'>

						<li class='lista'>
							<ul>";


    for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
        echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "' " . (in_array($arrayTipoInmueble[$x]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'>Inmueble</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select  estados'>
					<ul id='template_busqueda_header_estado' class='template_campos'>

						<li class='lista'>
							<ul>";


    for ($x = 0; $x < count($arrayEstado); $x++) {
        echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
    }


    echo
    "</ul>
						</li>
						<p data-value='-1'>Estado</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select municipios'>
					<ul id='template_busqueda_header_municipio' class='template_campos'>

						<li class='lista'>
							<ul></ul>
						</li>
						<p data-value='-1'>Municipio</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select colonias'>
					<ul id='template_busqueda_header_colonia' class='template_campos'>

						<li class='lista'>
							<ul></ul>
						</li>
						<p data-value='-1'>Colonia</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>
				<div class='template_campos_select precio'>
					<ul id='template_busqueda_header_precios_min' class='template_campos'>

						<li class='lista'>
							<ul>
								<li data-value='500' data-transaccion='3'>$500</li>
								<li data-value='1000' data-transaccion='3'>$1,000</li>
								<li data-value='2000' data-transaccion='1,3'>$2,000</li>
								<li data-value='3000' data-transaccion='1'>$3,000</li>
								<li data-value='4000' data-transaccion='1'>$4,000</li>
								<li data-value='5000' data-transaccion='1'>$5,000</li>
								<li data-value='6000' data-transaccion='1'>$6,000</li>
								<li data-value='7000' data-transaccion='1'>$7,000</li>
								<li data-value='8000' data-transaccion='1'>$8,000</li>
								<li data-value='9000' data-transaccion='1'>$9,000</li>
								<li data-value='10000' data-transaccion='1'>$10,000</li>
								<li data-value='11000' data-transaccion='1'>$11,000</li>
								<li data-value='12000' data-transaccion='1'>$12,000</li>
								<li data-value='13000' data-transaccion='1'>$13,000</li>
								<li data-value='14000' data-transaccion='1'>$14,000</li>
								<li data-value='15000' data-transaccion='1'>$15,000</li>
								<li data-value='500000' data-transaccion='2'>$500,000</li>
								<li data-value='600000' data-transaccion='2'>$600,000</li>
								<li data-value='700000' data-transaccion='2'>$700,000</li>
								<li data-value='800000' data-transaccion='2'>$800,000</li>
								<li data-value='900000' data-transaccion='2'>$900,000</li>
								<li data-value='1000000' data-transaccion='2'>$1,000,000</li>
								<li data-value='1500000' data-transaccion='2'>$1,500,000</li>
								<li data-value='2000000' data-transaccion='2'>$2,000,000</li>
								<li data-value='2500000' data-transaccion='2'>$2,500,000</li>
								<li data-value='3000000' data-transaccion='2'>$3,000,000</li>
								<li data-value='3500000' data-transaccion='2'>$3,500,000</li>
								<li data-value='4000000' data-transaccion='2'>$4,000,000</li>
								<li data-value='4500000' data-transaccion='2'>$4,500,000</li>
								<li data-value='5000000' data-transaccion='2'>$5,000,000</li>
								<li data-value='5500000' data-transaccion='2'>$5,500,000</li>
								<li data-value='6000000' data-transaccion='2'>$6,000,000</li>
							</ul>
						</li>
						<p data-value='-1'>Precio</p>
						<input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
					</ul>
				</div>

				<p class='template_campos_select buscar text-center'><a class='buscador btn btn-inmueble btn-lg'><i class='glyphicon glyphicon-search'></i></a></p>
			</div>";
}

function templateSeleccionTransaccion($transaccion){
  switch ($transaccion) {
    case 2: //Renta
      $incre = 1000000;
      for ($x = 0; $x < 5000000; $x += $incre) {
        echo "<li data-value='" . $x . "-" . ($x + $incre) . "'>$" . number_format($x, 0, ".", ",") . " - $" . number_format(($x + $incre), 0, ".", ",") . "</li>";
      }
    ?>
    <li data-value='5000000-7000000'>$5,000,000 - $7,000,000</li>
    <li data-value='7000000-9000000'>$7,000,000 - $9,000,000</li>
    <li data-value='9000000-1000000000'>Más de $9,000,000</li>
      <?php
      break;
    case 1:
      $incre = 2500;

      for ($x = 0; $x < 5000; $x += $incre) {
        echo "<li data-value='".$x."-".($x + $incre)."'>$".number_format($x, 0, ".", ",")." - $".number_format(($x + $incre), 0, ".", ",")."</li>";
      }

      $incre = 5000;

      for ($x = 5000; $x < 30000; $x += $incre) {
        echo "<li data-value='".$x."-".($x + $incre)."'>$".number_format($x, 0, ".", ",")." - $".number_format(($x + $incre), 0, ".", ",")."</li>";
      }
    ?>
    <li data-value='30000-40000'>$30,000 - $40,000</li>
    <li data-value='40000-50000'>$40,000 - $50,000</li>
    <li data-value='50000-1000000000'>Más de $50,000</li>

      <?php
      break;

    case 3:
      $incre = 1000;

      for ($x = 0; $x < 3000; $x += $incre) {
        echo "<li data-value='".$x."-".($x + $incre)."'>$".number_format($x, 0, ".", ",")." - $".number_format(($x + $incre), 0, ".", ",")."</li>";
      }
    ?>
      <li data-value='3000-1000000000'>Más de $3,000</li>

      <?php
      break;

    default:
      # code...
      break;
  }
}

function templateBuscadorResponsive(){
    $arrayTipoInmueble = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }


    $arrayEstado = array();

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }

    ?>
    <!-- buscador activo -->
    
    <div class='template_contenedorBusquedaHeader hidden-print no-padding'>
    	<div class="texto-buscador-index mobile-only">Encuentra tu<br /> propiedad</div>
        <div class='template_campos_select renta'>
            <ul id='template_busqueda_header_transaccion' class='template_campos butt'>
                <li class='lista'>
                    <ul>
                        <li data-value='1'>Renta</li>
                        <li data-value='3'>Renta Vacacional</li>
                        <li data-value='2'>Venta</li>
                    </ul>
                </li>
                <p data-value='-1'>Transacci&oacute;n</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>
        <div class='template_campos_select tipoInmueble'>
            <ul id='template_busqueda_header_tipoInmueble' class='template_campos butt'>
                <li class='lista'>
                    <ul>
                        <?php
                        /*casa*/
                        echo "<li data-value='" . $arrayTipoInmueble[1]["id"] . "' " . (in_array($arrayTipoInmueble[1]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[1]["nombre"] . "</li>";
                        /*Depa*/
                        echo "<li data-value='" . $arrayTipoInmueble[2]["id"] . "' " . (in_array($arrayTipoInmueble[2]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[2]["nombre"] . "</li>";
                        /*Oficina*/
                        echo "<li data-value='" . $arrayTipoInmueble[4]["id"] . "' " . (in_array($arrayTipoInmueble[4]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[4]["nombre"] . "</li>";
                        /*Local*/
                        echo "<li data-value='" . $arrayTipoInmueble[6]["id"] . "' " . (in_array($arrayTipoInmueble[6]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[6]["nombre"] . "</li>";
                        /*Terreno*/
                        echo "<li data-value='" . $arrayTipoInmueble[3]["id"] . "' " . (in_array($arrayTipoInmueble[3]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[3]["nombre"] . "</li>";
                        /*for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
                            echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "' " . (in_array($arrayTipoInmueble[$x]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
                        }*/
                        ?>
                    </ul>
                </li>
                <p data-value='-1'>Inmueble</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>
        <div class='template_campos_select  estados'>
            <ul id='template_busqueda_header_estado' class='template_campos butt'>
                <li class='lista'>
                    <ul>
                        <?php
                        $var=13;
                        echo "<li data-value='" . $arrayEstado[$var]["id"] . "'>" . $arrayEstado[$var]["nombre"] . "</li>";
                        for ($x = 0; $x < count($arrayEstado); $x++) {
                            if($x!=13){
                                echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
                            }
                        }
                        ?>
                    </ul>
                </li>
                <p data-value='-1'>Estado</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>
        <div class='template_campos_select municipios'>
            <ul id='template_busqueda_header_municipio' class='template_campos butt'>
                <li class='lista'>
                    <ul></ul>
                </li>
                <p data-value='-1'>Municipio</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>
        <!--<div class='template_campos_select colonias hidden-xs'>
            <ul id='template_busqueda_header_colonia' class='template_campos'>
                <li class='lista'>
                    <ul></ul>
                </li>
                <p data-value='-1'>Colonia</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>-->

        <!--<div class='template_campos_select precio hidden-xs'>
            <ul id='template_busqueda_header_precios_min' class='template_campos'>
                <li class='lista'>
                    <ul>-->
                        <?php/*
                        templateSeleccionTransaccion($_SESSION[userFiltros]['transaccion']);*/
                        ?>
                    <!--</ul>
                </li>
                <p data-value='-1'>Precio</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>-->

        <p class='textBuscar template_campos_select buscar' onclick='template_buscar();'>
            <a class='buscador btn btn-inmueble btn-lg'><i class='glyphicon glyphicon-search'></i></a>
        </p>

    </div>

    <?php
}

function templateVotacionUsuario($inmobiliaria, $usuarioCalificado){
    $conexion = crearConexionPDO();


    $inmobiliaria = isset($inmobiliaria) ? $inmobiliaria : -1;
    $usuarioCalificado = isset($usuarioCalificado) ? $usuarioCalificado : -1;
    $usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;


    $arrayInformativo = array();
    $arrayComentarios = array();
    $arrayCondiciones = array();
    $consulta = "";
    $urlArchivos = "images/images/";


    if ($inmobiliaria != -1) {//inmobiliaria
        $consulta = "SELECT INM_NOMBRE_EMPRESA, INM_CREATE, INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
        $pdo = $conexion->prepare($consulta);
        $pdo->execute(array($inmobiliaria));
        $res = $pdo->fetchAll(PDO::FETCH_ASSOC);
        $row = $res[0];


        $arrayInformativo = array(
            "id"			=>	$inmobiliaria,
            "nombre"		=>	$row["INM_NOMBRE_EMPRESA"],
            "create"		=>	getDateNormal($row["INM_CREATE"]),
            "imagen"		=>	$row["INM_LOGOTIPO"] != NULL ? $urlArchivos.$row["INM_LOGOTIPO"] : "",
            "calificaciones"=>	0
        );


        $consulta =
            "SELECT
				VIN_ID AS CONS_ID,
				VIN_USUARIO AS CONS_USUARIO_ID,
				VIN_CALIFICACION AS CONS_CALIFICACION,
				VIN_COMENTARIO AS CONS_COMENTARIO,
				USU_NOMBRE AS CONS_USUARIO_NOMBRE,
				USU_IMAGEN AS CONS_USUARIO_IMAGEN,
				USU_FBID AS CONS_USUARIO_FBID
			FROM VOTACION_INMOBILIARIA, USUARIO
			WHERE VIN_INMOBILIARIA = ?
			AND USU_ID = VIN_USUARIO
			ORDER BY VIN_ID DESC;";

        $arrayCondiciones = array($inmobiliaria);
    }
    else {//usuario
        $consulta = "SELECT USU_NOMBRE, USU_CREATE, USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
        $pdo = $conexion->prepare($consulta);
        $pdo->execute(array($usuarioCalificado));
        $res = $pdo->fetchAll(PDO::FETCH_ASSOC);
        $row = $res[0];


        $arrayInformativo = array(
            "id"			=>	$usuarioCalificado,
            "nombre"		=>	$row["USU_NOMBRE"],
            "create"		=>	getDateNormal($row["USU_CREATE"]),
            "imagen"		=>	$row["USU_IMAGEN"] != NULL ? $urlArchivos.$row["USU_IMAGEN"] : "",
            "calificaciones"=>	0
        );


        $consulta =
            "SELECT
				VUS_ID AS CONS_ID,
				VUS_USUARIO_CALIFICADOR AS CONS_USUARIO_ID,
				VUS_CALIFICACION AS CONS_CALIFICACION,
				VUS_COMENTARIO AS CONS_COMENTARIO,
				USU_NOMBRE AS CONS_USUARIO_NOMBRE,
				USU_IMAGEN AS CONS_USUARIO_IMAGEN,
				USU_FBID AS CONS_USUARIO_FBID
			FROM VOTACION_USUARIO, USUARIO
			WHERE VUS_USUARIO_CALIFICADO = ?
			AND USU_ID = VUS_USUARIO_CALIFICADOR
			ORDER BY VUS_ID DESC;";

        $arrayCondiciones = array($usuarioCalificado);
    }
    $pdo = $conexion->prepare($consulta);
    $pdo->execute($arrayCondiciones);
    foreach ($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $arrayComentarios[] = array(
            "id"			=>	$row["CONS_ID"],
            "calificacion"	=>	$row["CONS_CALIFICACION"],
            "comentario"	=>	$row["CONS_COMENTARIO"],
            "usuario"		=>	array(
                "id"		=>	$row["CONS_USUARIO_ID"],
                "nombre"	=>	$row["CONS_USUARIO_NOMBRE"],
                "imagen"	=>	$row["CONS_USUARIO_FBID"] != NULL ? "https://graph.facebook.com/".$row["CONS_USUARIO_FBID"]."/picture?type=large" : ($row["CONS_USUARIO_IMAGEN"] != NULL ? $urlArchivos.$row["CONS_USUARIO_IMAGEN"] : "images/userSinFoto.png"),
                "FBId"		=>	$row["CONS_USUARIO_FBID"] != NULL ? $row["CONS_USUARIO_FBID"] : ""
            )
        );
    }


    $arrayInformativo["calificaciones"] = count($arrayComentarios);


    $cadena =
        "<h1>Calificaciones de".($inmobiliaria != -1 ? " la Inmobiliaria" : "l Usuario")."</h1>
		<div class='infoDescriptivo'>
			<table>
				<tbody>
					<tr>
						".($arrayInformativo["imagen"] != "" ? ("<td class='imagen'><img src='".$arrayInformativo["imagen"]."' alt='".$arrayInformativo["nombre"]."' /></td>") : "")."
						<td>".$arrayInformativo["nombre"]."<br /><strong>Miembro desde: </strong>".$arrayInformativo["create"]."<br />".$arrayInformativo["calificaciones"]." Calificacion".($arrayInformativo["calificaciones"] > 1 ? "es" : "")."</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='contenedorMensajes'>";


    for ($x = 0; $x < count($arrayComentarios); $x++) {
        $cadena.=
            "<div class='contenedorMensaje' data-id='".$arrayComentarios[$x]["id"]."' data-tipo='".($inmobiliaria != -1 ? "inmobiliaria" : "usuario")."'>
				<table>
					<tbody>
						<tr>
							<td class='imagen'><img src='".$arrayComentarios[$x]["usuario"]["imagen"]."' /></td>
							<td class='nombre'>".$arrayComentarios[$x]["usuario"]["nombre"]."</td>
							<td class='contenedorEstrellas' data-calificacion='".$arrayComentarios[$x]["calificacion"]."'>";


        for ($y = 0; $y < 5; $y++) {
            $cadena.= "<a class='_estrella ".(($y + 1) <= $arrayComentarios[$x]["calificacion"] ? "_100" : "")."' data-value='".($y + 1)."'>".($y + 1)."</a>";
        }


        $cadena.=
            "</tr>
						<tr>
							<td class='texto' colspan='3'>".$arrayComentarios[$x]["comentario"]."</td>
						</tr>";


        if ($usuario == $arrayComentarios[$x]["usuario"]["id"]) {
            $cadena.=
                "<tr>
							<td class='eliminar' colspan='3'><span>Eliminar Calificación</span></td>
						</tr>";
        }

        $cadena.=
            "</tbody>
				</table>
			</div>";
    }


    $cadena.=
        "</div>";

    return $cadena;
}
?>
<?php
function templateBuscadorResponsive2(){
    $arrayTipoInmueble = array();

    $conexion = crearConexionPDO();
    $consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayTipoInmueble[] = array(
            "id" => $row["TIN_ID"],
            "nombre" => $row["TIN_NOMBRE"]
        );
    }


    $arrayEstado = array();

    $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
    foreach ($conexion->query($consulta) as $row) {
        $arrayEstado[] = array(
            "id" => $row["EST_ID"],
            "nombre" => $row["EST_NOMBRE"]
        );
    }

    ?>
    <!-- buscador activo -->
    <div class='template_contenedorBusquedaHeader2 hidden-print no-padding'>
        <div class="texto-buscador-index mobile-only">Encuentra tu<br /> propiedad</div>
        <div class='template_campos_select renta'>
            <ul id='template_busqueda_header_transaccion' class='template_campos'>
                <li class='lista'>
                    <ul>
                        <li data-value='1'>Renta</li>
                        <li data-value='3'>Renta Vacacional</li>
                        <li data-value='2'>Venta</li>
                    </ul>
                </li>
                <p data-value='-1'>Transacci&oacute;n</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div><!--<br/><br/><br/>-->
        <div class='template_campos_select tipoInmueble'>
            <ul id='template_busqueda_header_tipoInmueble' class='template_campos'>
                <li class='lista'>
                    <ul>
                          <?php
                        /*casa*/
                        echo "<li data-value='" . $arrayTipoInmueble[1]["id"] . "' " . (in_array($arrayTipoInmueble[1]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[1]["nombre"] . "</li>";
                        /*Depa*/
                        echo "<li data-value='" . $arrayTipoInmueble[2]["id"] . "' " . (in_array($arrayTipoInmueble[2]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[2]["nombre"] . "</li>";
                        /*Oficina*/
                        echo "<li data-value='" . $arrayTipoInmueble[4]["id"] . "' " . (in_array($arrayTipoInmueble[4]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[4]["nombre"] . "</li>";
                        /*Local*/
                        echo "<li data-value='" . $arrayTipoInmueble[6]["id"] . "' " . (in_array($arrayTipoInmueble[6]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[6]["nombre"] . "</li>";
                        /*Terreno*/
                        echo "<li data-value='" . $arrayTipoInmueble[3]["id"] . "' " . (in_array($arrayTipoInmueble[3]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[3]["nombre"] . "</li>";
                        /*for ($x = 0; $x < count($arrayTipoInmueble); $x++) {
                            echo "<li data-value='" . $arrayTipoInmueble[$x]["id"] . "' " . (in_array($arrayTipoInmueble[$x]["id"], array(1, 2)) ? "data-transaccion='3'" : "") . ">" . $arrayTipoInmueble[$x]["nombre"] . "</li>";
                        }*/
                        ?>
                    </ul>
                </li>
                <p data-value='-1'>Inmueble</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div><!--<br/><br/><br/>-->
        <div class='template_campos_select  estados'>
            <ul id='template_busqueda_header_estado' class='template_campos'>
                <li class='lista'>
                    <ul>
                        <?php
                         $var=13;
                        echo "<li data-value='" . $arrayEstado[$var]["id"] . "'>" . $arrayEstado[$var]["nombre"] . "</li>";
                        for ($x = 0; $x < count($arrayEstado); $x++) {
                            if($x!=13){
                                echo "<li data-value='" . $arrayEstado[$x]["id"] . "'>" . $arrayEstado[$x]["nombre"] . "</li>";
                            }
                        }
                        ?>
                    </ul>
                </li>
                <p data-value='-1'>Estado</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div><!--<br/><br/><br/>-->
        <div class='template_campos_select municipios'>
            <ul id='template_busqueda_header_municipio' class='template_campos'>
                <li class='lista'>
                    <ul></ul>
                </li>
                <p data-value='-1'>Municipio</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div><!--<br/><br/><br/>-->
        <div class='template_campos_select colonias hidden-xs'>
            <ul id='template_busqueda_header_colonia' class='template_campos'>
                <li class='lista'>
                    <ul></ul>
                </li>
                <p data-value='-1'>Colonia</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>

        <div class='template_campos_select precio hidden-xs'>
            <ul id='template_busqueda_header_precios_min' class='template_campos'>
                <li class='lista'>
                    <ul>
                        <?php

                        templateSeleccionTransaccion($_SESSION[userFiltros]['transaccion']);
                        ?>
                    </ul>
                </li>
                <p data-value='-1'>Precio</p>
                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' readonly='true' />
            </ul>
        </div>

        <p class='textBuscar template_campos_select buscar' onclick='template_buscar();'>
            <a class='buscador btn btn-inmueble btn-lg'><i class='glyphicon glyphicon-search'></i> BUSCAR</a>
        </p>

    </div>

    <?php
}
?>