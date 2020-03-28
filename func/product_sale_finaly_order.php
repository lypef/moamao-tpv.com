<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $folio = $_POST['folio'];
    $total = 0;
    $descuento = Sale_Descuento($folio);
    

    $Lproducts = mysqli_query($con,"SELECT product, unidades, precio, p_generico FROM `product_pedido` where folio_venta = '$folio';");
    while($row = mysqli_fetch_array($Lproducts))
    {
        if ($row[3] == "")
        {
            $total = $total + ($row[1] * $row[2]);
        }
    }

    $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id FROM product_pedido v WHERE p_generico != '' and folio_venta = '$folio'");
    while($row = mysqli_fetch_array($genericos))
    {
        $total = $total + ($row[0] * $row[2]);
    }

    $total = $total - ($total * ($descuento / 100));
    
    $abonos = mysqli_query($con,"SELECT cobrado FROM folio_venta WHERE folio_venta_ini = '$folio'");

    while($row = mysqli_fetch_array($abonos))
    {
        $t_abonos = $t_abonos + $row[0];
    }
    
    $adeudo = $total - $t_abonos;

    
    if ($adeudo <= 0)
    {
        mysqli_query($con,"UPDATE `folio_venta` SET `open` = '0' WHERE folio = $folio;");
        if (!mysqli_error($con))
        {
            echo '<script>location.href = "/orders.php?folio='.$folio.'&sale_finaly=true"</script>';
        }else
        {
            echo '<script>location.href = "/sale_order.php?folio='.$folio.'&nosale_finaly=true"</script>';
        }
    }else
    {
        echo '<script>location.href = "/sale_order.php?folio='.$folio.'&nopay=true"</script>';
    }
?>