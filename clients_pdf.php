<?php
    require_once 'func/db.php';
    
    // Dompdf php 7
    //require_once 'dompdf_php7.1/autoload.inc.php';
    //use Dompdf\Dompdf;

    // Dompdf php 5
    require_once("dompdf_php5.6/dompdf_config.inc.php");
    
    session_start();
    
    $con = db_conectar();  
    
    $sales = mysqli_query($con,"SELECT id, nombre, if (direccion = '', 'DESCONOCIDO', direccion) as direccion, if (telefono = '', 'DESCONOCIDO', telefono) as telefono, if (rfc = '', 'DESCONOCIDO', rfc) as rfc, if (razon_social = '', 'DESCONOCIDO', razon_social) as razon_social, if (correo = '', 'DESCONOCIDO', correo) as correo FROM `clients` ORDER BY nombre ASC");

    
    $body = '';
    while($row = mysqli_fetch_array($sales))
    {
        $body = $body.'
        <tr>
        
        <td><p>'.$row[1].'</p></td>
        <td><p>'.$row[2].'</p></td>
        <td><p>'.$row[3].'</p></td>
        <td><p>'.$row[4].'</p></td>
        <td><p>'.$row[5].'</p></td>
        <td><p>'.$row[6].'</p></td>
        
        </tr>
        ';
    }
    
    $codigoHTML='
    
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h4><center>LISTA DE CLIENTES</center></h4>
    <hr width="50%">
    <table border="1">
        <tr>
        <th>NOMBRE</th>
        <th>DIRECCION</th>
        <th>TELEFONO</th>
        <th>RFC</th>
        <th>R. SOCIAL</th>
        <th>CORREO</th>
        </tr>
        '.$body.'
    </table>
    <br><br>
    <br>
    ';
    
//    echo $codigoHTML;
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('legal', 'landscape');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("reporte_productos.pdf");
?>