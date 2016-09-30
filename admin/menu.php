<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0, 1));
	
	adminCabeceraHTML("menu.css");
	adminCuerpoHTML();
?>
<div id="main">
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding:20px;">
        <tbody>
            <tr height="50">
                <td style="border-bottom:1px solid #012851;"><span style="font-size:18px;">Bienvenido <?php echo $_SESSION[adminLogin] ?>!</span></td>
            </tr>
            <tr height="35" valign="bottom"><?php
				$arrayTitulo = array();
				$arrayUrl = array();
				
				if ($_SESSION[adminTipo] == 0) {
					$arrayTitulo_admin = array("Administradores", "Usuarios", "Inmuebles", "Páginas", "Contacto", "Imágen de Portada", "Inmobiliarias", "Desarrollos", "Anuncios Vencidos", "Precio del Anuncio", "Pagos de Inmobiliaria");
					$arrayUrl_admin = array("administrador.php", "usuario.php", "inmueble.php", "pagina.php", "contacto.php", "imagenPortada.php", "inmobiliaria.php", "desarrollo.php", "anunciosVencidos.php", "promocion.php", "pagoInmobiliaria.php");
					
					$arrayTitulo = array_merge($arrayTitulo_admin, $arrayTitulo);
					$arrayUrl = array_merge($arrayUrl_admin, $arrayUrl);
				}
				
				for ($x = 0; $x < count($arrayUrl); $x++) {
					if ($x != 0) {
						echo
							"</tr>".
							"<tr>";
					}
					
					echo
						"<td style='border-top:1px solid #fff;'><a href='".$arrayUrl[$x]."'>".$arrayTitulo[$x]."</a></td>";
				}
            ?></tr>
        </tbody>
    </table>
</div>
<?php
	adminPopUpsGenerales();
	adminFinHTML();
?>