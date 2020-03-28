<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
    
    $id = $_GET['id'];
    $concepto = $_GET['concepto'];
    $price = $_GET['price'];
    $fecha = date("Y-m-d H:i:s");
    
    $con = db_conectar();  
    mysqli_query($con,"UPDATE `annuities` SET active = 1, date_last = '$fecha' WHERE `annuities`.`id` = '$id';");

    if (!mysqli_error($con))
    {
        sale_annuity();
        echo '<script>location.href = "/annuity.php?okannuity=true"</script>';
    }else
    {
        echo '<script>location.href = "/annuity.php?noannuity=true"</script>';
    }
    
    function sale_annuity()
    {
        $con = db_conectar();  
        
        $id = $_GET['id'];
        $price = $_GET['price'];
        $concepto = $_GET['concepto'];
        $client = ReturnNameAnnuity($id);
    
        $vendedor = $_SESSION['users_id'];
        $fecha = date("Y-m-d H:i:s");
        $folio = $vendedor . date("YmdHis");
        $descuento = 0;
        $sucursal = $_SESSION['sucursal'];
        $iva = 16;
        $t_pago = 'efectivo';
        
        mysqli_multi_query($con,"
        INSERT INTO `folio_venta` (`folio`,`vendedor`, `client`, `descuento`, `fecha`, `open`, `sucursal`, `iva`, `t_pago`) VALUES ('$folio', '$vendedor', '$client', '$descuento', '$fecha', '1', '$sucursal','$iva', '$t_pago');
        INSERT INTO `product_venta` (`folio_venta`, `unidades`, `precio`,`p_generico`) VALUES ('$folio', 1, '$price','$concepto');
        ");
        
        $con = db_conectar();  
        mysqli_query($con,"UPDATE `folio_venta` SET `open` = '0', `cotizacion` = '0', `fecha_venta` = '$fecha', `cobrado` = '$price' WHERE folio = $folio;");
        
        SendMailLog($folio);
    }
?>