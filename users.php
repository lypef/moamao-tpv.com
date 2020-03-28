<?php
    include 'func/header.php';
    if ($_SESSION['usuarios'] == 0)
    {
        echo '<script>location.href = "products.php?pagina=1"</script>';
    }
?>

<body>

<div class="col-md-12">
    <?php 
        echo table_users(); 
    ?>
</div>  
<br>
<hr>


<script>
    if (getUrlVars()["delete"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ELIMINADO!</strong> El usuario se elimino con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["nodelete"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El usuario no se elimino.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<?php
    include 'func/footer.php';
    echo table_UsersModal();
?>