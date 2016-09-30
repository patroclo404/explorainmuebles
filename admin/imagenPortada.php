<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	
	
	adminCabeceraHTML("imagenPortada_ver5.js");
	adminCuerpoHTML("imagenPortada_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Modificar", "Imágen", "Orden");
	$widths = array(120, NULL, 120);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de las Imágenes de Portada", $arrayCamposWidth, true, false);
	adminPopUpsGenerales("imagenPortada_cerrarPopUp");
?>
<div id="backImage">
	<form id="subirImagenPortada" method="post" enctype="multipart/form-data" action="lib_php/updImagenPortada.php">
    	<input type="text" id="idImagenPortada" name="id" style="display:none;" />
        <table cellspacing="0" border="0" width="100%" style="padding:10px;">
            <tbody>
                <tr height="50">
                    <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
                </tr>
                <tr height="35">
                	<td><input type="text" id="texto" name="texto" class="ObjFocusBlur" maxlength="128" placeholder="Texto alternativo imágen" />*</td>
                </tr>
                <tr height="35">
                	<td><input type="text" id="textoPrincipal" name="textoPrincipal" class="ObjFocusBlur" maxlength="128" placeholder="Texto Principal" /></td>
                </tr>
                <tr height="35">
                	<td><input type="text" id="textoSecundario" name="textoSecundario" class="ObjFocusBlur" maxlength="128" placeholder="Texto Secundario" /></td>
                </tr>
                <tr height="35">
                	<td><div class="template_contenedoresCeldas">Imágen: <a href="" id="imagenActual" target="_blank">Ver imágen</a></div></td>
                </tr>
                <tr height="35">
                	<td><input type="file" id="imagen" name="imagen" class="ObjFocusBlur" /></td>
                </tr>
                <tr height="35">
                	<td><div class="template_contenedoresCeldas">* Imágen de: 1920px * 800px</div></td>
                </tr>
                <tr height="35">
                	<td><input type="text" id="orden" name="orden" class="ObjFocusBlur" placeholder="Orden" /></td>
                </tr>
                <tr height="50">
                    <td>
                        <div id="btnGuardar" class="btnOpciones" onClick="validarCampos();">Guardar</div>
                        <span id="mensajeTemporal" style="display:none;">Espere un momento...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php
	adminFinHTML();
?>