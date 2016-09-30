// JavaScript Document
/*
	Los mensajes es posibles mostrarlos con multidioma; si no se envia el parametro "lang" entonces
	se tomara el idioma por default: "esp"
*/
languajeDefault = "esp";

/*********************************************************************************************************
	funciones de validaciones
*********************************************************************************************************/
/*
	Evalua que la cadena "valor" sea una cadena vacia; en caso de ser asi devuelve true; en caso contrario
	muestra un mensaje con el texto "mensaje". Multidioma
	
		* valor:	String, es la cadena a evaluar
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "valor" no esta vacio
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function vacio(valor, mensaje, lang) {
	if (lang == null)
		lang = languajeDefault;

	if(valor.length == 0){
		switch(lang) {
			case "esp":
				window.alert("El campo " + mensaje + " es obligatorio");
				break;
			case "eng":
				window.alert("The field of " + mensaje + " is obligatory");
				break;
		}
		
		return true;
	}
	else
		return false;
}


/*
	Evalua que la cadena "valor" sea una cadena vacia; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* valor:	String, es la cadena a evaluar
*/
function isVacio(valor) {
	if(valor.length == 0)
		return true;
	else
		return false;
}


/*
	Evalua que la cadena "valor" sea una cadena de longitud menor a "maximo"; en caso de ser menor a "maximo" devuelve false;
	en caso contrario muestra un mensaje con el texto "mensaje"
	
		* valor:	String, es la cadena a evaluar
		* maximo:	Integer, es la cantidad de caracteres como maximo permitido
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "valor" no es muy largo
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function muyLargo(valor, maximo, mensaje, lang) {
	if (lang == null)
		lang = languajeDefault;
		
	var lon = valor.length;

	if(lon > maximo){
		switch(lang) {
			case "esp":
				window.alert("El campo "+ mensaje +" es muy largo. Maximo "+ maximo +" caracteres");
				break;
			case "eng":
				window.alert("Campo "+ mensaje +" muy largo. Maximo "+ maximo +" caractereseng");
				break;
		}
		
		return true;
	}
	else
		return false;
}


/*
	Evalua que la cadena "valor" sea una cadena de longitud menor a "minimo"; en caso de ser mayor a "minimo" devuelve false;
	en caso contrario muestra un mensaje con el texto "mensaje"
	
		* valor:	String, es la cadena a evaluar
		* minimo:	Integer, es la cantidad de caracteres como minimo permitido
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "valor" si no es muy corto
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function muyCorto(valor, minimo, mensaje, lang){
	if (lang == null)
		lang = languajeDefault;
		
	var lon = valor.length;

	if(lon < minimo){
		switch(lang) {
			case "esp":
				window.alert("El campo "+ mensaje +" es muy corto. Minimo "+ minimo +" caracteres");
				break;
			case "eng":
				window.alert("Campo "+ mensaje +" muy corto. Minimo "+ minimo +" caractereseng");
				break;
		}
		
		return true;
	}
	else
		return false;
}


/*
	Evalua que "valor" sea un valor flotante; en caso de ser asi devuelve true; en caso contrario
	devuelve un mensaje junto con la cadena "mensaje"
	
		* valor:	String, es la cadena a evaluar
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "valor" si no es flotante
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function flotante(valor, mensaje, lang) {
	if (lang == null)
		lang = languajeDefault;
	
	if (valor.length== 0) {
		switch(lang) {
			case "esp":
				window.alert("El campo "+ mensaje +" no puede estar vacio.\nDebe ser n\u00famerico");
				break;
			case "eng":
				window.alert("El campo "+ mensaje +" no puede estar vacio.\nDebe ser numericoeng");
				break;
		}
		return false;
	}

	var i;
	var flag = true;//revisa la existencia de un punto decimal

	for (i=0;i<valor.length;i++){
		if (!isDigito(valor.charAt(i))) {
			if (valor.charAt(i) == '.' && flag)
				flag = false;
			else {
				switch(lang) {
					case "esp":
						window.alert("El campo "+ mensaje +" debe ser n\u00famerico");
						break;
					case "eng":
						window.alert("The field of "+ mensaje +" must be numeric");
						break;
				}
				return false;
			}
		}
	}

	return true;
}


/*
	Evalua que "valor" sea un valor flotante; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* valor:	String, es la cadena a evaluar
*/
function isFlotante(valor) {
	if (valor.length== 0)
		return false;

	var i;
	var flag = true;//revisa la existencia de un punto decimal

	for (i=0;i<valor.length;i++){
		if (!isDigito(valor.charAt(i))) {
			if (valor.charAt(i) == '.' && flag)
				flag = false;
			else
				return false;
		}
	}

	return true;
}


/*
	Evalua que "valor" sea un valor entero; en caso de ser asi devuelve true; en caso contrario
	devuelve un mensaje junto con la cadena "mensaje"
	
		* valor:	String, es la cadena a evaluar
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "valor" no es entero
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function entero(valor, mensaje, lang){
	if (lang == null)
		lang = languajeDefault;
		
	if(valor.length==0){
		switch(lang) {
			case "esp":
				window.alert("El campo "+ mensaje +" no puede estar vacio.\nDebe ser n\u00famerico");
				break;
			case "eng":
				window.alert("El campo "+ mensaje +" no puede estar vacio.\nDebe ser numericoeng");
				break;
		}
		return false;
	}

	var i;
	for(i=0;i<valor.length;i++){
		if(!isDigito(valor.charAt(i))){
			switch(lang) {
				case "esp":
					window.alert("El campo "+ mensaje +" debe ser n\u00famerico");
					break;
				case "eng":
					window.alert("The field of "+ mensaje +" must be numeric");
					break;
			}
			return false;
		 }
	}

	return true;
}


/*
	Evalua que "valor" sea un valor entero; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* valor:	String, es la cadena a evaluar
*/
function isEntero(valor){
	if(valor.length==0)
		return false;

	var i;
	for(i=0;i<valor.length;i++){
		if(!isDigito(valor.charAt(i)))
			return false;
	}

	return true;
}


/*
	Evalua que "valor" sea un digito; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* valor:	String, es la cadena a evaluar
*/
function isDigito(valor){
	var digito="0123456789"
	var i;

	for(i=0;i<digito.length;i++)
		if(digito.charAt(i)==valor)
			return true;

	return false;
}


/*
	Evalua que "valor" sea un caracter; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* valor:	String, es el caracter a evaluar
*/
function isCaracter(valor){
	var caracter="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var i;

	for(i=0;i<caracter.length;i++)
		if(caracter.charAt(i)==valor)
			return true;

	return false;
}


/*
	Evalua que "email" sea un correo valido; en caso de ser asi devuelve true; en caso contrario
	devuelve un mensaje junto con la cadena "email"
	
		* email:	String, es la cadena a evaluar
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function correoValido(email, lang){
	if (lang == null)
		lang = languajeDefault;
	
	if(correo(email))
		return true
	else {
		switch(lang) {
			case "esp":
				window.alert("El correo "+ email + " no es un correo valido");
				break;
			case "eng":
				window.alert("E-mail "+ email + " is invalid");
				break;
		}
		return false;
	}
}


/*
	Evalua que "email" sea un correo valido; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* email:	String, es la cadena a evaluar
*/
function correo(email){
	var emailReg = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
	if(!emailReg.test(email) ) {
		return false;
	} else {
		return true;
	}
}

  
/*
	Evalua que "valor" sea una fecha valida; en caso de ser asi devuelve true; en caso contrario
	devuelve un mensaje junto con la cadena "mensaje"
	
		* valor:	String, es la cadena a evaluar
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "valor" si es una fecha invalida
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function fechaValida(valor, mensaje, lang){
	if (lang == null)
		lang = languajeDefault;
	
    fecha=formateaFecha(valor);
    if(fecha.length == 10)
        return true;
    else {
		switch(lang) {
			case "esp":
		        window.alert("Fecha "+mensaje+ " incorrecta");
				break;
			case "eng":
				window.alert("Date "+mensaje+ " invalid");
				break;
		}
		
		return false;
    }
}


/*
	Evalua que "fecha" sea una fecha valida; devuelve la fecha formateada
	
		* fecha:	String, es la cadena a evaluar
*/
var primerslap=false;
var segundoslap=false;

function formateaFecha(fecha) {
    var long = fecha.length;
    var dia;
    var mes;
    var ano;

    if ((long>=2) && (primerslap==false)){
        dia=fecha.substr(0,2);
        if ((isEntero(dia)==true) && (dia<=31) && (dia!="00")) {
            fecha=fecha.substr(0,2)+"/"+fecha.substr(3,7);
            primerslap=true;
        }
        else { 
            fecha=""; 
            primerslap=false;
        }
    }
    else {
        dia=fecha.substr(0,1);
        if (isEntero(dia)==false)
            fecha="";

        if ((long<=2) && (primerslap=true)){
            fecha=fecha.substr(0,1); 
            primerslap=false; 
        }
    }

    if ((long>=5) && (segundoslap==false)){
        mes=fecha.substr(3,2);
        dia=fecha.substr(0,2);
		
        if ((isEntero(mes)==true) &&(mes<=12) && (mes!="00")) { 
            if(((dia<='31')&&(mes=='01' || mes=='03' || mes=='05' || mes=='07' || mes=='08' || mes=='10' || mes=='12')) || ((dia<='30')&&((mes=='04') || (mes=='06') || (mes=='09') || (mes=='11')))||((dia<='29')&&(mes='02'))){
                fecha=fecha.substr(0,5)+"/"+fecha.substr(6,4); 
                segundoslap=true;  
            }
            else{
                fecha=fecha.substr(0,3); 
                segundoslap=false;
            }
        }
        else { 
            fecha=fecha.substr(0,3); 
            segundoslap=false;
        }
    }
    else { 
        if ((long<=5) && (segundoslap=true)) { 
           fecha=fecha.substr(0,4); 
           segundoslap=false;            
        } 
    }

	if (long>=7){ 
        ano=fecha.substr(6,4);
        if (isEntero(ano)==false)
            fecha=fecha.substr(0,6); 
        else { 
            if (long==10){ 
                if ((ano==0) || (ano<1900) || (ano>2100)) 
                    fecha=fecha.substr(0,6); 
            } 
        }
    }

	if (long>=10){
        fecha=fecha.substr(0,10);
        dia=fecha.substr(0,2);
        mes=fecha.substr(3,2);
        ano=fecha.substr(6,4);
        diag1=fecha.substr(2,1);
        diag2=fecha.substr(5,1);

        // Año no viciesto y es febrero y el dia es mayor a 28
        if ( (ano%4 != 0) && (mes ==02) && (dia > 28) )
            fecha=fecha.substr(0,2)+"/"; 

        else 
			if(( ano%4 == 0) && (mes == 02) && (dia > 29))
	            fecha=fecha.substr(0,2)+"/"; 

        if(((dia<='31')&&(mes=='01' || mes=='03' || mes=='05' || mes=='07' || mes=='08' || mes=='10' || mes=='12')) || ((dia<='30')&&((mes=='04') || (mes=='06') || (mes=='09') || (mes=='11')))||((dia<='29')&&(mes='02'))){
            fecha=fecha.substr(0,5)+"/"+fecha.substr(6,4); 
            segundoslap=true;  
        }
        else {
            fecha=fecha.substr(0,2)+"/"; 
            primerslap=false; 
            segundoslap=false;
        }

        if(diag2!="/"){
            fecha=fecha.substr(0,5)+"/"; 
            primerslap=false; 
            segundoslap=false;
        }

        if(diag1!="/"){
            fecha=fecha.substr(0,2)+"/"; 
            primerslap=false; 
            segundoslap=false;
        }

        if(!isEntero(mes) || mes>12 || mes<01 )
            fecha=fecha.substr(0,2)+"/"; 
    }

    return (fecha);
}


/*
	Campara 2 fechas; la primera fecha debe ser mayor o igual a la segunda fecha. En caso de ser asi devuelve true
	en caso contrario devuelve un mensaje de fecha invalida
	
		* fecha:	String, es la primera fecha a evaluar; esta debe ser mayor o igual a la segunda
		* fecha2:	String, es la segunda fecha a evaluar y debe ser menor o igual a la primera
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function compareDates(fecha, fecha2, lang)  {
	if (lang == null)
		lang = languajeDefault;
	
	if (isCompareDates(fecha, fecha2))
		return true;
	else {
		switch(lang) {
			case "esp":
				window.alert("La primera fecha debe ser mayor a la segunda.");
				break;
			case "eng":
				window.alert("La primera fecha debe ser mayor a la segunda.eng");
				break;
		}
		
		return false;
	}
}


/*
	Campara 2 fechas; la primera fecha debe ser mayor o igual a la segunda fecha. En caso de ser asi devuelve true
	en caso contrario devuelve false
	
		* fecha:	String, es la primera fecha a evaluar; esta debe ser mayor o igual a la segunda
		* fecha2:	String, es la segunda fecha a evaluar y debe ser menor o igual a la primera
*/
function isCompareDates(fecha, fecha2)  {
    var xMonth=fecha.substring(3, 5);  
    var xDay=fecha.substring(0, 2);  
    var xYear=fecha.substring(6,10);  
    var yMonth=fecha2.substring(3, 5);  
    var yDay=fecha2.substring(0, 2);  
    var yYear=fecha2.substring(6,10);  
    if (xYear> yYear)
        return(true);
    else {  
      if (xYear == yYear){   
        if (xMonth> yMonth)
            return(true);
        else{   
          if (xMonth == yMonth){  
            if (xDay >= yDay)
              return(true);  
            else  
              return(false);  
          }
          else  
            return(false);  
        }  
      }  
      else  
        return(false);  
    }
}


/*
	Evalua que "url" sea una url valida; en caso de ser asi devuelve true; en caso contrario
	devuelve un mensaje junto con la cadena "mensaje"
	
		* url:	String, es la url a evaluar
		* mensaje:	String, se mostrara una mensaje junto a esta cadena si "url" si es una url invalida
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function validaURL(url, mensaje, lang) {
	if (lang == null)
		lang = languajeDefault;
		
	if (isValidaURL(url))
		return true;
	else {
		switch(lang) {
			case "esp":
				window.alert("EL campo "+mensaje+" con la url "+url+" es invalido.");
				break;
			case "eng":
				window.alert("EL campo "+mensaje+" con la url "+url+" es invalido.eng");
				break;
		}
		
		return false;
	}
}


/*
	Evalua que "url" sea una url valida; en caso de ser asi devuelve true; en caso contrario
	devuelve false
	
		* url:	String, es la url a evaluar
*/
function isValidaURL(url) {
	var regex = /^(ht|f)tps?:\/\/\w+([\.\-\w]+)?\.([a-z]{2,4}|travel)(:\d{2,5})?(\/.*)?$/i;
	return regex.test(url);
}


/*************************************************************
	objeto: NumeroFormato
	
	permite formatear al numero a un numero de decimales,
	ademas de que muestra o no el formato de miles con coma
*************************************************************/
function NumeroFormato(numero) {
	//propiedades
	this.valor = numero || 0;
	this.dec = -1;
	
	//metodos
	this.formato = numFormat;
	this.ponValor = ponValor;
	
	function ponValor(cad) {
		if (cad == "-" || cad == "+")
			return
		if (cad.length == 0)
			return
		if (cad.indexOf('.') >= 0)
			this.valor = parseFloat(cad);
		else
			this.valor = parseInt(cad);
	}
	
	function numFormat(dec, miles) {
		var num = this.valor, signo = 3, expr;
		var cad = ""+this.valor;
		var ceros = "", pos, pdec, i;
		
		var bandDecTemp = false;
		if (dec == 0 && miles) {
			dec = 1;
			bandDecTemp = true;
		}
		
		for (i = 0; i < dec; i++)
			ceros += "0";
		pos = cad.indexOf(".");
		if (pos < 0) {
			if (dec > 0)
				cad = cad+"."+ceros;
		}
		else {
			pdec = cad.length - pos - 1;
			if (pdec <= dec) {
				for (i = 0; i < (dec-pdec); i++)
					cad += "0";
			}
			else {
				num = num * Math.pow(10, dec);
				num = Math.round(num);
				num = num/Math.pow(10, dec);
				cad = new String(num);
			}
		}
		pos = cad.indexOf(".");	
		if (pos < 0)
			pos = cad.length;
		if (cad.substr(0, 1) == "-" || cad.substr(0, 1) == "+")
			signo = 4;
		if (miles && pos > signo) {
			do {
				expr = /([+-]?\d)(\d{3}[\.\,]\d*)/;
				cad.match(expr);
				cad = cad.replace(expr, RegExp.$1+","+RegExp.$2);
			}
			while (cad.indexOf(",")>signo);
		}
		if (dec<0)
			cad = cad.replace(/\-/,'');
			
		if (dec == 1 && bandDecTemp) {
			temp = cad.split(".");
			cad = temp[0];
			dec = 0;
			bandDecTemp = false;
		}
		
		return cad;
	}
}


/*
	Evalua que "título" sea un titulo valido; en caso de ser asi devuelve true; en caso contrario
	devuelve un mensaje junto con la cadena "titulo". El titulo tiene excepciones que se omiten en
	la validacion: !, ¡, ¿, ?, ","
	
		* titulo:	String, es la cadena a evaluar
		* lang:		String, es el idioma en el que se mostrara el alert
*/
function tituloValido(titulo, lang){
	if (lang == null)
		lang = languajeDefault;
	
	var excepciones = titulo.replace(new RegExp("[!?&\,\u00bf\u00a1]", "g"), "");;
	var regular = /^[A-Za-z0-9\u00e1\u00e9\u00ed\u00f3\u00fa\u00f1\u00c1\u00c9\u00cd\u00d3\u00da\u00d1]{1,}([\s][A-Za-z0-9\u00e1\u00e9\u00ed\u00f3\u00fa\u00f1\u00c1\u00c9\u00cd\u00d3\u00da\u00d1]{1,})*$/;
	
	if(regular.test(excepciones.trim()))
		return true;
	else {
		switch(lang) {
			case "esp":
				window.alert("El t\u00edtulo "+ titulo + " no es un t\u00edtulo valido");
				break;
			case "eng":
				window.alert("Title "+ titulo + " is invalid");
				break;
		}
		return false;
	}
}