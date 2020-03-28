<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $id = $_POST['id'];
    
    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM departamentos WHERE id = '$id';");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/departments.php?delete_departament=true"</script>';
    }else
    {
        echo '<script>location.href = "/departments.php?nodelete_departament=true"</script>';
    }

?>