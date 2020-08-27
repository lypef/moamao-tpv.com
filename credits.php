<?php
    include 'func/header.php';
?>

<!-- Start page content -->
        <section id="page-content" class="page-wrapper">
            <!-- Start Product List -->
            <div class="product-list-tab">
                <div class="container" style="width:99%; !important">
                    <div class="row">
                        <div class="product-list tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                <?php 
                                    echo table_credits($_GET["client"], $_GET["sucursal"]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End page content -->
    
<?php
    include 'func/footer.php';
    echo sales_delete_credits($_GET["client"], $_GET["sucursal"]);
?>
<script>
function loadclient() {
  var client = document.getElementById("select_client").value;
  var suc = document.getElementById("select_sucursal").value;
  location.href = "/credits.php?client="+client+"&sucursal="+suc;
}
</script>