<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    $nombre = $_POST['departamento_add_nombre'];
    $descripcion = $_POST['departamento_add_descripcion'];
    
    $con = db_conectar();  
    mysqli_query($con,"UPDATE `departamentos` SET `nombre` = '$nombre', `descripcion` = '$descripcion' WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/departments.php?update_departament=true"</script>';
    }else
    {
        echo '<script>location.href = "/departments.php?noupdate_departament=true"</script>';
    }

?>