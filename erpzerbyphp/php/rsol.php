<?php

//version 1.0 agosto 23, 2013.

//preparacion de la pagina-------------------------------------------------------------------------------------------
//directiva de la conexion a la base de datos
include_once "config.php"; 
//directiva a la revision de conexion
include_once"lock.php";

//directiva a la clase fpdf

require_once ('fpdf.php');

//recepción de variables desde la página de llamada

$formato = $_SESSION['formato'];

//consultas sql -----------------------------------------------------------------------------------------------------
//los numeros de solicitud seleccionados
$query2 = "SELECT idconfirmaciones FROM confirmaciones WHERE status_sol = 10 " ;
$sols1= mysql_query($query2)or die ("Error en la consulta de solicitudes1.".mysql_error());



//CONSTANTES---------------------------------------------------------------------------------------------------------
    //arreglo para los upcs de los articulos
        $upc= array();
    //arreglo para las cantidades de articulos
        $cantar = array();
//VARIABLES GLOBALES----------------------------------------------------------------------------------------------------------

//definicion de constantes de formato--------------------------------------------------------------------------------
//ancho de la hoja
define('ah', 280);
//margenes
define('mr', 2.5);
//la anchura de una celda
define('acel',14);
 //altura de celda
define('hcel', 4);
//la altura de un titulo
define('htit', 5);
//el numero de renglones de una página
define('rengt', 16);


//FUENTES------------------------------------------------------------------------------------------------------------
//la ruta a las fuentes
define('FPDF_FONTPATH','/font/');

//FUENTE normal

define('nor',12);
define('peq',10);
define('mini',8);

//CLASE PDF--------------------------------------------------------------------------------------------------------------------
 class PDF extends FPDF
 
    {
        function Header(){
           global $formato;
            
             // Arial bold 15
            $this->SetFont('Arial','B',15);
            $this->Image('../img/logozerby.jpg',45,mr,15,15);
            $this->Ln(4);
            $this->Cell(280-(mr),hcel,'ZERBY ENTREGAS OPORTUNAS S.A. de C.V.',0,1,'C');
             $this->Ln(2);
            $this->Cell(280-(mr),hcel,'SOLICITUDES A ALMACEN '.$formato,0,1,'C');
            $this->Ln(4);
            
            
        }
        
        function Footer(){
            $fecha = date("d-m-Y");
            $centra = (ah/2)-(($this->GetStringWidth('fecha: '.$fecha)/2));
            $this->SetY(-10);
            $this->SetFont('Arial','B',15);
  //centrado de la fecha
            $this->Cell($centra,hcel,'',0,0,'C') ; 
            $this->Cell(35,hcel,'fecha:',0,0,'C') ; 
            $this->Cell(8,hcel,$fecha,0,1,'C');
          
        }
        
        

}
//FIN DE LA CLASE PDF-----------------------------------------------------------------------------------------------------------  

// Creación del objeto de la clase heredada
        $pdf = new PDF('L','mm','letter');
        $pdf->SetDisplayMode('fullpage','continuous');
        $pdf->SetLeftMargin(5);
        $pdf->AliasNbPages();
        $pdf->SetMargins(2,2);
        
        
        $pdf->AddPage();
        
//CONSTRUCCION DE PAGINA--------------------------------------------------------------------------------------  

//obtener articulos de las ordenes en estado de solicitar
           $arts= mysql_query("SELECT DISTINCT t1.upc, t3.desc1, t1.cad_art FROM orden_detalle as t1 
           INNER JOIN orden_resumen as t2 ON t1.orden = t2.orden
           INNER JOIN cat_arts as t3 ON t1.upc=t3.upc AND t1.cad_art=t3.cadena 
           WHERE t2.status >3 && t2.status < 7 ORDER BY t1.upc ")
            or die ("Error en la consulta de articulos solicitados.".mysql_error()); 
                
//ciclo de solicitudes por imprimir--------------------------------------------------------------------
       while ($sols2 = mysql_fetch_row($sols1)) {
       //OJO ES NECESARIO OBTENER DATOS PARA LA FORMA POR SEPARADO TODAVIA NO HAY DATOS DE CONFIRMACION
       //el numero de solicitud para poder obtener los demas datos
            $solact = $sols2[0];
       //el numero de orden para esa solicitud
           $orden1= "SELECT orden FROM confirmaciones WHERE idconfirmaciones = $solact" ;
           $orden2= mysql_query($orden1)or die ("Error en la consulta de solicitudes2.".mysql_error());
           $orden3 = mysql_fetch_row($orden2);
           $ordenf = $orden3[0];
       //el numero de tienda, de orden resumen
           $datos1 =  "SELECT no_tienda,formato_tienda,obs FROM orden_resumen WHERE orden = $ordenf";
           $datos2= mysql_query($datos1)or die ("Error en la consulta de solicitudes3.".mysql_error());
           $datos3 = mysql_fetch_row($datos2);
           $tiendaf= $datos3[0];
           $formato = $datos3[1];
           $obs= $datos3[2];
           
           //obtener datos para  la solicitud en otras tablas
           
 //EL NOBRE DEL CEDI   de cdi       
           $cedi= mysql_query("SELECT nomcorto FROM cdi WHERE no_tienda = $tiendaf")
           or die ("Error en la consulta de cedi.".mysql_error());
           $cediact1 = mysql_fetch_array($cedi);
           $cediact2 = $cediact1[0];
                     
        
     //TITULOS
        //definir coordenadas de inicio
           $yact = $pdf->GetY();
           $pdf->SetXY((ah/2)-(acel*4),$yact);
           $xinic = $pdf->GetX();
           $yinic = $pdf->GetY();
           $pdf->SetFontSize(nor);
           $pdf->Cell(acel*2,htit,"CITA:",0,0,'R');
 //en esta impresion no hay datos de cita
           $pdf->Cell(acel*2,htit,"",1,0,'C');
           $pdf->Cell(acel*2,htit,"CONFIRMA:",0,0,'R');
           $pdf->Cell(acel*2,htit,"",1,1,'C');
           $pdf->SetX($xinic);
           $pdf->Cell(acel*2,htit,"FECHA:",0,0,'R');
           $pdf->Cell(acel*2,htit,"",1,0,'C');
           $pdf->Cell(acel*2,htit,"HORA:",0,0,'R');
           $pdf->Cell(acel*2,htit,"",1,1,'C');
           $pdf->SetX($xinic);
           $pdf->Cell(acel*2,htit,"CEDI:",0,0,'R');
           $pdf->Cell(acel*2,htit,$tiendaf,1,0,'C');
           $pdf->Cell(acel*2,htit,'',0,0,'R');
           $pdf->Cell(acel*2,htit,$cediact2,1,1,'C');
           
           
       }
 //fin del ciclo de soliitudes  ------------------------------------------------------------------------------ 
 
  
         
//COLUMNA DE ARTICULOS
            
      $pdf->Ln(4);
//inicio de la linea 2
      $yl2=$pdf->GetY();
      $pdf->Ln(); 
      $pdf->SetFont('Arial','',peq);
//titulos de la columna
      $pdf->Cell(acel*2,htit,'UPC',1,0,'C');
          $pdf->Cell(acel*4.5,htit,'DESCRIPCION',1,0,'L');
          $xcol3=$pdf->GetX();
          $pdf->Ln();
          
//definicion del numero de renglones de articulo
        $rengarts = mysql_num_rows($arts);
        $r= 0;
//ciclo con renglones de articulos para todas las solicitudes-------------------------------
      while ($artact = mysql_fetch_array($arts)) {
       //nombre, descripcion del articulo
          $pdf->Cell(acel*2,htit,$artact[0],1,0,'C');
          $pdf->Cell(acel*4.5,htit,$artact[1],1,1,'L');
       //poblacion del arreglo upc
          $upc[$r]=$artact[0];
          $cad[$r]=$artact[2];
         $r++; 
      }
      
 //impresión del resto de renglones
        $resto = rengt-$rengarts;
         for ($r=0; $r < $resto; $r++) { 
            $pdf->Cell(acel*2,htit,'',1,0,'C');
            $pdf->Cell(acel*4.5,htit,'',1,1,'L');  
         }
 //linea de totales
          $pdf->Cell(acel*2,htit,'TOTALES',1,0,'C');
          $pdf->Cell(acel*4.5,htit,'',1,1,'L');
          
//linea de observaciones
          $pdf->SetFontSize(mini);
          $pdf->Cell(acel*2,hcel*3,'OBSERVACIONES',1,0,'L');
          $pdf->Cell(acel*4.5,hcel*3,'',1,1,'L');
  
          $pdf->Output();  
echo "<h1> PRUEBA 2</h1>";

//LINEA DE ORDENES -----------------------------------------------------

       
 //ciclo de solicitudes en espera de ser surtidas----------------------------------------
            $pdf->SetFont('Arial','',peq);
            $c=0;
            
            $xact= $xcol3;
            
            $pdf->SetXY($xact,$yl2);
            
            $datos= mysql_query("SELECT orden,formato_tienda,obs FROM orden_resumen WHERE num_confir = $citaact")
            or die ("Error en la consulta de citas3".mysql_error());        
  
            while ($ords = mysql_fetch_row($datos)){

                $pdf->Cell(acel*2,hcel+1,$ords[1],1,1,'C');
                $pdf->SetX($xact);
                $pdf->Cell(acel*2,hcel+1,$ords[0],1,1,'C');
                $pdf->SetX($xact);
  //celdas con cantidades de cajas
        //inicialización de total de columna
                $totc= 0;
                //seleccion de las cantidades del articulo
                
                 for ($i=0; $i<$rengarts;$i++){
                    $query3=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = $ords[0] 
                     AND upc =$upc[$i] AND cad_art = $cad[$i]")
                    or die ("Error en la consulta de cantidades confs.".mysql_error());
                    $cantact=mysql_fetch_array($query3);
                    //celdas con cantidades de articulos
                        $cantactm=number_format($cantact[0],0);
                       $pdf->Cell(acel*2,hcel+1,$cantactm,1,1,'C');
                       $pdf->SetX($xact);
                    //adición al total de columna
                        $totc += $cantact[0]; 
                 }
                 
  //impresión del resto de renglones
                    for ($r=0; $r < $resto; $r++) { 
                $pdf->Cell(acel*2,hcel+1,'',1,1,'C');
                $pdf->SetX($xact);
                    }
  //linea de totales
                $pdf->Cell(acel*2,hcel+1,$totc,1,1,'C');
                $pdf->SetX($xact);
  //linea de observaciones
                $pdf->SetFontSize(6);
                $pdf->MultiCell(acel*2,hcel*3,$ords[2],1,'L');
                $pdf->SetFontSize(peq);
  //toma la altura para la siguiente sección 
               $yl3=$pdf->GetY();           
                 $c++;
  //se coloca el cursor al inicio de la siguiente columna
                 $xact= $xact+(acel*2);
                 $pdf->SetXY($xact, $yl2);
            }
 //fin del ciclo ordenes------------------------------------------------------------- 
 
 //SECCCION DE TOTALES
 
 //consulta de totales
 
 $query3= "SELECT SUM(t1.cantidad/t1.empaque),SUM(t1.precio*t1.cantidad) FROM orden_detalle AS t1 INNER JOIN orden_resumen AS t2 
 ON t1.orden=t2.orden WHERE t2.num_confir= $citaact";
 $totales1=mysql_query($query3)or die ("Error en la consulta de citas3.".mysql_error());
 $totales = mysql_fetch_array($totales1);
 
 $querypeso= "SELECT SUM((t1.cantidad/t1.empaque)*t3.pesocaja) FROM orden_detalle AS t1 INNER JOIN orden_resumen AS t2 
 ON t1.orden=t2.orden INNER JOIN cat_arts AS t3 ON t1.upc= t3.upc AND t1.cad_art = t3.cadena WHERE t2.num_confir= $citaact";
 $peso1=mysql_query($querypeso)or die ("Error en la consulta de pesos tot.".mysql_error());
 $peso2 = mysql_fetch_array($peso1);
 
 $queryvol= "SELECT SUM((t1.cantidad/t1.empaque)*t3.volcaja) FROM orden_detalle AS t1 INNER JOIN orden_resumen AS t2 
 ON t1.orden=t2.orden INNER JOIN cat_arts AS t3 ON t1.upc= t3.upc AND t1.cad_art = t3.cadena WHERE t2.num_confir= $citaact";
 $vol1=mysql_query($queryvol)or die ("Error en la consulta de pesos tot.".mysql_error());
 $vol2 = mysql_fetch_array($vol1);
 
 
 
 $pdf->SetFont('Arial','B',nor+1);
 $stot = 'TOTALES DE LA ORDEN DE COMPRA';
 $lenstot= $pdf->GetStringWidth($stot);
 $xl3= (ah/2)-($lenstot/2);
 $pdf->SetXY($xl3,$yl3+4);
 
 $pdf->Cell($lenstot+2,hcel+1,$stot,1,1,'C');
 $pdf->SetX($xl3);
 $pdf->Cell(($lenstot+2)/2,hcel+1,'IMPORTE',1,0,'C');
  $pdf->Cell(($lenstot+2)/2,hcel+1,number_format($totales[1],2),1,1,'C'); //$totales[1]
  $pdf->SetX($xl3);
 $pdf->Cell(($lenstot+2)/2,hcel+1,'CAJAS',1,0,'C');
  $pdf->Cell(($lenstot+2)/2,hcel+1,number_format($totales[0],0),1,1,'C'); //$totales[0]
  $pdf->SetX($xl3);
 $pdf->Cell(($lenstot+2)/2,hcel+1,'PESO KG.',1,0,'C');
  $pdf->Cell(($lenstot+2)/2,hcel+1,number_format($peso2[0],1),1,1,'C');
  $pdf->SetX($xl3);
 $pdf->Cell(($lenstot+2)/2,hcel+1,'VOL. M3.',1,0,'C');
  $pdf->Cell(($lenstot+2)/2,hcel+1,number_format($vol2[0],2),1,1,'C');
 
 $pdf->Ln(20);
 

 
        
        $pdf->Output();  
//FIN DE PAGINA -----------------------------------------------------------------------------------

//cambio de estado a las ordenes impresas
   $queryacto = ("UPDATE `orden_resumen` SET `status`= 11 WHERE `status` = 10")
   or die ("Error en la consulta de actualizacion de status .".mysql_error());
   $result1= mysql_query($queryacto);
 
?>