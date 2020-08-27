<?php
    include 'db.php';
    $con = db_conectar();  

    $url = $_POST['url'];
    $url = str_replace("&add_product_sale=true", "", $url);
    $url = str_replace("?add_product_sale=true", "", $url);
    $url = str_replace("&noadd_product_sale=true", "", $url);
    $url = str_replace("?noadd_product_sale=true", "", $url);
    $url = str_replace("&nostock=true", "", $url);
    $url = str_replace("?nostock=true", "", $url);
    
    $folio = $_POST['folio'];
    
    $soporte_id = $_POST['soporte_id'];

    $product_soporte = mysqli_query($con,"SELECT * FROM soporte where id = $soporte_id ");
    while($row = mysqli_fetch_array($product_soporte))
    {
        $product_descripcion = $row[1];
        $product_price = $row[2];
    }
        
    mysqli_query($con,"INSERT INTO `product_venta` (`folio_venta`, `unidades`, `precio`,`p_generico`) VALUES ('$folio', 1, '$product_price','$product_descripcion');");

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