<!---esta barra se usa para confe ---------------------------------------->

<!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
     <div id="bandasup">
        
        <div id="zerby">
         <h1>Comercializadora Confe</h1>
       </div> 
       
       <div id="derarr">
         <h3 align="right">fecha: <?php echo date("d-m-Y") ?></h3>     
       </div>
       
       <div id="logoprinc" >
                          <img  src="../img/confe.png" alt="confe" width="120px" height="80px">  
       </div>
       <h3 id="saluda">Bienvenido, <?php echo $_SESSION['username']; ?></h3>
       
     </div>
     
 
<!--INCLUSION DE LA BARRA DE MENU -->
         <?php 
              include_once "menu2.php" 
          ?>   
        
        <h1 id="titpag" align="center">
            <?php 
            echo "<h1 id='titpag' align='center'>";
                if(!isset($titulo)){
                   
                   echo "NO HAY TITULO PARA ESTA PAGINA" ;
                }
                else {
                 echo $titulo; 
                }
             echo "</h1>"  ; 
            ?>

        
 
     
        
        
        
