<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $unidades = $_POST['unidades'];
    $product = $_POST['product'];
    $folio = $_POST['folio'];
    $url = $_POST['url'];
    $precio = $_POST['costo'];
    $hijo = $_POST['hijo'];

    $url = str_replace("&add_product_sale=true", "", $url);
    $url = str_replace("?add_product_sale=true", "", $url);
    $url = str_replace("&noadd_product_sale=true", "", $url);
    $url = str_replace("?noadd_product_sale=true", "", $url);
    $url = str_replace("&nostock=true", "", $url);
    $url = str_replace("?nostock=true", "", $url);

    $con = db_conectar();  
        if ($hijo > 0)
        {
            if (isset($_POST['cm_ancho'.$product]))
            {
                $price = returnVc2($product);
                $ancho = $_POST['cm_ancho'.$product];
                $alto = $_POST['cm_alto'.$product];
                $total = ($ancho * $alto) * $price;

                mysqli_query($con,"INSERT INTO `product_venta` (`folio_venta`, `product`, `unidades`, `precio`, `product_sub`, `ancho`, `alto`) VALUES ('$folio', '$product', '$unidades', '$total', '$hijo','$ancho','$alto');");
            }else
            {
                mysqli_query($con,"INSERT INTO `product_venta` (`folio_venta`, `product`, `unidades`, `precio`, `product_sub`) VALUES ('$folio', '$product', '$unidades', '$precio', '$hijo');");
            }            
        }else
        {
            if (isset($_POST['cm_ancho'.$product]))
            {
                $price = returnVc2($product);
                $ancho = $_POST['cm_ancho'.$product];
                $alto = $_POST['cm_alto'.$product];
                $total = ($ancho * $alto) * $price;
                mysqli_query($con,"INSERT INTO `product_venta` (`folio_venta`, `product`, `unidades`, `precio`, `ancho`, `alto`) VALUES ('$folio', '$product', '$unidades', '$total','$ancho','$alto');");
            }else
            {
                mysqli_query($con,"INSERT INTO `product_venta` (`folio_venta`, `product`, `unidades`, `precio`) VALUES ('$folio', '$product', '$unidades', '$precio');");
            }
        }
        
      if (!mysqli_error($con))
        {
            $addpregunta = false;

            for($i=0;$i<strlen($url);$i++)
            {
                if ($url[$i] == "?")
                {
                    $addpregunta = true;
                }
            }

            if ($addpregunta)
            {
                echo '<script>location.href = "'.$url.'&add_product_sale=true"</script>';
            }else{
                echo '<script>location.href = "'.$url.'?add_product_sale=true"</script>';
            }
        }else
        {
            $addpregunta = false;

            for($i=0;$i<strlen($url);$i++)
            {
                if ($url[$i] == "?")
                {
                    $addpregunta = true;
                }
            }

            if ($addpregunta)
            {
                echo '<script>location.href = "'.$url.'&noadd_product_sale=true"</script>';
            }else{
                echo '<script>location.href = "'.$url.'?noadd_product_sale=true"</script>';
            }
        }
?>