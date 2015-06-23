<?php
//ver 1.0 abr 3 2013
//esta hoja contiene las funciones para la validacion y carga de ordenes de compra

require_once"conversiones.php";



function revisaOrden($archsub){
//esta funcion recibe como parametro el nombre del archivo a examinar, proveniente de un cuadro de input

//valida tipo de archivo permitido
$tipos= array("csv","xls" );
//construye ruta del archivo
$rutasub = "uploads/";
$archivo = $rutasub.basename( $archsub);     //cambiar por $_FILES['uploadedfile']['name']

//validar contenidos   //cambiar por $_FILES['uploadedfile']['name']
foreach ($tipos as $valor) {
    reset($tipos);
    if(substr($archsub,-3) == $valor){
 //el archivo tiene una extensión valida

            return $valor;
        }
}
//el archivo no tiene una extensión valida
         return 1;
}
//---FIN DE LA FUNCION REVISAORDEN------------------------------------------------------------------------------


function cargaDatos($archorig,$tipo){
  //obtencion del usuario
  $usu2=$_SESSION['username'] ;  
            
  // si se pudo subir el archivo se intenta llenar las tablas y se avisa del resultado.
            
    switch ($tipo) {
        case 'csv':
            return cargaCsv($archorig,$usu2);
            break;
            
        case 'xls':
            return cargaXls($archorig,$usu2);
            break;
        
        default:
            
            break;
    }
    
          return 0;


//no se pudo subir el archivo             

            
            }
//FIN DE LA FUNCION CARGADATOS---------------------------------------------------------------------- 

        function cargaCsv($archorig,$usu){
            //carga archivo csv. parametro el archivo elegido. ciclo de lectura csv
                
        if (($handle = fopen($archorig, "r")) !=FALSE) {

                //si puede abrir el archivo, prodede a la lectura, hasta terminar con el string.                        
                 $fila=0;
                 while (($data = fgetcsv($handle, 1000, ",")) !=FALSE) {
                                            
                     if ($fila == 1){
                                                      
                //llenado de encabezado
                    
                    // hace las conversiones necesarias para llenar la tabla resumen
                    $campo6=qblanco($data[6]);
                    $campo7=qblanco($data[7]);
                    $campo8=qblanco($data[8]);
                    $campo9=qblanco($data[9]);
                    $campo10=qblanco($data[10]);
                    $campo11=qblanco($data[11]);
                    $campo12=qblanco($data[12]);
                    $campo14=qblanco($data[14]);
                    $fecha1=convfecha($data[19]);
                    $fecha2=convfecha($data[20]);
                    $fecha3=convfecha($data[21]);
                    $campo22 = qblanco($data[22]);
                    $campo23 = qblanco($data[23]);
                    $campo24 = qblanco($data[24]);
                    $campo27 = qblanco($data{27});
                    $campo28 = qblanco($data[28]);
                    $campo29 = qblanco($data[29]);
                    $campo30 = qblanco($data[30]);
                    $campo31 = qblanco($data[31]);
                    $campo32 = qblanco($data[32]);
                    $campo33 = qblanco($data[33]);
                    $campo34 = qblanco($data[34]);
                    $campo35 = qblanco($data[35]);
                    $notienda =notienda($data[10]);
                    //obtencion de datos recurrentes
                    $orden = $data[1];
                    $vend= $data[3];
                    
                     //validaciones fila 1-------------------------------------------------------------------------
                    //la orden no existe en las tablas            
                        if (validaOrden($orden)==1){
                            //el numero de orden ya existe
                            fclose($handle);
                            return 4;
                        }
                        
                       //Validacion de cadena. modificar más adelante por una consulta a tabla de cadenas
                        //definicion de la cadena para resumen           
                             $cadena= defcadena($campo11);
                             
                             if($cadena==990){
                                fclose($handle);
                                return 5;
                             }
                        
              //el cliente si existe en las tablas      
                        $cliente = validaCte($vend);  
                        
                        if ($cliente==990){
                             fclose($handle);
                             return 6;
                        }
                       
                        
                        
    //insertar los datos en la tabla-------------------------------------------------------------------------------------                 
                     $resultllena= llenaordenres($cadena,$cliente,$orden,$data[2],$data[3],$data[4],$data[5],$campo6,$campo7,
                     $campo8,$campo9,$campo10,$campo11,$campo12,$campo14,$fecha1,$fecha2,$fecha3,$campo22,$campo23,$campo24,
                     $campo27,$campo28,$campo29,$campo30,$campo31,$campo32,$campo33,$campo34,$campo35,$usu,$notienda);
                   
    //valida si se logro la inserción             
              if ($resultllena !=0) {
                  fclose($handle);
                  return 7;
              }
                
           
                    
                    }//fin del if fila 1
                    
    
                                   
         
                    //filas subsecuentes-------------------------------------------------------------------------------
                    if($fila>2){
                        
                        //validación de existencia de los upcs
                            $upc=validaUpc($data[2]);
                            
                            if($upc==1){
                                return 9;
                            }
                            
                         //definicion de cadena por articulo para distinguir el articulo patyleta con upc repetido
                         
                        if($data[2]=='605391414759' && $cadena== 1){
                            $cad_art= 1;
                        }
                        
                        elseif($data[2]=='605391414759' && $cadena== 3){
                            $cadart=3;
                        }
                        else{
                            $cad_art=99;
                        }
                            
                     
                  //string de llenado de campos tabla orden_detalle   
                        $queryd = "INSERT INTO orden_detalle (orden,no,upc,cad_art,no_comprador,size,cantidad,medida,precio,
                                empaque,color,monto_linea)
                                VALUES ($orden,$data[1],$data[2], $cad_art,$data[3],'$data[5]',$data[7],'$data[8]',$data[9],$data[10],
                                '$data[11]',$data[12])";
                                
                      //llenado de campos
                               
                               $resultd = mysql_query($queryd);
                    
                                       if(!$resultd){
                             //No se pudo lograr la insercion, crea una entrada en el log
                                           creaLog(mysql_error());
                                           fclose($handle);
                                           return 8;
                                       }            
                                            
                                        
                                    }//fin del if fila >2
                      
                       
                      $fila++;
                    }//FIN DEL WHILE GETCSV
                    
                         fclose($handle);
                         return 0;
                 }//FIN DEL CICLO IF OPEN
                 
                    else {
                        return 5;
                    }
        }   //FIN DE LA FUNCION CARGACSV
        
    
        
        function cargaXls($archorig,$usu){

            //carga archivo xls elegido
            require_once 'excel_reader2.php';
            $data = new Spreadsheet_Excel_Reader($archorig,false);
            $datos=array();
            $datos=$data-> dumptoArray();
            //numero de renglones
            $reng=$data->rowcount();
                       
            //obtencion de datos orden resumen
            $orden= $datos[3][2];
            $vend= $datos[11][2];
            $tipo = $datos[5][2];
            $proveedor = $datos[11][2];
            $moneda = $datos[14][2];
            $depto = $datos[6][2];
            $monto = $datos[18][2];
            $partidas = $datos[19][2];
            $nocomprador = $datos[8][2];
            $comprador =$datos[9][2];
            $notienda=notienda($comprador);
            $noformato = $datos[6][4];
            $formato = $datos[7][4];
            $lugar = $datos[13][4];
            $fechao = convfechaxls($datos[3][4]);
            $fechaemb = convfechaxls($datos[4][4]);
            $fechac = convfechaxls($datos[5][4]);
            $diasp = $datos[17][4];
            
            //validaciones
             if (validaOrden($orden)==1){
                            //el numero de orden ya existe
                            unset($datos); 
                            return 4;
                        }
 
 //definicion de la cadena para resumen y formato            
             $cadena= defcadena($noformato);
             
             if($cadena==990){
                  unset($datos);                
                  return 5;
             }
                                         
//el cliente si existe en las tablas      
                        $cliente = validaCte($vend);  
                        
                        if ($cliente==990){
                             unset($datos); 
                             return 6;
                        } 
                          
//insertar los datos en la tabla-------------------------------------------------------------------------------------
                    
                     $queryr = "INSERT INTO orden_resumen (cadena,cliente_zerby,orden,tipo_orden,vendedor,moneda,depto,monto_total,promocion,
                 no_partidas,no_comprador,comprador,no_formato_tienda,formato_tienda,lugar_embarque,fecha_orden,fecha_canc, fecha_ent,
                 dias_pago,dias_ppago,p_desc,cargo_flete,libre1,libre2,libre3,libre4,libre5,libre6,libre7,libre8,dadealta,no_tienda,status,status_maestro)
                  VALUES ($cadena,$cliente,$orden,$tipo,$vend,'$moneda',$depto,$monto,null,$partidas,$nocomprador,'$comprador',$noformato,'$formato','$lugar',
                  $fechao,$fechac,$fechaemb,$diasp,null,null,null,null,null,null,null,null,null,null,null,'$usu',$notienda,0,0)";
               
                $result2=mysql_query($queryr);
    
               if(!$result2){
     //No se pudo lograr la insercion, crea una entrada en el log
                   creaLog(mysql_error());
                   unset($datos);   
                   return 7;
                 }
                                         
             
             //carga de datos orden_detalle
                 for ($i=22; $i <$reng ; $i++) {
                        $upc=$datos[$i+1][1];
                        $comprador2= $datos[$i+1][2];
                        $color = $datos[$i+1][4];
                        $precio = $datos[$i+1][5];
                        $medida =$datos[$i+1][6];
                        $montol =$datos[$i+1][7];
                        $cantidad =$datos[$i+1][8];
                        $empaque =substr($datos[$i+1][9],0,2);
                        
                        
                     //validación de existencia de los upcs
                            $upc2=validaUpc($upc);
                           
                            if($upc2==1){
                                unset($datos);  
                                return 9;
                            }
                            
                         //definicion de cadena por articulo para distinguir el articulo patyleta con upc repetido
                        if($upc=='605391414759' && $cadena== 1){
                            $cad_art= 1;
                        }
                        
                        elseif($upc=='605391414759' && $cadena== 3){
                            $cadart=3;
                        }
                        else{
                            $cad_art=99;
                        }
                        
                  //string de llenado de campos tabla orden_detalle   
                        $queryd = "INSERT INTO orden_detalle (orden,upc,cad_art,no_comprador,cantidad,medida,precio,
                                empaque,color,monto_linea)
                                VALUES ($orden,$upc, $cad_art,$comprador2,$cantidad,'$medida',$precio,$empaque,
                                '$color',$montol)";
                                
                      //llenado de campos
                               
                               $resultd = mysql_query($queryd);
               //validación de la inserción                  
                                       if(!$resultd){
                             //No se pudo lograr la insercion, crea una entrada en el log
                                           creaLog(mysql_error());
                                           return 8;
                                       }                 
        
        
                }//FIN DEL FOR I DE RENGLONES--
    
                                 unset($datos);  
                                return 0;
        }//FIN DE LA FUNCION CARGAXLS.------------------------------------------


        function defcadena($formato){
    
                //definicion de cadena para orden resumen y orden detalle.
                            switch ($formato) {
             //caso bodega
                                case 7507003100025:
                                    return 1;
                                    break;
             //caso superama                       
                                case 7507003100032:
                                     return 2;
                                    break;
            //caso wal-mart                   
                                 case 7507003100001:
                                     return 3;
                                    break;
                                    
                                
                                default:
            //caso desconocido
                                    return 990;
                                    break;
                }
}
        
        
        function validaOrden($orden){
            //esta funcion revisa si el numero de orden ya existe
            // ese numero de orden ya existe  
            $sql="SELECT orden FROM orden_resumen WHERE orden = $orden";
            $result1=mysql_query($sql);
            $count=mysql_num_rows($result1);
                  
        //Si el numero de orden ya existe, sale de la rutina
        return $count != 0 ? 1: 0;
        
        }
        
        
        function validaUpc($upcp){
            //esta funcion revisa si el numero de upc ya existe  
            $sql="SELECT upc FROM cat_arts WHERE upc = $upcp";
            $result1=mysql_query($sql);
            $count=mysql_num_rows($result1);
                  
        //retorno del valor
        return $count == 0 ? 1: 0;
        
        }
        
        function validaCte($cte){
            //esta funcion valida si el cliente existe y le asigna su numero de catalogo
            
            //conversion del no. prov wm a 6 digitos
                $novend=provwm($cte);
            
            $sql="SELECT id_clientes FROM clientes WHERE  prov_wm = $novend ";
            $result1=mysql_query($sql);
            $result2=mysql_fetch_array($result1);
            $count=mysql_num_rows($result1);
            
            return $count == 0 ? 990:$result2[0];
        }
        
        function provwm($vend){
            //esta funcion extrae los primeros 6 digitos del campo vendedor en la orden de compra
            //para registrar el numero de proveedor de 6 dig, en la base de datos.
            return substr($vend,0,6);
        }
        
        
        
        
        function llenaordenres($cad,$cte,$ord,$tipo,$vend,$mone,$dep,$monto,$promo,$nopart,$nocompra,$compra,$noforma,
        $forma,$lugar,$fechaord,$fechacanc,$fechaent,$diasp,$diaspp,$pdesc,$flete,$l1,$l2,$l3,$l4,$l5,$l6,$l7,$l8,$dalta,$tienda){
            //esta funcion hace la consulta de insercion a orden resumen
            
            
              //llenado de la tabla si validaciones OK            
                 $queryr = "INSERT INTO orden_resumen (cadena,cliente_zerby,orden,tipo_orden,vendedor,moneda,depto,monto_total,promocion,
                 no_partidas,no_comprador,comprador,no_formato_tienda,formato_tienda,lugar_embarque,fecha_orden,fecha_canc, fecha_ent,
                 dias_pago,dias_ppago,p_desc,cargo_flete,libre1,libre2,libre3,libre4,libre5,libre6,libre7,libre8,dadealta,no_tienda,status,status_maestro)
                  VALUES ($cad,$cte,$ord,$tipo,$vend,'$mone',$dep,$monto,'$promo',$nopart,$nocompra,'$compra',$noforma,'$forma','$lugar',
                  $fechaord,$fechacanc,$fechaent,$diasp,$diaspp,$pdesc,'$flete','$l1','$l2','$l3','$l4','$l5','$l6','$l7','$l8','$dalta',$tienda,0,0)";
               
                $result2=mysql_query($queryr);
    
               if(!$result2){
     //No se pudo lograr la insercion, crea una entrada en el log
                   creaLog(mysql_error());
                    return 4;
                 }
                 
            else return 0;   
        }
        

?>