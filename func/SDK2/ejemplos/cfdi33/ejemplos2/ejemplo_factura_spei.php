<?php
// Se desactivan los mensajes de debug
error_reporting(0);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../../sdk2.php';

// Se especifica la version de CFDi 3.3
$datos['version_cfdi'] = '3.3';

// SE ESPECIFICA EL COMPLEMENTO
$datos['complemento'] = 'spei';

// Ruta del XML Timbrado
$datos['cfdi']='../../../timbrados/ejemplo_factura_spei.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../../timbrados/debug_ejemplo_factura_spei.xml';

// Credenciales de Timbrado
$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO';

// Rutas y clave de los CSD
$datos['conf']['cer'] = '../../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

// Datos de la Factura
$datos['factura']['condicionesDePago'] = 'CONDICIONES';
$datos['factura']['descuento'] = '0.00';
$datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s', time() - 120);
$datos['factura']['folio'] = '100';
$datos['factura']['forma_pago'] = '01';
$datos['factura']['LugarExpedicion'] = '45079';
$datos['factura']['metodo_pago'] = 'PUE';
$datos['factura']['moneda'] = 'MXN';
$datos['factura']['serie'] = 'A';
$datos['factura']['subtotal'] = '100.00';
$datos['factura']['tipocambio'] = '1';
$datos['factura']['tipocomprobante'] = 'I';
$datos['factura']['total'] = '100.00';
$datos['factura']['RegimenFiscal'] = '601';

// Datos del Emisor
$datos['emisor']['rfc'] = 'LAN7008173R5'; //RFC DE PRUEBA
$datos['emisor']['nombre'] = 'ACCEM SERVICIOS EMPRESARIALES SC';  // EMPRESA DE PRUEBA

// Datos del Receptor
$datos['receptor']['rfc'] = 'XAXX010101000';
$datos['receptor']['nombre'] = 'Publico en General';
$datos['receptor']['UsoCFDI'] = 'G01';

// Se agregan los conceptos
for ($i = 1; $i <= 1; $i++)
{
    $datos['conceptos'][$i]['cantidad'] = '1.00';
    $datos['conceptos'][$i]['unidad'] = 'PZ';
    $datos['conceptos'][$i]['ID'] = "COD$i";
    $datos['conceptos'][$i]['descripcion'] = "PRODUCTO $i";
    $datos['conceptos'][$i]['valorunitario'] = '100.00';
    $datos['conceptos'][$i]['importe'] = '100.00';
    $datos['conceptos'][$i]['ClaveProdServ'] = '01010101';
    $datos['conceptos'][$i]['ClaveUnidad'] = 'C81';
}

// Se agregan los Impuestos
$datos['impuestos']['TotalImpuestosTrasladados'] = '0.00';
$datos['impuestos']['translados'][0]['impuesto'] = '003';
$datos['impuestos']['translados'][0]['tasa'] = '0.160000';
$datos['impuestos']['translados'][0]['importe'] = '0.00';
$datos['impuestos']['translados'][0]['TipoFactor'] = 'Tasa';

// Complemento SPEI
$datos['spei']['SPEI_Tercero'][0]['FechaOperacion'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Hora'] = '2016-10-31';
$datos['spei']['SPEI_Tercero'][0]['ClaveSPEI'] = '2016-10-16';
$datos['spei']['SPEI_Tercero'][0]['sello'] = '2016-10-31';
$datos['spei']['SPEI_Tercero'][0]['numeroCertificado'] = '15';
$datos['spei']['SPEI_Tercero'][0]['cadenaCDA'] = '15';

$datos['spei']['SPEI_Tercero'][0]['Ordenante']['BancoEmisor'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Ordenante']['Nombre'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Ordenante']['TipoCuenta'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Ordenante']['Cuenta'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Ordenante']['RFC'] = 'O';

$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['BancoReceptor'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['Nombre'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['TipoCuenta'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['Cuenta'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['RFC'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['Concepto'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['IVA'] = 'O';
$datos['spei']['SPEI_Tercero'][0]['Beneficiario']['MontoPago'] = 'O';

$datos['spei']['SPEI_Tercero'][1]['FechaOperacion'] = 'fewO';
$datos['spei']['SPEI_Tercero'][1]['Hora'] = '2016-10-31';
$datos['spei']['SPEI_Tercero'][1]['ClaveSPEI'] = '2016-10-16';
$datos['spei']['SPEI_Tercero'][1]['sello'] = '2016-10-31';
$datos['spei']['SPEI_Tercero'][1]['numeroCertificado'] = 'f15';
$datos['spei']['SPEI_Tercero'][1]['cadenaCDA'] = 'ef15';

$datos['spei']['SPEI_Tercero'][1]['Ordenante']['BancoEmisor'] = '56O';
$datos['spei']['SPEI_Tercero'][1]['Ordenante']['Nombre'] = 'hytO';
$datos['spei']['SPEI_Tercero'][1]['Ordenante']['TipoCuenta'] = 'hyO';
$datos['spei']['SPEI_Tercero'][1]['Ordenante']['Cuenta'] = '45O';
$datos['spei']['SPEI_Tercero'][1]['Ordenante']['RFC'] = '4O';

$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['BancoReceptor'] = 'hO';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['Nombre'] = 'hfO';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['TipoCuenta'] = 'hfO';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['Cuenta'] = 'hfO';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['RFC'] = 'rwtO';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['Concepto'] = 'kyiuO';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['IVA'] = '65O';
$datos['spei']['SPEI_Tercero'][1]['Beneficiario']['MontoPago'] = 'gO';

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