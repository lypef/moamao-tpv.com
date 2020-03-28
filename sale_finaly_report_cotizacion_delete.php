<?php
    require_once 'func/db.php';
    
    $con = db_conectar();  
    
    $folio = $_GET["folio_sale"];
    
    mysqli_query($con,"DELETE FROM folio_venta WHERE folio = '$folio';");

    if (!mysqli_error($con))
    {
        echo '<h3 style="text-align: center;"><span style="color: #800000;"><strong>COTIZACION ELIMINADA CORRECTAMENTE</strong></span></h3>';
    }else
    {
        echo '<h3 style="text-align: center;"><span style="color: #800000;"><strong><span style="color: #0000ff;">LA COTIZACION NO SE ENCONTRO</span><br /></strong></span></h3>';
    }
?>