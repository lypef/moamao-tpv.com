<?php
    include 'db.php';
    db_sessionValidarNO();
    
    if ($_SESSION['token'] == GetToken())
    {
        $fecha = $_POST['fecha'];
        $folio = $_POST['folio'];
        
        $con = db_conectar();  

        mysqli_query($con,"UPDATE `folio_venta` SET `f_entrega` = '$fecha' WHERE `folio_venta`.`folio` = '$folio'; ");

        if (!mysqli_error($con))
        {
            //Enviar email
            echo '<script>location.href = "/orders.php?proceso_yes=1&search='.$folio.'"</script>';
        }else
        {
            echo '<script>location.href = "/orders.php?proceso_no=1&search='.$folio.'"</script>';
        }
    }
?>