<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
require_once 'funaux.php';
require_once"conversiones.php";
include_once"config.php";

$data = new Spreadsheet_Excel_Reader("C:\Users\jmv\Documents\sistemas\erp zerby\925485MX00_20130205104957_431 zerby.xls",false);

?>
<html>
<head>
<style>
</style>
</head>

<body>

<?php 
$datos=array();
$datos=$data-> dumptoArray();

//obtencion de datos orden resumen
$orden= $datos[3][2];
$tipo = $datos[5][2];
$proveedor = $datos[11][2];
$moneda = $datos[14][2];
$depto = $datos[6][2];
$monto = $datos[18][2];
$partidas = $datos[19][2];
$nocomprador = $datos[8][2];
$comprador =$datos[9][2];
$notienda=notienda($comprador);
$noformato = $datos[6][4];
$formato = $datos[7][4];
$lugar = $datos[13][4];
$fechao = convfecha($datos[3][4]);
$fechaemb = $datos[4][4];
$fechac = convfecha($datos[5][4]);
$diasp = $datos[17][4];

//falta definir consulta para obtener valor de cliente zerby

//consulta de inserción
 $queryr = "INSERT INTO orden_resumen (cadena,cliente_zerby,orden,tipo_orden,vendedor,moneda,depto,monto_total,
                promocion,no_partidas,no_comprador,comprador,no_formato_tienda, formato_tienda,lugar_embarque,fecha_orden, 
                fecha_canc, fecha_ent,dias_pago,dias_ppago,p_desc,cargo_flete,libre1,libre2,libre3,libre4,libre5,libre6,libre7,libre8,
                dadealta,status,no_tienda) VALUES ($cadena,$cliente,$orden,$data[2],$data[3],'$data[4]',$data[5],$campo6,'$campo7',$campo8,$campo9,
                '$campo10',$campo11,'$campo12','$campo14',$fecha1,$fecha2,$fecha3,$campo22,$campo23,$campo24,'$campo27',
                '$campo28','$campo29','$campo30','$campo31','$campo32','$campo33','$campo34','$campo35','$usu',0,$notienda)";
         
             //lenado de campos
               
             $result2=mysql_query($queryr) or die ("Error en el llenado de orden_resumen.".mysql_error()); ;

//numero de renglones
$reng=$data->rowcount();
//definicion de cadena
$cadenas= defcadena($noformato);
$cadena=$cadenas[0];
$cad_art=$cadenas[1];


//obtencion de datos orden detalle. arreglos para datos


for ($i=22; $i <$reng ; $i++) {
    $upc= $datos[23][1];
    $comprador2= $datos[23][2];
    $color = $datos[23][4];
    $precio = $datos[23][5];
    $medida =$datos[23][6];
    $montol =$datos[23][7];
    $cantidad =$datos[23][8];
    $empaque =substr($datos[23][9],0,2);
    
	
}


        
echo $data->dump(true,true); 

?>
</body>
</html>
