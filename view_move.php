<?php
    include 'func/header.php'
?>
<form action="view_move.php">
<?php 
    echo '
        <div class="row">
        <div class="col-md-4 text-center">
            <label>Seleccione usuario</label><br>
            <select id="usuario" name="usuario" onchange="load_data()">
                    '.Select_UsuariosCutBox($_GET["usuario"]).'
            </select>                                       
        </div>

        <div class="col-md-4 text-center">
            <label>Selecione sucursal</label><br>
            <select id="sucursal" name="sucursal" onchange="load_data()">
                    '.Select_SucursalesCutBox($_GET["sucursal"]).'
            </select>                                       
        </div>
        <div class="col-md-4 text-center">
            <a href="#" data-toggle="modal" data-target="#cut_z_yes_global" style="
            background-color: #58ACFA;
            width: 100%;
            border: none;
            color: white;
            padding: 16px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px;
            margin: 4px 2px;
            cursor: pointer;
            ">REALIZAR CORTE Z GLOBAL</a>
        </div>
    </div>
    </form>
        ';
        
    echo view_move($_GET["usuario"], $_GET["sucursal"]);
?>
<script>
    function load_data() {
        location.href = "/view_move.php?usuario="+document.getElementById("usuario").value+"&sucursal="+document.getElementById("sucursal").value;
    }
</script>
<?php
    include 'func/footer.php';
?>
        
