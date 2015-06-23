<?php
//version 1.0 agosto 12 2013.
//esta hoja recolecta los datos de la hola solicita.php, y realiza las consultas a la base de datos-

//directiva con funciones de conversion de datos
    include_once"conversiones.php";
//la directiva a las funciones auxiliares
    include_once"funaux.php";
       
function llenabases($colscheck, $ordenes, $rengcheck,$invs2,$formtit){
//determinación de la hora de creación del registro ---------
    $fechaact = date(DATE_ATOM);
//recolección de datos de la página--------------------------------------------------------------------------------------------
// toma la variable formato para el titulo de la pagina de impresión
 $_SESSION['formato']=$formtit;
 $usu= $_SESSION['username'];
 
//ciclo para revisar si los checklist están  activados
for($i=0; $i<$colscheck;$i++){
//si el check esta encendido, procesa--------------------------      
        if(isset($_POST['check'.$i])){
            
//cambia los valores en la tabla html por los que se van entregar. funcion en funaux.php
            cambiatablas($i,$rengcheck,$invs2);
 //toma variables para la consulta           
            $ordenact = $ordenes[$i];
            
//revisa si la entrega esta completa en funaux.php
            $estado=completa($ordenact,$invs2,$i);

//establece el valor del estado en el que queda la orden después de la entrega
            switch ($estado) {
                case 0:
            //orden surtida completa
                    $status = 6;
                    break;
                case 2:
            //las cantidades excederian los articulos existentes
                    $status = 9999;
                    break;
            //la orden se surte parcial    
                default:
                    $status = 4;
                    break;
            }

//Si el status es correcto, se hacen las consultas---------------------
if ($status!=9999) {

    //inserción de registro en  base confirmaciones-------------------------------------------------------------------------------------
                $queryc = "INSERT INTO confirmaciones (status_sol,orden,usu_sol)
                            VALUES (0,$ordenact,'$usu')";
                        //llenado de campos
                            $result1=mysql_query($queryc)or die ("Error al insertar solicitud.".mysql_error());
    //lectura del numero de solicitud registrada
                $sol1= "SELECT * FROM confirmaciones ORDER BY idconfirmaciones DESC LIMIT 1";
                $sol2= mysql_query($sol1)or die ("Error al leer confirmaciones.".mysql_error());
                $sol3 =mysql_fetch_array($sol2);
                $sol4= $sol3[0];
     //inserción de registros en inventarios---------------------------------------------------------------------------------------
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
                            $queryi = "INSERT INTO inventario (upc,cadena,ref,status,cajas,almacen,fecha,dadealta)
                            VALUES ($upcact,$cadact,$sol4,2,$inventact,2,NOW(),'$usu')";
                        //llenado de campos
                            $result2=mysql_query($queryi)or die ("Error al actualizar inventario4.".mysql_error());
                        }
                        
                        else{$result2="";}
                            
                    }            
      
                //actualizacion de la orden de tabla orden_resumen ------------------------------------------------------------------------------------------
                $queryf = ("UPDATE orden_resumen SET status= $status, status_maestro = $status WHERE orden = $ordenact ");
            
                //llenado de campos
               
                $result3=mysql_query($queryf)or die ("Error actualizacion de estado.".mysql_error());
                
                //actualizacion de la confirmacion en la tabla confirmaciones
                
                 $queryac = ("UPDATE confirmaciones SET status_sol= 10 WHERE idconfirmaciones = $sol4 ");
                 $result4=mysql_query($queryac)or die ("Error actualizacion de solicitud.".mysql_error());
                
  //validacion de la insercion
                if(!$result1){
  //No se pudo lograr la actualizacion de ordenes, crea una entrada en el log----------
                   //creaLog(mysql_error());
                         echo '<script type="text/javascript">
                          window.alert("La confirmacion no se pudo insertar.");
                        </script>';     
                         return 1;
                    }
             
             elseif($result2=""){
  //No se pudo lograr la insercion de inventarios, crea una entrada en el log----------
                   creaLog(mysql_error());    
                       echo '<script type="text/javascript">
                          window.alert("Los inventarios no se pudieron actualizar.");
                           document.location = "solicita.php";
                        </script>';
                         return 2;
                    }
 //no se pudo actualizar el estado de la orden
            elseif(!$result3 || !$result4){
    
            creaLog(mysql_error());    
                       echo '<script type="text/javascript">
                          window.alert("Las ordenes no se pudieron actualizar.");
                           document.location = "solicita.php";
                        </script>';
                         return 3;
    
            }
             
              
     //-----------fin del if no se pudo lograr la insercion
               else{
    // sí se pudo lograr la inserción--------------------------------
            
             // imprime la forma de confirmaciones para las ordenes solicitadas
                    echo '<script type="text/javascript" language="Javascript">
                            window.open("php/rsol.php");
                            window.alert("Ordenes e inventarios actualizados OK.");
                            document.location = "solicita.php";
                          </script>'; 
                     
                }
//fin del if el status es correcto
}

else {
    echo '<script type="text/javascript" language="Javascript">
           window.alert("Una orden exedio los inventarios existentes, revise.");
           document.location = "solicita.php";
          </script>'; 
}                 
//fin del isset                       
                 
            }
            
//fin del for columnas
        }
                        return 0;
//fin de la funcion
}

?>