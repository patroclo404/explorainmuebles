<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	
	
	adminCabeceraHTML("vendedor_ver2.js");
	adminCuerpoHTML("vendedor_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Nombre", "Email", "Cambiar");
	$widths = array(NULL, 300, 120);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de los Vendedores", $arrayCamposWidth, true, true);
	adminPopUpsGenerales("vendedor_cerrarPopUp");
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
            <tr height="35">
                <td><div class="template_contenedorCeldas">
                	<input type="radio" name="sexo" value="H" style="margin-right:10px;" />Hombre
                    <input type="radio" name="sexo" value="M" style="margin:0px 10px 0px 50px;" />Mujer
                </div></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="telefono1" class="ObjFocusBlur" placeholder="Teléfono 1" maxlength="16" /></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="telefono2" class="ObjFocusBlur" placeholder="Teléfono 2" maxlength="16" /></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><input type="text" id="calleNumero" class="ObjFocusBlur" placeholder="Calle y Número" /></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="estado" class="ObjFocusBlur">
                	<option value="-1" class="off">Estado</option>
                    <?php
						$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
						$res = crearConsulta($consulta);
						while($row = mysql_fetch_array($res)) {
							echo "<option value='".$row["EST_ID"]."'>".$row["EST_NOMBRE"]."</option>";
						}
					?>
                </select></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="ciudad" class="ObjFocusBlur">
                	<option value="-1" class="off">Ciudad</option>
                </select></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="colonia" class="ObjFocusBlur">
                	<option value="-1" class="off">Colonia</option>
                </select></td>
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
<div id="vendedor_abrirModificarPassword" class="classPopUp">
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