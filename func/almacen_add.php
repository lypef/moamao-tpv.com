<?php
    include 'db.php';
    db_sessionValidarNO();

    $url = $_POST['url'];
    $nombre = $_POST['almacen_nombre'];
    $ubicacion = $_POST['almacen_ubicacion'];
    $telefono = $_POST['almacen_telefono'];

    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `almacen` (`nombre`, `ubicacion`, `telefono`) VALUES ('$nombre', '$ubicacion', '$telefono');");

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
            echo '<script>location.href = "'.$url.'&add_almacen=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?add_almacen=true"</script>';
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
            echo '<script>location.href = "'.$url.'&noadd_almacen=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?noadd_almacen=true"</script>';
        }
    }
?>