<?php
error_reporting(0);
include "lib/cfdi32_multifacturas.php";
date_default_timezone_set('America/Mexico_City');
include_once "sdk2.php";

$datos['RESPUESTA_UTF8'] = "SI";

$datos['PAC']['usuario'] = "DEMO700101XXX";
$datos['PAC']['pass'] = "DEMO700101XXX";
$datos['PAC']['produccion'] = "NO";


$datos['modulo'] = "validacsd";

$datos['cer'] = 'pruebas/aaa010101aaa.cer.pem';
$datos['key'] = 'pruebas/aaa010101aaa.key.pem';
$datos['pass'] = '12345679a';

$res = cargar_modulo_multifacturas($datos);

print_r($res);