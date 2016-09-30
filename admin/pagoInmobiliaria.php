<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	adminCabeceraHTML("pagoInmobiliaria_ver3.css,pagoInmobiliaria_ver7.js");
	adminCuerpoHTML("pagoInmobiliaria_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Inmobiliaria", "Total", "Fecha - Hora", "Tipo", "Pagado", "Créditos", "Vigencia");
	$widths = array(NULL, 120, 170, 70, 60, 70, 100);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de los Pagos de Inmobiliarias", $arrayCamposWidth, true, false);
	adminPopUpsGenerales("pagoInmobiliaria_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="creditos" class="ObjFocusBlur" placeholder="Creditos" maxlength="11" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="total" class="ObjFocusBlur" placeholder="Total" maxlength="11" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="validez" class="ObjFocusBlur" placeholder="Vigencia" maxlength="10" />*</td>
            </tr>
            <tr height="35">
                <td><select id="tipo" class="ObjFocusBlur">
                    <option value="-1" class="off">Tipo</option>
                    <option value="0">Manual</option>
                    <option value="1">Conekta</option>
                </select>*</td>
            </tr>
            <tr height="35">
                <td><select id="inmobiliaria" class="ObjFocusBlur">
                    <option value="-1" class="off">Inmobiliaria</option><?php
					$consulta = "SELECT INM_ID, INM_NOMBRE_EMPRESA FROM INMOBILIARIA ORDER BY INM_NOMBRE_EMPRESA;";
					foreach($conexion->query($consulta) as $row) {
						echo "<option value='".$row["INM_ID"]."'>".$row["INM_NOMBRE_EMPRESA"]."</option>";
					}
                ?></select>*</td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas"><input type="checkbox" id="isPagado" style="margin-right:10px;" />Pagado</div></td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas"><input type="checkbox" id="notificar" style="margin-right:10px;" />Notificar a inmobiliaria</div></td>
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