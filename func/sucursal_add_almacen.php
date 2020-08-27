<?php
    include 'db.php';
    db_sessionValidarNO();

    $url = $_POST['url'];
    $sucursal = $_POST['id'];
    $almacen = $_POST['almacen'];
    

    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `sucursal_almacen` (`sucursal`, `almacen`) VALUES ('$sucursal', '$almacen');");

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