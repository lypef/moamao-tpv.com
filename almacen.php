<?php
    include 'func/header.php';
    if ($_SESSION['almacen_guest'] == 0)
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
                    <th class="table-head item-nam">UBICACION</th>
                    <th class="table-head item-nam">TELEFONO</th>
                    <th class="table-head item-nam">OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php echo table_almacen(); ?>
            </tbody>
        </table>
    </div>
</div>  
<script>
    if (getUrlVars()["delete_departament"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ELIMINADO!</strong> El departamento y sus productos fueron eliminado con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["update_departament"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ACTUALIZADO!</strong> El departamento se actualizo con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["noupdate_departament"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El departamento no se actualizo.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["nodelete_departament"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> El departamento fue eliminado.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["update_almacen"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ACTUALIZADO!</strong> El almacen se actualizo con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["noupdate_almacen"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El almacen no se actualizo.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["delete_almacen"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ELIMINADO!</strong> El almacen se elimino con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["nodelete_almacen"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El almacen no se elimino.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
    echo table_almacenModal();
?>
        
