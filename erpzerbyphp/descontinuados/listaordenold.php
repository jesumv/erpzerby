<?php
//directiva de la conexion a la base de datos
include_once "php/config.php";
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php";
//directiva de llamada al llenado de tablas
include_once "php/llenatablas.php";
//directiva a la revision de conexion
include_once"php/lock.php";



//---------------------------------rutinas

//VALIDACIONES PARA LA SUBIDA DE ARCHIVOS.----------------------------------------------------------------------

//revisa si se ha llenado el campo de eleccion del archivo a subir ----------------------------------------------

     if(isset($_POST['envio'])){
         
        $cteup = $_POST['cteup'];       
        $rutasub = "uploads/";
        $archivo = $rutasub.basename( $_FILES['uploadedfile']['name']); 
        
        
        
        //valida si el archivo es de tipo csv.-------------------------------------------------
        
        if(substr($_FILES['uploadedfile']['name'],-3) != "csv"){
            echo '<script type="text/javascript">
                          window.alert("El archivo elegido no tiene formato CSV. No se subirá.");
                    </script>';
        }
            
        else if(exisarch($archivo)==false){
             
        //---------el archivo elegido no existe en la base de datos. se procede a subirlo------------------
        
                
           // Where the file is going to be placed 
            $target_path = "uploads/";
            
            /* Add the original filename to our target path.  
            Result is "uploads/filename.extension" */
            $target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
            
            
            //call to the move:uploaded file function
            // java script from http://forums.phpfreaks.com/topic/213143-php-message-box-popup/
            
            
            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
  // si se pudo subir el archivo se intenta llenar las tablas y se avisa del resultado.
  
                switch (llenatablas($target_path, $login_session,$cteup)) {
                    case 0:
                        
                        echo '<script type="text/javascript">
                            window.alert("Archivo añadido correctamente!");
                        </script>';
                        
                        break;
                        
                    case 1:
                        
                        echo '<script type="text/javascript">
                            window.alert("El archivo no se pudo abrir!");
                        </script>';
                        break;
                        
                    case 2:
                        
                        echo '<script type="text/javascript">
                            window.alert("Formato de tienda desconocido!");
                        </script>';
                        break;
                        
                    case 3:
                        
                        echo '<script type="text/javascript">
                            window.alert("La orden de compra ya existia en catalogo!");
                        </script>';
                        break;
                        
                    case 4:
                        
                        echo '<script type="text/javascript">
                            window.alert("No se pudo insertar orden resumen!");
                        </script>';
                        break;
                        
                     case 5:
                        
                        echo '<script type="text/javascript">
                            window.alert("No se pudo insertar orden detalle!");
                        </script>';
                        break;
                        
                    
                    default:
                       echo '<script type="text/javascript">
                            window.alert("ERROR DESCONOCIDO!");
                        </script>';
                        break;
                }
             
                    
            } 
            
            else{
                echo '<script type="text/javascript">
                             window.alert("Error al subir el archivo. por favor intente de nuevo.");
                        </script>';
                        
            }
           
 //----------------------fin del if  move uploades         
         }

         else{
                 //el archivo elegido ya existe. Pone mensaje y regresa
              echo '<script type="text/javascript">
                          window.alert("El archivo elegido ya existe. No se subirá.");
                    </script>';
                    
         }

        
        
     }
//--------------------------------------------------fin de acciones si se oprimio boton-----------------------------

// VALIDACIONES PARA LA ELABORACION DE LA LISTA DE ORDENES-------------------------------------------------------------

//revisa si se ha elegido filtro por cliente o status para elaborar la lista de ordenes

if(isset($_POST['enviof'])){
    // construccion de variables de resultados
    
       $cte = $_POST['cte'];
       $stt = $_POST['stt'];
       
        
  
 // 1.-se eligio la opcion todos en ambos combos por lo que no hay clausula WHERE en la consulta.
        
        if ($cte == '0' && $stt =='0'){
           $resultf = mysql_query("SELECT t1.orden,t1.tipo_orden,t1.depto,t1.monto_total,t1.promocion,t1.no_partidas,t1.comprador,
        t1.formato_tienda,t1.fecha_orden,t1.fecha_canc,t1.p_desc,t2.descr FROM orden_resumen as t1 INNER JOIN status as t2 
        on t1.status = t2.status ")or die ("Error en la seleccion de ordenes.".mysql_error()); 
            $nomcte1 = "todos";
            $nomstt1 = "todos";
         }
        
        
       
 //2.- Se elige cliente y todos los status
 
         if ($cte != '0' && $stt =='0') {
            $resultf = mysql_query("SELECT t1.orden,t1.tipo_orden,t1.depto,t1.monto_total,t1.promocion,t1.no_partidas,t1.comprador,
            t1.formato_tienda,t1.fecha_orden,t1.fecha_canc,t1.p_desc,t2.descr FROM orden_resumen as t1 INNER JOIN status as t2 
            on t1.status = t2.status WHERE t1.cliente_zerby = $cte");
            $result= mysql_query("SELECT nomcorto FROM clientes WHERE id_clientes = $cte")
            or die ("Error en el filtrado de cliente.".mysql_error());
            $nomcte=mysql_fetch_array($result);
            $nomcte1= $nomcte[0];
            $nomstt1 = "todos";
        }    
            
        
 
 //3.- Se elige status y todos los clientes
        if ($cte == '0' && $stt !='0') {
            $resultf = mysql_query("SELECT t1.orden,t1.tipo_orden,t1.depto,t1.monto_total,t1.promocion,t1.no_partidas,t1.comprador,
            t1.formato_tienda,t1.fecha_orden,t1.fecha_canc,t1.p_desc,t2.descr FROM orden_resumen as t1 INNER JOIN status as t2 
            on t1.status = t2.status WHERE t1.status = $stt");
            $result = mysql_query("SELECT descr FROM status WHERE status = $stt")
            or die ("Error en el filtrado de status.".mysql_error());
            $nomstt=mysql_fetch_array($result);
            $nomstt1=$nomstt[0];
            $nomcte1 = "todos";
            
        }  
        

 
 //4.- Se eligen tanto cliente como status  
       
        
       if ($cte != '0' && $stt !='0') {
            $resultf = mysql_query("SELECT t1.orden,t1.tipo_orden,t1.depto,t1.monto_total,t1.promocion,t1.no_partidas,t1.comprador,
            t1.formato_tienda,t1.fecha_orden,t1.fecha_canc,t1.p_desc,t2.descr FROM orden_resumen as t1 INNER JOIN status as t2 
            on t1.status = t2.status WHERE t1.cliente_zerby = $cte AND t1.status = $stt")
            or die ("Error en el filtrado de ambos campos.".mysql_error());
            
             $result1 = mysql_query("SELECT descr FROM status WHERE status = $stt");
             $nomstt=mysql_fetch_array($result1)
             or die ("Error en el filtrado de descripcion.".mysql_error());
             $nomstt1 = $nomstt[0];
             
             $result2 = mysql_query("SELECT nombre FROM clientes WHERE id_clientes = $cte");
             $nomcte=mysql_fetch_array($result2)
             or die ("Error en el filtrado de cliente.".mysql_error());
             $nomcte1= $nomcte[0];
        }
    
    }
    
else {
    //no se eligieron filtros, se muestran todas las ordenes al entrar a la pagina. ok
    
        $resultf = mysql_query("SELECT t1.orden,t1.tipo_orden,t1.depto,t1.monto_total,t1.promocion,t1.no_partidas,t1.comprador,
        t1.formato_tienda,t1.fecha_orden,t1.fecha_canc,t1.p_desc,t2.descr FROM orden_resumen as t1 INNER JOIN status as t2 
        on t1.status = t2.status")or die ("Error en al seleccionar ordenes.".mysql_error());
         $nomcte1 = "todos";
         $nomstt1 = "todos";
        }
        
     



?>

<!--CONSTRUCCION DE LA PAGINA --------------------------------------------------------------------------------------->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="js/comunes.js"></script>
<title>INTRANET ZERBY</title>
<link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
</head>

<body>
 <!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->
            
    <div id="bandasup">
        
        <div id="zerby">
         <h1>Zerby Entregas Oportunas</h1>
       </div>  
         
    <!-- CONSTRUCCION DE CAJA DE ELECCION DE ARCHIVO A SUBIR ------------------------------------->
        <!--
        <div float="right" style= "width:750px; padding-left:3px ;margin-left:10px">
        //-->
            <div id="actionbox" >
                <div style="background-color:#333333; color:#FFFFFF; padding:3px;"><b>SUBIR NUEVA ORDEN</b></div>
                <div style="margin:10px">
    
             <form id = "subir" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
                Seleccione el archivo:
                <input name="uploadedfile" type="file" /> 
                    <?php
                           // query de seleccion de combo clientes
                            $query="SELECT id_clientes,nomcorto FROM clientes ORDER BY nomcorto";
                            $result1 = mysql_query ($query);
                    ?> 
        
                        CLIENTE:
                        <!--el combo de cliente -->
                       <select name= "cteup" >
                        // printing the list box select command
                        
                    <?php
                        //llenado del combo cliente
                        while($nt=mysql_fetch_array($result1)){//Array or records stored in $nt
                        echo "<option value='$nt[id_clientes]'>$nt[nomcorto]</option>";
                        }   
                    ?>
                        </select> 
       
            <input type="submit" name ="envio" value="Subir archivo" />
            </form>
     
                </div>
        </div>
                    <div id="logoprinc" >
                          <img  src="img/logozerby.jpg" alt="logo zerby" width="120px" height="80px">  
                     </div>
                     
                     <h3 id="saluda">Bienvenido, <?php echo $_SESSION['username']; ?></h3>
                      
     </div>
                    
<!--menu de navegacion ------------------------------------------------------------------------------->
        <?php 
              include_once "include/menu1.php" 
          ?>                 

    

<!--SECCION DE LISTA DE ORDENES DE EMBARQUE ----------------------------------------------------------->
    <div>
              <h1 id="titpag" align="center">LISTADO DE ORDENES DE EMBARQUE</h1>
    </div>
             
              <div class='centrares' align="center">
                  <table class = 'n1'border = '3'>
                      <tr>
                          <th>CLIENTE</th> <th>STATUS</th>
                      </tr>
                      <tr>
                          <td class='enc'><?php echo "$nomcte1"; ?></td> <td class='enc'><?php echo "$nomstt1"; ?> </td>
                      </tr>
                      
                  </table>
              </div>
     
<!-- construccion de combo de filtrado ---------------------------------------------------------------->    

     <div id ="cajacent">
        <form id= "combodoble" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        
    <?php
           // query de seleccion de combo clientes
            $query="SELECT id_clientes, nomcorto FROM clientes ORDER BY nomcorto";
            $result1 = mysql_query ($query);
    ?> 

        Elija el Filtrado: CLIENTE
 <!--el combo de cliente -->
       <select name= "cte" >
        // printing the list box select command
        <option value = '0' >Todos</option>
        <?php
            while($nt=mysql_fetch_array($result1)){//Array or records stored in $nt
            echo "<option value='$nt[id_clientes]'>$nt[nomcorto]</option>";
            }   
        ?>
        
        </select>
        STATUS
 <!--el combo de status -->  
 <?php
 //query de seleccion de combo status
        $query2="SELECT status, descr FROM status ORDER BY descr";
        $result2 = mysql_query ($query2); 
 ?>
        <select name= "stt" >
        // printing the list box select command
        <option value = '0' >Todos</option>
        <?php
            while($nt=mysql_fetch_array($result2)){//Array or records stored in $nt
            echo "<option value='$nt[status]'>$nt[descr]</option>";
            }   
        ?>
        <input type="submit" name ="enviof" value="Filtrar"
      </select>    
    </form>

     </div>
          
     <p></p>
     

    <div align="center">
    
    <?php
    
       
    //presentar la tabla de lista de ordenes actualizada en la pagina
    
     
    if (!$resultf) {
        die("Error al seleccionar datos: ".mysql_error());
    }
    
    $fields_num = mysql_num_fields($resultf);
    
    
    
    echo "<table border='1'>
    
    <tr>";
    // printing table headers
    echo "<td>CONS.</td>";
    echo "<td>ORDEN</td>";
    echo "<td>TIPO</td>";
    echo "<td>DEPTO</td>";
    echo "<td>MONTO TOTAL</td>";
    echo "<td>PROMOCION</td>";
    echo "<td>PARTIDAS</td>";
    echo "<td>ENTREGAR A</td>";
    echo "<td>FORMATO TIENDA</td>";
    echo "<td>FECHA ORDEN</td>";
    echo "<td>FECHA CANC</td>";
    echo "<td>% DESC</td>";
    echo "<td>STATUS</td>";
    
    
    echo "</tr>\n";
    //inicializa numero de renglones
    $reng = 1;
    
    // printing table rows
    while($row = mysql_fetch_row($resultf))
    {
        echo "<tr>";
    //prints number of row
        echo "<td>$reng</td>";
        // $row is array... foreach( .. ) puts every element
        // of $row to $cell variable
        $col1 = 0;
        foreach($row as $cell){
             if ($col1 == 3){
                 $impact = number_format($cell,2);
               echo "<td name = '$row,$cell'>$impact</td>";
             }
             else {
                echo "<td name = '$row,$cell'>$cell</td>";
             }
          $col1++;
        }
            echo "</tr>\n";
        $reng++;
    }
    
    
    echo "</table>";
    ?>
        
    
    </div>
    
    <div id="footer"></div>   


</body>


</html>