<?php
//este archivo contiene las funciones para manejo de inventarios

//esta funcion llena la tabla de inventarios con el contenido de la pagina altainventario.
    
    if(isset($_POST['enviot'])){
        //validar si se ha elegido tipo de movimiento
        transpaso();
        
            }
    
 
    
    function transpaso(){
    
    //lectura de datos comunes en todos los registros
        $login_session=$_SESSION['login_user'];
    //el numero de renglones en la tabla de la hoja de datos
        $reng = $_SESSION['reng'];
    //la fecha proveniente del calendario
        $fecha = $_REQUEST['date1']; 

    //repasa cada renglon de la hoja de datos
    for ($r=0;$r<$reng;$r++){
                    //si el contenido de la celda cantidad no esta vacio, se intenta ingresar el registro
                    
        if(!empty($_POST['c'.$r])){
            //el codigo del producto
            $upc= $_POST['upc'.$r];
            //el numero de cajas
            $cant = $_POST['c'.$r];
            
                //ciclo para hacer lal insercion 2 veces, 1 por cada almacen
                for ($a=1;$a<3;$a++){
                   $dato = ($a == 1? $signo= '-': $signo ='');
                   $cant = $dato.$_POST['c'.$r];
                   $queryb = "INSERT INTO inventario (cadena,almacen,upc,fecha,ref,status,cajas,dadealta) 
                   VALUES (99,'$a','$upc','$fecha','traspaso',1,'$cant','$login_session')";
                   $resultal=mysql_query($queryb) or die("Error en movimientos de  inventario: ".mysql_error());
                }

             //lenado de campos
                   
                    
                    
                     if($resultal){
    //el registro se inserto correctamente------------------------------------------------------------------
                        echo '<script type="text/javascript">
                                window.alert("Traspaso efectuado correctamente!");
                                document.location = "transpaso.php";
                            </script>';
                    
                    }
                   
                         
                      
                     else{
                     //No se pudo lograr la insercion, crea una entrada en el log------------------------------
                     
                            echo '<script type="text/javascript">
                                window.alert("Error. No se pudo efectuar el traspaso!");
                                 document.location = "transpaso.php";
                            </script>';
                            
                           creaLog(mysql_error());

                     }
             
        }
    }
    

             
    // fin de la funcion transpaso------------------------------------------------------------------------------                    
                     }
         
    


?>

