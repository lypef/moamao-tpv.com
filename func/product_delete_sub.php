<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    $url = $_POST['url'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM productos_sub WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }
?>