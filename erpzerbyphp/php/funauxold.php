<?php

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




?>


