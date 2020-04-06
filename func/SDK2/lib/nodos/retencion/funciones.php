<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Timbra una retencion
 * @param $pac integer
 * @param $usuario string
 * @param $clave string
 * @param $xml string
 * @return mixed
 */
if(!function_exists('mf_timbrar_retencion'))
{
    function mf_timbrar_retencion($pac, $usuario, $clave, $xml)
    {
        global $__mf_constantes__;
        global $__mf_modo_local__;
        global $__mf_servidor_local__;

        if(file_exists($xml) === true)
        {
            $xml = file_get_contents($xml);
        }

        // URL Web Service retenciones
        if($__mf_modo_local__ == true)
        {
            $urlws = "https://$__mf_servidor_local__/pac/timbrar_retenciones.php?wsdl";
        }
        else
        {
            $urlws = "https://pac$pac.multifacturas.com/pac/timbrar_retenciones.php?wsdl";
        }

        mf_carga_libreria($__mf_constantes__['__MF_LIBS_DIR__'] . 'nusoap/nusoap.php');

        $cliente = new nusoap_client($urlws);
        
        $params = array(
            'rfc' => $usuario,
            'clave' => $clave,
            'xml' => base64_encode(utf8_encode($xml)),
            'produccion' => $__mf_constantes__['__MF_PRODUCCION__']
        );

        $params['xml'] = base64_decode(utf8_decode($params['xml']));
        $res = $cliente->call('retencion', $params);
        // por compatibilidad se agrego el base64
        $res['cfdi'] = base64_encode($res['cfdi']);


        if($res['codigo_mf_numero'] != 0)
        {
            $res['abortar'] = true;
        }
        else
        {
            $res['abortar'] = false;
            $res['cfdi'] = base64_decode($res['cfdi']);
        }
        return $res;
    }
}

/////////////////////////////////////////////////////////////////////////////////
/**
 * _cfdi_almacena_error_()
 *
 * @return
 */
if(!function_exists('_cfdi_almacena_error_')) 
{
    function _cfdi_almacena_error_()
    {
        global $cfd_sin_timbrar;
        global $__mf_constantes__;
        $cfd_sin_timbrar=$cfd_sin_timbrar;
        @mkdir($__mf_constantes__['__MF_SDK_TMP__']);
        @chmod($__mf_constantes__['__MF_SDK_TMP__'],0777);
        $file_target = $__mf_constantes__['__MF_SDK_TMP__'].'ultimo_error.xml';

        @unlink($file_target);
        if (file_exists($file_target)) {
            @chmod($file_target, 0777);
        } // add write permission
        if (($wh = fopen($file_target, 'wb')) === false) {
            return "ERROR ESCRITURA EN  $file_target";
        } // error messages.
        if (fwrite($wh, $cfd_sin_timbrar) === false) {
            fclose($wh);
            return "ERROR ESCRITURA EN  $file_target";
        }
        fclose($wh);
        @chmod($file_target, 0777);
    }
}