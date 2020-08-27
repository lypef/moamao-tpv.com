<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_GET["id"];

    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM sucursal_almacen WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/sucursales.php?delete_almacen=true"</script>';
    }else
    {
        echo '<script>location.href = "/sucursales.php?nodelete_almacen=true"</script>';
    }

?>