<?php
//este archivo contiene las funciones para manejo de iproducción


    
    if(isset($_POST['enviop'])){
        altaprod();
        
            }
 //esta funcion llena la tabla de inventarios, con el alta, y da de baja los materiales con el contenido de la pagina altaprod.php 
 //OJO: SE ESTA FORZANDO A QUE LA CADENA SEA 99. SI SE DAN DE ALTA PRODUCTOS PARA OTRA CADENA, REVISAR ESTE DATO.  
     function altaprod(){
            $login_session=$_SESSION['login_user'];
            $fecha = $_REQUEST['date1'];
            $upc= $_REQUEST['prod'];
            $caj = $_REQUEST['cant'];
            $ref = 'alta autom prod';
  //el status del movimiento es 1 = permanenente
            $st= 1;
            
            
                
            //string de llenado de campos tabla inventario si el cuadro de texto cantidad no está vacío
                        if($caj != ""){
                            $querya = "INSERT INTO inventario (cadena,almacen,upc,fecha,ref,status,cajas,dadealta) 
                           VALUES (99,1,$upc,'$fecha','$ref',$st,$caj,'$login_session')";
                     
            //lenado de campos
                           
                            $resultal=mysql_query($querya) or die("Error en alta inventario por produccion: ".mysql_error());
//DISMINUCION DE INVENTARIOS DE MATERIALES---------------------------------
        //seleccionar los materiales del producto elaborado
                            $querye= "SELECT id_materiales, cantidad from explosion WHERE upc= $upc";
                            $resulta2=mysql_query($querye)or die("Error en consulta de insumos: ".mysql_error());
        //ciclo para disminuir cada insumo
                            while ($ins=mysql_fetch_row($resulta2)) {
                                $id=$ins[0];
                                $cant2=$ins[1]*$caj*-1;
   //query de insercion en la tabla de inventarios
                                   $queryiv= "INSERT INTO inv_mats (idmateriales,fecha,ref,cant,dadealta) 
                                   VALUES ($id,'$fecha','cons prod',$cant2,'$login_session')";
                                   $resulta3=mysql_query($queryiv)or die("Error en dism. materiales: ".mysql_error());                        
                                   
                            }
                            
            
            
             
      
 //VALIDACION DE INSERCION                      
                             if($resultal && $resulta3){
            //el registro se inserto correctamente------------------------------------------------------------------
                                echo '<script type="text/javascript">
                                        window.alert("La producción de añadió corectamente");
                                        document.location = "altaprod.php";
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
        
 //fin de la validacion de que exista una cantidad de producto (if)-----------------------------------------------       
                }


      
                         
//--fin de la funcion altaprod--------------------------------------------------------------------------------------------------------------
    }

//esta funcion toma como entradas el upc del articulo y la cantidad de cajas producidas
//busca su composición, y reduce los inventarios de cada material     
        function disminv($clave,$cajas){

            //consulta de selección de materiales
            //¿poner en explosión un campo para definir si la cantidad es por unidad, o hay que hacer conversión?
            //¿y upc?
            $query="SELECT idmateriales,unidad,cantidad FROM explosion WHERE idcat_arts = $clave";
            $result =mysql_query($query) or die("Error en selección de insumos: ".mysql_error());

            return array();
        }

?>