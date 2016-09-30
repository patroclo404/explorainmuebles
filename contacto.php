<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$conexion = crearConexionPDO();
	$consulta = "SELECT CON_ID, CON_EMAIL, CON_TELEFONO, CON_WHATSAPP FROM CONTACTO WHERE CON_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(1));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$contacto = array(
		"email"		=>	$row["CON_EMAIL"] != NULL ? $row["CON_EMAIL"] : "",
		"telefono"	=>	$row["CON_TELEFONO"] != NULL ? $row["CON_TELEFONO"] : "",
		"whatsapp"	=>	$row["CON_WHATSAPP"] != NULL ? $row["CON_WHATSAPP"] : ""
	);
	
	
	CabeceraHTML("contacto_ver4.css,contacto_ver2.js");
	CuerpoHTML();
?>
<div class="contacto_cuerpo">
	<div class="datos"><?php
		if ($contacto["email"] != "")
			echo "<p><a class='otrosBotones email'>Email</a>: ".$contacto["email"]."</p>";
		if ($contacto["telefono"] != "")
			echo "<p><a class='otrosBotones telefono'>Teléfono</a>: ".$contacto["telefono"]."</p>";
		if ($contacto["whatsapp"] != "")
			echo "<p><a class='otrosBotones whatsapp'>WhatsApp</a>: ".$contacto["whatsapp"]."</p>";
    ?></div>
	<table class="contacto">
        <tbody>
            <tr>
                <td class="titulo"><a class="btnBotones email">Email</a>Contáctanos</td>
            </tr>
            <tr>
                <td><input type="text" id="contacto_nombre" class="template_campos" placeholder="Nombre" /></td>
            </tr>
            <tr>
                <td><input type="text" id="contacto_email" class="template_campos" placeholder="E-mail" /></td>
            </tr>
            <tr>
                <td><input type="text" id="contacto_telefono" class="template_campos" placeholder="Teléfono" /></td>
            </tr>
            <tr>
                <td><textarea id="contacto_mensaje" class="template_campos" placeholder="Mensaje"></textarea></td>
            </tr>
            <tr>
                <td align="right"><span class="btnEnviar" onclick="contacto_validarCampos();">Enviar</span></td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	FinCuerpo();
?>
<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="//v2.zopim.com/?3BgsBlMADES5k7vpR2wQMwjlcLZCehK9";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<!--End of Zopim Live Chat Script-->
<?php
	PopUpGenerales();
	FinHTML();
?>