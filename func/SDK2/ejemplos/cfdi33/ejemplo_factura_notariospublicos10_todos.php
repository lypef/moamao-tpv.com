<?php
// Se desactivan los mensajes de debug
error_reporting(0);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';

// Se especifica la version de CFDi 3.3
$datos['version_cfdi'] = '3.3';
$datos['modulos_inter'] = 'debug';

// SE ESPECIFICA EL COMPLEMENTO
$datos['complemento'] = 'notariospublicos10';

// Ruta del XML Timbrado
$datos['cfdi']='../../timbrados/ejemplo_factura_notariospublicos10.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../timbrados/debug_ejemplo_factura_notariospublicos10.xml';

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

$datos['notariospublicos10']['DatosNotario']['CURP']='AAQM010101HCSMNZ00';
$datos['notariospublicos10']['DatosNotario']['NumNotaria']='3';
$datos['notariospublicos10']['DatosNotario']['EntidadFederativa']='16';
$datos['notariospublicos10']['DatosNotario']['Adscripcion']='Guanajuato';

$datos['notariospublicos10']['DatosOperacion']['NumInstrumentoNotarial']='12345';
$datos['notariospublicos10']['DatosOperacion']['FechaInstNotarial']='2014-04-22';
$datos['notariospublicos10']['DatosOperacion']['MontoOperacion']='1234.56';
$datos['notariospublicos10']['DatosOperacion']['Subtotal']='1234.56';
$datos['notariospublicos10']['DatosOperacion']['IVA']='1234.56';

$datos['notariospublicos10']['DatosEnajenante']['CoproSocConyugalE']='Si';

$datos['notariospublicos10']['DatosEnajenante']['DatosUnEnajenante']['CURP']='AAAA010101HCLJND07';
$datos['notariospublicos10']['DatosEnajenante']['DatosUnEnajenante']['RFC']='HSJ600903MN0';
$datos['notariospublicos10']['DatosEnajenante']['DatosUnEnajenante']['ApellidoMaterno']='Garcia';
$datos['notariospublicos10']['DatosEnajenante']['DatosUnEnajenante']['ApellidoPaterno']='Alvarado';
$datos['notariospublicos10']['DatosEnajenante']['DatosUnEnajenante']['Nombre']='Aimee';

$datos['notariospublicos10']['DatosAdquiriente']['DatosUnAdquiriente']['CURP']='AAAA010101HCLJND07';
$datos['notariospublicos10']['DatosAdquiriente']['DatosUnAdquiriente']['RFC']='HSJ600903MN0';
$datos['notariospublicos10']['DatosAdquiriente']['DatosUnAdquiriente']['ApellidoMaterno']='Alvarado';
$datos['notariospublicos10']['DatosAdquiriente']['DatosUnAdquiriente']['ApellidoPaterno']='Castro';
$datos['notariospublicos10']['DatosAdquiriente']['DatosUnAdquiriente']['Nombre']='Sebastian';

$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][0]['Porcentaje']='60.00';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][0]['CURP']='OAAJ840102HJCVRN00';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][0]['RFC']='HSJ600903MN0';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][0]['ApellidoMaterno']='Guzman';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][0]['ApellidoPaterno']='Rodriguez';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][0]['Nombre']='Demo';

$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][1]['Porcentaje']='40.00';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][1]['CURP']='OAAJ840102HJCVRN00';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][1]['RFC']='MSB600304KL9';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][1]['ApellidoMaterno']='Hernandez';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][1]['ApellidoPaterno']='Perez';
$datos['notariospublicos10']['DatosEnajenante']['DatosEnajenantesCopSC'][1]['Nombre']='Demitria';

$datos['notariospublicos10']['DatosAdquiriente']['CoproSocConyugalE']='Si';

$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][0]['Porcentaje']='60.00';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][0]['CURP']='OAAJ840102HJCVRN00';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][0]['RFC']='HSJ600903MN0';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][0]['ApellidoMaterno']='Luna';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][0]['ApellidoPaterno']='Ochoa';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][0]['Nombre']='Mario';

$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][1]['Porcentaje']='40.00';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][1]['CURP']='OAAJ840102HJCVRN00';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][1]['RFC']='MSB600304KL9';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][1]['ApellidoMaterno']='Ruiz';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][1]['ApellidoPaterno']='Garcia';
$datos['notariospublicos10']['DatosAdquiriente']['DatosAdquirientesCopSC'][1]['Nombre']='Perla';

$datos['notariospublicos10']['DescInmuebles'][0]['CodigoPostal']='12345';
$datos['notariospublicos10']['DescInmuebles'][0]['Pais']='MEX';
$datos['notariospublicos10']['DescInmuebles'][0]['Estado']='14';
$datos['notariospublicos10']['DescInmuebles'][0]['Municipio']='Oaxaca';
$datos['notariospublicos10']['DescInmuebles'][0]['Localidad']='Oaxaca';
$datos['notariospublicos10']['DescInmuebles'][0]['Colonia']='Bondojito';
$datos['notariospublicos10']['DescInmuebles'][0]['NoInterior']='B';
$datos['notariospublicos10']['DescInmuebles'][0]['NoExterior']='123';
$datos['notariospublicos10']['DescInmuebles'][0]['Calle']='Av. Siempre Viva';
$datos['notariospublicos10']['DescInmuebles'][0]['TipoInmueble']='01';

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