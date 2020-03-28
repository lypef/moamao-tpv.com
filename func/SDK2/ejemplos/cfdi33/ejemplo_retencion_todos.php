<?php
// Se desactivan los mensajes de debug
error_reporting(~(E_WARNING|E_NOTICE));
//error_reporting(E_ALL);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

// Se incluye el SDK
require_once '../../sdk2.php';
////////////////////////////////////////////////////////////
////// PRUEBA DIVIDENDOS
////////////////////////////////////////////////////////////

//multifacturas_modo_pruebas();

$datos['cfdi']='../../timbrados/ejemplo_retencion_dividendos.xml';
$datos['remueve_acentos']='SI';
$datos['retencion']='SI';

$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO'; //   [SI|NO]

$datos['conf']['cer'] = '../../certificados/lan7008173r5.cer.pem';
$datos['conf']['key'] = '../../certificados/lan7008173r5.key.pem';
$datos['conf']['pass'] = '12345678a';

//OPCIONAL, ACTIVAR SOLO EN CASO DE CONFLICTOS
//$datos['remueve_acentos']='SI';

//OPCIONAL, UTILIZAR LA LIBRERIA PHP DE OPENSSL, DEFAULT SI
$datos['php_openssl']='SI';

$datos['factura']['FolioInt'] = '21RET';
$datos['factura']['FechaExp'] = date('Y-m-d\TH:i:sP',time()-120);
$datos['factura']['CveRetenc'] = '08';
//$datos['factura']['DescRetenc'] = '004';

$datos['emisor']['RFCEmisor'] = 'LAN7008173R5';
$datos['emisor']['NomDenRazSocE'] = 'Empresa DEMO para Rentenciones S de TST';
//$datos['emisor']['CURPE'] = 'GAAR930830HNLMNL02';


$datos['receptor']['Nacionalidad'] = 'Nacional';
$datos['receptor']['Nacional']['RFCRecep'] = 'SOHM7509289MA';
$datos['receptor']['Nacional']['NomDesRazSocR'] = 'MIGUEL ANGEL SOSA HERNANDEZ';
$datos['receptor']['Nacional']['CURPR'] = 'SOHM750928HCLSRG06';


//$datos['receptor']['Extranjero']['NumRegIdTrib'] = '';
//$datos['receptor']['Extranjero']['NomDenRazSocR'] = '';

$datos['periodo']['MesIni'] = '10';
$datos['periodo']['MesFin'] = '10';
$datos['periodo']['Ejerc'] = '2017';

$datos['totales']['montoTotOperacion'] = '10000.00';
$datos['totales']['montoTotGrav'] = '9000.00';
$datos['totales']['montoTotExent'] = '0.00';
$datos['totales']['montoTotRet'] = '1000.00';

$datos['totales']['ImpRetenidos'][0]['BaseRet']=10000.00;
$datos['totales']['ImpRetenidos'][0]['Impuesto']='01';
$datos['totales']['ImpRetenidos'][0]['montoRet']='1000.00';
$datos['totales']['ImpRetenidos'][0]['TipoPagoRet']='Pago definitivo';

/*dividendos
$datos['dividendos']['DividOUtil']['CveTipDivOUtil']='04';
$datos['dividendos']['DividOUtil']['MontISRAcredRetMexico']='100.00';
$datos['dividendos']['DividOUtil']['MontISRAcredRetExtranjero']='200.00';
$datos['dividendos']['DividOUtil']['MontRetExtDivExt']='300.00';
$datos['dividendos']['DividOUtil']['TipoSocDistrDiv']='Sociedad Nacional';
$datos['dividendos']['DividOUtil']['MontISRAcredNal']='400.00';
$datos['dividendos']['DividOUtil']['MontDivAcumNal']='500.00';
$datos['dividendos']['DividOUtil']['MontDivAcumExt']='600.00';

$datos['dividendos']['Remanente']['ProporcionRem']='1000.00';*/



$res= cfdi_retenicion_generar_xml($datos,$produccion='NO');


echo "<h1>Respuesta </h1>";
foreach($res AS $variable=>$valor)
{
    $valor=htmlentities($valor);
    $valor=str_replace('&lt;br/&gt;','<br/>',$valor);
    echo "<b>[$variable]=</b>$valor<hr>";
}
?>