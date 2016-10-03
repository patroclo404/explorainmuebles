<?php

	require_once("../lib_php/template.php");
	require_once("lib_php/template.php");
	validar_credenciales(array(0));
	$conexion = crearConexionPDO();
	
	
	adminCabeceraHTML("inmueble_ver12.css,inmueble_ver20.js");
	adminCuerpoHTML("inmueble_inicializarBotones();");
	
	
	$arrayCamposWidth = array();
	$titulos = array("Título", "Anunciante", "ID", "Publicación", "Vencimiento", "Visitas", "Contactado", "Estado");//, "Categoría", "Tipo", "Código"
	$widths = array(NULL, 180, 50, 100, 100, 60, 90, 110);//, 135, 120, 100
	
	for ($x = 0; $x < count($titulos); $x++) {
		$arrayCamposWidth[] = array(
			"titulo"	=>	$titulos[$x],
			"width"		=>	$widths[$x]
		);
	}
	
	
	$arrayEstado = array();
	
	$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayEstado[] = array(
			"id"	=>	$row["EST_ID"],
			"nombre"=>	$row["EST_NOMBRE"]
		);
	}
	
	
	$arrayCategoria = array();
	
	$consulta = "SELECT CIN_ID, CIN_NOMBRE FROM CATEGORIA_INMUEBLE ORDER BY CIN_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayCategoria[] = array(
			"id"		=>	$row["CIN_ID"],
			"nombre"	=>	$row["CIN_NOMBRE"]
		);
	}
	
	
	$arrayTipoCategoria = array();
	
	$consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$_arrayCategorias = array();
		$consulta2 = "SELECT TCA_CATEGORIA_INMUEBLE FROM TIPO_INMUEBLE_CATEGORIA_INMUEBLE WHERE TCA_TIPO_INMUEBLE = ? ORDER BY TCA_ID;";
		$pdo2 = $conexion->prepare($consulta2);
		$pdo2->execute(array($row["TIN_ID"]));
		foreach($pdo2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
			$_arrayCategorias[] = $row2["TCA_CATEGORIA_INMUEBLE"];
		}
		
		$arrayTipoCategoria[] = array(
			"id"		=>	$row["TIN_ID"],
			"nombre"	=>	$row["TIN_NOMBRE"],
			"categorias"=>	$_arrayCategorias
		);
	}
	
	
	$arrayTransaccion = array();
	
	$consulta = "SELECT TRA_ID, TRA_NOMBRE FROM TRANSACCION ORDER BY TRA_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayTransaccion[] = array(
			"id"		=>	$row["TRA_ID"],
			"nombre"	=>	$row["TRA_NOMBRE"]
		);
	}
	
	
	$arrayUsuario = array();
	
	$consulta = "SELECT USU_ID, USU_NOMBRE, USU_INMOBILIARIA FROM USUARIO ORDER BY USU_NOMBRE;";
	foreach($conexion->query($consulta) as $row) {
		$arrayUsuario[] = array(
			"id"			=>	$row["USU_ID"],
			"nombre"		=>	$row["USU_NOMBRE"],
			"inmobiliaria"	=>	$row["USU_INMOBILIARIA"] != NULL ? $row["USU_INMOBILIARIA"] : -1
		);
	}
	
	
	adminMainHTML("Lista de los Inmuebles", $arrayCamposWidth, true, true);
	adminPopUpsGenerales("inmueble_cerrarPopUp");
?>
<div id="backImage">
    <table cellspacing="0" border="0" width="100%" style="padding:10px;">
        <tbody>
            <tr height="50">
                <td id="tituloEmergente" style="font-size:18px; border-bottom:1px solid #012851;"></td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas" data-pestana="caracteristicas">Características<span class="inmueble_pestana">+</span></div></td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><input type="text" id="titulo" class="ObjFocusBlur" placeholder="Título" maxlength="256" />*</td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><select id="usuario" class="ObjFocusBlur">
                    <option value="-1" class="off">Usuario</option>
                    <?php
                        $consulta = "SELECT USU_ID, USU_NOMBRE, USU_INMOBILIARIA FROM USUARIO ORDER BY USU_NOMBRE;";
                        foreach($conexion->query($consulta) as $row) {
                            echo "<option value='".$row["USU_ID"]."' data-inmobiliaria='".$row["USU_INMOBILIARIA"]."'>".$row["USU_NOMBRE"]."</option>";
                        }
                    ?>
                </select>*</td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><select id="categoria" class="ObjFocusBlur">
                	<option value="-1" class="off">Categoría</option>
                    <?php
						$consulta = "SELECT CIN_ID, CIN_NOMBRE FROM CATEGORIA_INMUEBLE ORDER BY CIN_NOMBRE;";
						foreach($conexion->query($consulta) as $row) {
							echo "<option value='".$row["CIN_ID"]."'>".$row["CIN_NOMBRE"]."</option>";
						}
					?>
                </select>*</td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><select id="tipo" class="ObjFocusBlur">
                	<option value="-1" class="off">Tipo</option>
                    <?php
						$consulta = "SELECT TIN_ID, TIN_NOMBRE FROM TIPO_INMUEBLE ORDER BY TIN_NOMBRE;";
						foreach($conexion->query($consulta) as $row) {
							$arrayCategorias = array();
							$consulta2 = "SELECT TCA_CATEGORIA_INMUEBLE FROM TIPO_INMUEBLE_CATEGORIA_INMUEBLE WHERE TCA_TIPO_INMUEBLE = ? ORDER BY TCA_ID;";
							$pdo2 = $conexion->prepare($consulta2);
							$pdo2->execute(array($row["TIN_ID"]));
							foreach($pdo2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
								$arrayCategorias[] = $row2["TCA_CATEGORIA_INMUEBLE"];
							}
							
							echo "<option value='".$row["TIN_ID"]."' data-categorias='".implode(",", $arrayCategorias)."'>".$row["TIN_NOMBRE"]."</option>";
						}
					?>
                </select>*</td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><input type="text" id="precio" class="ObjFocusBlur" placeholder="Precio" maxlength="18" />*</td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><input type="text" id="calleNumero" class="ObjFocusBlur" placeholder="Calle y Número" />*</td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="estado" class="ObjFocusBlur">
                	<option value="-1" class="off">Estado</option>
                    <?php
						$consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
						foreach($conexion->query($consulta) as $row) {
							echo "<option value='".$row["EST_ID"]."'>".$row["EST_NOMBRE"]."</option>";
						}
					?>
                </select>*</td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="ciudad" class="ObjFocusBlur">
                	<option value="-1" class="off">Ciudad</option>
                </select>*</td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="colonia" class="ObjFocusBlur">
                	<option value="-1" class="off">Colonia</option>
                </select>*</td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><input type="text" id="latitud" class="ObjFocusBlur" placeholder="Latitud" maxlength="32" />*</td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><input type="text" id="longitud" class="ObjFocusBlur" placeholder="Longitud" maxlength="32" />*</td>
            </tr>
            <tr name="caracteristicas">
            	<td><textarea id="descripcion" class="ObjFocusBlur" placeholder="Descripción"></textarea></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="wcs" class="ObjFocusBlur">
                	<option value="-1" class="off">Baños</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">Más de 10</option>
                </select></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="recamaras" class="ObjFocusBlur">
                	<option value="-1" class="off">Recamaras</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">Más de 10</option>
                </select></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="antiguedad" class="ObjFocusBlur">
                	<option value="-1" class="off">Antig&uuml;edad</option>
                    <option value="1">0 Años</option>
                    <option value="2">1 Año</option>
                    <option value="3">2 Años</option>
                    <option value="4">3 Años</option>
                    <option value="5">4 Años</option>
                    <option value="6">5 - 9 Años</option>
                    <option value="7">10 - 19 Años</option>
                    <option value="8">20 - 29 Años</option>
                    <option value="9">30 - 39 Años</option>
                    <option value="10">40 - 49 Años</option>
                    <option value="11">50 Años ó mas</option>
                </select></td>
            </tr>
            <tr height="35" name="caracteristicas">
            	<td><select id="estadoConservacion" class="ObjFocusBlur">
                	<option value="-1" class="off">Estado de Conservación</option>
                    <option value="1">Excelente</option>
                    <option value="2">Bueno</option>
                    <option value="3">Regular</option>
                    <option value="4">Malo</option>
                    <option value="5">Muy Malo</option>
                </select></td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><input type="text" id="dimensionTotal" class="ObjFocusBlur" placeholder="Dimensión Total" maxlength="11" /></td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><input type="text" id="dimensionConstruida" class="ObjFocusBlur" placeholder="Dimensión Construida" maxlength="11" data-campos="1" /></td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><input type="text" id="metrosFrente" class="ObjFocusBlur" placeholder="Metros de Frente" maxlength="11" /></td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><input type="text" id="metrosFondo" class="ObjFocusBlur" placeholder="Metros de Fondo" maxlength="11" /></td>
            </tr>
            <tr height="35" name="caracteristicas">
                <td><select id="desarrollo" class="ObjFocusBlur">
                    <option value="-1" class="off">Desarrollo</option>
                    <?php
                        $consulta = "SELECT DES_ID, DES_TITULO FROM DESARROLLO ORDER BY DES_TITULO;";
                        foreach($conexion->query($consulta) as $row) {
                            echo "<option value='".$row["DES_ID"]."'>".$row["DES_TITULO"]."</option>";
                        }
                    ?>
                </select></td>
            </tr>
            <tr id="celdaCodigo" height="35" name="caracteristicas">
            	<td><input type="text" id="codigo" class="ObjFocusBlur" placeholder="Código" maxlength="64" /></td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas" data-pestana="ambientes">Ambientes<span class="inmueble_pestana">+</span></div></td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="cocinaEquipada" data-campos="1" style="margin-right:10px;" />Cocina Equipada
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="estudio" data-campos="1" style="margin-right:10px;" />Estudio
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="cuartoServicio" data-campos="1" style="margin-right:10px;" />Cuarto de Servicio
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="cuartoTV" data-campos="1" style="margin-right:10px;" />Cuarto de TV
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="bodega" data-campos="1" style="margin-right:10px;" />Bodega
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="terraza" data-campos="1" style="margin-right:10px;" />Terraza
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="jardin" data-campos="1" style="margin-right:10px;" />Jardín
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="areaJuegosInfantiles" data-campos="1" style="margin-right:10px;" />Área de Juegos Infantiles
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="comedor" style="margin-right:10px;" data-campos="1" />Comedor
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="sala" style="margin-right:10px;" />Sala
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="biblioteca" style="margin-right:10px;" />Biblioteca
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="usosMultiples" style="margin-right:10px;" />Salón de Usos Múltiples
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="recibidor" style="margin-right:10px;" />Recibidor
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="vestidor" style="margin-right:10px;" />Vestidor
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="oratorio" style="margin-right:10px;" />Oratorio
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="cava" style="margin-right:10px;" />Cava
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="patio" style="margin-right:10px;" />Patio
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="balcon" style="margin-right:10px;" />Balcón
                    </div>
                </td>
            </tr>
            <tr height="35" name="ambientes">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="lobby" style="margin-right:10px;" />Lobby
                    </div><div class="template_contenedorCeldas2">
                    </div>
                </td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas" data-pestana="servicios">Servicios<span class="inmueble_pestana">+</span></div></td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="serviciosBasicos" style="margin-right:10px;" />Servicios Básicos
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="gas" data-campos="1" style="margin-right:10px;" />Gas
                    </div>
                </td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="lineaTelefonica" data-campos="1" style="margin-right:10px;" />Línea Telefónica
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="internetDisponible" data-campos="1" style="margin-right:10px;" />Internet Disponible
                    </div>
                </td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="aireAcondicionado" data-campos="1" style="margin-right:10px;" />Aire Acondicionado
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="calefaccion" data-campos="1" style="margin-right:10px;" />Calefacción
                    </div>
                </td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="casetaVigilancia" data-campos="1" style="margin-right:10px;" />Caseta de Vigilancia
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="seguridad" style="margin-right:10px;" />Seguridad
                    </div>
                </td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="cisterna" style="margin-right:10px;" />Cisterna
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="calentador" style="margin-right:10px;" />Calentador
                    </div>
                </td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="camaras" style="margin-right:10px;" />Cámaras de Vigilancia
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="anden" style="margin-right:10px;" />Andén
                    </div>
                </td>
            </tr>
            <tr height="35" name="servicios">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="chkCuotaMantenimiento" style="margin-right:10px;" /><span>Cuota Mantenimiento</span>
                    	<input type="text" id="cuotaMantenimiento" class="ObjFocusBlur" placeholder="Cuota Mantenimiento" maxlength="10" style="width:70%;" />
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="chkElevador" data-campos="1" style="margin-right:10px;" /><span>Elevador</span>
                    	<input type="text" id="elevador" class="ObjFocusBlur" placeholder="Elevador" maxlength="11" style="width:70%;" />
                    </div>
                </td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas" data-pestana="amenidades">Amenidades<span class="inmueble_pestana">+</span></div></td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="alberca" data-campos="1" style="margin-right:10px;" />Alberca
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="casaClub" data-campos="1" style="margin-right:10px;" />Casa Club
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="canchaTenis" data-campos="1" style="margin-right:10px;" />Cancha de Tenis
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="vistaMar" data-campos="1" style="margin-right:10px;" />Vista al Mar
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="jacuzzi" data-campos="1" style="margin-right:10px;" />Jacuzzi
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="permiteMascotas" data-campos="1" style="margin-right:10px;" />Se Permite Mascotas
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="gimnasio" data-campos="1" style="margin-right:10px;" />Gimnasio
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="vapor" style="margin-right:10px;" />Vapor
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="sauna" style="margin-right:10px;" />Sauna
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="asador" style="margin-right:10px;" />Asador
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="playa" style="margin-right:10px;" />Playa
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="clubPlaya" style="margin-right:10px;" />Club de Playa
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="portonElectrico" style="margin-right:10px;" />Portón Eléctrico
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="chimenea" style="margin-right:10px;" />Chimenea
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="areasVerdes" style="margin-right:10px;" />Áreas Verdes
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="vistaPanoramica" style="margin-right:10px;" />Vista Panorámica
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="canchaSquash" style="margin-right:10px;" />Cancha de Squash
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="canchaBasket" style="margin-right:10px;" />Cancha de Basketball
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="canchaFut" style="margin-right:10px;" />Cancha de Fútbol
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="salaCine" style="margin-right:10px;" />Sala de Cine
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="familyRoom" style="margin-right:10px;" />Family Room
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="campoGolf" style="margin-right:10px;" />Campo de Golf
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="cableTV" style="margin-right:10px;" />Televisión por Cable
                    </div><div class="template_contenedorCeldas2">
                    </div>
                </td>
            </tr>
            <tr height="35" name="amenidades">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="chkEstacionamientoVisitas" data-campos="1" style="margin-right:10px;" /><span>Estacionamiento para Visitas</span>
                    	<input type="text" id="estacionamientoVisitas" class="ObjFocusBlur" placeholder="Estacionamiento para Visitas" maxlength="11" style="width:70%;" />
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="chkCajonesEstacionamiento" style="margin-right:10px;" /><span>Cajones de Estacionamiento</span>
                    	<input type="text" id="cajonesEstacionamiento" class="ObjFocusBlur" placeholder="Cajones de Estacionamiento" maxlength="11" style="width:70%;" />
                    </div>
                </td>
            </tr>
            <tr height="35">
            	<td><div class="template_contenedorCeldas" data-pestana="otros">Otras Características<span class="inmueble_pestana">+</span></div></td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="amueblado2" style="margin-right:10px;" />Amueblado
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="semiAmueblado" style="margin-right:10px;" />Semi Amueblado
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="fumadoresPermitidos" data-campos="1" style="margin-right:10px;" />Fumadores Permitidos
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="excelenteUbicacion" style="margin-right:10px;" />Excelente Ubicación
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="centrosComercialesCercanos" style="margin-right:10px;" />Centros Comerciales Cercanos
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="escuelasCercanas" style="margin-right:10px;" />Escuelas Cercanas
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="hospitalesCercanos" style="margin-right:10px;" />Hospitales Cercanos
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="baresCercanos" style="margin-right:10px;" />Bares y Restaurantes
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="iglesiasCercanas" style="margin-right:10px;" />Iglesias Cercanas
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="supermercadosCercanos" style="margin-right:10px;" />Supermercados Cercanos
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="zonaIndustrial" style="margin-right:10px;" />Zona Industrial
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="zonaTuristica" style="margin-right:10px;" />Zona Turística
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="zonaComercial" style="margin-right:10px;" />Zona Comercial
                    </div><div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="zonaResidencial" style="margin-right:10px;" />Zona Residencial
                    </div>
                </td>
            </tr>
            <tr height="35" name="otros">
            	<td>
                	<div class="template_contenedorCeldas2">
                    	<input type="checkbox" id="chkNumeroOficinas" data-campos="1" style="margin-right:10px;" /><span>Número de Oficinas</span>
                    	<input type="text" id="numeroOficinas" class="ObjFocusBlur" placeholder="Número de Oficinas" maxlength="11" style="width:70%;" />
                    </div><div class="template_contenedorCeldas2">
                    </div>
                </td>
            </tr>
            <tr height="50">
                <td>
                    <div id="btnGuardar" class="btnOpciones" onClick="validarCampos();">Guardar</div>
                    <span id="mensajeTemporal" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="inmueble_abrirModificarImagenes" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td colspan="4" style="font-size:18px; border-bottom:1px solid #aeadb3;">Imágenes</td>
            </tr>
            <tr height="35">
            	<td style="border-top:1px solid #fff;" align="left">Modificar</td>
                <td width="120" style="text-align:center;">Imágen</td>
                <td width="120" style="text-align:center;">Principal</td>
                <td width="30" align="right" style="border-top:1px solid #fff;"><img style="cursor:pointer;" src="images/btnAgregar.png" onclick="inmueble_subirImagen(-1);" /></td>
            </tr>
            <tr>
            	<td colspan="4" style="border-bottom:1px solid #aeadb3;"><div id="contenedorInmuebleImagenes" style="width:100%; height:300px; overflow:auto;"></div></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="inmueble_abrirModificarVideos" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td colspan="2" style="font-size:18px; border-bottom:1px solid #aeadb3;">Videos</td>
            </tr>
            <tr height="35">
            	<td style="border-top:1px solid #fff;" align="left">Url del Video</td>
                <td width="30" align="right" style="border-top:1px solid #fff;"><img style="cursor:pointer;" src="images/btnAgregar.png" onclick="inmueble_subirVideo(-1);" /></td>
            </tr>
            <tr>
            	<td colspan="2" style="border-bottom:1px solid #aeadb3;"><div id="contenedorInmuebleVideos" style="width:100%; height:300px; overflow:auto;"></div></td>
            </tr>
        </tbody>
    </table>
</div>
<div id="inmueble_abrirModificarTransacciones" class="classPopUp">
	<table>
    	<tbody>
        	<tr height="50">
            	<td colspan="2" style="font-size:18px; border-bottom:1px solid #aeadb3;">Transacciones</td>
            </tr>
            <tr height="35">
            	<td style="border-top:1px solid #fff;" align="left">Transacción</td>
                <td width="30" align="right" style="border-top:1px solid #fff;"></td>
            </tr>
            <tr>
            	<td colspan="2" style="border-bottom:1px solid #aeadb3;"><div id="contenedorInmuebleTransacciones" style="width:100%; height:300px; overflow:auto;"></div></td>
            </tr>
            <tr height="50">
                <td colspan="2">
                    <div id="btnGuardar5" class="btnOpciones" onClick="validarCampos5();">Guardar</div>
                    <span id="mensajeTemporal5" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="inmueble_abrirModificarUsuario" class="classPopUp inmueble_abrirModificarUsuario">
	<form id="subirUsuario" method="post" enctype="multipart/form-data" action="lib_php/updUsuario.php">
    	<input type="text" id="idUsuario" name="id" style="display:none;" />
        <table cellspacing="0" border="0" width="100%" style="padding:10px;">
            <tbody>
                <tr height="50">
                    <td style="font-size:18px; border-bottom:1px solid #012851;">Modificar Usuario</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_nombre" name="nombre" class="ObjFocusBlur" placeholder="Nombre Completo" maxlength="128" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_email" name="email" class="ObjFocusBlur" placeholder="Email" maxlength="64" />*</td>
                </tr>
                <tr height="35" style="display:none;">
                    <td><input type="password" id="usu_password" name="password" class="ObjFocusBlur" placeholder="Contraseña" maxlength="32" />*</td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_FBid" name="FBid" class="ObjFocusBlur" placeholder="Facebook Id" maxlength="32" /></td>
                </tr>
                <tr height="35">
                    <td><div class="template_contenedorCeldas">
                        <input type="radio" name="sexo" value="H" style="margin-right:10px;" />Hombre
                        <input type="radio" name="sexo" value="M" style="margin:0px 10px 0px 50px;" />Mujer
                    </div></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_fechaNac" name="fechaNac" class="ObjFocusBlur" placeholder="Fecha de Nacimiento" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_telefono1" name="telefono1" class="ObjFocusBlur" placeholder="Teléfono 1" maxlength="16" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_telefono2" name="telefono2" class="ObjFocusBlur" placeholder="Teléfono 2" maxlength="16" /></td>
                </tr>
                <tr height="35">
                    <td><input type="text" id="usu_calleNumero" name="calleNumero" class="ObjFocusBlur" placeholder="Calle y Número" /></td>
                </tr>
                <tr height="35">
                    <td><select id="usu_estado" name="estado" class="ObjFocusBlur">
                        <option value="-1" class="off">Estado</option>
                        <?php
                            $consulta = "SELECT EST_ID, EST_NOMBRE FROM ESTADO ORDER BY EST_NOMBRE;";
                            foreach($conexion->query($consulta) as $row) {
                                echo "<option value='".$row["EST_ID"]."'>".$row["EST_NOMBRE"]."</option>";
                            }
                        ?>
                    </select></td>
                </tr>
                <tr height="35">
                    <td><select id="usu_ciudad" name="ciudad" class="ObjFocusBlur">
                        <option value="-1" class="off">Ciudad</option>
                    </select></td>
                </tr>
                <tr height="35">
                    <td>
                    	<select id="usu_colonia" name="colonia" class="ObjFocusBlur">
                        	<option value="-1" class="off">Colonia</option>
                    	</select>
                    	<input type="text" id="usu_cp" name="cp" style="display:none;" />
                    </td>
                </tr>
                <tr height="35">
                	<td>
                    	<div class="template_contenedoresCeldas">Imágen: <a href="" id="usu_imagenActual" target="_blank">Ver imágen</a></div>
                    </td>
                </tr>
                <tr height="35">
                	<td><input type="file" id="usu_imagen" name="imagen" class="ObjFocusBlur" /></td>
                </tr>
                <tr height="35">
                	<td><div class="template_contenedoresCeldas"><input type="checkbox" id="usu_notificaciones" name="notificaciones" style="margin-right:10px;" />Recibir notificaciones de contacto</div></td>
                </tr>
                <tr height="50">
                    <td>
                        <div id="btnGuardar6" class="btnOpciones" onClick="validarCampos6();">Guardar</div>
                        <span id="mensajeTemporal6" style="display:none;">Espere un momento...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<div id="inmueble_popupIframe" class="classPopUp inmueble_popupIframe">
	<div class='template_principal'>
    	<div class='template_contenedorCuerpo'>
            <div class='template_cuerpo'>
                <div class="formularioInmueble_cuerpo">
                    <p id="_leyendaTitulo" class="titulo"></p>
                    <form id="subirAnuncio" method="post" enctype="multipart/form-data" action="lib_php/updInmueble.php">
                        <input type="text" id="subirAnuncioNuevoModificar" value="1" style="display:none;" />
                        <input type="text" id="idInmueble2" name="id" value="" style="display:none;" />
                        <table class="conMargen">
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <p class="subtitulo">Características</p>
                                        <div class="contenedorCampos">
                                            <p>Título del Anuncio*</p>
                                            <input type="text" id="crearAnuncio_titulo" name="titulo" class="template_campos" placeholder="Título del Anuncio" maxlength="256" value="" />
                                            <p>Propiedad Destacada*</p>
                                            <input type="checkbox" name="propiedadDestacada"  id="crearAnuncio_propiedadesDestacadas" style="margin-right:10px;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="contenedorCampos">
                                        	<p>Categoría*</p>
                                            <select id="crearAnuncio_categoria" name="categoria" class="ObjFocusBlur">
												<option value="-1" class="off">Categoría</option><?php
                                            	for ($x = 0; $x < count($arrayCategoria); $x++) {
                                                	echo "<option value='".$arrayCategoria[$x]["id"]."'>".$arrayCategoria[$x]["nombre"]."</option>";
                                                }
                                            ?></select>
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Tipo de Transacción*</p>
                                        	<select id="crearAnuncio_transaccion" name="transaccion" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Tipo de Transacción</option><?php
												for ($x = 0; $x < count($arrayTransaccion); $x++) {
													echo "<option value='".$arrayTransaccion[$x]["id"]."'>".$arrayTransaccion[$x]["nombre"]."</option>";
												}
                                            ?></select>
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Calle y Número*</p>
                                            <input type="text" id="crearAnuncio_calleNumero" name="calleNumero" class="template_campos" placeholder="Calle y Número" maxlength="64" value="<?php echo $edit == 1 ? $inmueble["calleNumero"] : ""; ?>" />
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Ciudad / Municipio / Delegación*</p>
                                        	<select id="crearAnuncio_ciudad" name="ciudad" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Ciudad / Municipio / Delegación</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contenedorCampos">
                                        	<p>Tipo de Inmueble*</p>
                                        	<select id="crearAnuncio_tipo" name="tipo" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Tipo de Inmueble</option><?php
												for ($x = 0; $x < count($arrayTipoCategoria); $x++) {
													echo "<option value='".$arrayTipoCategoria[$x]["id"]."' data-categorias='".implode(",", $arrayTipoCategoria[$x]["categorias"])."'>".$arrayTipoCategoria[$x]["nombre"]."</option>";
												}
											?></select>
                                        </div>
                                        <div class="contenedorCampos">
                                            <p id="etiquetaPrecio">Precio*</p>
                                            <input type="text" id="crearAnuncio_precio" name="precio" class="template_campos" placeholder="Precio" maxlength="18" value="" />
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Estado*</p>
                                        	<select id="crearAnuncio_estado" name="estado" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Estado</option><?php
												for ($x = 0; $x < count($arrayEstado); $x++) {
													echo "<option value='".$arrayEstado[$x]["id"]."'>".$arrayEstado[$x]["nombre"]."</option>";
												}
											?></select>
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Colonia*</p>
                                        	<select id="crearAnuncio_colonia" name="colonia" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Colonia</option>
                                            </select>
                                            <input type="text" id="_crearAnuncioCP" name="cp" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="contenedorCampos">
                                            <p>Ubicación en el mapa*<a class="encontrarUbicacion" href="javascript:nuevoAnuncio_encontrarUbicacion();">Haz click aquí para encontrar tu ubicación en el mapa</a></p>
                                            <div id="contenedorMapa" class="contenedorMapa"></div>
                                            <input type="text" id="_crearAnuncioLatitud" name="latitud" value="" style="display:none;" />
                                            <input type="text" id="_crearAnuncioLongitud" name="longitud" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="contenedorCampos">
                                            <p>Descripción*</p>
                                            <textarea id="crearAnuncio_descripcion" name="descripcion" class="template_campos" placeholder="Descripción"></textarea>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="contenedorCampos">
                                        	<p>Baños<span class="obligatorio">*</span></p>
                                        	<select id="crearAnuncio_wcs" name="wcs" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Baños</option><?php
													for ($x = 1; $x <= 10; $x++) {
														echo "<option value='".$x."'>".$x."</option>";
													}
												?><option value="11">Más de 10</option>
                                            </select>
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Antig&uuml;edad</p>
                                        	<select id="crearAnuncio_antiguedad" name="antiguedad" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Antig&uuml;edad</option>
                                                <option value="1">0 Años</option>
                                                <option value="2">1 Año</option>
                                                <option value="3">2 Años</option>
                                                <option value="4">3 Años</option>
                                                <option value="5">4 Años</option>
                                                <option value="6">5 - 9 Años</option>
                                                <option value="7">10 - 19 Años</option>
                                                <option value="8">20 - 29 Años</option>
                                                <option value="9">30 - 39 Años</option>
                                                <option value="10">40 - 49 Años</option>
                                                <option value="11">50 Años ó mas</option>
                                            </select>
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Dimensión de Terreno (m<sup style="font-size:8px;">2</sup>)</p>
                                            <input type="text" id="crearAnuncio_dimesionTotal" name="dimensionTotal" class="template_campos" placeholder="Dimensión de Terreno" maxlength="12" value="" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Metros de Frente (mts.)</p>
                                            <input type="text" id="crearAnuncio_metrosFrente" name="metrosFrente" class="template_campos" placeholder="Metros de Frente" maxlength="11" value="" />
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Usuario*</p>
                                        	<select id="crearAnuncio_usuario" name="usuario" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Usuario</option><?php
												for ($x = 0; $x < count($arrayUsuario); $x++) {
													echo "<option value=".$arrayUsuario[$x]["id"]." data-inmobiliaria='".$arrayUsuario[$x]["inmobiliaria"]."'>".$arrayUsuario[$x]["nombre"]."</option>";
												}
                                            ?></select>
                                        </div>
                                        <div class="contenedorCampos" style="display:none;">
                                            <input type="text" id="_crearAnuncioDesarrollo" name="desarrollo" value="" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contenedorCampos">
                                        	<p>Recamaras<span class="obligatorio">*</span></p>
                                        	<select id="crearAnuncio_recamaras" name="recamaras" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Recamaras</option><?php
													for ($x = 1; $x <= 10; $x++) {
														echo "<option value='".$x."'>".$x."</option>";
													}
												?>
												<option value="11">Más de 10</option>
                                            </select>
                                        </div>
                                        <div class="contenedorCampos">
                                        	<p>Estado de Conservación</p>
                                        	<select id="crearAnuncio_estadoConservacion" name="estadoConservacion" class="ObjFocusBlur">
                                            	<option value="-1" class="off">Estado de Conservación</option>
                                                <option value="1">Excelente</option>
                                                <option value="2">Bueno</option>
                                                <option value="3">Regular</option>
                                                <option value="4">Malo</option>
                                                <option value="5">Muy Malo</option>
                                            </select>
                                        </div>
                                        <div class="contenedorCampos">
                                            <p>Dimensión de Construcción (m<sup style="font-size:8px;">2</sup>)</p>
                                            <input type="text" id="crearAnuncio_dimensionConstruida" name="dimensionConstruida" class="template_campos" placeholder="Dimensión de Construcción" maxlength="12" value="" />
                                        </div>
                                        <div class="contenedorCampos">
            
                                            <p>Metros de Fondo (mts.)</p>
                                            <input type="text" id="crearAnuncio_metrosFondo" name="metrosFondo" class="template_campos" placeholder="Metros de Fondo" maxlength="11" value="" />
                                        </div>
                                        <div class="contenedorCampos" id="celdaCodigo2" style="display:none;">
                                            <p>Código</p>
                                            <input type="text" id="crearAnuncio_codigo" name="codigo" data-inmueble="" class="template_campos" placeholder="Código" maxlength="64" value="" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2' id="celdaImagenesExistentes">
                                        <div class='contenedorCampos'>
                                            <p>Galeria de Imagenes</p>
                                            <div id='galeriaImagenes' class='imagenesTemporales'></div>
                                            <div id="contenedorGaleriasImagenesImagenPrincipal"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="contenedorCampos">
                                            <p>Imágen</p>
                                            <div id="imagenesTemporales" class="imagenesTemporales"></div>
                                            <p>Selecciona tu imagen principal</p>
                                            <div id="iframeSubirImagen">
                                                <iframe src="../lib_php/tempSubirImagen.php" frameborder="0" width="400" height="50"></iframe>
                                            </div>
                                            <input type="text" name="imagen" id="imagen2" value="" style="display:none;" />
                                            <input type="text" name="imagenPrincipal" id="imagenPrincipal2" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2' id="celdaVideosExistentes">
                                        <div class='contenedorCampos'>
                                            <p>Galeria de Videos</p>
                                            <div id='galeriaVideos' class='videosTemporales'></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="contenedorCampos">
                                            <p>Videos</p>
                                            <div id="videosTemporales" class="videosTemporales"></div>
                                            <p><input type="text" id="nuevoAnuncio_urlVideo" class="template_campos" placeholder="Ingresa una url de Youtube y enseguida presiona enter" /></p>
                                            <input type="text" name="videos" id="videos" value="" style="display:none;" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <p class="subtitulo">Ambientes</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cocinaEquipada" style="margin-right:10px;" />Cocina Equipada
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="estudio" style="margin-right:10px;" />Estudio
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="bodega" style="margin-right:10px;" />Bodega
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="terraza" style="margin-right:10px;" />Terraza
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="comedor" style="margin-right:10px;" />Comedor
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="sala" style="margin-right:10px;" />Sala
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="recibidor" style="margin-right:10px;" />Recibidor
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vestidor" style="margin-right:10px;" />Vestidor
                                        </div>     
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="patio" style="margin-right:10px;" />Patio
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="balcon" style="margin-right:10px;" />Balcón
                                        </div>                                                           
                                    </td>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cuartoServicio" style="margin-right:10px;" />Cuarto de Servicio
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cuartoTV" style="margin-right:10px;" />Cuarto de TV
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="jardin" style="margin-right:10px;" />Jardín
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="areaJuegosInfantiles" style="margin-right:10px;" />Área Infantil
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="biblioteca" style="margin-right:10px;" />Biblioteca
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="usosMultiples" style="margin-right:10px;" />Salón Usos Múlt.
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="oratorio" style="margin-right:10px;" />Oratorio
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cava" style="margin-right:10px;" />Cava
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="lobby" style="margin-right:10px;" />Lobby
                                        </div>                                
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="subtitulo">Servicios</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="serviciosBasicos" style="margin-right:10px;" />Servicios Básicos
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="gas" style="margin-right:10px;" />Gas
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="aireAcondicionado" style="margin-right:10px;" />Aire Acondicionado
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="calefaccion" style="margin-right:10px;" />Calefacción
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cisterna" style="margin-right:10px;" />Cisterna
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="calentador" style="margin-right:10px;" />Calentador
                                        </div>                                
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkCuotaMantenimiento2" style="margin-right:10px;" />Cuota Mantenimiento
                                            <input type="text" id="crearAnuncio_cuotaMantenimiento" name="cuotaMantenimiento" class="template_campos" placeholder="Cuota Mantenimiento" maxlength="11" value="" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="lineaTelefonica" style="margin-right:10px;" />Línea Telefónica
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="internetDisponible" style="margin-right:10px;" />Internet Disponible
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="casetaVigilancia" style="margin-right:10px;" />Caseta de Vigilancia
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="seguridad" style="margin-right:10px;" />Seguridad
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="camaras" style="margin-right:10px;" />Cámaras de Vigilancia
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="anden" style="margin-right:10px;" />Andén
                                        </div>                                
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkElevador2" style="margin-right:10px;" />Elevador
                                            <input type="text" id="crearAnuncio_elevador" name="elevador" class="template_campos" placeholder="Cantidad de Elevador" maxlength="11" value="" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="subtitulo">Amenidades</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="alberca" style="margin-right:10px;" />Alberca
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="casaClub" style="margin-right:10px;" />Casa Club
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="jacuzzi" style="margin-right:10px;" />Jacuzzi
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="permiteMascotas" style="margin-right:10px;" />Se Permite Mascotas
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="sauna" style="margin-right:10px;" />Sauna
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="asador" style="margin-right:10px;" />Asador
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="portonElectrico" style="margin-right:10px;" />Portón Eléctrico
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="chimenea" style="margin-right:10px;" />Chimenea
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaSquash" style="margin-right:10px;" />Cancha de Squash
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaBasket" style="margin-right:10px;" />Cancha de Basketball
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="familyRoom" style="margin-right:10px;" />Family Room
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="campoGolf" style="margin-right:10px;" />Campo de Golf
                                        </div>                                                                                                
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkEstacionamientoVisitas2" style="margin-right:10px;" />Estacionamiento para Visitas
                                            <input type="text" id="crearAnuncio_estacionamientoVisitas" name="estacionamientoVisitas" class="template_campos" placeholder="Cantidad de Estacionamiento para Visitas" maxlength="11" value="" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaTenis" style="margin-right:10px;" />Cancha de Tenis
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vistaMar" style="margin-right:10px;" />Vista al Mar
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="gimnasio" style="margin-right:10px;" />Gimnasio
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vapor" style="margin-right:10px;" />Vapor
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="playa" style="margin-right:10px;" />Playa
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="clubPlaya" style="margin-right:10px;" />Club de Playa
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="areasVerdes" style="margin-right:10px;" />Áreas Verdes
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="vistaPanoramica" style="margin-right:10px;" />Vista Panorámica
                                        </div>      
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="canchaFut" style="margin-right:10px;" />Cancha de Fútbol
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="salaCine" style="margin-right:10px;" />Sala de Cine
                                        </div>                                                                                          
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="cableTV" style="margin-right:10px;" />Televisión por Cable
                                        </div>
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkCajonesEstacionamiento2" style="margin-right:10px;" />Cajones de Estacionamiento
                                            <input type="text" id="crearAnuncio_cajonesEstacionamiento" name="cajonesEstacionamiento" class="template_campos" placeholder="Cantidad de Cajones de Estacionamiento" maxlength="11" value="" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p class="subtitulo">Otras Características</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="amueblado2" style="margin-right:10px;" />Amueblado 
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="semiAmueblado" style="margin-right:10px;" />Semi Amueblado
                                        </div>                            
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="centrosComercialesCercanos" style="margin-right:10px;" />Centros Comerciales 
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="escuelasCercanas" style="margin-right:10px;" />Escuelas Cercanas
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="iglesiasCercanas" style="margin-right:10px;" />Iglesias Cercanas
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="supermercadosCercanos" style="margin-right:10px;" />Supermercados Cerc.
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaComercial" style="margin-right:10px;" />Zona Comercial
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaResidencial" style="margin-right:10px;" />Zona Residencial
                                        </div>                                                                                                             
                                    </td>
                                    <td>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="fumadoresPermitidos" style="margin-right:10px;" />Fumadores
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="excelenteUbicacion" style="margin-right:10px;" />Excelente Ubicación
                                        </div>
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="hospitalesCercanos" style="margin-right:10px;" />Hospitales Cercanos
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="baresCercanos" style="margin-right:10px;" />Bares y Restaurantes
                                        </div>                                
                                        <div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaIndustrial" style="margin-right:10px;" />Zona Industrial
                                        </div><div class="contenedorCampos columnas">
                                            <input type="checkbox" name="zonaTuristica" style="margin-right:10px;" />Zona Turística
                                        </div>      
                                        <div class="contenedorCampos">
                                            <input type="checkbox" id="chkNumeroOficinas2" style="margin-right:10px;" />Oficinas
                                            <input type="text" id="crearAnuncio_numeroOficinas" name="numeroOficinas" class="template_campos" placeholder="Cantidad de Oficinas" maxlength="11" value="" />
                                        </div>
                                        <div class="contenedorCampos">
                                            <p id="btnGuardar7" class="subtitulo guardar" onClick="nuevoAnuncio_validarCampos_inmueble();" style="padding-top:40px;"></p>
                                            <p id="mensajeTemporal7" style="display:none;">Espere un momento...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="mascaraPrincipalNivel2" class="backInv mascaraPrincipalNivel2" onclick="inmueble_cerrarPopUp2();"></div>
<div id="inmueble_obtenerCoordenadas" class="classPopUp classPopUpNivel2 inmueble_obtenerCoordenadas">
    <table>
        <tbody>
            <tr height="50">
                <td style="font-size:18px; border-bottom:1px solid #aeadb3;">Coordenadas en el Mapa</td>
            </tr>
            <tr>
            	<td>
                	<p><a href="javascript:inmueble_encontrarUbicacion();">Haz click aquí para encontrar tu ubicación en el mapa</a></p><br />
                	<div id="contenedorInmuebleMapa" style="width:100%; height:500px; overflow:auto;"></div>
                </td>
            </tr>
            <tr height="50">
                <td>
                    <div id="btnGuardar2" class="btnOpciones" onClick="validarCampos2();">Guardar</div>
                    <span id="mensajeTemporal2" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="inmueble_subirImagen" class="classPopUp classPopUpNivel2 inmueble_subirImagen">
	<form id="subirImagen" method="post" enctype="multipart/form-data" action="lib_php/updInmuebleImagen.php">
        <table>
            <tbody>
                <tr height="50">
                    <td id="tituloEmergenteImagenes" style="font-size:18px; border-bottom:1px solid #aeadb3;"></td>
                </tr>
                <tr height="35">
                	<td>
                    	<input type="text" id="idInmueble" name="id" style="display:none;" />
                        <input type="text" id="nuevo" name="nuevo" style="display:none;" />
                        <input type="text" id="modificar" name="modificar" style="display:none;" />
                        <input type="text" id="idInmuebleInmueble" name="idImagen" style="display:none;" />
                    	<input type="file" id="imagen" name="imagen" class="ObjFocusBlur" />*
                    </td>
                </tr>
                <tr height="35">
                	<td>
                    	<div class="template_contenedorCeldas">
                        	<input type="checkbox" id="imagenPrincipal" name="imagenPrincipal" style="margin-right:10px;" />Imágen Principal
                        </div>
                    </td>
                </tr>
                <tr height="50">
                    <td>
                        <div id="btnGuardar3" class="btnOpciones" onClick="validarCampos3();">Guardar</div>
                        <span id="mensajeTemporal3" style="display:none;">Espere un momento...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
<div id="inmueble_subirVideo" class="classPopUp classPopUpNivel2">
    <table>
        <tbody>
            <tr height="50">
                <td id="tituloEmergenteVideos" style="font-size:18px; border-bottom:1px solid #aeadb3;"></td>
            </tr>
            <tr height="35">
                <td><input type="text" id="video" class="ObjFocusBlur" placeholder="Url de del video" maxlength="128" />*</td>
            </tr>
            <tr height="50">
                <td>
                    <div id="btnGuardar4" class="btnOpciones" onClick="validarCampos4();">Guardar</div>
                    <span id="mensajeTemporal4" style="display:none;">Espere un momento...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyBlYGs9bCTLNLYemvkULvJUaQR_vA7S9k4"></script>
<script type="text/javascript">
	<?php
		$tipoCategoriaCampos = array();
	
		$consulta = "SELECT TCA_TIPO_INMUEBLE, TCA_CATEGORIA_INMUEBLE, TCA_CAMPOS FROM TIPO_INMUEBLE_CATEGORIA_INMUEBLE ORDER BY TCA_ID;";
		foreach($conexion->query($consulta) as $row) {
			$tipoCategoriaCampos[$row["TCA_TIPO_INMUEBLE"]][$row["TCA_CATEGORIA_INMUEBLE"]] = $row["TCA_CAMPOS"];
		}
		
		$cadena = "var tipoCategoriaCampos = { ";
		
		$keys1 = array_keys($tipoCategoriaCampos);
		
		for ($x = 0; $x < count($keys1); $x++) {
			$cadena.= '"'.$keys1[$x].'": { ';
			
			$keys2 = array_keys($tipoCategoriaCampos[$keys1[$x]]);
			
			for ($y = 0; $y < count($keys2); $y++) {
				$valores = str_replace("elevador", "chkElevador", $tipoCategoriaCampos[$keys1[$x]][$keys2[$y]]);
				$valores = str_replace("estacionamientoVisitas", "chkEstacionamientoVisitas", $valores);
				$valores = str_replace("numeroOficinas", "chkNumeroOficinas", $valores);
				
				$cadena.= '"'.$keys2[$y].'": "'.$valores.'"'.($y < (count($keys2) - 1) ? "," : "");
			}
			
			$cadena.= " }".($x < (count($keys1) - 1) ? "," : "");
		}
		
		$cadena.= "};";
		
		echo $cadena;
	?>
</script>
<?php
	adminFinHTML();
?>