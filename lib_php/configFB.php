<?php

	require 'facebook-php-sdk/facebook.php';
	
	//cache para lo del facebook
	$cache_expire = 0;
	header("Pragma: public");
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
	//cache para lo del facebook
	
	$facebook = new Facebook(array(
		'appId' => '868276969900595',
		'secret' => '01a9753288a7fb037fafbaf6cc512ada',
		'req_perms'	=>	'email,read_stream'
	));
	

	$userFacebook = $facebook->getUser();//obtengo el usuario del facebook en caso de estar logueado
	
	if ($userFacebook) {//si el usuario esta logueado
		try {
			$userFacebookProfile = $facebook->api("/me");//obtenog el perfil del mismo
		}
		catch (FacebookApiException $e) {//si no
			$userFacebook = NULL;//hago nulo la variable de usuario de facebook
		}
	}

?>