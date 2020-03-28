<?php
    session_start();
    if (isset($_SESSION['users_id']) == false)
    {
         echo '<script>location.href = "products_detail_nosesion.php?id='.$_GET["id"].'"</script>';
    }    
    include 'func/header.php';
?>
<?php echo _getProductsDetails($_GET["id"]) ?>
<?php
    include 'func/footer.php'
?>
