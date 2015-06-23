<?php
//este archivo contiene las funciones para actualizar los datos de entrega de una orden

//version 1.0 Marzo 17, 2013.



    
    if(isset($_POST['envioe'])){
        global $tabla;
        $reng = $_REQUEST['rengs'];
        //ciclo para recorrer los renglones de la hoja
        for ($i=0; $i<$reng;$i++){
            $fechaa = $_REQUEST['date'.$i];
            $horai= $_REQUEST['hora'.$i];
            $obsa= "'".$_REQUEST['obs'.$i]."'";
            $ordena=$_REQUEST['ords'.$i];
            //si se introdujo hora, agrega los datos
            if(substr($horai,0,1)!= '#'){
            //convierte la hora a string
                $horaa="'".$horai."'";
            //valida si se registro el evento
           
                if(entrega($ordena,$fechaa,$horaa,$obsa)==0){
                     $bandera = 0;
                }
                else {
                    $bandera = 1; 
                }
            }
            
            
        }
//analisis del resultado
        switch ($bandera) {
            case 0:
                echo '<script type="text/javascript">
                                        window.alert("Entrega registrada OK.");
                                        document.location = "listaorden.php";
                                    </script>';
                break;      
            
            default:
                echo '<script type="text/javascript">
                                        window.alert("Error. No se pudo registrar la entrega!");
                                         document.location = "entrega.php";
                                    </script>';
                break;
        }
        
//fin del if de post de la forma--------------------------------------------------------------------------------------        
            }

 
     function entrega($orden,$fecha,$hora,$obs){
 //esta funcion actualiza la tabla orden resumen, con datos de la entrega y actualiza el startus de la orden a entregado


                            $querye = "UPDATE orden_resumen SET fecha_del = '$fecha', hora_del = $hora,obs = CONCAT(obs,$obs), status =30
                            WHERE orden = $orden" ;            
//lenado de campos
                           
                            $resulte1=mysql_query($querye) or die("Error en registro de entrega: ".mysql_error());               
                                   
                            
 //VALIDACION DE INSERCION                      
                             if(mysql_affected_rows()>0){
            //el registro se inserto correctamente------------------------------------------------------------------
                               return 0;
                            }
                           
                                 
                              
                             else{
             //No se -----------------------------
                             
                                   return 1;
                                   creaLog(mysql_error());
                             }
                         
//--fin de la funcion entrega--------------------------------------------------------------------------------------------------------------
    }



?>