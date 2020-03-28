<?php
    include 'func/header.php';
?>
<div class="col-md-12">
    <div class="section-title-2 text-uppercase mb-40 text-center">
            <h4>SELECCIONE CLIENTE PARA CREAR VENTA</h4>
    </div>
    <?php 
        if ($_GET["search"])
        {
            echo create_sale_SelectClientSearch_client($_GET["search"]);
        }else
        {
            echo create_sale_SelectClient_client($_GET["pagina"]);
        }
    ?>
</div>  
<?php
    include 'func/footer.php';
?>
        
