<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	$usuario = array();
	
	
	$conexion = crearConexionPDO();
	$consulta = "SELECT USU_NOMBRE, USU_EMAIL, USU_SEXO, USU_FECHANACIMIENTO, USU_TELEFONO1, USU_TELEFONO2, USU_CALLE_NUMERO, USU_ESTADO, USU_CIUDAD, USU_COLONIA, USU_CP, USU_INMOBILIARIA, USU_IMAGEN FROM USUARIO WHERE USU_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($_SESSION[userId]));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$usuario = array(
		"id"			=>	$_SESSION[userId],
		"nombre"		=>	$row["USU_NOMBRE"],
		"email"			=>	$row["USU_EMAIL"],
		"sexo"			=>	$row["USU_SEXO"],
		"fechaNac"		=>	$row["USU_FECHANACIMIENTO"] != NULL ? getDateNormal($row["USU_FECHANACIMIENTO"]) : "",
		"telefono1"		=>	$row["USU_TELEFONO1"] != NULL ? $row["USU_TELEFONO1"] : "",
		"telefono2"		=>	$row["USU_TELEFONO2"] != NULL ? $row["USU_TELEFONO2"] : "",
		"calleNumero"	=>	$row["USU_CALLE_NUMERO"] != NULL ? $row["USU_CALLE_NUMERO"] : "",
		"estado"		=>	$row["USU_ESTADO"] != NULL ? $row["USU_ESTADO"] : -1,
		"ciudad"		=>	$row["USU_CIUDAD"] != NULL ? $row["USU_CIUDAD"] : -1,
		"colonia"		=>	$row["USU_COLONIA"] != NULL ? $row["USU_COLONIA"] : -1,
		"cp"			=>	$row["USU_CP"] != NULL ? $row["USU_CP"] : -1,
		"inmobiliaria"	=>	$row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : "",
		"imagen"		=>	$row["USU_IMAGEN"] != NULL ? $urlArchivos.$row["USU_IMAGEN"] : ""
	);
	
	
	$arrayEstado = array();
	
	$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayEstado[] = array(
			"id"	=>	$row["EST_ID"],
			"nombre"=>	$row["EST_NOMBRE"]
		);
	}
	
	
	$inmobiliaria = array();
	if ($_SESSION[userInmobiliaria] != 0) {
		$consulta = "SELECT INM_NOMBRE_EMPRESA, INM_RFC, INM_LOGOTIPO FROM INMOBILIARIA WHERE INM_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($_SESSION[userInmobiliaria]));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$inmobiliaria = array(
			"id"			=>	$_SESSION[userInmobiliaria],
			"nombreEmpresa"	=>	$row["INM_NOMBRE_EMPRESA"],
			"rfc"			=>	$row["INM_RFC"] != NULL ? $row["INM_RFC"] : "",
			"logotipo"		=>	$row["INM_LOGOTIPO"] != NULL ? $urlArchivos.$row["INM_LOGOTIPO"] : ""
		);
	}
	
	
	$variables = "";
	
	if ($usuario["estado"] != -1) {
		$variables.= "post_usuario_estado=".$usuario["estado"];
		$variables.= ",post_usuario_ciudad=".$usuario["ciudad"];
		$variables.= ",post_usuario_colonia=".$usuario["colonia"];
	}
	
	
	CabeceraHTML("perfil_ver3.css,perfil_ver7.js", $variables);
	CuerpoHTML();
?>
<div class="perfil_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
    	<div id="lk_miPerfil">
        	<p class="titulo">Mi Perfil</p>
            <form id="subirPerfil" method="post" enctype="multipart/form-data" action="lib_php/updUsuario.php">
            	<input type="text" name="modificar" value="1" style="display:none;" />
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <p class="subtitulo">Perfil Personalizado</p>
                                <div class="contenedorCampos">
                                    <p>Nombre</p>
                                    <input type="text" id="perfil_nombre" name="nombre" class="template_campos" placeholder="Nombre" value="<?php echo $usuario["nombre"]; ?>" maxlength="128" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <p>Fecha de Nacimiento</p>
                                    <?php
										$partes = $usuario["fechaNac"] != "" ? explode("/", $usuario["fechaNac"]) : array("", "", "");
									?>
                                    <input type="text" id="perfil_fechaNacDia" class="template_campos" placeholder="DD" maxlength="2" style="width:40px;" value="<?php echo $partes["0"]; ?>" />/
                                    <input type="text" id="perfil_fechaNacMes" class="template_campos" placeholder="MM" maxlength="2" style="width:40px;" value="<?php echo $partes["1"]; ?>" />/
                                    <input type="text" id="perfil_fechaNacYear" class="template_campos" placeholder="YYYY" maxlength="4" style="width:60px;" value="<?php echo $partes["2"]; ?>" />
                                    <input type="text" id="perfil_fechaNac" name="fechaNac" class="template_campos" placeholder="Fecha de Nacimiento" value="<?php echo $usuario["fechaNac"]; ?>" maxlength="12" style="display:none;" />
                                </div><div class="contenedorCampos columnas">
                                    <p>Email</p>
                                    <input type="text" id="perfil_email" name="email" disabled="disabled" class="template_campos" placeholder="Email" value="<?php echo $usuario["email"]; ?>" maxlength="64" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <p>Teléfono</p>
                                    <input type="text" id="perfil_telefono1" name="telefono1" class="template_campos" placeholder="Teléfono" value="<?php echo $usuario["telefono1"]; ?>" maxlength="16" />
                                </div><div class="contenedorCampos columnas">
                                    <p>Celular</p>
                                    <input type="text" id="perfil_telefono2" name="telefono2" class="template_campos" placeholder="Celular" value="<?php echo $usuario["telefono2"]; ?>" maxlength="16" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <p>Sexo</p>
                                    <input type="radio" name="sexo" value="H" <?php echo $usuario["sexo"] == "H" ? "checked='checked'" : ""; ?> style="margin-right:10px;" />Hombre
                                </div><div class="contenedorCampos columnas">
                                    <p>&nbsp;</p>
                                    <input type="radio" name="sexo" value="M" <?php echo $usuario["sexo"] == "M" ? "checked='checked'" : ""; ?> style="margin-right:10px;" />Mujer
                                </div>
                                <div class="contenedorCampos" style="padding-top:90px;">
                                    <p id="btnGuardar" class="subtitulo guardar" onclick="perfil_validarCampos();"><a class="btnBotones guardar">Buscar</a>Guardar Cambios</p>
                                    <p id="mensajeTemporal" style="display:none;">Espere un momento...</p>
                                </div>
                            </td>
                            <td>
                            	<p class="subtitulo">&nbsp;</p>
                            	<div class="contenedorCampos">
                                    <p>Calle y Número</p>
                                    <input type="text" id="perfil_calleNumero" name="calleNumero" class="template_campos" placeholder="Calle y Número" value="<?php echo $usuario["calleNumero"]; ?>" maxlength="64" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <ul id="perfil_estado" class="template_campos">
                                        Estado<span></span>
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
                                    <input type="text" id="_perfilEstado" name="estado" value="" style="display:none;" />
                                </div><div class="contenedorCampos columnas">
                                    <ul id="perfil_ciudad" class="template_campos">
                                        Ciudad<span></span>
                                        <li class="lista">
                                            <ul></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                        <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                    </ul>
                                    <input type="text" id="_perfilCiudad" name="ciudad" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="perfil_colonia" class="template_campos">
                                        Colonia<span></span>
                                        <li class="lista">
                                            <ul></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                        <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                    </ul>
                                    <input type="text" id="_perfilColonia" name="colonia" value="" style="display:none;" />
                                    <input type="text" id="_perfilCP" name="cp" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <p>Imágen</p>
                                    <div class="imagen"><?php
                                        echo $usuario["imagen"] != "" ? "<img src='".$usuario["imagen"]."' class='logotipo' />" : "";
                                    ?></div>
                                    <input type="file" id="perfil_imagen" name="imagen" class="template_campos" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
			if (count($inmobiliaria) > 0) {
				echo
					"<div id='lk_inmobiliaria'>
						<p class='titulo'>Datos de Inmobiliaria</p>
						<form id='subirInmobiliaria' method='post' enctype='multipart/form-data' action='lib_php/updInmobiliaria.php'>
							<input type='text' id='idInmobiliaria' name='id' value='".$inmobiliaria["id"]."' style='display:none;' />
							<input type='text' name='modificar' value='1' style='display:none;' />
							<table>
								<tbody>
									<tr>
										<td>
											<div class='contenedorCampos'>
												<p>Nombre de la Inmobiliaria</p>
												<input type='text' id='perfil_empresa' name='nombreEmpresa' class='template_campos' placeholder='Nombre de la inmobiliaria' value='".$inmobiliaria["nombreEmpresa"]."' maxlength='128' ".($_SESSION[userAdminInmobiliaria] == 0 ? "disabled='disabled'" : "")." />
											</div>
											<div class='contenedorCampos'>
												<p>RFC</p>
												<input type='text' id='perfil_rfc' name='rfc' class='template_campos' placeholder='RFC' value='".$inmobiliaria["rfc"]."' maxlength='32' ".($_SESSION[userAdminInmobiliaria] == 0 ? "disabled='disabled'" : "")." />
											</div>";
											
											
			if ($_SESSION[userAdminInmobiliaria] == 1) {
				echo
											"<div class='contenedorCampos' style='padding-top:20px;'>
												<p id='btnGuardar2' class='subtitulo guardar' onclick='perfil_validarCamposInmobiliaria();'><a class='btnBotones guardar'>Buscar</a>Guardar Cambios</p>
												<p id='mensajeTemporal2' style='display:none;'>Espere un momento...</p>
											</div>";
			}
			
			
			echo
										"</td>
										<td>
											<div class='contenedorCampos'>
												<p>Logotipo</p>
												<div class='imagen'>
													".($inmobiliaria["logotipo"] != "" ? "<img src='".$inmobiliaria["logotipo"]."' class='logotipo' />" : "")."
												</div>
												".($_SESSION[userAdminInmobiliaria] == 1 ? "<input type='file' id='perfil_logotipo' name='logotipo' class='template_campos' />" : "")."
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div>";
			}
        ?>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales("perfil_cerrarPopUp");
?>
<?php
	if (isset($_GET["errorNuevoAnuncio"])) {
		echo
			"<div id='perfil_errorNuevoAnuncio' class='templatePopUp perfil_errorNuevoAnuncio'>
				<span class='btnCerrar' onclick='template_principalCerrarPopUp(perfil_cerrarPopUp);'>x</span>
				<table>
					<tbody>
						<tr>
							<td>";
		
							
		if (($usuario["telefono1"] == "") && ($usuario["telefono2"] == ""))
			echo "<p>Debes capturar Teléfono ó Celular para poder publicar un anuncio</p>";
			
		if ($usuario["calleNumero"] == "")
			echo "<p>Debes capturar Calle y Número para poder publicar un anuncio</p>";
			
		if ($usuario["estado"] == -1)
			echo "<p>Debes capturar Estado para poder publicar un anuncio</p>";
			
		if ($usuario["ciudad"] == -1)
			echo "<p>Debes capturar Ciudad para poder publicar un anuncio</p>";
			
		if ($usuario["colonia"] == -1)
			echo "<p>Debes capturar Colonia para poder publicar un anuncio</p>";
		
							
		echo
							"</td>
						</tr>
					</tbody>
				</table>
			</div>";
	}
?>
<?php
	FinHTML();
?>