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
$datos['complemento'] = 'renovacionvehiculos10';

// Ruta del XML Timbrado
$datos['cfdi']='../../../timbrados/ejemplo_factura_renovacionvehiculos10.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../../timbrados/debug_ejemplo_factura_renovacionvehiculos10.xml';

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

// Complemento de Renovacion de Vehiculos
$datos['renovacionvehiculos10']['TipoDeDecreto']='02';
$datos['renovacionvehiculos10']['DecretoRenovVehicular']['VehEnaj']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['PrecioVehUsado']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['TipoVeh']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['Marca']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['TipooClase']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['Año']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['Modelo']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['NIV']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['NumSerie']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['NumPlacas']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['NumMotor']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['NumFolTarjCir']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['NumPedIm']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['Aduana']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['FechaRegulVeh']='A1';
$datos['renovacionvehiculos10']['DecretoRenovVehicular'][0]['Foliofiscal']='A1';

$datos['renovacionvehiculos10']['DecretoRenovVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['Año']='2012';
$datos['renovacionvehiculos10']['DecretoRenovVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['Modelo']='fre';
$datos['renovacionvehiculos10']['DecretoRenovVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['NumPlacas']='786dfsr';
$datos['renovacionvehiculos10']['DecretoRenovVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['RFC']='fg4378gv86t2374';

$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehEnaj']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['PrecioVehUsado']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['TipoVeh']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['Marca']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['TipooClase']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['Año']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['Modelo']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NIV']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NumSerie']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NumPlacas']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NumMotor']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NumFolTarjCir']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NumFolAvisoint']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['NumPedIm']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['Aduana']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['FechaRegulVeh']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoUsadoEnajenadoPermAlFab']['Foliofiscal']='A1';

$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['Año']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['Modelo']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['NumPlacas']='A1';
$datos['renovacionvehiculos10']['DecretoSustitVehicular']['VehiculoNuvoSemEnajenadoFabAlPerm']['RFC']='A1';

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