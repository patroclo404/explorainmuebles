<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	$usuario = isset($_SESSION[userId]) ? $_SESSION[userId] : -1;
	$idDesarrollo = $_GET["id"];
	$urlArchivos = "images/images/";
	
	
	$desarrollo = array();
	$conexion = crearConexionPDO();
	$consulta =
		"SELECT
			DES_ID,
			DES_TITULO,
			DES_TIPO,
			DES_ENTREGA,
			DES_UNIDADES,
			DES_LATITUD,
			DES_LONGITUD,
			DES_DESCRIPCION,
			( ".(
				$usuario != -1
				? (
					"SELECT FDE_ID
					FROM FAVORITO_DESARROLLO
					WHERE FDE_USUARIO = :usuario
					AND FDE_DESARROLLO = DES_ID"
				) : -1
			)." ) AS CONS_LIKE
		FROM DESARROLLO
		WHERE DES_ID = :idDesarrollo;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":usuario" => $usuario, ":idDesarrollo" => $idDesarrollo));
	$res = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$row = $res[0];
	$desarrollo = array(
		"id"			=>	$idDesarrollo,
		"titulo"		=>	$row["DES_TITULO"],
		"tipo"			=>	$row["DES_TIPO"],
		"entrega"		=>	$row["DES_ENTREGA"] != NULL ? $row["DES_ENTREGA"] : "",
		"unidades"		=>	$row["DES_UNIDADES"] != NULL ? $row["DES_UNIDADES"] : "",
		"latitud"		=>	$row["DES_LATITUD"],
		"longitud"		=>	$row["DES_LONGITUD"],
		"descripcion"	=>	$row["DES_DESCRIPCION"],
		"like"			=>	$row["CONS_LIKE"] == NULL ? "0" : $row["CONS_LIKE"],
		"imagenes"		=>	array(),
		"inmuebles"		=>	array()
	);
	
	
	$consulta = "SELECT IDE_IMAGEN FROM IMAGEN_DESARROLLO WHERE IDE_DESARROLLO = ? ORDER BY IDE_ORDEN DESC;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array($idDesarrollo));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$desarrollo["imagenes"][] = $urlArchivos.$row["IDE_IMAGEN"];
	}
	
	
	$consulta =
		"SELECT
			IMU_ID,
			IMU_TITULO,
			IMU_PRECIO,
			(
				SELECT IIN_IMAGEN
				FROM IMAGEN_INMUEBLE
				WHERE IIN_INMUEBLE = IMU_ID
				ORDER BY IIN_ORDEN DESC LIMIT 1
			) AS CONS_IMAGEN
		FROM INMUEBLE
		WHERE IMU_DESARROLLO = :idDesarrollo
		AND(
			SELECT COUNT(IIN_ID)
			FROM IMAGEN_INMUEBLE
			WHERE IIN_INMUEBLE = IMU_ID
		) > 0 
		AND IMU_LIMITE_VIGENCIA >= :vigencia
		ORDER BY IMU_PRECIO, IMU_TITULO;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":idDesarrollo" => $idDesarrollo, ":vigencia" => date("Y-m-d")));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$desarrollo["inmuebles"][] = array(
			"id"		=>	$row["IMU_ID"],
			"titulo"	=>	$row["IMU_TITULO"],
			"precio"	=>	$row["IMU_PRECIO"],
			"imagen"	=>	$urlArchivos.$row["CONS_IMAGEN"]
		);
	}
	
	
	$arrayRazonReporte = array();
	
	$consulta = "SELECT RAR_ID, RAR_NOMBRE FROM RAZON_REPORTE ORDER BY RAR_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayRazonReporte[] = array(
			"id"		=>	$row["RAR_ID"],
			"nombre"	=>	$row["RAR_NOMBRE"]
		);
	}
	
	
	$metasFacebook = array(
		"titulo"		=>	$desarrollo["titulo"],
		"imagen"		=>	$desarrollo["imagenes"][0],
		"descripcion"	=>	$desarrollo["descripcion"],
		"url"			=>	"desarrollo.php?id=".$desarrollo["id"]
	);
	
	
	CabeceraHTML("desarrollo_ver2.css,desarrollo_ver6.js", NULL, $metasFacebook);
	CuerpoHTML();
?>
<div class="desarrollo_cuerpo">
	<div class="columna1"><?php
    	template_busquedaAvanzada();
    ?></div><div class="columna2">
    	<p class="titulo">
			<?php echo $desarrollo["titulo"]; ?>
        </p>
        <table class="contenedorInfo">
        	<tbody>
            	<tr>
                	<td class="imagen">
                    	<img class="imagenPrincipal" src="<?php echo $desarrollo["imagenes"][0]; ?>" alt="<?php echo $desarrollo["titulo"]; ?>" data-pos="0" />
                        <div class="galeria">
							<div class="contenedorFlechas">
                            	<a class="flechas prev">Prev</a>
                            </div><div class="contenedorDesplazamiento">
								<div class="desplazamiento"><?php
									for ($x = 0; $x < count($desarrollo["imagenes"]); $x++) {
										echo
											"<div class='bloque'>
												<img src='".$desarrollo["imagenes"][$x]."' alt='Imagen ".($x + 1)."' />
											</div>";
									}
								?></div>
							</div><div class="contenedorFlechas">
                            	<a class="flechas next">Next</a>
                           	</div>
                        </div>
                    </td>
                    <td class="descripcionBotones">
                        <p><a class="btnBotones mundo" data-label="desarrollo_mapa">Mundo</a><a class="otrosBotones" data-label="desarrollo_mapa">Ver Ubicación</a></p>
                        <p><a class="btnBotones estrella <?php echo $desarrollo["like"] > 0 ? "activo" : ""; ?>" data-id="<?php echo $desarrollo["like"]; ?>" data-desarrollo="<?php echo $desarrollo["id"]; ?>">Like</a><a class="otrosBotones">Favoritos</a></p>
                        <p><a class="btnBotones compartir" href="javascript:desarrollo_botonesCompartir();">Compartir</a><a class="otrosBotones" href="javascript:desarrollo_botonesCompartir();">Compartir este anuncio</a></p>
                        <div id="desarrollo_botonesCompartir" class="contenedorCompartir">
            				<a class="template_btnsShare facebook" data-url="desarrollo.php?id=<?php echo $desarrollo["id"]; ?>">Facebook</a>
                            <a class="template_btnsShare twitter" data-url="desarrollo.php?id=<?php echo $desarrollo["id"]; ?>" data-titulo="<?php echo $desarrollo["titulo"]; ?>">Twitter</a>
                            <a class="template_btnsShare email" data-url="desarrollo.php?id=<?php echo $desarrollo["id"]; ?>">Email</a>
                        </div>
                        <p><a class="btnBotones contacto" data-label="desarrollo_contacto">Contactar</a><a class="otrosBotones" data-label="desarrollo_contacto">Contactar al anunciante</a></p>
                        <p><a class="btnBotones pdf">PDF</a><a class="otrosBotones">Guardar en PDF</a></p>
                        <p><a class="btnBotones imprimir" href="javascript:window.print();">Imprimir</a><a class="otrosBotones" href="javascript:window.print();">Imprime este anuncio</a></p>
                        <p><a class="btnBotones reportar" href="javascript:desarrollo_reportarAnuncio();">Reportar</a><a class="otrosBotones" href="javascript:desarrollo_reportarAnuncio();">Reportar este anuncio</a></p>
                        <div id="desarrollo_reportarAnuncio" class="reportarAnuncio">
                        	<ul id="reportar_motivo" class="template_campos">
                                Motivo<span></span>
                                <li class="lista">
                                    <ul><?php
										for ($x = 0; $x < count($arrayRazonReporte); $x++) {
											echo "<li data-value='".$arrayRazonReporte[$x]["id"]."'>".$arrayRazonReporte[$x]["nombre"]."</li>";
										}
									?></ul>
                                </li>
                                <p data-value="-1"></p>
                                <input type='text' value='' style='position:absolute; top:0px; left:0px; z-index:-1;' />
                            </ul>
                            <span class="btnEnviar" onclick="desarrollo_validarReporte();" data-desarrollo="<?php echo $desarrollo["id"]; ?>">Enviar</span>
                        </div>
                    </td>
                </tr>
                <tr>
                	<td colspan="2" class="descripcion">
                        <p><?php echo $desarrollo["descripcion"]; ?></p><br /><br />
                        <table>
                        	<tbody>
                            	<tr>
                                	<td class="subtitulo">Precio desde</td>
                                    <?php echo $desarrollo["unidades"] != "" ? "<td class='subtitulo'>Unidades</td>" : ""; ?>
                                </tr>
                                <tr>
                                	<td>$ <?php echo number_format((count($desarrollo["inmuebles"]) > 0 ? $desarrollo["inmuebles"][0]["precio"] : 0), 2, ".", ","); ?></td>
                                    <?php echo $desarrollo["unidades"] != "" ? "<td>".$desarrollo["unidades"]."</td>" : ""; ?>
                        		</tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="inmuebles">
        	<tbody><?php
				for ($x = 0; $x < count($desarrollo["inmuebles"]); $x++) {
					echo
						"<tr>
							<td class='imagen'><a href='inmueble.php?id=".$desarrollo["inmuebles"][$x]["id"]."'><img src='".$desarrollo["inmuebles"][$x]["imagen"]."' alt='".$desarrollo["inmuebles"][$x]["titulo"]."' /></a></td>
							<td><a href='inmueble.php?id=".$desarrollo["inmuebles"][$x]["id"]."'>".$desarrollo["inmuebles"][$x]["titulo"]."</a></td>
							<td class='precio'>$ ".number_format($desarrollo["inmuebles"][$x]["precio"], 2, ".", ",")."</td>
						</tr>";
				}
            ?></tbody>
        </table>
        <div id="desarrollo_mapa" class="desarrollo_mapa" data-latitud="<?php echo $desarrollo["latitud"] ?>" data-longitud="<?php echo $desarrollo["longitud"]; ?>"></div>
        <table class="desarrollo_contacto">
        	<tbody>
            	<tr>
                	<td colspan="2" class="titulo">Contacto<a class="btnBotones email">Email</a></td>
                </tr>
            	<tr>
                	<td>Nombre:</td>
                    <td><input type="text" id="desarrollo_nombre" class="template_campos" placeholder="Nombre" /></td>
                </tr>
                <tr>
                	<td>E-mail:</td>
                    <td><input type="text" id="desarrollo_email" class="template_campos" placeholder="E-mail" /></td>
                </tr>
                <tr>
                	<td>Teléfono:</td>
                    <td><input type="text" id="desarrollo_telefono" class="template_campos" placeholder="Teléfono" /></td>
                </tr>
                <tr>
                	<td>Mensaje:</td>
                    <td><textarea id="desarrollo_mensaje" class="template_campos" placeholder="Mensaje"></textarea></td>
                </tr>
                <tr>
                	<td colspan="2" align="right"><span class="btnEnviar" data-desarrollo="<?php echo $desarrollo["id"]; ?>" onclick="desarrollo_validarContacto();">Enviar</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales("desarrollo_cerrarPopup");
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<div id="desarrollo_mostrarImagen" class="templatePopUp desarrollo_mostrarImagen">
    <span class="btnCerrar" onclick="template_principalCerrarPopUp(desarrollo_cerrarPopup);">x</span>
    <table>
        <tbody>
            <tr>
                <td>
                	<img src="" alt="Imágen" data-pos="0" />
                    <a class="flechas prev">Prev</a>
					<a class="flechas next">Next</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php
	FinHTML();
?>