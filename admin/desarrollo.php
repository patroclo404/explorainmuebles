<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	adminCabeceraHTML("desarrollo.css,desarrollo_ver4.js");
	adminCuerpoHTML("desarrollo_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Título", "Tipo", "Imágenes");
	$widths = array(NULL, 200, 120);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de los Desarrollos", $arrayCamposWidth, true, false);
	adminPopUpsGenerales("desarrollo_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="titulo" class="ObjFocusBlur" placeholder="Título" maxlength="128" />*</td>
            </tr>
            <tr height="35">
                <td><select id="tipo" class="ObjFocusBlur">
                    <option value="-1" class="off">Tipo</option>
                    <option value="0">Horizontal</option>
                    <option value="1">Vertical</option>
                </select>*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="entrega" class="ObjFocusBlur" placeholder="Entrega" maxlength="32" /></td>
            </tr>
            <tr height="35">
            	<td><input type="text" id="unidades" class="ObjFocusBlur" placeholder="Unidades" maxlength="11" /></td>
            </tr>
            <tr height="35">
            	<td><input type="text" id="latitud" class="ObjFocusBlur" placeholder="Latitud" maxlength="32" />*</td>
            </tr>
            <tr height="35">
            	<td><input type="text" id="longitud" class="ObjFocusBlur" placeholder="Longitud" maxlength="32" />*</td>
            </tr>
            <tr>
            	<td><textarea id="descripcion" class="ObjFocusBlur" placeholder="Descripción"></textarea>*</td>
            </tr>
            <tr height="35">
            	<td><select id="inmobiliaria" class="ObjFocusBlur">
                	<option value="-1" class="off">Inmobiliaria</option>
                    <?php
						$consulta = "SELECT INM_ID, INM_NOMBRE_EMPRESA FROM INMOBILIARIA ORDER BY INM_NOMBRE_EMPRESA;";
						foreach($conexion->query($consulta) as $row) {
							echo "<option value='".$row["INM_ID"]."'>".$row["INM_NOMBRE_EMPRESA"]."</option>";
						}
					?>
                </select>*</td>
            </tr>
            <tr height="50">
                <td>
                    <div id="btnGuardar" class="btnOpciones" onClick="validarCampos();">Guardar</div>
                    <span id="mensajeTemporal" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="desarrollo_abrirModificarImagenes" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td colspan="4" style="font-size:18px; border-bottom:1px solid #aeadb3;">Imágenes</td>
            </tr>
            <tr height="35">
            	<td style="border-top:1px solid #fff;" align="left">Modificar</td>
                <td width="120" style="text-align:center;">Imágen</td>
                <td width="120" style="text-align:center;">Principal</td>
                <td width="30" align="right" style="border-top:1px solid #fff;"><img style="cursor:pointer;" src="images/btnAgregar.png" onclick="desarrollo_subirImagen(-1);" /></td>
            </tr>
            <tr>
            	<td colspan="4" style="border-bottom:1px solid #aeadb3;"><div id="contenedorDesarrolloImagenes" style="width:100%; height:300px; overflow:auto;"></div></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="mascaraPrincipalNivel2" class="backInv mascaraPrincipalNivel2" onclick="desarrollo_cerrarPopUp2();"></div>
<div id="desarrollo_obtenerCoordenadas" class="classPopUp classPopUpNivel2 desarrollo_obtenerCoordenadas">
    <table>
        <tbody>
            <tr height="50">
                <td style="font-size:18px; border-bottom:1px solid #aeadb3;">Coordenadas en el Mapa</td>
            </tr>
            <tr>
            	<td><div id="contenedorDesarrolloMapa" style="width:100%; height:500px; overflow:auto;"></div></td>
            </tr>
            <tr height="50">
                <td>
                    <div id="btnGuardar2" class="btnOpciones" onClick="validarCampos2();">Guardar</div>
                    <span id="mensajeTemporal2" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="desarrollo_subirImagen" class="classPopUp classPopUpNivel2 desarrollo_subirImagen">
	<form id="subirImagen" method="post" enctype="multipart/form-data" action="lib_php/updDesarrolloImagen.php">
        <table>
            <tbody>
                <tr height="50">
                    <td id="tituloEmergenteImagenes" style="font-size:18px; border-bottom:1px solid #aeadb3;"></td>
                </tr>
                <tr height="35">
                	<td>
                    	<input type="text" id="idDesarrollo" name="id" style="display:none;" />
                        <input type="text" id="nuevo" name="nuevo" style="display:none;" />
                        <input type="text" id="modificar" name="modificar" style="display:none;" />
                        <input type="text" id="idImagen" name="idImagen" style="display:none;" />
                    	<input type="file" id="imagen" name="imagen" class="ObjFocusBlur" />*
                    </td>
                </tr>
                <tr height="35">
                	<td>
                    	<div class="template_contenedorCeldas">
                        	<input type="checkbox" id="imagenPrincipal" name="imagenPrincipal" style="margin-right:10px;" />Imágen Principal
                        </div>
                    </td>
                </tr>
                <tr height="50">
                    <td>
                        <div id="btnGuardar3" class="btnOpciones" onClick="validarCampos3();">Guardar</div>
                        <span id="mensajeTemporal3" style="display:none;">Espere un momento...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<?php
	adminFinHTML();
?>