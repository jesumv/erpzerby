
<?php

//version 1.0 Marzo 17, 2013.

//preparacion de la pagina-------------------------------------------------------------------------------------------
//directiva de la conexion a la base de datos
include_once "config.php"; 
//directiva a la revision de conexion
include_once"lock.php";

//directiva a la clase fpdf

require_once ('fpdf.php');

//consultas sql -----------------------------------------------------------------------------------------------------


//el numero de CEDIS a distribuir
$cedis= mysql_query("SELECT DISTINCT no_tienda FROM orden_resumen WHERE status = 20 ")
or die ("Error en la consulta de ordenes .".mysql_error());



//el numero de folio del documento a imprimir
      $folioc= mysql_query("SELECT valor FROM indicadores WHERE id_indicadores = 1")
      or die ("Error en la consulta de folio.".mysql_error());
      $folio=mysql_fetch_array($folioc);
      $folioa = $folio[0]; 
      
//los datos de la cita
   $cedis= mysql_query("SELECT DISTINCT no_tienda FROM orden_resumen WHERE status = 20")
   or die ("Error en la consulta de ordenes .".mysql_error()); 
   
      
//CONSTANTES---------------------------------------------------------------------------------------------------------

//el numero de columnas de ordenes
define('cord',4);

//la anchura dela hoja, mm
define('ah',280);
//la anchura de los margenes
define('mr',2.5);
//la anchura de una sección
define('ac',(ah-(mr*4))/3);

//el ancho de la columna cliente
define('act',4);


//VARIABLES GLOBALES----------------------------------------------------------------------------------------------------------
//la anchura de los encabezados
$aenc=ac*.4;

//la anchura de la segunda parte de los encabezados
$aenc2=ac*.6;

           global $yfinsup2;

//arreglo de ordenes para la obtencion de cantidades
$orda= array();

//arreglo de articulos para la obtención de cantidades
$arta=array();

//arreglo para el total de articulos por renglon
$tott=array();

$totc=array();


//definicion de constantes de formato--------------------------------------------------------------------------------


//la anchura de una celda
define('acel',$aenc2/(cord+1));

//el alto de una celda
define('hcel',4);



//FUENTES------------------------------------------------------------------------------------------------------------
//la ruta a las fuentes
define('FPDF_FONTPATH','C:/xampp/htdocs/erpzerbyphp/php/font/');


//atributos para la fuente de titulo de cliente
define('ftci',10);

//atributos de la fuente para titulo de columna
define('ft1',8);

//el tamaño de fuente comun
define('ftc',6);

//atributos de la fuente pequeña
define('ft5',5);

//atributos de la fuente para ordenes y facturas
define('fto',4);

//CLASES --------------------------------------------------------------------------------------------------------------------
    
        class PDF extends FPDF
    {
        // Cabecera de página
        function Header()
        {
            global $folioa;
    
            
             // Arial bold 15
            $this->SetFont('Arial','B',15);
            // Título
            
            // Movernos a la derecha
            $this->Cell(ah-40-(mr*2));
            $this->Cell(20,hcel*2,'FOLIO',1,0,'C');
            $this->Cell(20,hcel*2,$folioa,1,1,'C');
            
    
             $this->Cell(280-(mr),hcel,'ZERBY ENTREGAS OPORTUNAS S.A. de C.V.',0,1,'C');
             
            //fila de logos
            $this->Image('../img/laestrella.jpg',10,22,15,15);
            $this->Image('../img/soyamigo.jpg',50,22,30,15);
            $this->cell(85);
            $this->SetFont('Arial','B',ft1);
            $this->MultiCell(100, 3,"RFC. ZEO 991025 GG8\nAv. De la Manzana No. 46 Loc. 5 y 6. Col. San Miguel Xochimanga.\n
            Atizapan de Zaragoza, Edo. de Mexico. C.P. 52927\n
            Tels: 55-53-78-24-62  y 55-53-78-24-63",0,'C');
            $this->Image('../img/patyleta.jpg',200,22,15,15);
            $this->Image('../img/confe.png',250,22,15,15);
      
            // Salto de línea
            $this->Ln(9);
        
        }
        
        // Pie de página
        function Footer()
        {
           
     // Arial italic 8
            $this->SetFont('Arial','I',ftc);
    // Posición: a 1,5 cm del final
            $this->SetY(-20);
            $this->SetX(ac+35);
            $this->ln(2);
            $this->Cell(5);
            $this->Cell(10,5,'',1,0,'C');
            $this->Cell(25,5,'HOJA DE RUTA',0,0,'L');
            $this->Cell(10,5,'',1,0,'C');
            $this->Cell(32,5,'HOJA DE ENTRADA A CEDIS',0,0,'R');
            $this->Cell(32);
            $this->Cell(20,5,'CHOFER',0,0,'R');
            $this->Cell(40,5,'','B',0,'R');
            $this->Cell(32);
            $this->Cell(20,5,'FIRMA DE RECIBIDO',0,0,'R');
            $this->Cell(40,5,'','B',1,'R');
           
            // Número de página
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }



 
 
//CONSTRUCCION DE PAGINA--------------------------------------------------------------------------------------
// Creación del objeto de la clase heredada
        $pdf = new PDF('L','mm','letter');
        $pdf->SetDisplayMode('fullpage','continuous');
        $pdf->SetLeftMargin(5);
        $pdf->AliasNbPages();
        $pdf->SetMargins(2,2);
        
        
//ciclo de página--------------------------------------------------------------------------------------------

while($cedi=mysql_fetch_array($cedis)){
    
    //el total de kilos por cedi
    $totkil = 0;

    //el total de m3 por cedi
    $totmet = 0; 
    
   //CALCULO DE TOTALES 
    
    //el total de cajas por cedi
$totcaj1= mysql_query("SELECT SUM(t1.cantidad/t1.empaque) FROM orden_detalle AS t1 INNER JOIN orden_resumen AS t2 ON t1.orden=t2.orden
WHERE t2.no_tienda = $cedi[0] AND t2.status = 20")
or die ("Error en la consulta de totales.".mysql_error());
$totcaj2= mysql_fetch_array($totcaj1);
$totcaj= number_format($totcaj2[0],0);

//los upcs de la orden
$totcant1= mysql_query("SELECT t1.upc,(t1.cantidad/t1.empaque),t1.cad_art FROM orden_detalle AS t1 
INNER JOIN orden_resumen AS t2 ON t1.orden= t2.orden WHERE t2.no_tienda= $cedi[0] AND t2.status = 20")
or die ("Error en la consulta de cantidades totales.".mysql_error());


    while ($totcant2 = mysql_fetch_array($totcant1)) {
            $totcant3 = mysql_query("SELECT pesocaja, volcaja FROM cat_arts WHERE upc = $totcant2[0] AND cadena=$totcant2[2]")
            or die ("Error en la consulta de peso y volumen.".mysql_error());
            $totcant4 = mysql_fetch_array($totcant3);
            $totkil =$totkil+number_format(($totcant2[1]*$totcant4[0]),1);
            $totmet = $totmet + number_format(($totcant2[1]*$totcant4[1]),2);
    }


    
   
$pdf->AddPage();

  
//FILA 1 -----------------------------------------------------------------------------------------------------> 

//numero de renglones de la columna
$reng= 30;
//coordenadas de inicio de la columna
$xinic=$pdf->GetX();
$yinici=$pdf->GetY();
     
    //TITULOS ------------------------------------------------
         $pdf->SetFont('Arial','',ft1);
         $pdf->SetFillColor(144,144,144);
    //la y de inicio de las celdas de encabezado
        $yinic=$pdf->GetY();
        $xinic=$pdf->GetX();
    //consulta de datos encabezado
        $datoenc= mysql_query("SELECT fecha_ent FROM orden_resumen  WHERE status = 20 AND no_tienda = $cedi[0] ")
        or die ("Error en la consulta de ordenes .".mysql_error());
        $datoenc1= mysql_fetch_row($datoenc);
        
        $pdf->Cell($aenc,hcel,'ENTREGA EN CEDIS DE:',1,0,'C',1);
        $pdf->Cell($aenc2,hcel,$cedi[0],1,1,'C');
        $pdf->Cell($aenc,hcel,'Fecha de entrega:',1,0,'C');
        $pdf->Cell($aenc2,hcel,$datoenc1[0],1,1,'C');
        $y = $pdf->GetY();
        $xc1=$pdf->getX();
        $pdf->SetFont('Arial','',ft5);
        $pdf->MultiCell($aenc,hcel*2,'Ordenes de Compra y no. de factura',1,'C');
        $xc1=$xc1+$aenc;
        $pdf->setXY($xc1,$y);
        $pdf->SetFont('Arial','',fto);

    //ciclo con las ordenes por CEDI-------------------------------------------------------------------------------------------
        //consulta de ordenes
          $ords1=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 2 
          AND status = 20")or die ("Error en la consulta de ordenes .".mysql_error()); 
          
          $nords = mysql_num_rows($ords1);
          
          switch($nords){
              
case 0:
    
    for($c=0;$c<cord;$c++){
        
        $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
    }

break;
                  
default : 

// ordenes
        while($ords2= mysql_fetch_array($ords1)){
            //creacion del arreglo de ordenes para la obtencion de totales
                   
            $orda[]=$ords2[0];
      
           $pdf->MultiCell(acel,hcel,$ords2[0]."\n".$ords2[1],1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
           
        }
        //columnas en blanco
 $blanco = cord-$nords;
        for($c=0;$c<$blanco;$c++){
            $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
        }

break;            
              
          }
//fin del ciclo de ordenes-------------------------------------------------------------------------------          
        
   //total
   $pdf->SetFont('Arial','B',ftc);
   $pdf->Cell(acel,hcel*2,'TOTAL',1,1,'C');
   
   //regresar a x origen de la columna

//columna cliente------------------------------------------------------------------------------------------
$pdf->SetFont('Arial','B',ftc);    
        $pdf->MultiCell(act,12.5,'LA ESTRELLA',1,'C');
//regresar a coordenadas de origen
        $pdf->setXY($xinic+act,$yinic+(hcel*4));
        
   
//columna de articulos--------------------------------------------------------------------------------------
    $artsc= mysql_query("SELECT t1.upc, t1.desc1 from cat_arts AS t1 INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc 
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 2 AND t3.status = 20
    ORDER BY t1.upc")
    or die ("Error en la consulta de articulos .".mysql_error());
    $narts=mysql_num_rows($artsc);
    
//seleccionar caso dependiendo del numero de articulos 

switch($narts){
    
    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-4,hcel,'',1,1);
            $pdf->SetX($xinic+act);
        }
        
    break;    
    default: 
        
        while($arts=mysql_fetch_array($artsc)){
    //creacion de arreglo de upcs para obtención de cantidades  
      $arta[]=$arts[0];
      
        $pdf->SetFont('Arial','B',ftc);
        $pdf->Cell($aenc-4,hcel,$arts[1],1,1);
        
 //regreso a x de origen
        $pdf->SetX($xinic+act);
    }
        
 //renglones en blanco
 
 $blanco = $reng-$narts;
        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-4,hcel,'',1,1);
            $pdf->SetX($xinic+act);
        }
    
    break;
} 

  $pdf->SetFont('Arial','B',ftc);

    $pdf->Cell($aenc-4,5,'TOTAL DE CAJAS POR O.C',1,0); 
    
//sección de cantidades de artículos--------------------------------------------------------------------------------------------------------------
//el cursor a la y inicial
    $pdf->SetXY($xinic+$aenc, $yinic+(hcel*4));
    
    //seleccion de caso dependiendo del numero de ordenes-------------------------------------------------------
    switch ($nords) {
        case 0:
            for($c=0;$c<cord;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
                // imprime la celda de totales
                     $pdf->Cell(acel,5,'',1,1,'C');
                
                //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $yinic+(hcel*4));
            }
            
            //impresion de la columna de totales
                   $xmov=$pdf->GetX(); 
                for($r=0;$r<$reng;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C');
                    $pdf->SetX($xmov);
                }
                    $pdf->Cell(acel,5,'',1,1,'C');
            break; //fin del caso no hay ordenes-----------
        
        default:
            
                //ciclo de columnas con los numero de orden---------------------------------------------------------------------
            for($c = 0;$c<$nords;$c++){
                
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                        
                   
                //ciclo de renglones con el upc del articulo--------------------------------------------------------------------
                
                

   //se imprimen los renglones con las cantidades de articulos------------------------------
   

                    for ($r=0;$r<$narts;$r++){
                                
                                    $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$orda[$c]'
                                AND upc = $arta[$r]")or die ("Error en la consulta de articulos .".mysql_error());
                                
                                $canta= mysql_fetch_array($cant);
                       //adicion a los totales
                            //renglon
                                $tott[$r][$c]=$canta[0];                            
                            
                            //columna
                                $totc [$r][$c]= $canta[0];
                                
                                $pdf->Cell(acel,hcel,number_format($canta[0],0),1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                              
                            }    
                            
                      //renglones en blanco
                   for($r=0;$r<$blanco;$r++){
                       
                       $pdf->Cell(acel,hcel,'',1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                   }
                    //toma las coordenadas para el renglon de totales
                        $xtot= $pdf->GetX();
                        $ytot=$pdf->GetY();
                        
                    //imprime la celda del total por columna
                    //seleccion del total por columna
                    $stot1=mysql_query("SELECT SUM(cantidad/empaque) FROM orden_detalle WHERE 
                    orden = '$orda[$c]'")or die ("Error en la consulta de articulos totales .".mysql_error());
                    $tot1 = mysql_fetch_array($stot1);
                    
                  
                        $pdf->Cell(acel,5,number_format($tot1[0],0),1,0,'C');
                        
                     //vuelve al inicio de la columna,una columna más a la derecha
                        
                       $pdf->SetXY($xmov+acel, $yinic+(hcel*4));
                        
                        
                                            
                  
                    
        } // fin del for columnas por numero de orden-------------------------------------------------
        
        
       
         //regresa al inicio de la tabla
                     $pdf->SetXY($xmov+acel, $yinic+(hcel*4));
                     
          //imprime las columnas restantes en blanco
                    $resto = 4-$nords;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                        }
                        //imprime la celda para el total de la columna
                         $pdf->Cell(acel,5,'',1,0,'C');
                       //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $yinic+(hcel*4));
                    
                    }
          //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $xmov=$pdf->GetX();
                $totf=0;
                for($r=0;$r<$narts;$r++){
                        $tota= array_sum($tott[$r]);
                        $totf=$totf+$tota;
                        
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
            
                    $pdf->Cell(acel,5,$totf,1,1,'C'); 
        break;
        
                    
                
        }//fin de switch numero de ordenes-----------------------------------------------------------------------
        
        
                 $pdf->SetX($xinic+act+($aenc-4));
//FIN DE LA SECCION----------------------------------------------------------------------------------------------------------------------------------------


//COLUMNA 2 parte patyleta------------------------------------------------------------------------------------------------------------------------------
//SECCION bodega--------------------------------------------------
//numero de renglones de la columna
$reng= 10;
//coordenadas de inicio de la columna
$xinic=mr+ac+mr;
$yinic =hcel*4;

//inicialización de variables 
//para las ordenes
  $orda=array();
 //inicialización del arreglo de upcs para cantidades
  $artab= array();
//inicialización de los totales por columna
$totc=array();

//TITULOS ------------------------------------------------
         $pdf->SetFont('Arial','',ft1);
         $pdf->SetFillColor(144,144,144);
    //la y de inicio de las celdas de encabezado
        $pdf->SetXY($xinic, $yinici);
    //consulta de datos encabezado
        $datoenc= mysql_query("SELECT fecha_ent FROM orden_resumen  WHERE status = 20 AND no_tienda = $cedi[0] ")
        or die ("Error en la consulta de ordenes .".mysql_error());
        $datoenc1= mysql_fetch_row($datoenc);
        
        $pdf->Cell($aenc,hcel,'ENTREGA EN CEDIS DE:',1,0,'C',1);
        $pdf->Cell($aenc2,hcel,$cedi[0],1,1,'C');
        $pdf->SetX($xinic);
        $pdf->Cell($aenc,hcel,'Fecha de entrega:',1,0,'C');
        $pdf->Cell($aenc2,hcel,$datoenc1[0],1,1,'C');
        $y = $pdf->GetY();
        $pdf->SetX($xinic);
        $xc1=$pdf->getX();
        $pdf->SetFont('Arial','',ft5);
        $pdf->MultiCell($aenc,hcel*2,'Ordenes de Compra y no. de factura',1,'C');
        $xc1=$xc1+$aenc;
        $pdf->setXY($xc1,$y);
        $pdf->SetFont('Arial','',fto);
        
 //ciclo con las ordenes por CEDI-------------------------------------------------------------------------------------------
        //consulta de ordenes
        
          $ords=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 3
          AND status = 20")or die ("Error en la consulta de ordenes .".mysql_error()); 
            
          $ordsb=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 3
          AND status = 20 AND formato_tienda='BODEGA'")or die ("Error en la consulta de ordenes .".mysql_error());
           
          $ordss=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 3
          AND status = 20 AND formato_tienda='SUPERAMA'")or die ("Error en la consulta de ordenes .".mysql_error()); 
          
          
          $nords= mysql_num_rows($ords);
          $nordsb = mysql_num_rows($ordsb);
          $nordss=  mysql_num_rows($ordss);

    //selección de caso por numero de ordenes existentes      
          switch($nords){
              
case 0:
    
    for($c=0;$c<cord;$c++){
        
        $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
    }

break;
                  
default : 

// ordenes
        while($ords2= mysql_fetch_array($ords)){
            
            //creacion del arreglo de ordenes para la obtencion de totales
            $orda[]=$ords2[0];
      
           $pdf->MultiCell(acel,hcel,$ords2[0]."\n".$ords2[1],1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
        }
        
        //columnas en blanco
        $blanco = cord-$nords;
        for($c=0;$c<$blanco;$c++){
            $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
        }

break;            
              
          }
//fin del ciclo de ordenes-------------------------------------------------------------------------------          
        
//total
   $pdf->SetFont('Arial','B',ftc);
   $pdf->Cell(acel,hcel*2,'TOTAL',1,1,'C');
   
   //regresar a x origen de la columna y tomar la y para el inicio de las demás columnas
   $y=$pdf->GetY();
   $pdf->SetX($xinic);     

//columna cliente------------------------------------------------------------------------------------------
$pdf->SetFont('Arial','B',ftc);    
        $pdf->MultiCell(act,8.65,'PATYLETA',1,'C');
        $pdf->SetXY($xinic+act,$y);
        $pdf->MultiCell(act,6.7,'BODEGA',1,'C');
        $pdf->SetX($xinic+act);
        $pdf->MultiCell(act,4.1,'SUPER WM',1,'C');
 
$pdf->setXY($xinic+(act*2),$y); 
        
//columna de articulos---------------------------------------------------------------------------------------------------------------------------

//SECCION BODEGA-------------------------------------------------------------------------------

    $artscb= mysql_query("SELECT DISTINCT t1.upc, t1.desc1 from cat_arts AS t1 
    INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc AND t1.cadena = t2.cad_art
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 3
    AND t3.formato_tienda= 'BODEGA' AND t3.status = 20 ORDER BY t1.upc")
    or die ("Error en la consulta de articulos .".mysql_error());
    $nartsb=mysql_num_rows($artscb);
    
   

    
//seleccionar caso dependiendo del numero de articulos 

switch($nartsb){
    
    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
        
    break; 
       
    default: 
        
        while($arts=mysql_fetch_array($artscb)){
    //creacion de arreglo de upcs para obtención de cantidades  
        $artab[]=$arts[0];
     
      
        $pdf->SetFont('Arial','B',ftc);
        $pdf->Cell($aenc-8,hcel,$arts[1],1,1);
        
 //regreso a x de origen
        $pdf->SetX($xinic+(act*2));
    }
        
 //renglones en blanco
 
 $blanco = $reng-$nartsb;

        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
    
    break;
} 

//SECCION SUPERAMA----------------------------------------------------------------------------------------

$reng = 6;
$artscs= mysql_query("SELECT DISTINCT t1.upc, t1.desc1 from cat_arts AS t1 
    INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc AND t1.cadena=t2.cad_art
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 3
    AND t3.formato_tienda= 'SUPERAMA' AND t3.status = 20 ORDER BY t1.upc")
    or die ("Error en la consulta de articulos .".mysql_error());
    $nartss=mysql_num_rows($artscs);

//seleccionar caso dependiendo del numero de articulos 

switch($nartss){

    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
        $pdf->SetFont('Arial','B',ftc);
        

    $pdf->Cell($aenc-8,5,'TOTAL CAJAS POR O.C',1,0); 
    break; 
       
    default: 
        
        while($arts=mysql_fetch_array($artscs)){
    //creacion de arreglo de upcs para obtención de cantidades 
      $artas[]=$arts[0];
        $pdf->SetFont('Arial','B',ftc);
        $pdf->Cell($aenc-8,hcel,$arts[1],1,1);
        
 //regreso a x de origen
        $pdf->SetX($xinic+(act*2));
    }
        
 //renglones en blanco

 
 $blanco = $reng-$nartss;
 
        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
    $pdf->SetFont('Arial','B',ftc);
    $pdf->SetX($xinic+(act*2));
    $pdf->Cell($aenc-8,5,'TOTAL CAJAS POR O.C',1,0); 
    break;
}


//sección de CANTIDADES DE ARTICULOS--------------------------------------------------------------------------------------------------------------

//seccion BODEGA ------------------------------------------------------------------------------------------------

$reng = 10;
//el cursor a las x y y inicial
    
     $pdf->setXY($xinic+(act*2)+($aenc-8),$y); 
     $xmov=$pdf->GetX();
     $y=$pdf->GetY();
     
//seleccion de caso dependiendo del numero de ordenes-------------------------------------------------------
   
    switch ($nordsb) {
        case 0:
            $totf=0;
            for($c=0;$c<4;$c++){
 
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
                
                //NO HAY RENGLON DE TOTALES.
                
                //vuelve al inicio de la columna,una columna más a la derecha
                       // $pdf->SetXY($xmov+acel, $yinic+(hcel*4));
                        $pdf->SetXY($xmov+acel, $y);
            }
            
            //impresion de la columna de totales
                   $xmov=$pdf->GetX(); 
                for($r=0;$r<$reng;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C');
                    $pdf->SetX($xmov);
                }
                //NO HAY CELDA DE TOTAL
                    //$pdf->Cell(acel,5,'',1,1,'C');
            break; //fin del caso no hay ordenes-----------
            
default:

                //ciclo de columnas con los numero de orden---------------------------------------------------------------------     
            for($c = 0;$c<$nordsb;$c++){
                
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                        
                   
   //ciclo de renglones con el upc del articulo--------------------------------------------------------------------

            //se imprimen los renglones con las cantidades de articulos------------------------------
                    //inicialización del total por columna

                    for ($r=0;$r<$nartsb;$r++){
                                    $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$orda[$c]'
                                AND upc = $artab[$r] ORDER BY upc")or die ("Error en la consulta de articulos .".mysql_error());
                                
                                $cantab= mysql_fetch_array($cant);
                                
                                
                       //adicion a los totales
                            //renglon
                                $tott[$r][$c]=$cantab[0];                            
                            
                                
                                $pdf->Cell(acel,hcel,number_format($cantab[0],0),1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                              
                            }    
     
                      //renglones en blanco

                   $blanco = $reng-$nartsb;
                   
                   for($r=0;$r<$blanco;$r++){
                       
                       $pdf->Cell(acel,hcel,'',1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                   }
                        
                    //imprime la celda del total por columna
                    //no hay total por columna
                        //$pdf->Cell(acel,5,$totc,1,0,'C');
                        
                     //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
                        
                                            
                  
                    
        } // fin del for columnas por numero de orden-------------------------------------------------
        
                    
          //imprime las columnas restantes en blanco
                    $resto = 4-$nordsb;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                        }
                            
                        //imprime la celda para el total de la columna
                        //no hay total de la columna
                        // $pdf->Cell(acel,5,'',1,0,'C');
                       //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
                    
                    }
          //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $xmov=$pdf->GetX();
                $totf=0;
                for($r=0;$r<$nartsb;$r++){
                        $tota= array_sum($tott[$r]);
                        $totf=$totf+$tota;
                        
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                //toma las coordenadas para la seccion superama
                $xtot=$pdf->GetX();
                $ytot=$pdf->GetY();
                    // no hay total de columna
                    //$pdf->Cell(acel,5,$totf,1,1,'C'); 
break;
        
                    
                
        }//fin de switch numero de ordenes-----------------------------------------------------------------------
            
  
//sección SUPERAMA --------------------------------------------------------------------------------------


$reng = 6;
    //el cursor a las x y y inicial
    
     $pdf->setX($xinic+(act*2)+($aenc-8)); 
     $xmov=$pdf->GetX();
     $y=$pdf->GetY();
     
    //seleccion de caso dependiendo del numero de ordenes totales-------------------------------------------------------
    switch ($nords) {
case 0:
     //primero imprime celdas en blanco, con una columna de más para los totales
            for($c=0;$c<cord+1;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
               
                
          //un reglon más para totales porque tiene más altura
                     $pdf->Cell(acel,hcel+1,'',1,1,'C');
                      $yfinsup2 = $pdf->GetY();
         //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
  
            }
        $pdf->SetY($yfinsup2);
            
        break;    //FIN DEL CASO NO HAY ORDENES TOTALES
            
default:
        //selección de caso si hay ordenes superama

            switch ($nordss) {
                //no hay ordenes superama
                case 0:
                 //imprime renglones y columnas en blanco
                     for($c=0;$c<cord+1;$c++){
                        //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                        for($r=0;$r<$reng;$r++){
                            
                             $pdf->Cell(acel,hcel,'',1,1,'C');
                            //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);
                        } 
                            
                          $yfinsup2 = $pdf->GetY();    
                        //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);         
                     }
                    
                        
                    break;//fin del caso no hay superama-------------------------------------
                    
 default://si hay ordenes superama
                    //ciclo de columnas con los numero de orden---------------------------------------------------------------------
                
            for($c = 0;$c<$nords;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
   //ciclo de renglones con el upc del articulo--------------------------------------------------------------------

    //se imprimen los renglones con las cantidades de articulos------------------------------
        


                    for ($r=0;$r<$nartss;$r++){
  
                                    $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$orda[$c]'
                                AND upc = $artas[$r] ORDER BY upc")or die ("Error en la consulta de articulos superama .".mysql_error());
                                
                                $cantas= mysql_fetch_array($cant);
                                
                       //adicion a los totales
                            //renglon
                                $tott[$r][$c]=$cantas[0];                            
                      //escribe la celda de la cantidad          
                                $pdf->Cell(acel,hcel,number_format($cantas[0],0),1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                              
                            }    
     
                      //renglones en blanco
                   $blanco = $reng-$nartss;
                   
                   for($r=0;$r<$blanco;$r++){
                       
                       $pdf->Cell(acel,hcel,'',1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                   }
                        
                     //vuelve al inicio de la columna,una columna más a la derecha
                        $yfinsup2 = $pdf->GetY(); 
                        $pdf->SetXY($xmov+acel, $y);
                                                                   
                    
        } // fin del for columnas por numero de orden-------------------------------------------------
        
                    
          //imprime las columnas restantes en blanco
                    $resto = cord-$nords;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                        }
                    $xmov = $xmov+acel;
                    $pdf->SetXY($xmov,$y); 
                    }
          //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $xmov=$pdf->GetX();
                
                for($r=0;$r<$nartss;$r++){
                        $tota= array_sum($tott[$r]);
                        
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                    
     
                    break;//fin del caso si hay superama
     
           }//fin del selector numero de ordenes superama
            
        break; //fin del caso si hay ordenes                  
     
    }
//SECCION TOTALES ---------------------------------------------------------------------------------

$pdf->setXY($xinic+(act*2)+($aenc-8),$yfinsup2);
    if ($nords!=0) {
         $totf2=0;
                    for ($c=0; $c < $nords; $c++) {
                         $stot2=mysql_query("SELECT SUM(cantidad/empaque) FROM orden_detalle WHERE 
                        orden = '$orda[$c]'") or die ("Error en la consulta de articulos totales .".mysql_error());
                        $tot2 = mysql_fetch_array($stot2);  
                        $totf2+=$tot2[0];
                         
                        $pdf->Cell(acel,hcel+1,number_format($tot2[0],0),1,0,'C');
                    }
                    //imprime celdas en blanco
                    for ($cr=0; $cr <(cord-$nords) ; $cr++) { 
                        $pdf->Cell(acel,hcel+1,'',1,0,'C');
                    }
                     //imprime la celda del total final
                        $pdf->Cell(acel,hcel+1,number_format($totf2,0),1,1,'C');
    }
                       
                        
//espacio entre secciones
$pdf->Ln(4);


//SECCION ZERBY------------------------------------------------------------------------------------------------

//variable numero de renglones
    $reng = 1;
    // volver al inicio de la columna
    $pdf->Setx($xinic);
    $yzerby = $pdf->GetY();
    //columna cliente
    $pdf->SetFont('Arial','B',ftc);    
      $pdf->MultiCell(act,2.4,'ZERBY',1,'C');
    $pdf->Setxy($xinic+act,$yzerby); 
    //titulos
    $pdf->SetFont('Arial','',ft5);
        $pdf->MultiCell($aenc-act,hcel*2,'Ordenes de Compra y no. de factura',1,'C');
  //avanzar x   
    $xc1=$xinic+$aenc;
        $pdf->setXY($xc1,$yzerby);
        $pdf->SetFont('Arial','',fto);
        
 //ciclo con las ordenes por CEDI-------------------------------------------------------------------------------------------
        //consulta de ordenes
    $ords=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 4
          AND status = 20")or die ("Error en la consulta de ordenes .".mysql_error());     
    $nords= mysql_num_rows($ords);
  //selección de caso por numero de ordenes existentes  

switch($nords){
    case 0:
    
    for($c=0;$c<cord;$c++){
        
        $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$yzerby); 
    }
break;
                  
default : 
// ordenes
    //inicializacion del arreglo de ordenes
        $ordaz = array();
        while($ords2= mysql_fetch_array($ords)){
            //creacion del arreglo de ordenes para la obtencion de totales
            $ordaz[]=$ords2[0];
      
           $pdf->MultiCell(acel,hcel,$ords2[0]."\n".$ords2[1],1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$yzerby); 
        }
        
        //columnas en blanco
        $blanco = cord-$nords;
        for($c=0;$c<$blanco;$c++){
            $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$yzerby); 
        }

break;            
      
}

//fin del ciclo de ordenes-------------------------------------------------------------------------------          
        
//total
   $pdf->SetFont('Arial','B',ftc);
   $pdf->Cell(acel,hcel*2,'TOTAL',1,1,'C');
   
   //regresar a x origen de la columna y tomar la y para el inicio de las demás columnas
   $y=$pdf->GetY();
   $pdf->SetX($xinic+act);     
//COLUMNA DE ARTICULOS ------------------------------------------------------------------------------------
$artscz= mysql_query("SELECT t1.upc, t1.desc1 from cat_arts AS t1 INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc 
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 4
    AND t3.status = 20 ORDER BY t1.upc")or die ("Error en la consulta de articulos .".mysql_error());
    $nartsz=mysql_num_rows($artscz);
    
//seleccionar caso dependiendo del numero de articulos 
switch($nartsz){
    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-4,hcel,'',1,1);
            $pdf->SetX($xinic+(act));
        }
        
    break; 
    default:
    //inicialización de la variable para numero de articulo
    $artaz=array();
        while($arts=mysql_fetch_array($artscz)){
    //creacion de arreglo de upcs para obtención de cantidades  
            
      $artaz[]= $arts[0];
      
        $pdf->SetFont('Arial','B',ftc);
        $pdf->Cell($aenc-4,hcel,$arts[1],1,1);
       
 //regreso a x de origen
        $pdf->SetX($xinic+(act));
    }
 //renglones en blanco
 
 $blanco = $reng-$nartsz;

        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-4,hcel,'',1,1);
            $pdf->SetX($xinic+(act));
        }       
        
    break;
}

//sección de cantidades de artículos--------------------------------------------------------------------------------------------------------------
//el cursor a la y inicial
    $pdf->SetXY($xinic+$aenc, $y);
//seleccion de caso dependiendo del numero de ordenes-------------------------------------------------------
    switch ($nords) {
        
        case 0:
            for($c=0;$c<cord;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
                //no hay totales
                
                // imprime la celda de totales
                     //$pdf->Cell(acel,5,'',1,1,'C');
                
                //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
            }
    //impresion de la columna de totales
                   $xmov=$pdf->GetX(); 
                for($r=0;$r<$reng;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C');
                    $pdf->SetX($xmov);
                }
                    //no hay totales
                    //$pdf->Cell(acel,5,'',1,1,'C');
            break; //fin del caso no hay ordenes-----------   
            
 default:
                //ciclo de columnas con los numero de orden---------------------------------------------------------------------
            for($c = 0;$c<$nords;$c++){
                
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                        
                   
                //ciclo de renglones con el upc del articulo--------------------------------------------------------------------
                
                

   //se imprimen los renglones con las cantidades de articulos------------------------------
   

                    for ($r=0;$r<$nartsz;$r++){

                                  
                                    $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$ordaz[$c]'
                                AND upc = $artaz[$r]")or die ("Error en la consulta de articulos .".mysql_error());
                                
                                $canta= mysql_fetch_array($cant);
      

                       //adicion a los totales
                            //renglon

                                $tottz[$r][$c]=$canta[0];  
                                                                      
                                $pdf->Cell(acel,hcel,number_format($canta[0],0),1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                              
                            }    
                            
                      //renglones en blanco
                   for($r=0;$r<$blanco;$r++){
                       
                       $pdf->Cell(acel,hcel,'',1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                   }
                    //toma las coordenadas para el renglon de totales
                        $xtot= $pdf->GetX();
                        $ytot=$pdf->GetY();
                        
                        
                     //vuelve al inicio de la columna,una columna más a la derecha
                        
                       $pdf->SetXY($xmov+acel, $y);
                                                                  
                  
                    
        } // fin del for columnas por numero de orden-------------------------------------------------
        
        
       
         //regresa al inicio de la tabla
                     $pdf->SetXY($xmov+acel, $y);
                     
          //imprime las columnas restantes en blanco
                    $resto = 4-$nords;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                        }
                        
                       //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
                    
                    }
          //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $xmov=$pdf->GetX();
                $totf=0;
               for($r=0;$r<$nartsz;$r++){
                        $tota= array_sum($tottz[$r]);
                        $totf=$totf+$tota;
                        
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                   $pdf->SetX($xmov);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
            
        break;
        
                    
                
        }//fin de switch numero de ordenes-----------------------------------------------------------------------
        
        
                 $pdf->SetX($xinic+act+($aenc-4));
            
        
   
//salto de linea
$pdf->Ln(4);

//SECCION MEZCAL-----------------------------------------------------------------------------------------------


//variable numero de renglones
    $reng = 1;
    // volver al inicio de la columna
    $pdf->Setx($xinic);
    $ymezcal = $pdf->GetY();
    //columna cliente
    $pdf->SetFont('Arial','B',ftc);    
      $pdf->MultiCell(act,2,'MEZCAL',1,'C');
    $pdf->Setxy($xinic+act,$ymezcal); 
    //titulos
    $pdf->SetFont('Arial','',ft5);
        $pdf->MultiCell($aenc-act,hcel*2,'Ordenes de Compra y no. de factura',1,'C');
  //avanzar x   
    $xc1=$xinic+$aenc;
        $pdf->setXY($xc1,$ymezcal);
        $pdf->SetFont('Arial','',fto);
        
 //ciclo con las ordenes por CEDI-------------------------------------------------------------------------------------------
        //consulta de ordenes
    $ords=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 5
          AND status = 20")or die ("Error en la consulta de ordenes .".mysql_error());     
    $nords= mysql_num_rows($ords);
  //selección de caso por numero de ordenes existentes  

switch($nords){
    case 0:
    
    for($c=0;$c<cord;$c++){
        
        $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$ymezcal); 
    }
break;
                  
default : 
// ordenes
    //inicializacion del arreglo de ordenes
        $ordam = array();
        while($ords2= mysql_fetch_array($ords)){
            //creacion del arreglo de ordenes para la obtencion de totales
            $ordam[]=$ords2[0];
      
           $pdf->MultiCell(acel,hcel,$ords2[0]."\n".$ords2[1],1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$ymezcal); 
        }
        
        //columnas en blanco
        $blanco = cord-$nords;
        for($c=0;$c<$blanco;$c++){
            $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$ymezcal); 
        }

break;            
      
}

//fin del ciclo de ordenes-------------------------------------------------------------------------------          
        
//total
   $pdf->SetFont('Arial','B',ftc);
   $pdf->Cell(acel,hcel*2,'TOTAL',1,1,'C');
   
   //regresar a x origen de la columna y tomar la y para el inicio de las demás columnas
   $y=$pdf->GetY();
   $pdf->SetX($xinic+act);     
//COLUMNA DE ARTICULOS ------------------------------------------------------------------------------------
$artscm= mysql_query("SELECT t1.upc, t1.desc1 from cat_arts AS t1 INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc 
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 5
    AND t3.status = 20 ORDER BY t1.upc")or die ("Error en la consulta de articulos .".mysql_error());
    $nartsm=mysql_num_rows($artscm);
    
//seleccionar caso dependiendo del numero de articulos 
switch($nartsm){
    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-4,hcel,'',1,1);
            $pdf->SetX($xinic+(act));
        }
        
    break; 
    default:
    //inicialización de la variable para numero de articulo
    $artaz=array();
        while($arts=mysql_fetch_array($artscm)){
    //creacion de arreglo de upcs para obtención de cantidades  
            
      $artam[]= $arts[0];
      
        $pdf->SetFont('Arial','B',ftc);
        $pdf->Cell($aenc-4,hcel,$arts[1],1,1);
       
 //regreso a x de origen
        $pdf->SetX($xinic+(act));
    }
 //renglones en blanco
 
 $blanco = $reng-$nartsm;

        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-4,hcel,'',1,1);
            $pdf->SetX($xinic+(act));
        }       
        
    break;
}

//sección de cantidades de artículos--------------------------------------------------------------------------------------------------------------
//el cursor a la y inicial
    $pdf->SetXY($xinic+$aenc, $y);
//seleccion de caso dependiendo del numero de ordenes-------------------------------------------------------
    switch ($nords) {
        
 case 0:
            for($c=0;$c<cord;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
                //no hay totales
                
                // imprime la celda de totales
                     //$pdf->Cell(acel,5,'',1,1,'C');
                
                //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
            }
    //impresion de la columna de totales
                   $xmov=$pdf->GetX(); 
                for($r=0;$r<$reng;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C');
                    $pdf->SetX($xmov);
                }
                    //no hay totales
                    //$pdf->Cell(acel,5,'',1,1,'C');
            break; //fin del caso no hay ordenes-----------   
            
 default:
                //ciclo de columnas con los numero de orden---------------------------------------------------------------------
            for($c = 0;$c<$nords;$c++){
                
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                        
                   
                //ciclo de renglones con el upc del articulo--------------------------------------------------------------------
                
                

   //se imprimen los renglones con las cantidades de articulos------------------------------
   

                    for ($r=0;$r<$nartsm;$r++){

                                  
                                    $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$ordam[$c]'
                                AND upc = $artam[$r]")or die ("Error en la consulta de articulos .".mysql_error());
                                
                                $canta= mysql_fetch_array($cant);
      

                       //adicion a los totales
                            //renglon

                                $tottm[$r][$c]=$canta[0];  
                                                                      
                                $pdf->Cell(acel,hcel,number_format($canta[0],0),1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                              
                            }    
                            
                      //renglones en blanco
                   for($r=0;$r<$blanco;$r++){
                       
                       $pdf->Cell(acel,hcel,'',1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                   }
                    //toma las coordenadas para el renglon de totales
                        $xtot= $pdf->GetX();
                        $ytot=$pdf->GetY();
                        
                        
                     //vuelve al inicio de la columna,una columna más a la derecha
                        
                       $pdf->SetXY($xmov+acel, $y);
                        
                                
                    
        } // fin del for columnas por numero de orden-------------------------------------------------
        
        
       
         //regresa al inicio de la tabla
                     $pdf->SetXY($xmov+acel, $y);
                     
          //imprime las columnas restantes en blanco
                    $resto = 4-$nords;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                        }
                        
                       //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
                    
                    }
          //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $xmov=$pdf->GetX();
                $totf=0;
               for($r=0;$r<$nartsm;$r++){
                        $tota= array_sum($tottm[$r]);
                        $totf=$totf+$tota;
                        
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                   $pdf->SetX($xmov);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
            
        break;
        
                    
                
        }//fin de switch numero de ordenes-----------------------------------------------------------------------
        
//SECCION DE TOTALES DE LA HOJA
$pdf->SetFontSize(ftci);
         $pdf->Ln(6);
         $pdf->SetX($xinic); 
         $pdf->Cell($aenc,hcel+1,'TOTAL DE CAJAS',1,0,'C'); 
         $pdf->Cell($aenc2,hcel+1,$totcaj,1,1,'C');
         $pdf->SetX($xinic); 
         $pdf->Cell($aenc/2,hcel+1,'PESO KG.',1,0,'C');
         $pdf->Cell($aenc/2,hcel+1,$totkil,1,0,'C');
         $pdf->Cell($aenc2/2,hcel+1,'VOLUMEN M3.',1,0,'C');
         $pdf->Cell($aenc2/2,hcel+1,$totmet,1,1,'C');
         
//SECCION DE SUMAS DE LA HOJA---------------------------------------------------------------------------  

    //consulta de totales
    $cita1=mysql_query("SELECT DISTINCT fecha_confir,hora_confir,num_confir,confirma FROM orden_resumen 
    WHERE no_tienda = $cedi[0] AND status = 20") or die ("Error en la consulta de datos de la cita .".mysql_error());
    $cita2=mysql_fetch_array($cita1);
    $fechaconfir = $cita2[0];
    $horaconfir = $cita2[1];
    $numconfir = $cita2[2];
    $confirma = $cita2[3];
    
 
$pdf->SetFontSize(ftc);
        $pdf->Ln(3);
        $pdf->SetX($xinic); 
        $pdf->Cell(12,hcel+1,'FECHA',0,0,'L');
        $pdf->Cell(25,hcel+1, $fechaconfir,1,0,'C');
        $pdf->Cell(acel*2,hcel+1,'HORARIO DE CITA',0,0,'L');
        $pdf->Cell(30,hcel+1,$horaconfir,1,1,'C');
        $pdf->Ln(2);
        $pdf->SetX($xinic); 
        $pdf->Cell(12,5,'CONFIRM.',0,0,'L');
        $pdf->Cell(25,5,$numconfir,1,0,'C');
        $pdf->Cell(acel*2,5,'CONFIRMO CITA',0,0,'R');
        $pdf->Cell(30,5,$confirma,1,1,'C');
        
 //vuelve a la x de inicio de columna
        $pdf->SetX($xmov);    
        
//FIN DE LA COLUMNA 2------------------------------------------------------------------------------------------------------------------------------

//tomar la coordenada x para fijar nuevo origen de columna
    $xinic3= $pdf->GetX();

//COLUMNA 3----------------------------------------------------------------------------------------------------------------------------------------   

//SECCION bodega--------------------------------------------------
//numero de renglones de la columna
$reng= 15;
//coordenadas de inicio de la columna
$xinic=$xinic3+acel+mr;
$yinic =hcel*4;

//inicialización de variables 
//para las ordenes
  $orda=array();
 //inicialización del arreglo de upcs para cantidades
  $artab= array();
//inicialización de los totales por columna
$totc=array();

//TITULOS ------------------------------------------------
         $pdf->SetFont('Arial','',ft1);
         $pdf->SetFillColor(144,144,144);
    //la y de inicio de las celdas de encabezado
        $pdf->SetXY($xinic, $yinici);
    //consulta de datos encabezado
        $datoenc= mysql_query("SELECT fecha_ent FROM orden_resumen  WHERE status = 20 AND no_tienda = $cedi[0] ")
        or die ("Error en la consulta de ordenes .".mysql_error());
        $datoenc1= mysql_fetch_row($datoenc);
        
        $pdf->Cell($aenc,hcel,'ENTREGA EN CEDIS DE:',1,0,'C',1);
        $pdf->Cell($aenc2,hcel,$cedi[0],1,1,'C');
        $pdf->SetX($xinic);
        $pdf->Cell($aenc,hcel,'Fecha de entrega:',1,0,'C');
        $pdf->Cell($aenc2,hcel,$datoenc1[0],1,1,'C');
        $y = $pdf->GetY();
        $pdf->SetX($xinic);
        $xc1=$pdf->getX();
        $pdf->SetFont('Arial','',ft5);
        $pdf->MultiCell($aenc,hcel*2,'Ordenes de Compra y no. de factura',1,'C');
        $xc1=$xc1+$aenc;
        $pdf->setXY($xc1,$y);
        $pdf->SetFont('Arial','',fto);    
        
//ciclo con las ordenes por CEDI-------------------------------------------------------------------------------------------
        //consulta de ordenes
        
          $ords=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 1
          AND status = 20")or die ("Error en la consulta de ordenes .".mysql_error()); 
           
          $ordsb=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 1
          AND status = 20 AND formato_tienda='BODEGA'")or die ("Error en la consulta de ordenes .".mysql_error());
           
          $ordss=mysql_query("SELECT orden, factura FROM orden_resumen WHERE no_tienda = $cedi[0] AND cliente_zerby = 1
          AND status = 20 AND formato_tienda='SUPERAMA'")or die ("Error en la consulta de ordenes .".mysql_error()); 
          
 //conteo de ordenes totales y por cadena         
          $nords= mysql_num_rows($ords);
          $nordsb = mysql_num_rows($ordsb);
          $nordss=  mysql_num_rows($ordss);
          
    //selección de caso por numero de ordenes existentes      
          switch($nords){
              
case 0:
    
    for($c=0;$c<cord;$c++){
        
        $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
    }

break;
                  
default : 
//inicializacion de arreglo de ordenes
$orda= array();
// ordenes
        while($ords2= mysql_fetch_array($ords)){
            //creacion del arreglo de ordenes para la obtencion de totales
            $orda[]=$ords2[0];
      
           $pdf->MultiCell(acel,hcel,$ords2[0]."\n".$ords2[1],1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
        }
        
        //columnas en blanco
        $blanco = cord-$nords;
        for($c=0;$c<$blanco;$c++){
            $pdf->MultiCell(acel,hcel," \n ",1,'C');
              $xc1 = $xc1+acel;
              $pdf->setXY($xc1,$y); 
        }

break;            
              
          }
//fin del ciclo de ordenes-------------------------------------------------------------------------------          

//total
   $pdf->SetFont('Arial','B',ftc);
   $pdf->Cell(acel,hcel*2,'TOTAL',1,1,'C');
   
   //regresar a x origen de la columna y tomar la y para el inicio de las demás columnas
   $y=$pdf->GetY();
   $pdf->SetX($xinic);     
   
//columna cliente------------------------------------------------------------------------------------------
$pdf->SetFont('Arial','B',ftc);    
        $pdf->MultiCell(act,8.34,'YESENIA DE LA CRUZ',1,'C');
        $pdf->SetXY($xinic+act,$y);
        $pdf->MultiCell(act,10,'BODEGA',1,'C');
        $pdf->SetX($xinic+act);
        $pdf->MultiCell(act,4.06,'SUPERAMA  WAL MART',1,'C');
 
$pdf->setXY($xinic+(act*2),$y); 

//columna de articulos---------------------------------------------------------------------------------------------------------------------------

    //SECCION BODEGA-------------------------------------------------------------------------------

    $artscb= mysql_query("SELECT  DISTINCT t1.upc, t1.desc1 from cat_arts AS t1 INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc 
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 1
    AND t3.formato_tienda= 'BODEGA' AND t3.status = 20 ORDER BY t1.upc")
    or die ("Error en la consulta de articulos .".mysql_error());
    $nartsb=mysql_num_rows($artscb);
    
   

    
//seleccionar caso dependiendo del numero de articulos 

switch($nartsb){
    
    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
        
    break; 
       
    default: 
        
        while($arts=mysql_fetch_array($artscb)){
    //creacion de arreglo de upcs para obtención de cantidades  
        $artab[]=$arts[0];
      
        $pdf->SetFont('Arial','B',ft5);
        $pdf->Cell($aenc-8,hcel,$arts[1],1,1);
        
 //regreso a x de origen
        $pdf->SetX($xinic+(act*2));
    }
        
 //renglones en blanco

 $blanco = $reng-$nartsb;
 
        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
    
    break;
} 



//SECCION SUPERAMA----------------------------------------------------------------------------------------

$reng = 15;
$artscs= mysql_query("SELECT DISTINCT t1.upc, t1.desc1 from cat_arts AS t1 INNER JOIN orden_detalle AS t2 ON t1.upc = t2.upc 
    INNER JOIN orden_resumen AS t3 ON t2.orden=t3.orden WHERE t3.no_tienda = $cedi[0] AND t3.cliente_zerby = 1
    AND t3.formato_tienda= 'SUPERAMA' AND t3.status = 20 ORDER BY t1.upc")
    or die ("Error en la consulta de articulos .".mysql_error());
    $nartss=mysql_num_rows($artscs);


//seleccionar caso dependiendo del numero de articulos 

switch($nartss){

    case 0:
        for($r=0;$r<$reng;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
        $pdf->SetFont('Arial','B',ftc);
        

    $pdf->Cell($aenc-8,5,'TOTAL CAJAS POR O.C',1,0); 
    break; 
       
    default: 
    //inicialización del arreglo de upcs 
        $artas= array();
        while($arts=mysql_fetch_array($artscs)){
    //creacion de arreglo de upcs para obtención de cantidades 
      $artas[]=$arts[0];
        $pdf->SetFont('Arial','B',ftc);
        $pdf->Cell($aenc-8,hcel,$arts[1],1,1);
        
 //regreso a x de origen
        $pdf->SetX($xinic+(act*2));
    }
        
 //renglones en blanco

 
 $blanco = $reng-$nartss;
 
        for($r=0;$r<$blanco;$r++){
            $pdf->SetFont('Arial','B',ftc);
            $pdf->Cell($aenc-8,hcel,'',1,1);
            $pdf->SetX($xinic+(act*2));
        }
    $pdf->SetFont('Arial','B',ftc);
    $pdf->SetX($xinic+(act*2));
    $pdf->Cell($aenc-8,5,'TOTAL CAJAS POR O.C',1,0); 
   
    break;
}

    $xfin3 = $pdf->GetX();
    $yfin3 = $pdf->Gety();
//sección de CANTIDADES DE ARTICULOS--------------------------------------------------------------------------------------------------------------

//seccion BODEGA ------------------------------------------------------------------------------------------------

$reng = 15;
//el cursor a las x y y inicial
    
     $pdf->setXY($xinic+(act*2)+($aenc-8),$y); 
     $xmov=$pdf->GetX();
     $y=$pdf->GetY();
     
    //seleccion de caso dependiendo del numero de ordenes-------------------------------------------------------
   
    switch ($nordsb) {
        case 0:

            for($c=0;$c<4;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
                
                //NO HAY RENGLON DE TOTALES.
                
                //vuelve al inicio de la columna,una columna más a la derecha
                       // $pdf->SetXY($xmov+acel, $yinic+(hcel*4));
                        $pdf->SetXY($xmov+acel, $y);
            }
            
            //impresion de la columna de totales
                   $xmov=$pdf->GetX(); 
                for($r=0;$r<$reng;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C');
                    $pdf->SetX($xmov);
                }
                //NO HAY CELDA DE TOTAL
                    //$pdf->Cell(acel,5,'',1,1,'C');
            break; //fin del caso no hay ordenes-----------
        
        default:

                //ciclo de columnas con los numero de orden---------------------------------------------------------------------     
            for($c = 0;$c<$nordsb;$c++){
                
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                        
                   
   //ciclo de renglones con el upc del articulo--------------------------------------------------------------------

            //se imprimen los renglones con las cantidades de articulos------------------------------

                    for ($r=0;$r<$nartsb;$r++){
                                    $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$orda[$c]'
                                AND upc = $artab[$r] ORDER BY upc")or die ("Error en la consulta de articulos .".mysql_error());
                                
                                $cantab= mysql_fetch_array($cant);
                                
                                
                       //adicion a los totales
                            //renglon
                                $tott[$r][$c]=$cantab[0];                            
                            
                                
                                $pdf->Cell(acel,hcel,number_format($cantab[0],0),1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                              
                            }    
     
                      //renglones en blanco

                   $blanco = $reng-$nartsb;
                   
                   for($r=0;$r<$blanco;$r++){
                       
                       $pdf->Cell(acel,hcel,'',1,1,'C');
                              //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                   }
                        
                    //imprime la celda del total por columna
                    //no hay total por columna
                        //$pdf->Cell(acel,5,$totc,1,0,'C');
                        
                     //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
                        
                                            
                  
                    
        } // fin del for columnas por numero de orden-------------------------------------------------
        
                    
          //imprime las columnas restantes en blanco
                    $resto = 4-$nordsb;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                        }
                            
                        //imprime la celda para el total de la columna
                        //no hay total de la columna
                        // $pdf->Cell(acel,5,'',1,0,'C');
                       //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
                    
                    }
          //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $xmov=$pdf->GetX();
              
                for($r=0;$r<$nartsb;$r++){
                        $tota= array_sum($tott[$r]);
        
                        
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xmov);   
                }
                //toma las coordenadas para la seccion superama
                $xtot=$pdf->GetX();
                $ytot=$pdf->GetY();
                    // no hay total de columna
                    //$pdf->Cell(acel,5,$totf,1,1,'C'); 
        break;
        
                    
                
        }//fin de switch numero de ordenes-----------------------------------------------------------------------

//sección superama---------------------- ------------------------------------------------------------------------

$reng = 15;
//el cursor a las x y y inicial
    
     $pdf->setX($xinic+(act*2)+($aenc-8)); 
     $xmov=$pdf->GetX();
     $y=$pdf->GetY();
     
    //seleccion de caso dependiendo del numero de ordenes totales-------------------------------------------------------
    switch ($nords) {
        //no hay ordenes totales, solo imprime celdas en blanco
        case 0:
        //primero imprime celdas en blanco, con una columna de más para los totales
            for($c=0;$c<cord+1;$c++){
                //toma la x de inicio
                    $xmov=$pdf->GetX();
                    
                for($r=0;$r<$reng;$r++){
                    
                     $pdf->Cell(acel,hcel,'',1,1,'C');
                    //vuelve a la x de inicio de columna
                              $pdf->SetX($xmov);
                }
          //un reglon más para totales porque tiene más altura
                     $pdf->Cell(acel,hcel+1,'',1,1,'C');
         //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);
  
            }
         break;// fin del caso no hay ordenes totales---------------------------------
         
         // hay ordenes totales o solo superama
         default:
         //selección de caso si hay ordenes
            switch ($nordss) {
                //no hay ordenes superama
                case 0:
                 //imprime renglones y columnas en blanco
                     for($c=0;$c<cord+1;$c++){
                        //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                        for($r=0;$r<$reng;$r++){
                            
                             $pdf->Cell(acel,hcel,'',1,1,'C');
                            //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);
                        } 
                            
                                
                        //vuelve al inicio de la columna,una columna más a la derecha
                        $pdf->SetXY($xmov+acel, $y);         
                     }
                    
                        
                    break;//fin del caso no hay superama-------------------------------------
                //si hay superama
                default:
                //imprime las celdas con cantidades
                //ciclo de columnas con los numero de orden---------------------------------------------------------------------
                
                    for($c = 0;$c<$nords;$c++){
                        //toma la x de inicio
                        $xmov=$pdf->GetX();
                        //se imprimen los renglones con las cantidades de articulos
                        for ($r=0;$r<$nartss;$r++){
                             $cant=mysql_query("SELECT cantidad/empaque FROM orden_detalle WHERE orden = '$orda[$c]'
                                AND upc = $artas[$r] ORDER BY upc")or die ("Error en la consulta de articulos superama .".mysql_error()); 
                                $cantas= mysql_fetch_array($cant);
                             //adicion al total de renglon
                                $tott[$r][$c]=$cantas[0];
                             //escribe la celda de la cantidad
                                $pdf->Cell(acel,hcel,number_format($cantas[0],0),1,1,'C');
                                $pdf->SetX($xmov) ; 
                        }
                        //imprime renglones en blanco
                        $blanco = $reng-$nartss;
                        
                        for ($r=0; $r <$blanco ; $r++) { 
                            $pdf->Cell(acel,hcel,'t',1,1,'C');
                             $pdf->SetX($xmov) ;
                        }
                        
                        //vuelve al origen de la tabla, una columna más a la derecha
                         $pdf->SetXY($xmov+acel, $y); 
                    }
                    
                //imprime columnas en blanco
                    $resto = cord-$nords;
                    
                        for ($c=0;$c<$resto;$c++){
                            
                             //toma la x de inicio
                            $xmov=$pdf->GetX();
                            
                            for($r=0;$r<$reng;$r++){
                        
                                 $pdf->Cell(acel,hcel,'',1,1,'C');   
                                //vuelve a la x de inicio de columna
                                      $pdf->SetX($xmov);    
                            }
                        }
                        
                //impresion de la columna de totales
                //impresion de total
                //toma la x de inicio
                $pdf->SetXY($xtot, $ytot);
                for($r=0;$r<$nartss;$r++){
                        $tota= array_sum($tott[$r]);              
                     $pdf->Cell(acel,hcel,$tota,1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xtot);   
                }
                
                //impresion de celdas en blanco
                for ($r=0;$r<$blanco;$r++){
                    $pdf->Cell(acel,hcel,'',1,1,'C'); 
                    
                     //vuelve a la x de inicio de columna
                    $pdf->SetX($xtot);   
                }
        
                    
                    break;//fin del caso si hay superama------------------------------------
            }

 //SECCION TOTALES
 
    if($nords != 0)      {
        
        $pdf->SetXY($xfin3, $yfin3) ;
                        $totf3=0;
                    for ($c=0; $c < $nords; $c++) {
                         $stot2=mysql_query("SELECT SUM(cantidad/empaque) FROM orden_detalle WHERE 
                        orden = '$orda[$c]'") or die ("Error en la consulta de articulos totales .".mysql_error());
                        $tot2 = mysql_fetch_array($stot2);  
                        $totf3+=$tot2[0];
                         
                        $pdf->Cell(acel,hcel+1,number_format($tot2[0],0),1,0,'C');
                    }
                    //imprime celdas en blanco
                    for ($cr=0; $cr <(cord-$nords) ; $cr++) { 
                        $pdf->Cell(acel,hcel+1,'',1,0,'C');
                    }
                     //imprime la celda del total final
                        $pdf->Cell(acel,hcel+1,number_format($totf3,0),1,0,'C'); 
    }  
            
                     
          
        
         break;  //fin del caso si hay ordenes ---------------------------------- 
           
             
    }


//FIN DE LA COLUMNA 3--------------------------------------------------------------------------------------   
    
// fin de la página-----------------------------------------------------------------------------------------  
   $folioa++;

}
    
//FIN DEL CICLO DE PAGINAS------------------------------------------------------------------------------------
 $pdf->Output(); 
 
 
 //cambio de estado a las ordenes impresas
    $queryacto = ("UPDATE `orden_resumen` SET `status`= 21 WHERE `status` = 20")
   or die ("Error en la consulta de actualizacion de status .".mysql_error());
                    $result1= mysql_query($queryacto);
   

 //guardado de folio de impresion
                    
     $queryactf = ("UPDATE indicadores SET valor= $folioa  WHERE id_indicadores = 1 ")
    or die ("Error en la consulta de actualizacion de folio control.".mysql_error());
                    $result1=mysql_query($queryactf);
                                 
   
?>