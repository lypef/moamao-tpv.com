<?php
    include 'func/header.php';
?>
<div class="col-md-12">
<form class="header-search-box" action="facturas.php">
			<div>
				<input type="hidden" id="pagina" name="pagina" value="1">
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              " value = "<?php echo $_GET["search"]; ?>">
			</div>
		</form>
</div>

<div class="col-md-12">
    <?php 
        
        if ($_GET["pagina"] && $_GET["search"])
        {
            echo table_facturas_search($_GET["search"], $_GET["pagina"]); 
        }
        elseif ($_GET["pagina"])
        {
            echo table_facturas($_GET["pagina"]); 
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
    if ($_GET["pagina"] && $_GET["search"])
    {
        echo table_facturas_options_modal_Search($_GET["search"], $_GET["pagina"]); 
    }
    elseif ($_GET["pagina"])
    {
        echo table_facturas_options_modal($_GET["pagina"]); 
    }
?>
        
