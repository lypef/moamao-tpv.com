<?php

error_reporting(~(E_NOTICE|E_WARNING));

date_default_timezone_set('America/Mexico_City');

require_once '../../sdk2.php';

$datos['complemento'] = 'comercioexterior11';
$datos['version_cfdi'] = '3.3';
$datos['cfdi']='../../timbrados/ejemplo_factura_comercio_exterior11.xml';
$datos['xml_debug']='../../timbrados/debug_ejemplo_factura_comercio_exterior11.xml';

$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO';

$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

$datos['factura']['condicionesDePago'] = 'CONDICIONES';
$datos['factura']['descuento'] = '0.00';
$datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s', time() - 120);
$datos['factura']['folio'] = '100';
$datos['factura']['forma_pago'] = '01';
$datos['factura']['LugarExpedicion'] = '45079';
$datos['factura']['metodo_pago'] = 'PUE';
$datos['factura']['moneda'] = 'MXN';
$datos['factura']['serie'] = 'A';
$datos['factura']['subtotal'] = '300.00';
$datos['factura']['tipocambio'] = '1';
$datos['factura']['tipocomprobante'] = 'I';
$datos['factura']['total'] = '300.00';

/*$datos['CfdisRelacionados']['TipoRelacion'] = '01';
$datos['CfdisRelacionados']['UUID'][0]='A39DA66B-52CA-49E3-879B-5C05185B0EF7';*/


//$datos['factura']['Confirmacion'] = '0234';
$datos['factura']['RegimenFiscal'] = '601';



$datos['emisor']['rfc'] = 'LAN7008173R5'; //RFC DE PRUEBA
$datos['emisor']['nombre'] = 'ACCEM SERVICIOS EMPRESARIALES SC';  // EMPRESA DE PRUEBA

$datos['receptor']['rfc'] = 'XEXX010101000';
$datos['receptor']['nombre'] = 'Extranjero';
$datos['receptor']['ResidenciaFiscal'] = 'USA';
$datos['receptor']['NumRegIdTrib'] = '1234567890';
$datos['receptor']['UsoCFDI'] = 'G03';

for ($i = 1; $i <= 3; $i++)
{
    $datos['conceptos'][$i]['cantidad'] = '1.00';
    $datos['conceptos'][$i]['unidad'] = 'PZ';
    $datos['conceptos'][$i]['ID'] = "123".$i;
    $datos['conceptos'][$i]['descripcion'] = "PRODUCTO $i";
    $datos['conceptos'][$i]['valorunitario'] = '100.00';
    $datos['conceptos'][$i]['importe'] = '100.00';
    $datos['conceptos'][$i]['ClaveProdServ'] = '01010101';
    $datos['conceptos'][$i]['ClaveUnidad'] = 'C81';
}

/*$datos['impuestos']['translados'][0]['impuesto'] = '001';
$datos['impuestos']['translados'][0]['tasa'] = '0.160000';
$datos['impuestos']['translados'][0]['importe'] = '16';
$datos['impuestos']['translados'][0]['TipoFactor'] = 'Tasa';*/

//$datos['impuestos']['retenciones'][0]['impuesto'] = 'ISR';
//$datos['impuestos']['retenciones'][0]['importe'] = '0.00';

$datos['comercioexterior11']['TipoOperacion'] = '2';
$datos['comercioexterior11']['ClaveDePedimento'] = 'A1';
$datos['comercioexterior11']['CertificadoOrigen'] = '0';
$datos['comercioexterior11']['Incoterm'] = 'FOB';
$datos['comercioexterior11']['Subdivision'] = '0';
$datos['comercioexterior11']['TipoCambioUSD'] = '20.00';
$datos['comercioexterior11']['TotalUSD'] = '15.00';

//$datos['comercioexterior11']['Emisor']['Curp'] = 'BAJS721028MDFMTR05';
$datos['comercioexterior11']['Emisor']['Domicilio']['Calle'] = 'Hidalgo';
$datos['comercioexterior11']['Emisor']['Domicilio']['NumeroExterior'] = '1000';
$datos['comercioexterior11']['Emisor']['Domicilio']['Colonia'] = '0209';
$datos['comercioexterior11']['Emisor']['Domicilio']['Municipio'] = '014';
$datos['comercioexterior11']['Emisor']['Domicilio']['Estado'] = 'QUE';
$datos['comercioexterior11']['Emisor']['Domicilio']['Pais'] = 'MEX';
$datos['comercioexterior11']['Emisor']['Domicilio']['CodigoPostal'] = '76224';

$datos['comercioexterior11']['Receptor']['Domicilio']['Calle'] = 'Avenue Sahara';
$datos['comercioexterior11']['Receptor']['Domicilio']['NumeroExterior'] = '74';
$datos['comercioexterior11']['Receptor']['Domicilio']['Colonia'] = 'BIG DESERT';
$datos['comercioexterior11']['Receptor']['Domicilio']['Estado'] = 'NV';
$datos['comercioexterior11']['Receptor']['Domicilio']['Pais'] = 'USA';
$datos['comercioexterior11']['Receptor']['Domicilio']['CodigoPostal'] = '45678';

$datos['comercioexterior11']['Destinatario'][0]['Domicilio']['Calle'] = 'Avenue Montesquieu';
$datos['comercioexterior11']['Destinatario'][0]['Domicilio']['NumeroExterior'] = '74';
$datos['comercioexterior11']['Destinatario'][0]['Domicilio']['Colonia'] = 'BIG DESERT';
$datos['comercioexterior11']['Destinatario'][0]['Domicilio']['Estado'] = 'NV';
$datos['comercioexterior11']['Destinatario'][0]['Domicilio']['Pais'] = 'USA';
$datos['comercioexterior11']['Destinatario'][0]['Domicilio']['CodigoPostal'] = '45678';

$datos['comercioexterior11']['Mercancias'][0]['NoIdentificacion'] = '1231';
$datos['comercioexterior11']['Mercancias'][0]['FraccionArancelaria'] = '94059102';
$datos['comercioexterior11']['Mercancias'][0]['CantidadAduana'] = '1';
$datos['comercioexterior11']['Mercancias'][0]['UnidadAduana'] = '01';
$datos['comercioexterior11']['Mercancias'][0]['ValorUnitarioAduana'] = '5.00';
$datos['comercioexterior11']['Mercancias'][0]['ValorDolares'] = '5.00';

$datos['comercioexterior11']['Mercancias'][1]['NoIdentificacion'] = '1232';
$datos['comercioexterior11']['Mercancias'][1]['FraccionArancelaria'] = '94059103';
$datos['comercioexterior11']['Mercancias'][1]['CantidadAduana'] = '1';
$datos['comercioexterior11']['Mercancias'][1]['UnidadAduana'] = '01';
$datos['comercioexterior11']['Mercancias'][1]['ValorUnitarioAduana'] = '5.00';
$datos['comercioexterior11']['Mercancias'][1]['ValorDolares'] = '5.00';

$datos['comercioexterior11']['Mercancias'][2]['NoIdentificacion'] = '1233';
$datos['comercioexterior11']['Mercancias'][2]['FraccionArancelaria'] = '94059104';
$datos['comercioexterior11']['Mercancias'][2]['CantidadAduana'] = '1';
$datos['comercioexterior11']['Mercancias'][2]['UnidadAduana'] = '01';
$datos['comercioexterior11']['Mercancias'][2]['ValorUnitarioAduana'] = '5.00';
$datos['comercioexterior11']['Mercancias'][2]['ValorDolares'] = '5.00';



$res = mf_genera_cfdi($datos);

///////////    MOSTRAR RESULTADOS DEL ARRAY $res   ///////////

echo "<h1>Respuesta Generar XML y Timbrado</h1>";
foreach($res AS $variable=>$valor)
{
    $valor=htmlentities($valor, ENT_IGNORE);
    $valor=str_replace('&lt;br/&gt;','<br/>',$valor);
    echo "<b>[$variable]=</b>$valor<hr>";
}

echo "<h1>Constantes</h1>";
$constantes = mf_constantes_sdk();
foreach($constantes as $name => $val)
{
    echo "<b>[$name]=</b>$val<hr>";
}
