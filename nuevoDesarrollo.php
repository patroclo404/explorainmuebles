<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	$variables = "";
	$conexion = crearConexionPDO();
	
	
	$edit = 0;
	$desarrollo = array();
	
	
	if (isset($_POST["edit"])) {
		$edit = 1;
		
		$consulta =
			"SELECT
				DES_ID,
				DES_TITULO,
				DES_TIPO,
				DES_ENTREGA,
				DES_UNIDADES,
				DES_LATITUD,
				DES_LONGITUD,
				DES_DESCRIPCION
			FROM DESARROLLO
			WHERE DES_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($_POST["id"]));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$desarrollo = array(
			"id"			=>	$row["DES_ID"],
			"titulo"		=>	$row["DES_TITULO"],
			"tipo"			=>	$row["DES_TIPO"],
			"entrega"		=>	$row["DES_ENTREGA"] != NULL ? $row["DES_ENTREGA"] : "",
			"unidades"		=>	$row["DES_UNIDADES"] != NULL ? $row["DES_UNIDADES"] : "",
			"latitud"		=>	$row["DES_LATITUD"],
			"longitud"		=>	$row["DES_LONGITUD"],
			"descripcion"	=>	$row["DES_DESCRIPCION"],
			"imagenes"		=>	array()
		);
		
		
		$consulta = "SELECT IDE_ID, IDE_IMAGEN, IDE_ORDEN FROM IMAGEN_DESARROLLO WHERE IDE_DESARROLLO = ? ORDER BY IDE_ID;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($_POST["id"]));
		foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$desarrollo["imagenes"][] = array(
				"id"		=>	$row["IDE_ID"],
				"imagen"	=>	$urlArchivos.$row["IDE_IMAGEN"],
				"principal"	=>	$row["IDE_ORDEN"]
			);
		}
		
		$variables = "post_tipo=".$desarrollo["tipo"].",post_latitud='".$desarrollo["latitud"]."',post_longitud='".$desarrollo["longitud"]."'";
	}
	
	
	CabeceraHTML("nuevoDesarrollo.css,nuevoDesarrollo_ver6.js", $variables);
	CuerpoHTML();
?>
<div class="nuevoDesarrollo_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
        <div id="lk_crearDesarrollo">
        	<p class="titulo"><?php echo $edit == 0 ? "Crear" : "Modificar"; ?> Desarrollo</p>
            <form id="subirDesarrollo" method="post" enctype="multipart/form-data" action="lib_php/updDesarrollo.php">
            	<input type="text" name="<?php echo $edit == 0 ? "nuevo" : "modificar"; ?>" value="1" style="display:none;" />
                <input type="text" id="idDesarrollo" name="id" value="<?php echo $edit == 0 ? "-1" : $desarrollo["id"]; ?>" style="display:none;" />
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div class="contenedorCampos">
                                    <p>Título*</p>
                                    <input type="text" id="desarrollo_titulo" name="titulo" class="template_campos" placeholder="Título" maxlength="128" value="<?php echo $edit == 1 ? $desarrollo["titulo"] : ""; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="contenedorCampos">
                                    <ul id="desarrollo_tipo" class="template_campos">
                                        Tipo*<span></span>
                                        <li class="lista">
                                            <ul>
                                            	<li data-value="0">Horizontal</li>
                                                <li data-value="1">Vertical</li>
                                            </ul>
                                        </li>
                                        <p data-value="-1"></p>
                                        <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                                    </ul>
                                    <input type="text" id="_desarrolloTipo" name="tipo" value="" style="display:none;" />
                                </div>
                            </td>
                            <td>
                            	<div class="contenedorCampos">
                                	<p>Unidades</p>
                                    <input type="text" id="desarrollo_unidades" name="unidades" class="template_campos" placeholder="Unidades" maxlength="11" value="<?php echo $edit == 1 ? $desarrollo["unidades"] : ""; ?>" style="width:90%;" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="2">
                                <div class="contenedorCampos">
                                    <p>Entrega</p>
                                    <input type="text" id="desarrollo_entrega" name="entrega" class="template_campos" placeholder="Entrega" maxlength="32" value="<?php echo $edit == 1 ? $desarrollo["entrega"] : ""; ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="contenedorCampos">
                                    <p>Ubicación en el mapa*</p>
                                    <div id="contenedorMapa" class="contenedorMapa"></div>
                                    <input type="text" id="_desarrolloLatitud" name="latitud" value="" style="display:none;" />
                                    <input type="text" id="_desarrolloLongitud" name="longitud" value="" style="display:none;" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="contenedorCampos">
                                    <p>Descripción*</p>
                                    <textarea id="desarrollo_descripcion" name="descripcion" class="template_campos" placeholder="Descripción"><?php echo $edit == 1 ? $desarrollo["descripcion"] : ""; ?></textarea>
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
								
								for ($x = 0; $x < count($desarrollo["imagenes"]); $x++) {
									echo
													"<div class='bloqueImagen' data-imagen='".$desarrollo["imagenes"][$x]["id"]."'>
														<img src='".$desarrollo["imagenes"][$x]["imagen"]."' />
														<span class='borrar'>X</span>
														<p><input type='radio' name='radioImagenPrincipal' ".($desarrollo["imagenes"][$x]["principal"] == 1 ? "checked='checked'" : "")." data-id=".$desarrollo["imagenes"][$x]["id"]." /></p>
													</div>";
													
									if ($desarrollo["imagenes"][$x]["principal"] == 1)
										$tempPrincipal = $desarrollo["imagenes"][$x]["id"];
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
                        	<td>&nbsp;</td>
                        	<td>
                            	<div class="contenedorCampos">
                                    <p id="btnGuardar" class="subtitulo <?php echo $edit == 0 ? "guardar" : "publicar"; ?>" onclick="validarCampos();" style="padding-top:40px;"><a class="btnBotones guardar">Guardar</a><?php echo $edit == 0 ? "Publicar Desarrollo" : "Guardar Cambios"; ?></p>
                                    <p id="mensajeTemporal" style="display:none;">Espere un momento...</p>
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