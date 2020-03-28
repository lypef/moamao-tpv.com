<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM almacen WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/almacen.php?delete_almacen=true"</script>';
    }else
    {
        echo '<script>location.href = "/almacen.php?nodelete_almacen=true"</script>';
    }

?>