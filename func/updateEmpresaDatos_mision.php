<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $url = $_POST['url'];
    $mision = $_POST['mision_new'];
    
    $con = db_conectar();  
    if ( 
        !empty(trim($mision))
       )
    {
        mysqli_query($con,"UPDATE `empresa` SET `mision` = '$mision' WHERE id = 1;");
    }
    
    if (!mysqli_error($con))
    {
        db_sessionDestroy_login();
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
            echo '<script>location.href = "'.$url.'&error_update_empresa=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?error_update_empresa=true"</script>';
        }
    }

?>