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
$datos['complemento'] = 'detallista131';

// Ruta del XML Timbrado
$datos['cfdi']='../../../timbrados/ejemplo_factura_detallista131.xml';

// Ruta del XML de Debug
$datos['xml_debug']='../../../timbrados/debug_ejemplo_factura_detallista131.xml';

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

// Complemento Detallista v1.3.1
$datos['detallista131']['documentStatus'] = 'COPY';
$datos['detallista131']['requestForPaymentIdentification']['entityType'] = 'INVOICE';
$datos['detallista131']['specialInstruction'][0]['textos'][0]['text'] = 'hasta15cadenas';
$datos['detallista131']['specialInstruction'][0]['code'] = 'ZZZ';
$datos['detallista131']['orderIdentification']['referenceIdentification'][0]['type'] = 'ON';
$datos['detallista131']['orderIdentification']['ReferenceDate'] = '2016-10-31';
$datos['detallista131']['AdditionalInformation']['referenceIdentification'][0]['type'] = 'ON';
$datos['detallista131']['DeliveryNote']['referenceIdentification'][0]['referenceIdentification'] = 'hasta30cadenas';
$datos['detallista131']['DeliveryNote']['ReferenceDate'] = '2016-10-31';
$datos['detallista131']['buyer']['gln'] = 'gthtr';
$datos['detallista131']['buyer']['contactInformation']['personOrDepartmentName']['text'] = 'mitexto';
$datos['detallista131']['seller']['gln'] = 'yun';
$datos['detallista131']['seller']['alternatePartyIdentification']['type'] = 'IEPS_REFERENCE';
$datos['detallista131']['shipTo']['gln'] = 'kui';
$datos['detallista131']['shipTo']['nameAndAddress'][0]['name'] = 'sergio';
$datos['detallista131']['shipTo']['nameAndAddress'][0]['streetAddressOne'] = 'torre del redentor';
$datos['detallista131']['shipTo']['nameAndAddress'][0]['city'] = 'torreon';
$datos['detallista131']['shipTo']['nameAndAddress'][0]['postalCode'] = '27000';
$datos['detallista131']['InvoiceCreator']['gln'] = 'jtyuj';
$datos['detallista131']['InvoiceCreator']['alternatePartyIdentification']['type'] = 'VA';
$datos['detallista131']['InvoiceCreator']['nameAndAddress']['name'] = 'Blanca';
$datos['detallista131']['InvoiceCreator']['nameAndAddress']['streetAddressOne'] = 'torre blanca';
$datos['detallista131']['InvoiceCreator']['nameAndAddress']['city'] = 'torreon';
$datos['detallista131']['InvoiceCreator']['nameAndAddress']['postalCode'] = '27200';
$datos['detallista131']['Customs'][0]['gln'] = 'trece';
$datos['detallista131']['currency'][0]['currencyFunction'][0]['currencyFunction'] = 'BILLING_CURRENCY';
$datos['detallista131']['currency'][0]['rateOfChange'] = '12.8';
$datos['detallista131']['currency'][0]['currencyISOCode'] = 'USD';
$datos['detallista131']['paymentTerms']['netPayment']['paymentTimePeriod']['timePeriodDue']['value'] = 'Ano';
$datos['detallista131']['paymentTerms']['netPayment']['paymentTimePeriod']['timePeriodDue']['timePeriod'] = 'DAYS';
$datos['detallista131']['paymentTerms']['netPayment']['netPaymentTermsType'] = 'DAYS';
$datos['detallista131']['paymentTerms']['discountPayment']['percentage'] = '1%';
$datos['detallista131']['paymentTerms']['discountPayment']['discountType'] = 'SANCTION';
$datos['detallista131']['paymentTerms']['paymentTermsEvent'] = 'DATE_OF_INVOICE';
$datos['detallista131']['paymentTerms']['PaymentTermsRelationTime'] = 'REFERENCE_AFTER';
$datos['detallista131']['shipmentDetail'] = 'LIHHI';
$datos['detallista131']['allowanceCharge']['allowanceChargeType'] = 'ALLOWANCE_GLOBAL';
$datos['detallista131']['allowanceCharge']['settlementType'] = 'BILL_BACK';
$datos['detallista131']['allowanceCharge']['sequenceNumber'] = 'del1al15';
$datos['detallista131']['allowanceCharge']['specialServicesType'] = 'RAA';
$datos['detallista131']['allowanceCharge']['monetaryAmountOrPercentage']['rate']['percentage'] = '21.32';
$datos['detallista131']['allowanceCharge']['monetaryAmountOrPercentage']['rate']['base'] = 'INVOICE_VALUE';
$datos['detallista131']['lineItem'][0]['tradeItemIdentification']['gtin'] = 'btyu';
$datos['detallista131']['lineItem'][0]['alternateTradeItemIdentification'][0]['type'] = 'SERIAL_NUMBER';
$datos['detallista131']['lineItem'][0]['tradeItemDescriptionInformation']['longText'] = 'textolargo';
$datos['detallista131']['lineItem'][0]['tradeItemDescriptionInformation']['language'] = 'ES';
$datos['detallista131']['lineItem'][0]['invoicedQuantity']['unitOfMeasure'] = 'ernyt';
$datos['detallista131']['lineItem'][0]['aditionalQuantity'][0]['QuantityType'] = 'FREE_GOODS';
$datos['detallista131']['lineItem'][0]['grossPrice']['Amount'] = '34.4';
$datos['detallista131']['lineItem'][0]['netPrice']['Amount'] = '23.8';
$datos['detallista131']['lineItem'][0]['AdditionalInformation']['referenceIdentification']['type'] = 'ON';
$datos['detallista131']['lineItem'][0]['Customs'][0]['gln'] = 'hastatrece';
$datos['detallista131']['lineItem'][0]['Customs'][0]['alternatePartyIdentification']['type'] = 'TN';
$datos['detallista131']['lineItem'][0]['Customs'][0]['ReferenceDate'] = '20219';
$datos['detallista131']['lineItem'][0]['Customs'][0]['nameAndAddress'] = 'SERGIO TORRES';
$datos['detallista131']['lineItem'][0]['LogisticUnits']['serialShippingContainerCode']['type'] = 'BJ';
$datos['detallista131']['lineItem'][0]['palletInformation']['palletQuantity'] = 'PAREG';
$datos['detallista131']['lineItem'][0]['palletInformation']['description']['type'] = 'BOX';
$datos['detallista131']['lineItem'][0]['palletInformation']['transport']['methodOfPayment'] = 'PAID_BY_BUYER';
$datos['detallista131']['lineItem'][0]['extendedAttributes']['lotNumber'][0]['productionDate'] = '098984';
$datos['detallista131']['lineItem'][0]['allowanceCharge'][0]['specialServicesType'] = 'VAB';
$datos['detallista131']['lineItem'][0]['allowanceCharge'][0]['monetaryAmountOrPercentage']['percentagePerUnit'] = 'kjhw';
$datos['detallista131']['lineItem'][0]['allowanceCharge'][0]['monetaryAmountOrPercentage']['ratePerUnit']['amountPerUnit'] = 'jyu';
$datos['detallista131']['lineItem'][0]['allowanceCharge'][0]['allowanceChargeType'] = 'ALLOWANCE_GLOBAL';
$datos['detallista131']['lineItem'][0]['allowanceCharge'][0]['settlementType'] = 'OFF_INVOICE';
$datos['detallista131']['lineItem'][0]['allowanceCharge'][0]['sequenceNumber'] = 'ntyyu';
$datos['detallista131']['lineItem'][0]['tradeItemTaxInformation'][0]['taxTypeDescription'] = 'AAA';
$datos['detallista131']['lineItem'][0]['tradeItemTaxInformation'][0]['referenceNumber'] = 'kuhywe';
$datos['detallista131']['lineItem'][0]['tradeItemTaxInformation'][0]['tradeItemTaxAmount']['taxPercentage'] = '76.12';
$datos['detallista131']['lineItem'][0]['tradeItemTaxInformation'][0]['tradeItemTaxAmount']['taxAmount'] = '43.6';
$datos['detallista131']['lineItem'][0]['tradeItemTaxInformation'][0]['taxCategory'] = 'TRANSFERIDO';
$datos['detallista131']['lineItem'][0]['totalLineAmount']['grossAmount']['Amount'] = '65.8';
$datos['detallista131']['lineItem'][0]['totalLineAmount']['netAmount']['Amount'] = '63.9';
$datos['detallista131']['lineItem'][0]['type'] = 'SimpleInvoiceLineItemType';
$datos['detallista131']['lineItem'][0]['number'] = '123';
$datos['detallista131']['totalAmount']['Amount'] = '12.3';
$datos['detallista131']['TotalAllowanceCharge'][0]['specialServicesType'] = 'ABZ';
$datos['detallista131']['TotalAllowanceCharge'][0]['Amount'] = '54.9';
$datos['detallista131']['TotalAllowanceCharge'][0]['allowanceOrChargeType'] = 'ALLOWANCE';

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