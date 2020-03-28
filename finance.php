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
        <h4>SELECCIONE UNA FECHA ESPECIFICA</h4>
    </div>
    <form action="finance.php">

    <div class="row">
            
        <div class="col-md-3 text-center">
            <label>Fecha de inicio</label><br>
            <input id="datepicker0" name="inicio">
        </div>

        <div class="col-md-3 text-center">
            <label>Fecha de finalizacion</label><br>
            <input id="datepicker1" name="finaliza">
        </div>

        <div class="col-md-3 text-center">
            <input id="folio" name="folio" value="<?php echo $_GET["folio"] ?>" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              " placeholder="Ingrese folio">
        </div>

        <div class="col-md-3 text-center">
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
            ">Buscar</button>
            
            <a href="report_pdf_sales.php?inicio=<?php echo $_GET["inicio"]?>&finaliza=<?php echo $_GET["finaliza"]?>&folio=<?php echo $_GET["folio"]?>&usuario=<?php echo $_GET["usuario"]?>&sucursal=<?php echo $_GET["sucursal"]?>"style="
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
            ">PDF</a>
            
            <a href="report_xls_sales.php?inicio=<?php echo $_GET["inicio"]?>&finaliza=<?php echo $_GET["finaliza"]?>&folio=<?php echo $_GET["folio"]?>&usuario=<?php echo $_GET["usuario"]?>&sucursal=<?php echo $_GET["sucursal"]?>"style="
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
            ">XLS</a>
            
        </div>
        <hr>
    <div>
        <div class="col-md-6 text-center">
            <label>Seleccione usuario</label><br>
            <select id="usuario" name="usuario">
                    <?php echo Select_Usuarios() ?>
            </select>                                       
        </div>

        <div class="col-md-6 text-left">
            <label>Selecione sucursal</label><br>
            <select id="sucursal" name="sucursal">
                    <?php echo Select_sucursales() ?>
            </select>                                       
        </div>
    </div>
    </form>
    </div>
    <div class="section-title-2 text-uppercase mb-40 text-center">
        <br>
        <h4>REPORTE DE VENTAS <?php if ($_GET["inicio"]) {echo ': DESDE:'.$_GET["inicio"]; } if ($_GET["finaliza"]) {echo ' | HASTA:'.$_GET["finaliza"]; } ?></h4>
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
                <div class="container" style="width:99%; !important">
                    <div class="row">
                        <div class="product-list tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                <?php 
                                    echo table_finance($_GET["inicio"],$_GET["finaliza"],$_GET["folio"], $_GET["usuario"], $_GET["sucursal"]);
                                ?>
                                <center>
                                <a href=""style="
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
                                " data-toggle="modal" data-target="#pagar_comision">PAGAR COMISION</a>
                                </center>
                            </div>
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
        
<!--Pagar comision-->
    <div class="modal fade" id="pagar_comision" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">PAGAR COMISION</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <span>A continuacion se realizara el pago de las comisiones y se marcaran como pagadas, solamente de el usuario seleccionado.</span>
            <form action="/func/pagar_comisiones.php" method="post">
        </div>
        <div class="modal-footer">
                <input type="hidden" name="user" id="user" value="<?php echo $_GET['usuario'] ?>">
                <input type="hidden" id="url" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
                <button type="button" name="no" id="no" class="btn btn-info" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-warning">Pagar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Finaliza pagar comision-->