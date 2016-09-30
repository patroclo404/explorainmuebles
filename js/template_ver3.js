// JavaScript Document


/*
	Funciones que se inicializan al cargar el html en todas las interfaces
*/
$(document).ready(function(){
	template_inicializarBotones();
});


/*
	Inicializa los botones comunes en todas las interfaces
*/
function template_inicializarBotones() {
	$("ul.template_campos").each(function(){
		var elemento = $(this);
		
		$(this).on({
			click: function() {
				var isOpen = false;
				
				if (elemento.find("li.lista").css("display") != "none")
					isOpen = true;
					
				if (isOpen)
					elemento.find("li.lista").hide();
				else
					elemento.find("li.lista").show();
			}
		});
		
		
		$(this).find("li.lista li").on({
			click: function() {
				elemento.find("p").attr("data-value", $(this).attr("data-value"));
				elemento.find("p").text($(this).text());
			}
		});
	});
	
	
	$("#template_venta_estado").on({
		change: function() {
			if ($(this).val() != -1) {
				$.ajax({
					url: "admin/lib_php/consDireccion.php",
					type: "POST",
					dataType: "json",
					data: {
						consCiudad: 1,
						estado: $(this).val()
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						$("#template_venta_municipio").prop("disabled", false);
						$("#template_venta_municipio option[value!='-1']").remove();
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#template_venta_municipio").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#template_venta_municipio").val("-1");
				//$("#colonia").val("-1");
				
				$("#template_venta_municipio").prop("disabled", true);
				//$("#colonia").prop("disabled", true);
			}
		}
	});
	
	
	$("#template_renta_estado").on({
		change: function() {
			if ($(this).val() != -1) {
				$.ajax({
					url: "admin/lib_php/consDireccion.php",
					type: "POST",
					dataType: "json",
					data: {
						consCiudad: 1,
						estado: $(this).val()
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						$("#template_renta_municipio").prop("disabled", false);
						$("#template_renta_municipio option[value!='-1']").remove();
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#template_renta_municipio").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#template_renta_municipio").val("-1");
				//$("#colonia").val("-1");
				
				$("#template_renta_municipio").prop("disabled", true);
				//$("#colonia").prop("disabled", true);
			}
		}
	});
	
	
	$("#template_rentaVac_estado").on({
		change: function() {
			if ($(this).val() != -1) {
				$.ajax({
					url: "admin/lib_php/consDireccion.php",
					type: "POST",
					dataType: "json",
					data: {
						consCiudad: 1,
						estado: $(this).val()
					}
				}).always(function(respuesta_json){
					if (respuesta_json.isExito == 1) {
						$("#template_rentaVac_municipio").prop("disabled", false);
						$("#template_rentaVac_municipio option[value!='-1']").remove();
						
						for (var x = 0; x < respuesta_json.datos.length; x++) {
							$("#template_rentaVac_municipio").append("<option value='"+respuesta_json.datos[x].id+"'>"+respuesta_json.datos[x].nombre+"</option>");
						}
					}
				});
			}
			else {
				$("#template_rentaVac_municipio").val("-1");
				//$("#colonia").val("-1");
				
				$("#template_rentaVac_municipio").prop("disabled", true);
				//$("#colonia").prop("disabled", true);
			}
		}
	});
	
	
	$(".template_campos_select").each(function(){
		var elementoSelect = $(this).find("select");
		
		$(this).find("span").on({
			click: function() {
				//elementoSelect.trigger('mousedown');
				//elementoSelect.focus();
			}
		});
	});
}


/*
	redirecciona a otra interfaz
	
		* url:	String, es la url a la que se redirecciona. Es posible pasar parametros GET
*/
function gotoURL(url){
	window.location=url;
}


/*
	Funcion de redireccionar pero con envio de parametros con POST
	Parametros:
	
		* page:		String, la pagina a la que se redireccionara
		* params:	String,	es el envio de los nombres de los parametros, seguido del valor. Primero empieza con apertura de
					llaves, seguido por grupo de parametros con valores separados por comas, en cada grupo comienza con el
					nombre del parametro encerrado en apostrofes, seguido de ":" y el valor de la variable encerrado en
					apostrofes. 
					Example: 	gotoURLPOST('file.php',{'var1':'hola','var2':'mundo'});
*/
function gotoURLPOST(page,params) {
	var body = document.body;
	form=document.createElement('form'); 
	form.method = 'POST'; 
	form.action = page;
	form.name = 'jsform';
	for (index in params) {
		var input = document.createElement('input');
		input.type='hidden';
		input.name=index;
		input.id=index;
		input.value=params[index];
		form.appendChild(input);
	}	  		  			  
	body.appendChild(form);
	form.submit();
}


/*
	Al capturar el evento de enter, entonces ejecuta la funcion recibida por parametro
	
		* evento:	Event, es el evento a capturar
		* fcn:		String, es el nombre de la funcion (sin parentesis y sin parametros)
*/
function template_displayUnicode(evento, fcn) {
	var unicode = evento.keyCode;
	
	if (unicode == 13)
		fcn();
}