$(document).ready(main);

var contador = 1;

function main(){
	$('.boton__barra').click(function(){
		/*$('.menu-movil').toggle();*/
		if(contador == 1){
			$('#menu-movil').animate({
				left:'0'
			});
			alert("Hola el contador es 1");
			contador = 0;
		} else {
			contador = 1;
			$('#menu-movil').animate({
				left:'-100%'
			});
			alert("Hola el contador es otra cosa");
		}

	});
};