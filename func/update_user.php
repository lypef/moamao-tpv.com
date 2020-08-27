<?php
    
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $id = $_POST['id'];
    $url = $_POST['url'];
    $nombre = $_POST['nombre'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    $descripcion = $_POST['descripcion'];
    $sucursal = $_POST['suc'.$_POST['id']];
    

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
    
    if ($_POST['vtd_pg'])
    {
        $vtd_pg = 1;
    }else
    {
        $vtd_pg = 0;
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

    if ($_FILES["imagen"]["name"])
    {
        if ($pass1 == $pass2 && !empty($pass1))
        {
            $pass = md5($pass1);
            mysqli_query($con,"UPDATE `users` SET `password` = '$pass', `nombre` = '$nombre', `imagen` = '$img', `product_add` = '$product_add', `product_gest` = '$product_gest', `gen_orden_compra` = '$gen_orden_compra', `client_add` = '$client_add', `client_guest` = '$client_guest', `almacen_add` = '$almacen_add', `almacen_guest` = '$almacen_guest', `depa_add` = '$depa_add', `depa_guest` = '$depa_guest', `propiedades` = '$propiedades', `usuarios` = '$usuarios', `finanzas` = '$finanzas', `descripcion` = '$descripcion', `sucursal` = '$sucursal', `change_suc` = '$change_suc', `sucursal_gest` = '$sucursal_gest', `caja` = '$caja', `super_pedidos` = '$super_pedidos', `vtd_pg` = '$vtd_pg' WHERE id = '$id';");
        }else
        {
            mysqli_query($con,"UPDATE `users` SET `nombre` = '$nombre', `imagen` = '$img', `product_add` = '$product_add', `product_gest` = '$product_gest', `gen_orden_compra` = '$gen_orden_compra', `client_add` = '$client_add', `client_guest` = '$client_guest', `almacen_add` = '$almacen_add', `almacen_guest` = '$almacen_guest', `depa_add` = '$depa_add', `depa_guest` = '$depa_guest', `propiedades` = '$propiedades', `usuarios` = '$usuarios', `finanzas` = '$finanzas', `descripcion` = '$descripcion', `sucursal` = '$sucursal', `change_suc` = '$change_suc', `sucursal_gest` = '$sucursal_gest', `caja` = '$caja', `super_pedidos` = '$super_pedidos', `vtd_pg` = '$vtd_pg' WHERE id = '$id';");
        }
        
    }else
    {
        if ($pass1 == $pass2 && !empty($pass1))
        {
            $pass = md5($pass1);
            mysqli_query($con,"UPDATE `users` SET `password` = '$pass', `nombre` = '$nombre', `product_add` = '$product_add', `product_gest` = '$product_gest', `gen_orden_compra` = '$gen_orden_compra', `client_add` = '$client_add', `client_guest` = '$client_guest', `almacen_add` = '$almacen_add', `almacen_guest` = '$almacen_guest', `depa_add` = '$depa_add', `depa_guest` = '$depa_guest', `propiedades` = '$propiedades', `usuarios` = '$usuarios', `finanzas` = '$finanzas', `descripcion` = '$descripcion', `sucursal` = '$sucursal', `change_suc` = '$change_suc', `sucursal_gest` = '$sucursal_gest', `caja` = '$caja', `super_pedidos` = '$super_pedidos', `vtd_pg` = '$vtd_pg' WHERE id = '$id';");
        }else
        {
            mysqli_query($con,"UPDATE `users` SET `nombre` = '$nombre', `product_add` = '$product_add', `product_gest` = '$product_gest', `gen_orden_compra` = '$gen_orden_compra', `client_add` = '$client_add', `client_guest` = '$client_guest', `almacen_add` = '$almacen_add', `almacen_guest` = '$almacen_guest', `depa_add` = '$depa_add', `depa_guest` = '$depa_guest', `propiedades` = '$propiedades', `usuarios` = '$usuarios', `finanzas` = '$finanzas', `descripcion` = '$descripcion', `sucursal` = '$sucursal', `change_suc` = '$change_suc', `sucursal_gest` = '$sucursal_gest', `caja` = '$caja', `super_pedidos` = '$super_pedidos', `vtd_pg` = '$vtd_pg' WHERE id = '$id';");
        }
    }

    if (!mysqli_error($con))
    {
        echo '<script>location.href = "'.$url.'"</script>';
    }else
    {
        echo mysqli_error($con);   
    }
?>