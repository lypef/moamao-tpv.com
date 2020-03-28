<?php
    include 'func/header.php';
    ValidateAnnuities();
?>
<div class="col-md-12">
    <?php 
        if ($_GET["search"])
        {
            echo table_annuity($_GET["search"]); 
        }else
        {
            echo table_annuity(""); 
        }
        
    ?>
</div>  
<?php
    include 'func/footer.php';
    if ($_GET["search"])
    {
        echo table_AnnuityModal($_GET["search"]); 
    }else
    {
        echo table_AnnuityModal("");
    }
?>
        
