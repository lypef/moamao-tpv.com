<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM productos WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/products.php?pagina=1&delete_product=true"</script>';
    }else
    {
        echo '<script>location.href = "/products_edit.php?id='.$id.'&nodelete=true"</script>';
    }
?>