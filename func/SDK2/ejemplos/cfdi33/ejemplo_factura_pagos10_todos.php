<?php
// Se desactivan los mensajes de debug
error_reporting(0);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';

// Se especifica la version de CFDi 3.3
$datos['version_cfdi'] = '3.3';

// SE ESPECIFICA EL COMPLEMENTO
$datos['complemento'] = 'pagos10';

// Ruta del XML Timbrado
$datos['cfdi']='../../timbrados/ejemplo_factura_pagos10.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../timbrados/debug_ejemplo_factura_pagos10.xml';

// Credenciales de Timbrado
$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO';

// Rutas y clave de los CSD
$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

// Datos de la Factura
$datos['factura']['serie'] = 'Z';
$datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s', time() - 120);
$datos['factura']['folio'] = '100';
$datos['factura']['subtotal'] = '0';
$datos['factura']['total'] = '0';
$datos['factura']['moneda'] = 'XXX';
$datos['factura']['tipocomprobante'] = 'P';
$datos['factura']['LugarExpedicion'] = '45079';
//$datos['factura']['Confirmacion'] = '12345';
$datos['factura']['RegimenFiscal'] = '601';

// Datos del Emisor
$datos['emisor']['rfc'] = 'LAN7008173R5'; //RFC DE PRUEBA
$datos['emisor']['nombre'] = 'ACCEM SERVICIOS EMPRESARIALES SC';  // EMPRESA DE PRUEBA

// Datos del Receptor
$datos['receptor']['rfc'] = 'XAXX010101000';
$datos['receptor']['nombre'] = 'Publico en General';
$datos['receptor']['UsoCFDI'] = 'P01';

// Se agregan los conceptos
$datos['conceptos'][0]['ClaveProdServ'] = '84111506';
$datos['conceptos'][0]['cantidad'] = '1';
//$datos['conceptos'][0]['unidad'] = 'ACT';
$datos['conceptos'][0]['ClaveUnidad'] = 'ACT';
$datos['conceptos'][0]['descripcion'] = "Pago";
$datos['conceptos'][0]['valorunitario'] = '0.0';
$datos['conceptos'][0]['importe'] = '0.0';

// Complemento de Pagos 1.0
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['IdDocumento'] = '970e4f32-0fe0-11e7-93ae-92361f002671';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['Serie'] = 'A';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['Folio'] = '210';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['MonedaDR'] = 'MXN';
//$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['TipoCambioDR'] = '15';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['MetodoDePagoDR'] = 'PIP';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['NumParcialidad'] = '1';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['ImpSaldoAnt']= '10000';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['ImpPagado'] = '5000';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][0]['ImpSaldoInsoluto'] = '5000';

/*$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['IdDocumento'] = '970e5496-0fe0-11e7-93ae-92361f002672';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['Serie'] = 'A';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['Folio'] = '210';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['MonedaDR'] = 'USD';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['TipoCambioDR'] = '20.00';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['MetodoDePagoDR'] = 'PIP';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['NumParcialidad'] = '2';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['ImpSaldoAnt']= '250.00';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['ImpPagado'] = '250.00';
$datos['pagos10']['Pagos'][0]['DoctoRelacionado'][1]['ImpSaldoInsoluto'] = '0.00';*/

$datos['pagos10']['Pagos'][0]['FechaPago']= date('Y-m-d\TH:i:s', time() - 120);
$datos['pagos10']['Pagos'][0]['FormaDePagoP']= '06';
$datos['pagos10']['Pagos'][0]['MonedaP']= 'MXN';
//$datos['pagos10']['Pagos'][0]['TipoCambioP']= '0.0';
$datos['pagos10']['Pagos'][0]['Monto']= '10000';
//$datos['pagos10']['Pagos'][0]['NumOperacion']= '0.0';
//$datos['pagos10']['Pagos'][0]['RfcEmisorCtaOrd']= 'XAXX010101000';
$datos['pagos10']['Pagos'][0]['NomBancoOrdExt']= '0.0';
$datos['pagos10']['Pagos'][0]['CtaOrdenante']= '1234567890';
//$datos['pagos10']['Pagos'][0]['RfcEmisorCtaBen']= '0.0';
//$datos['pagos10']['Pagos'][0]['CtaBeneficiario']= '0.0';
//$datos['pagos10']['Pagos'][0]['TipoCadPago']= '0.0';
//$datos['pagos10']['Pagos'][0]['CertPago']= '0.0';
//$datos['pagos10']['Pagos'][0]['CadPago']= '0.0';
//$datos['pagos10']['Pagos'][0]['SelloPago']= '0.0';

// Se ejecuta el SDK
$res= mf_genera_cfdi($datos);


///////////    MOSTRAR RESULTADOS DEL ARRAY $res   ///////////
 
echo "<h1>Respuesta Generar XML y Timbrado</h1>";
foreach($res AS $variable=>$valor)
{
    $valor=htmlentities($valor);
    $valor=str_replace('&lt;br/&gt;','<br/>',$valor);
    echo "<b>[$variable]=</b>$valor<hr>";
}



?>