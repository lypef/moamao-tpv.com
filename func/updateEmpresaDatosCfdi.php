<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $url = $_POST['url'];
    $cfdi_lugare_expedicion = $_POST['cfdi_lugare_expedicion'];
    $cfdi_rfc = $_POST['cfdi_rfc'];
    $cfdi_regimen = $_POST['cfdi_regimen'];
    $cfdi_cer = $_POST['cfdi_cer'];
    $cfdi_key = $_POST['cfdi_key'];
    $cfdi_pass = $_POST['cfdi_pass'];
    
    
    $con = db_conectar();  
    if ( 
        !empty(trim($cfdi_lugare_expedicion)) && 
        !empty(trim($cfdi_rfc)) && 
        !empty(trim($cfdi_regimen)) && 
        !empty(trim($cfdi_cer)) && 
        !empty(trim($cfdi_key)) && 
        !empty(trim($cfdi_pass))
       )
    {
        mysqli_query($con,"UPDATE `empresa` SET `cfdi_lugare_expedicion` = '$cfdi_lugare_expedicion', `cfdi_rfc` = '$cfdi_rfc', `cfdi_regimen` = '$cfdi_regimen', `cfdi_cer` = '$cfdi_cer', `cfdi_key` = '$cfdi_key', `cfdi_pass` = '$cfdi_pass' WHERE id = 1;");
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