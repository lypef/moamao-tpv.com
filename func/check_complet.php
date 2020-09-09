<?php
    include 'db.php';
    db_sessionValidarNO();
    
    if ($_SESSION['token'] == GetToken())
    {
        $id = $_GET['id'];
        $folio = $_GET['folio'];
        
        if ($id > 0)
        {
            $con = db_conectar();  

            mysqli_query($con,"UPDATE `product_pedido` SET `completado` = '1' WHERE `product_pedido`.`id` = $id; ");

            if (!mysqli_error($con))
            {
                echo '<script>location.href = "/orders.php?proceso_yes=1&search='.$folio.'"</script>';
            }else
            {
                echo '<script>location.href = "/orders.php?proceso_no=1&search='.$folio.'"</script>';
            }
        }
        else
        {
            echo '<script>location.href = "/orders.php?proceso_no=1&search='.$folio.'"</script>';
        }
    }
?>