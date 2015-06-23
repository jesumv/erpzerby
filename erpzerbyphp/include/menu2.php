<!--este es el menu basico de la aplicación confe-->
     
 <!--menu de navegacion ------------------------------------------------------------------------------->
   <?php     
        echo "<ul id = 'nav'>";
  //si eres mortal
        echo "<li  class='current'><a href='../php/logout.php'>Salir</a></li>";
        echo"<li class='current'><a href='inventconfe.php'>Consulta de Inventarios</a></li>";
 //si estas en curtidores        
            if($_SESSION['nivel']==3 ){
                echo"<li class='current'><a href='altaprod.php'>Alta de Producción</a></li> ";
                echo"<li class='current'><a href='movinvent.php'>Movimientos de Inventarios</a></li> ";
                   
            }
  //si eres todopoderoso         
           if($_SESSION['nivel']==1  ){
               echo"<li class='current'><a href='altaprod.php'>Alta de Producción</a></li> ";
               echo"<li ><a href='#'>Almacen</a>";
               echo "<ul>";
               echo "<li><a href='movinvent.php'>Movimientos de Inventarios</a></li> ";
               echo "<li><a href='altaexplosion.php'>Explosión de Materiales</a></li> ";  
                                   
               echo"</ul>";
               
                echo"</li>";
                  
            }
            
            
        echo"</ul>";

        
    ?>
        
 
     
        
        
        
