<?php
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=reporte_adicionales.xls");
    
    require_once 'func/db.php';
    // Dompdf php 7
    require_once 'dompdf_php7.1/autoload.inc.php';
    use Dompdf\Dompdf;

    // Dompdf php 5
    //require_once("dompdf/dompdf_config.inc.php");

    session_start();
    
    $inicio = $_GET["inicio"] . ' 00:00:00';
    $finaliza = $_GET["finaliza"] . ' 23:59:59';
    $vendedor = $_GET["usuario"];
    $sucursal = $_GET["sucursal"];
    $folio = $_GET["folio"];
    $efectivo = 0;
    $transferencia = 0;
    $cheque = 0;
    $tarjeta = 0;
    $total = 0;
    $print = "";
    $deposito = 0;

    $con = db_conectar();  
    if ($folio != "" && $vendedor == 0 && $sucursal == 0)
    {
        $sales = mysqli_query($con,"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.folio = '$folio'");
    }
    elseif ($folio == "" && $vendedor > 0 && $sucursal == 0)
    {
        $sales = mysqli_query($con,"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza' and f.vendedor = '$vendedor'");
    }
    elseif ($folio == "" && $vendedor == 0 && $sucursal > 0)
    {
        $sales = mysqli_query($con,"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza' and f.sucursal = '$sucursal'");
    }
    elseif ($folio == "" && $vendedor > 0 && $sucursal > 0)
    {
        $sales = mysqli_query($con,"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza' and f.sucursal = '$sucursal' and f.vendedor = '$vendedor' ");
    }
    else
    {
        $sales = mysqli_query($con,"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza'");
    }

    $body = '';
    while($row = mysqli_fetch_array($sales))
    {
        if ($row[5] > 0)
        {
            if ($row[8] == "efectivo")
            {
                $efectivo = $efectivo + $row[5];
            }
            elseif ($row[8] == "transferencia")
            {
                $transferencia = $transferencia + $row[5];
            }
            elseif ($row[8] == "deposito")
            {
                $deposito = $deposito + $row[5];
            }
            elseif ($row[8] == "tarjeta")
            {
                $tarjeta = $tarjeta + $row[5];
            }
            
            $body = $body.'
            <tr>
            <td class="item-des">'.$row[0].'</td>
            <td class="item-des"><p>'.$row[1].'</p></td>
            <td class="item-des"><p>'.$row[2].'</p></td>
            <td class="item-des"><p>'.$row[7].'</p></td>
            <td class="item-des"><p>'.$row[6].'</p></td>
            <td class="item-des"><center><p>'.$row[3].' %</p></center></td>
            <td class="item-des"><center><p>$ '.$row[5].' MXN</p></center></td>
            <td class="item-des uppercase"><center><p>'.strtoupper($row[8]).'</p></center></td>
            </tr>
            ';
            $total = $total + $row[5];
        }
    }
    
    $print .= '
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h4><center>LISTADO DE VENTAS : DESDE:'.$inicio.' | HASTA:'.$finaliza.'</center></h4>
    <table style="width:100%">
        <tr>
        <th class="table-head th-name uppercase">FOLIO</th>
        <th class="table-head th-name uppercase">VENDEDOR</th>
        <th class="table-head th-name uppercase">CLIENTE</th>
        <th class="table-head th-name uppercase">SUCURSAL</th>
        <th class="table-head th-name uppercase">F.VENTA</th>
        <th class="table-head th-name uppercase">DESCUENTO</th>
        <th class="table-head th-name uppercase">COBRADO</th>
        <th class="table-head th-name uppercase">M. PAGO</th>
        </tr>
        '.$body.'
    </table>
    
    <br><br>
    <br>
    <div align="right">';
    
    if ($efectivo > 0)
		{
			$print .= '
			<h5>Efectivo: $ '.number_format($efectivo,2,".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$print .=  '
			<h5>Tranferencia: $ '.number_format($transferencia,2,".",",").' MXN</h5>
			';
        }
        
        if ($tarjeta > 0)
		{
			$print .=  '
			<h5>Tarjeta: $ '.number_format($tarjeta,2,".",",").' MXN</h5>
			';
		}

		if ($deposito > 0)
		{
			$print .=  '
			<h5>Depositos: $ '.number_format($deposito,2,".",",").' MXN</h5>
			';
		}
    
        $print .= '<h3>TOTAL RECAUDADO: $ '.number_format($total,2,".",",").' MXN</h3>
    </div>
    <br>
    ';
    
    $print = mb_convert_encoding($print, 'HTML-ENTITIES', 'UTF-8');

    echo $print;
?>