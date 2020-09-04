<?php
    include 'db.php';
    db_sessionValidarNO();
    session_start();
	
	if ($_SESSION['token'] == GetToken())
	{

		$id = $_GET['id'];
		$concepto = $_GET['concepto'];
		$price = $_GET['price'];
		$client = $_GET['client'];
		$lastpay = $_GET['lastpay'];
		
		$message = 'Apreciable: <b>'.$client.'</b>, le notificamos que su anualidad: <b>'.$concepto.'</b> esta vencida o proxima a vencer, ya que su ultimo pago se registro el dia: <b>'.$lastpay.'</b>, esperando no causarle ninguna molestia si no todo lo contrario nos despedimos de usted quedando a la espera de su pronto pago para evitar demoras o suspenciones en su servicio.';

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
							padding: 0 15px;
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
						<div class="opps-reminder">Recordatorio de pago</div>
						<div class="opps-info">
							<div class="opps-ammount">
								<h4>Monto a pagar</h4>
										<h2>$ '.number_format($price,2,".",",").' <sup>MXN</sup></h2>
										<p><center><b>'.numtoletras($price).'</b></center></p>
									</div>
								</div>
							</div>
							<span><center><br><br>'.$message.'<br><br></center></span>
					</p>
					<div class="opps-instructions">
								<h2>Instrucciones</h2>
								<ol>
									<li>Elija opcion de pago <a href="https://docs.google.com/document/d/1sAfwi1dGMLck4KXnpdhF4e4_XHYj4L4YnErFkgvIxXY/edit" target="_blank">SELECCIONE AQUI</a>.</li>
									<li>Realice el pago correspondiente con tranferencia o en efectivo.</li>
									<li>Responda este correo con su ficha de pago o envielo por <a href="https://api.whatsapp.com/send?phone=5219231200505&text=&source=&data=" target="_blank">whatsapp</a>.</li>
									<li>Al confirmar su pago, le entregaran un comprobante impreso o digital segun sea el caso. <strong>En se podra verificar que se haya realizado correctamente.</strong> Conserva este comprobante de pago.</li>
									<li>Al completar estos pasos recibiras un correo de <strong>CLTA D & D</strong> confirmando tu pago e iniciando logistica.</li>
								</ol>
								<div class="opps-footnote">GRUPO ASCGAR</div>
							</div>
						</div>	
					</body>
				</html>
		';
		

		$asunto = 'ANUALIDAD';
		
		$mail = MailConfig();
		
		//Email receptor
		$mail_receptor = ReturnEmailClientAnnuities($id);
		$mail_receptor = $mail_receptor . ',contacto@cyberchoapas.com';
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
		
		if ($r)
		{
				echo '<script>location.href = "/annuity.php?sendmail=true"</script>';
		}
		else
		{
			echo '<script>location.href = "/annuity.php?nosendmail=true"</script>';
		}
	}
?>