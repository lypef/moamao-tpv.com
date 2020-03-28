<?php
//C:\multifacturas_sdk\php -c C:\multifacturas_sdk\php.ini -f c:\test.php
date_default_timezone_set('America/Mexico_City');

include_once "../../sdk2.php";

$datos['archivo']='../../timbrados/ejemplo_contabilida_electronica_catalogos.xml';


$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO'; //   [SI|NO]
$datos['conf']['cer'] = '../../certificados/aaa010101aaa.cer.pem';
$datos['conf']['key'] = '../../certificados/aaa010101aaa.key.pem';
$datos['conf']['pass'] = '12345678a';

$datos['modulo'] = 'contabilidad';
$datos['CC']['Ejercicio'] = '2015';
$datos['CC']['Periodo'] = '01';

// == Catalago == 
$datos['factura']['Catalago']['Anio'] = '2015';
$datos['factura']['Catalago']['Mes'] = '01';
$datos['factura']['Catalago']['RFC'] = 'FJC780315E91';

// == Cuentas ==
$datos['factura']['Catalago']['Ctas'][0]['CodAgrup'] = '101.01';
$datos['factura']['Catalago']['Ctas'][0]['Desc'] = 'Caja';
$datos['factura']['Catalago']['Ctas'][0]['Natur'] = 'D';
$datos['factura']['Catalago']['Ctas'][0]['Nivel'] = '1';
$datos['factura']['Catalago']['Ctas'][0]['NumCta'] = '1000';

$datos['factura']['Catalago']['Ctas'][1]['CodAgrup'] = '201.01';
$datos['factura']['Catalago']['Ctas'][1]['Desc'] = 'Proveedores';
$datos['factura']['Catalago']['Ctas'][1]['Natur'] = 'A';
$datos['factura']['Catalago']['Ctas'][1]['Nivel'] = '1';
$datos['factura']['Catalago']['Ctas'][1]['NumCta'] = '2100';

$datos['tipo'] = 'catalogo';

$res = cargar_modulo_multifacturas($datos);

print_r($res);

