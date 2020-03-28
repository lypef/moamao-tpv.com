<?php
//C:\multifacturas_sdk\php -c C:\multifacturas_sdk\php.ini -f c:\test.php
date_default_timezone_set('America/Mexico_City');

include_once "../../sdk2.php";

//$datos['ruta']='C:\multifacturas_sdk\\';
$datos['archivo']='../../timbrados/ejemplo_contabilida_electronica_polizas.xml';
$datos['tipo'] = 'poliza';

$datos['PAC']['usuario'] = 'DEMO700101XXX';
$datos['PAC']['pass'] = 'DEMO700101XXX';
$datos['PAC']['produccion'] = 'NO'; //   [SI|NO]
$datos['conf']['cer'] = '../../certificados/aaa010101aaa.cer.pem';
$datos['conf']['key'] = '../../certificados/aaa010101aaa.key.pem';
$datos['conf']['pass'] = '12345678a';

$datos['modulo'] = 'contabilidad';
$datos['CC']['Ejercicio'] = '2015';
$datos['CC']['Periodo'] = '01';

/// Polizas ///
$datos['factura']['Polizas']['RFC'] = 'AAA010101AAA';
$datos['factura']['Polizas']['Mes'] = '07';
$datos['factura']['Polizas']['Anio'] = '2015';
$datos['factura']['Polizas']['TipoSolicitud'] = 'AF';
$datos['factura']['Polizas']['NumOrden'] = 'AAA0000000/00';
/// Polizas => Poliza ///
$datos['factura']['Polizas']['Poliza'][0]['NumUnIdenPol'] = '1968';
$datos['factura']['Polizas']['Poliza'][0]['Fecha'] = '2014-12-06';
$datos['factura']['Polizas']['Poliza'][0]['Concepto'] = 'Póliza de ingresos 1';
/// Polizas => Poliza => Transaccion ///
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['NumCta'] = '00010001';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['DesCta'] = 'XXXX';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Concepto'] = 'Venta de mercancía';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Debe'] = '0.00';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Haber'] = '400.50';
/// Polizas => Poliza => Transaccion => Cheque ///
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['Num'] = '123456';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['BanEmisNal'] = '106';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['BanEmisExt'] = 'Banco Emisor Extranjero';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['CtaOri'] = '12345678910';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['Fecha'] = '2014-12-06';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['Benef'] = 'Empresa';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['RFC'] = 'AAA010101AAA';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['Monto'] = '200.50';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['Moneda'] = 'MXN';
$datos['factura']['Polizas']['Poliza'][0]['Transaccion'][0]['Cheque'][0]['TipCamb'] = '1.0';

/// Polizas => Poliza ///
$datos['factura']['Polizas']['Poliza'][1]['NumUnIdenPol'] = '1969';
$datos['factura']['Polizas']['Poliza'][1]['Fecha'] = '2014-12-07';
$datos['factura']['Polizas']['Poliza'][1]['Concepto'] = 'Póliza de ingresos 2';
/// Polizas => Poliza => Transaccion ///
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['NumCta'] = '00010001';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['DesCta'] = 'XXXX';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Concepto'] = 'Venta de mercancía';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Debe'] = '0.00';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Haber'] = '400.50';
/// Polizas => Poliza => Transaccion => Cheque ///
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['Num'] = '123456';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['BanEmisNal'] = '106';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['BanEmisExt'] = 'Banco Emisor Extranjero';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['CtaOri'] = '12345678910';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['Fecha'] = '2014-12-06';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['Benef'] = 'Empresa';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['RFC'] = 'AAA010101AAA';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['Monto'] = '200.50';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['Moneda'] = 'MXN';
$datos['factura']['Polizas']['Poliza'][1]['Transaccion'][0]['Cheque'][0]['TipCamb'] = '1.0';



$res = cargar_modulo_multifacturas($datos);

print_r($res);

