<?php
//directiva de la conexion a la base de datos
include_once "php/config.php";
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php";
//directiva de llamada al llenado de tablas
include_once "php/llenatablas.php";
//directiva a la revision de conexion
include_once"php/lock.php";

//declaracion para el arreglo de upcs y nombres de cadena a mostrar
$lupc = array();
$lcadn = array();

// VALIDACIONES PARA LA ELABORACION DE LA LISTA DE INVENTARIOS-------------------------------------------------------------

//revisa si se ha elegido filtro por cliente o status para elaborar la lista de ordenes

if(isset($_POST['enviof'])){
    // construccion de variables de resultados
    
       $cte = $_POST['cte'];
       
        
  
 // 1.-se eligio la opcion todos por lo que no hay clausula WHERE en la consulta.
        
        if ($cte == '0'){
            $resultf = mysql_query("SELECT t1.upc,t2.nombre, t1.desc1  FROM cat_arts AS t1
             INNER JOIN cadenas AS t2 ON t1.cadena=t2.id_cadenas WHERE 1")
             or die ("Error en al seleccionar inventarios.".mysql_error());
            $nomcte1 = "todos";
         }
        
        
       
 //2.- Se elige cliente 
 
         if ($cte != '0') {
            $resultf = mysql_query(" SELECT t1.upc,t2.nombre, t1.desc1  FROM cat_arts AS t1
             INNER JOIN cadenas AS t2 ON t1.cadena=t2.id_cadenas WHERE t1.cliente = $cte ")
            or die ("Error al filtrar inventarios.".mysql_error());
            $result= mysql_query("SELECT nomcorto FROM clientes WHERE id_clientes = $cte");
            $nomcte=mysql_fetch_array($result);
            $nomcte1= $nomcte[0];
        }    
            
        
  
        
    
    }
    
else {
    //no se eligieron filtros, se muestran todos los articulos al entrar  a la pagina. 
    
       $resultf =mysql_query("SELECT t1.upc,t2.nombre, t1.desc1  FROM cat_arts AS t1
             INNER JOIN cadenas AS t2 ON t1.cadena=t2.id_cadenas WHERE 1")
             or die ("Error en al seleccionar inventarios.".mysql_error());
         $nomcte1 = "todos";
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
  <?php 
  $titulo = "CONSULTA DE INVENTARIOS ";
  include_once "include/barrasup.php" 
  ?>                  
    

<!--SECCION DE TABLA DE INVENTARIOS----------------------------------------------------------->
  
             
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
       
        <input type="submit" name ="enviof" value="Filtrar"
      </select>    
    </form>

     </div>
          
     <p></p>
     

    <div align="center">
    
    <?php
    
       
    //presentar la tabla de CONSULTA DE INVENTARIOS actualizada en la pagina
    
     
    if (!$resultf) {
        die("Error al seleccionar datos: ".mysql_error());
    }
      
    
    echo "<table border='1'>;
    <tr>";
     
    // printing table headers
    echo "<td>CONS.</td>";
        echo "<td>UPC</td>";
        echo "<td>CADENA</td>";
        echo "<td>ARTICULO</td>";
        echo "<td>EXISTENCIA CAJAS</td>";
    echo "</tr>\n";
    //inicializa numero de renglones
    $reng = 0;
    
    // printing table rows
    while($row = mysql_fetch_row($resultf)){
        //llena el array de no. de upc
            $lupc[$reng]=$row[0];
            $lcadn[$reng]=$row[1];
            
        // el numero de renglon
            $cons = $reng+1;
        echo "<tr>";
    //prints number of row
        echo "<td>$cons</td>";
        // $row is array... foreach( .. ) puts every element
        // of $row to $cell variable
        $col1 = 0;
        foreach($row as $cell){
                echo "<td name = '$row,$cell'>$cell</td>";
             }
          $col1++;
           //consulta para la seleccion del producto 
                    $query = "SELECT SUM(cajas) FROM inventario as t1 INNER JOIN cadenas as t2 ON t1.cadena = t2.id_cadenas
                    WHERE t1.upc = $lupc[$reng] AND t2.nombre = '$lcadn[$reng]'";
                    $curt=mysql_query($query)or die ("Error al seleccionar inventarios curtidores.".mysql_error());
                    $curt2=mysql_fetch_row($curt);
                    $curt3=$curt2[0];
                    echo "<td name = 'curt'.$reng>$curt3</td>";

            echo "</tr>\n";
        $reng++;
     }  
    echo "</table>";
    ?>
        
    
    </div>
    
    <div id="footer"></div>   


</body>


</html>