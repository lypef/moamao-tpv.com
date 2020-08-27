<?php
// Se desactivan los mensajes de debug
error_reporting(~(E_WARNING|E_NOTICE));
//error_reporting(E_ALL);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';

$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO'; //   [SI|NO]
$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

//Version cfdi 3.3
$datos['version_cfdi'] = '3.3';
//RUTA DONDE ALMACENARA EL CFDI
$datos['cfdi']='../../timbrados/ejemplo_arrendamiento.xml';
// OPCIONAL GUARDAR EL XML GENERADO ANTES DE TIMBRARLO
$datos['xml_debug']='../../timbrados/debug_ejemplo_arrendamiento.xml';

//OPCIONAL, ACTIVAR SOLO EN CASO DE CONFLICTOS
//$datos['remueve_acentos']='SI';

//OPCIONAL, UTILIZAR LA LIBRERIA PHP DE OPENSSL, DEFAULT SI
$datos['php_openssl']='SI';

$datos['factura']['serie'] = 'A'; //opcional
$datos['factura']['folio'] = '100'; //opcional
$datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s',time()-120);// Opcional  "time()-120" para retrasar la hora 2 minutos para evitar falla de error en rango de fecha


$datos['factura']['metodo_pago'] = 'PPD'; // VER DOCUMENTACION :: EFECTIV0, CHEQUE, TARJETA DE CREDITO, TRANSFERENCIA BANCARIA, NO IDENTIFICADO
$datos['factura']['forma_pago'] = '01';  //PAGO EN UNA SOLA EXHIBICION, CREDITO 7 DIAS, CREDITO 15 DIAS, CREDITO 30 DIAS, ETC
$datos['factura']['tipocomprobante'] = 'I'; //ingreso, egreso
$datos['factura']['moneda'] = 'MXN'; // MXN USD EUR
$datos['factura']['tipocambio'] = 1; // OPCIONAL (MXN = 1.00, OTRAS EJ: USD = 13.45; EUR = 16.86)
$datos['factura']['LugarExpedicion'] = '27000';
//$datos['factura']['NumCtaPago'] = '0234'; //opcional; 4 DIGITOS pero obligatorio en transferencias y cheques

$datos['factura']['RegimenFiscal'] = '601';

/*$datos['factura']['FolioFiscalOrig'] = '3B985D09-26CD-4643-B39D-6D4F54E7D046';
$datos['factura']['SerieFolioFiscalOrig'] = 'A90';//OPCIONAL
$datos['factura']['FechaFolioFiscalOrig'] = '2017-07-27T02:12:58';
$datos['factura']['MontoFolioFiscalOrig'] = '12345.67';*/


$datos['emisor']['rfc'] = 'LAN7008173R5'; //RFC DE PRUEBA  
$datos['emisor']['nombre'] = 'ACCEM SERVICIOS EMPRESARIALES SC';  // EMPRESA DE PRUEBA

// IMPORTANTE PROBAR CON NOMBRE Y RFC REAL O GENERARA ERROR DE XML MAL FORMADO
$datos['receptor']['rfc'] = 'SOHM7509289MA';
$datos['receptor']['nombre'] = 'MIGUEL ANGEL SOSA HERNANDEZ';
$datos['receptor']['UsoCFDI'] = 'P01';

//AGREGAR 10 CONCEPTOS DE PRUEBA
	$concepto['ClaveProdServ'] = '84111506';
    $concepto['cantidad'] = '1';
    $concepto['unidad'] = 'NO APLICA';
    $concepto['ClaveUnidad'] = "A90"; //ID, REF, CODIGO O SKU DEL PRODUCTO
    $concepto['descripcion'] = "COBRO PARCIALIDAD DE VENTA A90";
    $concepto['valorunitario'] = '1000.00'; // SIN IVA
    $concepto['importe'] = '1000.00';

    $datos['conceptos'][0] = $concepto;
	$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Base'] = '1000.00';
	$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Impuesto'] = '002';
	$datos['conceptos'][0]['Impuestos']['Traslados'][0]['TasaOCuota'] = '0.160000';
	$datos['conceptos'][0]['Impuestos']['Traslados'][0]['Importe'] = '160.00'; 
	$datos['conceptos'][0]['Impuestos']['Traslados'][0]['TipoFactor'] = 'Tasa';

$datos['factura']['subtotal'] = 1000.00; // sin impuestos
$datos['factura']['total'] = 1160.00; // total incluyendo impuestos

$datos['impuestos']['TotalImpuestosTrasladados']='160.00';
$translado1['Impuesto'] = '002';
$translado1['TasaOCuota'] = '0.160000';
$translado1['Importe'] = '160.00'; // iva de los productos facturados
$translado1['TipoFactor'] = 'Tasa';
$datos['impuestos']['translados'][0] = $translado1;

// Se ejecuta el SDK
$res = mf_genera_cfdi($datos);

///////////    MOSTRAR RESULTADOS DEL ARRAY $res   ///////////
 
echo "<h1>Respuesta Generar XML y Timbrado</h1>";
foreach($res AS $variable=>$valor)
{
    $valor=htmlentities($valor);
    $valor=str_replace('&lt;br/&gt;','<br/>',$valor);
    echo "<b>[$variable]=</b>$valor<hr>";
}
?>