<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	if (isset($_SESSION[userId]))
		header("location: index.php");
	
	
	CabeceraHTML("registro_ver2.css,registro_ver3.js");
	CuerpoHTML();
?>
<div class="registro_cuerpo container center">
	<h3 class="center">Para poder publicar un anuncio, debes contar con una cuenta en ExploraInmuebles.com<br />Por favor completa tus datos o regístrate con Facebook.</h3>
	<br /> <br /><br />
	<div class="col-md-6 col-xs-12">
		<p>Ingresa tus Datos</p>
        <input type="text" id="reg_FBId" value="" style="display:none;" />
        <p><input type="text" id="reg_nombre" class="template_campos" placeholder="Nombre" /></p>
        <p><input type="text" id="reg_email" class="template_campos" placeholder="Correo Electrónico" /></p>
        <p><input type="password" id="reg_password" class="template_campos" placeholder="Contraseña" /></p>
        <p><input type="password" id="reg_confPassword" class="template_campos" placeholder="Confirmar Contraseña" /></p>
        <p class="btn" style="text-align:right;"><span onclick="registro_validaCampos_count();"><a class="paloma"></a>Registrarme</span></p><br /> <br /><br />
		<p>Regístrate con Facebook</p>
        <p class="btn"><span onclick="registro_validaCampos_countFB();"><a class="btnBotones facebook">Facebook</a>Registrarme</span></p>
	</div>
	<div class="col-md-6 col-xs-12">
		<p>Iniciar Sesión</p>
	    <p><input type="text" id="reg_email2" class="template_campos" placeholder="Correo Electrónico" /></p>
	    <p><input type="password" id="reg_password2" class="template_campos" placeholder="Contraseña" /></p>
	    <p class="btn" style="text-align:right;"><span onclick="registro_validaCampos_login();"><a class="paloma"></a>Iniciar Sesión</span></p><br /> <br /><br />
		<p>Inicia con Facebook</p>
        <p class="btn"><span onclick="registro_validaCampos_loginFB();"><a class="btnBotones facebook">Facebook</a>Iniciar Sesión</span></p>
	</div>
	<div class="col-md-12 col-xs-12">
		<p style="padding-top:50px;"><input id="reg_check" type="checkbox" style="margin-right:10px;" />He leido y acepto los <a href="terminosCondiciones.php" target="_blank" style="color:#852c2b;">Términos y Condiciones</a></p>
	</div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>