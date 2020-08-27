<?php
    include 'db.php';
    
    $id = $_POST['id'];
    $url = $_POST['url'];

    $con = db_conectar();  
    mysqli_query($con,"DELETE FROM credits WHERE id = '$id';");

    if (!mysqli_error($con))
    {
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
            echo '<script>location.href = "'.$url.'&sale_delete=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?sale_delete=true"</script>';
        }
    }else
    {
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
            echo '<script>location.href = "'.$url.'&sale_nodelete=true"</script>';
        }else{
            echo '<script>location.href = "'.$url.'?sale_nodelete=true"</script>';
        }
    }
?>