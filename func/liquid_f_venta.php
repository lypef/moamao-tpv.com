<?php
    include 'db.php';
    
    $folio = $_POST['folio'];
    $url = $_POST['url'];

    $con = db_conectar();  
    mysqli_query($con,"UPDATE `folio_venta` SET `credit_pay` = '1' WHERE `folio_venta`.`folio` =  '$folio';");

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
            echo '<script>location.href = "'.$url.'&sale_liquid=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?sale_liquid=true"</script>';
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
            echo '<script>location.href = "'.$url.'&sale_noliquid=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?sale_noliquid=true"</script>';
        }
    }
?>