<?php
    include 'func/header.php'
?>
<form action="view_move.php">
<?php 
    if ($_SESSION['finanzas'] == 1)
    {
        echo '
        <div class="row">
        <div class="col-md-4 text-center">
            <label>Seleccione usuario</label><br>
            <select id="usuario" name="usuario">
                    '.Select_Usuarios().'
            </select>                                       
        </div>

        <div class="col-md-4 text-center">
            <label>Selecione sucursal</label><br>
            <select id="sucursal" name="sucursal">
                    '.Select_sucursales().'
            </select>                                       
        </div>
        
        <div class="col-md-4 text-center">
            <br><button type="submit" class="btn btn-primary">Consultar</button>
        </div>

    </div>
    </form>
        ';
    }
    echo view_move($_GET["usuario"], $_GET["sucursal"]); 
?>
<script>
    document.getElementById("usuario").value = "<?php echo $_GET["usuario"] ?>";
    document.getElementById("sucursal").value = "<?php echo $_GET["sucursal"] ?>";
    document.getElementById("t_pago").value = "<?php echo $_GET["t_pago"] ?>";
</script>
<?php
    include 'func/footer.php';
?>
        
