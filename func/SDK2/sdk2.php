<?php

function mf_phpversion()
{
    $version = phpversion();
    $numversion = '';
    for($i = 0, $punto = 0; $i < strlen($version); $i++)
    {
        if($version[$i] == '.')
        {
            if($punto == 0)
            {
                $numversion .= $version[$i];
                $punto++;
            }
        }
        else
        {
            $numversion .= $version[$i];
        }
    }

	$version_final="$numversion";
	
	$ver=$version_final[0].'.'.$version_final[2];
	return $ver;
    //return doubleval($numversion);
}

function mf_postfijo_php()
{
    if($_SERVER['SERVER_ADDR']=='192.168.10.11')
    {
        return 'XX';
    }
    $php_version = mf_phpversion();

    switch($php_version)
    {
    	case 5.3: return '53'; break;
    	case 5.4: return '54'; break;
    	case 5.5: return '55'; break;
    	case 5.6: return '56'; break;
    	case 7.0: return '56'; break;
    	case 7.1: return '71'; break;
    	case 7.2: return '72'; break;
        default : echo "La version '$php_version' no es compatible";die();
    }

}

$php_version = mf_postfijo_php();
if($_SERVER['SERVER_ADDR']=='192.168.10.11')
{
    require_once 'sdk2XX.php';
}
else
{
	$ruta="sdk2$php_version.php";
    require_once $ruta;  
}