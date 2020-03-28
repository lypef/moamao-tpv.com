<?php
    include 'func/header.php';
?>
<div class="col-md-12">
    <form class="header-search-box" action="facturas.php">
        <div class="col-md-8">
            <input type="text" placeholder="Ingrese folio o nombre de cliente." name="search" autocomplete="off">
        </div>
        <div class="col-md-4">
            <button class="submit-btn" type="submit">Buscar</button>
        </div>
    </form>
</div>

<div class="col-md-12">
    <?php 
        if ($_GET["pagina"])
        {
            echo table_facturas($_GET["pagina"]); 
        }
        if ($_GET["search"])
        {
            echo table_facturas_search($_GET["search"]); 
        }
    ?>
</div>  
<script>
    if (getUrlVars()["send_mail"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ELIMINADO!</strong> Correo enviado.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
</script>
<?php
    include 'func/footer.php';
    echo table_facturas_options_modal(); 
?>
        
