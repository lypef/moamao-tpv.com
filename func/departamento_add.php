<?php
    include 'db.php';
    db_sessionValidarNO();

    $url = $_POST['url'];
    $nombre = $_POST['departamento_add_nombre'];
    $descripcion = $_POST['departamento_add_descripcion'];

    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `departamentos` (`nombre`, `descripcion`) VALUES ('$nombre', '$descripcion');");

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
            echo '<script>location.href = "'.$url.'&add_department=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?add_department=true"</script>';
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
            echo '<script>location.href = "'.$url.'&noadd_department=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?noadd_department=true"</script>';
        }
    }
?>