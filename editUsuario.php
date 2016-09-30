<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
	if ($_SESSION[userAdminInmobiliaria] == 0)
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	$usuario = array();
	$conexion = crearConexionPDO();
	
	
	if (isset($_POST["id"])) {
		$consulta = "SELECT USU_NOMBRE, USU_EMAIL, USU_SEXO, USU_FECHANACIMIENTO, USU_TELEFONO1, USU_TELEFONO2, USU_CALLE_NUMERO, USU_ESTADO, USU_CIUDAD, USU_COLONIA, USU_CP, USU_IMAGEN, USU_NOTIFICACIONES FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($_POST["id"]));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$usuario = array(
			"id"			=>	$_POST["id"],
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
			"imagen"		=>	$row["USU_IMAGEN"] != NULL ? $urlArchivos.$row["USU_IMAGEN"] : "",
			"notificaciones"=>	$row["USU_NOTIFICACIONES"]
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
	
	
	$variables = "";
	
	if (count($usuario) > 0) {
		if ($usuario["estado"] != -1) {
			$variables.= "post_usuario_estado=".$usuario["estado"];
			$variables.= ",post_usuario_ciudad=".$usuario["ciudad"];
			$variables.= ",post_usuario_colonia=".$usuario["colonia"];
		}
	}
	
	
	CabeceraHTML("editUsuario_ver2.css,editUsuario.js", $variables);
	CuerpoHTML();
?>
<div class="editUsuario_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
    	<div id="editarPerfil">
        	<p class="titulo">Nuevo Usuario</p>
            <form id="subirPerfilInmobiliaria" method="post" enctype="multipart/form-data" action="lib_php/updUsuarioInmobiliaria.php">
            	<input type="text" id="idUsuario" name="id" value="<?php echo count($usuario) > 0 ? $usuario["id"] : -1; ?>" style="display:none;" />
            	<input type="text" name="<?php echo count($usuario) > 0 ? "modificar" : "nuevo"; ?>" value="1" style="display:none;" />
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <div class="contenedorCampos">
                                    <p>Nombre*</p>
                                    <input type="text" id="edit_nombre" name="nombre" class="template_campos" placeholder="Nombre" value="<?php echo count($usuario) > 0 ? $usuario["nombre"] : ""; ?>" maxlength="128" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <p>Fecha de Nacimiento</p>
                                    <?php
										$partes = count($usuario) > 0 ? ($usuario["fechaNac"] != "" ? explode("/", $usuario["fechaNac"]) : array("", "", "")) : array("", "", "");
									?>
                                    <input type="text" id="edit_fechaNacDia" class="template_campos" placeholder="DD" maxlength="2" style="width:40px;" value="<?php echo $partes["0"]; ?>" />/
                                    <input type="text" id="edit_fechaNacMes" class="template_campos" placeholder="MM" maxlength="2" style="width:40px;" value="<?php echo $partes["1"]; ?>" />/
                                    <input type="text" id="edit_fechaNacYear" class="template_campos" placeholder="YYYY" maxlength="4" style="width:60px;" value="<?php echo $partes["2"]; ?>" />
                                    <input type="text" id="edit_fechaNac" name="fechaNac" class="template_campos" placeholder="Fecha de Nacimiento" value="<?php echo count($usuario) > 0 ? $usuario["fechaNac"] : ""; ?>" maxlength="12" style="display:none;" />
                                </div><div class="contenedorCampos columnas">
                                    <p>Email*</p>
                                    <input type="text" id="edit_email" name="email" class="template_campos" placeholder="Email" value="<?php echo count($usuario) > 0 ? $usuario["email"] : ""; ?>" maxlength="64" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <p>Teléfono</p>
                                    <input type="text" id="edit_telefono1" name="telefono1" class="template_campos" placeholder="Teléfono" value="<?php echo count($usuario) > 0 ? $usuario["telefono1"] : ""; ?>" maxlength="16" />
                                </div><div class="contenedorCampos columnas">
                                    <p>Celular</p>
                                    <input type="text" id="edit_telefono2" name="telefono2" class="template_campos" placeholder="Celular" value="<?php echo count($usuario) > 0 ? $usuario["telefono2"] : ""; ?>" maxlength="16" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <p>Sexo</p>
                                    <input type="radio" name="sexo" value="H" <?php echo count($usuario) == 0 ? "checked='checked'" : ($usuario["sexo"] == "H" ? "checked='checked'" : ""); ?> style="margin-right:10px;" />Hombre
                                </div><div class="contenedorCampos columnas">
                                    <p>&nbsp;</p>
                                    <input type="radio" name="sexo" value="M" <?php echo count($usuario) > 0 ? ($usuario["sexo"] == "M" ? "checked='checked'" : "") : ""; ?> style="margin-right:10px;" />Mujer
                                </div>
                                <div class="contenedorCampos">
                                	<p>Recibir notificaciones de contacto</p>
                                    <input type="checkbox" name="notificaciones" <?php echo count($usuario) == 0 ? "checked='checked'" : ($usuario["notificaciones"] == 1 ? "checked='checked'" : ""); ?> style="margin-right:10px;" />Recibir notificaciones de contacto
                                </div>
                                <div class="contenedorCampos" <?php echo count($usuario) > 0 ? "style='display:none;'" : ""; ?>>
                                    <p>Contraseña*</p>
                                    <input type="password" id="edit_password" name="password" class="template_campos" placeholder="Contraseña" />
                                </div>
                                <div class="contenedorCampos" <?php echo count($usuario) > 0 ? "style='display:none;'" : ""; ?>>
                                    <p>Confirmar Contraseña*</p>
                                    <input type="password" id="edit_confPassword" class="template_campos" placeholder="Confirmar Contraseña" />
                                </div>
                                <div class="contenedorCampos" style="padding-top:30px;">
                                    <p id="btnGuardar" class="subtitulo guardar" onclick="edit_validarCampos();"><a class="btnBotones guardar">Buscar</a>Guardar Cambios</p>
                                    <p id="mensajeTemporal" style="display:none;">Espere un momento...</p>
                                </div>
                            </td>
                            <td>
                            	<div class="contenedorCampos">
                                    <p>Calle y Número</p>
                                    <input type="text" id="edit_calleNumero" name="calleNumero" class="template_campos" placeholder="Calle y Número" value="<?php echo count($usuario) > 0 ? $usuario["calleNumero"] : ""; ?>" maxlength="64" />
                                </div>
                                <div class="contenedorCampos columnas">
                                    <ul id="edit_estado" class="template_campos">
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
                                    <input type="text" id="_editEstado" name="estado" value="" style="display:none;" />
                                </div><div class="contenedorCampos columnas">
                                    <ul id="edit_ciudad" class="template_campos">
                                        Ciudad<span></span>
                                        <li class="lista">
                                            <ul></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                        <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                    </ul>
                                    <input type="text" id="_editCiudad" name="ciudad" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <ul id="edit_colonia" class="template_campos">
                                        Colonia<span></span>
                                        <li class="lista">
                                            <ul></ul>
                                        </li>
                                        <p data-value="-1"></p>
                                        <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                    </ul>
                                    <input type="text" id="_editColonia" name="colonia" value="" style="display:none;" />
                                    <input type="text" id="_editCP" name="cp" value="" style="display:none;" />
                                </div>
                                <div class="contenedorCampos">
                                    <p>Imágen</p>
                                    <div class="imagen"><?php
                                        echo count($usuario) > 0 ? ($usuario["imagen"] != "" ? "<img src='".$usuario["imagen"]."' class='logotipo' />" : "") : "";
                                    ?></div>
                                    <input type="file" id="edit_imagen" name="imagen" class="template_campos" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
			if (count($usuario) > 0) {
				echo
					"<div id='editarPassword'>
						<p class='titulo'>Cambiar Contraseña</p>
						<table>
							<tbody>
								<tr>
									<td>
										<div class='contenedorCampos'>
											<p>Nueva Contraseña*</p>
											<input type='password' id='edit_newPassword' class='template_campos' placeholder='Nueva Contraseña' />
										</div>
										<div class='contenedorCampos' style='padding-top:30px;'>
											<p id='btnGuardar2' class='subtitulo guardar' onclick='edit_validarCamposPassword();'><a class='btnBotones guardar'>Buscar</a>Guardar Cambios</p>
											<p id='mensajeTemporal2' style='display:none;'>Espere un momento...</p>
										</div>
									</td>
									<td>
										<div class='contenedorCampos'>
											<p>Confirmar Nueva Contraseña*</p>
											<input type='password' id='edit_confNewPassword' class='template_campos' placeholder='Confirmar Nueva Contraseña' />
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>";
			}
        ?>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>