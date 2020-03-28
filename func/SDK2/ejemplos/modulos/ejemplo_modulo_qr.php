<?php
error_reporting(0); // OPCIONAL DESACTIVA NOTIFICACIONES DE DEBUG
include "lib/cfdi32_multifacturas.php";
date_default_timezone_set('America/Mexico_City');
include_once "lib/cfdi32_multifacturas.php";

$datos['RESPUESTA_UTF8'] = "SI";

$datos['PAC']['usuario'] = "DEMO700101XXX";
$datos['PAC']['pass'] = "DEMO700101XXX";
$datos['PAC']['produccion'] = "NO";


$datos['modulo']="qr";                                  //NOMBRE DEL MODULO
$datos['archivo_png']="timbrados/qr_defactura.png";     //RUTA DONDE SE GUARDARA EL  QR.PNG
$datos['cadena']="hola hola";                           //CADENA A GUARDAR EN EL QR
$res = cargar_modulo_multifacturas($datos);

echo "<pre>";
print_r($res);
echo "</pre>";
?>