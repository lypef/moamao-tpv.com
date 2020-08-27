<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $folio = $_POST['folio'];
    
    $fecha = date("Y-m-d H:i:s");
    $descuento = Sale_Descuento($folio);
    $total = 0;
    
    $Lproducts = mysqli_query($con,"SELECT product, unidades, precio, product_sub, p_generico FROM `product_venta` where folio_venta = '$folio';");
    while($row = mysqli_fetch_array($Lproducts))
    {
        if ($row[4] == "")
        {
            $total = $total + ($row[1] * $row[2]);
            if ($row[3])
            {
                DescontarProductosStock_hijo($row[3], $row[1]);
            }else
            {
                DescontarProductosStock($row[0], $row[1]);
            }
        }
    }

    $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");
    while($row = mysqli_fetch_array($genericos))
    {
        $total = $total + ($row[0] * $row[2]);
    }
    $total = $total - ($total * ($descuento / 100));
    
     
    mysqli_query($con,"UPDATE `folio_venta` SET `open` = '0', `cotizacion` = '0', `fecha_venta` = '$fecha', `cobrado` = '$total' WHERE folio = $folio;");

    if (!mysqli_error($con))
    {
        SendMailLog($folio, true);
        mysqli_query($con,"UPDATE credits SET abono = adeudo , pay = 1 where factura = '$folio' ");
        echo '<script>location.href = "/products.php?pagina=1&sale_ok=true&folio_sale='.$folio.'"</script>';
    }else
    {
        echo '<script>location.href = "/products.php?pagina=1&nosale_ok=true"</script>';
    }
?>