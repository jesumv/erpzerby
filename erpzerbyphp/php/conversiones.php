<?php
    function convfecha($fechaor){
        //esta funcion convierte una fecha de formato dd-mm-aaaa A aaaa-mm-dd
        $elem = array();
//leer el string        
        for($i = 0; $i < 10; $i++){
           $elem[$i]= substr($fechaor, $i,1);
        }
//reordenar los elementos
        $camb = array();
        $camb[0] = "'";
        $camb[1]= $elem[6];
        $camb[2]= $elem[7];
        $camb[3]= $elem[8]; 
        $camb[4]= $elem[9];
        $camb[5]= '-';
        $camb[6]= $elem[3];
        $camb[7]= $elem[4];
        $camb[8]= '-';
        $camb[9]= $elem[0];
        $camb[10]= $elem[1];
        $camb[11]="'";
      
       
        $cambc = $camb[0].$camb[1].$camb[2].$camb[3].$camb[4].$camb[5]
        .$camb[6].$camb[7].$camb[8].$camb[9].$camb[10].$camb[11];
        return $cambc;
    }
    
    function convfechaxls($fechaor){
        //esta funcion convierte una fecha de formato mm-dd-aaaa A aaaa-mm-dd
        $elem = array();
//leer el string        
        for($i = 0; $i < 10; $i++){
           $elem[$i]= substr($fechaor, $i,1);
        }
//reordenar los elementos
        $camb = array();
        $camb[0] = "'";
        $camb[1]= $elem[6];
        $camb[2]= $elem[7];
        $camb[3]= $elem[8]; 
        $camb[4]= $elem[9];
        $camb[5]= '-';
        $camb[6]= $elem[0];
        $camb[7]= $elem[1];
        $camb[8]= '-';
        $camb[9]= $elem[3];
        $camb[10]= $elem[4];
        $camb[11]="'";
      
       
        $cambc = $camb[0].$camb[1].$camb[2].$camb[3].$camb[4].$camb[5]
        .$camb[6].$camb[7].$camb[8].$camb[9].$camb[10].$camb[11];
        return $cambc;
    }
    
    
   function qblanco($campo){
       $sinbl = !empty($campo) ? $campo : "NULL";
       return $sinbl;
   }
?>