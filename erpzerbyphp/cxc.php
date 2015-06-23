<?php
//directiva a la conexion con base de datos
include_once "php/config.php";
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php";
// inicio de sesion para variables globales
session_start();

//consulta de seleccion de datos

 $resultc = mysql_query("SELECT t1.orden,t1.tipo_orden,t1.monto_total,t1.no_partidas,t1.formato_tienda,
 t1.fecha_orden,t1.fecha_canc,t2.descr FROM orden_resumen as t1 INNER JOIN status as t2 
        on t1.status = t2.id_status WHERE t1.cliente_zerby = 1 ");

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

		<title>Dist. Confe</title>

	</head>

	<body>
	<h1>COBRANZA</h1>
	<div  align="center">
    
    <?php
    
       
    //presentar la tabla de lista de ordenes actualizada en la pagina
    
     
    if (!$resultc) {
        die("Error al seleccionar datos: ".mysql_error());
    }
    
    $fields_num = mysql_num_fields($resultc);
    
    
    
    echo "<table border='1'>
    
    <tr>";
    // printing table headers
    echo "<td>CONS.</td>";
    echo "<td>ORDEN</td>";
    echo "<td>TIPO</td>";
    echo "<td>MONTO TOTAL</td>";
    echo "<td>PARTIDAS</td>";
    echo "<td>FORMATO TIENDA</td>";
    echo "<td>FECHA ORDEN</td>";
    echo "<td>FECHA CANC</td>";
    echo "<td>STATUS</td>";
    
    
    echo "</tr>\n";
    //inicializa numero de renglones
    $reng = 1;
    
    // printing table rows
    while($row = mysql_fetch_row($resultc))
    {
        echo "<tr>";
    //prints number of row
        echo "<td>$reng</td>";
        // $row is array... foreach( .. ) puts every element
        // of $row to $cell variable
        $col1 = 0;
        foreach($row as $cell){
             if ($col1 == 2){
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

	</body>
</html>
