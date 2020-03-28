<?php
    include 'func/header.php';
?>
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.common.min.css"/>
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.rtl.min.css"/>
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.silver.min.css"/>
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.2.620/styles/kendo.mobile.all.min.css"/>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="/kendo/kendo.all.min.js"></script>
  
    <div class="section-title-2 text-uppercase mb-40 text-center">
        <h4>SELECCIONE UNA FECHA ESPECIFICA O UN PRODUCTO</h4>
    </div>
    <form action="finance_product.php">

    <div class="row" style="padding: 20px;">
            
        <div class="col-md-2 text-center">
            <label>Fecha de inicio</label><br>
            <input id="datepicker0" name="inicio">
        </div>

        <div class="col-md-3 text-center">
            <label>Fecha de finalizacion</label><br>
            <input id="datepicker1" name="finaliza">
        </div>
        
        <div class="col-md-3 text-center">
            <label>Buscar producto</label><br>
            <form action="finance_product.php" autocomplete="off">
                <input type="text" placeholder="No. parte" name="search" autocomplete="off" style="height:45px">
            </form>
        </div>
            
        <div class="col-md-2 text-center">
            <label>Seleccione producto</label><br>
            <select id="product" name="product">
                    <?php echo Select_productsFinance_Products($_GET["search"]) ?>
            </select>                                       
        </div>
        
        <div class="col-md-2 text-left">
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
        
    </form>
    </div>
    
<script id="cell-template" type="text/x-kendo-template">
    <span class="#= isInArray(data.date, data.dates) ? 'party' : '' #">#= data.value #</span>
</script>

<script>
  var fecha = new Date();

  $("#datepicker0").kendoDatePicker({
    value: new Date(),
    month: {
      content: $("#cell-template").html()
    }
  });

  $("#datepicker1").kendoDatePicker({
    value: new Date(),
    month: {
      content: $("#cell-template").html()
    },
    dates: [
      new Date(2000, 10, 10),
      new Date(2000, 10, 30)
    ] //can manipulate month template depending on this array.
  });

  function isInArray(date, dates) {
    for(var idx = 0, length = dates.length; idx < length; idx++) {
      var d = dates[idx];
      if (date.getFullYear() == d.getFullYear() &&
          date.getMonth() == d.getMonth() &&
          date.getDate() == d.getDate()) {
        return true;
      }
    }

    return false;
  }

    if (getUrlVars()["product"])
    {
        document.getElementById("product").value = getUrlVars()["product"];
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
                                <div id="areaImprimir">    
                                <?php 
                                    echo table_finance_product($_GET["inicio"],$_GET["finaliza"],$_GET["product"]);
                                ?>
                                </div>
                                <center>
                                <a href="report_products_gen.php?inicio=<?php echo $_GET["inicio"]?>&finaliza=<?php echo $_GET["finaliza"]?>&product=<?php echo $_GET["product"]?>"style="
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
                                ">Generar Pdf</a>
                                <a href="report_products_gen_xls.php?inicio=<?php echo $_GET["inicio"]?>&finaliza=<?php echo $_GET["finaliza"]?>&product=<?php echo $_GET["product"]?>"style="
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
                                ">Generar Xls</a>
                                </center>
                            </div>
                        </div>
                </div>
            </div>
        </section>
        <!-- End page content -->
<?php
    include 'func/footer.php';
?>
        
