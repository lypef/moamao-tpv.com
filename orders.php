<?php
    include 'func/header.php';
?>

<body>

<div class="col-md-12">
    <?php 
        if ($_GET["search"])
        {
            echo table_orders_search($_GET["search"]); 
        }else
        {
            echo table_orders(); 
        }
    ?>
</div>  
<br>
<hr>


<script>
    if (getUrlVars()["sale_finaly"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Finalizado!</strong> El pedido se finalizo con exito";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["abono"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO!</strong> Abono realizado correctamente.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

</script>
<?php
    include 'func/footer.php';
    echo table_orders_modal();
    if ($_GET["pay"])
    {
        echo '<script>location.href = "sale_finaly_report_order.php?folio='.$_GET["pay"].'"</script>';
    }
?>