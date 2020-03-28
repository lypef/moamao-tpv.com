<?php
    include 'func/header.php';
    if ($_SESSION['sucursal_gest'] == 0)
    {
        echo '<script>location.href = "products.php?pagina=1"</script>';
    }
?>
<div class="col-md-12">
    <div class="table-responsive compare-wraper mt-30">
        <table class="cart table">
            <thead>
                <tr>
                    <th class="table-head th-name uppercase">NOMBRE</th>
                    <th class="table-head item-nam">DIRECCION</th>
                    <th class="table-head item-nam">TELEFONO</th>
                    <th class="table-head item-nam">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php echo table_sucursales(); ?>
            </tbody>
        </table>
    </div>
    <hr>


    <form id="contact-form" action="func/sucursal_add.php" method="post" autocomplete="off">
            <div class="col-md-12">
                <div class="section-title text-uppercase mb-40">
                    <h4>Agregar sucursal</h4>
                </div>
            </div>

            <div class="row">
            
                <div class="col-md-3">
                    <label>Nombre de sucursal</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre de sucursal" required>
                </div>
                
                <div class="col-md-3">
                    <label>Direccion de sucursal</label>
                    <input type="text" name="direccion" id="direccion" placeholder="Direccion">
                </div>

                <div class="col-md-3">
                    <label>Telefono de sucursal</label>
                    <input type="text" name="telefono" id="telefono" placeholder="Telefono">
                </div>
                
                <div class="col-md-3">
                    <label>Serie CFDI</label>
                    <input type="text" name="cfdi_serie" id="cfdi_serie" placeholder="Serie cfdi">
                </div>

                <div class="country-select shop-select col-md-12">
                    <button class="submit-btn mt-20" type="submit">Guardar</button>
                </div>

            </div>
      </form>




</div>  
<script>
    if (getUrlVars()["update_sucursal"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ACTUALIZADO!</strong> La sucursal se actualizo con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["noupdate_sucursal"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> La sucursal no se actualizo.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["add"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO! </strong> La sucursal se agrego con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["noadd"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> La sucursal no se agrego.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["delete_sucursal"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ELIMINADO!</strong> La sucursal se elimino con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["nodelete_sucursal"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> La sucursal no se elimino.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
    echo table_SucursalModal();
?>
        
