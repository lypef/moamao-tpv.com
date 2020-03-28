<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    $url = $_POST['url'];
    
    $concepto = $_POST['concepto'];
    $price = $_POST['price'];
    
    
    $con = db_conectar();  
    mysqli_query($con,"UPDATE `annuities` SET `concepto` = '$concepto', `price` = '$price' WHERE `annuities`.`id` = '$id';");

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
        echo '<script>location.href = "/annuity.php?noupdate=true"</script>';
    }

?>