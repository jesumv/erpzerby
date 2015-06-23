<?php
//version 1.1 julio 11, 2013.
//esta version tiene solo el css para entregas parciales

//directiva de la conexion a la base de datos
include_once "php/config.php";   
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php"; 
//directiva a la revision de conexion
include_once"php/lock.php";
//directiva de inclusion de funciones de bd
include_once"php/llenatablas.php";


//revisa si la pagina se esta posteando despues de llenar los datos

$confir = true;
$_SESSION['bandimp']= 0;

if(isset($_GET['escoge'])){
    $_SESSION['selec']=$_GET['escoge'];
    $escoge = $_GET['escoge'];
}

else if(isset($_POST['enviof'])){
    $escoge = $_SESSION['selec'];
//indica al sistema que se desea imprimir las ordenes confirmadas
    $_SESSION['bandimp']= 1;
             
}

else {
    $escoge = $_SESSION['selec'];
             
}

//variables globales
// indica el titulo de la pagina y determina el signo para la consulta de ordenes
    switch ($escoge) {
      
    	case '1':
            global $signo;
    		$signo = '=';
            $formato = "BODEGA AURRERA";
            $cad = "=1";
    		break;
        case '2':
            global $signo;
            $signo = '!=';
            $formato = "WAL-MART Y SUPERAMA";
            $cad = "!=1";
            break;         
    	
    	default:
            
    	   echo "diferente </br>";
           echo $escoge."</br>";
           echo $signo;
    }

$_SESSION['formato']= $formato;
$_SESSION['signo']= $signo;

//inicializacion de arreglo para tabla de articulos
$tabla = array();
//inicializacion de arreglos para elementos de la tabla
$ords = array();
$importes = array();
$formatos = array();
$notienda = array();
$nomtienda = array();
$cancela = array();
$obs = array();
$ctes = array();
$pesos = array();
$vols = array();
$invents = array();
$pesotot = array();
$voltot = array();
$cajord = array();
$cajtot = array();

 
//CONSULTAS SQL-----------------------------------------------------------------------------------------------

//articulos a entregar 
      $arts= mysql_query("SELECT DISTINCT t2.upc,t3.desc1,t2.cad_art,t3.pesocaja,t3.volcaja  FROM orden_resumen AS t1 INNER JOIN orden_detalle AS t2
      ON t1.orden=t2.orden INNER JOIN cat_arts AS t3 ON t2.upc = t3.upc  AND t2.cad_art=t3.cadena 
      WHERE t1.status = 0 AND t1.no_formato_tienda".$signo."7507003100025 ORDER BY t2.upc")
      or die ("Error en la consulta de articulos.".mysql_error());
      
//ordenes a entregar 
        
      $ordenes = mysql_query("SELECT t1.orden, t1.monto_total,t1.formato_tienda,t1.no_tienda, t2.nomcorto, t1.fecha_canc, t1.obs FROM orden_resumen as t1 LEFT JOIN 
       cdi as t2 ON t1.no_tienda = t2.no_tienda WHERE  t1.status = 0 AND t1.no_formato_tienda".$signo."7507003100025 ORDER BY t2.nomcorto, 
       t1.no_tienda,t1.orden" ) or die ("Error en la consulta de ordenes.".mysql_error());
      
// seleccion de clientes con ordenes x entregar fila 1 encabezado
       $clientes= mysql_query("SELECT t1.nomcorto FROM clientes as t1 INNER JOIN orden_resumen as t2 
        on t1.id_clientes = t2.cliente_zerby INNER JOIN cdi as t3 ON t2.no_tienda = t3.no_tienda WHERE t2.status = 0 
        AND t2.no_formato_tienda ".$signo."7507003100025 ORDER BY t3.nomcorto, t2.no_tienda,t2.orden")
         or die ("Error en la consulta de clientes.".mysql_error());


//VALIDAR QUE EXISTAN LAS CONSULTAS--------------------------------------------------------------------------------


//CONSTRUCCION DE MATRICES DE DATOS -------------------------------------------------------------------------------

//construccion de encabezado y definicion de numero de columnas
 
    $colord = 0;
     while($columna = mysql_fetch_array($ordenes)){
      $ords[$colord]= $columna[0];
      $importes[$colord]= $columna[1];
      $formatos[$colord]= $columna[2];
      $notienda[$colord]= $columna[3];
      $nomtienda[$colord]= $columna[4];
      $cancela[$colord] = $columna[5];
      $obs[$colord]=$columna[6];
      $colord++; 
     } 
     
//construccion de matriz de clientes, renglon 1 del encabezado
    $col1=0;
    while($fila1 = mysql_fetch_array($clientes)){
        $ctes[$col1] = $fila1[0];
    $col1++;
    }

   
      
//construccion de columnas 0-4, upc, descripcion,cadena,peso, vol para la tabla PRINCIPAL  y definicion de no. renglones  

$reng = 0;
while($renglon= mysql_fetch_array($arts)){
    //upc
    $tabla[$reng][0]= $renglon[0];
    $upcact= $renglon[0];
  //descripcion
    $tabla[$reng][1] =$renglon[1];
    //cadena
    $tabla[$reng][2] =$renglon[2];
    $cadact=$renglon[2];
    //peso
    $tabla[$reng][3] =$renglon[3];
    //volumen
    $tabla[$reng][4] =$renglon[4];
    
  
//construccion arreglo de inventarios existentes por upc y cadena  

     $invs= mysql_query("SELECT SUM(cajas) FROM inventario WHERE upc  = $upcact AND cadena = $cadact")
     or die ("Error en la consulta de inventarios.".mysql_error());
       
     $invent=mysql_fetch_array($invs);
     $invents[$reng]= $invent[0];

      
    $reng ++;   
}

//construccion del resto de las columnas 5... n de la tabla principal

// ciclo para cada orden, es decir conjunto de 2 columnas-------------------------------------
// se empieza en la columna 5

    $colact = 5;
// se toma el numero de columnas de la seccion encabezado
for ($outer = 0; $outer<$colord; $outer++){
// variable para tomar el numero de orden que definira la columna
    $ordact = $ords[$outer]; 


    //ciclo interior para los renglones de cada columna-------------------------------------------
    // se toma el numero de renglones de la seccion columnas 0-4
    for ($j =0; $j <$reng; $j++){
        //el numero de articulo
            $upc = $tabla[$j][0];
        //la cadena
            $cad= $tabla[$j][2];
        //la consulta de variables por cada orden
            $cant= mysql_query("SELECT cantidad,empaque FROM orden_detalle WHERE upc = $upc AND cad_art = $cad AND orden = $ordact")
            or die ("Error en la consulta de cantidades.".mysql_error()); 
//asignacion de valor 0 si el upc no aparece en esa orden en particular
            if(!$cant){
                $cantart[0]= 0; 
                $cantart [1]= 0;
                }
//en caso contario, se obtiene el arreglo de cantidades y empaques     
            else{
             $cantart = mysql_fetch_array($cant);
            }
//Y se crean tres columnas (articulos, cajas y entregar) por cada orden empezando en 5
           $tabla[$j][$colact]= $cantart[0];
           $tabla[$j][$colact+1]= cajas($cantart[0],$cantart[1]);
           $tabla[$j][$colact+2]= $tabla[$j][$colact+1];
           
// y se crean arreglos para las cantidades que se sumaran en el resumen
           $cajord [$j][$outer]= $tabla[$j][$colact+1];
           $pesos[$j][$outer]= ($tabla[$j][$colact+1])*$tabla[$j][3];
           $vols[$j][$outer]= ($tabla[$j][$colact+1])*$tabla[$j][4];
           
        
    }   
//FIN DEL CICLO INTERIOR DE RENGLONES-------------------------------------------------------------------

// continuacion del ciclo de  3 columnas 
            $colact = $colact +3;
            
//calculo de las variables de la parte inferior de la tabla  por cada columna  
            //calculo del peso y volumen para cada orden
           $pesotot[]= array_sum_key( $pesos, $outer );
           $voltot[]= array_sum_key( $vols, $outer );
           $cajtot[]= array_sum_key( $cajord, $outer );

}

//FIN DEL CICLO EXTERIOR DE COLUMNAS---------------------------------------------------------------------

//inicializacion  para los valores de la tabla resumen
$itotal = 0;
$ctotal = 0;
$ptotal = 0;
$vtotal = 0;

//sección de acciones al envio de la forma -----------------------------------------------------------------
if(isset($_POST['enviof'])){
    
        
        llenacita($colord,$ords,$reng,$tabla,$formato); 
       
   
}

   
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    
<head>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!--scripts de mascaras de campos -->

<script type="text/javascript" src="js/typecast_1.4.js"></script>
<script type="text/javascript" src="js/typecast.config.js"></script>

<!--links a hojas de estilo ----------------------------------------------------->
<link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
<link rel="stylesheet" type="text/CSS" href="css/confirmaciones.css">
<!-- links a hojas javascript ---------------------------------------------------->
<script type="text/javascript" src="js/comunes.js"></script>


<title>CONFIRMACIONES</title>

</head>

<body>
    
    
    
  <!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
    <?php 
  $titulo = "CONFIRMACIONES ".$formato;
  include_once "include/barrasup.php" 
  ?>          


 <!---CONSTRUCCION DE LA  PAGINA -----------------------------------------------------------------  
      
  <!--tablas ------------------------------------------------------------------------------->
   
<div >
<!--tabla 1-->
    <table id="resumen" border= "1">
        <th colspan = '2'>RESUMEN DE LA ORDEN</th>
            <tr>
 <?php
           echo "<td width = '175px'>importe total</td>" ;
           $totalmod = number_format($itotal,2);
           echo " <td id='itot'>$totalmod</td>";
                
           echo "</tr>" ;
            echo "<tr>";
               echo "<td>Cajas</td>" ;
               echo "<td id='cajtot'>$ctotal</td>";
                
            echo "</tr>";
            echo"<tr>";
            
                echo "<td>Peso</td>";
             echo " <td id= 'pesotot'>$ptotal</td>";
            echo "</tr>";
            
            echo"<tr>";
            
            echo"<td>Volumen</td>";   
            echo " <td id='voltot'>$vtotal</td>";    
 ?>              
            </tr>
        
    </table>
    
</div>
    
<div class ='fila'>
<!--TABLA DE ACCIONES ------------------------------------------> 
<form id ="incluye" action= "<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST" >
<!--tabla 2 -->
    <table class = 'acciones' border = '2' id='accion'>
        <tr>
            <td class = 'rh'>INCLUIR</td>
 <?php
        for ($i=0;$i<$colord;$i++){
            echo "<td class =\"cl\"><input type=\"checkbox\" name=\"check$i\"
                 id=\"check$i\" value=\"$i\" onclick='cresumen(\"check$i\",$i);'/> </td>";
        }

 ?>
        </tr>
        
        
        <tr>
            <td>FECHA(DD/MM/AAA)</td>
  <?php
        for ($i=0;$i<$colord;$i++){
            echo "<td class =\"cl\"><input type=\"text\" class=\"TCMask[##/##/####]\" value=\"mm/dd/yyyy\"name=\"fecha$i\"
                 id=\"fecha$i\" '/> </td>";
        }

 ?>
            
        </tr>
        
        <tr>
            <td>HORA(HH:MM)</td>
  <?php
        for ($i=0;$i<$colord;$i++){
            echo "<td class =\"cl\"><input type=\"text\" class=\"TCMask[##:##]\" value=\"HH/MM\" name=\"hora$i\"
                 id=\"hora$i\" '/> </td>";
        }

 ?>
            
        </tr>
        
        <tr>
            <td>NO.CONFIRM.</td>
  <?php
        for ($i=0;$i<$colord;$i++){
            echo "<td class =\"cl\"><input type=\"text\" class='cincluye' name=\"noc$i\"
                 id=\"noc$i\" '/> </td>";
        }

 ?>
            
        </tr>
        
        <tr>
            <td>CONFIRMA</td>
  <?php
        for ($i=0;$i<$colord;$i++){
            echo "<td class =\"cl\"><input type=\"text\" class='cincluye' name=\"conf$i\"
                 id=\"conf$i\" '/> </td>";
        }

 ?>
            
        </tr>
   <!--celda de observaciones ---------------------------->     
        <tr>
            <td class="obs">OBSERVACIONES</td>
   <?php
            for ($i=0;$i<$colord;$i++){
            echo "<td class =\"cl\"> <textarea rows = \"4\" cols = \"15\" name=\"obs$i\"
                 id=\"obs$i\"> </textarea> </td>";
            }
    ?>        
        </tr>
        
        
        
        <tr>
            <td></td>
<!---campo oculto para enviar el nombre del formato de las ordenes al reporte impreso--->
       <?php
             echo "<input type='hidden' name=\"formato\" value=$formato>";
             echo "<td align='center' colspan = $colord><input type='submit' name ='enviof' value='Confirmado' /></td>"; 
       ?> 
           
        </tr>
         
    </table>

 
</form>
<!--TABLA SUPERIOR--------------------------------------------->  
<!--tabla 3 -->
    <table class = "tfila" border = '1' id='sup'>
        <tr>
            
                    <th class="inic">CLIENTE</th>
           
        <?php
//fila de clientes
             for($i=0; $i<$colord;$i++){ 
                echo "<th class= 'subsec' align='center' colspan='2'>$ctes[$i]</th>";  
             }
        ?>  
        </tr> 
         
        <tr>
                   <td class="in">FORMATO</td>
         <?php
//fila de formatos
                       for($i=0; $i<$colord;$i++){ 
                        echo "<td width = '86px'>$formatos[$i]</td>";
                        echo "<td width= '53px'>$notienda[$i]</td>";  
                        }
         ?>
       </tr> 
       
       <tr>
                    <td>ORDEN</td>
         <?php
 //fila de orden y nombre de tienda
                       for($i=0; $i<$colord;$i++){ 
                           echo "<td class = 'cant'>$ords[$i]</td>";
                           echo "<td class = 'caj'>$nomtienda[$i]</td>";  
                        }
         ?>
           
       </tr> 
       
       <tr>
                    <td>CANCELA</td>
           <?php
                    for($i=0; $i<$colord;$i++){ 
                           echo "<td align='center' colspan='2'> $cancela[$i] </td>"; 
                        }
           ?>
       </tr>

        
    </table>
    
 <!--TABLA PRINCIPAL ----------------------------------------------------->
    
    <table class= "principal" border =1 id='tablaprinc'>
        
        <tr>
             <th>NO.</th>
             <th>UPC</th>
            <th style='display:none'>CADENA</th>
             <th class = "desc">DESCRIPCION</th>
             
         <?php 
//FILA DE TITULOS ----------------------------------------------------------          
            for ($i = 0; $i<$colord; $i++){
               echo"<th class='izq'>CANTIDAD</th>";
               echo"<th class='der'>CAJAS</th>";
               echo"<th class='ent'>ENTREGAR</th>";
               
            }
         ?>
            <th >EXISTENCIA</th>
            <th >ORDEN</th>
            <th>INVENTARIO</th>
        </tr>
        
<?php
//CONSTRUCCION DE TABLA PRINCIPAL----------------------------------------------
//contador del numero de renglon COLUMNA 0
           $col1 = 1; 
            for ($j=0;$j < $reng;$j++){
                    echo "<tr>";             
                              
    // el numero del renglon                
                    echo "<td>$col1</td>";                
                    
                    //ciclo para las columnas 1,2 Y 3 de cada renglón
                                    
                    for($k=0;$k<2;$k++){
                   //upc y descripcion   
                       echo "<td>".$tabla[$j][$k]."</td>"; 
                    }
                    
                    
                    //ciclo para las columnas restantes de cada renglon
                    //cantidad, cajas, entregar por cada renglon solo si se activa la casilla
                //contador para saber cuando la celda debe cambiar de color
                
                $cuentaent = 1;
                    for($k=5;$k<$colact;$k++){
                        $celact = number_format($tabla[$j][$k],0);
                        if(($cuentaent % 3)==0){
                            echo "<td class = \"entc\"><input type=\"text\" name=\"entc$k\"id=\"entc$k\" value = $celact  size=\"4\"/></td>"; 
                        }
                        else{
                            echo "<td class = 'cant'>".$celact ."</td>"; 
                        }
                     $cuentaent++;
                    }
 //construccion de la columna de inventarios 
                    $invact =  $invents[$j];                
                    $invact1 = number_format($invact,0);
                    $invact2 = number_format($invact,0);
                    
                    echo "<td class = 'cant' id = 'ii$j'>".$invact1 ."</td>"; 
                    echo "<td class = 'cant' id = 'io$j' >0</td>"; 
                    echo "<td class = 'cant' id = 'if$j'>".$invact1."</td>"; 
                    
 //columnas OCULTAS de inventarios para el calculo
                    echo "<td  id = 'ii2$j' style='display:none'>".$invact2 ."</td>"; 
                    echo "<td  id = 'io2$j' style='display:none' >0</td>"; 
                    echo "<td  id = 'if2$j' style='display:none'>".$invact2."</td>"; 
 
                    
                    
 //FIN DEL RENGLON ----------------------------------------------------------------                               
                    echo "</tr>";
                
                $col1++;
 //fin del ciclo de renglones------------------------------------------------------
            }
          
              
            echo "</table>";
            

            
//CONSTRUCCION DE PARTE INFERIOR
            echo "<table class = 'bfila' border = '1'id='inf'>";
//fila de importe 1
                echo "<tr id='importerow'>";
                   
                    echo"<td class= 'enc'>IMPORTE </td>";
                    for ($i = 0; $i<$colord; $i++){
                        $imporact = number_format($importes[$i],2);
                        echo "<td class = 'imptot' class='cifr'>$imporact </td>";
                    }
            
                echo "</tr>";
//fila oculta de importes 2             
                echo "<tr id='impornumrow' style='display:none'>";
                   
                    echo"<td></td>";
                    for ($i = 0; $i<$colord; $i++){
                       $impornum = round($importes[$i], 2);
                        echo "<td class = 'imptot' >$impornum </td>";
                    }
            
                echo "</tr>";
                
                
 //fila de pesos 3               
                 echo "<tr>";
                    echo "<td width='108px'>PESO kg.</td>";
                    for ($i = 0; $i<$colord; $i++){
                        $pesoact = number_format($pesotot[$i],1);
                        echo "<td class = 'derecha'>$pesoact</td>"; 
                       
                    }
                 echo "</tr>";
                 

//fila oculta de pesos 4

                echo "<tr id='pesonumrow' style='display:none'>";
                    echo"<td></td>";
                    for ($i = 0; $i<$colord; $i++){
                        $pesonum = round($pesotot[$i], 1);
                        echo "<td class = 'derecha'>$pesonum</td>"; 
                       
                    }
                 echo "</tr>";
                
                                           
              
 //fila de volumenes 5               
                 echo "<tr>";
                    echo "<td width='108px'>VOLUMEN m3.</td>";
                    for ($i = 0; $i<$colord; $i++){
                        $volact = number_format($voltot[$i],2);
                        echo "<td class = 'derecha'>$volact</td>";   
                       
                    }
                 echo "</tr>"; 
                 
 //fila oculta de volumenes 6
                echo "<tr id='volnumrow' style='display:none'>";
                    echo"<td></td>";
                    for ($i = 0; $i<$colord; $i++){
                        $volnum = round($voltot[$i], 2);
                        echo "<td class = 'derecha'>$volnum</td>";   
                       
                    }
                 echo "</tr>"; 
 
 
                 
                              
 //fila  total de cajas 7
 
                 echo "<tr>";
                    echo "<td width='108px'>CAJAS</td>";
                    for ($i = 0; $i<$colord; $i++){
                        $cajaact = number_format($cajtot[$i],0);
                        echo "<td class = 'derecha'>$cajaact</td>";   
                       
                    }
                 echo "</tr>"; 
                 
//fila oculta de cajas 8

                 echo "<tr id='cajnumrow' style='display:none'>";
                    echo"<td></td>";
                    for ($i = 0; $i<$colord; $i++){
                        $cajanum = round($cajtot[$i], 0);
                        echo "<td class = 'derecha'>$cajanum</td>";   
                       
                    }
                 echo "</tr>"; 



                 
 //celda de comentarios fila 9
                 echo "<tr>";
                    echo "<td width='108px' height='100'>OBSERVAC.</td>";
                    for ($i=0; $i < $colord; $i++) { 
                       echo "<td >$obs[$i]</td>";  
                    }
                    
                 echo "</tr>";             
                   
           echo "</table>";        
 ?>   
    
    
    
<!--FIN DE LA DIVISION FILA ------------------------------------------>     
</div>

<?php
    include_once "include/footer.php" 
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
                invs[i-1] = parseFloat(invini1.data,10)  

        }

    }
// fin del if si si hay ordenes


        function cresumen(chorigen,columnt){
            
 // esta funcion recibe el id del objeto que se examina, y la columna correspondiente 
           
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
 
            labelimpor.innerHTML = addCommas(sec1.toFixed(2)) + ' pesos';
            labelcaja.innerHTML = addCommas(sec2.toFixed(0)) ;
            labelpeso.innerHTML = addCommas(sec3.toFixed(1)) + ' kg.';
            labelvol.innerHTML = addCommas(sec4.toFixed(2)) + ' m3.';
            
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
            var mireng2       = mitabla2.getElementsByTagName("tr")[7];
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
         // esta funcion calcula el peso total  de una  orden
    
        //reune los elementos de la tabla de totales
                    
        // la fila oculta de pesos
            var mireng3      = mitabla2.getElementsByTagName("tr")[3];
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
            var mireng4      = mitabla2.getElementsByTagName("tr")[5];
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
            var ordenact1 = mireng5.getElementsByTagName("td")[(col*2)+2];
            var ordenact2 = ordenact1.childNodes[0];
            var ordenact3 = parseFloat(ordenact2.data,10);
            
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
             
             var invfin = invinic-invorden
             
            return [invorden, invfin];
        
        }
     
     function addCommas(nStr)
{
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

    </script>
    
    
</body>


</html>