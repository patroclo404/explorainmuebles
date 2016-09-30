// JavaScript Document


/*
	Valida campos para crear una nueva cuenta
*/
function registro_validaCampos_count() {
	if (!isVacio($("#reg_nombre").val())) {
		if (!isVacio($("#reg_email").val())) {
			if (correo($("#reg_email").val())) {
				if (!isVacio($("#reg_password").val())) {
					if (!isVacio($("#reg_confPassword").val())) {
						if ($("#reg_password").val() == $("#reg_confPassword").val()) {
							if ($("#reg_check").prop("checked")) {
								$.ajax({
									url: "lib_php/updUsuario.php",
									type: "POST",
									dataType: "json",
									data: {
										id: -1,
										validarEmail: 1,
										email: $("#reg_email").val()
									}
								}).always(function(respuesta_json){
									if (respuesta_json.isExito == 1) {
										$.ajax({
											url: "lib_php/updUsuario.php",
											type: "POST",
											dataType: "json",
											data: {
												id: -1,
												nuevo: 1,
												nombre: $("#reg_nombre").val(),
												email: $("#reg_email").val(),
												password: md5Script($("#reg_password").val()),
												FBId: $("#reg_FBId").val()
											}
										}).always(function(respuesta_json2){
											if (respuesta_json2.isExito == 1) {
												if ($("#template_count_FBId").val() != "") {
													$("#template_login_email").val($("#reg_email").val());
													$("#template_login_pass").val($("#reg_password").val());
													
													template_validaCampos_login();
												}
												else {
													$("#reg_nombre").val("");
													$("#reg_email").val("");
													$("#reg_password").val("");
													$("#reg_FBId").val("");
													$("#reg_confPassword").val("");
													$("#reg_check").prop("checked", false);
													$("#template_alertPersonalizado td").html("Gracias por registrarte!.<br /><br />Te enviamos un email para que valides tu cuenta y puedas comenzar. Recuerda revisar tu correo no deseado.");
													template_alertPersonalizado();
												}
											}
										});
									}
									else {
										$("#template_alertPersonalizado td").text(respuesta_json.mensaje);
										template_alertPersonalizado();
									}
								});
							}
							else {
								$("#template_alertPersonalizado td").text("Acepte los términos y condiciones.");
								template_alertPersonalizado();
							}
						}
						else {
							$("#template_alertPersonalizado td").text("Las contraseñas son diferentes, vuelva a intentarlo.");
							template_alertPersonalizado();
						}
					}
					else {
						$("#template_alertPersonalizado td").text("El campo "+$("#reg_confPassword").attr("placeholder")+" es obligatorio");
						template_alertPersonalizado();
					}
				}
				else {
					$("#template_alertPersonalizado td").text("El campo "+$("#reg_password").attr("placeholder")+" es obligatorio");
					template_alertPersonalizado();
				}
			}
			else {
				$("#template_alertPersonalizado td").text("El correo "+$("#reg_email").val()+" no es un correo valido");
				template_alertPersonalizado();
			}
		}
		else {
			$("#template_alertPersonalizado td").text("El campo "+$("#reg_email").attr("placeholder")+" es obligatorio");
			template_alertPersonalizado();
		}
	}
	else {
		$("#template_alertPersonalizado td").text("El campo "+$("#reg_nombre").attr("placeholder")+" es obligatorio");
		template_alertPersonalizado();
	}
}


/*
	Inicia session con FB y completa los datos para registrarse con FB
*/
function registro_validaCampos_countFB() {
	FB.login(function(respuesta_json){
		if (respuesta_json.authResponse) {
			FB.api('/me', function(respuesta2_json) {
				$("#reg_FBId").val(respuesta2_json.id);
				$("#reg_nombre").val(respuesta2_json.name);
				$("#reg_email").val(respuesta2_json.email);
				$("#reg_password").val(respuesta2_json.email);
				$("#reg_password").val(respuesta2_json.email);
				
				registro_validaCampos_count();
			});
		}
	},{
		scope:'email,public_profile,user_birthday'
	});
}


/*
	Valida los campos para hacer login
*/
function registro_validaCampos_login() {
	if (!isVacio($("#reg_email2").val())) {
		if (correo($("#reg_email2").val())) {
			if (!isVacio($("#reg_password2").val())) {
				$.ajax({
					url: "lib_php/login.php",
					type: "POST",
					dataType: "json",
					data: {
						FBId: $("#reg_FBId").val(),
						nombre: $("#reg_nombre").val(),
						email: $("#reg_email2").val(),
						password: md5Script($("#reg_password2").val())
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						urlActual = basename(String(window.location));
						gotoURL(urlActual);
					}
					else {
						$("#template_alertPersonalizado td").text(respuesta_json.mensaje);
						template_alertPersonalizado();
					}
				});
			}
			else {
				$("#template_alertPersonalizado td").text("El campo "+$("#reg_password2").attr("placeholder")+" es obligatorio");
				template_alertPersonalizado();
			}
		}
		else {
			$("#template_alertPersonalizado td").text("El correo "+$("#reg_password2").val()+" no es un correo valido");
			template_alertPersonalizado();
		}
	}
	else {
		$("#template_alertPersonalizado td").text("El campo "+$("#reg_email2").attr("placeholder")+" es obligatorio");
		template_alertPersonalizado();
	}
}


/*
	Inicia session con FB y completa los datos para iniciar session en explora inmuebles
*/
function registro_validaCampos_loginFB() {
	FB.login(function(respuesta_json){
		if (respuesta_json.authResponse) {
			FB.api('/me', function(respuesta2_json) {
				$("#reg_FBId").val(respuesta2_json.id);
				$("#reg_nombre").val(respuesta2_json.name);
				$("#reg_email2").val(respuesta2_json.email);
				$("#reg_password2").val(respuesta2_json.email);
				
				registro_validaCampos_login();
			});
		}
	},{
		scope:'email,public_profile,user_birthday'
	});
}
/*
*/