<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
    
    $url = $_POST['url'];
    $url = str_replace("&abono=true", "", $url);
    $url = str_replace("&noabono=true", "", $url);
    $url = str_replace("&nopay=true", "", $url);

    $folio_a = $_POST['folio_a'];
    $vendedor = $_SESSION['users_id'];
    $folio = $vendedor . date("YmdHis");
    $t_pago = $_POST['t_pago'];
    $abono = $_POST['abono'];
    $fecha_venta = date("Y-m-d H:i:s");

    $con = db_conectar();  
    $vals = mysqli_query($con,"SELECT client, descuento, fecha, fecha_venta, sucursal, iva FROM `folio_venta` WHERE folio = '$folio_a'");

    while($row = mysqli_fetch_array($vals))
    {
        $cliente = $row[0];
        $descuento = $row[1];
        $fecha = $row[2];
        $f_venta = $row[3];
        $sucursal = $row[4];
        $iva = $row[5];
    }

    mysqli_query($con,"INSERT INTO `folio_venta` (`folio`,`vendedor`, `client`, `descuento`, `fecha`, `open`, `sucursal`, `iva`, `t_pago`, `pedido`, `folio_venta_ini`, `cobrado`, `fecha_venta`) VALUES ('$folio','$vendedor', '$cliente', '$descuento', '$fecha', '0', '$sucursal', '$iva', '$t_pago','1','$folio_a', '$abono','$fecha_venta');");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/sale_order.php?folio='.$folio_a.'&abono=true&pay='.$folio.'"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'&noabono=true"</script>';
    }
?>