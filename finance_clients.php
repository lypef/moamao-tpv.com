<?php
    include 'func/header.php';
?>
    <form action="finance_clients.php">

    <div class="row">
        <input type="hidden" name="client" id="client" value="<?php echo $_GET["client"] ?>">    
        <div class="col-md-3 text-center">
            <label>Seleccione usuario</label><br>
            <select id="usuario" name="usuario">
                    <?php echo Select_Usuarios() ?>
            </select>                                       
        </div>

        <div class="col-md-3 text-center">
            <label>Selecione sucursal</label><br>
            <select id="sucursal" name="sucursal">
                    <?php echo Select_sucursales() ?>
            </select>                                       
        </div>

        <div class="col-md-6 text-center">
            

        <div class="col-md-6 text-right">
            <button type="submit" style="
            background-color: #58ACFA;
            border: none;
            color: white;
            padding: 18px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px;
            margin: 4px 2px;
            cursor: pointer;
            ">Consultar</button>
        </div>    
        
        <div class="col-md-6 text-left">
            <a href="report_pdf_sales_clients.php?inicio=<?php echo $_GET["inicio"]?>&finaliza=<?php echo $_GET["finaliza"]?>&client=<?php echo $_GET["client"]?>&usuario=<?php echo $_GET["usuario"]?>&sucursal=<?php echo $_GET["sucursal"]?>"style="
            background-color: #58ACFA;
            border: none;
            color: white;
            padding: 18px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px;
            margin: 4px 2px;
            cursor: pointer;
            ">IMP. PDF</a>
        </div>    
        </div>
    </form>
    </div>

<script id="cell-template" type="text/x-kendo-template">
    <span class="#= isInArray(data.date, data.dates) ? 'party' : '' #">#= data.value #</span>
</script>

<script>
    if (getUrlVars()["usuario"])
    {
        document.getElementById("usuario").value = getUrlVars()["usuario"];
    }

    if (getUrlVars()["sucursal"])
    {
        document.getElementById("sucursal").value = getUrlVars()["sucursal"];
    }
</script>

<!-- Start page content -->
        <section id="page-content" class="page-wrapper">
            <!-- Start Product List -->
            <div class="product-list-tab">
                <div class="row" style="padding: 20px;">
                        <div class="product-list tab-content">
                            <div class="section-title-2 text-uppercase mb-40 text-center">
                                <h4>HISTORIA DE VENTAS</h4>
                            </div>
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                <?php 
                                    echo table_finance_client($_GET["inicio"],$_GET["finaliza"], $_GET["usuario"], $_GET["sucursal"],$_GET["client"]);
                                ?>
                                <center>
                                <a href="report_xls_sales_client.php?inicio=<?php echo $_GET["inicio"]?>&finaliza=<?php echo $_GET["finaliza"]?>&client=<?php echo $_GET["client"]?>&usuario=<?php echo $_GET["usuario"]?>&sucursal=<?php echo $_GET["sucursal"]?>"style="
                                background-color: #58ACFA;
                                border: none;
                                color: white;
                                padding: 18px 10px;
                                text-align: center;
                                text-decoration: none;
                                display: inline-block;
                                font-size: 20px;
                                margin: 4px 2px;
                                cursor: pointer;
                                ">Generar xls</a>
                                </center>
                            </div>
                        </div>
                    </div>
                
            </div>
        </section>
        <!-- End page content -->
<?php
    include 'func/footer.php';
    echo sales_delete_finance();
?>
        
