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
$datos['complemento'] = 'terceros11';

// Ruta del XML Timbrado
$datos['cfdi']='../../../timbrados/ejemplo_factura_terceros11.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../../timbrados/debug_ejemplo_factura_terceros11.xml';

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

/* Se agregan los Impuestos
$datos['impuestos']['TotalImpuestosTrasladados'] = '0.00';
$datos['impuestos']['translados'][0]['impuesto'] = '003';
$datos['impuestos']['translados'][0]['tasa'] = '0.160000';
$datos['impuestos']['translados'][0]['importe'] = '0.00';
$datos['impuestos']['translados'][0]['TipoFactor'] = 'Tasa';*/

// Complemento Terceros
$datos['terceros11']['rfc'] = 'SOHM7509289MA';
$datos['terceros11']['nombre'] = 'maria';

$datos['terceros11']['InformacionFiscalTercero']['calle'] = 'JUAREZ';
$datos['terceros11']['InformacionFiscalTercero']['noExterior'] = '7';
$datos['terceros11']['InformacionFiscalTercero']['noInterior'] = '1';
$datos['terceros11']['InformacionFiscalTercero']['colonia'] = 'DEL VALLE';
$datos['terceros11']['InformacionFiscalTercero']['localidad'] = 'MONTERREY';
$datos['terceros11']['InformacionFiscalTercero']['referencia'] = 'nuirenfuf';
$datos['terceros11']['InformacionFiscalTercero']['municipio'] = 'MONTERREY';
$datos['terceros11']['InformacionFiscalTercero']['estado'] = 'NUEVO LEON';
$datos['terceros11']['InformacionFiscalTercero']['pais'] = 'MEXICO';
$datos['terceros11']['InformacionFiscalTercero']['codigoPostal'] = '64610';

$datos['terceros11']['InformacionAduanera']['numero'] = '2331';
$datos['terceros11']['InformacionAduanera']['fecha'] = '2016-10-31';
$datos['terceros11']['InformacionAduanera']['aduana'] = 'miaduana';

$datos['terceros11']['Parte'][0]['InformacionAduanera'][0]['numero'] = '15';
$datos['terceros11']['Parte'][0]['InformacionAduanera'][0]['fecha'] = '2017-05-25';
$datos['terceros11']['Parte'][0]['InformacionAduanera'][0]['aduana'] = '56vs';

$datos['terceros11']['Parte'][0]['cantidad'] = '1';
$datos['terceros11']['Parte'][0]['unidad'] = 'pieza';
$datos['terceros11']['Parte'][0]['noIdentificacion'] = 'cod3';
$datos['terceros11']['Parte'][0]['descripcion'] = 'algo';
$datos['terceros11']['Parte'][0]['valorUnitario'] = '10.00';
$datos['terceros11']['Parte'][0]['importe'] = '10.00';

$datos['terceros11']['CuentaPredial']['numero'] = '56';

$datos['terceros11']['Impuestos']['Retenciones'][0]['impuesto'] = 'IVA';
$datos['terceros11']['Impuestos']['Retenciones'][0]['importe'] = '0.60';

$datos['terceros11']['Impuestos']['Traslados'][0]['impuesto'] = 'IVA';
$datos['terceros11']['Impuestos']['Traslados'][0]['tasa'] = '0.16';
$datos['terceros11']['Impuestos']['Traslados'][0]['importe'] = '0.60';

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