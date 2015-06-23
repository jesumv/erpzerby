<?php

//version 1.0 Marzo 17, 2013.

//directiva de la conexion a la base de datos
include_once "php/config.php";   
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php"; 
//directiva a la revision de conexion
include_once"php/lock.php";
//directiva de inclusion de funciones de bd
include_once"php/auxentrega.php";



//inicializacion de arreglo para tabla de articulos
$tabla = array();

//CONSULTAS SQL-----------------------------------------------------------------------------------------------

//ordenes a programar
        
             $result= mysql_query("SELECT t1.orden,t1.formato_tienda,t1.fecha_confir, t1.hora_confir, t1.num_confir FROM orden_resumen as t1 
            WHERE t1.status = 21  ORDER BY t1.orden");
            
            
            
//VALIDAR QUE EXISTAN LAS CONSULTAS--------------------------------------------------------------------------------
      if (!$result) {
        die("Error al seleccionar datos entrega: ".mysql_error());
    }
      
  

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
        <!--scripts de mascaras de campos -->
        <script type="text/javascript" src="js/typecast_1.4.js"></script>
        <script type="text/javascript" src="js/typecast.config.js"></script>
        
        <?php
        // Request selected language for calendar-----------------------------------------------------
            $hl = (isset($_POST["hl"])) ? $_POST["hl"] : false;
            if(!defined("L_LANG") || L_LANG == "L_LANG")
            {
                if($hl) define("L_LANG", $hl);
            
                // You need to tell the class which language you want to use.
                // L_LANG should be defined as en_US format!!! Next line is an example, just put your own language from the provided list
                else define("L_LANG", "es_ES");
            }
        ?>
		<title>Zerby Intranet</title>


	</head>

	<body>
	    
	    <!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
    <?php 
  $titulo = "Prueba de Entrega ";
  include_once "include/barrasup.php" 
  ?>   
        


    
  <!--tablas ------------------------------------------------------------------------------->
  
  <!-- presentar la tabla de lista de ordenes actualizada en la pagina-->
  
          <form id = "pruebaent" method = "POST"  >
          
              <table class="centrada" border = '1' align='center'>
                    <tr>        
                        <th>CONS.</th>
                        <th>ORDEN</th>
                        <th>FORMATO</th>
                        <th>FECHA CITA</th>
                        <th>HORA CITA</th>
                        <th>CONFIRMACION</th>
                        <th>FECHA LLEGADA</th>
                        <th>HORA LLEGADA</th>
                        <th>OBSERVACIONES</th>
                    </tr>
            
      <?php
              //construccion de la tabla principal
                        
                //inicializa numero de renglones
                $reng = 0;
                
// Ciclo para presentar los renglones-----------------------------------
                while($row = mysql_fetch_row($result))
                {
                    echo "<tr>";
                //prints number of row
                    echo "<td>".($reng+1)."</td>";
                    // $row is array... foreach( .. ) puts every element
                    // of $row to $cell variable
                    $col1 = 0;
                    foreach($row as $cell){
                        //toma el numero de orden
                           echo "<input type=\"hidden\" name=\"ords$reng\" value = '$row[0]'/>";      
                           echo "<td name = '$row,$col1'>$cell</td>";
                         
//se llena un arreglo con los valores de la tabla principal                 
                        $tabla[$reng][$col1] = $cell;
                        $col1++;
                    }

//celdas con datos de la entrega
                    
                        // obtiene los datos para la fecha actual en el calendario
                        $today = getdate();
                        $año=($today['year']);
                        $mes=($today['mon']);
                        $dia=($today['mday']);
                        
                        //get class into the page
                        require_once('calendar/tc_calendar.php');
                        echo "<td>";
                        //instantiate class and set properties
                            $myCalendar = new tc_calendar("date".$reng,true,false);
                            $myCalendar->setIcon("calendar/images/iconCalendar.gif");
                            $myCalendar->setDate(1, 1, 2013);
                            $myCalendar->setYearInterval(2013, 2020); 
                            $myCalendar->setDate($dia, $mes,$año);
                            $myCalendar->setPath("calendar");
                            
                            //output the calendar
                            $myCalendar->writeScript();   
                         echo "</td>";
                          echo "<td><input type=\"text\" class=\"TCMask[##:##]\" value=\"HH/MM\" name=\"hora$reng\"
                            id=\"hora$reng\" '/> </td>";
                            echo "<td> <textarea rows = \"4\" cols = \"15\" name=\"obs$reng\"
                            id=\"obs$reng\"> </textarea> </td>";
                            echo "</tr>\n";
                      $reng++;
                    }
                            echo "<input type=\"hidden\" name=\"rengs\" value = $reng/>";                    
                
                 ?>
            
              </table>
              <br />
                <div align= "center"><input type="submit" name = 'envioe' value ='Entregado'/></div>
                
        </form>
        
        <div id="footer">
            

        </div> 

        
<!--SECCION DE SCRIPTS ------------------------------------------------------------------------------------------------------>
    
        <script type="text/javascript">
        
        // inicialización de máscaras --------------------------------------------------------------------------------------------->
        
        window.onload = go;
            function go(){
                Typecast.Init();
            }
        
            </script>
	</body>
</html>