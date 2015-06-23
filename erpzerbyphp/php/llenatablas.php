<?php

//version 2.1 agosto 3, 2013.

// este archivo llena las tablas relativas a las ordenes de compra
//VALORES DE RETORNO: 0 ok; 
//1 el archivo csv no se pudo abrir. 2 el formato de tienda es desconocido. 3 la orden de compra ya ha sido agregada. 
//4.no se inserto el registro en la tabla de orden resumen. 5no se pudo insertar en orden detalle.

//directiva con funciones de conversion de datos
    include_once"conversiones.php";
//la directiva a las funciones auxiliares
    include_once"funaux.php";
       

function llenacita($colscheck, $ordenes, $rengcheck,$invs2,$formtit){
    
    
//esta funcion toma los valores de fecha,hora, numero de confirmacion y confirma para cada orden confirmada y los agrega a la tabla
//orden resumen
//tambien toma las cantidades de cada articulo ordenado y afecta los inventarios 
//Y llama a la rutina de impresion de reporte de confirmaciones

//recibe como argumento el numero de columnas, para realizar el ciclo de busqueda, arreglo con los numeros de orden, no. de renglones para el ciclo
//arreglo con las cantidades en inventario y formato de la tienda.

// toma la variable formato para el titulo de la pagina de impresión
 $_SESSION['formato']=$formtit;

//revisa los check boxes para ver cuales están checados. ciclo por columnas-----------------------------------------------------------

    for($i=0; $i<$colscheck;$i++){
//si el check esta encendido, procesa--------------------------      
        if(isset($_POST['check'.$i])){
            
//cambia los valores en la tabla html por los que se van entregar. funcion en funaux.php
            cambiatablas($i,$rengcheck,$invs2);
//toma el numero de orden para la consulta           
            $ordenact = $ordenes[$i];
//revisa si la entrega esta completa en funaux.php
            $estado=completa($ordenact,$invs2,$i);

//establece el valor del estado en el que queda la orden después de la entrega
            switch ($estado) {
                case 0:
            //orden confirmada completa
                    $status = 10;
                    break;
                case 2:
            //las cantidades excederian los articulos existentes
                    $status = 9999;
                    break;
            //la orden se confirma parcial    
                default:
                    $status = 9;
                    break;
            }
            
//Si el status es correcto, se hacen las consultas---------------------
if ($status!=9999) {
	

//consultas sql-------------------------------------------------------
                     
            $fechaconf = convfecha($_POST['fecha'.$i]);
            $horaconf = "'".$_POST['hora'.$i]."'";
            $noconf = "'".$_POST['noc'.$i]."'";
            $confirma="'".$_POST['conf'.$i]."'";
            $obs = "'".$_POST['obs'.$i]."'";
            
            $queryf = ("UPDATE orden_resumen SET status= $status, status_maestro = $status, obs = $obs 
            WHERE orden = $ordenact ");
            
            //llenado de campos
               
            $result1=mysql_query($queryf)or die ("Error en el llenado de cita.".mysql_error());
            
//ciclo para inventarios, por renglones----------------------------------------------------------------------------------------------
                for($j=0; $j<$rengcheck; $j++){
                    //obtener el valor del inventario a afectar
                        //cajas del articulo
                        $inventact= -($invs2[$j][8+($i*3)]);
            //si el inventario es mayor de 0, se registra el movimiento
                    if($inventact <0){
                        //upc
                        $upcact = $invs2[$j][0];
                        //cadena
                        $cadact=$invs2[$j][2];
                        $usu= $_SESSION['username'];
                        $queryi = "INSERT INTO inventario (upc,cadena,ref,status,cajas,almacen,fecha,dadealta,num_confir)
                        VALUES ($upcact,$cadact,$ordenact,2,$inventact,2,$fechaconf,'$usu',$noconf)";
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
    // sí se pudo lograr la inserción--------------------------------
    
            // se añade registro a la tabla de confirmaciones
            //FALTA MODIFICAR FLUJO PARA INCLUIR LA FACTURA EN ESTA ETAPA.
             $queryc = "INSERT INTO confirmaciones (num_confir,orden,fecha_confir,hora_confir,confirma,usu)
                        VALUES ($noconf,$ordenact,$fechaconf,$horaconf, $confirma,'$usu')";
                    //llenado de campos. SI NO SE LOGRA, LA PAGINA SE DETIENE
                        $queryc2=mysql_query($queryc)or die ("Error al insertar confirmacion.".mysql_error());
             // imprime la forma de confirmaciones para las ordenes programadas
                    echo '<script type="text/javascript" language="Javascript">
                            window.open("php/rconfir.php");
                            window.alert("Ordenes e inventarios actualizados OK.");
                            document.location = "confirmaciones.php";
                          </script>'; 
                     
                }
//fin del if el status es correcto
}

else {
	echo '<script type="text/javascript" language="Javascript">
           window.alert("Una orden exedio los inventarios existentes, revise.");
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
                    $queryp = ("UPDATE orden_resumen SET status= 20 ,status_maestro = 20, factura = '$factact' WHERE orden = $ordenact ");
            
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


          