<?php
    include 'db.php';
    
    
    $url = $_POST['url'];
    $id = $_POST['id'];
    
    $url = str_replace("&delete=true", "", $url);
    $url = str_replace("?delete=true", "", $url);
    $url = str_replace("&nodelete=true", "", $url);
    $url = str_replace("?nodelete=true", "", $url);

    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM product_venta WHERE id = '$id';");

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
            echo '<script>location.href = "'.$url.'&delete=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?delete=true"</script>';
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
            echo '<script>location.href = "'.$url.'&nodelete=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?nodelete=true"</script>';
        }
    }

?>