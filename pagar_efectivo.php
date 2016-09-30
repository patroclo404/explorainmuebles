<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
		
	unset($_SESSION[userCarrito]);
		
		
	$reference_id = $_GET["reference_id"];
	$partes = explode("_", $reference_id);
	$idPago = $partes[1];
	
	
	$barcode = $_GET["barcode"];
	$total = $_GET["total"];
	
	
	CabeceraHTML("pagar_efectivo.css");
	CuerpoHTML();
?>
<div class="pagar_efectivo_cuerpo">
    <p class="titulo">Información del Pago</p>
    <table>
        <tbody>
            <tr>
                <td colspan="2" style="font-weight:bold; text-align:center; padding-bottom:20px;">
                	GRACIAS POR TU ORDEN<br />
                    <span style="font-weight:bold; font-size:16px;">¡YA SOLO TE FALTA UN PASO!</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom:30px;">Tu orden quedará reservada por un periodo de tres horas durante las cuales deberás realizar tu pago en tu Oxxo más cercano.</td>
            </tr>
            <tr>
                <td width="40%">Código de Barras</td>
                <td align="right"><img src="<?php echo $barcode; ?>" /></td>
            </tr>
            <tr>
                <td style="padding-top:10px;">Importe</td>
                <td align="right" style="padding-top:10px;">$<?php echo $total; ?> MXN</td>
            </tr>
            <tr>
                <td>Compañia</td>
                <td align="right">EXPLORA INMUEBLES</td>
            </tr>
            <tr>
                <td>Referencia</td>
                <td align="right"><?php echo $idPago; ?></td>
            </tr>
            <tr>
                <td colspan="2" align="center" style="padding-top:50px;"><div class="btn_general" onclick="window.print();">IMPRIMIR FICHA DE PAGO</div></td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>