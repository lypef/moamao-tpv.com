<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $url = $_POST['url'];
    $url = str_replace("&delete=true","",$url);
    $url = str_replace("?delete=true","",$url);
    $url = str_replace("&deleteno=true","",$url);
    $url = str_replace("?deleteno=true","",$url);
    
    $id = $_POST['id'];
    
    $con = db_conectar(); 
    
    $intento = false;
    
    $relation = Return_ExistRelationsAnnuity($id);
    
    $add_money = Return_ExistRelationsSale($id);

    
    if ($id != 1 && !$relation && !$add_money)
    {
        $intento = true;
        mysqli_query($con,"DELETE FROM clients WHERE id = '$id';");    
    }
    
    if (!mysqli_error($con) && $intento)
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
        echo '<script>location.href = "/clients.php?pagina=1&deleteno=true"</script>';
    }

?>