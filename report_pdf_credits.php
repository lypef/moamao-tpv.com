<?php
    require_once 'func/db.php';
    // Dompdf php 7
    //require_once 'dompdf_php7.1/autoload.inc.php';
    //use Dompdf\Dompdf;

    // Dompdf php 5
    require_once("dompdf_php5.6/dompdf_config.inc.php");
    session_start();
    
    $client = $_GET["client"];
    $sucursal = $_GET["sucursal"];
    $nombre_cliente = "CLIENTE: TODOS LOS CLIENTES";

    if ($client > 0)
    {
        if ($sucursal > 0)
        {
            $data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id  and c.client =  '$client' and c.sucursal = '$sucursal' ORDER by  f_vencimiento asc");
        }else
        {
            $data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id  and c.client =  '$client' ORDER by  f_vencimiento asc");
        }
    }else{
        if ($sucursal > 0)
        {
            $data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id and c.pay = 0 and c.sucursal = '$sucursal' ORDER by  f_vencimiento asc");
        }else
        {
            $data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id and c.pay = 0 ORDER by  f_vencimiento asc");
        }
    }

    $num_total_registros = mysqli_num_rows($data);

    //Variables a detalle 
    $plus_lastID = 0;
    $plus_last_client = "";
    $plus_contador = 0;
    $plus_total = 0;
    $plus_cont = 0;
    $plus_total_g = 0;
        
    $body = '';
    while($row = mysqli_fetch_array($data))
    {
        if ($plus_lastID != $row[10] && $plus_lastID != 0)
        {
            
            $body = $body.'
            <tr>
                <td><b>CLIENTE</b></td>
                <td><b>TOTAL CREDITOS</b></td>
                <td><b>CREDITO TOTAL</b></td>
                <td><b>ADEUDO PENDIENTE</b></td>
            </tr>

            <tr>
                <td><i>'.$plus_last_client.'</i></td>
                <td><i>'.$plus_contador.' CREDITOS</i></td>
                <td><i>$ '.number_format($plus_total_g,GetNumberDecimales(),".",",").' MXN</i></td>
                <td><i>$ '.number_format($plus_total,GetNumberDecimales(),".",",").' MXN</i></td>
            </tr>


            <tr>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
                <td><hr></td>
            </tr>

            <tr>
                <td><b>NOMBRE</b></td>
                <td><b>F. VENCIMIENTO</b></td>
                <td><b>DIAS_RESTANTES</b></td>
                <td><b>FACTURA</b></td>
                <td><center><b>CREDITO_TOTAL</b></center></td>
                <td><center><b>PENDIENTE_DE_PAGO</b></center></td>
            </tr>
            ';
            $plus_contador = 0;
            $plus_total = 0;
            $plus_total_g = 0;
        }

        $font = "";
        
        $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
        $fecha_db = strtotime($row[3]);
        
        if($fecha_actual > $fecha_db)
        {
            $font = 'style="color: red;"';
        }
        
        if ($row[7] <= 0)
        {
            $font = 'style="color: blue;"';
        }

        $body = $body.'
            <tr>
            <td class="item-des" '.$font.' >'.$row[1].'</td>
            <td class="item-des" '.$font.' >'.GetFechaText($row[3]).'</td>
            <td class="item-des" '.$font.' ><center>'.$row[8].' DIAS</center></td>
            <td class="item-des" '.$font.' ><a href="http://'.$_SERVER['HTTP_HOST'].'/sale_finaly_report_cotizacion.php?folio_sale='.$row[4].'"><center>'.$row[4].'</center></a></td>
            <td class="item-des" '.$font.' ><center>$ '.number_format($row[5],GetNumberDecimales(),".",",").' MXN</center></td>
            <td class="item-des" '.$font.' ><center>$ '.number_format($row[7],GetNumberDecimales(),".",",").' MXN</center></td>
            </tr>
            ';
        
        if ($row[11] > 0)
        {
            $body = $body.'
            <tr>
                <td></td>
                <td><b>ABONO: </b> '.number_format($row[11],GetNumberDecimales(),".",",").' MXN</td>
                <td><b>FECHA: </b> '.GetFechaText($row[3]).'</td>
            </tr>
            ';
        }
        
        $data_log = mysqli_query(db_conectar(),"SELECT monto, fecha FROM `credit_pay` WHERE credito = $row[0]");
        while($log = mysqli_fetch_array($data_log))
        {
            $body = $body.'
            <tr>
                <td></td>
                <td><b>ABONO: </b> '.number_format($log[0],GetNumberDecimales(),".",",").' MXN</td>
                <td><b>FECHA: </b> '.GetFechaText($log[1]).'</td>
            </tr>
            ';
        }

        $total = $total + $row[7];


        $plus_lastID = $row[10];
        $plus_last_client = $row[1];
        $plus_contador ++;
        $plus_total = $plus_total + $row[7];
        $plus_total_g = $plus_total_g + $row[5];
        $plus_cont ++;

        //Ultimo
        if ($plus_cont == $TAMANO_PAGINA || $plus_cont == $num_total_registros)
        {
            
            $body = $body.'
            <tr>
                <td><b>CLIENTE</b></td>
                <td><b>TOTAL CREDITOS</b></td>
                <td><b>CREDITO TOTAL</b></td>
                <td><b>ADEUDO PENDIENTE</b></td>
            </tr>

            <tr>
                <td><i>'.$plus_last_client.'</i></td>
                <td><i>'.$plus_contador.' CREDITOS</i></td>
                <td><i>$ '.number_format($plus_total_g,GetNumberDecimales(),".",",").' MXN</i></td>
                <td><i>$ '.number_format($plus_total,GetNumberDecimales(),".",",").' MXN</i></td>
            </tr>
            ';
            $plus_contador = 0;
            $plus_total = 0;
            $plus_total_g = 0;
        }
    }
    
    $codigoHTML='
    <h1><center>'.$_SESSION['empresa_nombre'].'</center></h1>
    <h3><center>'.$_SESSION['empresa_direccion'].'</center></h3>
    <h3><center>MAIL: '.$_SESSION['empresa_correo'].' | TEL: '.$_SESSION['empresa_telefono'].'</center></h3>
    <h4><center>HISTORIA DE CREDITOS: '.$nombre_cliente.'</center></h4>
    <hr>
    <br><br>
    <table style="width:100%">
        <tr>
        <th class="table-head th-name uppercase">NOMBRE</th>
            <th class="table-head th-name uppercase">F. VENCIMIENTO</th>
            <th class="table-head th-name uppercase">DIAS_RESTANTES</th>
            <th class="table-head th-name uppercase">FACTURA</th>
            <th class="table-head th-name uppercase">CREDITO_TOTAL</th>
            <th class="table-head th-name uppercase">PENDIENTE_DE_PAGO</th>
        </tr>
        '.$body.'
    </table>
    
    <br>
    <div align="right">';
    
    $codigoHTML .= '<h3>TOTAL ADEUDO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h3>
    </div>
    <br>
    <footer>
      <center><p>CLTA DESARROLLO & DISTRIBUCION DE SOFTWARE<br><a href="https://www.cyberchoapas.com"> www.cyberchoapas.com</a></p></center>
    </footer>
    ';
    
    $codigoHTML = mb_convert_encoding($codigoHTML, 'HTML-ENTITIES', 'UTF-8');
    $dompdf=new DOMPDF();
    $dompdf->set_paper('legal', 'landscape');
    $dompdf->load_html($codigoHTML);
    ini_set("memory_limit","128M");
    $dompdf->render();
    $dompdf->stream("ListCreditsActivos.pdf");
?>