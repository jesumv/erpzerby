<?php
//version 2.0 Julio 23, 2013.

function exisarch($nomarchivo){
  // esta rutina verifica si un archivo existe en el directorio especificado
     

    if (file_exists($nomarchivo)) {
       return 11;
    } else {
        return 0;
    }
}

//--------------------------------------------------------------------------------
function uploader($archabrir){
    //Esta funcion toma los datos de un archivo y lo sube al forlder uploads
    
        $target_path = "../uploads/";
        
        /* Add the original filename to our target path.  
        Result is "uploads/filename.extension" */
        $target_path = $target_path . basename( $_FILES[$archabrir]['name']); 
        
        
        //call to the move:uploadedfile function
        // java srcipt from http://forums.phpfreaks.com/topic/213143-php-message-box-popup/
        
        if(move_uploaded_file($_FILES[$archabrir]['tmp_name'], $target_path)) {
            
                return true;
                
        } else{
            
               return false;
        }
        
    }
//-------------------------------------------------------------------------------------------

function creaLog($data){
//esta funcion crea un log de errores cuando no se puede insertar una orden de compra.
//solo funciona si la rutina de llamado esta en el directorio raiz.
    $file = "logs/erroressql.txt";
    $fh = fopen($file, 'a') or die("can't open file");
    fwrite($fh,date("c").$data);
    fwrite($fh, "\n");
    
    fclose($fh);
}

//-------------------------------------------------------------------------------------------

function hazcombo($tabla,$campo1,$campo2,$regnombre){
    //esta funcion crea un combo con los campos de las tablas pasados en los argumentos
    //y crea un arreglo con la respuesta, nombrado segun el valor del tercer argumento
    //no la pude hacer funcionar, revisarla
    
   //query de seleccion
    $query="SELECT $campo1,$campo2 FROM $tabla ORDER BY $campo2";
    $result = mysql_query ($query);
    echo "<select name= $regnombre action='<?php echo $_SERVER[PHP_SELF]; ?>'method='post'>Cliente</option>" ;
    // printing the list box select command
    echo "<option selected = 'selected' value = ''>Todos</option>";
    while($nt=mysql_fetch_array($result)){//Array or records stored in $nt
    echo "<option value=$nt[$campo1]>$nt[$campo2]</option>";
    /* Option values are added by looping through the array */
    }
    echo "</select>";
    
    echo "$resp = $_POST[$regnombre];";
    
    return $_POST[$regnombre];
    
    // Closing of list box  
    
}


function notienda($nombretienda){
 //esta funcion recibe un string de la orden y extrae el numero de la tienda
    return $notienda = substr($nombretienda,-4,4) ;
}


function cajas($arts,$artcaja){
//esta funcion calcula el numero de cajas a partir de las cantidades y empaque de la orden 
//se modifica para que si el parametro $arts es 0, no cause error al parsear.
        if($arts==0){
           return $cajas =0;
        }
        else{
         return $cajas = number_format($arts/$artcaja);  
        }
        
    
}


/**
 * sum values in array optional index that is to be summed
 *
 * @param array $arr
 * @param string [optional]$index
 * @return int result
 */
function array_sum_key( $arr, $index = null ){
    if(!is_array( $arr ) || sizeof( $arr ) < 1){
        return 0;
    }
    $ret = 0;
    foreach( $arr as $id => $data ){
        if( isset( $index )  ){
            $ret += (isset( $data[$index] )) ? $data[$index] : 0;
        }else{
            $ret += $data;
        }
    }
    return $ret;
}


function ponnombres($query) {
    //esta funcion crea una tabla con los datos del query pasado como argumento
    //se ha modificado para que la primera columna sea una imagen con link.
    $numfields = mysql_num_fields($query);
    echo '<table border="1" bgcolor="white"><tr>';
    echo '<th></th>';
    for ($i = 0; $i<$numfields; $i += 1) {
        $field = mysql_fetch_field($query, $i);
        echo '<th>' . $field->name . '</th>';
    }
    echo '</tr>';
    while ($fielddata = mysql_fetch_array($query)) {
        echo '<tr>';
        echo '<td><a href = "altaarticulo.php"><img src="img/edita.jpg" ALT="editar"></a></td>';
        for ($i = 0; $i<$numfields; $i += 1) {
            $field = mysql_fetch_field($query, $i);
            echo '<td>' . $fielddata[$field->name] . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';   
}
    
    
function ArrayFromCsv($file,$delimiter) {
    //esta funcion recibe el nombre de un archivo csv, y su delimitador y devuelve un arreglo con los datos del archivo.
        if (($handle = fopen($file, "r")) !== FALSE) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
                for ($j=0; $j<count($lineArray); $j++) {
                    $data2DArray[$i][$j] = $lineArray[$j];
                }
                $i++;
            }
            fclose($handle);
        }
        return $data2DArray;
    } 



function cambiatablas($icolcanv,$rengs,&$invs3){
    //esta funcion cambia los valores de los inventarios en el arreglo tablas por los valores introducidos en las cajas de texto
    //de aquellas ordenes seleccionadas
    //recibe como parametros el ordinal de la orden a cambiar, el numero de renglones de la tabla, y la tabla de inventarios
   for ($r=0; $r < $rengs; $r++) {
   //el numero de columna a cambiar
       $colmod = ($icolcanv+9)+(2*$icolcanv);
   //recibir el valor de la hoja de confirmaciones     
       $valorp = $_POST['entc'.$r.$colmod];
       $invs3[$r][8+($icolcanv*3)]=$valorp;
       
   }
}

function completa($orden,$articulos,$col){
    //esta funcion recibe un numero de orden, los articulos a surtir de la hoja confirmaciones e informa si el surtido está completo o no
    //se debe aplicar después de haber aplicado los inventarios. 
    
    //consulta de articulos ya surtidos previamente, de la base de datos como cajas
     $surt= mysql_query("SELECT SUM(cajas) FROM inventario WHERE ref  = $orden")
     or die ("Error en la consulta de articulos entregados.".mysql_error());
     $surts1=mysql_fetch_array($surt);
//si no hay articulos entregados se forza la variable a 0.
  if(is_null($surts1)){
      $surts2= 0;
   }
  else{
      $surts2= $surts1[0];
  }  
     
//definición de artículos a entregar
//ajuste de la columna a leer en el arreglo  de articulos
  $colmod = 8+(3*$col);
//suma de los articulos a entregar
    $ent=array_sum_key($articulos,$colmod);
//consulta de articulos en la orden original
    $ordorig= mysql_query("SELECT SUM(cantidad/empaque) FROM orden_detalle WHERE orden  = $orden")
     or die ("Error en la consulta de articulos entregados.".mysql_error());
     $ordorigs1=mysql_fetch_array($ordorig);
     $ordorigs2= number_format($ordorigs1[0]);

 //comparación con los artículos a entregar 
    
   $result = $ordorigs2-($ent-$surts2);
   if(($result)== 0){
   // se han entregado todos los articulos
         return 0; 
   }
   elseif(($result)<0){
   //se excedería el número de artículos
         return 2; 
   }
   //quedan artículos por entregar
   else return 1;

}


function colorestado($orden){
  //esta funcion regresa el color de fondo y el texto para la casilla de estado, dependiendo del estado de confirmacion de una orden
  //consulta SQL del estado de la orden
  $querya=mysql_query("SELECT status_maestro FROM orden_resumen WHERE orden = $orden ")
  or die ("Error en la consulta de estado de la orden".mysql_error());
  $queryb=mysql_fetch_array($querya);
  $estado= $queryb[0];
  
  //mensaje segun el estado de la orden
  
  switch ($estado) {
      case 0:
          
          return array("POR CONFIRMAR","uno") ;
          
      case 4:
          
          return array("SURTIDA PARCIAL","dos") ;
          
      case 5:
          
          return array("SURTIDA PARCIAL","dos") ;
          
      case 6:
          
          return array("SURTIDA","dos") ;
          
      case 7:
          
          return array("SURTIDA","dos") ;
          
          
      case 9:
          
          return array("CONFIRMADA PARCIAL","dos");
      
      default:
          
          return array("ERROR","");
  }
    
}

?>


