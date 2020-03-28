<?php
    include 'func/header.php';
    CompareFolioOpen($_GET["folio"]);
?>
<form action="func/delete_f_venta.php" autocomplete="off" method="post">
    
    <input type="hidden" id="folio" name="folio" value="'.$row[0].'">
    <input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
    
</form>

<div class="col-lg-12 col-md-6z">
    <a class="button small button-black mb-20" href="/sale_cot.php?folio=<?php echo $_GET["folio"] ?>"><span>Agregar productos</span> </a>
    <a class="button small button-black mb-20" href="/sale_finaly_report_cotizacion.php?folio_sale=<?php echo $_GET["folio"] ?>"><span>Imprimir</span> </a>
    <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#delete"><span>Eliminar</span> </a>
    <a class="button small button-black mb-20" href="/sale_finaly.php?folio=<?php echo $_GET["folio"] ?>"><span>Remisionar</span> </a>
</div>

<?php 
    if ($_GET["folio"])
    {
        echo table_sale_products_finaly_cotizacion($_GET["folio"]); 
    }else{
        echo '<script>location.href = "create_cotizacion.php?pagina=1"</script>';
    }
?>

<!-- End Of Wishlist Area -->
<script>
if (getUrlVars()["delete"])
{
    var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>REMOVIDO!</strong> Producto REMOVIDO con exito.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
if (getUrlVars()["nodelete"])
{
    var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>ERROR!</strong> No fue posible remover el producto, intente de nuevo.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
if (getUrlVars()["update"])
{
    var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>ACTUALIZADO!</strong> Unidades actualizadas.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
if (getUrlVars()["noupdate"])
{
    var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>ERROR!</strong> No fue posible actualizar las unidades, intente de nuevo.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
if (getUrlVars()["nostock"])
{
    var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>ERROR!</strong> No hay existencias.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
</script>
<?php
    include 'func/footer.php';
    if ($_GET["folio"])
    {
        echo table_SalesModal($_GET["folio"]);
    }
?>
        
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