<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $unidades = $_POST['unidades'];
    
    $folio = $_POST['folio'];
    $url = $_POST['url'];
    $precio = $_POST['costo'];
    $hijo = $_POST['hijo'];
    $p_generic = $_POST['p_generic'];
    
    
    $url = str_replace("&add_product_sale=true", "", $url);
    $url = str_replace("?add_product_sale=true", "", $url);
    $url = str_replace("&noadd_product_sale=true", "", $url);
    $url = str_replace("?noadd_product_sale=true", "", $url);
    $url = str_replace("&nostock=true", "", $url);
    $url = str_replace("?nostock=true", "", $url);

    $con = db_conectar();  
    
        mysqli_query($con,"INSERT INTO `product_venta` (`folio_venta`, `unidades`, `precio`,`p_generico`) VALUES ('$folio', '$unidades', '$precio','$p_generic');");
        
    
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