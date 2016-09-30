<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	
	
	adminCabeceraHTML("administrador.js");
	adminCuerpoHTML("administrador_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Nombre", "Email", "Cambiar");
	$widths = array(NULL, 300, 120);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de los Administradores", $arrayCamposWidth, true, false);
	adminPopUpsGenerales("administrador_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="nombre" class="ObjFocusBlur" placeholder="Nombre Completo" maxlength="128" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="email" class="ObjFocusBlur" placeholder="Email" maxlength="64" />*</td>
            </tr>
            <tr height="35" id="celdaPassword">
                <td><input type="password" id="password" class="ObjFocusBlur" placeholder="Contraseña" maxlength="32" />*</td>
            </tr>
            <tr height="35" id="celdaConfPassword">
                <td><input type="password" id="confPassword" class="ObjFocusBlur" placeholder="Confirmar Contraseña" maxlength="32" />*</td>
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
<div id="administrador_abrirModificarPassword" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td style="font-size:18px; border-bottom:1px solid #012851;">Modificar Contraseña</td>
            </tr>
            <tr height="35">
            	<td><input type="password" id="password2" class="ObjFocusBlur" placeholder="Nueva Contraseña" maxlength="32" />*</td>
            </tr>
            <tr height="35">
            	<td><input type="password" id="confPassword2" maxlength="32" class="ObjFocusBlur" placeholder="Confirmar Nueva Contraseña" />*</td>
            </tr>
            <tr height="50">
            	<td>
                    <div id="btnGuardar2" class="btnOpciones" onclick="validarCampos2();">Guardar</div>
                    <span id="mensajeTemporal2" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	adminFinHTML();
?>