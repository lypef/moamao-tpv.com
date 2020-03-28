<?php
    require_once 'db.php';
    
    $url = $_POST['url'];

    $asunto = $_POST['asunto'];

    $url = str_replace("&sendmail=true","",$url);
    $url = str_replace("?sendmail=true","",$url);
    $url = str_replace("&nosendmail=true","",$url);
    $url = str_replace("?nosendmail=true","",$url);

    $mail_receptor = $_POST['mail_cliente'];
    
    $body = $_POST['body_msg'];

    $mail = MailConfig();
    
    //Email receptor
    $ArrMail = explode(",",$mail_receptor);
    
    foreach ($ArrMail as $valor) {
        $mail->addAddress($valor);
    }

    
    //Asunto
    $mail->Subject = $asunto;
  
    $mail->msgHTML(file_get_contents($body), __DIR__);
    //Replace the plain text body with one created manually  
    $mail->Body = $body;
    
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