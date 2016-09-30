<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	$arrayDesarrollos = array();
	
	$conexion = crearConexionPDO();
	$consulta =
		"SELECT
			DES_ID,
			DES_TITULO,
			DES_TIPO,
			DES_ENTREGA,
			DES_UNIDADES,
			DES_DESCRIPCION,
			(
				SELECT IDE_IMAGEN
				FROM IMAGEN_DESARROLLO
				WHERE IDE_DESARROLLO = DES_ID
				ORDER BY IDE_ORDEN DESC
				LIMIT 1
			) AS CONS_IMAGEN,
			(
				SELECT IMU_PRECIO
				FROM INMUEBLE
				WHERE IMU_DESARROLLO = DES_ID
				ORDER BY IMU_PRECIO
				LIMIT 1
			) AS CONS_PRECIO
		FROM DESARROLLO, USUARIO
		WHERE DES_INMOBILIARIA = USU_INMOBILIARIA
		AND USU_ID = ?
		AND (
			SELECT COUNT(IDE_ID)
			FROM IMAGEN_DESARROLLO
			WHERE IDE_DESARROLLO = DES_ID
		) > 0
		ORDER BY DES_TITULO;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($_SESSION[userId]));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayDesarrollos[] = array(
			"id"			=>	$row["DES_ID"],
			"titulo"		=>	$row["DES_TITULO"],
			"tipo"			=>	$row["DES_TIPO"],
			"entrega"		=>	$row["DES_ENTREGA"] != NULL ? $row["DES_ENTREGA"] : "",
			"unidades"		=>	$row["DES_UNIDADES"] != NULL ? $row["DES_UNIDADES"] : "",
			"descripcion"	=>	$row["DES_DESCRIPCION"],
			"imagen"		=>	$urlArchivos.$row["CONS_IMAGEN"],
			"precio"		=>	$row["CONS_PRECIO"] != NULL ? $row["CONS_PRECIO"] : 0
		);
	}
	
	
	CabeceraHTML("misDesarrollos_ver2.css");
	CuerpoHTML();
?>
<div class="misDesarrollos_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
        <div>
        	<p class="titulo">Desarrollos<span onclick="gotoURL('nuevoDesarrollo.php');"><a class='agregar'>+</a>Crear Nuevo Desarrollo</span></p>
            <?php
				$maxCadena = 30;
			
				for ($x = 0; $x < count($arrayDesarrollos); $x++) {
					$textTiulo = strlen($arrayDesarrollos[$x]["titulo"]) > $maxCadena ? (substr($arrayDesarrollos[$x]["titulo"], 0, ($maxCadena - 3))."...") : $arrayDesarrollos[$x]["titulo"];
					
					echo
						"<div class='template_catalogo_contenedorInfo'>
							<table>
								<tbody>
									<tr>
										<td class='imagen'>
											<div style='background:url(".$arrayDesarrollos[$x]["imagen"].") no-repeat center center / 100% auto;' onclick='gotoURL(\"desarrollo.php?id=".$arrayDesarrollos[$x]["id"]."\");'></div>
										</td>
										<td class='descripcion'>
											<p class='like'>
												<span onclick='gotoURL(\"desarrollo.php?id=".$arrayDesarrollos[$x]["id"]."\");'>".$textTiulo."</span><a class='btnBotones editar' href='javascript:gotoURLPOST(\"nuevoDesarrollo.php\", {edit: 1, id: ".$arrayDesarrollos[$x]["id"]."});'>Editar</a>
											</p>
											<div class='info' onclick='gotoURL(\"desarrollo.php?id=".$arrayDesarrollos[$x]["id"]."\");'>
												<p>".($arrayDesarrollos[$x]["tipo"] == 0 ? "Horizontal" : "Vertical")."</p>
												<p>".($arrayDesarrollos[$x]["unidades"] != "" ? ($arrayDesarrollos[$x]["unidades"]." Unidades") : "")."</p>
												<p>".($arrayDesarrollos[$x]["entrega"] != "" ? ("Entrega ".$arrayDesarrollos[$x]["entrega"]) : "")."</p><br />
												<p class='descripcion'>".$arrayDesarrollos[$x]["descripcion"]."</p>
											</div>
											<div class='precioVerMas'><span class='precio' onclick='gotoURL(\"desarrollo.php?id=".$arrayDesarrollos[$x]["id"]."\");'>Precio desde: $".number_format($arrayDesarrollos[$x]["precio"], 0, ".", ",")." MXN</span><span class='verMas' onclick='gotoURL(\"inmuebleDesarrollo.php?id=".$arrayDesarrollos[$x]["id"]."\");'>Inmuebles</span></div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>";
				}
			?>
        </div>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>