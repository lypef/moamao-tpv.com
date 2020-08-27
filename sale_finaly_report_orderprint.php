<?php
    require_once 'func/db.php';
    
    // Dompdf php 7
    //require_once 'dompdf_php7.1/autoload.inc.php';
    //use Dompdf\Dompdf;

    // Dompdf php 5
    require_once("dompdf_php5.6/dompdf_config.inc.php");
    
    $ColorBarr = ColorBarrReport();
    
    $folio = $_GET["folio_sale"];
    session_start();

    $con = db_conectar();  
    $venta = mysqli_query($con,"SELECT u.nombre, c.nombre, v.descuento, v.fecha, v.cobrado, v.fecha_venta, s.nombre, s.direccion, s.telefono, v.iva, c.razon_social, c.direccion FROM folio_venta v, users u, clients c, sucursales s WHERE v.vendedor = u.id and v.client = c.id and v.sucursal = s.id and v.folio = '$folio'");
    
    $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id, product  FROM product_pedido v WHERE p_generico != '' and folio_venta = $folio");

    while($row = mysqli_fetch_array($venta))
    {
        $vendedor = $row[0];
        $cliente = $row[1];
        $descuento = $row[2];
        $fecha_ini = $row[3];
        $cobrado = $row[4];
        $fecha_fini = $row[5];
        $sucursal = $row[6];
        $direccion = $row[7];
        $tel = $row[8];
        $iva = $row[9];
        $bodysucursal = $row[7] . '
        <br><span style="font-size: 14px;">RESPONSABLE: ' . $vendedor . '</span>';
        $r_social = $row[10];
        $cliente_direccion = $row[11];
    }

    $products = mysqli_query($con,"SELECT p.nombre, p.`no. De parte`, v.unidades, v.precio , a.nombre, p.loc_almacen, p.stock FROM product_pedido v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = '$folio'");
    $body_products = '';
    while($row = mysqli_fetch_array($products))
    {
        if ($row[6] <= 0)
        {
            $asterisk = '***';            
        }else
        {
            $asterisk = '';            
        }
        
        if (!$row[6])
        {
            $ubicacion = substr($row[4],0,3) . ' ' . $row[5];
        }
        else
        {
            $ubicacion = Almacen_ubicacion_p_sub($row[6]);
        }

        $total_sin = $total_sin + ($row[2] * $row[3]);

        $body_products = $body_products . '
        </tr>
        <tr>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none"><center>'.$row[2].'</center></td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none">'.$asterisk.' ('.$row[1].') '.$row[0].'</td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none; font-size:10; ">'.$ubicacion.'</td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none; text-align: right;">
                <table border="0" width="100%">
                    <tr>
                        <td align="left"> $</td>
                        <td align="right">
                        '.number_format($row[3] / 1.160000,GetNumberDecimales(),".",",").'
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none; text-align: right;">
                <table border="0" width="100%">
                    <tr>
                        <td align="left"> $</td>
                        <td align="right">
                        '.number_format(($row[2] * $row[3]) / 1.160000,GetNumberDecimales(),".",",").'
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        ';
    }
    
    while($row = mysqli_fetch_array($genericos))
    {
        $total_sin = $total_sin + ($row[0] * $row[2]);

        $body_products = $body_products . '
        </tr>
        <tr>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none"><center>'.$row[0].'</center></td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none">*** (NA) '.$row[1].'</td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none"><center>NA</center></td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none; text-align: right;">
                <table border="0" width="100%">
                    <tr>
                        <td align="left"> $</td>
                        <td align="right">
                        '.number_format($row[2] / 1.160000,GetNumberDecimales(),".",",").'
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom:none;border-top:none; text-align: right;">
                <table border="0" width="100%">
                    <tr>
                        <td align="left"> $</td>
                        <td align="right">
                        '.number_format(($row[0] * $row[2]) / 1.160000,GetNumberDecimales(),".",",").'
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        ';
    }

    $ivac = '.'.$iva;

    $total_pagar = $total_sin - ($total_sin * ($descuento / 100));
    $total_pagar_ = $total_pagar;
    
    $subtotal = $total_pagar / 1.160000;

    $iva_ = $total_pagar - $subtotal;

    $subtotal = number_format($subtotal,GetNumberDecimales(),".",",");
    $total_pagar = number_format($total_pagar,GetNumberDecimales(),".",",");
    $iva_ = number_format($iva_,GetNumberDecimales(),".",",");
    
    $abonos = mysqli_query($con,"SELECT folio, cobrado, fecha_venta FROM folio_venta WHERE folio_venta_ini = '$folio'");

    $pagos .= '
    <tr>
        <td align="right"><hr></td>
        <td align="right"><hr></td>
    </tr>
    ';
    while($row = mysqli_fetch_array($abonos))
    {
        if ($row[1] > 0)
        {
            $pagos .= '
            <tr>
                <td align="right">
                <strong> ABONO: $</strong>
                </td>
                <td align="right">
                '.number_format($row[1],GetNumberDecimales(),".",",").'
                </td>
            </tr>
            ';
            $total_abono = $total_abono + $row[1];
        }
    }
    $pagos .= '
    <tr>
        <td align="right"><hr></td>
        <td align="right"><hr></td>
    </tr>
    ';
    $pagos .= '
    <tr>
        <td align="right"><strong>ADEUDO: $</strong></td>
        <td align="right">'.number_format(($total_pagar_ - $total_abono),GetNumberDecimales(),".",",").'</td>
    </tr>
    ';
    
    $descuento_body = "";
    
    if ($descuento > 0)
    {
        $descuento_body = '
        <tr>
            <td align="right">
            <strong> DESC '.$descuento . ' %: $</strong>
            </td>
            <td align="right">
             - '.number_format(($total_sin - $total_pagar_),GetNumberDecimales(),".",",").'
            </td>
        </tr>
        ';    
    }
    
    $MostrarIva = "";

    if (DesglosarReportIva())
    {
        $MostrarIva = '
            <tr>
                <td align="right">
                <strong> SUBTOTAL: $</strong>
                </td>
                <td align="right">
                '.$subtotal.'
                </td>
            </tr>
            <tr>
                <td align="right">
                <strong> IVA: $</strong>
                </td>
                <td align="right">
                '.$iva_.'
                </td>
            </tr>
        ';
    }
    
    $codigoHTML='
    <style>
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
                <img src="'.ReturnImgLogo().'" alt="Membrete" height="auto" width="350">
            </td>

            <td>
                <center>
                <h2 style="display:inline;">'.$sucursal.'</h2>
                <br>'.$bodysucursal.'
                </center>
            </td>
        </tr>
    </table>
    
    <table width="100%" border="1" style="border-collapse: collapse;">
        <tr>
        <td width="70%">
            <strong>NOMBRE: </strong>'.$cliente.'
            <br><strong>DIRECCION: </strong>'.$cliente_direccion.'
        </td>

        <td style="padding-left: 20px; border-right:1px solid white;border-left:1px solid black;border-bottom:1px solid white;border-top:1px solid white">
            FECHA:'.$fecha_ini.'
            PEDIDO:'.$folio.'
        </td>
        </tr>
    </table>
    <br>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <tr>
        <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">CANT</th> 
        <th bgcolor="'.$ColorBarr.'" style="width:50%; border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">DESCRIPCION</th> 
        <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">UBIC</th>
        <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">P.U</th>
        <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">IMP</th>
        </tr>
        '.$body_products.'
    </table>
    
    <br>
    <table width="100%" border="0" style="border-collapse: collapse;" style="padding: 20px;">
        <tr>
            <td width="70%" style="padding-left: 20px; border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;border-top:1px solid black">
                '.numtoletras($total_pagar_ - $total_abono).'
            </td>

            <td style="padding-left: 20px;" align="right">
                <table border="0">
                    '.$descuento_body.'
                    '.$MostrarIva.'
                    <tr>
                        <td align="right">
                        <strong> TOTAL: $</strong>
                        </td>
                        <td align="right">
                        '.$total_pagar.'
                        </td>
                    </tr>
                    '.$pagos.'
                </table>
            </td>
        </tr>
    </table>';
    
    
    $codigoHTML .= FooterPageReport();
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('letter');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("pedido".$_GET["folio_sale"].".pdf");
?>