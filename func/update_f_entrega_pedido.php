<?php
    include 'db.php';
    db_sessionValidarNO();
    
    if ($_SESSION['token'] == GetToken())
    {
        $fecha = $_POST['fecha'];
        $folio = $_POST['folio'];
        $body = str_replace("%f_pedido%",'<a href="'.GetDominio().'/sale_finaly_report_orderprint.php?folio_sale='.$folio.'">'.$folio.'</a>',str_replace("0:00:00","",str_replace("%f_entrega%",GetFechaText($fecha),$_POST['body_msg'])));
        $email = $_POST['email'];
        
        $con = db_conectar();  

        mysqli_query($con,"UPDATE `folio_venta` SET `f_entrega` = '$fecha' WHERE `folio_venta`.`folio` = '$folio'; ");

        if (!mysqli_error($con))
        {
            //Enviar email si existe un correo electronico
            if (!empty($email))
            {
                $mail = MailConfig();
    
                //Email receptor
                $ArrMail = explode(",",$email);
                
                foreach ($ArrMail as $valor) {
                    $mail->addAddress($valor);
                }

                
                //Asunto
                $mail->Subject = 'Actualizacion fecha de entrega';
            
                $mail->msgHTML(file_get_contents($body), __DIR__);
                //Replace the plain text body with one created manually  
                $mail->Body = $body;
                
                $mail->send();
            } // Finaliza envio de correo

            echo '<script>location.href = "/orders.php?proceso_yes=1&search='.$folio.'"</script>';
        }else
        {
            echo '<script>location.href = "/orders.php?proceso_no=1&search='.$folio.'"</script>';
        }
    }
?>