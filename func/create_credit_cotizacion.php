<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
    
    $con = db_conectar();

    $url = $_POST['url'];
    $url = str_replace("?process_yes=true", "", $url);
    $url = str_replace("?sale_noliquid=true", "", $url);
    $url = str_replace("&process_yes=true", "", $url);
    $url = str_replace("&sale_noliquid=true", "", $url);
    
    $folio = $_POST['folio'];
    $cliente = 0;
    $sucursal = 0;

    $data = mysqli_query($con,"SELECT client, sucursal FROM folio_venta WHERE folio = $folio ");
	
    if($row = mysqli_fetch_array($data))
    {
        $cliente = $row[0];
        $sucursal = $row[1];
    }
    
    $totalPagar = Return_TotalPagar_Folio($folio);
    $sql = "INSERT INTO `credits` (`client`, `f_registro`, `factura`, `adeudo`, `abono`, `dias_credit`, `pay`, `sucursal`) VALUES ('$cliente', current_timestamp(), '$folio', '$totalPagar', '0', '7', '0', '$sucursal')";
    mysqli_query($con,$sql);

    $addpregunta = false;

    for($i=0;$i<strlen($url);$i++)
    {
        if ($url[$i] == "?")
        {
            $addpregunta = true;
        }
    }

    if ($addpregunta)
    {
        if (!mysqli_error($con))
        {
            echo '<script>location.href = "'.$url.'&process_yes=true"</script>';
        }else
        {
            echo '<script>location.href = "'.$url.'&sale_noliquid=true"</script>';
        }
    }else
    {
        if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'?process_yes=true"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'?sale_noliquid=true"</script>';
    }
    }

    
?>