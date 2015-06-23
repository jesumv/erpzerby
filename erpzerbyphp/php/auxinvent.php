<?php
//ver 1.0 marzo 15 2013
//este archivo contiene las funciones para manejo de inventarios

//esta funcion llena la tabla de inventarios con el contenido de la pagina altainventario.
    
    if(isset($_POST['envioc'])){
        
            if(($_POST['ref'])==""){
                echo '<script type="text/javascript">
                                window.alert("Por favor introduzca una referencia.");
                                document.location = "altainventario.php";
                                document.getElementById("ref").focus();
                            </script>';
               exit;
            }
        

        
            switch (llenainv()) {
                case 0:
                    echo '<script type="text/javascript">
                                window.alert("Inventario añadido correctamente!");
                            </script>';
                    break;
                
                default:
                         echo '<script type="text/javascript">
                                window.alert("Error. No se pudo dar de alta el inventario!");
                                 document.location = "altainventario.php";
                            </script>';
                         creaLog(mysql_error());
                    break;
            }

        
            }
    
 
    
    function llenainv(){
   
  //los valores por defecto para los campos requeridos

    $st = 1;
    
    //lectura de datos iguales en todos los registros
        $login_session=$_SESSION['login_user'];
        $ref = $_REQUEST['ref'];
        $reng = $_SESSION['reng'];
        $fecha=date('c');
    
    
    //inicializacion de variable para el campo de numero de cajas
    
    for($i=0; $i<$reng;$i++) {
                   
            //obtencion del numero de cajas,upc y cadena
            
                $caj = $_REQUEST['c'.$i];
                $upc = $_REQUEST['u'.$i];
                $cad = $_REQUEST['cad'.$i];

    //string de llenado de campos tabla inventario si el cuadro de texto no está vacío
                if($caj != ""){
                    $querya = "INSERT INTO inventario (almacen,upc,cadena,fecha,ref,status,cajas,dadealta) 
                   VALUES (2,'$upc',$cad,'$fecha','$ref',$st,$caj,'$login_session')";
             
    //lenado de campos
                   
                    $resulta1=mysql_query($querya) or die("Error en alta inventario: ".mysql_error());
                    
                     if(!$resulta1){
    //el registro no se inserto correctamente------------------------------------------------------------------
                        return 1;
                    
                    }
                   
                         
 // fin de la condicion la celda cantidad no esta vacia------------------------------------------------------                   
                     }
         
//fin del ciclo for  para todos los renglones
}

return 0;
      
                         
//fin de la funcion llenainv-----------------------------------------------------------------------------------
}    

    


?>

