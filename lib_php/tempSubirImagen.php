<?php

	require_once("template.php");

	$variables = "upload_max_filesize='".ini_get('upload_max_filesize')."',upload_max_filesize_bytes=".template_return_bytes(ini_get('upload_max_filesize'));
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
	* {
		margin:0px;
		padding:0px;
		font-size:14px;
		color:#575756;
	}

	.template_campos {
		width:300px;
		height:25px;
		border:1px solid #dadad9;
		border-radius:15px;
		padding:0px 5px;
		color:#575756;
		background-color:#dadad9;
	}
	
	p {
		padding-top:10px;
		display:none;
	}
</style>
<script type="text/javascript" src="../js/jQuery.js"></script>
<script type="text/javascript" src="../js/jQueryForm.js"></script>
<script>
	var <?php echo $variables; ?>;

	$(document).ready(function() {
		$("#imagen").on({
			change: function() {
				//entra para validar el tamaño del archivo solo si el navegador tienen la api de File
				if (window.File && window.FileReader && window.FileList && window.Blob) {
					var fSize = 0;
					
					for (x = 0; x < $("#imagen")[0].files.length; x++) {
						fSize += $("#imagen")[0].files[x].size;
					}
					
					if (fSize >= parseInt(upload_max_filesize_bytes)) {
						alert("El archivo excede el tamaño permitido: "+upload_max_filesize);
						$("#imagen").val("");
						return false;
					}
				}
				
				saveImagen();
			}
		});
	});
	
	
	function saveImagen() {
		$("body").css("cursor", "wait");
		$("#imagen").hide();
		$("p").show();
		
		$("#subirTempImagen").ajaxSubmit({
			dataType: "json",
			success: function(respuesta_json){
				window.parent.nuevoAnuncio_tempImagenCargada(respuesta_json.imagen);
			}
		});
	}
</script>
</head>

<body>
	<form id="subirTempImagen" method="post" enctype="multipart/form-data" action="tempSubirImagen2.php">
    	<input type="text" name="nuevo" value="1" style="display:none;" />
		<input type="file" id="imagen" name="imagen[]" class="template_campos" multiple="" />
        <p>Cargando...</p>
	</form>
</body>
</html>