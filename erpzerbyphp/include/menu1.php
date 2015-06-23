<!--este es el menu basico de la aplicación-->
     
 <!--menu de navegacion -------------------------------------------------------------------------------><!--forma para presentar el boton de impresion---->   
<?php 
//si la pagina es de confirmaciones, muestra el icono de impresora
    if(!empty($escoge)){
        echo "<div id ='impr'>";
        
        echo "<form display = 'in-line'action='php/rconfirvacio.php' method='post' target = 'blank' > ";
    
            echo "<input type='image'name='imprime' src='img/print.ico'/>";
    
        echo "</form>";
        
       echo "</div>";
          
    }
?>

 
        <ul id = "nav">
            <li  class="current"><a href="php/logout.php">Salir</a></li>
            <li class="current"><a href="listaorden.php">Listado de Ordenes Recibidas</a></li>
            
             <li ><a href="#">Solicitar</a>
                <ul>
                     <li><a onclick="eligemenu2(1)">Bodega Aurrera</a></li>
                     <li><a onclick="eligemenu2(2)">Wal-Mart</a></li>
                </ul>
             </li>
            <li ><a href="#">Confirmaciones</a>
                <ul>
                     <li><a onclick="eligemenu(1)">Bodega Aurrera</a></li>
                     <li><a onclick="eligemenu(2)">Wal-Mart</a></li>
                </ul>
             </li>
             <li ><a href="#">Entregas</a>
                <ul>
                     <li><a href="programacion.php">Programación</a></li>
                     <li><a href="entrega.php">Prueba de Ent.</a></li>
                </ul>
             </li>      
             <li><a>Articulos</a>
                 <ul>
                     <li><a href="altaarticulo.php">Alta de Artículos</a></li>
                     <li><a href="modifart.php">Modificación de Artículos</a></li>
                 </ul>
                 
             </li>
             <li ><a href="#">Inventarios</a>
                <ul>   
                     <li><a href="altainventario.php">Alta de Inventarios</a></li>
                     <li><a href="listainventario.php">Consulta de Inventarios</a></li>
                     <li><a href="salewm.php">Salida Wal-Mart</a></li>                                  
                </ul>
             </li>
            
        </ul>




        
       
        
 
     
        
        
        
