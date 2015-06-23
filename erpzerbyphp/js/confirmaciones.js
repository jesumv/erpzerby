/**
 * @author jmv
 */

//VARIABLES GLOBALES -------------------------------------------------- >     
var imptotal = 0;
var cajtotal = 0;
var pestotal = 0;
var voltotal = 0;
var rengs = document.getElementById('tablaprinc').getElementsByTagName('tr').length;

var arreglo1;
var arreglo2;

arreglo1=creaarreglo();
arreglo2=creainf();


$( document ).ready(function() {
// inicialización de máscaras --------------------------------------------------------------------------------------------->
	Typecast.Init();
	focusIt("entc09");	
}

)

//validación de que existan ordenes para el formato-------------------------------------------------------------------->

if(rengs<2){
	//NO HAY ORDENES
    mensaje("no hay ordenes para ese formato de tienda");
}

else{
	//SI HAY ORDENES
    var cols = document.getElementById('tablaprinc').rows[1].cells.length;
    var mybody= document.getElementsByTagName("body")[0];
    var mitabla= document.getElementById('tablaprinc');
    var mitabla2 = document.getElementById("inf");
    
    // arreglo para el calculo de inventarios
    var invs = new Array();
    var invo = new Array();
    var invf = new Array();  
    
    //llenar el arreglo con los inventarios iniciales

        for (var i= 1; i<rengs; i++){
        
                var mireng= mitabla.getElementsByTagName("tr")[i];
                var micel = mireng.getElementsByTagName("td")[cols-3];
            // first item element of the childNodes list of mycel.toma el valor de los inventarios iniciales
                var invini1=micel.childNodes[0];
            //el arreglo invs contiene los valores del inventario inicial. se reduce 1 para que lea desde renglon0. esta variable
            //resulta en un warning de javascript porque la varibale no esta definida.
                invs[i-1] = parseFloat(invini1.data,10);  

        }

    }
// fin del if si sí hay ordenes

     
 function calccaj(columc,band){
 // esta funcion calcula el total de cajas de la orden
    
        //reune los elementos de la tabla de totales
                    
        // la fila oculta de importes 
            var mireng2       = mitabla2.getElementsByTagName("tr")[6];
        //OJO: esta celda debera variar con el check list elegido
            var micel2       = mireng2.getElementsByTagName("td")[columc+1];
             
            // first item element of the childNodes list of mycel
            var mycelvalue2=micel2.childNodes[0];
             
            // content of valoract is the data content of myceltext
            // the value varies according to bandera
            switch(band)
            {
            case 1:
              var valoract2=- parseFloat(mycelvalue2.data.replace(",", ""),10);
              break;
            case 2:
              var valoract2= parseFloat(mycelvalue2.data.replace(",", ""),10);
              break;
            default:
               var valoract2= 0;
            }
           
            
            //regresa el valor de la celda
            return valoract2;
     
            
        }
        
        function calcpeso(colump,band){
         // esta funcion calcula el peso total  de una  orden
    
        //reune los elementos de la tabla de totales
                    
        // la fila oculta de pesos
            var mireng3      = mitabla2.getElementsByTagName("tr")[2];
        //OJO: esta celda debera variar con el check list elegido
            var micel3      = mireng3.getElementsByTagName("td")[colump+1];
             
            // first item element of the childNodes list of mycel
            var mycelvalue3=micel3.childNodes[0];
             
            // content of valoract is the data content of myceltext
            // the value varies according to bandera
            switch(band)
            {
            case 1:
              var valoract3=- parseFloat(mycelvalue3.data.replace(",", ""),10);
              break;
            case 2:
              var valoract3= parseFloat(mycelvalue3.data.replace(",", ""),10);
              break;
            default:
               var valoract3= 0;
            }
           
            
            //regresa el valor de la celda
            return valoract3;
        }
        
        
        function calcvol(columv,band){
            
        // esta funcion calcula el volumen total  de la orden
    
        //reune los elementos de la tabla de totales

        // la fila oculta de importes 
            var mireng4      = mitabla2.getElementsByTagName("tr")[4];
        //OJO: esta celda debera variar con el check list elegido
            var micel4       = mireng4.getElementsByTagName("td")[columv+1];
             
            // first item element of the childNodes list of mycel
            var mycelvalue4=micel4.childNodes[0];
             
            // content of valoract is the data content of myceltext
            // the value varies according to bandera
            switch(band)
            {
            case 1:
              var valoract4=- parseFloat(mycelvalue4.data.replace(",", ""),10);
              break;
            case 2:
              var valoract4= parseFloat(mycelvalue4.data.replace(",", ""),10);
              break;
            default:
               var valoract4= 0;
            }
           
            
            //regresa el valor de la celda
            return valoract4;
      
            
        }
        
        
        function calcinv(reng,col,band){
            //esta funcion calcula los inventarios restantes después de incluir una orden
           //se llama para la columna del check oprimido.
           //recibe el renglon del cual tomara el inventario inicial y la orden, la columna correspondiente a la orden
           //y la bandera para saber si el checkbox está checado o no.
           //regresa un arreglo con los valores del inventario de la orden y el inventario final.

// el valor del inventario inicial del arreglo construido al inicio       
            var invinic=invs[reng];
//se ajusta el numero de columna 
	        var col2=(col+8)+(2*(col-1));
//el valor existente de la orden-------------------------------------------------------------
                    
//el renglon es uno mas porque en la tabla el 0 son los titulos
            var mireng5  = mitabla.getElementsByTagName("tr")[reng+1];
            var mitexto = document.getElementById('entc'+reng+col2).value;
            
//la columna la define el cheklist oprimido + 2 para obtener la columna de cajas de la orden
            var ordenact1 = mireng5.getElementsByTagName("td")[(col+3)+(2*(col-1))];
            var ordenact2 = ordenact1.childNodes[0];
           var ordenact3 = parseFloat(mitexto,10);
//el valor de las ordenes seleccionadas----------------------------------------------------------
            var ordenacum1  = mireng5.getElementsByTagName("td")[cols-2];
            var ordenacum2  = ordenacum1.childNodes[0];
            var ordenacum3  = parseFloat(ordenacum2.data,10);
            
             
            // the value varies according to band
            switch(band)
            {
            case 1:
              var invorden= ordenacum3 - ordenact3;
                    
              break;
              
            case 2:
              var invorden= ordenacum3 + ordenact3;      
              break;
              
             default:
              var invorden= 0;
                
             } 
             
             var invfin = invinic-invorden;
             
            return [invorden, invfin];
        
        }
        

function calcimpor(columim, band){
    // esta funcion calcula el total del importe de la orden
    //calcula el nuevo importe de la orden y modifica la celda del importe total
    	 	cambiaimpor(columim);   
    // la fila oculta de importes totales por cada orden
        var mireng1 = mitabla2.getElementsByTagName("tr")[0];
        var micel1  = mireng1.getElementsByTagName("td")[columim+1];
        // first item element of the childNodes list of mycel
        var mycelvalue1=micel1.childNodes[0];
         
        // content of valoract is the data content of myceltext
        // the value varies according to bandera
        switch(band)
        {
        case 1:
          var valoract1=- parseFloat(mycelvalue1.data.replace(",", ""),10);
          break;
        case 2:
          var valoract1= parseFloat(mycelvalue1.data.replace(",", ""),10);
          break;
        default:
           var valoract1= 0;
        }
       
        
        //regresa el valor de la celda
        return valoract1;
        }
 //----------------------------------------------------------------------------------------------------

        function cresumen(chorigen,columnt){
        	
 // esta funcion recibe el id del objeto que se examina, y la columna correspondiente 
 //calcula los valores para la seccion de resumen de la hoja confirmaciones.          
        var checkboxvar = document.getElementById(chorigen);
        var labelimpor = document.getElementById('itot');
        var labelcaja= document.getElementById('cajtot');
        var labelpeso= document.getElementById('pesotot');
        var labelvol= document.getElementById('voltot');
        var bandera = 0;
        
 // el caso cuando el check box no está checado--------------------------------------------------------
 
        if (!checkboxvar.checked) {
                        
 //si ya es cero, el valor de los datos de resumen se queda en cero
             
            if (labelimpor == 0.00){
                bandera = 0;
                prim1= 0;
                prim2 = 0;
                prim3 = 0;
                prim4 = 0;
                 
            }
 //si no, calcula el valor de las celdas
            else{             
            bandera = 1 ; // indica que el checkbox no esta encendido y se  pasa a las funciones de calculo                                                                                
            var prim1 = parseFloat(calcimpor(columnt,bandera),10);  
            var prim2 = parseFloat(calccaj(columnt,bandera),10);
            var prim3 = parseFloat(calcpeso(columnt,bandera),10);
            var prim4 = parseFloat(calcvol(columnt,bandera),10);
               
            }
//se convierten los valores a numeros y se suma el valor de la celda con el nuevo calculo
            
                var sec1 =  parseFloat(imptotal,10)+parseFloat(prim1,10);
                var sec2 =  parseFloat(cajtotal,10)+parseFloat(prim2,10);
                var sec3 =  parseFloat(pestotal,10)+parseFloat(prim3,10);
                var sec4 =  parseFloat(voltotal,10)+parseFloat(prim4,10);
                
                imptotal = sec1;
                cajtotal = sec2;
                pestotal = sec3;
                voltotal = sec4;
                
                //ciclo para contruir arreglos con calculo de inventarios 
            for(var k = 0; k < rengs-1; k++){
//calcula los inventarios despues del click
                var resp = calcinv(k,columnt+1,bandera);
//regresa el valor del la orden y el inventario final y se asignan a una variable
                    invo[1]  = resp [0];
                    invf[1]  = resp [1];
                var midato1 = document.getElementById('io'+k);
                var midato2 = document.getElementById('if'+k);
                var midato3 = document.getElementById('io2'+k);
                var midato4 = document.getElementById('if2'+k);
                 
                midato1.innerHTML = invo[1] ;
                midato2.innerHTML = invf[1] ;
                midato3.innerHTML = invo[1] ;
                midato4.innerHTML = invf[1] ;
                           
            }
//regreso de celdas de cantidades al valor original
		devorig(columnt);
           
        }
//---------------------------------------------------------------------------------------------------------


 //el caso cuando el checkbox sí está checado----------------------------------------------------------------

        else {
            bandera = 2;
 			
 //definicion del contenido de las celdas de la tabla resumen
            var prim1 = parseFloat(calcimpor(columnt,bandera),10);
            var prim2 = parseFloat(calccaj(columnt,bandera),10);
            var prim3 = parseFloat(calcpeso(columnt,bandera),10);
            var prim4 = parseFloat(calcvol(columnt,bandera),10);
//adicion al total           
            var sec1 =  parseFloat(imptotal,10)+parseFloat(prim1,10);
            var sec2 =  parseFloat(cajtotal,10)+parseFloat(prim2,10);
            var sec3 =  parseFloat(pestotal,10)+parseFloat(prim3,10);
            var sec4 =  parseFloat(voltotal,10)+parseFloat(prim4,10);
            
            imptotal = sec1;
            cajtotal = sec2;
            pestotal = sec3;
            voltotal = sec4;
            
                   
//ciclo para contruir arreglos con calculo de inventarios  
            for(var k = 0; k < rengs-1; k++){
//calcula los inventarios despues del click
                var resp = calcinv(k,columnt+1,bandera);
				
//regresa el valor del la orden y el inventario final y se asignan a una variable
                    invo[1]  = resp [0];
                    invf[1]  = resp [1];
                var midato1 = document.getElementById('io'+k);
                var midato2 = document.getElementById('if'+k);
                var midato3 = document.getElementById('io2'+k);
                var midato4 = document.getElementById('if2'+k);
                 
                midato1.innerHTML = invo[1] ;
                midato2.innerHTML = invf[1] ;
                midato3.innerHTML = invo[1] ;
                midato4.innerHTML = invf[1] ;
                           
            }
            
                            

        }
//-------------------------------------------------------------------------------------------------------------------------    

 //se muestra el valor de los totales en la tabla resumen. para todos los casos
 
            labelimpor.innerHTML = addCommas(sec1.toFixed(2)) + ' pesos';
            labelcaja.innerHTML = addCommas(sec2.toFixed(0)) ;
            labelpeso.innerHTML = addCommas(sec3.toFixed(1)) + ' kg.';
            labelvol.innerHTML = addCommas(sec4.toFixed(2)) + ' m3.';
            
 
            
     }
// fin de cresumen ---------------------------------------------------------------------------------


function focusIt(elemento)
{
  var mytext = document.getElementById(elemento);
  mytext.focus();
}


function cambiaimpor(col){
	//esta funcion toma el valor de las celdas de cantidades que se han cambiado para volver a calcular el importe de la orden
	//variable para guardar el nuevo importe de la orden
	var nuevoimpor=0;
	var nuevopeso = 0;
	var nuevovol = 0;
	var nuevacaj= 0;
	var precio = 0;
	var emp = 0;
	var cant = 0;
	var colcant =(col+9)+(2*(col));
	//esta funcion calcula el importe total de una orden, multiplicando la columna de precio por la columna de cantidad
	//obtención del total de renglones de la tabla
	var oRows = document.getElementById('tablaprinc').getElementsByTagName('tr');
	var iRowCount = oRows.length;
//obtención del importe de cada articulo en la orden	
		for(var i = 0;i < (iRowCount-1); i++ ){
			precio = parseFloat(document.getElementById('prec'+i).innerHTML,10);
			emp =parseFloat(document.getElementById('emp'+i).innerHTML,10);
			cant =parseFloat(document.getElementById('entc'+i+ colcant).value,10);
			nuevoimpor =nuevoimpor+(cant*emp*precio);
			var pesoact = document.getElementById('pes'+(i)).innerHTML;
			nuevopeso = nuevopeso + (pesoact*cant);
			var volact = document.getElementById('vol'+(i)).innerHTML;
			nuevovol = nuevovol +(cant*volact);
			var nuevacaj = nuevacaj+ cant;
		};
	//escritura de los nuevos totales
	document.getElementById('impor'+col).innerHTML = addCommas(nuevoimpor.toFixed(2));
	document.getElementById('peso'+col).innerHTML = addCommas(nuevopeso.toFixed(1));
	document.getElementById('volt'+col).innerHTML = addCommas(nuevovol.toFixed(2));
	document.getElementById('caja'+col).innerHTML = addCommas(nuevacaj.toFixed(0));
	
}


function devorig(col){
	//esta funcion regresa el valor de las cantidades de la orden a su valor original recibe el numero de columna de la orden
	//variable para guardar el nuevo importe de la orden
	var colcant =(col+9)+(2*(col));
	var oRows = document.getElementById('tablaprinc').getElementsByTagName('tr');
	var iRowCount = oRows.length;
//obtención de la cantidad original cada articulo en la orden y cambio en html	
		for(var i = 0;i < (iRowCount-1); i++ ){
			document.getElementById('entc'+i+colcant).value = arreglo1[i][1+(col*2)];
		};
//obtención de los totales originales y cambio en html
		var datoinf = document.getElementById('inf').getElementsByClassName('derecha');
		//cambio del importe
		document.getElementById('impor'+col).innerHTML = arreglo2[0][col];
		//canbio del peso
		document.getElementById('peso'+col).innerHTML = arreglo2[3][col];
		//cambio del volumen
		document.getElementById('volt'+col).innerHTML = arreglo2[5][col];
		//cambio del numero de cajas
		document.getElementById('caja'+col).innerHTML = arreglo2[7][col];
}


function creaarreglo(){
	//esta funcion crea y regresa un arreglo con los valores originales de cada orden
	//obtención del total de renglones de la tabla
	var oRows = document.getElementById('tablaprinc').getElementsByTagName('tr');
	var iRowCount = oRows.length -1;
	var lcelda = document.getElementById('tablaprinc').getElementsByClassName('cant').length;
	var noCol =((lcelda-(iRowCount*3)))/iRowCount;
	
	//creacion del arreglo
	 var columnas = new Array(iRowCount);

	for(var r = 0; r<iRowCount; r++){
		columnas[r] = new Array(noCol);
		for (var col = 0; col< noCol;col++){
				 columnas[r][col] = document.getElementById('celd'+r+col).innerHTML;
		}
	}
	
		return columnas;
	
}


function creainf(){
	//esta funcion crea y regresa un arreglo con los valores de la tabla inf
	var lceldai = document.getElementById('inf').getElementsByClassName('derecha').length;
	var noColi = (lceldai)/8;
	var elementos = document.getElementById('inf').getElementsByClassName('derecha');
	//variable para circular los elementos de la tabla
	var ciclo = 0;
	//creacion del arreglo
	 var columnasi = new Array(8);
	
	for(var r = 0; r<8; r++){
		columnasi[r] = new Array(noColi);
		for (var coli = 0; coli< noColi;coli++){
				 columnasi[r][coli] = elementos[ciclo].innerHTML;
		ciclo++;
		}
	}
	
		return columnasi;
	
	
}


