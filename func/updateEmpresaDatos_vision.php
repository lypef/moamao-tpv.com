<?php
    include 'db.php';
    db_sessionValidarNO();
    
    if ($_SESSION['token'] == GetToken())
    {
        $url = $_POST['url'];
        $vision290513 = $_POST['vision_new'];
        
        $con = db_conectar();  
        if ( !empty($vision290513) )
        {
            mysqli_query($con,"UPDATE `empresa` SET `vision` = '$vision290513' WHERE id = 1;");
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
    }
?>