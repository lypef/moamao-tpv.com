<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $url = $_POST['url'];
    $almacen = $_POST['almacen'];
    $padre = $_POST['padre'];
    $stock = $_POST['stock'];
    $ubicacion = $_POST['ubicacion'];
    $max = $_POST['max'];
    $min = $_POST['min'];

    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `productos_sub` (`padre`, `almacen`, `stock`, `ubicacion`, `max`, `min`) VALUES ('$padre', '$almacen', '$stock', '$ubicacion', '$max', '$min');");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }
    
?>