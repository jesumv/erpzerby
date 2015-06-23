<?php
//esta hoja se usa para dar de alta la producción de dulces destellos
    
//directiva de la conexion a la base de datos
include_once "../php/config.php";

//directiva a la revision de conexion
include_once"../php/lock.php";

//directiva de conexion a hoja de funciones alta de producción
include_once"../php/auxaltaexplo.php";


// query de seleccion filas segun el numero de productos registrados
   $query1="SELECT idmateriales, upc, descrip, presentacion,cantidad,unidad,tipocons FROM materiales WHERE 1
   ORDER BY descrip";
   $result1 = mysql_query ($query1);
   
//query de seleccion para el combo de producto a explotar
     $query2="SELECT upc, desc1 FROM cat_arts WHERE produccion = 1";
     $result2 = mysql_query ($query2);
   
//validacion de la existencia de la consulta  
       if (!$result1||!$result2) {
        die("Error al leer materiales: ".mysql_error());
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <script type="text/javascript" src="../js/comunes.js"></script>
        <title>Confe</title>
        <link rel="stylesheet" type="text/CSS" href="../css/plantilla1.css" />
        
    </head>

    <body>
        
        <?php 
              $titulo = "ALTA DE COMPOSICION DE PRODUCTO";
              include_once "../include/barrasup2.php" ;
              
        ?> 
        
<h4 align="center">Introduzca para cada ingrediente del producto el tipo de unidad </h4>
<h4 align="center">y la cantidad en la unidad elegida</h4>

 <!--construcción del combo de eleccion de producto---------------------------------------------------------------------->
        <div id ="cajacent">

                 
                <form id= "altaexplo"   method="post" >
                    
                    <br />
                    Elija el producto elaborado: 
             <!--el combo de producto a explotar -->
                   <select name= "prod" >
                        // printing the list box select command
                        <?php
                            while($nt=mysql_fetch_array($result2)){//Array or records stored in $nt
                            echo "<option value='$nt[upc]'>$nt[desc1]</option>";
                            }   
                        ?>
                         
                    </select>
                        
                     <br />
                     <br />
             
                    
                        <table id="table1" border =1>
                            <tr><th>CLAVE</th><th>UPC</th> <th>ARTICULO</th>
                                <th colspan="3">PRESENTACION</th>
                                <th>TIPO</th>
                                <th colspan="2">CANTIDAD</th>
                            </tr>
                             <?php
        
                            // printing table rows
                             $cont1 = 0; 
                            
                            while($row = mysql_fetch_row($result1)){                                            
  //selección del rotulo de la unidad a introducir
                            $ud= $row[5];
                                
                                echo "<tr>";
                     //celda etiquetada con el no. de renglon con el id de material
                                    echo "<input type = 'hidden' name = 'code$cont1' value= '$row[0]'/>"; 
                                    echo "<input type = 'hidden' name = 'tipo$cont1' value= '$row[6]'/>";
                    //ciclo para insertar una celda por cada campo y registro de la consulta SQL
                                 foreach($row as $cell){ 
                                     echo "<td>$cell</td>";   
                                 }
                   //celdas de informacion              
                                     echo "<td><input type = 'text' name = 'cant$cont1' /></td>";
                    //seleccion de la unidad a introducir
                                switch ($ud) {
                                    case 'PZA':
                                            $texto = "EN PIEZAS";
                                        break;
                                        
                                    case 'KG':
                                            $texto = "EN GRAMOS";
                                        break;  
                                      
                                    case 'M':
                                            $texto = "EN METROS";
                                        break;    
                                    
                                    default:
                                            $texto = "UNIDAD INDEFINIIDA";
                                        break;
                                }
                                
                                     echo "<td>$texto</td>";
                                     
                                echo "</tr>\n"; 
                            $cont1++;  
                            }
                                $_SESSION['reng'] = $cont1;
                                
                                
                                ?>
                        </table>
                    <br />
                <div align= "center"><input type="submit" name = 'envioe' value ='Registrar Movimiento'/></div>
                        
                    
            </form>
            
            
         
                     <br />
                     <br />
                      
                     
        </div>
    
    <div id="footer"></div>       

    </body>
</html>
