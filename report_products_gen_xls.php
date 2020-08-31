s<?php
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=reporte_adicionales.xls");
    
    require_once 'func/db.php';
    // Dompdf php 7
    require_once 'dompdf_php7.1/autoload.inc.php';
    use Dompdf\Dompdf;

    // Dompdf php 5
    //require_once("dompdf_php5.6/dompdf_config.inc.php");

    session_start();
    
    $inicio = $_GET["inicio"] . ' 00:00:00';
    $finaliza = $_GET["finaliza"] . ' 23:59:59';
    $total = 0;

    $con = db_conectar();  
    $tmp = db_conectar();

    $data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza'");

    $body = '';
    while($row = mysqli_fetch_array($data))
    {
        if ($product > 0)
        {
          $datatmp = mysqli_query($tmp,"SELECT p.nombre, pv.precio, pv.unidades, p.id FROM `product_venta` pv, productos p WHERE pv.product = p.id and pv.folio_venta = '$row[0]' and p.id = $product ");
        }
        else
        {
          $datatmp = mysqli_query($tmp,"SELECT p.nombre, pv.precio, pv.unidades, p.id FROM `product_venta` pv, productos p WHERE pv.product = p.id and pv.folio_venta = '$row[0]'");
        }
        while($row0 = mysqli_fetch_array($datatmp))
        {
        $t_ud = $t_ud + $row0[2];
        
        if (!$row[10])
        {
          if ($row[8] == "efectivo")
          {
            $efectivo = $efectivo + ($row0[2] * $row0[1]);
          }
          elseif ($row[8] == "transferencia")
          {
            $transferencia = $transferencia + ($row0[2] * $row0[1]);
          }
          elseif ($row[8] == "tarjeta")
          {
            $cheque = $cheque + ($row0[2] * $row0[1]);
          }
          
          $body = $body.'
          <tr>
          <td class="item-des"><p>'.$row[0].'</p></td>
          <td class="item-des"><p>'.$row[1].'</p></td>
          <td class="item-des"><p>'.$row[2].'</p></td>
          <td class="item-des"><p>'.$row[6].'</p></td>
          <td class="item-des"><center>'.$row0[2].'</center></td>
          <td class="item-des"><center><p>'.$row0[0].'</p></center></td>
          <td class="item-des"><center><p>$ '.$row0[2] * $row0[1].' MXN</p></center></td>
          </tr>
          ';
          $total = $total + ($row0[2] * $row0[1]);
        }
      }
    }
    
    $print =  '
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h4><center>REPORTE DE VENTAS : DESDE:'.$inicio.' | HASTA:'.$finaliza.'</center></h4>
    <table style="width:100%">
        <tr>
        <th class="table-head th-name uppercase">FOLIO</th>
        <th class="table-head th-name uppercase">VENDEDOR</th>
        <th class="table-head th-name uppercase">CLIENTE</th>
        <th class="table-head th-name uppercase">F.VENTA</th>
        <th class="table-head th-name uppercase">UNIDADES</th>
        <th class="table-head th-name uppercase"><center>PRODUCTO</center></th>
        <th class="table-head th-name uppercase">COBRADO</th>
        </tr>
        '.$body.'
    </table>
    <br>';    
    $print .=  '<h3>TOTAL RECAUDADO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h3>
    <br>
    ';

    $print = mb_convert_encoding($print, 'HTML-ENTITIES', 'UTF-8');

    echo $print;
?>