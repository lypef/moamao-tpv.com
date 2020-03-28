<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
          
    $vendedor = $_SESSION['users_id'];
    $client = 1;
    $fecha = date("Y-m-d H:i:s");
    $folio = $vendedor . date("YmdHis");
    $descuento = 0;
    $sucursal = $_SESSION['sucursal'];
    $iva = 16;
    $t_pago = 'transferencia';
    
    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `folio_venta` (`folio`,`vendedor`, `client`, `descuento`, `fecha`, `open`, `sucursal`, `iva`, `t_pago`, `pedido`,`folio_venta_ini`,`cobrado`,`cotizacion`) VALUES ('$folio', '$vendedor', '$client', '$descuento', '$fecha', '1', '$sucursal','$iva', '$t_pago', '0','$folio','0','1');");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/sale_cot.php?folio='.$folio.'"</script>';
    }else
    {
        echo '<script>location.href = "/create_cotizacion.php?pagina=1"</script>';
    }
?>