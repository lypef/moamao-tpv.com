<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    $nombre = $_POST['almacen_nombre'];
    $ubicacion = $_POST['almacen_ubicacion'];
    $telefono = $_POST['almacen_telefono'];
    $serie_cfdi = $_POST['serie_cfdi'];
        
    $con = db_conectar();  
    mysqli_query($con,"UPDATE `sucursales` SET `nombre` = '$nombre', `direccion` = '$ubicacion', `telefono` = '$telefono', `cfdi_serie` = '$serie_cfdi' WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/sucursales.php?update_sucursal=true"</script>';
    }else
    {
        echo '<script>location.href = "/sucursales.php?noupdate_sucursales=true"</script>';
    }

?>