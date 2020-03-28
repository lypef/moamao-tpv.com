<?php
    include 'func/header.php';
?>
<!-- Start page content -->
        <section id="page-content" class="page-wrapper">
            <!-- Start Product List -->
            <div class="product-list-tab">
                <div class="container">
                    <div class="row">
                        <div class="product-list tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                <?php 
                                    if ($_GET["department"])
                                    {
                                        echo _getProductsDepartment($_GET["department"]);
                                    }
                                    elseif ($_GET["search"])
                                    {
                                        echo _getProductsSearch($_GET["search"]);
                                    }
                                    elseif ($_GET["almacen"])
                                    {
                                        echo _getProductsAlmacen($_GET["almacen"]);
                                    }
                                    else
                                    {
                                        echo _getProducts($_GET["pagina"]);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End page content -->
<script >
    if (getUrlVars()["delete_product"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ELIMINADO!</strong> Producto ELIMINADO con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["update_producto"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ACTUALIZADO!</strong> Producto ACTUALIZADO con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["noupdate_producto"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> Se encontraron errores al actualizar el producto.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["sale_ok"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>EXITO!</strong> Venta realizada correctamente.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["nosale_ok"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Ups!</strong> Se encontraron errores, verifique stock de productos y ventas";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
    
    if ($_GET["department"])
    {
        echo _getProductsModalDepartment($_GET["department"]);
    }
    elseif ($_GET["search"])
    {
        echo _getProductsModalSearch($_GET["search"]);
    }
    elseif ($_GET["almacen"])
    {
        echo _getProductsModalAlmacen($_GET["almacen"]);
    }
    else
    {
        echo _getProductsModal($_GET["pagina"]);
    }
    if ($_GET["folio_sale"])
    {
        echo '<meta http-equiv="refresh" content="0; url=sale_finaly_report.php?folio_sale='.$_GET["folio_sale"].'">';
    }
?>
        
