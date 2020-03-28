<?php
    include 'func/header.php';
?>
<div class="col-md-12">
    <div class="section-title-2 text-uppercase mb-40 text-center">
            <h4>SELECCIONE NUEVO CLIENTE</h4>
    </div>
    <?php 
        if ($_GET["search"])
        {
            echo ClientSearch_changeClient($_GET["search"], $_GET["folio"], $_GET["cotizacion"], $_GET["pedido"], $_GET["vtd"]);
        }else
        {
            echo create_sale_SelectClient_ChangeClient($_GET["pagina"], $_GET["folio"], $_GET["cotizacion"], $_GET["pedido"], $_GET["vtd"]);
        }
    ?>
</div>  
<script>
if (getUrlVars()["clientreturn"])
    {
        var body = "<div class='alert alert-warning alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> La venta no fue creada.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
?>
        
