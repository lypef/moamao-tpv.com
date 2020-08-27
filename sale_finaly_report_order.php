<?php
    require_once 'func/db.php';
    // Dompdf php 7
    require_once 'dompdf_php7.1/autoload.inc.php';
    use Dompdf\Dompdf;

    // Dompdf php 5
    //require_once("dompdf_php5.6/dompdf_config.inc.php");
    
    $folio = $_GET["folio"];
    session_start();

    $con = db_conectar();  
    
    $venta = mysqli_query($con,"SELECT u.nombre, c.nombre, v.descuento, v.fecha, v.cobrado, v.fecha_venta, s.nombre, c.direccion, c.razon_social, v.iva, v.folio_venta_ini, s.nombre, s.direccion, s.telefono FROM folio_venta v, users u, clients c, sucursales s WHERE v.vendedor = u.id and v.client = c.id and v.sucursal = s.id and v.folio = '$folio'");
    
    while($row = mysqli_fetch_array($venta))
    {
        $vendedor = $row[0];
        $cliente = $row[1];
        $descuento = $row[2];
        $fecha_ini = $row[3];
        $cobrado = $row[4];
        $fecha_fini = $row[5];
        $sucursal = $row[6];
        $cliente_direccion = $row[7];
        $r_social = $row[8];
        $tel = $row[8];
        $iva = $row[9];
        $folio_uno = $row[10];
        $bodysucursal = $row[12] . '
        <br><span style="font-size: 14px;">RESPONSABLE: ' . $vendedor . '</span>';
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
    <br>
    <table width="100%" border="1" style="border-collapse: collapse;">
        <tr>
            <td width="70%">
                <strong>NOMBRE: </strong>'.$cliente.'
                <br><strong>DIRECCION: </strong>'.$cliente_direccion.'
            </td>

            <td style="padding-left: 20px; border-right:1px solid white;border-left:1px solid black;border-bottom:1px solid white;border-top:1px solid white">
                FECHA:'.$fecha_ini.'
                ABONO:'.$folio.'
            </td>
        </tr>
    </table>
    <br><hr>
    <p>ABONO: $ '.number_format($cobrado,GetNumberDecimales(),".",",").' MXN | '.numtoletras($cobrado).'</p>
    <p>RECIBIMOS LA CANTIDAD DE : $ '.number_format($cobrado,GetNumberDecimales(),".",",").' MXN ('.numtoletras($cobrado).') EN CALIDAD DE ABONO POR CONCEPTO DE EL PEDIDO CON FOLIO: '.$folio.'</p>';
    
    $codigoHTML .= FooterPageReport();
    
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('letter', '');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("Abono_pedido".$folio_uno.".pdf");
?>