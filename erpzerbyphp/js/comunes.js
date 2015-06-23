//funciones de uso comun en las paginas

function mensaje(digo){
	alert("AVISO: "+ digo);
}
//las siguientes dos funciones se usan  para definir el titulo de la pagina y se 
//duplica para abrir solicitud, o confirmaciones segun el caso.
function eligemenu(escoge){
	
	window.location.href = "confirmaciones.php?escoge=" + escoge;	
}

function eligemenu2(escoge){
	
	window.location.href = "solicita.php?escoge=" + escoge;	
}


function addCommas(nStr)
{
	var x;
	var x1;
	var x2;
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}