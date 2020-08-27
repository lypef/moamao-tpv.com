<?php
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
        <td><p>'.$row[1].'</p></td>
        <td><p><center>'.$row[8].'</center></p></td>
        <td><p>'.$row[2].'</p></td>
        <td><p>'.$row[3].'</p></td>
        <td align="center"><p>'.$row[7].'</p></td>
        <td align="right"><p>$ '.number_format($row[5],GetNumberDecimales(),".",",").'</p></td>
        <td align="right"><p>$ '.number_format($row[6],GetNumberDecimales(),".",",").'</p></td>
        <td align="right"><p>$ '.number_format($row[7] * $row[5],GetNumberDecimales(),".",",").'</p></td>
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
            <td><p>'.$row[1].'</p></td>
            <td><p><center>'.$row[8].'</center></p></td>
            <td><p>'.$row[2].'</p></td>
            <td><p>'.$item[2].'</p></td>
            <td align="center"><p>'.$item[3].'</p></td>
            <td align="right"><p>$ '.number_format($row[5],GetNumberDecimales(),".",",").'</p></td>
            <td align="right"><p>$ '.number_format($row[6],GetNumberDecimales(),".",",").'</p></td>
            <td align="right"><p>$ '.number_format($item[3] * $row[5],GetNumberDecimales(),".",",").'</p></td>
            </tr>
            ';
            $total_inventario = $total_inventario + ($item[3] * $row[5]);
        } //Finaliza hijos
    }
    
    $codigoHTML='
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h3><center>'.$_SESSION['empresa_direccion'].'</center></h3>
    <h3><center>MAIL: '.$_SESSION['empresa_correo'].' | TEL: '.$_SESSION['empresa_telefono'].'</center></h3>
    <h4><center>LISTA DE PRODUCTOS EN EXISTENCIA</center></h4>
    <h1><center>TOTAL DE INVENTARIO $ '.number_format($total_inventario,GetNumberDecimales(),".",",").'</center></h1>
    <hr>
    <br><br>
    <table style="width:100%">
        <tr>
        <th>NO. PARTE</th>
        <th>UBICACION</th>
        <th>PRODUCTO</th>
        <th>ALMACEN</th>
        <th>EXISTENCIA</th>
        <th>PRECIO COSTO</th>
        <th>PRECIO VENTA</th>
        <th>VALOR DE INVENTARIO</th>
        </tr>
        '.$body.'
    </table>
    <br><br>
    <br>
    ';
    
    echo $codigoHTML;
    /*
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('letter', 'landscape');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("reporte_productos.pdf");*/
?>