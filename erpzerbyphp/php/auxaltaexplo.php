<?php
//este archivo contiene las funciones para manejo de iproducción


    
    if(isset($_POST['envioe'])){
        altaexplosion();
        
            }
 //esta funcion llena la tabla de explosion con los materiales de un producto con el contenido de la pagina altaprod.php   
     function altaexplosion(){
         
  //los valores comunes a todos los registros
            $reng = $_SESSION['reng'];
            $upc= $_REQUEST['prod'];
  //ciclo para el llenado de registros por ingrediente
  
            for($i=0; $i<$reng; $i++){
  //si esta vacia tanto la cantidad  no se inserta nada              
               if(!empty($_POST['cant'.$i])){
               $idmat = $_POST['code'.$i];
               $tipo = $_POST['tipo'.$i];
               $cant = $_POST['cant'.$i];
   //string de llenado de campos tabla explosion si el cuadro de texto cantidad no está vacío            
               $querya = "INSERT INTO explosion (upc,id_materiales,tipo_ud,cantidad) 
                          VALUES ($upc, $idmat,$tipo,$cant)";
                $resultal=mysql_query($querya)
                 or die("Error en alta de insumos: ".mysql_error()); 
               } 
               
                
            }        
            
             
      
 //VALIDACION DE INSERCION                      
                             if($resultal){
            //el registro se inserto correctamente------------------------------------------------------------------
                                echo '<script type="text/javascript">
                                       window.alert("Las cantidades se añadieron corectamente");
                                        document.location = "altaexplosion.php";
                                   </script>';
                            
                            }
                             
                             else{
             //No se pudo lograr la insercion, crea una entrada en el log------------------------------
                             
                                    echo '<script type="text/javascript">
                                       window.alert("Error. No se pudo dar de alta la producción!");
                                        document.location = "altaprod.php";
                                    </script>';
                                    
                                  creaLog(mysql_error());
        
                             }
        
 
                         
//--fin de la funcion altaexplosion--------------------------------------------------------------------------------------------------------------
    }



?>