<?php
    include 'db.php';
    db_sessionValidarNO();
    
    session_start();
    $usuario = $_SESSION['users_id'];
    $sql_update = "";

    $txt_user = Return_NombreUser($usuario);
    $txt_suc = "Todos";
    $data = mysqli_query(db_conectar(),"SELECT v.folio, c.nombre, s.nombre, v.fecha_venta, v.cobrado FROM folio_venta v, sucursales s, users u, clients c where v.sucursal = s.id and v.vendedor = u.id and v.client = c.id and v.open = 0 and v.cut = 0 and v.vendedor = $usuario order by v.fecha_venta desc");
    $sql_update = "UPDATE folio_venta SET cut = '1' where vendedor = $usuario;";
    
    
    // Dompdf php 7
    //require_once 'dompdf_php7.1/autoload.inc.php';
    //use Dompdf\Dompdf;

    // Dompdf php 5
    require_once("../dompdf_php5.6/dompdf_config.inc.php");
    
    
    
    $ColorBarr = ColorBarrReport();
    $Showiva = DesglosarReportIva();

    session_start();
    $usd = GetUsd();
    
    $body_products = "";

    $cont = 0; $first = true;

    while($row = mysqli_fetch_array($data))
    {
        $total_sin += ($row[4]);

        if ($Showiva)
        {
            $p_total = number_format($row[4] / 1.160000,GetNumberDecimales(),".",",");
        }else
        {
            $p_total = number_format($row[4],GetNumberDecimales(),".",",");
        }

        if ($cont == 0)
        {
            $body_products .= '
            <table border="0" style="width:100%; border-collapse: collapse;">
                <tr>
                <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">FOLIO</th> 
                <th bgcolor="'.$ColorBarr.'" style="width:25%; border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">CLIENTE</th> 
                <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">SUCURSAL</th>
                <th bgcolor="'.$ColorBarr.'" style="width:22%; border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">FECHA</th>
                <th bgcolor="'.$ColorBarr.'" style="border-right:1px solid '.$ColorBarr.';border-left:1px solid '.$ColorBarr.';border-bottom:1px solid black;border-top:1px solid '.$ColorBarr.'">MONTO</th>
                </tr>
                <tr>
                    <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">'.$row[0].'</td>
                    <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">'.ucwords(strtolower(substr($row[1], 0, 20))).'</td>
                    <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; font-size:10; ">'.ucfirst(strtolower(substr($row[2], 0, 20))).'</td>
                    <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: left;">
                    '.substr(GetFechaText($row[3]), 0, 20).'
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
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">'.$row[0].'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none">'.ucwords(strtolower(substr($row[1], 0, 20))).'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; font-size:10; ">'.ucfirst(strtolower(substr($row[2], 0, 20))).'</td>
                <td style="font-family: Arial, serif; font-size: small; border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top:none; text-align: left;">
                '.substr(GetFechaText($row[3]), 0, 20).'
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
            if ($cont == 28)
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

    if ( ($cont+2) < 38)
    {

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
                <img src="'."../".ReturnImgLogo().'" alt="Membrete" height="auto" width="350">
            </td>

            <td>
                <center>
                <h2 style="display:inline;">'.$_SESSION['empresa_nombre'].'</h2>
                <br>Direccion: '.$_SESSION['empresa_direccion'].'<br>Telefono: '.$_SESSION['empresa_telefono'].'
                </center>
            </td>
        </tr>
    </table>
    
    <table style="height: 5px;" width="100%">
        <tbody>
        <tr>
            <td bgcolor="'.$ColorBarr.'" align="center"><strong>CORTE X</strong></td>
        </tr>
        <tr>
            <td>
            <table width="100%">
                <tbody>
                    <tr>
                        <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><b>FECHA:</b> '.GetFechaText(date("Y-m-d H:i:s")).'</td>
                        <td style="border-right: 1px solid black;border-left:1px solid black;border-bottom: 1px solid black;border-top: 1px solid black" align="center"><b>USUARIOS:</b> '.$txt_user.' | <b>SUC:</b> '.$txt_suc.' </td>
                    </tr>
                </tbody>
            </table>
            </td>
        </tr>
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
    <br>
    ';
    
    $codigoHTML .= FooterPageReport();
    
    
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf= new DOMPDF();
    $dompdf->set_paper('letter');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("corte_z_usuario.pdf");
    
    if (isset($_POST['cut'])) 
    {
        if ($_POST['cut'] == "1")
        {
            mysqli_query(db_conectar(),$sql_update);
        }
    }
?>