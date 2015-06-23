<?php

//version 1.0 Marzo 17, 2013.

//directiva de la conexion a la base de datos
include_once "php/config.php";   
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php"; 
//directiva a la revision de conexion
include_once"php/lock.php";
//directiva de inclusion de funciones de bd
include_once"php/llenatablas.php";



//inicializacion de arreglo para tabla de articulos
$tabla = array();

//CONSULTAS SQL-----------------------------------------------------------------------------------------------

//ordenes a programar
        
             $result= mysql_query("SELECT t1.orden,t1.monto_total,t1.comprador,
            t1.formato_tienda,t1.fecha_confir, t1.hora_confir, t1.num_confir FROM orden_resumen as t1 
            WHERE t1.status = 11  ORDER BY t1.fecha_confir,t1.comprador");
            
            
            
//VALIDAR QUE EXISTAN LAS CONSULTAS--------------------------------------------------------------------------------
      if (!$result) {
        die("Error al seleccionar datos: ".mysql_error());
    }
      
      $num_rows = mysql_num_rows($result);
      
      
      


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- links a hojas javascript ---------------------------------------------------->
        <script type="text/javascript" src="js/comunes.js"></script>
        <!-- links a hojas de estilo ---------------------------------------------------->
        <link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
		<title>Programación de Entregas</title>


	</head>

	<body>
	    
	    <!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
    <?php 
  $titulo = "PROGRAMACION DE ORDENES ";
  include_once "include/barrasup.php" 
  ?>   
        


    
  <!--tablas ------------------------------------------------------------------------------->
  
  <!-- presentar la tabla de lista de ordenes actualizada en la pagina-->
  
          <form name = "programa" action = "<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST"  >
          
              <table class="centrada" border = '1' align='center'>
                    <tr>        
                        <th>CONS.</th>
                        <th>ORDEN</th>
                        <th>MONTO</th>
                        <th>ENTREGAR A</th>
                        <th>FORMATO</th>
                        <th>FECHA ENTREGA</th>
                        <th>HORA</th>
                        <th>NO. CONFIR.</th>
                        <th>FACTURA</th> 
                        <th>PROGRAMAR</th>
                    </tr>
            
                    <?php
              //construccion de la tabla principal
                        
                //inicializa numero de renglones
                $reng = 0;
                
                // printing table rows
                while($row = mysql_fetch_row($result))
                {
                    echo "<tr>";
                //prints number of row
                    echo "<td>".($reng+1)."</td>";
                    // $row is array... foreach( .. ) puts every element
                    // of $row to $cell variable
                    $col1 = 0;
                    foreach($row as $cell){
                         if ($col1 == 1){
                             $impact = number_format($cell,2);
                           echo "<td name = '$row,$cell'>$impact</td>";
                         }
                         
                         else {
                            echo "<td name = '$row,$cell'>$cell</td>";
                         }
//se llena un arreglo con los valores de la tabla principal                 
                        $tabla[$reng][$col1] = $cell;
                        
                      $col1++;
                    }
                        echo "<td><input type='text' name = \"fact$reng\" /> </td>";
                        echo "<td> <input type='checkbox' name = \"prog$reng\"/></td>";
                        echo "</tr>\n";
                    $reng++;
                } 


//SECCION DE ACCIONES -----------------------------------------------------------------------------------------------

//se oprimio el boton de la forma
if(isset($_POST['enviop'])){

    global $tabla;    

    programa($num_rows,$tabla);

    
}    

                    ?>
            
              </table>
              <br />
                <div align= "center"><input type="submit" name = 'enviop' value ='Programación OK'/></div>
                
        </form>
        
        <div id="footer">
            

        </div> 
	</body>
</html>
