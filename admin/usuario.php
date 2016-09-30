<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	adminCabeceraHTML("usuario_ver3.css,usuario_ver12.js");
	adminCuerpoHTML("usuario_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Nombre", "Email", "Cambiar", "ID", "Anuncios", "Miembro desde", "Inmobiliaria");
	$widths = array(NULL, 230, 95, 50, 75, 120, 210);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de los Usuarios", $arrayCamposWidth, true, true);
	adminPopUpsGenerales("usuario_cerrarPopUp");
?>
<div id="backImage">
	<form id="subirUsuario" method="post" enctype="multipart/form-data" action="lib_php/updUsuario.php">
    	<input type="text" id="idUsuario" name="id" style="display:none;" />
        <table cellspacing="0" border="0" width="100%" style="padding:10px;">
            <tbody>
                <tr height="50">
                    <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="nombre" name="nombre" class="ObjFocusBlur" placeholder="Nombre Completo" maxlength="128" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="email" name="email" class="ObjFocusBlur" placeholder="Email" maxlength="64" />*</td>
                </tr>
                <tr height="35" id="celdaPassword">
                    <td><input type="password" id="password" name="password" class="ObjFocusBlur" placeholder="Contraseña" maxlength="32" />*</td>
                </tr>
                <tr height="35" id="celdaConfPassword">
                    <td><input type="password" id="confPassword" class="ObjFocusBlur" placeholder="Confirmar Contraseña" maxlength="32" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="FBid" name="FBid" class="ObjFocusBlur" placeholder="Facebook Id" maxlength="32" /></td>
                </tr>
                <tr height="35">
                    <td><div class="template_contenedorCeldas">
                        <input type="radio" name="sexo" value="H" style="margin-right:10px;" />Hombre
                        <input type="radio" name="sexo" value="M" style="margin:0px 10px 0px 50px;" />Mujer
                    </div></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="fechaNac" name="fechaNac" class="ObjFocusBlur" placeholder="Fecha de Nacimiento" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="telefono1" name="telefono1" class="ObjFocusBlur" placeholder="Teléfono 1" maxlength="16" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="telefono2" name="telefono2" class="ObjFocusBlur" placeholder="Teléfono 2" maxlength="16" /></td>
                </tr>
                <tr height="35" name="caracteristicas">
                    <td><input type="text" id="calleNumero" name="calleNumero" class="ObjFocusBlur" placeholder="Calle y Número" /></td>
                </tr>
                <tr height="35" name="caracteristicas">
                    <td><select id="estado" name="estado" class="ObjFocusBlur">
                        <option value="-1" class="off">Estado</option>
                        <?php
                            $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
                            foreach($conexion->query($consulta) as $row) {
                                echo "<option value='".$row["EST_ID"]."'>".$row["EST_NOMBRE"]."</option>";
                            }
                        ?>
                    </select></td>
                </tr>
                <tr height="35" name="caracteristicas">
                    <td><select id="ciudad" name="ciudad" class="ObjFocusBlur">
                        <option value="-1" class="off">Ciudad</option>
                    </select></td>
                </tr>
                <tr height="35" name="caracteristicas">
                    <td>
                    	<select id="colonia" name="colonia" class="ObjFocusBlur">
                        	<option value="-1" class="off">Colonia</option>
                    	</select>
                    	<input type="text" id="cp" name="cp" style="display:none;" />
                    </td>
                </tr>
                <tr height="35">
                	<td>
                    	<div class="template_contenedoresCeldas">Imágen: <a href="" id="imagenActual" target="_blank">Ver imágen</a></div>
                    </td>
                </tr>
                <tr height="35">
                	<td><input type="file" id="imagen" name="imagen" class="ObjFocusBlur" /></td>
                </tr>
                <tr height="35">
                	<td><div class="template_contenedoresCeldas"><input type="checkbox" id="notificaciones" name="notificaciones" style="margin-right:10px;" />Recibir notificaciones de contacto</div></td>
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
<div id="usuario_abrirModificarPassword" class="classPopUp">
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
<div id="usuario_abrirModificarInmuebles" class="classPopUp usuario_abrirModificarInmuebles">
	<table>
    	<tbody>
        	<tr height="50">
            	<td style="font-size:18px; border-bottom:1px solid #aeadb3;">Inmuebles</td>
            </tr>
            <tr height="35">
                <td>Título</td>
            </tr>
            <tr>
            	<td style="border-bottom:1px solid #aeadb3;"><div id="contenedorUsuarioInmuebles" style="width:100%; height:300px; overflow:auto;"></div></td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	adminFinHTML();
?>