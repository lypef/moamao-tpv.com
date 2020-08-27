<?php
    require_once 'db.php';
    db_sessionValidarNO();
    
    if ($_SESSION['token'] == GetToken())
    {
        $url = $_POST['url'];
        $link = $_POST['link'];
    
        $header = $_POST['header'];
    
        $url = str_replace("&sendmail=true","",$url);
        $url = str_replace("?sendmail=true","",$url);
        $url = str_replace("&nosendmail=true","",$url);
        $url = str_replace("?nosendmail=true","",$url);
    
        $current_url = $_POST['url_web']; 
    
        $mail_receptor = $_POST['mail'];
        
        $body = $_POST['body'];
        
        $folio = $_POST['folio'];
        
        $message = $_POST['body'];
        
        // Copiar y subir titulo
    	if ($_FILES["titulo"]["name"])
    	{
    		$ruta_img = 'titulos/'.$folio.'.pdf';
            $img_access = '../'.$ruta_img;
    		
    		//unlink($img_access);
    
    		copy($_FILES["titulo"]["tmp_name"], $img_access );
    		mysqli_query(db_conectar(),"UPDATE `folio_venta` SET `titulo` = '$ruta_img' WHERE `folio_venta`.`folio` = '$folio';");
    	}
    	// Finaliza Copiar y subir titulo
    
        if ($txtxtra.length > 0)
        {
            $txtxtra .= '<br>'; 
        }
        
        $formato = 
        '
        <html>
    				<head>
    					<meta charset="utf-8">
    					<meta charset="ISO-8859-1">
    					<link href="styles.css" media="all" rel="stylesheet" type="text/css" />
    					<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
    					<style>
    					/* Reset -------------------------------------------------------------------- */
    					* 	 { margin: 0;padding: 0; }
    					body { font-size: 14px; }
    			
    					/* OPPS --------------------------------------------------------------------- */
    			
    					h4 {
    						margin-bottom: 8px;
    						font-size: 12px;
    						font-weight: 600;
    						text-transform: uppercase;
    					}
    			
    					.opps {
    						width: 100%; 
    						border-radius: 4px;
    						box-sizing: border-box;
    						padding: 0 0px;
    						margin: 40px auto;
    						overflow: hidden;
    						border: 1px solid #b0afb5;
    						font-family: "Open Sans", sans-serif;
    						color: #4f5365;
    					}
    			
    					.opps-reminder {
    						position: relative;
    						top: -1px;
    						padding: 9px 0 10px;
    						font-size: 11px;
    						text-transform: uppercase;
    						text-align: center;
    						color: #ffffff;
    						background: #000000;
    					}
    			
    					.opps-info {
    						margin-top: 26px;
    						position: relative;
    					}
    			
    					.opps-info:after {
    						visibility: hidden;
    						display: block;
    						font-size: 0;
    						content: " ";
    						clear: both;
    						height: 0;
    			
    					}
    			
    					.opps-ammount {
    						width: 100%;
    						float: right;
    					}
    			
    					.opps-ammount h2 {
    						font-size: 36px;
    						color: #000000;
    						line-height: 24px;
    						margin-bottom: 15px;
    					}
    			
    					.opps-ammount h2 sup {
    						font-size: 16px;
    						position: relative;
    						top: -2px
    					}
    			
    					.opps-ammount p {
    						font-size: 10px;
    						line-height: 14px;
    					}
    			
    					.opps-reference {
    						margin-top: 14px;
    					}
    			
    					h3 {
    						font-size: 15px;
    						color: #000000;
    						text-align: center;
    						margin-top: -1px;
    						padding: 6px 0 7px;
    						border: 1px solid #b0afb5;
    						border-radius: 4px;
    						background: #f8f9fa;
    					}
    			
    					.opps-instructions {
    						margin: 32px -45px 0;
    						padding: 32px 45px 45px;
    						border-top: 1px solid #b0afb5;
    						background: #f8f9fa;
    					}
    			
    					ol {
    						margin: 14px 0 0 13px;
    					}
    			
    					li + li {
    						margin-top: 8px;
    						color: #000000;
    					}
    			
    					a {
    						color: #1155cc;
    					}
    			
    					.opps-footnote {
    						margin-top: 22px;
    						padding: 22px 20 24px;
    						color: #108f30;
    						text-align: center;
    						border: 1px solid #108f30;
    						border-radius: 4px;
    						background: #ffffff;
    					}
    			</style>
    				</head>
    				<body>
    				<div class="opps">
    				<div class="opps-header">
    					<div class="opps-reminder">GRUPO ASCGAR</div>
    						</div>
                      		<span><center><br>'.$message.'<br><br></center></span>
                      </p>
    						<div class="opps-instructions">
    							<h2>Instrucciones !</h2>
    							<ol>
    								<li>Descargue sistema, <a href="'.$link.'" target="_blank"> AQUI</a>.</li>
                                  <li>Visualice videos de como se instala, <a href="https://www.youtube.com/channel/UCyGopyJoASFYL6uulromDwg/playlists" target="_blank"> VER AQUI</a>.</li>
                                  <li>Instale e introduca licencia: '.$folio.'.</li><br>
    								<hr>
                                  <br><h2>Algun problema ?</h2>
    							<ol>
    								<li>Soporte tecnico <a href="mailto:soporte@cyberchoapas.com" target="_blank">Enviar correo</a></li>
    								<li>Contacto xpres por <a href="https://api.whatsapp.com/send?phone=5219231200505&text=&source=&data=" target="_blank">whatsapp</a></li>
    							</ol>
    							</ol>
    							<div class="opps-footnote"> <strong>LICENCIA: </strong>'.$folio.'</div>
    						</div>
    					</div>	
    				</body>
    			</html>
        ';
        
        //$message = $message . '<br><br><b>Si no puede acceder a el enlace, ingrese manualmente aqui.</b><br>' . $current_url.'/sale_finaly_report_cotizacion.php?folio_sale='.$folio;
    
        $asunto = $_POST['header'];
        
        $folio = $_POST['folio'];
        
        $mail = MailConfig();
        
        //Email receptor
        $mail_receptor .= ','.static_empresa_email();
        $ArrMail = explode(",",$mail_receptor);
        
        foreach ($ArrMail as $valor) {
            $mail->addAddress($valor);
        }
    
        
        //Asunto
        $mail->Subject = $asunto;
      
        $mail->msgHTML(file_get_contents($formato), __DIR__);
        //Replace the plain text body with one created manually  
        $mail->Body = $formato;
        
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
    }
?>