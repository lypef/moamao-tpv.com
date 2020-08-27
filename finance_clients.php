<?php
    include 'func/header.php';
?>
    <div class="section-title-2 text-uppercase mb-40 text-center">
        <h4>SELECCIONE UNA FECHA ESPECIFICA</h4>
    </div>
    <form action="finance_clients.php">

    <div class="row">
            
        <input type="hidden" id="client" name="client" value="<?php echo $_GET["client"]; ?>">
        <div class="col-md-3 text-center">
            <label>Fecha de inicio</label><br>
            <input type="date" id="inicio" name="inicio"
			value="<?php echo $_GET["inicio"]; ?>" style="text-align: center; height:40px; border: 2px solid #D9D7D7;" >
            
        </div>

        <div class="col-md-3 text-center">
            <label>Fecha de finalizacion</label><br>
            <input type="date" id="finaliza" name="finaliza"
			value="<?php echo $_GET["finaliza"]; ?>" style="text-align: center; height:40px; border: 2px solid #D9D7D7;" >
        </div>

        <div class="col-md-3 text-center">
            <label>Seleccione usuario</label><br>
            <select id="usuario" name="usuario" style="text-align: center; height:40px; border: 2px solid #D9D7D7;" >
                    <?php echo Select_Usuarios() ?>
            </select>                                       
        </div>

        <div class="col-md-3 text-center">
            <label>Selecione sucursal</label><br>
            <select id="sucursal" name="sucursal" style="text-align: center; height:40px; border: 2px solid #D9D7D7;" >
                    <?php echo Select_sucursales() ?>
            </select>                                       
        </div>

        
        <div class="col-md-12 text-center">
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
            ">PDF</a>
            
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
            ">XLS</a>
            
        </div>
        <hr>
    <div>
        

    </div>
    </form>
    </div>
    <div class="section-title-2 text-uppercase mb-40 text-center">
        <br>
        <h4>REPORTE DE VENTAS <?php if ($_GET["inicio"]) {echo ': DESDE:'.$_GET["inicio"]; } if ($_GET["finaliza"]) {echo ' | HASTA:'.$_GET["finaliza"]; } ?></h4>
    </div>

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
                <div class="container" style="width:99%; !important">
                    <div class="row">
                        <div class="product-list tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                <?php 
                                    echo table_finance_client($_GET["inicio"],$_GET["finaliza"],$_GET["usuario"], $_GET["sucursal"], $_GET["client"], $_GET["pagina"]);
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
    echo sales_delete_finance_clients($_GET["inicio"],$_GET["finaliza"],$_GET["client"], $_GET["usuario"], $_GET["sucursal"], $_GET["pagina"]);
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