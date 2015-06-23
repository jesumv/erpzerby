<?php
//EN CONSTRUCCION+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//directiva de la conexion a la base de datos
include_once "php/config.php";   
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php"; 
//directiva a la revision de conexion
include_once"php/lock.php";
//directiva de inclusion de funciones de bd
include_once"php/llenatablas.php";


 
//CONSULTAS SQL-----------------------------------------------------------------------------------------------

//catalogo de articulos
      $arts= mysql_query("SELECT DISTINCT t2.upc,t3.desc1,t3.pesocaja,t3.volcaja  FROM orden_resumen AS t1 INNER JOIN orden_detalle AS t2 
      ON t1.orden = t2.orden LEFT JOIN cat_arts AS t3 ON t2.upc = t3.upc WHERE t1.status = 0 AND t1.no_formato_tienda".$signo."7507003100025 ORDER BY t2.upc")
      or die ("Error en la consulta de articulos.".mysql_error());
        
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    
<head>
    <style>
        table.ex1 {margin-left:29px;}
    </style>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    
    <!--links a hojas de estilo ----------------------------------------------------->
    
    <link rel="stylesheet" type="text/css" href="css/comun.css">
    <link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
    <!-- links a hojas javascript ---------------------------------------------------->
    <script type="text/javascript" src="js/comunes.js"></script>
    
    <title>Confe</title>

</head>

<body>
    
    
  <!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
    <?php 
  $titulo = "CATALOGO DE ARTICULOS";
  include_once "/include/barrasup.php" 
  ?>          


 <!---CONSTRUCCION DE LA  PAGINA -----------------------------------------------------------------  
      
  <!--tablas ------------------------------------------------------------------------------->
   

    
<div class = 'fila'>

 <!--TABLA PRINCIPAL ----------------------------------------------------->
    
    <table class= "ex1" border =1 id='tablaprinc'style = "margin-left:6px">
        
        <tr>
             <th>NO.</th>
             <th>UPC</th>
             <th>DESCRIPCION</th>    
            <th>INVENTARIO</th>
        </tr>
        

           
    
<!--FIN DE LA DIVISION FILA ------------------------------------------>     
</div>

<?php
    include_once "/include/footer.php" 
  ?>

<!--SECCION DE SCRIPTS ------------------------------------------------------------------------------------------------------>
    
<script type="text/javascript">

// inicialización de máscaras --------------------------------------------------------------------------------------------->

window.onload = go;
    function go(){
        Typecast.Init();
    }
    
//VARIABLES GLOBALES --------------------------------------------------      
var imptotal = 0;
var cajtotal = 0;
var pestotal = 0;
var voltotal = 0;
var rengs = document.getElementById('tablaprinc').getElementsByTagName('tr').length;

//validación de que existan ordenes para el formato
if(rengs<2){
    mensaje("no hay ordenes para ese formato de tienda");
}

    else{
    var cols = document.getElementById('tablaprinc').rows[1].cells.length;
    var mybody= document.getElementsByTagName("body")[0];
    var mitabla= document.getElementById('tablaprinc');
    var mitabla2    = mybody.getElementsByTagName("table")[4];
    
    // arreglo para el calculo de inventarios
    var invs = new Array();
    var invo = new Array();
    var invf = new Array();
    
    
    
    //llenar el arreglo con los inventarios iniciales
    
        for (var i= 1; i<rengs; i++){
    
                var mireng       = mitabla.getElementsByTagName("tr")[i];
                var micel       = mireng.getElementsByTagName("td")[cols-3];
            // first item element of the childNodes list of mycel
                var invini1=micel.childNodes[0];
            //el arreglo invs contiene los valores del inventario inicial
                invs[i-1] = parseFloat(invini1.data,10)
                
                
        }
    
    }
// fin del if si si hay ordenes


        function cresumen(chorigen,columnt){
            
 // esta funcion recibe el id del objeto que se examina,el del objeto que se modifica como resultado, y la columna correspondiente 
           
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
            bandera = 1  // indica que el checkbox no esta encendido y se  pasa a las funciones de calculo
            
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
            
        }
//---------------------------------------------------------------------------------------------------------


 //el caso cuando el checkbox sí está checado----------------------------------------------------------------

        else {
            bandera = 2
 //definicion del contenido de la celda
            var prim1 = parseFloat(calcimpor(columnt,bandera),10);
            var prim2 = parseFloat(calccaj(columnt,bandera),10);
            var prim3 = parseFloat(calcpeso(columnt,bandera),10);
            var prim4 = parseFloat(calcvol(columnt,bandera),10);
//adicion al total           
            var sec1 =  parseFloat(imptotal,10) + parseFloat(prim1,10);
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
 
            labelimpor.innerHTML = sec1.toFixed(2) + ' pesos';
            labelcaja.innerHTML = sec2.toFixed(0) ;
            labelpeso.innerHTML = sec3.toFixed(1) + ' kg.';
            labelvol.innerHTML = sec4.toFixed(2) + ' m3.';
            
 //y se muestra el valor de los inventarios.
 
            
     }
// fin de cresumen ---------------------------------------------------------------------------------

    function calcimpor(columim, band){
    // esta funcion calcula el total del importe de la orden
    
    //reune los elementos de la tabla de totales
                
        
    // la fila oculta de importes 
        var mireng1       = mitabla2.getElementsByTagName("tr")[1];
        var micel1       = mireng1.getElementsByTagName("td")[columim+1];
         
        // first item element of the childNodes list of mycel
        var mycelvalue1=micel1.childNodes[0];
         
        // content of valoract is the data content of myceltext
        // the value varies according to bandera
        switch(band)
        {
        case 1:
          var valoract1=- parseFloat(mycelvalue1.data,10);
          break;
        case 2:
          var valoract1= parseFloat(mycelvalue1.data,10);
          break;
        default:
           var valoract1= 0;
        }
       
        
        //regresa el valor de la celda
        return valoract1;
        }
 //----------------------------------------------------------------------------------------------------
        
        function calccaj(columc,band){
            // esta funcion calcula el total de cajas de la orden
    
        //reune los elementos de la tabla de totales
                    
        // la fila oculta de importes 
            var mireng2       = mitabla2.getElementsByTagName("tr")[4];
        //OJO: esta celda debera variar con el check list elegido
            var micel2       = mireng2.getElementsByTagName("td")[columc+1];
             
            // first item element of the childNodes list of mycel
            var mycelvalue2=micel2.childNodes[0];
             
            // content of valoract is the data content of myceltext
            // the value varies according to bandera
            switch(band)
            {
            case 1:
              var valoract2=- parseFloat(mycelvalue2.data,10);
              break;
            case 2:
              var valoract2= parseFloat(mycelvalue2.data,10);
              break;
            default:
               var valoract2= 0;
            }
           
            
            //regresa el valor de la celda
            return valoract2;
     
            
        }
        
        function calcpeso(colump,band){
         // esta funcion calcula el peso total  de la orden
    
        //reune los elementos de la tabla de totales
                    
        // la fila oculta de importes 
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
              var valoract3=- parseFloat(mycelvalue3.data,10);
              break;
            case 2:
              var valoract3= parseFloat(mycelvalue3.data,10);
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
            var mireng4      = mitabla2.getElementsByTagName("tr")[3];
        //OJO: esta celda debera variar con el check list elegido
            var micel4       = mireng4.getElementsByTagName("td")[columv+1];
             
            // first item element of the childNodes list of mycel
            var mycelvalue4=micel4.childNodes[0];
             
            // content of valoract is the data content of myceltext
            // the value varies according to bandera
            switch(band)
            {
            case 1:
              var valoract4=- parseFloat(mycelvalue4.data,10);
              break;
            case 2:
              var valoract4= parseFloat(mycelvalue4.data,10);
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
            
//el valor existente de la orden-------------------------------------------------------------
                    
//el renglon es uno mas porque en la tabla el 0 son los titulos
            var mireng5      = mitabla.getElementsByTagName("tr")[reng+1];
            
//la columna la define el cheklist oprimido +1 para obtener la columna de cajas de la orden

            var ordenact1   = mireng5.getElementsByTagName("td")[(col*2)+2];
            var ordenact2 = ordenact1.childNodes[0];
            var ordenact3 = parseFloat(ordenact2.data,10);
            
//el valor de las ordenes acumuladas----------------------------------------------------------

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
             
             var invfin = invinic-invorden
             
            return [invorden, invfin];
        
        }
     
     

    </script>
    
    
</body>


</html>