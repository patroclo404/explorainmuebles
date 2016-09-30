<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	adminCabeceraHTML("anunciosVencidos_ver4.css,anunciosVencidos_ver4.js");
	adminCuerpoHTML("anunciosVencidos_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Título", "Anunciante", "ID", "Vigencia", "Renovar");
	$widths = array(NULL, 180, 50, 100, 70);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de los Anuncios Vencidos", $arrayCamposWidth, false, true);
	adminPopUpsGenerales("anunciosVencidos_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="limiteVigencia" class="ObjFocusBlur" placeholder="Nuevo Límite de Vigencia" maxlength="10" />*</td>
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
<div id="anunciosVencidos_abrirModificarUsuario" class="classPopUp anunciosVencidos_abrirModificarUsuario">
	<form id="subirUsuario" method="post" enctype="multipart/form-data" action="lib_php/updUsuario.php">
    	<input type="text" id="idUsuario" name="id" style="display:none;" />
        <table cellspacing="0" border="0" width="100%" style="padding:10px;">
            <tbody>
                <tr height="50">
                    <td style="font-size:18px; border-bottom:1px solid #012851;">Modificar Usuario</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_nombre" name="nombre" class="ObjFocusBlur" placeholder="Nombre Completo" maxlength="128" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_email" name="email" class="ObjFocusBlur" placeholder="Email" maxlength="64" />*</td>
                </tr>
                <tr height="35" style="display:none;">
                    <td><input type="password" id="usu_password" name="password" class="ObjFocusBlur" placeholder="Contraseña" maxlength="32" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_FBid" name="FBid" class="ObjFocusBlur" placeholder="Facebook Id" maxlength="32" /></td>
                </tr>
                <tr height="35">
                    <td><div class="template_contenedorCeldas">
                        <input type="radio" name="sexo" value="H" style="margin-right:10px;" />Hombre
                        <input type="radio" name="sexo" value="M" style="margin:0px 10px 0px 50px;" />Mujer
                    </div></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_fechaNac" name="fechaNac" class="ObjFocusBlur" placeholder="Fecha de Nacimiento" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_telefono1" name="telefono1" class="ObjFocusBlur" placeholder="Teléfono 1" maxlength="16" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_telefono2" name="telefono2" class="ObjFocusBlur" placeholder="Teléfono 2" maxlength="16" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_calleNumero" name="calleNumero" class="ObjFocusBlur" placeholder="Calle y Número" /></td>
                </tr>
                <tr height="35">
                    <td><select id="usu_estado" name="estado" class="ObjFocusBlur">
                        <option value="-1" class="off">Estado</option>
                        <?php
                            $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
                            foreach($conexion->query($consulta) as $row) {
                                echo "<option value='".$row["EST_ID"]."'>".$row["EST_NOMBRE"]."</option>";
                            }
                        ?>
                    </select></td>
                </tr>
                <tr height="35">
                    <td><select id="usu_ciudad" name="ciudad" class="ObjFocusBlur">
                        <option value="-1" class="off">Ciudad</option>
                    </select></td>
                </tr>
                <tr height="35">
                    <td>
                    	<select id="usu_colonia" name="colonia" class="ObjFocusBlur">
                        	<option value="-1" class="off">Colonia</option>
                    	</select>
                    	<input type="text" id="usu_cp" name="cp" style="display:none;" />
                    </td>
                </tr>
                <tr height="35">
                	<td>
                    	<div class="template_contenedoresCeldas">Imágen: <a href="" id="usu_imagenActual" target="_blank">Ver imágen</a></div>
                    </td>
                </tr>
                <tr height="35">
                	<td><input type="file" id="usu_imagen" name="imagen" class="ObjFocusBlur" /></td>
                </tr>
                <tr height="35">
                	<td><div class="template_contenedoresCeldas"><input type="checkbox" id="usu_notificaciones" name="notificaciones" style="margin-right:10px;" />Recibir notificaciones de contacto</div></td>
                </tr>
                <tr height="50">
                    <td>
                        <div id="btnGuardar2" class="btnOpciones" onClick="validarCampos2();">Guardar</div>
                        <span id="mensajeTemporal2" style="display:none;">Espere un momento...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php
	adminFinHTML();
?>