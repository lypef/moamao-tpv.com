<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_GET['id'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM annuities WHERE id = $id;");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/annuity.php?okannuity=true"</script>';
    }else
    {
        echo '<script>location.href = "/annuity.php?noannuity=true"</script>';
    }
?>