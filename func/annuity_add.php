<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
    
    $url = $_POST['url'];
    $url = remove_url_query_args($url,array("okannuity","noannuity"));
    
    $price = $_POST['price'];
    $concepto = $_POST['concepto'];
    $client = $_POST['client'];
    
    if ($price > 0)
    {
        $con = db_conectar();  
        mysqli_query($con,"INSERT INTO `annuities` (`client`,`concepto`, `price`) VALUES ('.$client.','$concepto','$price');");
    
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
            if (!mysqli_error($con))
            {
                sale_annuity();
                echo '<script>location.href = "'.$url.'&okannuity=true"</script>';
            }else
            {
                echo '<script>location.href = "'.$url.'&noannuity=true"</script>';
            }
        }else
        {
            if (!mysqli_error($con))
            {
                sale_annuity();
                echo '<script>location.href = "'.$url.'?okannuity=true"</script>';
            }else
            {
                echo '<script>location.href = "'.$url.'?noannuity=true"</script>';
            }
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
            echo '<script>location.href = "'.$url.'&noannuity=true"</script>';
        }else
        {
            echo '<script>location.href = "'.$url.'?noannuity=true"</script>';
        }
    }
    
    function sale_annuity()
    {
        $con = db_conectar();  
        $price = $_POST['price'];
        $concepto = $_POST['concepto'];
        $client = $_POST['client'];
    
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