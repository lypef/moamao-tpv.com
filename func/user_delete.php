<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $url = $_POST['url'];
    $id = $_POST['id'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM users WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'?delete=true"</script>';
    }else
    {
        echo '<script>location.href = "/users.php?nodelete=true"</script>';
    }

?>