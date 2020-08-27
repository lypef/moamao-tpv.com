<?php
    
    require_once 'db.php';
    
    $url = 'https://' .$_POST['url_web'] . $_POST['url'];
    $url = remove_url_query_args($url,array("Cont_MailSend","Cont_MailNoSend"));
    
    $asunto = $_POST['header'];
    
    
    $txtxtra = $_POST['txtxtra'];
    
    if (!empty($txtxtra))
    {
        $txtxtra = '<h1 style="text-align: center;"><span style="color: #ff0000; background-color: #000000;"><strong>'.strtoupper($txtxtra).'</strong></span></h1><br>';
    }
    
    $Cont_MailSend = 0;
    $Cont_MailNoSend = 0;
    
    $folios = mysqli_query(db_conectar(),'SELECT f.folio, c.nombre, c.correo  FROM folio_venta f, clients c WHERE f.open = 1 and f.pedido = 0 and f.cotizacion = 1 and f.client = c.id and c.correo != ""');
    
    $mail = MailConfig();
        
    while($item = mysqli_fetch_array($folios))
    {
        $formato = ""; $folio = $item[0]; $cliente = $item[1]; $correo = $item[2];
        
        $total_pagar = Return_TotalPagar_Folio($folio);
        
        //Email receptor
        $ArrMail = explode(",",$correo);
        
        foreach ($ArrMail as $valor) {
            $mail->addAddress($valor);
        }
    
        if (empty($asunto))
        {
            $mail->Subject = 'COTIZACION: ' . $folio;
        }else
        {
            $mail->Subject = $asunto;
        }
        
        
        $formato = 
        '
        <html>
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					
					
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
						padding: 0 45px;
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
				'.$txtxtra.'
				<div class="opps">
				<div class="opps-header">
					<div class="opps-reminder">Ficha digital. No es necesario imprimir.</div>
					<div class="opps-info">
						<div class="opps-ammount">
							<h4>Monto a pagar</h4>
									<h2>$ '.number_format($total_pagar,GetNumberDecimales(),".",",").' <sup>MXN</sup></h2>
									<p>'.numtoletras($total_pagar).'</p>
								</div>
							</div>
							<div class="opps-reference">
							<h4>FOLIO</h4>
					<h3><a href="https://www.ascgar.com/sale_finaly_report_cotizacion.php?folio_sale='.$folio.'" target="_blank">'.$folio.'</a></h3>
								</div>
						</div>
                  		<span>
                  		    <center>
                  		        <br>
                          		APRECIABLE <b>'.$cliente.'</b>. SE ADJUNTA COTIZACION VIGENTE, <a href="https://www.ascgar.com/sale_finaly_report_cotizacion.php?folio_sale='.$folio.'" target="_blank">VER DOCUMENTO</a>
                          		<br><br>
                      		</center>
                  		</span>
                  </p>
						<div class="opps-instructions">
							<h2>Instrucciones</h2>
							<ol>
								<li>Eliga opcion de pago <a href="https://docs.google.com/document/d/1sAfwi1dGMLck4KXnpdhF4e4_XHYj4L4YnErFkgvIxXY/edit" target="_blank">SELECCIONE AQUI</a>.</li>
								<li>Realice el pago correspondiente con tranferencia o en efectivo.</li>
								<li>Responda este correo con su ficha de pago o envielo por <a href="https://api.whatsapp.com/send?phone=5219231200505&text=&source=&data=" target="_blank">whatsapp</a>.</li>
								<li>Al confirmar su pago, le entregaran un comprobante impreso o digital segun sea el caso. <strong>En se podra verificar que se haya realizado correctamente.</strong> Conserva este comprobante de pago.</li>
							</ol>
							<div class="opps-footnote">Al completar estos pasos recibiras un correo de <strong>CLTA D & D</strong> confirmando tu pago e iniciando logistica.</div>
						</div>
					</div>	
					<p style="text-align: right;"><a title="Eliminar cot" href="https://www.ascgar.com/sale_finaly_report_cotizacion_delete.php?folio_sale='.$folio.'"><span style="color: #800000;"><strong>Eliminar cotizacion</strong></span></a></p>
				</body>
			</html>
        ';
        
        $mail->msgHTML(file_get_contents($formato), __DIR__);
        //Replace the plain text body with one created manually  
        $mail->Body = $formato;
        
        if ($mail->send())
        {
            $Cont_MailSend ++;
    
        }else 
        {
            $Cont_MailNoSend ++;
        }
        $mail->ClearAddresses();  
    }
    
    
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
        echo '<script>location.href = "'.$url.'&Cont_MailSend='.$Cont_MailSend.'&Cont_MailNoSend='.$Cont_MailNoSend.'"</script>';
    }else
    {
        echo '<script>location.href = "'.$url.'?Cont_MailSend='.$Cont_MailSend.'&Cont_MailNoSend='.$Cont_MailNoSend.'"</script>';
    }
?>