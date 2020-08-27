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
    
    $con = db_conectar();  
    $id = $_GET['almacen'];

    if ($_GET["almacen"])
    {
        $sales = mysqli_query($con,"SELECT p.id, p.`no. De parte`,p.nombre, a.nombre, d.nombre, p.precio_costo, p.precio_normal, p.stock, p.loc_almacen FROM productos p, almacen a, departamentos d WHERE p.almacen = a.id and p.departamento = d.id and a.id = '$id' ORDER by p.nombre asc");
    }
    
    if ($_GET["almacen"] == 'full')
    {
        $sales = mysqli_query($con,"SELECT p.id, p.`no. De parte`,p.nombre, a.nombre, d.nombre, p.precio_costo, p.precio_normal, p.stock, p.loc_almacen FROM productos p, almacen a, departamentos d WHERE p.almacen = a.id and p.departamento = d.id ORDER by p.nombre asc");
    }

    $total_inventario = 0;
    
    $body = '';
    while($row = mysqli_fetch_array($sales))
    {
        $body = $body.'
        <tr>
        <td class="item-des"><p><center>'.$row[1].'</center></p></td>
        <td class="item-des"><p><center>'.$row[8].'</center></p></td>
        <td class="item-des"><p>'.$row[2].'</p></td>
        <td class="item-des"><p>'.$row[3].'</p></td>
        <td class="item-des"><p><center>'.$row[7].'</center></p></td>
        <td class="item-des" align="right"><p>'.$row[5].'</p></td>
        <td class="item-des" align="right"><p>'.$row[6].'</p></td>
        <td align="right"><p>'.($row[7] * $row[5]).'</p></td>
        </tr>
        ';
        $total_inventario = $total_inventario + ($row[7] * $row[5]);
        
        // Add hijos
        if ($_GET["almacen"])
        {
            $hijos = mysqli_query($con,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[0]' and a.id = '$id' ");
        }
        
        if ($_GET["almacen"] == 'full')
        {
            $hijos = mysqli_query($con,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[0]'");
        }
        
        while($item = mysqli_fetch_array($hijos))
        {
            $body = $body.'
            <tr>
            <td class="item-des"><center><p>'.$row[1].'</center></p></td>
            <td class="item-des"><p><center>'.$row[8].'</center></p></td>
            <td class="item-des"><p>'.$row[2].'</p></td>
            <td class="item-des"><p>'.$item[2].'</p></td>
            <td class="item-des"><p><center>'.$item[3].'</center></p></td>
            <td class="item-des" align="right"><p>'.$row[5].'</p></td>
            <td class="item-des" align="right"><p>$ '.$row[6].'</p></td>
            <td align="right"><p>'.($item[3] * $row[5]).'</p></td>
            </tr>
            ';
            $total_inventario = $total_inventario + ($item[3] * $row[5]);
        } //Finaliza hijos
    }
    
    $print =  '
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h3><center>'.$_SESSION['empresa_direccion'].'</center></h3>
    <h3><center>MAIL: '.$_SESSION['empresa_correo'].' | TEL: '.$_SESSION['empresa_telefono'].'</center></h3>
    <h4><center>LISTA DE PRODUCTOS EN EXISTENCIA</center></h4>
    <h1><center>TOTAL DE INVENTARIO: $ '.number_format($total_inventario,GetNumberDecimales(),".",",").'</center></h1>
    <table style="width:100%">
        <tr>
        <th class="table-head th-name uppercase">NO. PARTE</th>
        <th class="table-head th-name uppercase">UBICACION</th>
        <th class="table-head th-name uppercase">PRODUCTO</th>
        <th class="table-head th-name uppercase">ALMACEN</th>
        <th class="table-head th-name uppercase">EXISTENCIA</th>
        <th class="table-head th-name uppercase">PRECIO COSTO</th>
        <th class="table-head th-name uppercase">PRECIO VENTA</th>
        <th>VALOR DE INVENTARIO</th>
        </tr>
        '.$body.'
    </table>
    
    <br><br>
    <br>
    ';

    $print = mb_convert_encoding($print, 'HTML-ENTITIES', 'UTF-8');

    echo $print;
?>