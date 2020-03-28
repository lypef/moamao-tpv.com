<?php

error_reporting(~(E_WARNING|E_NOTICE));

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';

// Ruta del CFDI
$datos['cfdi'] = '../../timbrados/ejemplo_factura_global.xml';

// XML para soporte en caso de error
$datos['xml_debug'] = '../../timbrados/debug_ejemplo_factura_global.xml';

// Version de CFDi a usar
$datos['version_cfdi'] = '3.3';

// Respuesta en UTF-8
$datos['RESPUESTA_UTF8'] = 'SI';

// Credenciales de timbrado
$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO'; // SI o NO (debe ir en mayusculas)

// Ruta y contraseña de los certificados
$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

// Datos de la factura
$datos['factura']['Descuento'] = '0.00';
$datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s');
$datos['factura']['Folio'] = '4764';
$datos['factura']['FormaPago'] = '01';
$datos['factura']['LugarExpedicion'] = '91500';
$datos['factura']['MetodoPago'] = 'PUE';
$datos['factura']['Moneda'] = 'MXN';
$datos['factura']['Serie'] = 'A';
$datos['factura']['SubTotal'] = '775.00';
$datos['factura']['TipoDeComprobante'] = 'I';
$datos['factura']['Total'] = '899.00';

// Datos del emisor
$datos['emisor']['Nombre'] = 'CINDEMEX SA DE CV';
$datos['emisor']['RegimenFiscal'] = '601';
$datos['emisor']['Rfc'] = 'LAN7008173R5';

// Datos del receptor
$datos['receptor']['Rfc'] = 'XAXX010101000';
$datos['receptor']['UsoCFDI'] = 'P01';

// Datos del concepto
$datos['conceptos']['0']['Cantidad'] = '1.000000';
$datos['conceptos']['0']['ClaveProdServ'] = '01010101';
$datos['conceptos']['0']['ClaveUnidad'] = 'ACT';
$datos['conceptos']['0']['Descripcion'] = 'Venta';
$datos['conceptos']['0']['Importe'] = '384.482758';
$datos['conceptos']['0']['NoIdentificacion'] = '157231';
$datos['conceptos']['0']['ValorUnitario'] = '384.482758';

// Impuestos del concepto
$datos['conceptos']['0']['Impuestos']['Traslados']['0']['Base'] = '384.482758';
$datos['conceptos']['0']['Impuestos']['Traslados']['0']['Importe'] = '61.517241';
$datos['conceptos']['0']['Impuestos']['Traslados']['0']['Impuesto'] = '002';
$datos['conceptos']['0']['Impuestos']['Traslados']['0']['TasaOCuota'] = '0.160000';
$datos['conceptos']['0']['Impuestos']['Traslados']['0']['TipoFactor'] = 'Tasa';

// Datos del concepto
$datos['conceptos']['1']['Cantidad'] = '1.000000';
$datos['conceptos']['1']['ClaveProdServ'] = '01010101';
$datos['conceptos']['1']['ClaveUnidad'] = 'ACT';
$datos['conceptos']['1']['Descripcion'] = 'Venta';
$datos['conceptos']['1']['Importe'] = '390.517243';
$datos['conceptos']['1']['NoIdentificacion'] = '157232';
$datos['conceptos']['1']['ValorUnitario'] = '390.517243';

// Impuestos del Concepto
$datos['conceptos']['1']['Impuestos']['Traslados']['0']['Base'] = '390.517243';
$datos['conceptos']['1']['Impuestos']['Traslados']['0']['Importe'] = '62.482759';
$datos['conceptos']['1']['Impuestos']['Traslados']['0']['Impuesto'] = '002';
$datos['conceptos']['1']['Impuestos']['Traslados']['0']['TasaOCuota'] = '0.160000';
$datos['conceptos']['1']['Impuestos']['Traslados']['0']['TipoFactor'] = 'Tasa';

// Totales de impuestos
$datos['impuestos']['TotalImpuestosTrasladados']='124.00';
$datos['impuestos']['translados']['0']['Importe'] = '124.00';
$datos['impuestos']['translados']['0']['Impuesto'] = '002';
$datos['impuestos']['translados']['0']['TasaOCuota'] = '0.160000';
$datos['impuestos']['translados']['0']['TipoFactor'] = 'Tasa';

// Se envia a timbrar
$res = mf_genera_cfdi($datos);

echo "<h1>Respuesta Generar XML y Timbrado</h1>";
foreach ($res AS $variable => $valor) {
    $valor = htmlentities($valor);
    $valor = str_replace('&lt;br/&gt;', '<br/>', $valor);
    echo "<b>[$variable]=</b>$valor<hr>";
}