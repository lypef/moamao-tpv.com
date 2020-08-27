<?php
    include 'db.php';
    
    $con = db_conectar();  
    
    $folio_venta = $_POST['folio'];

    $data = mysqli_query($con,"SELECT c.id, v.vendedor, v.sucursal FROM folio_venta v, clients c WHERE v.client = c.id and v.open = 0 and v.folio = $folio_venta");
    
    if($row = mysqli_fetch_array($data))
    {
        $client = $row[0];
        $vendedor = $row[1];
        $fecha = date("Y-m-d H:i:s");
        $folio = $vendedor . date("YmdHis");
        $descuento = 0;
        $sucursal = $row[2];
        $iva = 16;
        $t_pago = 'oxxo';
        
        mysqli_query($con,"INSERT INTO `folio_venta` (`folio`,`vendedor`, `client`, `descuento`, `fecha`, `open`, `sucursal`, `iva`, `t_pago`, `pedido`,`folio_venta_ini`,`cobrado`,`cotizacion`) VALUES ('$folio', '$vendedor', '$client', '$descuento', '$fecha', '1', '$sucursal','$iva', '$t_pago', '0','$folio','0','1');");
        $r = 1;

        echo '<script>location.href = "/soporte_asistencia_tecnico.php?pagina=1&folio='.$folio.'"</script>';
    }else
    {
        echo 'El folio no existe, consulte nuevamente.';
    }
?>