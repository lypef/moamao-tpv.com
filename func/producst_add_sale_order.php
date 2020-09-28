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
      
    if (isset($_POST['costo'.$product]))
    {
        // Producto linea
        $ancho = $_POST['cm_ancho'.$product];
        $alto = $_POST['cm_alto'.$product];
        $total = $_POST['costo'.$product];
        
        mysqli_query($con,"INSERT INTO `product_pedido` (`folio_venta`, `product`, `unidades`, `precio`, `ancho`, `alto`) VALUES ('$folio', '$product', '$unidades', '$total', '$ancho', '$alto');");
    }
    else if (isset($_POST['cm_ancho'.$product]))
    {
        // Producto por area
        $price = returnVc2($product);
        $ancho = $_POST['cm_ancho'.$product];
        $alto = $_POST['cm_alto'.$product];
        $total = ($ancho * $alto) * $price;

        mysqli_query($con,"INSERT INTO `product_pedido` (`folio_venta`, `product`, `unidades`, `precio`, `ancho`, `alto`) VALUES ('$folio', '$product', '$unidades', '$total', '$ancho', '$alto');");
    }else
    {
        // Producto normal
        mysqli_query($con,"INSERT INTO `product_pedido` (`folio_venta`, `product`, `unidades`, `precio`) VALUES ('$folio', '$product', '$unidades', '$precio');");
    }  

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'&add_product_sale=true"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'&noadd_product_sale=true"</script>';
    }
?>