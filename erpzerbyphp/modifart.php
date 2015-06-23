<?php
//directiva de la conexion a la base de datos
include_once "php/config.php";
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php";
//directiva a la revision de conexion
include_once"php/lock.php";

//SECCION DE QUERIES -----------------------------------------------------------------

//SELECCION DE FILTROS POR COMBO DE FILTRADO-------------------------------------------   
if(isset($_POST['enviof'])){
     $cte = $_POST['cte'];
       
        
  
 // 1.-se eligio la opcion todos en ambos combos por lo que no hay clausula WHERE en la consulta.
        
        if ($cte == '0'){
            $resultf = mysql_query("SELECT * FROM cat_arts WHERE 1")
             or die ("Error en al seleccionar cliente.".mysql_error());
            $nomcte1 = "todos";
            $idcte = "1";
         }
        
        
       
 //2.- Se elige cliente y todos los status
 
         if ($cte != '0') {
            $resultf = mysql_query("SELECT * FROM cat_arts WHERE cliente = $cte")
            or die ("Error al filtrar cliente.".mysql_error());
            $result= mysql_query("SELECT nomcorto FROM clientes WHERE id_clientes = $cte");
            $idcte= "id_clientes =".$cte;
            $nomcte=mysql_fetch_array($result);
            $nomcte1= $nomcte[0];
           
        }    
    
}

else{
    
    //no se eligieron filtros, se muestran todas los articulos en la tabla

    $resultf = mysql_query("SELECT * FROM cat_arts WHERE 1")or die ("Error en al seleccionar ordenes.".mysql_error());
    $nomcte1 = "todos";
    $idcte = "1";
}
   
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
        <script type="text/javascript" src="js/comunes.js"></script>

		<title>Zerby Intranet</title>

	</head>

	<body>
<!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
     <?php 
          $titulo = "MODIFICACIONES A ARTICULOS";
          include_once "include/barrasup.php" 
      ?> 
      
<!--division con los datos del filtrado------------------------------------------------------------------------------------------------>       
  <div class='centrares' align="center">
                  <table class = 'n1'border = '3'>
                      <tr>
                          <th>CLIENTE</th>
                      </tr>
                      <tr>
                          <td class='enc'><?php echo "$nomcte1"; ?></td> 
                      </tr>
                      
                  </table>
  </div>
  
<!--------DIVISION CON EL COMBO DE FILTRADO------------------------------------------------------------------------------------------->

     <div id ="cajacent" align="center">
        <form id= "combo1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        
            <?php
                   // query de seleccion de combo clientes
                    $query="SELECT id_clientes, nomcorto FROM clientes ORDER BY nomcorto";
                    $result1 = mysql_query ($query);
            ?> 
        
                Elija el Filtrado: CLIENTE
         <!--el combo de cliente -->
               <select name= "cte" >
                // printing the list box select command
                <option value = '0' >Todos</option>
                <?php
                    while($nt=mysql_fetch_array($result1)){
                    echo "<option value='$nt[id_clientes]'>$nt[nomcorto]</option>";
                    }   
                ?>
                
                </select>
                <input type="submit" name ="enviof" value = "Filtrar" />
          </form>
        
    </div>
<br/>
  
<!---------division para la tabla principal------------------------------------------------------------------------------------------->


<div>

         <?php
        //obtención de los nombres de la tabla---
        $query= "SELECT t1.upc,t1.desc1 as DESCRIPCION , t3.nomcorto as CLIENTE,t2.nombre as CADENA,t1.ud1,t1.med1,t1.ud2,t1.med2,t1.ud3,t1.med3,
        t1.precioventa as PRECIO, t1.pesocaja as PESO,t1.volcaja as VOLUMEN FROM cat_arts AS t1 INNER JOIN 
        cadenas AS t2 ON t1.cadena = t2.id_cadenas INNER JOIN clientes AS t3 ON t1.cliente = t3.id_clientes WHERE $idcte ORDER by desc1 ";
        $result=mysql_query($query)or die ("Error en al traer nombre de tabla.".mysql_error());
        
        ponnombres($result);
    
        ?>

    
</div>

<!--PIE DE PAGINA------------------------------------------------------------------------------------------------>  
    <?php 
          include_once "include/footer.php" 
      ?> 


	</body>
</html>
