<?php
error_reporting(0); // OPCIONAL DESACTIVA NOTIFICACIONES DE DEBUG
date_default_timezone_set('America/Mexico_City');
include_once "lib/cfdi32_multifacturas.php";

$datos['RESPUESTA_UTF8'] = "SI";

$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO';

$datos['modulo'] = 'googleprint';

$datos['usuario'] = 'mashtersoporte@gmail.com';
$datos['clave'] = 'mash9900';

$datos['operacion'] = 'lista_impresoras';

$res = cargar_modulo_multifacturas($datos);

var_dump($res);
