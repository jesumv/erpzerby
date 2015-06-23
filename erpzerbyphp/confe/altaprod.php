<?php
//esta hoja se usa para dar de alta la producción de dulces destellos
    
//directiva de la conexion a la base de datos
include_once "../php/config.php";

//directiva a la revision de conexion
include_once"../php/lock.php";

//directiva de conexion a hoja de funciones alta de producción
include_once"../php/auxprod.php";


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
              $titulo = "ALTA DE PRODUCCION ";
              include_once "../include/barrasup2.php" ;
              
        ?> 
        

 <!--construcción del combo de eleccion de producto---------------------------------------------------------------------->
        <div id ="cajacent">
            <p>Elija la fecha de producción:</p>
                 

            
                <form id= "altaprod"   method="post">
                   
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
                        $myCalendar->setIcon("/calendar/images/iconCalendar.gif");
                        $myCalendar->setDate(1, 1, 2013);
                        $myCalendar->setYearInterval($año-1,$año); 
                        $myCalendar->setDate($dia, $mes,$año);
                        $myCalendar->setPath("../calendar");
                        
                        //output the calendar
                        $myCalendar->writeScript();   
                    
                        echo "<br/>";
                    // query de seleccion de combo de producto
                        $query="SELECT upc, desc1 FROM cat_arts WHERE produccion = 1 ORDER BY upc";
                        $result1 = mysql_query ($query);
                        ?> 
                        
                    <br />
                    Elija el producto elaborado: 
             <!--el combo de cliente -->
                   <select name= "prod" >
                    // printing the list box select command
                    <?php
                        while($nt=mysql_fetch_array($result1)){//Array or records stored in $nt
                        echo "<option value='$nt[upc]'>$nt[desc1]</option>";
                        }   
                    ?>
                     
                    </select>
                        
                     <br />
                     Introduzca la cantidad de cajas producidas:
                    <input type="text" name="cant" id="cant"/> 
                  
                      <br />
                     <br />                
 
                <div align= "center"><input type="submit" name = 'enviop' value ='Dar de Alta'/></div>
                        
                    
            </form>
            
            
         
                     <br />
                     <br />
                      

                     
        </div>
    
    <div id="footer"></div>       

    </body>
</html>
