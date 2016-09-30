<?php

	require_once("lib_php/configFB.php");
	require_once("lib_php/template.php");
	
	
	if (!isset($_SESSION[userId]))
		header("location: index.php");
		
	if ($_SESSION[userAdminInmobiliaria] == 0)
		header("location: index.php");
	
	
	$urlArchivos = "images/images/";
	
	$arrayUsuarios = array();
	
	$conexion = crearConexionPDO();
	$consulta = "SELECT USU_ID, USU_NOMBRE, USU_EMAIL FROM USUARIO WHERE USU_INMOBILIARIA = :userInmobiliaria AND USU_ID <> :userId ORDER BY USU_NOMBRE;";
	$pdo = $conexion->prepare($consulta);
	$pdo->execute(array(":userInmobiliaria" => $_SESSION[userInmobiliaria], ":userId" => $_SESSION[userId]));
	foreach($pdo->fetchAll(PDO::FETCH_ASSOC) as $row) {
		$arrayUsuarios[] = array(
			"id"		=>	$row["USU_ID"],
			"nombre"	=>	$row["USU_NOMBRE"],
			"email"		=>	$row["USU_EMAIL"]
		);
	}
	
	
	CabeceraHTML("usuarios_ver3.css,usuarios.js");
	CuerpoHTML();
?>
<div class="usuarios_cuerpo">
	<div class="columna1">
    	<?php template_opciones_miPerfil(); ?>
    </div><div class="columna2">
    	<div id="lk_miPerfil">
        	<p class="titulo">Usuarios<a href="editUsuario.php" class="agregar">+</a></p>
            <?php
				for ($x = 0; $x < count($arrayUsuarios); $x++) {
					echo "<p class='modificarUsuario'>".$arrayUsuarios[$x]["nombre"]."<span class='borrar' onclick='usuario_deleteUsuario(".$arrayUsuarios[$x]["id"].");'>X</span><span class='editar' onclick='gotoURLPOST(\"editUsuario.php\", {id: ".$arrayUsuarios[$x]["id"]."});'>Editar</span></p>";
				}
			?>
    </div>
</div>
<?php
	FinCuerpo();
	PopUpGenerales();
	FinHTML();
?>