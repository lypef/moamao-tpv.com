<?php
    require_once 'func/db.php';
    require_once("dompdf/dompdf_config.inc.php");
    session_start();

    $vendedor = $_SESSION['users_id'];
    $v = $_SESSION['users_id'];
    $con = db_conectar();  
    $sales = mysqli_query($con,"SELECT u.nombre, c.nombre, v.descuento, v.fecha, v.cobrado, v.fecha_venta, v.folio, v.t_pago FROM folio_venta v, users u, clients c WHERE v.vendedor = u.id and v.client = c.id and v.open = 0 and v.cut = 0 and v.vendedor = '$vendedor'");


    while($row = mysqli_fetch_array($sales))
    {
        if ($row[7] == "efectivo")
        {
            $efectivo = $efectivo + $row[4];
        }
        elseif ($row[7] == "transferencia")
        {
            $transferencia = $transferencia + $row[4];
        }
        elseif ($row[7] == "tarjeta")
        {
            $cheque = $cheque + $row[4];
        }
            
        $vendedor = $row[0];
        $total = $total + $row[4];

        $logs = $logs . '
        </tr>
        <tr>
        <td>'.$row[1].'</td>
        <td><center>'.$row[2].' %</center></td>
        <td><center>'.$row[6].'</center></td>
        <td><center>'.$row[3].'</center></td>
        <td><center>$ '.$row[4].' MXN</center></td>
        <td class="item-des"><center><p>'.strtoupper($row[7]).'</p></center></td>
        </tr>
        ';
    }

    
    $codigoHTML='
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h3><center>'.$_SESSION['empresa_direccion'].'</center></h3>
    <h3><center>MAIL: '.$_SESSION['empresa_correo'].' | TEL: '.$_SESSION['empresa_telefono'].'</center></h3>
    <hr>
    <h4><center>CORTE Z USUARIO: '.$_SESSION['users_nombre'].'</center></h4>
    <p>FECHA Y HORA DE GENERACION: '.date("Y-m-d H:i:s").'</p>
    <hr>
    <table style="width:100%">
        <tr>
        <th>CLIENTE</th> 
        <th>DESCUENTO</th> 
        <th>FOLIO</th> 
        <th>FECHA DE VENTA</th>
        <th>COBRADO</th>
        <th class="table-head th-name">M. PAGO</th>
        </tr>
        '.$logs.'
    </table>
    <br><br>
    <br><br>
    <div align="right">';
    
    if ($efectivo > 0)
		{
			$codigoHTML .= '
			<h5>Efectivo: $ '.number_format($efectivo,2,".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$codigoHTML .= '
			<h5>Tranferencia: $ '.number_format($transferencia,2,".",",").' MXN</h5>
			';
		}

		if ($cheque > 0)
		{
			$codigoHTML .= '
			<h5>Tarjeta: $ '.number_format($cheque,2,".",",").' MXN</h5>
			';
		}
    
    $codigoHTML .= '<h3>TOTAL RECAUDADO: $ '.number_format($total,2,".",",").' MXN</h3>
    </div>
    <br>
    <footer>
      <center><p>CLTA DESARROLLO & DISTRIBUCION DE SOFTWARE<br><a href="http://www.cyberchoapas.com"> www.cyberchoapas.com</a></p></center>
    </footer>
    ';
    
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('letter', '');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("CorteZ".$vendedor.date("YmdHis").".pdf");

    mysqli_query($con,"UPDATE `folio_venta` SET `cut` = '1' WHERE open = 0 and vendedor = '$v';");
?>