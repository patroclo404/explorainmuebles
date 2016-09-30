<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	$conexion = crearConexionPDO();
	
	
	$idPago = $_GET["idPago"];
	$usuario = array(
		"id"	=> isset($_SESSION[userId]) ? $_SESSION[userId] : -1
	);
	
	
	$consulta =
		"SELECT PIN_CREDITOS, PIN_TOTAL, PIN_VALIDEZ, USU_EMAIL, PIN_INMOBILIARIA
		FROM PAGO_INMOBILIARIA, INMOBILIARIA, USUARIO
		WHERE PIN_INMOBILIARIA = INM_ID
		AND INM_USUARIO = USU_ID
		AND PIN_ID = ?;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idPago));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$creditos = $row["PIN_CREDITOS"];
	$total = $row["PIN_TOTAL"];
	$validez = getDateNormal($row["PIN_VALIDEZ"]);
	$inmobiliaria = array(
		"id"		=>	$row["PIN_INMOBILIARIA"],
		"usuario"	=>	-1
	);

	
	$partes = explode("/", $validez);
	$arrayMeses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	

	$arrayEstado = array();	
	$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayEstado[] = array(
			"id"	=>	$row["EST_ID"],
			"nombre"=>	$row["EST_NOMBRE"]
		);
	}
	
	
	if ($usuario["id"] != -1) {
		$consulta = "SELECT USU_ID, USU_NOMBRE, USU_EMAIL, USU_TELEFONO1, USU_CALLE_NUMERO, USU_ESTADO, USU_CIUDAD, USU_COLONIA, USU_CP, USU_INMOBILIARIA FROM USUARIO WHERE USU_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($usuario["id"]));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		
		$usuario = array(
			"id"			=>	$row["USU_ID"],
			"nombre"		=>	$row["USU_NOMBRE"],
			"email"			=>	$row["USU_EMAIL"],
			"telefono1"		=>	$row["USU_TELEFONO1"] != NULL ? $row["USU_TELEFONO1"] : "",
			"calleNumero"	=>	$row["USU_CALLE_NUMERO"] != NULL ? $row["USU_CALLE_NUMERO"] : "",
			"estado"		=>	$row["USU_ESTADO"] != NULL ? $row["USU_ESTADO"] : -1,
			"ciudad"		=>	$row["USU_CIUDAD"] != NULL ? $row["USU_CIUDAD"] : -1,
			"colonia"		=>	$row["USU_COLONIA"] != NULL ? $row["USU_COLONIA"] : -1,
			"cp"			=>	$row["USU_CP"] != NULL ? $row["USU_CP"] : -1,
			"inmobiliaria"	=>	$row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : -1
		);
		
		
		$consulta = "SELECT INM_USUARIO FROM INMOBILIARIA WHERE INM_ID = ?;";
		$pdo = $conexion->prepare($consulta);
		$pdo->execute(array($inmobiliaria["id"]));
		$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
		$row = $res[0];
		$inmobiliaria["usuario"] = $row["INM_USUARIO"];
	}
	
	
	$variables = "";
	
	if ($usuario["id"] != -1) {
		$variables = "_post_estado=".$usuario["estado"].",_post_ciudad=".$usuario["ciudad"].",_post_colonia=".$usuario["colonia"];
	}
	
	
	CabeceraHTML("pagoInmobiliaria.css,pagoInmobiliaria.js", $variables);
	CuerpoHTML();
?>
<div class="pagoInmobiliaria_cuerpo">
	<p class="titulo">Pago de Inmobiliaria</p><br /><br />
	<p><?php echo "Plan de Inmobiliaria con ".$creditos." créditos y vigencia al ".$partes[0]." de ".$arrayMeses[((int)$partes[1])-1]." del ".$partes[2]; ?></p><br /><br />
    <p class="total">Total: $<?php echo number_format($total, 2, ".", ","); ?></p><br /><br />
    <table <?php echo $usuario["id"] == -1 ? "style='display:none;'" : ($inmobiliaria["usuario"] != $usuario["id"] ? "style='display:none;'" : ""); ?>>
        <tbody>
        	<tr>
            	<td class="_titulo">Dirección de Facturación</td>
            </tr>
            <tr>
            	<td>Nombre Completo</td>
            </tr>
            <tr>
                <td><input type="text" id="pago_nombre" class="template_campos" placeholder="Nombre Completo" maxlength="128" value="<?php echo $usuario["id"] != -1 ? $usuario["nombre"] : ""; ?>" /></td>
            </tr>
            <tr>
            	<td>Calle y Número</td>
            </tr>
            <tr>
                <td><input type="text" id="pago_calleNumero" class="template_campos" placeholder="Calle y Número" maxlength="64" value="<?php echo $usuario["id"] != -1 ? $usuario["calleNumero"] : ""; ?>" /></td>
            </tr>
            <tr>
                <td>
                	<div class="nombreCampos" style="width:285px; padding-right:20px;">
                    	<ul id="pago_estado" class="template_campos">
                            Estado<span></span>
                            <li class="lista">
                                <ul><?php
                                    for ($x = 0; $x < count($arrayEstado); $x++) {
                                        echo "<li data-value='".$arrayEstado[$x]["id"]."'>".$arrayEstado[$x]["nombre"]."</li>";
                                    }												
                                ?></ul>
                            </li>
                            <p data-value="-1"></p>
                            <input type="text" value="" style="position:absolute; top:0px; left:0px; z-index:-1;" />
                        </ul>
                    </div><div class="nombreCampos" style="width:285px;">
                        <ul id="pago_ciudad" class="template_campos">
                            Ciudad<span></span>
                            <li class="lista">
                                <ul></ul>
                            </li>
                            <p data-value="-1"></p>
                            <input type="text" value="" style="position:absolute; top:0px; left:0px; z-index:-1;" />
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
            	<td>
                	<div class="nombreCampos" style="width:285px; padding-right:20px;">
                        <ul id="pago_colonia" class="template_campos">
                            Colonia<span></span>
                            <li class="lista">
                                <ul></ul>
                            </li>
                            <p data-value="-1"></p>
                            <input type="text" value="" style="position:absolute; top:0px; left:0px; z-index:-1;" />
                        </ul>
                    </div><div class="nombreCampos" style="width:285px;">
                        <p style="padding:4px 0px;">Código Postal</p>
                    	<input type="text" id="pago_cp" maxlength="5" class="template_campos" placeholder="Código Postal" value="<?php echo $usuario["id"] != -1 ? "" : ""; ?>" />
                    </div>
                </td>
            </tr>
            <tr>
            	<td>Teléfono</td>
            </tr>
            <tr>
                <td><input type="text" id="pago_telefono" class="template_campos" placeholder="Teléfono" maxlength="16" value="<?php echo $usuario["id"] != -1 ? $usuario["telefono1"] : ""; ?>" /></td>
            </tr>
            <tr>
            	<td style="padding-top:40px;">
                    <p id="btnGuardar" class="guardar" onclick="pago_validarCampos(<?php echo $idPago; ?>);"><a class="btnBotones palomita">Guardar</a>Pagar</p>
               	</td>
            </tr>
        </tbody>
    </table>
    <strong><?php echo $usuario["id"] == -1 ? "Inicie Sesión para realizar el pago." : ($inmobiliaria["usuario"] != $usuario["id"] ? "Sólo el administrador de la inmobiliaria puede realizar el pago." : ""); ?></strong>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>