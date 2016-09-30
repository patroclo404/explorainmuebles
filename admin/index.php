<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	
	
	if (isset($_SESSION[adminId]))
		header("Location: menu.php");
	
	
	adminCabeceraHTML("index.css,index.js");
?>
<body>
	<div class="cabecera">
        <div class="cabeceraContenedor">
            <img src="../images/logo.png" class="logo" />
        </div>
    </div>
	<div id="login">
        <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding:0px 15px;">
            <tr height="50">
                <td style="border-bottom:1px solid #012851;"><span style="font-size:16px; font-weight:bold;">Bienvenido</span></td>
            </tr>
            <tr height="50" valign="bottom">
                <td align="center" style="border-top:1px solid #fff;"><input type="text" id="email" class="ObjFocusBlur" placeholder="Email" maxlength="64" /></td>
            </tr>
            <tr height="35">
                <td align="center"><input type="password" id="password" class="ObjFocusBlur" maxlength="32" placeholder="Contraseña" /></td>
            </tr>
            <tr>
                <td align="center" colspan="2">&nbsp;</td>
            </tr>
            <tr height="25">
                <td align="center" colspan="2">
                    <div class="btnOpciones" onClick="index_validaCampos();">Entrar</div>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2">
                    <div id="msg" style="color:#f00;"></div>
                </td>
            </tr>
        </table>
	</div>
</body>
</html>