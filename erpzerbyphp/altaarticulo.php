<?php
//directiva de la conexion a la base de datos
include_once "php/config.php";
//directiva al archivo de funciones auxiliares
include_once "php/funaux.php";
//directiva a la revision de conexion
include_once"php/lock.php";

// query de seleccion de combo clientes
   $query1="SELECT id_clientes, nombre FROM clientes ORDER BY nombre";
   $result1 = mysql_query ($query1);
//query de selección de combo cadenas
    $query2="SELECT id_cadenas, nombre FROM cadenas ORDER BY nombre";
   $result2 = mysql_query ($query2);

    
//script de alta de articulos en la base de datos

if(isset($_POST['envioc'])){
// se oprimio el boton de alta
//validaciones

// se han llenado todos los campos necesarios
//el upc no existe
// LOS CAMPOS NUMERICOS SON NUMERICOS
// MAS DE UN PUNTO EN CAMPOS NUMERICOS
//--------------------------------------------------

//CONVERSIONES A STRING

 $ctealta =strtoupper($_POST ['cteup']) ;
 $upc = strtoupper($_POST['upc']);
 $desc1 = strtoupper($_POST['desc1']);
 $desc2 =strtoupper( $_POST['desc2']);
 $ud1 = strtoupper($_POST['ud1']);
 $med1 = strtoupper($_POST['med1']);
 $ud2 = strtoupper($_POST['ud2']);
 $med2 = strtoupper($_POST['med2']);
 $ud3 = strtoupper($_POST['ud3']);
 $med3 = strtoupper($_POST['med3']);
 $pventa= strtoupper($_POST['pventa']);
 $peso= strtoupper($_POST['peso']);
 $vol= strtoupper($_POST['vol']);
 $cadena = strtoupper($_POST['cadup']);
  
  
//string de llenado de campos tabla cat_arts
               $querya = "INSERT INTO cat_arts (cliente,cadena,upc,desc1,desc2,ud1,med1,ud2,med2,ud3,
               med3,precioventa,pesocaja,volcaja,dadealta) 
               VALUES ($ctealta,$cadena,$upc,'$desc1','$desc2','$ud1',$med1,'$ud2',$med2,'$ud3',
               $med3,$pventa,$peso,$vol,'$login_session')";
         
//lenado de campos
               
                $resultal=mysql_query($querya) or die("Error en alta articulo: ".mysql_error());
                 if($resultal){
//el registro se inserto correctamente
                    echo '<script type="text/javascript">
                            window.alert("Articulo añadido correctamente!");
                        </script>';
                     
                  }
                 else{
                 //No se pudo lograr la insercion, crea una entrada en el log
                 
                        echo '<script type="text/javascript">
                            window.alert("Error. No se pudo dar de alta el articulo!");
                        </script>';
                        
                       echo "error en alta articulo".mysql_error(); 
                       creaLog(mysql_error());
                 }
        
    }
    
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
<script type="text/javascript" src="js/comunes.js"></script>
<title>Zerby Intranet</title>
</head>

<body

<!--LISTON DE ENCABEZADO ---------------------------------------------------------------------------------------->  
     <?php 
  $titulo = "ALTA DE ARTICULO ";
  include_once "include/barrasup.php" 
  ?>                  
    
        


<!-- la forma para el alta ----------------------------------------->

<div id="centra" align="center">

    <form id="altaart" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST">
        
       <table  border ="1">
            
        <!--el combo de cliente -------------------------------------------------------------------->
         <tr> 
             <td class="celdacolor" >CLIENTE:</td>
            <!--el combo de cliente -->
            <td colspan = "3" align='center'>
               <select name= "cteup" >
               
            <?php
                //llenado del combo cliente
               while($nt1=mysql_fetch_array($result1)){//Array or records stored in $nt
                echo "<option  value='$nt1[id_clientes]'>$nt1[nombre]</option>";
                }   
            ?>
               </select> 
            </td>
          </tr> 
          
        <!-- el combo de cadena------------------------------------------------------------------------>
        
        <tr> 
             <td class="celdacolor" >COMPRADOR:</td>
            <!--el combo de cliente -->
            <td colspan = "3" align='center'>
               <select name= "cadup" >
               
            <?php
                //llenado del combo cliente
               while($nt2=mysql_fetch_array($result2)){//Array or records stored in $nt2
                echo "<option  value='$nt2[id_cadenas]'>$nt2[nombre]</option>";
                }   
            ?>
               </select> 
            </td>
          </tr> 
        
           
        <!--los demas elementos de la forma. ------>
        
     <tr>
            <td >DESCRIPCION PRIMARIA:</td> 
            <td><input name ='desc1'/> </td>
            <td> DESCRIPCION ADIC:</td>
            <td ><input name ='desc2' /></td>
     </tr>
   
     <tr>
            <td>UPC:</td>
            <td><input name = 'upc'</td>
            <td>UNIDAD ARTICULO:</td>
            <td><input name ='ud1' value = 'GRAMOS' /></td>

     </tr>
   
     <tr>
             <td>CANTIDAD PRESEN:</td>
            <td><input name ='med1' /></td>
            <td>PRESENTACION CONS:</td>
           <td> <input name ='ud2' value = 'PIEZA'/></td>
     </tr>
     <tr>
         <td>ART/PRESENTACION:</td>
         <td><input name ='med2' /></td>
         <td>PRESENTACION CADENA:</td>
         <td><input name ='ud3' value = 'CAJA' /></td>
     </tr>
     <tr>
         <td>ARTI/PRESENT CADENA:</td>
         <td><input name ='med3' /></td>
         <td>PRECIO DE VENTA:</td>
         <td> <input name ='pventa' /></td>
     </tr>
     
     <tr>
         <td>PESO CAJA (KG.):</td>
         <td> <input name ='peso' /> </td>
         <td> VOL CAJA (m3):</td>
         <td><input name ='vol' />   </td>
     </tr>
                      
          </table>  <br />
    <!--------el boton de enviar ------------->  
           <input type="submit" name ="envioc" value="Alta" /> 
            
        </form>
    

</div>

<div id="footer"></div> 
 
</body>


</html>

