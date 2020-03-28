<?php
// Se desactivan los mensajes de debug
error_reporting(~(E_WARNING|E_NOTICE));
//error_reporting(E_ALL);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';

$datos['complemento'] = 'implocal10';

// Se especifica la version de CFDi 3.3
$datos['version_cfdi'] = '3.3';

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
$datos['factura']['subtotal'] = 298.00;
$datos['factura']['tipocambio'] = 1;
$datos['factura']['tipocomprobante'] = 'E';
$datos['factura']['total'] = 345.68;
$datos['factura']['RegimenFiscal'] = '601';

// Datos del Emisor
$datos['emisor']['rfc'] = 'LAN7008173R5'; //RFC DE PRUEBA
$datos['emisor']['nombre'] = 'ACCEM SERVICIOS EMPRESARIALES SC';  // EMPRESA DE PRUEBA

// Datos del Receptor
$datos['receptor']['rfc'] = 'ACO0510202G0';
$datos['receptor']['nombre'] = 'COSMETICA, S. A. DE C. V.';
$datos['receptor']['UsoCFDI'] = 'G02';

// Se agregan los conceptos

$datos['conceptos'][0]['cantidad'] = 1.00;
$datos['conceptos'][0]['unidad'] = 'NA';
$datos['conceptos'][0]['ID'] = "1726";
$datos['conceptos'][0]['descripcion'] = "un bubu";
$datos['conceptos'][0]['valorunitario'] = 99.00;
$datos['conceptos'][0]['importe'] = 99.00;
$datos['conceptos'][0]['ClaveProdServ'] = '01010101';
$datos['conceptos'][0]['ClaveUnidad'] = 'ACT';

$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Base'] = 99.00;
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Impuesto'] = '002';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['TipoFactor'] = 'Tasa';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['TasaOCuota'] = '0.160000';
$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Importe'] = 15.84;


$datos['conceptos'][1]['cantidad'] = 1.00;
$datos['conceptos'][1]['unidad'] = 'NA';
$datos['conceptos'][1]['ID'] = "1586";
$datos['conceptos'][1]['descripcion'] = "PRODUCTO DE PRUEBA";
$datos['conceptos'][1]['valorunitario'] = 199.00;
$datos['conceptos'][1]['importe'] = 199.00;
$datos['conceptos'][1]['ClaveProdServ'] = '01010101';
$datos['conceptos'][1]['ClaveUnidad'] = 'ACT';


$datos['conceptos'][1]['Impuestos']['Traslados'][0]['Base'] = 199.00;
$datos['conceptos'][1]['Impuestos']['Traslados'][0]['Impuesto'] = '002';
$datos['conceptos'][1]['Impuestos']['Traslados'][0]['TipoFactor'] = 'Tasa';
$datos['conceptos'][1]['Impuestos']['Traslados'][0]['TasaOCuota'] = '0.160000';
$datos['conceptos'][1]['Impuestos']['Traslados'][0]['Importe'] = 31.84;


// Se agregan los Impuestos
$datos['impuestos']['translados'][0]['impuesto'] = '002';
$datos['impuestos']['translados'][0]['tasa'] = '0.160000';
$datos['impuestos']['translados'][0]['importe'] = 47.68;
$datos['impuestos']['translados'][0]['TipoFactor'] = 'Tasa';


$datos['impuestos']['TotalImpuestosTrasladados'] = 47.68;

// Impuestos Locales 1.0
$datos['implocal10']['TotaldeRetenciones'] = '10';
$datos['implocal10']['TotaldeTraslados'] = '10';

$datos['implocal10']['RetencionesLocales'][0]['ImpLocRetenido'] = '10';
$datos['implocal10']['RetencionesLocales'][0]['TasadeRetencion'] = '10';
$datos['implocal10']['RetencionesLocales'][0]['Importe'] = '10';

$datos['implocal10']['TrasladosLocales'][0]['ImpLocTrasladado'] = '10';
$datos['implocal10']['TrasladosLocales'][0]['TasadeTraslado'] = '10';
$datos['implocal10']['TrasladosLocales'][0]['Importe'] = '10';


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