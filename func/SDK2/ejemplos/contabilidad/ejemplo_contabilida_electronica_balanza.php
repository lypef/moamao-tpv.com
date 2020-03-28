<?php
date_default_timezone_set('America/Mexico_City');

include_once "../../sdk2.php";

$datos['tipo'] = 'balanza';
$datos['archivo']='../../timbrados/ejemplo_contabilida_electronica_balanza.xml';


$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO'; //   [SI|NO]
$datos['conf']['cer'] = '../../certificados/aaa010101aaa.cer.pem';
$datos['conf']['key'] = '../../certificados/aaa010101aaa.key.pem';
$datos['conf']['pass'] = '12345678a';

$datos['modulo'] = 'contabilidad';
$datos['CC']['Ejercicio'] = '2015';
$datos['CC']['Periodo'] = '01';

// == Balanza == 
$datos['factura']['Balanza']['Anio'] = '2015';
$datos['factura']['Balanza']['Mes'] = '01';
$datos['factura']['Balanza']['RFC'] = 'FJC780315E91';
$datos['factura']['Balanza']['TipoEnvio'] = 'N';

// == Cuentas ==
$datos['factura']['Balanza']['Ctas'][0]['Debe'] = '1000.00';
$datos['factura']['Balanza']['Ctas'][0]['Haber'] = '990.00';
$datos['factura']['Balanza']['Ctas'][0]['NumCta'] = '1000';
$datos['factura']['Balanza']['Ctas'][0]['SaldoFin'] = '10.00';
$datos['factura']['Balanza']['Ctas'][0]['SaldoIni'] = '0.00';

$datos['factura']['Balanza']['Ctas'][1]['Debe'] = '1000.00';
$datos['factura']['Balanza']['Ctas'][1]['Haber'] = '1000.00';
$datos['factura']['Balanza']['Ctas'][1]['NumCta'] = '2100';
$datos['factura']['Balanza']['Ctas'][1]['SaldoFin'] = '2000.00';
$datos['factura']['Balanza']['Ctas'][1]['SaldoIni'] = '2000.00';



$res = cargar_modulo_multifacturas($datos);
print_r($res);

