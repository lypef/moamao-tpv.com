<?php
    require_once 'func/db.php';
    // Dompdf php 7
    //require_once 'dompdf_php7.1/autoload.inc.php';
    //use Dompdf\Dompdf;

    // Dompdf php 5
    require_once("dompdf_php5.6/dompdf_config.inc.php");
    
    $folio = $_GET["folio_sale"];

    $ColorBarr = ColorBarrReport();
    $Showiva = DesglosarReportIva();

    session_start();
    $usd = GetUsd();
    $con = db_conectar();  

    $venta = mysqli_query($con,"SELECT u.nombre, c.nombre, v.descuento, v.fecha, v.cobrado, v.fecha_venta, s.nombre, s.direccion, s.telefono, v.iva, c.razon_social, c.direccion FROM folio_venta v, users u, clients c, sucursales s WHERE v.vendedor = u.id and v.client = c.id and v.sucursal = s.id and v.folio = '$folio'");
    $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");
    $cont = 0; $first = true;

    if (Ticket())
    {
        /////////////////////////////////
        /////////////////////////////////
        // Se imprimer reporte ticket ///
        /////////////////////////////////
        /////////////////////////////////
        
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
            <br><br><span style="font-size: 14px;"><b>RESPONSABLE</b><br>' . $vendedor . '</span>';
            $r_social = $row[10];
            $cliente_direccion = $row[11];
        }

        if (!empty($r_social))
        {
            $r_social = ' | ' . $r_social;
        }
        
        $products = mysqli_query($con,"SELECT p.nombre, p.`no. De parte`, v.unidades, v.precio , a.nombre, p.loc_almacen, v.product_sub FROM product_venta v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = '$folio'");
        $body_products = '';
        while($row = mysqli_fetch_array($products))
        {
            if (!$row[6])
            {
                $ubicacion = substr($row[4],0,3) . ', ' . $row[5];
            }
            else
            {
                $ubicacion = Almacen_ubicacion_p_sub($row[6]);
            }

            $total_sin = $total_sin + ($row[2] * $row[3]);

            if ($Showiva)
            {
                $body_products .= 
                '
                    ( '.$row[2].' ) '.$row[0] . ' $ ' . number_format(($row[2] * $row[3]) / 1.160000,GetNumberDecimales(),".",",").'<br>
                ';
            }else
            {
                $body_products .= 
                '
                    ( '.$row[2].' ) '.$row[0] . ' $ ' . number_format($row[2] * $row[3],GetNumberDecimales(),".",",").'<br>
                ';
            }

        }
        
        while($row = mysqli_fetch_array($genericos))
        {
            $total_sin = $total_sin + ($row[0] * $row[2]);

            if ($Showiva)
            {
                $body_products .= 
                '
                    ( '.$row[0].' ) '.'P. GENERICO'. ' $ ' . number_format(($row[0] * $row[2]) / 1.160000,GetNumberDecimales(),".",",").'<br>
                ';
            }else
            {
                $body_products .= 
                '
                    ( '.$row[0].' ) '.'P. GENERICO'. ' $ ' . number_format($row[0] * $row[2],GetNumberDecimales(),".",",").'<br>
                ';
            }

            
        }

        $ivac = '.'.$iva;

        $total_pagar = $total_sin - ($total_sin * ($descuento / 100));
        $total_pagar_ = $total_pagar;
        
        $subtotal = $total_pagar / 1.160000;
        $subtotal_ = $subtotal;

        $iva_ = $total_pagar - $subtotal;

        $subtotal = number_format($subtotal,2,".",",");
        $total_pagar = number_format($total_pagar,2,".",",");
        $iva_ = number_format($iva_,2,".",",");
        
        $descuento_body = "";
        
        if ($descuento > 0)
        {
            $descuento_body = '
            <br>
            <strong>TOTAL SN DESC:</strong> $ '.number_format($total_sin,2,".",",").'
            <br>
            <strong>DESC '.$descuento .' %:</strong> - $ '.number_format(($total_sin - $total_pagar_),2,".",",").'
            ';
        }
        
        $MostrarDesgloseIva = "";

        if ($Showiva)
        {
            $MostrarDesgloseIva = '
                <br><strong> SUBTOTAL:</strong> $ '.$subtotal.'
                <br><strong> IVA:</strong> $ '.$iva_.'
            ';
        }

        $codigoHTML='
        <style>
        
        
        @page {
            size: 5cm 40cm;
            font-size: 12px;
            margin-top: 0.1em;
            margin-left: 0.1em;
            margin-right: 0.1em;
            margin-bottom: 0.1em;
        }
        </style>
        <body>
        <center>
        <img src="'.ReturnImgLogo().'" alt="Membrete" height="auto" width="210">
        <br>
        <h2 style="display:inline;">'.$sucursal.'</h2>
        <br>'.$bodysucursal.'
        <br><br>
        <strong>CLIENTE: </strong><br>'.strtoupper($cliente).'
        <br><br><b>FECHA:</b> <br>'.GetFechaText($fecha_ini).'
        <br><b><br>FOLIO REMISION:</b><br> '.$folio.'
        <br><br></center>
        '.$body_products.'
        '.$descuento_body.'
        '.$MostrarDesgloseIva.'
        <br><br><center><strong> TOTAL PAGAR<br></strong> $ '.$total_pagar.' MXN<br></center>
        ';
        
        $codigoHTML .= FooterPageReport();
        
        $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
        $dompdf=new DOMPDF();
        $dompdf->set_paper('letter');
        $dompdf->load_html($codigoHTML);
        ini_set("memory_limit","128M");
        $dompdf->render();
        $dompdf->stream("remision".$_GET["folio_sale"].".pdf");
    }else
    {
        /////////////////////////////////
        /////////////////////////////////
        // Se imprimer reporte normal ///
        /////////////////////////////////
        /////////////////////////////////
        
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

        if (!empty($r_social))
        {
            $r_social = ' | ' . $r_social;
        }
        
        $products = mysqli_query($con,"SELECT p.nombre, p.`no. De parte`, v.unidades, v.precio , a.nombre, p.loc_almacen, v.product_sub FROM product_venta v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = '$folio'");
        $body_products = '';

        while($row = mysqli_fetch_array($products))
        {
            if (!$row[6])
            {
                $ubicacion = substr($row[4],0,3) . ', ' . $row[5];
            }
            else
            {
                $ubicacion = Almacen_ubicacion_p_sub($row[6]);
            }

            $total_sin = $total_sin + ($row[2] * $row[3]);

            if ($Showiva)
            {
                $p_unitario = number_format($row[3] / 1.160000,GetNumberDecimales(),".",",");
                $p_total = number_format(($row[2] * $row[3]) / 1.160000,GetNumberDecimales(),".",",");
            }else
            {
                $p_unitario = number_format($row[3],GetNumberDecimales(),".",",");
                $p_total = number_format($row[2] * $row[3],GetNumberDecimales(),".",",");
            }

            

            if ($cont == 0)
        {
            $body_products .= '
            <table border="1" style="width:100%; border-collapse: collapse;">
            <tr>
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">CANT</th> 
                <th bgcolor="'.$ColorBarr .'" style="width:50%; border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">DESCRIPCION</th> 
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">UBIC</th>
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">P.U MXN</th>
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">IMP MXN</th>
            </tr>
                <tr>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none"><center>'.$row[2].'</center></td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">'.ucwords(strtolower(substr($asterisk.'('.$row[1].') '.$row[0], 0, 56))).'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; font-size:10; ">'.ucwords(strtolower(substr($ubicacion, 0, 15))).'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                    <table border="0" width="100%">
                        <tr>
                            <td align="left"> $</td>
                            <td align="right">
                            '.$p_unitario.'
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                    <table border="0" width="100%">
                        <tr>
                            <td align="left"> $</td>
                            <td align="right">
                            '.$p_total.'
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            ';
        }

        if ($cont > 0)
        {
            $body_products .= '
            <tr>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none"><center>'.$row[2].'</center></td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">'.ucwords(strtolower(substr($asterisk.'('.$row[1].') '.$row[0], 0, 56))).'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; font-size:10; ">'.ucwords(strtolower(substr($ubicacion, 0, 15))).'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                    <table border="0" width="100%">
                        <tr>
                            <td align="left"> $</td>
                            <td align="right">
                            '.$p_unitario.'
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                    <table border="0" width="100%">
                        <tr>
                            <td align="left"> $</td>
                            <td align="right">
                            '.$p_total.'
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            ';
        }

        if ($first)
        {
            if ($cont == 26)
            {
                $cont = -1;
                $first = false;
                $body_products .= 
                '
                    </table>
                    <div style="page-break-after:always;"></div>
                ';
            }
        }else
        {
            if ($cont == 38)
            {
                $cont = -1;
                $body_products .= 
                '
                    </table>
                    <div style="page-break-after:always;"></div>
                ';
            }
        }

        $cont ++;
        }
        

        while($row = mysqli_fetch_array($genericos))
        {
            $total_sin = $total_sin + ($row[0] * $row[2]);

            if ($Showiva)
            {
                $p_unitario = number_format($row[2] / 1.160000,GetNumberDecimales(),".",",");
                $p_total = number_format(($row[0] * $row[2]) / 1.160000,GetNumberDecimales(),".",",");
            }else
            {
                $p_unitario = number_format($row[2],GetNumberDecimales(),".",",");
                $p_total = number_format($row[0] * $row[2],GetNumberDecimales(),".",",");
            }

            if ($cont == 0)
        {
            $body_products .= '
            <table border="1" style="width:100%; border-collapse: collapse;">
            <tr>
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">CANT</th> 
                <th bgcolor="'.$ColorBarr .'" style="width:50%; border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">DESCRIPCION</th> 
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">UBIC</th>
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">P.U MXN</th>
                <th bgcolor="'.$ColorBarr .'" style="border-right:1px solid '.$ColorBarr .';border-left:1px solid '.$ColorBarr .';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr .'">IMP MXN</th>
            </tr>
            <tr>
            <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none"><center>'.$row[0].'</center></td>
            <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">*** (NA) '.$row[1].'</td>
            <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none"><center>NA</center></td>
            <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                <table border="0" width="100%">
                    <tr>
                        <td align="left"> $</td>
                        <td align="right">
                        '.$p_unitario.'
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                <table border="0" width="100%">
                    <tr>
                        <td align="left"> $</td>
                        <td align="right">
                        '.$p_total.'
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
            ';
        }

        if ($cont > 0)
        {
            $body_products .= '
            <tr>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none"><center>'.$row[0].'</center></td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">*** (NA) '.$row[1].'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none"><center>NA</center></td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                    <table border="0" width="100%">
                        <tr>
                            <td align="left"> $</td>
                            <td align="right">
                            '.$p_unitario.'
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: right;">
                    <table border="0" width="100%">
                        <tr>
                            <td align="left"> $</td>
                            <td align="right">
                            '.$p_total.'
                            </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            ';
        }

        if ($first)
        {
            if ($cont == 26)
            {
                $cont = -1;
                $first = false;
                $body_products .= 
                '
                    </table>
                    <div style="page-break-after:always;"></div>
                ';
            }
        }else
        {
            if ($cont == 38)
            {
                $cont = -1;
                $body_products .= 
                '
                    </table>
                    <div style="page-break-after:always;"></div>
                ';
            }
        }

        $cont ++;
        }

        $ivac = '.'.$iva;

        $total_pagar = $total_sin - ($total_sin * ($descuento / 100));
        $total_pagar_ = $total_pagar;
        
        $subtotal = $total_pagar / 1.160000;
        $subtotal_ = $subtotal;

        $iva_ = $total_pagar - $subtotal;

        $subtotal = number_format($subtotal,GetNumberDecimales(),".",",");
        $total_pagar = number_format($total_pagar,GetNumberDecimales(),".",",");
        $iva_ = number_format($iva_,GetNumberDecimales(),".",",");
        
        $descuento_body = "";
        
        if ($descuento > 0)
        {
            $descuento_body = '
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong>DESC '.$descuento .' %:</strong> - $ '.number_format(($total_sin - $total_pagar_),GetNumberDecimales(),".",",").'</td>
            ';
        }
        
        $ShowDesgloseIva = "";

        if ($Showiva)
        {
            $ShowDesgloseIva = '
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> SUBTOTAL:</strong> $ '.$subtotal.'</td>
            <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> IVA:</strong> $ '.$iva_.'</td>
            ';
        }

        $codigoHTML='
        <style>
        @page {
            margin-top: 0.7em;
            margin-left: 0.6em;
            margin-right: 0.6em;
            margin-bottom: 0.1em;
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
        
        <table style="height: 5px;" width="100%">
            <tbody>
            
            <tr>
                <td bgcolor="'.$ColorBarr.'" align="center"><strong>CLIENTE: </strong>'.strtoupper($cliente . $r_social).'</td>
            </tr>

            <tr>
                <td>
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><b>FECHA:</b> '.GetFechaText($fecha_ini).'</td>
                                <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><b>FOLIO REMISION:</b> '.$folio.'</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

            </tbody>
        </table>        
        
        <br>
        
        <table style="height: 5px;" width="100%">
            <tbody>
                
                <tr>
                    <td bgcolor="'.$ColorBarr.'" align="center"><strong>'.str_replace("M.N.","MXN",numtoletras($total_pagar_)).'</strong></td>
                </tr>
                
                <tr>
                    <td>
                        <table width="100%">
                            <tbody>
                                <tr>
                                    '.$descuento_body.'
                                    '.$ShowDesgloseIva.'
                                    <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><strong> TOTAL:</strong> $ '.$total_pagar.' MXN</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        '.$body_products.'
        ';
        
        $codigoHTML .= FooterPageReport();
        

        $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
        $dompdf=new DOMPDF();
        $dompdf->set_paper('letter');
        $dompdf->load_html($codigoHTML);
        ini_set("memory_limit","128M");
        $dompdf->render();
        $dompdf->stream("remision".$_GET["folio_sale"].".pdf");
        // Finaliza reporte normal
    }
?>