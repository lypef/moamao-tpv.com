<?php
//error_reporting(0);
session_start();
include 'db.php';
$con = db_conectar();

if ($con->connect_errno)
{
    echo '<script>location.href = "?db_noconect=true"</script>';
    exit();
}

if ($_POST['username'] == null || $_POST['password'] == null)
{
  echo '<script>location.href = "?db_empty=true"</script>';
}
else
{
    $user = mysqli_real_escape_string($con, $_POST['username']);
    $pass = mysqli_real_escape_string($con, md5($_POST['password']));
    $consulta = mysqli_query($con, "SELECT * FROM users WHERE username = '$user' AND password = '$pass'");
    if (mysqli_num_rows($consulta) > 0)
    {
            while($row = mysqli_fetch_array($consulta))
            {
              $_SESSION['users_id'] = $row[0];
              $_SESSION['users_username'] = $row[1];
              $_SESSION['users_nombre'] = $row[3];
              $_SESSION['users_foto'] = $row[4];
              $_SESSION['product_add'] = $row[5];
              $_SESSION['product_gest'] = $row[6];
              $_SESSION['gen_orden_compra'] = $row[7];
              $_SESSION['client_add'] = $row[8];
              $_SESSION['client_guest'] = $row[9];
              $_SESSION['almacen_add'] = $row[10];
              $_SESSION['almacen_guest'] = $row[11];
              $_SESSION['depa_add'] = $row[12];
              $_SESSION['depa_guest'] = $row[13];
              $_SESSION['propiedades'] = $row[14];
              $_SESSION['usuarios'] = $row[15];
              $_SESSION['finanzas'] = $row[16];
              $_SESSION['sucursal'] = $row[18];
              $_SESSION['change_suc'] = $row[19];
              $_SESSION['sucursal_gest'] = $row[20];
              $_SESSION['caja'] = $row[21];
              $_SESSION['super_pedidos'] = $row[22];
              $_SESSION['vtd_pg'] = $row[25];
            }
             
            $tmp = mysqli_query($con, "SELECT * FROM empresa");
            while($row = mysqli_fetch_array($tmp))
            {
              $_SESSION['empresa_nombre'] = $row[1];
              $_SESSION['empresa_nombre_corto'] = $row[2];
              $_SESSION['empresa_direccion'] = $row[3];
              $_SESSION['empresa_correo'] = $row[4];
              $_SESSION['empresa_telefono'] = $row[5];
              $_SESSION['empresa_mision'] = $row[6];
              $_SESSION['empresa_vision'] = $row[7];
              $_SESSION['empresa_contacto'] = $row[8];
              $_SESSION['empresa_fb'] = $row[9];
              $_SESSION['empresa_yt'] = $row[10];
              $_SESSION['empresa_tw'] = $row[11];
              $_SESSION['iva'] = $row[12];
              $_SESSION['empresa_footer'] = $row[13];
              $_SESSION['cfdi_lugare_expedicion'] = $row[14];
              $_SESSION['cfdi_rfc'] = $row[15];
              $_SESSION['cfdi_regimen'] = $row[16];
              $_SESSION['cfdi_cer'] = $row[17];
              $_SESSION['cfdi_key'] = $row[18];
              $_SESSION['cfdi_pass'] = $row[19];
            }
            setcookie('clta_session', 'yes', time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('clta_session_user', $user, time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('clta_session_pass', $pass, time() + (86400 * 30), "/"); // 86400 = 1 day
            echo '<script>location.href = "products.php?pagina=1"</script>';
    }
    else
    {
        setcookie('clta_session', 'yes', 0, "/");
        setcookie('clta_session_user', '', 0, "/");
        setcookie('clta_session_pass', '', 0, "/");
        echo '<script>location.href = "?no_session=true"</script>';
    }
}
?>
