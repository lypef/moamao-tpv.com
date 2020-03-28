<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    $nombre = $_POST['almacen_nombre'];
    $ubicacion = $_POST['almacen_ubicacion'];
    $telefono = $_POST['almacen_telefono'];
    
    $con = db_conectar();  
    mysqli_query($con,"UPDATE `almacen` SET `nombre` = '$nombre', `ubicacion` = '$ubicacion', `telefono` = '$telefono' WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/almacen.php?update_almacen=true"</script>';
    }else
    {
        echo '<script>location.href = "/almacen.php?noupdate_almacen=true"</script>';
    }

?>