<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
          
    $vendedor = $_SESSION['users_id'];
    $client = $_POST['id'];
    $fecha = date("Y-m-d H:i:s");
    $folio = $vendedor . date("YmdHis");
    $descuento = $_POST['desc'. $_POST['id']];
    $sucursal = $_POST['suc'. $_POST['id']];
    $iva = $_POST['iva'. $_POST['id']];
    $t_pago = $_POST['t_pago'];

    $con = db_conectar();  
    mysqli_query($con,"INSERT INTO `folio_venta` (`folio`,`vendedor`, `client`, `descuento`, `fecha`, `open`, `sucursal`, `iva`, `t_pago`) VALUES ('$folio', '$vendedor', '$client', '$descuento', '$fecha', '1', '$sucursal','$iva', '$t_pago');");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "/sale.php?folio='.$folio.'&pagina=1"</script>';
    }else
    {
        echo '<script>location.href = "/create_sale.php?pagina=1&clientreturn=true"</script>';
    }
?>