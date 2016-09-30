<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	if (!isset($_SESSION[userCarrito]))
		header("location: index.php");
	
	
	CabeceraHTML("pagar_payment.css,conekta.js,pagar_payment_ver2.js");
	CuerpoHTML();
?>
<div class="pagar_payment_cuerpo">
	<p class="titulo">Información del Pago</p><br /><br />
    <div class="tiposDePago">
        <p><input type="radio" name="formaPago" value="card" style="margin-right:10px;" checked="checked" />Tarjeta Bancaria</p>
        <p><input type="radio" name="formaPago" value="oxxo" style="margin-right:10px;" />Oxxo</p>
    </div><br />
    <form action="" method="POST" id="card-form">
        <table>
            <tbody>
                <tr>
                    <td class="card-errors"></td>
                </tr>
                <tr data-name="data_tarjeta">
                	<td>Nombre del tarjetahabiente</td>
                </tr>
                <tr data-name="data_tarjeta">
                    <td><input type="text" id="pag_nombre" data-conekta="card[name]" class="template_campos" placeholder="Nombre del tarjetahabiente" /></td>
                </tr>
                <tr data-name="data_tarjeta">
                	<td>Número de tarjeta de crédito</td>
                </tr>
                <tr data-name="data_tarjeta">
                    <td><input type="text" id="pag_tarjeta" maxlength="20" data-conekta="card[number]" class="template_campos" placeholder="Número de tarjeta de crédito" /></td>
                </tr>
                <tr data-name="data_tarjeta">
                	<td>CVC</td>
                </tr>
                <tr data-name="data_tarjeta">
                    <td><input type="text" id="pag_cvc" maxlength="3" data-conekta="card[cvc]" class="template_campos" style="width:100px;" placeholder="CVC" /><span style="margin-left:10px; cursor:pointer; color:#852c2b;" onclick="pagarPayment_popupCVC();">¿Dónde está el CVC?</span></td>
                </tr>
                <tr data-name="data_tarjeta">
                    <td>Fecha de expiración (MM/AAAA)</td>
                </tr>
                <tr data-name="data_tarjeta">
                    <td><input type="text" id="pag_mes" maxlength="2" data-conekta="card[exp_month]" style="width:100px;" class="template_campos" placeholder="MM" /><input type="text" id="pag_anio" maxlength="4" data-conekta="card[exp_year]" style="width:100px; margin-left:20px;" class="template_campos" placeholder="AAAA" /></td>
                </tr>
                <tr>
                    <td style="text-align:center; padding-top:40px;">
                        <p id="btnGuardar" class="guardar" onclick="validarCampos();"><a class="btnBotones palomita">Guardar</a>Pagar</p>
                        <span id="mensajeTemporal" style="display:none;">Tu pago esta siendo procesado, espera unos segundos a que aparezca tu clave de confirmación.</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<?php
	FinCuerpo();
	PopUpGenerales("pagarPayment_cerrarPopup");
?>
<div id="pagarPayment_popupCVC" class="templatePopUp pagarPayment_popupCVC">
    <span class="btnCerrar" onclick="template_principalCerrarPopUp(pagarPayment_cerrarPopup);">x</span>
    <table>
        <tbody>
            <tr>
                <td><img src="images/cvc.jpg" alt="cvc" /></td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	FinHTML();
?>