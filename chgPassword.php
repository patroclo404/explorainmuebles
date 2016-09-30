<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	
	
	CabeceraHTML("chgPassword_ver2.css,chgPassword.js");
	CuerpoHTML();
?>
<div class="chgPassword_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
        <div id="lk_cambiarPassword">
        	<p class="titulo">Cambiar Contraseña</p>
            <table>
            	<tbody>
                	<tr>
                    	<td>
                        	<div class="contenedorCampos">
                            	<p>Contraseña Actual</p>
                            	<input type="password" id="cambiarPassword_passActual" class="template_campos" placeholder="Contraseña Actual" maxlength="32" />
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                    	<td>
                        	<div class="contenedorCampos">
                            	<p>Nueva Contraseña</p>
                            	<input type="password" id="cambiarPassword_newPass" class="template_campos" placeholder="Nueva Contraseña" maxlength="32" />
                            </div>
                        </td>
                        <td>
                        	<div class="contenedorCampos">
                            	<p>Confirmar Nueva Contraseña</p>
                            	<input type="password" id="cambiarPassword_confNewPass" class="template_campos" placeholder="Confirmar Nueva Contraseña" maxlength="32" />
                            </div>
                            <div class="contenedorCampos">
                            	<p id="btnGuardar3" class="subtitulo guardar" onclick="chgPassword_validarCampos();"><a class="btnBotones guardar">Guardar</a>Guardar Cambios</p>
                                <p id="mensajeTemporal3" style="display:none;">Espere un momento...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>