<?php
    $url = $_POST['url'];
    $url = str_replace("&update_producto=true", "", $url);
    $url = str_replace("&noupdate_producto=true", "", $url);
    
    if ($_POST['precio'] > 0 && $_POST['p_oferta'] > 0)
    {
        include 'db.php';
        db_sessionValidarNO();
        $con = db_conectar();  
        
        $id = $_POST['id'];
        $url_img_1 = ReturnUrlImg1($_POST['id']);
        $url_img_2 = ReturnUrlImg2($_POST['id']);
        $url_img_3 = ReturnUrlImg3($_POST['id']);
        $url_img_4 = ReturnUrlImg4($_POST['id']);

        if ($_POST['delete_img_1'])
        {
            $img_url_tmp = "../images/".$url_img_1;
            mysqli_query($con,"UPDATE `productos` SET foto0 = '' WHERE id = $id;");
            if (file_exists($img_url_tmp))
            {
                unlink($img_url_tmp);
            }
        }
        
        if ($_POST['delete_img_2'])
        {
            $img_url_tmp = "../images/".$url_img_2;
            mysqli_query($con,"UPDATE `productos` SET foto1 = '' WHERE id = $id;");
            if (file_exists($img_url_tmp))
            {
                unlink($img_url_tmp);
            }
        }

        if ($_POST['delete_img_3'])
        {
            $img_url_tmp = "../images/".$url_img_3;
            mysqli_query($con,"UPDATE `productos` SET foto2 = '' WHERE id = $id;");
            if (file_exists($img_url_tmp))
            {
                unlink($img_url_tmp);
            }
        }

        if ($_POST['delete_img_4'])
        {
            $img_url_tmp = "../images/".$url_img_4;
            mysqli_query($con,"UPDATE `productos` SET foto3 = '' WHERE id = $id;");
            if (file_exists($img_url_tmp))
            {
                unlink($img_url_tmp);
            }
        }
        
        $parte = $_POST['parte'];
        $nombre = $_POST['name'];
        $precio = $_POST['precio'];
        $p_oferta = $_POST['p_oferta'];
        $stock = $_POST['stock'];
        $t_entrega = $_POST['t_entrega'];
        $descripcion = $_POST['descripcion'];
        $almacen = $_POST['almacen'];
        $departamento = $_POST['departamento'];
        $ubicacion = $_POST['ubicacion'];
        $marca = $_POST['marca'];
        $proveedor = $_POST['proveedor'];
        $use_oferta = $_POST['use_oferta'];
        $stock_min = $_POST['stock_minimo'];
        $stock_max = $_POST['stock_maximo'];
        $precio_costo = $_POST['precio_costo'];
        $cv = $_POST['cv'];
        $um = $_POST['um'];
        $um_des = $_POST['um_des'];
        
        $name_img = date("YmdHis").".jpg";

        $img0 = "";
        $img1 = "";
        $img1 = "";
        $img2 = "";

        if ($_FILES["imagen0"]["name"])
        {
            $ruta_img = 'product/product_img1'.$name_img;
            $img_access = '../images/'.$ruta_img;

            if ( copy($_FILES["imagen0"]["tmp_name"], $img_access ) )
            {
                $img0 = $ruta_img;
                mysqli_query($con,"UPDATE `productos` SET foto0 = '$img0' WHERE id = $id;");
            }
        }

        if ($_FILES["imagen1"]["name"])
        {
            $ruta_img = 'product/product_img2'.$name_img;
            $img_access = '../images/'.$ruta_img;

            if ( copy($_FILES["imagen1"]["tmp_name"], $img_access ) )
            {
                $img1 = $ruta_img;
                mysqli_query($con,"UPDATE `productos` SET foto1 = '$img1' WHERE id = $id;");
            }
        }

        if ($_FILES["imagen2"]["name"])
        {
            $ruta_img = 'product/product_img3'.$name_img;
            $img_access = '../images/'.$ruta_img;

            if ( copy($_FILES["imagen2"]["tmp_name"], $img_access ) )
            {
                $img2 = $ruta_img;
                mysqli_query($con,"UPDATE `productos` SET foto2 = '$img2' WHERE id = $id;");
            }
        }

        if ($_FILES["imagen3"]["name"])
        {
            $ruta_img = 'product/product_img4'.$name_img;
            $img_access = '../images/'.$ruta_img;

            if ( copy($_FILES["imagen3"]["tmp_name"], $img_access ) )
            {
                $img3 = $ruta_img;
                mysqli_query($con,"UPDATE `productos` SET foto3 = '$img3' WHERE id = $id;");
            }
        }

        mysqli_query($con,"UPDATE `productos` SET `no. De parte` = '$parte', `nombre` = '$nombre', `descripcion` = '$descripcion', `almacen` = '$almacen', `departamento` = '$departamento', `loc_almacen` = '$ubicacion', `marca` = '$marca', `proveedor` = '$proveedor', `oferta` = '$use_oferta', `precio_normal` = '$precio', `precio_oferta` = '$p_oferta', `stock` = '$stock', `tiempo de entrega` = '$t_entrega', `stock_min` = '$stock_min', `stock_max` = '$stock_max', `precio_costo` = '$precio_costo', `cv` = '$cv', `um` = '$um', `um_des` = '$um_des' WHERE `productos`.`id` = $id;");

        if (!mysqli_error($con))
        {
            echo '<script>location.href = "'.$url.'&update_producto=true"</script>';
        }else
        {
            echo '<script>location.href = "'.$url.'&noupdate_producto=true"</script>';
        }
    }
    else
    {
        echo '<script>location.href = "'.$url.'&noupdate_producto=true"</script>';
    }
?>