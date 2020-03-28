<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $id = $_POST['id'];
    $unidades = $_POST['unidades'];
    $url = $_POST['url'];
    
    $url = str_replace("&update=true", "", $url);
    $url = str_replace("?update=true", "", $url);
    $url = str_replace("&noupdate=true", "", $url);
    $url = str_replace("?noupdate=true", "", $url);

    $url = str_replace("&delete=true", "", $url);
    $url = str_replace("?delete=true", "", $url);
    $url = str_replace("&nodelete=true", "", $url);
    $url = str_replace("?nodelete=true", "", $url);
    $url = str_replace("&nostock=true", "", $url);
    $url = str_replace("?nostock=true", "", $url);
    
    mysqli_query($con,"UPDATE `product_pedido` SET `unidades` = '$unidades' WHERE id = $id;");

        if (!mysqli_error($con))
        {
            for($i=0;$i<strlen($url);$i++)
            {
                if ($url[$i] == "?")
                {
                    $addpregunta = true;
                }
            }
            if ($addpregunta)
            {
                echo '<script>location.href = "'.$url.'&update=true"</script>';
            }else{
                echo '<script>location.href = "'.$url.'?update=true"</script>';
            }
        }else
        {
            for($i=0;$i<strlen($url);$i++)
            {
                if ($url[$i] == "?")
                {
                    $addpregunta = true;
                }
            }
            if ($addpregunta)
            {
                echo '<script>location.href = "'.$url.'&noupdate=true"</script>';
            }else{
                echo '<script>location.href = "'.$url.'?noupdate=true"</script>';
            }
        }
?>