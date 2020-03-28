<?php

// Se desactivan los mensajes de debug
error_reporting(0);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';

$datos['cfdi'] = '../../timbrados/ejemplo_cfdi_todos.xml';
$datos['xml_debug'] = '../../timbrados/sin_timbrar_ejemplo_cfdi_todos.xml';
$datos['remueve_acentos'] = 'NO';
$datos['RESPUESTA_UTF8'] = 'SI';
$datos['version_cfdi'] = '3.3';

$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO';

$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '';

$datos['factura']['serie'] = 'A';
$datos['factura']['folio'] = 'A';
$datos['factura']['fecha_expedicion'] = 'A';
$datos['factura']['metodo_pago'] = 'A';
$datos['factura']['forma_pago'] = 'A';
$datos['factura']['tipocomprobante'] = 'A';
$datos['factura']['LugarExpedicion'] = 'A';
$datos['factura']['RegimenFiscal'] = 'A';
$datos['factura']['subtotal'] = 'A';
$datos['factura']['descuento'] = 'A';
$datos['factura']['total'] = 'A';

$datos['emisor']['rfc'] = 'LAN7008173R5';
$datos['emisor']['nombre'] = 'A';

$datos['receptor']['rfc'] = 'A';
$datos['receptor']['nombre'] = 'A';
$datos['receptor']['UsoCFDI'] = 'A';

$datos['conceptos'][0]['cantidad'] = 'A';
$datos['conceptos'][0]['unidad'] = 'A';
$datos['conceptos'][0]['ClaveUnidad'] = 'A';
$datos['conceptos'][0]['ID'] = 'A';
$datos['conceptos'][0]['descuento'] = 'A';
$datos['conceptos'][0]['ClaveProdServ'] = 'A';
$datos['conceptos'][0]['descripcion'] = 'A';
$datos['conceptos'][0]['valorunitario'] = 'A';
$datos['conceptos'][0]['importe'] = 'A';
$datos['conceptos'][0]['predial'] = 'A';
$datos['conceptos'][0]['fecha'] = 'A';
$datos['conceptos'][0]['aduana'] = 'A';
$datos['conceptos'][0]['numero'] = 'A';

$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Base'] = 'A';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Impuesto'] = 'A';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['TipoFactor'] = 'A';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['TasaOCUota'] = 'A';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Importe'] = 'A';

$datos['conceptos'][0]['Impuestos']['Retenciones'][0]['Base'] = 'A';
$datos['conceptos'][0]['Impuestos']['Retenciones'][0]['Impuesto'] = 'A';
$datos['conceptos'][0]['Impuestos']['Retenciones'][0]['TipoFactor'] = 'A';
$datos['conceptos'][0]['Impuestos']['Retenciones'][0]['TasaOCuota'] = 'A';
$datos['conceptos'][0]['Impuestos']['Retenciones'][0]['Importe'] = 'A';

$datos['conceptos'][0]['Parte'][0]['ClaveProdServ'] = 'A';
$datos['conceptos'][0]['Parte'][0]['NoIdentificacion'] = 'A';
$datos['conceptos'][0]['Parte'][0]['Cantidad'] = 'A';
$datos['conceptos'][0]['Parte'][0]['Unidad'] = 'A';
$datos['conceptos'][0]['Parte'][0]['Descripcion'] = 'A';
$datos['conceptos'][0]['Parte'][0]['ValorUnitario'] = 'A';
$datos['conceptos'][0]['Parte'][0]['Importe'] = 'A';
$datos['conceptos'][0]['Parte'][0]['InformacionAduanera'][0]['NumPedimento'] = 'A';

$datos['impuestos']['translados'][0]['impuesto'] = 'A';
$datos['impuestos']['translados'][0]['tasa'] = 'A';
$datos['impuestos']['translados'][0]['importe'] = 'A';
$datos['impuestos']['translados'][0]['TipoFactor'] = 'A';

$datos['impuestos']['retenciones'][0]['impuesto'] = 'A';
$datos['impuestos']['retenciones'][0]['importe'] = 'A';

$res = mf_genera_cfdi($datos);

print_r($res);