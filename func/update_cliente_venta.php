<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $folio = $_POST['folio'];
    $cliente = $_POST['cliente'];
    $cotizacion = $_POST['cotizacion'];
    $pedido = $_POST['pedido'];
    $vtd = $_POST['vtd'];
    
    mysqli_query($con,"UPDATE `folio_venta` SET `client` = '$cliente' WHERE folio = $folio;");

    if (!mysqli_error($con))
    {
        if ($cotizacion)
        {
            echo '<script>location.href = "/sale_cot.php?folio='.$folio.'"</script>';
        }
        elseif ($pedido)
        {
            echo '<script>location.href = "/sale_order.php?folio='.$folio.'"</script>';
        }
        elseif ($vtd)
        {
            echo '<script>location.href = "/sale.php?folio='.$folio.'&pagina=1"</script>';
        }
    }else
    {
        if ($cotizacion)
        {
            echo '<script>location.href = "/cotizaciones.php"</script>';
        }
        elseif ($pedido)
        {
            echo '<script>location.href = "/orders.php"</script>';
        }
        elseif ($vtd)
        {
            echo '<script>location.href = "/sale.php?folio='.$folio.'&pagina=1"</script>';
        }
    }
?>