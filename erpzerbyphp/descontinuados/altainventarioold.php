<?php

//version 1.0 marzo 15, 2013

//directiva de la conexion a la base de datos
include_once "php/config.php";

//directiva a la revision de conexion
include_once"php/lock.php";

//directiva de conexion a hoja de funciones de base de datos

include_once"php/auxinvent.php";


// query de seleccion filas con upc
   $query1="SELECT upc, cadena, desc1 FROM cat_arts  ORDER BY upc,cadena";
   $result1 = mysql_query ($query1);
   
//validacion de la existencia de la consulta  
       if (!$result1) {
        die("Error al leer articulos: ".mysql_error());
    }
    
    


   
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
<script type="text/javascript" src="js/comunes.js"></script>
<title>Zerby Intranet</title>
</head>

<body

<!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
    <?php 
  $titulo = "ALTA DE INVENTARIO ";
  include_once "include/barrasup.php" 
  ?>   
    

     <div id="centra" align="center">
         
         <form id = 'alta' method = 'POST'>
               <label for 'ref'>REFERENCIA</label><input type="text" name="ref" id="ref"/> 
               <table id='tabla1' border = '1'>
                   <tr><th>UPC</th><th>CAD</th><th>DESCRIPCION</th><th>CAJAS</th></tr>
                    <?php
        
                    // printing table rows
                    $cont1 = 0;
                    while($row = mysql_fetch_row($result1)){
                        echo "<input type = 'hidden' name = 'ren[]' value= $cont1/>";
                        echo "<input type = 'hidden' name = 'u$cont1' value= '$row[0]'/>";
                        echo "<input type = 'hidden' name = 'cad$cont1' value= '$row[1]'/>";    
                        echo "<tr>";
        
                         foreach($row as $cell){
                             
                             echo "<td>$cell</td>";
                             
                         }
                         
                         
                             echo "<td><input type = 'text' name = 'c$cont1'/></td>";
                         
                        echo "</tr>\n"; 
        
                        $cont1++;  
                    }
                        $_SESSION['reng'] = $cont1;
                        
                        ?>
                   
               </table>
               
              
               
             <!--------el boton de enviar ------------->  
             <br />
             <br />
                    <input type="submit" name ="envioc" value="Alta" />
            
            
        </form>

         
     </div>  
   

<div id="footer"></div>


</body>


</html>

