<?php
    //ini_set( 'display_errors', 1 );
    //error_reporting( E_ALL );
    require_once 'db.php';
    // Dompdf php 7
    require_once '../dompdf_php7.1/autoload.inc.php';
    use Dompdf\Dompdf;

    // Dompdf php 5
    //require_once("dompdf/dompdf_config.inc.php");

    $ColorBarr = "#cc353a";
    
    $url = $_POST['url'];
    $user = $_POST['user'];

    $con = db_conectar();  
    
    $data_one = mysqli_query(db_conectar(),"SELECT f.folio, f.fecha_venta, u.comision FROM folio_venta f, users u WHERE f.open = 0 and f.comision_pagada = 0 and f.vendedor = u.id and f.vendedor = $user ");
    
    $body = '';
    $utilidad = 0;
    $porcent_comision = 0;
    $nombre_user = Return_NombreUser($user);
    $sueldo = Return_SueldoUser($user);
    
    while($one = mysqli_fetch_array($data_one))
    {
        $folio = $one[0]; $fecha = $one[1]; $porcent_comision = $one[2];
        
        $products = mysqli_query($con,"SELECT v.unidades, p.precio_costo, p.precio_normal, p.oferta, p.nombre FROM product_venta v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = $folio ");                
        while($temp1 = mysqli_fetch_array($products))
        {
            if (!$temp1[3])
            {
                $costo = $temp1[0] * $temp1[1];   
                $precio_p = $temp1[0] * $temp1[2];   
                $utilidad = $utilidad + ($precio_p - $costo);
                //$body .= "PRODUCTO: ".$temp1[4]." UNIDADDES: " . $temp1[0] . " PRECIO unitario: " . $temp1[2] . " Total: " . $precio_p . "<br>";
                $body_products .= '
                <tr>
                    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: left;">'.$temp1[4].'</td>
                    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: left;">'.GetFechaText($fecha).'</td>
                    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: center;">'.$temp1[0].'</td>
                    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">'.number_format($temp1[2],GetNumberDecimales(),".",",").'</td>
                    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">'.number_format($precio_p,GetNumberDecimales(),".",",").'</td>
                </tr>
                ';
           }
        }
            
        $genericos = mysqli_query($con,"SELECT v.unidades, v.precio, v.p_generico FROM product_venta v WHERE p_generico != '' and folio_venta = $folio ");    
            
        while($temp0 = mysqli_fetch_array($genericos))
        {
            $tmp = ($temp0[0] * $temp0[1]);
            $utilidad = $utilidad + $tmp;
            //$body .= "PRODUCTO: ".$temp0[2]." UNIDADDES: " . $temp0[0] . " PRECIO unitario: " . $temp0[1] . " Total: " . $tmp . "<br>";
            
            $body_products .= '
            <tr>
                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: left;">'.$temp0[2].'</td>
                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: left;">'.GetFechaText($fecha).'</td>
                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: center;">'.$temp0[0].'</td>
                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">'.number_format($temp0[1],GetNumberDecimales(),".",",").'</td>
                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">'.number_format($tmp,GetNumberDecimales(),".",",").'</td>
            </tr>
            ';
        }
    }
    
    $sueldo_pagar = $sueldo + ($utilidad * ($porcent_comision / 100));
    
    $body = '<style>
    @page {
        margin-top: 0.3em;
        margin-left: 0.6em;
        margin-right: 0.6em;
        margin-bottom: 3.0em;
    }
    </style>
    <body>
    <table width="100%" border="0">
        <tr>
            <td width="35%">
                <img src="../images/logolola.jpg" alt="Membrete" height="auto" width="350">
            </td>

            <td>
                <center>
                <h2 style="display:inline;">CLTA</h2>
                <br>CLTA D&D | GRUPO ASCGAR
                </center>
            </td>
        </tr>
    </table>
    
    <table width="100%" border="1" style="border-collapse: collapse;">
        <tr>
        <td width="100%">
            <strong>USUARIO: </strong>'.$nombre_user.'
        </td>
        </tr>
    </table>
    <br>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <tr>
        <th bgcolor="#5a94dd" style="width:40%; border-right:1px solid #5a94dd;border-left:1px solid #5a94dd;border-bottom:1px solid black;border-top:1px solid #5a94dd">PRODUCTO</th> 
        <th bgcolor="#5a94dd" style="width:25%;border-right:1px solid #5a94dd;border-left:1px solid #5a94dd;border-bottom:1px solid black;border-top:1px solid #5a94dd">FECHA VENTA</th> 
        <th bgcolor="#5a94dd" style="border-right:1px solid #5a94dd;border-left:1px solid #5a94dd;border-bottom:1px solid black;border-top:1px solid #5a94dd">UNIDADES</th> 
        <th bgcolor="#5a94dd" style="border-right:1px solid #5a94dd;border-left:1px solid #5a94dd;border-bottom:1px solid black;border-top:1px solid #5a94dd">PRECIO UNITARIO</th>
        <th bgcolor="#5a94dd" style="border-right:1px solid #5a94dd;border-left:1px solid #5a94dd;border-bottom:1px solid black;border-top:1px solid #5a94dd">TOTAL</th>
        </tr>
        '.$body_products.'
    </table>
    
    <br>
    <table style="height: 5px;" width="100%">
    <tbody>
    <tr>
    <td bgcolor="#5a94dd" align="center"><strong>'.numtoletras($sueldo_pagar).'</strong></td>
    </tr>
    <tr>
    <td>
     <table width="100%">
    <tbody>
    <tr>
    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> UTILIDAD:</strong> $ '.number_format( $utilidad,GetNumberDecimales(),".",",").'</td>
    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> SUELDO:</strong> $ '.number_format( $sueldo,GetNumberDecimales(),".",",").'</td>
    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> COMISION '.$porcent_comision.' % :</strong> $ '.number_format( $utilidad* ($porcent_comision / 100),GetNumberDecimales(),".",",").'</td>
    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> TOTAL:</strong> $ '.number_format($sueldo_pagar,GetNumberDecimales(),".",",").'</td>
    </tr>
    </tbody>
    </table>
     
     </td>
    </tr>
    </tbody>
    </table>';
    
    mysqli_query(db_conectar(),"UPDATE `folio_venta` SET `comision_pagada` = '1' WHERE vendedor = $user ");  
    
    $body .= FooterPageReport();
    
    $body = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('letter');
    $dompdf->load_html($body);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("PAY".$nombre_user.".pdf");
?>