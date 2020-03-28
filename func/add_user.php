<?php
    
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $url = $_POST['url'];
    $username = $_POST['username'];
    $pass = md5($_POST['pass']);
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $sucursal = $_POST['sucursal'];
    

    if ($_POST['product_add'])
    {
        $product_add = 1;
    }else
    {
        $product_add = 0;
    }

    if ($_POST['product_gest'])
    {
        $product_gest = 1;
    }else
    {
        $product_gest = 0;
    }

    if ($_POST['gen_orden_compra'])
    {
        $gen_orden_compra = 1;
    }else
    {
        $gen_orden_compra = 0;
    }

    if ($_POST['client_add'])
    {
        $client_add = 1;
    }else
    {
        $client_add = 0;
    }

    if ($_POST['client_guest'])
    {
        $client_guest = 1;
    }else
    {
        $client_guest = 0;
    }

    if ($_POST['almacen_add'])
    {
        $almacen_add = 1;
    }else
    {
        $almacen_add = 0;
    }

    if ($_POST['almacen_guest'])
    {
        $almacen_guest = 1;
    }else
    {
        $almacen_guest = 0;
    }

    if ($_POST['depa_add'])
    {
        $depa_add = 1;
    }else
    {
        $depa_add = 0;
    }

    if ($_POST['depa_guest'])
    {
        $depa_guest = 1;
    }else
    {
        $depa_guest = 0;
    }

    if ($_POST['propiedades'])
    {
        $propiedades = 1;
    }else
    {
        $propiedades = 0;
    }

    if ($_POST['usuarios'])
    {
        $usuarios = 1;
    }else
    {
        $usuarios = 0;
    }

    if ($_POST['finanzas'])
    {
        $finanzas = 1;
    }else
    {
        $finanzas = 0;
    }

    if ($_POST['change_suc'])
    {
        $change_suc = 1;
    }else
    {
        $change_suc = 0;
    }

    if ($_POST['sucursal_gest'])
    {
        $sucursal_gest = 1;
    }else
    {
        $sucursal_gest = 0;
    }

    if ($_POST['caja'])
    {
        $caja = 1;
    }else
    {
        $caja = 0;
    }

    if ($_POST['super_pedidos'])
    {
        $super_pedidos = 1;
    }else
    {
        $super_pedidos = 0;
    }
    
    
    
    $name_img = date("YmdHis").".jpg";

    $img = "";

    if ($_FILES["imagen"]["name"])
    {
        $ruta_img = 'users/usuario'.$name_img;
        $img_access = '../images/'.$ruta_img;

        if ( copy($_FILES["imagen"]["tmp_name"], $img_access ) )
        {
            $img = $ruta_img;
        }
    }

    mysqli_query($con,"INSERT INTO `users` (`username`, `password`, `nombre`, `imagen`, `product_add`, `product_gest`, `gen_orden_compra`, `client_add`, `client_guest`, `almacen_add`, `almacen_guest`, `depa_add`, `depa_guest`, `propiedades`, `usuarios`, `finanzas`, `descripcion`, `sucursal`, `change_suc`, `sucursal_gest`, `caja`, `super_pedidos`) VALUES ('$username', '$pass', '$nombre','$img', '$product_add', '$product_gest', '$gen_orden_compra', '$client_add', '$client_guest', '$almacen_add', '$almacen_guest', '$depa_add', '$depa_guest', '$propiedades', '$usuarios', '$finanzas', '$descripcion', '$sucursal', '$change_suc', '$sucursal_gest', '$caja', '$super_pedidos');");

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }
?>