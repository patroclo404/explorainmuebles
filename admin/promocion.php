<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	
	
	adminCabeceraHTML("promocion_ver3.js");
	adminCuerpoHTML("promocion_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Promoción", "Precio");
	$widths = array(NULL, 120);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de Precio del Anuncio", $arrayCamposWidth, false, false);
	adminPopUpsGenerales("promocion_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="precio" class="ObjFocusBlur" placeholder="Precio" maxlength="11" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="promocion" class="ObjFocusBlur" placeholder="Promoción" maxlength="128" /></td>
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
<?php
	adminFinHTML();
?>