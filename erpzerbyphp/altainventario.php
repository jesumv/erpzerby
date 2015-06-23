<?php

//version 1.1 MAYO 16, 2013
//se incluye filtro por cliente

//directiva de la conexion a la base de datos
include_once "php/config.php";

//directiva a la revision de conexion
include_once"php/lock.php";

//directiva de conexion a hoja de funciones de base de datos

include_once"php/auxinvent.php";

// VALIDACIONES PARA LA ELABORACION DE LA LISTA DE INVENTARIOS-------------------------------------------------------------

//revisa si se ha elegido filtro por cliente o status para elaborar la lista de ordenes

if(isset($_POST['enviof'])){
    // construccion de variables de resultados
       $cte = $_POST['cte'];
       
        
  
 // 1.-se eligio la opcion todos  por lo que no hay clausula WHERE en la consulta.
        
        if ($cte == '0'){
            $query1="SELECT upc, cadena, desc1 FROM cat_arts   WHERE 1 ORDER BY upc,cadena";
            $result1 = mysql_query ($query1)
             or die ("Error en al seleccionar inventarios.".mysql_error());;
            $nomcte1 = "todos";
         } 
       
 //2.- Se elige cliente 
 
         if ($cte != '0') {
            $query1="SELECT upc, cadena, desc1 FROM cat_arts   WHERE cliente = $cte  ORDER BY upc,cadena";
            $result1 = mysql_query ($query1)
             or die ("Error en al FILTRAR inventarios.".mysql_error());;
            $result= mysql_query("SELECT nomcorto FROM clientes WHERE id_clientes = $cte");
            $nomcte=mysql_fetch_array($result);
            $nomcte1= $nomcte[0];
            
        }    
            
        
  
        
    
    }
    
else {
    //no se eligieron filtros, se muestran todos los articulo.

            $query1="SELECT upc, cadena, desc1 FROM cat_arts   WHERE 1 ORDER BY upc,cadena";
            $result1 = mysql_query ($query1)
             or die ("Error en al seleccionar inventarios.".mysql_error());;
            $nomcte1 = "todos";
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


<!--SECCION DE TABLA DE ARTICULOS----------------------------------------------------------->
  
             
              <div class='centrares' align="center">
                  <table class = 'n1'border = '3'>
                      <tr>
                          <th>CLIENTE</th>
                      </tr>
                      <tr>
                          <td class='enc'><?php echo "$nomcte1"; ?></td>
                      </tr>
                      
                  </table>
              </div>
     
<!-- construccion de combo de filtrado ---------------------------------------------------------------->    

     <div id ="cajacent">
        <form id= "combodoble" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        
    <?php
           // query de seleccion de combo clientes
            $query2="SELECT id_clientes, nomcorto FROM clientes ORDER BY nomcorto";
            $result2 = mysql_query ($query2);
    ?> 

        Elija el Filtrado: CLIENTE
 <!--el combo de cliente -->
       <select name= "cte" >
        // printing the list box select command
        <option value = '0' >Todos</option>
        <?php
            while($nt=mysql_fetch_array($result2)){//Array or records stored in $nt
            echo "<option value='$nt[id_clientes]'>$nt[nomcorto]</option>";
            }   
        ?>
        
        </select>
       
        <input type="submit" name ="enviof" value="Filtrar"
      </select>    
    </form>

     </div>
          
     <p></p>
     

    <div align="center">
 
    

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

