// JavaScript Document

$(document).ready(function(){
	$(".reglasPublicacion_cuerpo font[face]").each(function(){
		$(this).css("font-family", $(this).attr("face"));
	});
	$(".reglasPublicacion_cuerpo font[size]").each(function(){
		toEm = 0.63;
		switch(parseInt($(this).attr("size"))) {
			case 1:
				toEm = 0.63;
				break;
			case 2:
				toEm = 0.82;
				break;
			case 3:
				toEm = 1.0;
				break;
			case 4:
				toEm = 1.13;
				break;
			case 5:
				toEm = 1.5;
				break;
			case 6:
				toEm = 2.0;
				break;
			case 7:
				toEm = 3.0;
				break;
		}
		$(this).css("font-size", toEm+"em");
	});
	$(".reglasPublicacion_cuerpo font[color]").each(function(){
		$(this).css("color", $(this).attr("color"));
	});
});