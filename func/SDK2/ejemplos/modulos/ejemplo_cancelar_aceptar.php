<?php
error_reporting(E_ERROR);                        
include_once "../../sdk2.php";
$datos['modulo']="cancelacion2018"; 
$datos['accion']="aceptar";//TAMBIEN SE PUEDE ENVIAR 'RECHAZAR'                                                    
$datos["produccion"]="NO";                              
$datos["rfc"] ="LAN7008173R5";
$datos["password"]="12345678a";
$datos["uuid"]="25d57a90-77cc-4fe2-acf6-67a3c2f2508d";
//$datos["xml"]="../../timbrados/cfdi_ejemplo_factura.xml";
$datos["b64Cer"]="Certificados/lan7008173r5.cer";
$datos["b64Key"]="Certificados/lan7008173r5.key";
$res = mf_ejecuta_modulo($datos);
print_r($res);
/*NOTA: PARA REALIZAR LA CANCELACION SE REQUIERE EL UUID DE LA FACTURA A CANCELAR. 
OPCIONALMENTE PODRA ENVIAR EL XML Y DE AHI SE ESTRAERÁ EL UUID, POR LO CUAL DEBE DE ELGIR UNA DE LAS 2 OPCIONES. ($datos["uuid"] O $datos["xml"])
EN CASO DE QUE POR ERROR SE ENVIEN AMBOS PARAMETROS EL VALOR QUE SERA TOMADO EN CUENTA SERA EL QUE ESTÉ EN EL CAMPO UUID
Y SE IGNORARA LA FACTURA QUE SE ESPECIFIQUE EN EL CAMPO "$datos["xml"]"*/    