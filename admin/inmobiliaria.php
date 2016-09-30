<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	adminCabeceraHTML("inmobiliaria_ver2.css,inmobiliaria_ver10.js");
	adminCuerpoHTML("inmobiliaria_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Nombre de Empresa", "Usuarios", "ID", "Vencimiento", "Publicados", "Guardados", "Créditos", "Generar");
	$widths = array(NULL, 75, 50, 100, 90, 90, 70, 70);
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	adminMainHTML("Lista de las Inmobiliarias", $arrayCamposWidth, true, true);
	adminPopUpsGenerales("inmobiliaria_cerrarPopUp");
?>
<div id="backImage">
	<form id="subirInmobiliaria" method="post" enctype="multipart/form-data" action="lib_php/updInmobiliaria.php">
    	<input type="text" id="idInmobiliaria" name="id" style="display:none;" />
        <table cellspacing="0" border="0" width="100%" style="padding:10px;">
            <tbody>
                <tr height="50">
                    <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="nombreEmpresa" name="nombreEmpresa" class="ObjFocusBlur" placeholder="Nombre de la Inmobiliaria" maxlength="128" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="rfc" name="rfc" class="ObjFocusBlur" placeholder="RFC" maxlength="32" /></td>
                </tr>
                <tr height="35">
                	<td>
                    	<div class="template_contenedoresCeldas">Logotipo: <a href="" id="imagenLogoTipo" target="_blank">Ver logotipo</a></div>
                    </td>
                </tr>
                <tr height="35">
                	<td><input type="file" id="logotipo" name="logotipo" class="ObjFocusBlur" /></td>
                </tr>
                <tr height="35">
                    <td><select id="usuario" name="usuario" class="ObjFocusBlur">
                        <option value="-1" class="off">Usuario</option>
                        <?php
                            $consulta = "SELECT USU_ID, USU_NOMBRE, USU_INMOBILIARIA FROM USUARIO ORDER BY USU_NOMBRE;";
                            foreach($conexion->query($consulta) as $row) {
                                echo "<option value='".$row["USU_ID"]."' ".($row["USU_INMOBILIARIA"] != NULL ? ("data-inmobiliaria='".$row["USU_INMOBILIARIA"]."'") : "").">".$row["USU_NOMBRE"]."</option>";
                            }
                        ?>
                    </select>*</td>
                </tr>
                <tr height="35" id="celdaValidez">
                	<td><input type="text" id="validez" name="validez" class="ObjFocusBlur" placeholder="Validez" maxlength="10" />*</td>
                </tr>
                <tr height="35" id="celdaCreditos">
                	<td><input type="text" id="creditos" name="creditos" class="ObjFocusBlur" placeholder="Créditos" maxlength="11" />*</td>
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
<div id="inmobiliaria_abrirModificarUsuarios" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td colspan="2" style="font-size:18px; border-bottom:1px solid #aeadb3;">Usuarios</td>
            </tr>
            <tr height="35">
            	<td style="border-top:1px solid #fff;" align="left">Usuario</td>
                <td width="30" align="right" style="border-top:1px solid #fff;"><img style="cursor:pointer;" src="images/btnAgregar.png" onclick="inmobiliaria_subirUsuarios(-1);" /></td>
            </tr>
            <tr>
            	<td colspan="2" style="border-bottom:1px solid #aeadb3;"><div id="contenedorInmobiliariaUsuarios" style="width:100%; height:300px; overflow:auto;"></div></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="inmobiliaria_abrirModificarPagos" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td colspan="2" style="font-size:18px; border-bottom:1px solid #aeadb3;">Pagos</td>
            </tr>
            <tr height="35">
            	<td><p id="inmobiliariaPago"></p></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="creditos2" class="ObjFocusBlur" placeholder="Créditos" maxlength="11" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="total" class="ObjFocusBlur" placeholder="Total" maxlength="11" />*</td>
            </tr>
            <tr height="35">
                <td><input type="text" id="validez2" class="ObjFocusBlur" placeholder="Vigencia" maxlength="10" />*</td>
            </tr>
            <tr height="35">
                <td><select id="tipo" class="ObjFocusBlur">
                    <option value="-1" class="off">Tipo</option>
                    <option value="0">Manual</option>
                    <option value="1">Conekta</option>
                </select>*</td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas"><input type="checkbox" id="isPagado" style="margin-right:10px;" />Pagado</div></td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas"><input type="checkbox" id="notificar" style="margin-right:10px;" />Notificar a inmobiliaria</div></td>
            </tr>
            <tr height="50">
                <td>
                    <div id="btnGuardar3" class="btnOpciones" onClick="validarCampos3();">Guardar</div>
                    <span id="mensajeTemporal3" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="mascaraPrincipalNivel2" class="backInv mascaraPrincipalNivel2" onclick="inmobiliaria_cerrarPopUp2();"></div>
<div id="inmobiliaria_subirUsuarios" class="classPopUp classPopUpNivel2 inmobiliaria_subirUsuarios">
	<form id="subirUsuario" method="post" enctype="multipart/form-data" action="lib_php/updUsuario.php">
    	<input type="text" id="idUsuario" name="id" style="display:none;" />
        <input type="text" id="usu_inmobiliaria" name="inmobiliaria" style="display:none;" value="-1" />
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
                <tr height="35" id="celdaPassword">
                    <td><input type="password" id="usu_password" name="password" class="ObjFocusBlur" placeholder="Contraseña" maxlength="32" />*</td>
                </tr>
                <tr height="35" id="celdaConfPassword">
                    <td><input type="password" id="usu_confPassword" class="ObjFocusBlur" placeholder="Confirmar Contraseña" maxlength="32" />*</td>
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