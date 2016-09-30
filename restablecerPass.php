<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	if (isset($_SESSION[userId]))
		header("location: index.php");
		
	if (!isset($_GET["recpass"]))
		header("location: index.php");
		
		
	$recpass = $_GET["recpass"];
	
	
	CabeceraHTML("restablecerPass.css,restablecerPass.js");
	CuerpoHTML();
?>
<div class="restablecerPass_cuerpo">
	<p class="titulo">Restablecer Contraseña</p>
    <p>Nueva Contraseña*</p>
    <input type="password" id="restablecer_pass" class="template_campos" placeholder="Nueva Contraseña" data-codigo="<?php echo $recpass; ?>" style="width:300px;" /><br /><br />
    <p>Confirmar Nueva Contraseña*</p>
    <input type="password" id="confRestablecer_pass" class="template_campos" placeholder="Confirmar Nueva Contraseña" style="width:300px;" />
    <p><br /><br /><span class="boton" onclick="validarCampos();">Restablecer</span></p>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>