<?php
// Se desactivan los mensajes de debug
error_reporting(~(E_WARNING|E_NOTICE));
//error_reporting(E_ALL);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se activa el modulo de debug
$datos['modulos_inter'] = 'debug';

// Se incluye el SDK
require_once '../../sdk2.php';

// Se especifica la version de CFDi 3.3
$datos['version_cfdi'] = '3.3';

// XML a Sellar y Timbrar
$datos['xml'] = '../../timbrados/factura_sin_timbrar2.xml';

// Ruta del XML Timbrado
$datos['cfdi']='../../timbrados/cfdi_ejemplo_factura.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../timbrados/sin_timbrar_ejemplo_factura.xml';

// Credenciales de Timbrado
$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO';

// Rutas y clave de los CSD
$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

// Se ejecuta el SDK
$res = mf_genera_cfdi($datos);

///////////    MOSTRAR RESULTADOS DEL ARRAY $res   ///////////
/*echo "<pre>";
print_r($datos);
echo "</pre>";*/
echo "<h1>Respuesta Generar XML y Timbrado</h1>";
foreach ($res AS $variable => $valor) {
    $valor = htmlentities($valor);
    $valor = str_replace('&lt;br/&gt;', '<br/>', $valor);
    echo "<b>[$variable]=</b>$valor<hr>";
}