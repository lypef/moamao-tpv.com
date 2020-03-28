<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $folio = $_POST['folio'];
    $url = $_POST['url'];
    
    mysqli_query($con,"UPDATE `folio_venta` SET `pedido` = 0, `cotizacion` = 0 WHERE folio = $folio;");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }
?>