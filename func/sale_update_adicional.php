<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $folio = $_POST['folio'];
    $add_operador = $_POST['add_operador'];
    $add_placa = $_POST['add_placa'];
    $add_no_nota = $_POST['add_no_nota'];
    $add_no_ticket = $_POST['add_no_ticket'];


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
    
    mysqli_query($con,"UPDATE `folio_venta` SET `operador` = '$add_operador', `placa` = '$add_placa', `no_nota` = '$add_no_nota', `no_ticket` = '$add_no_ticket' WHERE `folio_venta`.`folio` = $folio ;");

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