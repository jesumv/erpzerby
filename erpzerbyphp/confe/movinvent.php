<?php
//esta hoja se usa para dar de alta la producción de dulces destellos
    
//directiva de la conexion a la base de datos
include_once "../php/config.php";

//directiva a la revision de conexion
include_once"../php/lock.php";

//directiva de conexion a hoja de funciones alta de producción
include_once"../php/auxmovinvent.php";

// query de seleccion filas segun el numero de productos registrados
   $query1="SELECT idmateriales, upc, descrip, presentacion FROM materiales WHERE 1";
   $result1 = mysql_query ($query1);
   
//validacion de la existencia de la consulta  
       if (!$result1) {
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
        
   
        
    </head>

    <body>
        
        <?php 
              $titulo = "MOVIMIENTOS DE INVENTARIOS ";
              include_once "../include/barrasup2.php" ;
              
        ?> 
        

 <!--construcción del calendario y botones de opcion---------------------------------------------------------------------->
        <div id ="cajacent">

                 
                <form id= "altamov"   method="post" ">
                    

                    <input type="radio" name="tipo" value="1" checked="true">Entrada
                    <input type="radio" name="tipo" value="-1">Salida

                     <p>Elija la fecha del movimiento:</p>
                   
                    <?php
                    
                        // obtiene los datos para la fecha actual en el calendario
                        $today = getdate();
                        $año=($today['year']);
                        $mes=($today['mon']);
                        $dia=($today['mday']);
                        
                        //get class into the page
                        require_once('../calendar/tc_calendar.php');
                        
                        //instantiate class and set properties
                            $myCalendar = new tc_calendar("date1", false);
                            $myCalendar->setIcon("calendar/images/iconCalendar.gif");
                            $myCalendar->setDate(1, 1, 2013);
                            $myCalendar->setYearInterval(2013, 2020); 
                            $myCalendar->setDate($dia, $mes,$año);
                            $myCalendar->setPath("../calendar");
                            
                            //output the calendar
                            $myCalendar->writeScript();   
                            
                        ?>
                       
                    <br />
                    
                        <table id="table1" border =1>
                            <tr><th>CLAVE</th><th>CODIGO</th> <th>ARTICULO</th><th>PRESENTACION</th><th>CANTIDAD</th>
                                <th>FACTURA</th><th>OBSERVACIONES</th></tr>
                             <?php
        
                            // printing table rows
                             $cont1 = 0; 
                             $cont2 =0;
                            
                            while($row = mysql_fetch_row($result1)){
                                echo "<tr>";
                //ciclo para insertar una celda por cada campo y registro de la consulta SQL
                                 foreach($row as $cell){ 
                                     echo "<td>$cell</td>";  
                                     echo "<input type = 'hidden' name = 'code$cont2' value= '$cell'/>";
                                     $cont2++;  
                                 }
                   //celdas de informacion              
                                     echo "<td><input type = 'text' name = 'c$cont1'/></td>";
                                     echo "<td><input type = 'text' name = 'f$cont1'/></td>";
                                      echo "<td><input type = 'text' name = 'o$cont1'/></td>";

                                echo "</tr>\n"; 
                            $cont1++;  
                            }
                                $_SESSION['reng'] = $cont1;
                                
                                ?>
                        </table>
                    <br />
                <div align= "center"><input type="submit" name = 'enviom' value ='Registrar Movimiento'/></div>
                        
                    
            </form>
            
            
         
                     <br />
                     <br />
                      

                     
        </div>
    
    <div id="footer"></div>       

    </body>
</html>