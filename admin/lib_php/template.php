<?php

	/*
		Valida que el tipo de login corresponda con el tipo de acceso permitido con el valor 1ue se recibe
		por parametro.
		Por default el valor de acceso es para tipo administrador. En caso de no tener las credenciales permitidas
		entonces se devuelve a la pagina principal.
		
			* tipo:	Integer, tipo de usuario
			* tipo: Array, un arreglo de valores enteros, que son el tipo de usuario
	*/
	function validar_credenciales($tipo = 1) {
		if (isset($_SESSION[adminId])) {
			if (is_array($tipo)) {
				for ($x = 0; $x < count($tipo); $x++) {
					if ($tipo[$x] == $_SESSION[adminTipo])
						return;
				}
				header("Location: ../index.php");
			}
			else {
				if ($_SESSION[adminTipo] != $tipo)
					header("Location: ../index.php");
			}
		}
		else
			header("Location: ../index.php");
	}
	
	
	/*********************************************************************************************
		funciones que imprimen el cuerpo general en todas las interfaces
	*********************************************************************************************/

	/*
		Muestra las cabeceras de html de todas las interfaces.
		En todas las cabeceras no es necesario cargar los siguientes archivos:
		
			google fonts Open Sans, template.css, jQuery.js, template.js, validaciones.js, md5Script.js, objFocusBlur.js
		
		Los valores recibidos por parametros son:
		
			* cssJs: 			String, todos los estilos que se vayan agregando, deben ser separados por comas junto con
								los archivos de script. Solo se pondran los nombres (sin ruta) de los archivos. Todos los
								archivos de estilos van en /css y los de script en /js
			* varJs:			String, son todas las variables que necesitamos inicializar desde el php. Se pueden recibir
								varias variables con sus valores, separados con comas "," y debe ir el nombre de la variable
								seguido del signo igual "=" y enseguida el valor a asignarle.
								Ejemplo: cadena='hola',numero=8
					
	*/
	function adminCabeceraHTML($cssJs = NULL, $varJs = false) {
		echo
            "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
            <html xmlns='http://www.w3.org/1999/xhtml'>
                <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                    <title>Explora Inmuebles</title>
					<link rel='icon' type='image/png' href='../images/logoIcon.png' />
					<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,600italic,700,700italic' rel='stylesheet' type='text/css'>
					<link rel='stylesheet' type='text/css' href='../css/picker.css' />
					<link rel='stylesheet' type='text/css' href='../css/pickerDate.css' />
					<link rel='stylesheet' type='text/css' href='../css/pickerTime.css' />
					<link rel='stylesheet' type='text/css' href='css/template.css' />
					<script language='javascript' type='text/javascript' src='../js/md5Script.js'></script>
                    <script language='javascript' type='text/javascript' src='../js/validaciones.js'></script>
					<script language='javascript' type='text/javascript' src='../js/jQuery.js'></script>
					<script language='javascript' type='text/javascript' src='../js/jQueryForm.js'></script>
					<script language='javascript' type='text/javascript' src='../js/picker.js'></script>
					<script language='javascript' type='text/javascript' src='../js/pickerDate.js'></script>
					<script language='javascript' type='text/javascript' src='../js/pickerTime.js'></script>
					<script language='javascript' type='text/javascript' src='../js/translations/es_ES.js'></script>
					<script language='javascript' type='text/javascript' src='js/template_ver2.js'></script>";
				
		if ($cssJs != NULL) {
			$archivos = explode(",", $cssJs);
			for ($x = 0; $x < count($archivos); $x++) {
				$extencion = explode(".", $archivos[$x]);
				switch($extencion[1]) {
					case "css":
								echo "<link rel='stylesheet' type='text/css' href='css/".$archivos[$x]."' />";
								break;
					case "js":
								echo "<script language='javascript' type='text/javascript' src='js/".$archivos[$x]."'></script>";
								break;
				}
			}
		}
		
		if ($varJs != NULL) {
			echo
					"<script language='javascript' type='text/javascript'>";
					
			$partes = explode(",", $varJs);
			for ($x = 0; $x < count($partes); $x++) {
				$variables = explode("=", $partes[$x]);
				
				echo
						"var ".$variables[0]." = ".$variables[1].";";
			}
			
			echo
					"</script>";
		}
		
		echo
				"</head>";
	}
	
	
	/*
		Muestra el cuerpo que es igual en todas las interfaces.
		Los valores recividos por parametros son:
		
			* fncLoading: 	String, es el nombre de la funcion que cargara en el loading del body. Se debe poner parentesis a la funcion;
							tambien es posible enviar parametros a la funcion
			
	*/
	function adminCuerpoHTML($fncLoading = NULL) {
		echo
				"<body ".($fncLoading != NULL ? ("onload='".$fncLoading."'") : "").">
					<div class='cabecera'>
						<div class='cabeceraContenedor'>
							<img src='../images/logo.png' class='logo' />
							<div class='cabeceraContenedor_contenedorLogueado'>
								<table>
									<tbody>
										<tr onclick='generalMostrarOcultar_contenedorLogueadoDesplegable();'>
											<td style='cursor:pointer;'>".$_SESSION[adminLogin]."</td>
											<td style='cursor:pointer;'><img src='images/dropdown.png' /></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div id='contenedorLogueadoDesplegable' class='cabeceraContenedor_contenedorLogueadoDesplegable' style='visibility:hidden;'>
								<table>
									<tbody>
										<tr>
											<td onclick='generalMostrarOcultar_contenedorLogueadoDesplegable();template_abrirModificarPassword(".$_SESSION[adminId].");'>Cambiar Contraseña</td>
										</tr>
										<tr>
											<td onclick='gotoURL(\"../lib_php/cerrarSession.php?tipo=".$_SESSION[adminTipo]."\");'>Cerrar sesión</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>";
		
	}
	
	/*
		Muestra el main, titulo de la interfaz, y los titulos de la informacion a mostrar. Es igual en todas las interfaces
		Los datos diferentes son el titulo, la informacion, el ancho en cada una de ellas y el boton de agregar mas.
		
			* tituloInterfaz:		String, es el titulo de la interfaz a mostrar
			* arrayCamposWidth:		Array, es un arreglo de keys: "titulo" (String, es el titulo del campo a mostrar) y "width" (Integer, es el ancho del campo a mostrar,
									tambien puede ser NULL y el ancho se ajustara automaticamente, dependiendo del ancho de los demas campos).
									
									$arrayCamposWidth = array(
										"titulo"	=>	"String",
										"width"		=>	Integer o NULL
									);
									
			* isAgregar:			Boolean, en "true" si se quiere mostrar el signo de mas para agregar mas tuplas a la base de datos; false para no mostrar el campo del
									signo de mas. Por default es true
			* isFiltro:				Boolean, en "true" si se quiere mostrar un filtro para hacer una busqueda; false para no mostrar el filtro. Por default es false
									
	*/
	function adminMainHTML($tituloInterfaz, $arrayCamposWidth, $isAgregar = true, $isFiltro = false) {
		echo
					"<div id='main'>
						<table class='main_table'>
							<tbody>
								<tr height='50'>
									<td colspan='".(count($arrayCamposWidth) + 1)."' class='titulo'>".$tituloInterfaz."</td>
								</tr>".
								(
									$isFiltro
										? (
											"<tr id='template_celdaBuscador'>
												<td colspan='".(count($arrayCamposWidth) + 1)."' style='text-align:center; padding-top:5px;'><input type='text' id='template_buscador' class='ObjFocusBlur' placeholder='Buscar...' style='width:95%;' /></td>
											</tr>"
										)
										: ""
								).
								(
									$isAgregar
									? (
										"<tr height='35'>
											<td colspan='".(count($arrayCamposWidth) + 1)."'><img src='images/btnAgregar.png' onclick='abrirModificarCampos(-1);' style='cursor:pointer;' /></td>
										</tr>"
									)
									: ""
								).
								"<tr id='template_nombreCampos' height='35'>";
								
		for ($x = 0; $x < count($arrayCamposWidth); $x++) {
			echo
									"<td ".($arrayCamposWidth[$x]["width"] != NULL ? ("width='".$arrayCamposWidth[$x]["width"]."'") : "")." style='border-top:1px solid #fff; ".($x != 0 ? "text-align:center;" : "")."'>".$arrayCamposWidth[$x]["titulo"]."</td>";
		}
		
		echo
									"<td width='30' style='border-top:1px solid #fff; text-align:right;'></td>
								</tr>
								<tr height='400'>
									<td colspan='".(count($arrayCamposWidth) + 1)."' style='border-bottom:1px solid #012851;'>
										<div id='contenedorConsulta' style='width:100%; height:400px; overflow:auto;'></div>
									</td>
								</tr>
								<tr id='template_celdaPaginacion' height='25' style='display:none;'>
									<td colspan='".(count($arrayCamposWidth) + 1)."' style='border-top:1px solid #fff;'>
										<div id='sistemaPaginacion' class='template_sistemaPaginacion'></div>
									</td>
								</tr>
								<tr height='25'>
									<td colspan='".(count($arrayCamposWidth) + 1)."' style='border-top:1px solid #fff;'>
										<div id='resultados' style='width:100%; height:25px;'></div>
									</td>
								</tr>
								<tr height='25'>
									<td colspan='".(count($arrayCamposWidth) + 1)."' style='text-align:right;'><div class='btnOpciones' onClick='gotoURL(\"menu.php\");' style='float:right;'>Regresar</div></td>
								</tr>
							</tbody>
						</table>
					</div>";
	}
	
	/*
		Todos los popups que van en cada interfaz, ademas de la mascara principal.
		Se recibe por parametro el nombre de la funcion a ejecutar despues de cerrar el popup general.
		PopUps Generales:
		
			* fcn:	String, nombre de la funcion a ejecutar despues de cerrar el popup general
	*/
	function adminPopUpsGenerales($fcn = NULL) {
		echo
			"<div id='mascaraPrincipal' class='backInv' onclick='principalCerrarPopUp(".($fcn != NULL ? $fcn : "").");'></div>
			<div id='template_abrirModificarPassword' class='classPopUp'>
				<table>
					<tbody>
						<tr height='50'>
							<td style='font-size:18px; border-bottom:1px solid #012851;'>Modificar Contraseña</td>
						</tr>
						<tr height='35'>
							<td><input type='password' id='template_oldPassword' maxlength='32' class='ObjFocusBlur' placeholder='Contraseña Anterior' />*</td>
						</tr>
						<tr height='35'>
							<td><input type='password' id='template_newPassword' maxlength='32' class='ObjFocusBlur' placeholder='Nueva Contraseña' />*</td>
						</tr>
						<tr height='35'>
							<td><input type='password' id='template_confPassword' maxlength='32' class='ObjFocusBlur' placeholder='Confirmar' />*</td>
						</tr>
						<tr height='50'>
							<td>
								<div id='template_btnGuardar' class='btnOpciones' onClick='template_validarCampos();'>Guardar</div>
								<span id='template_mensajeTemporal' style='display:none;'>Espere un momento...</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>";
	}
	
	/*
		Termina el body y html en todas las interfaces
	*/
	function adminFinHTML() {
		echo
				"</body>
			</html>";
	}
	

?>