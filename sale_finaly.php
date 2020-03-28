<?php
    include 'func/header.php';
    CompareFolioOpen($_GET["folio"]);
?>

<?php 
    if ($_GET["folio"])
    {
        echo table_sale_products_finaly_($_GET["folio"]); 
    }else{
        echo '<script>location.href = "create_sale.php?pagina=1"</script>';
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

<div class="modal fade" id="success_sale" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">FINALIZAR VENTA ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Al finalizar la venta, el sistema disminuira las existencias de cada producto agregado y posteriomente tomara la sumatoria como un ingreso.</p>
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