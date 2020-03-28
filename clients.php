<?php
    include 'func/header.php';
?>
<div class="col-md-12">
    <?php 
        if ($_GET["pagina"])
        {
            echo table_clientes($_GET["pagina"]); 
        }
        if ($_GET["search"])
        {
            echo table_clientes_search($_GET["search"]); 
        }
    ?>
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

    if (getUrlVars()["update"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ACTUALIZADO!</strong> El cliente se actualizo con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["noupdate"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El cliente no se actualizo.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
    if ($_GET["pagina"])
    {
        echo table_ClientesModal($_GET["pagina"]);
    }
    if ($_GET["search"])
    {
        echo table_ClientesModal_search($_GET["search"]); 
    }
?>
        
