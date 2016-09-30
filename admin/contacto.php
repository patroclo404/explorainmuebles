<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	
	
	adminCabeceraHTML("contacto.js");
	adminCuerpoHTML("contacto_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Email");
	$widths = array(NULL);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de Contactos", $arrayCamposWidth, false, false);
	adminPopUpsGenerales("contacto_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="email" class="ObjFocusBlur" placeholder="Email" maxlength="64" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="telefono" class="ObjFocusBlur" placeholder="Teléfono" maxlength="64" /></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="whatsapp" class="ObjFocusBlur" placeholder="Whatsapp" maxlength="64" /></td>
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