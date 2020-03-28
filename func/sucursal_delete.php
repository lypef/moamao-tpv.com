<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM sucursales WHERE id = $id;");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/sucursales.php?delete_sucursal=true"</script>';
    }else
    {
        echo '<script>location.href = "/sucursales.php?nodelete_sucursal=true"</script>';
    }

?>