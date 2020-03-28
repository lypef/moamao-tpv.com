<?php
    
    include 'db.php';
    db_sessionValidarNO();
    $con = db_conectar();  
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    
    
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
            mysqli_query($con,"UPDATE `users` SET `nombre` = '$nombre', `imagen` = '$img', `password` = '$pass' WHERE id = $id;");
        }else
        {
            mysqli_query($con,"UPDATE `users` SET `nombre` = '$nombre', `imagen` = '$img' WHERE id = $id;");
        }
        
    }else
    {
        if ($pass1 == $pass2 && !empty($pass1))
        {
            $pass = md5($pass1);
            mysqli_query($con,"UPDATE `users` SET `nombre` = '$nombre', `password` = '$pass' WHERE id = $id;");
        }else
        {
            mysqli_query($con,"UPDATE `users` SET `nombre` = '$nombre' WHERE id = $id;");
        }
    }
    db_sessionDestroy();
?>