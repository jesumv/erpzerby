<?php
//este archivo contiene las funciones para manejo de inventarios

//esta funcion llena la tabla de inventarios con el contenido de la pagina altainventario.
    
    if(isset($_POST['enviom'])){
        //validar si se ha elegido tipo de movimiento
        movimiento();
        
            }
    
 
    
    function movimiento(){
    
    //lectura de datos comunes en todos los registros
        $login_session=$_SESSION['login_user'];
    //el numero de renglones en la tabla de la hoja de datos
        $reng = $_SESSION['reng'];
    //la fecha proveniente del calendario
        $fecha = $_REQUEST['date1'];
        $signo = $_POST['tipo'];
    

    //repasa cada renglon de la hoja de datos
    for ($r=0;$r<$reng;$r++){
            //algoritmo para obtener el numero de celda con el codigo
            $reng2= ($r*4);
        //si el contenido de la celda cantidad no esta vacio,
        // y el numero de celda es el inicial del renglon, se intenta ingresar el registro

        if(!empty($_POST['c'.$r])){
            //el codigo del producto
            $cod = $_POST['code'.$reng2];
            //la factura del producto
            $fac = $_POST['f'.$r]; 
            //el numero de cajas
            $cant = $_POST['c'.$r]*$signo;
           // las observaciones
            $obs= $_POST['o'.$r];
            //el query de SQL
            $queryb = "INSERT INTO inv_mats (idmateriales,fecha,ref,cant,dadealta,obs) 
                   VALUES ($cod,'$fecha','$fac','$cant','$login_session','$obs')";
             //lenado de campos
                   
                    $resultal=mysql_query($queryb) or die("Error en movimientos de  inventario: ".mysql_error());
                    
                     if($resultal){
    //el registro se inserto correctamente------------------------------------------------------------------
                        echo '<script type="text/javascript">
                                window.alert("Movimiento efectuado correctamente!");
                                document.location = "movinvent.php";
                            </script>';
                    
                    }
                   
                         
                      
                     else{
                     //No se pudo lograr la insercion, crea una entrada en el log------------------------------
                     
                            echo '<script type="text/javascript">
                                window.alert("Error. No se pudo efectuar el movimiento!");
                                 document.location = "movinvent.php";
                            </script>';
                            
                           creaLog(mysql_error());

                     }
             
        }
    }
    

             
    // fin de la funcion movint ------------------------------------------------------------------------------                    
                     }
         
    


?>

