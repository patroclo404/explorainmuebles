<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	if (isset($_SESSION[userId]))
		header("location: index.php");
	
	
	CabeceraHTML("solicitudRestablecer.css,solicitudRestablecer_ver2.js");
	CuerpoHTML();
?>
<div class="solicitudRestablecer_cuerpo">
	<h1>Si deseas restablecer la contraseña, ingresa la dirección de correo electrónico que utilizas para acceder a Explora Inmuebles.</h1>
    <h1>Te enviaremos un correo con instrucciones para restablecer tu contraseña.</h1><br />
    <p>Dirección de correo electrónico*</p>
    <input type="text" id="solicitud_email" class="template_campos" placeholder="Email" style="width:300px;" />
    <p><br /><br /><span class="boton" onclick="validarCampos();">Enviar Instrucciones</span></p>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>