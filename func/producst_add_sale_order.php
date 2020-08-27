<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $unidades = $_POST['unidades'];
    $product = $_POST['product'];
    $folio = $_POST['folio'];
    $url = $_POST['url'];
    $precio = $_POST['costo'];

    
    $url = str_replace("&add_product_sale=true", "", $url);
    $url = str_replace("?add_product_sale=true", "", $url);
    $url = str_replace("&noadd_product_sale=true", "", $url);
    $url = str_replace("?noadd_product_sale=true", "", $url);

    $con = db_conectar();  
        
    mysqli_query($con,"INSERT INTO `product_pedido` (`folio_venta`, `product`, `unidades`, `precio`) VALUES ('$folio', '$product', '$unidades', '$precio');");
    

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'&add_product_sale=true"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'&noadd_product_sale=true"</script>';
    }
?>