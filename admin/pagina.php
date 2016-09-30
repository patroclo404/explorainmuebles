<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	
	
	adminCabeceraHTML("pagina.css,pagina_ver2.js");
	adminCuerpoHTML("pagina_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Título");
	$widths = array(NULL);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de las Páginas", $arrayCamposWidth, false, false);
	adminPopUpsGenerales("pagina_cerrarPopUp");
?>
<div id="backImage">
	<form id="subirPagina" method="post" enctype="multipart/form-data" action="lib_php/updPagina.php">
    	<input type="text" id="idPagina" name="id" value="" style="display:none;" />
        <table cellspacing="0" border="0" width="100%" style="padding:10px;">
            <tbody>
                <tr height="50">
                    <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="titulo" name="titulo" class="ObjFocusBlur" placeholder="Título" maxlength="64" />*</td>
                </tr>
                <tr>
                    <td><textarea id="contenido" name="contenido" class="ObjFocusBlur"></textarea></td>
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
<script type="text/javascript" src="../js/minified/jquery.sceditor.bbcode.min.js"></script>
<?php
	adminFinHTML();
?>