<?php
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $id = $_POST['id'];
    $folio = $_POST['folio'];
    $abono = $_POST['abono'];
    $url = $_POST['url'];
    
    //mysqli_query($con,"UPDATE `credits` SET `abono` = abono + $abono WHERE `credits`.`id` = $id;");
    mysqli_query($con,"INSERT INTO `credit_pay` (`credito`, `monto`, `fecha`) VALUES ('$id', '$abono', current_timestamp());");

    
    if (!mysqli_error($con))
    {
        CheckCredit($id, $folio);
        echo '<script>location.href = "'.$url.'"</script>';
    }
?>