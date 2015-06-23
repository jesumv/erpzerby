<?php

//version 1.0 Marzo 17, 2013.

// este archivo llena las tablas relativas a las ordenes de compra
//VALORES DE RETORNO: 0 ok; 
//1 el archivo csv no se pudo abrir. 2 el formato de tienda es desconocido. 3 la orden de compra ya ha sido agregada. 
//4.no se inserto el registro en la tabla de orden resumen. 5no se pudo inserttar en orden detalle.

//directiva con funciones de conversion de datos
    include_once"conversiones.php";
//la directiva a las funciones auxiliares
    include_once"funaux.php";
       
    

function llenatablas($archorig, $usu,$cliente){
    
   //lectura del archivo y construccion de arreglo con datos
    
 
    if (($handle = fopen($archorig, "r")) != FALSE) {
       $fila=0;
  //si puede abrir el archivo, prodede a la lectura, hasta terminar con el string. 
    
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
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
                
//VALIDACIONES PREVIAS ----------------------------------------------------------------------------------------------------------------     
                
            //definicion de cadena
                switch ($campo11) {
 //caso bodega
                    case 7507003100025:
                        $cadena = 1;
                        break;
 //caso superama                       
                    case 7507003100032:
                         $cadena = 2;
                        break;
//caso wal-mart                   
                     case 7507003100001:
                         $cadena = 3;
                        break;
                        
                    
                    default:
//caso desconocido
                        return 2;
                        break;
                }
                
// ese numero de orden ya existe  
       
       
            $sql="SELECT orden FROM orden_resumen WHERE orden = $orden";
            $result1=mysql_query($sql);
            $row=mysql_fetch_array($result1);  
            $count=mysql_num_rows($result1);
                  
        //Si el numero de orden ya existe, sale de la rutina
        if($count!=0)
       {
            echo '<script type="text/javascript">alert("La orden de compra ya existe, revise.")</script>';
            fclose($handle);
            return 3;   
       
       }
       
       
       //LLENADO DE TABLAS ------------------------------------
            
            //string de llenado de campos tabla orden_resumen siempre se da de alta con status 0 = por confirmar.
            
               $queryr = "INSERT INTO orden_resumen (cadena,cliente_zerby,orden,tipo_orden,vendedor,moneda,depto,monto_total,
                promocion,no_partidas,no_comprador,comprador,no_formato_tienda, formato_tienda,lugar_embarque,fecha_orden, 
                fecha_canc, fecha_ent,dias_pago,dias_ppago,p_desc,cargo_flete,libre1,libre2,libre3,libre4,libre5,libre6,libre7,libre8,
                dadealta,status,no_tienda) VALUES ($cadena,$cliente,$orden,$data[2],$data[3],'$data[4]',$data[5],$campo6,'$campo7',$campo8,$campo9,
                '$campo10',$campo11,'$campo12','$campo14',$fecha1,$fecha2,$fecha3,$campo22,$campo23,$campo24,'$campo27',
                '$campo28','$campo29','$campo30','$campo31','$campo32','$campo33','$campo34','$campo35','$usu',0,$notienda)";
         
             //lenado de campos
               
             $result2=mysql_query($queryr) ;
    
               if(!$result2){
     //No se pudo lograr la insercion, crea una entrada en el log
                   creaLog(mysql_error());
      //intenta eliminar el archivo original para que se pueda importar correctamente             
                   chmod($archorig, 0777);
                   unlink($archorig);
           
                    if (is_file($archorig) == true){
      //si no lo puede eliminar, avisa                
                        echo "el archivo no se pudo eliminar";
                    } 
                    fclose($handle);
                    return 4;
      //-----------fin del if no se pudo lograr la insercion
             }
            
      //-----------fin del if fila 1
         }
        
   //---------------------------------llenado de orden_detalle----------------------------------------------------------------------
            if($fila>2){
        //Hace las conversiones necesarias para el llenado de la tabla detalle
        

        $cadart= 7;
        //definicion de cadena por articulo para distinguir el articulo patyleta con upc repetido
        if($data[2]=='605391414759' && $cadena== 1){
            $cadart= 1;
        }
        
        elseif($data[2]=='605391414759' && $cadena== 3){
            $cadart=3;
        }
        else{
            $cadart=99;
        }
     
  //string de llenado de campos tabla orden_detalle   
        $queryd = "INSERT INTO orden_detalle (orden,no,upc,cad_art,no_comprador,size,cantidad,medida,precio,
                empaque,color,monto_linea)
                VALUES ($orden,$data[1],$data[2],$cadart,$data[3],'$data[5]',$data[7],'$data[8]',$data[9],$data[10],
                '$data[11]',$data[12])";
                
      //llenado de campos
               
               $resultd = mysql_query($queryd);
    
                       if(!$resultd){
             //No se pudo lograr la insercion, crea una entrada en el log
                           creaLog(mysql_error());
                           fclose($handle);
                           return 5;
                       }
  // fin del if de fila  > 2           
            }
  //se incrementa la fila
             $fila++;    
  //cierre de while data
         }
  //cierre de archivos
           fclose($handle);
           return 0;
// fin de if si se pudo abrir el archivo----------------------------------------------------------------------------
        }
else{
//no se pudo abrir el archivo
          
}
// fin de la funcion---------------------------------------------------------------------------------------------------
    }

function llenacita($colscheck, $ordenes, $rengcheck,$invs2,$formtit){
        
    $_SESSION['formato']=$formtit;
    
//esta funcion toma los valores de fecha,hora, numero de confirmacion y confirma para cada orden confirmada y los agrega a la tabla
//orden resumen
//tambien toma las cantidades de cada articulo ordenado y afecta los inventarios
//Y llama a la rutina de impresion de reporte de confirmaciones

//recibe como argumento el numero de columnas, para realizar el ciclo de busqueda

//revisa los check list para ver cuales están checados ciclo por columnas-----------------------------------------------------------
    for($i=0; $i<$colscheck;$i++){
//si el check esta encendido, procesa--------------------------      
        if(isset($_POST['check'.$i])){
//toma el numero de orden para la consulta           
            $ordenact = $ordenes[$i];
            
//consultas sql-------------------------------------------------------
                      
            $fechaconf = convfecha($_POST['fecha'.$i]);
            $horaconf = "'".$_POST['hora'.$i]."'";
            $noconf = "'".$_POST['noc'.$i]."'";
            $confirma="'".$_POST['conf'.$i]."'";
            $obs = "'".$_POST['obs'.$i]."'";
            
            $queryf = ("UPDATE orden_resumen SET status= 10,fecha_confir = 
            $fechaconf, hora_confir=$horaconf,num_confir = $noconf, confirma = $confirma, obs = $obs 
            WHERE orden = $ordenact ");
            
            //llenado de campos
               
            $result1=mysql_query($queryf)or die ("Error en el llenado de cita.".mysql_error());
            
//ciclo para inventarios, por renglones----------------------------------------------------------------------------------------------
                for($j=0; $j<$rengcheck; $j++){
                    //obtener el valor del inventario a afectar
                        //cajas del articulo
                        $inventact= -($invs2[$j][6+($i*2)]);
            //si el inventario es mayor de 0, se registra el movimiento
                    if($inventact <0){
                        //upc
                        $upcact = $invs2[$j][0];
                        //cadena
                        $cadact=$invs2[$j][2];
                        $usu= $_SESSION['username'];
                        $queryi = "INSERT INTO inventario (upc,cadena,ref,status,cajas,almacen,fecha,dadealta)
                        VALUES ($upcact,$cadact,$ordenact,2,$inventact,2,$fechaconf,'$usu')";
                    //llenado de campos
                        $result2=mysql_query($queryi)or die ("Error al actualizar inventario.".mysql_error());
                    }
                    
                    else{$result2="";}
                        
                }            
         

             
  //validacion de insercion correcta ----------------------------------
             if(!$result1){
  //No se pudo lograr la actualizacion de ordenes, crea una entrada en el log----------
                   //creaLog(mysql_error());
                         echo '<script type="text/javascript">
                          window.alert("Las ordenes no se pudieron actualizar.");
                        </script>';

                                 
                         return 1;
                    }
             
             elseif($result2=""){
  //No se pudo lograr la insercion de inventarios, crea una entrada en el log----------
                   creaLog(mysql_error());
                                 
                       echo '<script type="text/javascript">
                          window.alert("Los inventarios no se pudieron actualizar.");
                           document.location = "confirmaciones.php";
                        </script>';
                        
                         return 2;
                    }
             
              
     //-----------fin del if no se pudo lograr la insercion
               else{
    // si se pudo lograr la inserción-------------------------------- 
             // imprime la forma de confirmaciones para las ordenes programadas
                    echo '<script type="text/javascript" language="Javascript">
                            window.open("php/rconfir.php");
                            window.alert("Ordenes e inventarios actualizados OK.");
                            document.location = "confirmaciones.php";
                    </script>'; 
                     
                }

                 
//fin del isset                       
             }
//fin del for   seccion columnas         
        }
                return 0;     
//fin de la funcion               
    }


function programa($rengsprog,$vals){
//esta funcion recibe como argumentos: el numero de renglones a revisar y el arreglo con facturas introducidas
//en la forma programacion.
//revisa los checklist, y si esta encendido, 
// revisa si se introdujo factura y si es positivo, obtiene el numero de orden


    for ($i=0;$i<$rengsprog;$i++){
        
            if(isset($_POST['prog'.$i])){
//toma el numero de orden para la consulta     
                    $ordenact = $vals[$i][0];
                    $factact = $_POST['fact'.$i];
                    
                    if (empty($factact)){
                        echo '<script type="text/javascript">
                          mensaje("Es necesario un numero de factura");
                            </script>';
                            return 1;
                    }
                    
                    else{
                     //actualiza la tabla al estado 20 = programada
                    $queryp = ("UPDATE orden_resumen SET status= 20 ,factura = '$factact' WHERE orden = $ordenact ");
            
                    $result1=mysql_query($queryp);

                         
//validacion de insercion correcta ----------------------------------
                         if(!$result1){
              //No se pudo lograr la insercion, crea una entrada en el log----------
                                    creaLog(mysql_error());
                                     echo '<script type="text/javascript">
                                      window.alert("Las ordenes no se pudieron programar.");
                                    </script>';                                      
                                     return 1;
                                }
                         
                 //fin de else
       
                        }
                    

//fin de isset                  
        }
        
           
 
 
 //fin de for       
    }
                        // imprime la forma de control de carga para las ordenes programadas
                         echo '<script type="text/javascript"language="Javascript">
                          window.open("php/controlc.php");</script>';
                         // si se pudo lograr la inserción-------------------------------- 
                         echo '<script type="text/javascript">
                                      window.alert("Ordenes programadas OK.");
                                      document.location = "programacion.php";
                                    </script>'; 
                          
                        
                        return 0;   
//fin de la funcion
}
         
  
?>


          