<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $url = 'http://' .$_POST['url_web'] . $_POST['url'];
    $url = remove_url_query_args($url,array("credit_add_add","credit_add_noadd"));
    
    $cliente = $_POST['select_client'];
    $sucursal = $_POST['select_sucursal'];
    $factura = $_POST['factura'];
    $adeudo = $_POST['adeudo'];
    $abono = $_POST['abono'];
    $dias = $_POST['dias'];
    
    
    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `credits` (`client`, `factura`, `adeudo`, `abono`, `dias_credit`, `sucursal`) VALUES ('$cliente', '$factura', '$adeudo', '$abono', '$dias', '$sucursal');");

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
            echo '<script>location.href = "'.$url.'&credit_add_add=true"</script>';
        }else
        {
            echo '<script>location.href = "'.$url.'&credit_add_noadd=true"</script>';
        }
    }else
    {
        if (!mysqli_error($con))
        {
            echo '<script>location.href = "'.$url.'?credit_add_add=true"</script>';
        }else
        {
            echo '<script>location.href = "'.$url.'?credit_add_noadd=true"</script>';
        }
    }
?>