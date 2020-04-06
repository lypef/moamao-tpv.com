<?php
    include 'db.php';
    db_sessionValidarNO();
    
    $folio = $_POST['folio'];
    $url = $_POST['url'];
    $cfdi_cliente_correo = $_POST['cfdi_cliente_correo'];
    $cfdi_serie = $_POST['cfdi_serie'];

    // Phpmail
    
    $url = str_replace("&sendmail=true","",$url);
    $url = str_replace("?sendmail=true","",$url);
    $url = str_replace("&nosendmail=true","",$url);
    $url = str_replace("?nosendmail=true","",$url);

    
    $message = 'SE REENVIA PDF Y XML DE SU FACTURA VALIDA ANTE EL SAT. <br><br>Fichero XML: <a href="https://www.ascgar.com/func/SDK2/timbrados/' . $folio . '.xml" target="_blank">Factura XML</a><br><br>Fichero PDF: <a href="https://www.ascgar.com/func/SDK2/timbrados/' . $folio . '.pdf" target="_blank">Factura PDF</a>';
    
    //$message = $message . '<br><br><b>Si no puede acceder a el enlace, ingrese manualmente aqui.</b><br>' . $current_url.'/sale_finaly_report_cotizacion.php?folio_sale='.$folio;

    $asunto = 'FACTURA CFDI: '. $folio;
    
    
    $mail = MailConfig();
    
    //Email receptor
    $ArrMail = explode(",",$cfdi_cliente_correo);
    
    foreach ($ArrMail as $valor) {
        $mail->addAddress($valor);
    }

    
    //Asunto
    $mail->Subject = $asunto;
  
    $mail->msgHTML(file_get_contents($message), __DIR__);
    //Replace the plain text body with one created manually  
    $mail->Body = $message;
    
    $r = $mail->send();
    
    
    $addpregunta = false;

    for($i=0;$i<strlen($url);$i++)
    {
        if ($url[$i] == "?")
        {
            $addpregunta = true;
        }
    }

    if ($addpregunta)
    {
        if ($r)
        {
            echo '<script>location.href = "'.$url.'&sendmail=true"</script>';
        }else {echo '<script>location.href = "'.$url.'&nosendmail=true"</script>';}
    }else
    {
        if ($r)
        {
            echo '<script>location.href = "'.$url.'?sendmail=true"</script>';
        }else {echo '<script>location.href = "'.$url.'?nosendmail=true"</script>';
            
        }
    }
?>
