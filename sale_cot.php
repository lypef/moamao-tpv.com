<?php
    include 'func/header.php';
    validateFolioVenta($_GET["folio"]);
    UpdateAdeudoCredits($_GET["folio"]);
?>
<!-- Start page content -->
<section id="page-content" class="page-wrapper">
    <!-- Start Accordion Area -->
    <div class="">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    PRODUCTOS AGREGADOS A COTIZACION
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                <?php
                                    if ($_GET["folio"])
                                    {
                                        echo table_sale_products_finaly_cotizacion($_GET["folio"]); 
                                    }
                                ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>            
    <!-- End Of Accordion Area -->
<div class="col-lg-12 col-md-6 text-center">
    <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#delete"><span>Eliminar</span> </a>
    <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#success_sale"><span>Remisionar</span> </a>
    <a class="button small button-black mb-20" href="/facturar.php?folio=<?php echo $_GET["folio"] ?>&stocck=1"><span>Facturar</span> </a>
    <a class="button small button-black mb-20" href="/sale_finaly_report_cotizacion.php?folio_sale=<?php echo $_GET["folio"] ?>"><span>Imprimir</span> </a>
    <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#credit<?php echo $_GET["folio"] ?>"><span>Credito</span> </a>
    <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#mail<?php echo $_GET["folio"] ?>"><span>Enviar</span> </a>
    
</div>
<!-- Start page content -->
<section id="page-content" class="page-wrapper">
    <!-- Start Product List -->
    <div class="product-list-tab">
        <div class="container">
            <div class="row">
                <div class="product-list tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="home">
                        <?php 
                            if ($_GET["search"])
                            {
                                echo _getProducts_CotSearch($_GET["search"], $_GET["folio"],$_GET["pagina"]);
                            }
                            else
                            {
                                echo _getProducts_cot($_GET["pagina"], $_GET["folio"]);
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
    if (getUrlVars()["nostock"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> No tenemos stock";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
    
    if ($_GET["search"])
    {
        echo _getProductsModal_sale_search($_GET["search"], $_GET["folio"],$_GET["pagina"]);
    }
    else
    {
        echo _getProductsModal_sale($_GET["pagina"], $_GET["folio"]);
    }
    if ($_GET["folio"])
    {
        echo table_SalesModal($_GET["folio"]);
    }
    echo table_cotizacion_modal();
?>
        

<!--Agragar producto a venta-->
<div class="modal fade" id="add_car_generic" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"></h5>
            </button>
            </div>
            <div class="modal-body">
            <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            <div class="section-title-2 text-uppercase mb-40 text-center">
                <h4>AGREGAR PRODUCTO GENERICO</h4>
            </div>
        </div>
        
        <form action="func/producst_add_sale_generic.php" autocomplete="off" method="post">
            <input type="hidden" id="url" name="url" value="<?php echo $_SERVER['REQUEST_URI'] 
            ?>">
            <input type="hidden" id="folio" name="folio" value="<?php echo $_GET["folio"] ?>">
            <input type="hidden" id="hijo" name="hijo" value="0">
            
            
            <div class="col-md-6">
                <label>Costo del producto</label>
                <input type="text" name="costo" id="costo" placeholder="Costo del producto" required>
            </div>

            <div class="col-md-6">
                <label>Numero de Unidades<</label>
                <input type="number" step="1" name="unidades" id="unidades" placeholder="Ingrese las unidades" required value="1">
            </div>

            <div class="col-md-12">
                <br><label>Nombre del producto</label>
                <input type="text" name="p_generic" id="p_generic" placeholder="Ingrese nombre de el producto" required>
            </div>
        </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Agregar</button>
            </form>
        </div>
</div>
</div>
</div>

<!--Eliminar venta-->
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"></h5>
            </button>
            </div>
            <div class="modal-body">
            <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            <div class="section-title-2 text-uppercase mb-40 text-center">
                <h4>Eliminar cotizacion</h4>
            </div>
            <form action="func/delete_f_venta.php" autocomplete="off" method="post">
                <input type="hidden" id="folio" name="folio" value="<?php echo $_GET["folio"] ?>">
                <input type="hidden" id="url" name="url" value="/cotizaciones.php">
        </div>
        </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="sumbit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
</div>
</div>
</div>

<div class="modal fade" id="success_sale" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">REMISIONAR COTIZACION ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Al REMISIONAR la cotizacion, el sistema disminuira las existencias de cada producto agregado y posteriomente tomara la sumatoria como un ingreso.</p>

        
      </div>
      <div class="modal-footer">
        <form action="func/product_sale_finaly.php" method="post">
            <input type="hidden" id="folio" name="folio" value="<?php echo $_GET["folio"]; ?>">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
            <button type="submit" class="btn btn-warning">CONFIRMAR</button>
        </form>
      </div>
    </div>
  </div>
</div>
