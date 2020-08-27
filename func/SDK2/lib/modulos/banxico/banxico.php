<?php
//https://www.banxico.org.mx/DgieWSWeb/DgieWS?WSDL

/**
 * La funcion siempre debe comenzar con tres guiones bajo y el nombre del mismo archivo PHP
 * SIN extension, y recibir una variable; esta variable puede tener el nombre que se desee.
 */
function ___banxico($datos)
{
    global $__mf_constantes__;
    // Si se recibieron todos los parametros se incluye nusoap
	if(!class_exists('nusoap_client'))
		require_once $__mf_constantes__['__MF_LIBS_DIR__'] . 'nusoap/nusoap.php';
	
	// Se crea el cliente
	$client = new nusoap_client('https://www.banxico.org.mx/DgieWSWeb/DgieWS?WSDL','wsdl');
	
	// Se envia la solicitud
	$resp = $client->call("tiposDeCambioBanxico", array(),"https://ws.dgie.banxico.org.mx","","","","rpc","https://schemas.xmlsoap.org/soap/encoding/","encoded");
    /*
     * Siempre se debe de regresar un arreglo asociativo, es decir que los datos que se
     * pretendan devolver siempre se puedan identificar/localizar por medio de una cadena
     */

	$doc = new DOMDocument();
	$doc->loadXML($resp);
	
	$monedas = array();
	
	$nodos = $doc->getElementsByTagNameNS('https://www.banxico.org.mx/structure/key_families/dgie/sie/series/compact', 'Series');
	
	for($i = 0; $i < $nodos->length; $i++) {
		$nodo = $nodos->item($i);
		$titulo = $nodo->getAttribute('TITULO');
		$titulo = preg_replace('/\s+/', ' ', $titulo);
		$hijo = $nodo->getElementsByTagNameNS('https://www.banxico.org.mx/structure/key_families/dgie/sie/series/compact', 'Obs')->item(0);
		$atributos = $hijo->attributes;
		$valor = $atributos->getNamedItem('OBS_VALUE')->value;
		
		if(strpos($titulo, 'Euro') !== FALSE) {
			$monedas['EUR'] = $valor;
		}
		
		if(strpos($titulo, 'Yen') !== FALSE) {
			$monedas['JPY'] = $valor;
		}
		
		if(strpos($titulo, 'FIX')) {
			$monedas['FIX'] = $atributos->getNamedItem('OBS_VALUE')->value;
		}
		
		if(strpos($titulo, 'liquidación')) {
			$monedas['USD'] = $atributos->getNamedItem('OBS_VALUE')->value;
		}
		
		if(strpos($titulo, 'esterlina')) {
			$monedas['GBP'] = $atributos->getNamedItem('OBS_VALUE')->value;
		}
	}
	
	$monedas['anotaciones'] = 'https://www.banxico.org.mx/repositorios/dgobc-web/sisfix/fix48.html';
	
    return array('resultado' => $monedas);
}

