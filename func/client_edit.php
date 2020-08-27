<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    $url = $_POST['url'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $p_descuento = $_POST['p_descuento'];
    $rfc = $_POST['rfc'];
    $r_social = $_POST['r_social'];
    $correo = $_POST['correo'];
    
    $con = db_conectar();  
    mysqli_query($con,"UPDATE `clients` SET `nombre` = '$nombre', `direccion` = '$direccion', `telefono` = '$telefono', `descuento` = '$p_descuento', `rfc` = '$rfc', `razon_social` = '$r_social', `correo` = '$correo' WHERE id = '$id';");

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
        echo '<script>location.href = "/clients.php?pagina=1&noupdate=true"</script>';
    }

?>