<?php
    include 'func/header.php';
?>
<div class="col-md-12">
    <div class="row">
        <form action="g_compra.php">
            <div class="col-md-3 text-left">
                <label>Seleccione almacen</label><br>
                <select id="almacen" name="almacen">
                        <?php echo Select_Almacen_ALL() ?>
                </select>                                       
            </div>

            <div class="col-md-3 text-left">
                <label>Selecione marca</label><br>
                <select id="marca" name="marca">
                        <?php echo Select_Marca() ?>
                </select>                                       
            </div>

            <div class="col-md-3 text-left">
                <label>Selecione proveedor</label><br>
                <select id="proveedor" name="proveedor">
                        <?php echo Select_Proveedor() ?>
                </select>                                       
            </div>

            <div class="col-md-3 text-left">
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
    <br>
    <div id="areaImprimir">    
        <?php  
            if (!$_GET["almacen"] && !$_GET["marca"] && !$_GET["proveedor"])
            {
                echo g_orden_compra_todos($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if ($_GET["almacen"] > 0 && !$_GET["marca"] && !$_GET["proveedor"])
            {
                echo g_orden_compra_almacen($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if (!$_GET["almacen"] && $_GET["marca"] && !$_GET["proveedor"])
            {
                echo g_orden_compra_marca($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if (!$_GET["almacen"] && !$_GET["marca"] && $_GET["proveedor"])
            {
                echo g_orden_compra_proveedor($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if ($_GET["almacen"] && $_GET["marca"] && !$_GET["proveedor"])
            {
                echo g_orden_compra_AlmacenMarca($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if ($_GET["almacen"] && !$_GET["marca"] && $_GET["proveedor"])
            {
                echo g_orden_compra_AlmacenProveedor($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if (!$_GET["almacen"] && $_GET["marca"] && $_GET["proveedor"])
            {
                echo g_orden_compra_MarcaProveedor($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }
            if ($_GET["almacen"] && $_GET["marca"] && $_GET["proveedor"])
            {
                echo g_orden_compra_AlmacenMarcaProveedor($_GET["almacen"], $_GET["marca"], $_GET["proveedor"]);
            }        
         ?>
    </div>
</div>  

<div align="right">
    <a class="button large mb-20" onclick="printDiv('areaImprimir')"><span>Imprimir</span> </a>
</div>


<script>
function printDiv(nombreDiv) {
     var contenido= document.getElementById(nombreDiv).innerHTML;
     var contenidoOriginal= document.body.innerHTML;

     document.body.innerHTML = contenido;

     window.print();

     document.body.innerHTML = contenidoOriginal;
}
</script>
<?php
    include 'func/footer.php';
?>
        
