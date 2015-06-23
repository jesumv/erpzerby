<?php
//este archivo contiene las funciones para manejo de inventarios

//esta funcion llena la tabla de inventarios con el contenido de la pagina altainventario.
    
    if(isset($_POST['envios'])){
        //validar si se ha elegido tipo de movimiento ++ pendiente
        salida();
        
            }
    
 
    
    function salida(){
        
    if(!isset($_REQUEST['orden'])){
            
        } 
    
    //lectura de datos comunes en todos los registros
        $login_session=$_SESSION['login_user'];
    //el numero de renglones en la tabla de la hoja de datos
        $reng = $_SESSION['reng'];
    //la fecha proveniente del calendario
        $fecha = $_REQUEST['date1'];
        $orden = $_REQUEST['orden'];
        
                

    //repasa cada renglon de la hoja de datos
    for ($r=0;$r<$reng;$r++){
                    //si el contenido de la celda cantidad no esta vacio, se intenta ingresar el registro
                    
        if(!empty($_POST['c'.$r])){
            //el codigo del producto
            $upc= $_POST['upc'.$r];
            //el numero de cajas
            $cant = -$_POST['c'.$r];
            
                //insercion de las cantidades de salida                    
                   $queryb = "INSERT INTO inventario (cadena,almacen,upc,fecha,ref,status,cajas,dadealta) 
                   VALUES (1,2,'$upc','$fecha',$orden,1,'$cant','$login_session')";
                   $resultal=mysql_query($queryb) or die("Error en movimientos de  inventario: ".mysql_error());

             //lenado de campos
                   
                    
                    
                     if($resultal){
    //el registro se inserto correctamente------------------------------------------------------------------
                        echo '<script type="text/javascript">
                                window.alert("Salida efectuada correctamente!");
                                document.location = "salewm.php";
                            </script>';
                    
                    }
                   
                         
                      
                     else{
                     //No se pudo lograr la insercion, crea una entrada en el log------------------------------
                     
                            echo '<script type="text/javascript">
                                window.alert("Error. No se pudo efectuar la salida!");
                                 document.location = "salewm.php";
                            </script>';
                            
                           creaLog(mysql_error());

                     }
             
        }
    }
    

             
    // fin de la funcion transpaso------------------------------------------------------------------------------                    
                     }
         
    


?>

