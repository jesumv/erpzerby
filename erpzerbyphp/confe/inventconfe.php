<?php
//directiva de la conexion a la base de datos
include_once "../php/config.php";
//directiva al archivo de funciones auxiliares
include_once "../php/funaux.php";
//directiva de llamada al llenado de tablas
include_once "../php/llenatablas.php";
//directiva a la revision de conexion
include_once"../php/lock.php";

//declaracion para el arreglo de upcs a mostrar
$lupc = array();

//consulta de producto terminado 
$resultf = mysql_query("SELECT t1.upc,t1.desc1, SUM(t2.cajas)  FROM cat_arts AS t1
         LEFT JOIN  inventario AS t2 ON t1.upc= t2.upc WHERE t1.produccion = 1 GROUP BY t1.id_art")
         or die ("Error al seleccionar inventarios curtidores.".mysql_error());
         
//consulta de materiales
$resultd  = mysql_query("SELECT t1.upc, t1.descrip, t1.presentacion, t1.cantidad, t1.unidad, SUM(t2.cant)  
FROM materiales AS t1 LEFT JOIN  inv_mats AS t2 ON t1.idmateriales= t2.idmateriales  
GROUP BY t1.idmateriales")or die ("Error al seleccionar materiales.".mysql_error());

$fechamax1 =mysql_query("SELECT MAX(fecha)FROM inventario");
$fechamax2 =mysql_fetch_row($fechamax1);

        
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<script type="text/javascript" src="../js/comunes.js"></script>
		<title>Confe</title>
		<link rel="stylesheet" type="text/CSS" href="../css/2columna.css" />
	</head>

	<body>
		
		<?php 
			  $titulo = "CONSULTA DE INVENTARIOS ";
			  include_once "../include/barrasup2.php" 
		?> 
        
        <p >
            <h2 align="center">Fecha de actualización: <?php echo $fechamax2[0]?></h2>	
	    </p>
	    
      <div id="twocolumn">
          
		
    		<div id="colizq">
    		    <h2>PRODUCTO TERMINADO</h2>
        
                <?php
            
               
            //presentar la tabla de CONSULTA DE INVENTARIOS actualizada en la pagina
            
            echo "<table border='1'>";    
            echo "<tr>";
            // printing table headers
                echo "<td>CONS.</td>";
                echo "<td>UPC</td>";
                echo "<td>ARTICULO</td>";
                echo "<td>EXISTENCIA CAJAS</td>";       
            echo "</tr>\n";
            //inicializa numero de renglones
            $reng = 0;
            
            
            //imprime las filas
            while($row = mysql_fetch_row($resultf)){
     //llena el array de no. de upc
            $lupc[$reng]=$row[0];
      // el numero de renglon
                $cons = $reng+1;
     
                echo "<tr>";
                echo "<td>$cons</td>";
                
                // $row is array... foreach( .. ) puts every element
                // of $row to $cell variable

                foreach($row as $cell){
                        echo "<td name = '$row,$cell'>$cell</td>";
                     }
                    echo "</tr>\n";
                $reng++;
             }  
            echo "</table>";
            ?>
 
        </div>
<!--fin de la columna izquierda ------------------>          
        
        
        <div id="colder">
         <h2>MATERIALES</h2>  
         <?php
         
            //presentar la tabla de CONSULTA DE INVENTARIOS actualizada en la pagina
        
            echo "<table border='1'>";
   //la fila para existencia         
            echo "</tr>";
            // printing table headers
                echo "<td>CONS.</td>";
                echo "<td>CLAVE</td>";
                echo "<td>ARTICULO</td>";
                echo "<td colspan='3'>PRESENTACION</td>";
                 echo "<td>EXISTENCIA</td>";
            echo "</tr>\n";
            //inicializa numero de renglones
            $reng2= 1;
            
            // printing table rows
            while($row = mysql_fetch_row($resultd))
            {
                echo "<tr>";
            //prints number of row
                echo "<td>$reng2</td>";
                // $row is array... foreach( .. ) puts every element
                // of $row to $cell variable
                foreach($row as $cell){
                        if($cell==$row[5]){
                           $celmod=number_format($row[5],0);
                           echo "<td name = '$reng,$cell'>$celmod</td>";   
                        }
                        
                        else {
                          echo "<td name = '$reng,$cell'>$cell</td>";  
                        }
                        
                     }
        
                    echo "</tr>\n";
                $reng2++;
             }  
            echo "</table>";
        ?>
            
        </div>
 <!--FIN DE LA COLUMNA DERECHA------------------------------------------------------->
    </div>
 <!--fin de la division 2 columnas---------------------------------------------------->  
    <div id="footer"></div>       

	</body>
</html>
