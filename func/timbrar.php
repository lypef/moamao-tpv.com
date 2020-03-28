<?php
// Se desactivan los mensajes de debug
error_reporting(~(E_WARNING|E_NOTICE));
//error_reporting(E_ALL);

// Se especifica la zona horaria
date_default_timezone_set('America/Mexico_City');

require_once 'db.php';

if (ExistFact($_POST['folio']) == false)
{
    //Variables
    $folio = $_POST['folio'];
    $cfdi_f_pago = $_POST['cfdi_f_pago'];
    $cfdi_uso = $_POST['cfdi_uso'];
    $cfdi_tipo = $_POST['cfdi_tipo'];
    $cfdi_m_pago = $_POST['cfdi_m_pago'];
    $cfdi_moneda = $_POST['cfdi_moneda'];
    $cfdi_serie = $_POST['cfdi_serie'];
    $stock = $_POST['stock'];
    
    if ($_POST['remisionar'])
    {
        $remisionar = 1;
    }else
    {
        $remisionar = 0;
    }

    $data = mysqli_query(db_conectar(),"SELECT cfdi_lugare_expedicion, cfdi_rfc, nombre, cfdi_regimen, cfdi_cer, cfdi_key, cfdi_pass FROM `empresa`");

    while($row = mysqli_fetch_array($data))
    {
        $cfdi_lugare_expedicion = $row[0];
        $cfdi_rfc = $row[1];
        $cfdi_nombre = 'GRUPO ASCGAR';
        $cfdi_regimen = $row[3];
        $cfdi_cer = $row[4];
        $cfdi_key = $row[5];
        $cfdi_pass = $row[6];
    }

    $cliente = mysqli_query(db_conectar(),"SELECT c.rfc, c.razon_social, c.correo, c.id, v.descuento FROM folio_venta v, clients c WHERE v.client = c.id and v.folio = '$folio'");

    while($row = mysqli_fetch_array($cliente))
    {
        $cfdi_cliente_rfc = $row[0];
        $cfdi_cliente_r_social = $row[1];
        $cfdi_cliente_correo = $row[2];
        $cfdi_cliente_id = $row[3];
        $venta_descuento = $row[4] / 100;
    }


    // Se incluye el SDK
    require_once 'SDK2/sdk2.php';

    // Se especifica la version de CFDi 3.3
    $datos['version_cfdi'] = '3.3';

    // Ruta del XML Timbrado
    $datos['cfdi']='SDK2/timbrados/'.$folio.'.xml';

    // Ruta del XML de Debug
    $datos['xml_debug']='SDK2/timbrados/'.$folio.'.xml';

    // Credenciales de Timbrado
    $datos['PAC']['usuario'] = $cfdi_rfc;
    $datos['PAC']['pass'] = $cfdi_pass;
    $datos['PAC']['produccion'] = 'SI';

    // Rutas y clave de los CSD
    $datos['conf']['cer'] = $cfdi_cer;
    $datos['conf']['key'] = $cfdi_key;
    $datos['conf']['pass'] = $cfdi_pass;

    // Datos de la Factura
    $datos['factura']['condicionesDePago'] = 'CONDICIONES';
    $datos['factura']['descuento'] = '';
    $datos['factura']['fecha_expedicion'] = date('Y-m-d\TH:i:s', time() - 120);
    $datos['factura']['folio'] = $folio;
    $datos['factura']['forma_pago'] = $cfdi_f_pago;
    $datos['factura']['LugarExpedicion'] = $cfdi_lugare_expedicion;
    $datos['factura']['metodo_pago'] = $cfdi_m_pago;
    $datos['factura']['moneda'] = $cfdi_moneda;
    $datos['factura']['serie'] = $cfdi_serie;
    $datos['factura']['tipocambio'] = 1.00;
    $datos['factura']['tipocomprobante'] = $cfdi_tipo;
    $datos['factura']['RegimenFiscal'] = $cfdi_regimen;

    // Datos del Emisor
    $datos['emisor']['rfc'] = $cfdi_rfc; //RFC DE PRUEBA
    $datos['emisor']['nombre'] = 'GRUPO ASCGAR'; //PRUEBA

    // Datos del Receptor
    $datos['receptor']['rfc'] = $cfdi_cliente_rfc;
    $datos['receptor']['nombre'] = $cfdi_cliente_r_social;
    $datos['receptor']['UsoCFDI'] = $cfdi_uso;

    // Se agregan los conceptos
    
    if ($stock > 0)
    {
        $data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock, _p.cv, _p.um, _p.id, _p.um_des FROM product_venta v, productos _p WHERE v.product = _p.id and v.folio_venta = '$folio' ");
        $genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");    
    }else
    {
        $data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock, _p.cv, _p.um, _p.id, _p.um_des FROM product_pedido v, productos _p WHERE v.product = _p.id and  v.folio_venta = '$folio' ");
		$genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_pedido v WHERE p_generico != '' and folio_venta = '$folio'");
    }
    

    $cont = 0;
    $total = 0;
    $total_iva = 0;
    $log = "";
    while($row = mysqli_fetch_array($data))
    {
        $cont = $cont + 1;

        $PriceAndOff = $row[2] - ($row[2] * $venta_descuento);
        
        $total_tmp = number_format(($row[0] * $PriceAndOff), 2, ".", "");;
        $iva_tmp = number_format( ( ($total_tmp / 1.160000) * 0.160000 ), 2, ".", "");;

        $total = $total +  $total_tmp;
        $total_iva = $total_iva + $iva_tmp;

        $um_des = $row[13];

        if (empty($um_des))
        {
            $um_des = 'NA';
        }

        $datos['conceptos'][$cont]['cantidad'] = $row[0];
        $datos['conceptos'][$cont]['unidad'] = $um_des;
        $datos['conceptos'][$cont]['ID'] = $row[7];
        $datos['conceptos'][$cont]['descripcion'] = $row[1];
        $datos['conceptos'][$cont]['valorunitario'] = number_format($PriceAndOff / 1.160000, 2, ".", "");
        $datos['conceptos'][$cont]['importe'] = number_format($total_tmp / 1.160000, 2, ".", "");
        
        
        $ClaveProdServ = $row[10];
        $ClaveUnidad = $row[11];
        
        if (empty($ClaveProdServ))
        {
            $ClaveProdServ = "01010101";    
        }
        
        if (empty($ClaveUnidad))
        {
            $ClaveUnidad = "ACT";    
        }
        
        $datos['conceptos'][$cont]['ClaveProdServ'] = $ClaveProdServ;
        $datos['conceptos'][$cont]['ClaveUnidad'] = $ClaveUnidad;


        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['Base'] = number_format($total_tmp / 1.160000, 2, ".", "");
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['Impuesto'] = '002';
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['TipoFactor'] = 'Tasa';
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['TasaOCuota'] = '0.160000';
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['Importe'] = $iva_tmp;

    }

    while($row = mysqli_fetch_array($genericos))
    {
        $cont = $cont + 1;

        $PriceAndOff = $row[2] - ($row[2] * $venta_descuento);
        
        $total_tmp = number_format(($row[0] * $PriceAndOff), 2, ".", "");;
        $iva_tmp = number_format( ( ($total_tmp / 1.160000) * 0.160000 ), 2, ".", "");;

        $total = $total +  $total_tmp;
        $total_iva = $total_iva + $iva_tmp;
        
        
        $datos['conceptos'][$cont]['cantidad'] = $row[0];
        $datos['conceptos'][$cont]['unidad'] = 'NA';
        $datos['conceptos'][$cont]['ID'] = $row[3];
        $datos['conceptos'][$cont]['descripcion'] = $row[1];
        $datos['conceptos'][$cont]['valorunitario'] = number_format($PriceAndOff / 1.160000, 2, ".", "");
        $datos['conceptos'][$cont]['importe'] = number_format($total_tmp / 1.160000, 2, ".", "");
        $datos['conceptos'][$cont]['ClaveProdServ'] = '01010101';
        $datos['conceptos'][$cont]['ClaveUnidad'] = 'ACT';


        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['Base'] = number_format($total_tmp / 1.160000, 2, ".", "");
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['Impuesto'] = '002';
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['TipoFactor'] = 'Tasa';
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['TasaOCuota'] = '0.160000';
        $datos['conceptos'][$cont]['Impuestos']['Traslados'][0]['Importe'] = $iva_tmp;

    }

    // Se agregan los Impuestos
    $datos['impuestos']['translados'][0]['impuesto'] = '002';
    $datos['impuestos']['translados'][0]['tasa'] = '0.160000';
    $datos['impuestos']['translados'][0]['importe'] =  number_format($total_iva, 2, ".", "");;
    $datos['impuestos']['translados'][0]['TipoFactor'] = 'Tasa';


    $datos['impuestos']['TotalImpuestosTrasladados'] =  number_format($total_iva, 2, ".", "");;

    //Se agregan totales
    $datos['factura']['subtotal'] = number_format($total - $total_iva, 2, ".", "");;
    $datos['factura']['total'] = number_format($total, 2, ".", "");;

    // Se ejecuta el SDK
    $res = mf_genera_cfdi($datos);


    ///////////    MOSTRAR RESULTADOS DEL ARRAY $res   ///////////
    /*echo "<pre>";
    print_r($datos);
    echo "</pre>";*/

    if ($res["codigo_mf_texto"] == 0)
    {

        echo "<h1>Respuesta Generar XML y Timbrado</h1>";
        foreach ($res AS $variable => $valor) {
            $valor = htmlentities($valor);
            $valor = str_replace('&lt;br/&gt;', '<br/>', $valor);
            echo "<b>[$variable]=</b>$valor<hr>";
        }


        //Generar pdf
        $datosHTML['RESPUESTA_UTF8'] = "SI";
        //MODULO MULTIFACTURAS : CONVIERTE UN XML CFDI A HTML
        $datosHTML['modulo']="cfdi2html";                                                //NOMBRE MODULO
        $datosHTML['rutaxml']='SDK2/timbrados/'.$folio.'.xml';    //RUTA DEL XML CFDI
        $datosHTML['titulo']='<b>'. strtoupper ($cfdi_nombre).'</b>';                                          //TITULO DE FACTURA
        $datosHTML['tipo']="FACTURA";                                                    //TIPO DE FACTURA VENTA,NOMINA,ARRENDAMIENTO, ETC
        $datosHTML['path_logo']="../images/logolola.jpg";                          //RUTA DE LOGOTIPO DE FACTURA
        $datosHTML['notas']="";                                       //NOTA IMPRESA EN FACTURA
        $datosHTML['color_marco']="#013ADF";                                             //COLOR DEL MARCO DE LA FACTURA
        $datosHTML['color_marco_texto']="#FFFFF";                                       //COLOR DEL TEXTO DEL MARCO DE LA FACTURA
        $datosHTML['color_texto']="#0174DF";                                             //COLOR DEL TEXTO EN GENERAL
        $datosHTML['fuente_texto']="arial";                                          //FUENTE DEL TEXTO EN GENERAL


        $res = mf_ejecuta_modulo($datosHTML);                                  //FUNCION QUE CARGA EL MODULO cfdi2html
        $HTML=$res['html'];                                     //HTML DEL XML           //RESPUESTA DE LA FUNCION CARGAR MODULO


        //////////////////////////////////////////////////////////////////////////////
        //CONVERTIR EL HTML DEL XML CFDI A PDF
        $datosPDF['PAC']['usuario'] = "DEMO700101XXX";
        $datosPDF['PAC']['pass'] = "DEMO700101XXX";
        $datosPDF['PAC']['produccion'] = "NO";
        $datosPDF['modulo']="html2pdf";                                                   //NOMBRE MODULO
        $datosPDF['html']="$HTML";                                                        // HTML DE XML CFDI A CONVERTIR A PDF
        $datosPDF['archivo_html']="";                                                     // OPCION SI SE TIENE UN ARCHIVO .HTML       
        $datosPDF['archivo_pdf']='SDK2/timbrados/'.$folio.'.pdf';
        //$datosPDF['archivo_pdf']="RUTA DONDE SE CREARA EL PDF/nombrearhivo.pdf";          //RUTA DONDE SE GUARDARA EL PDF

        $res = mf_ejecuta_modulo($datosPDF);                                    //RESPUESTA DE LA FUNCION CARGAR MODULO  
        //$res = ___html2pdf($datosPDF);                                    //RESPUESTA DE LA FUNCION CARGAR MODULO

        echo "<pre>";
        print_r($res);
        echo $res;
        echo "</pre>";
        
        // ********* Se envia factura por correo
        
        $to = $cfdi_cliente_correo;
        $to .= ',contacto@cyberchoapas.com';
	    $to = str_replace("", ",,", $to);
	    
        $subject = "FACTURA CFDI: " . $cfdi_serie . $folio;
        
        $message = 'ESTIMADO/A '. $cfdi_cliente_r_social .', SE ADJUNTA PDF Y XML DE SU FACTURA VALIDA ANTE EL SAT. <br><br>Fichero XML: <a href="http://www.ascgar.com/func/' . $datosHTML['rutaxml'] . '" target="_blank">Factura XML</a><br><br>Fichero PDF: <a href="http://www.ascgar.com/func/' . $datosPDF['archivo_pdf'].'" target="_blank">Factura PDF</a>';
        
        $mail = MailConfig();
        
        //Email receptor
        $ArrMail = explode(",",$to);
        
        foreach ($ArrMail as $valor) {
            $mail->addAddress($valor);
        }
    
        
        //Asunto
        $mail->Subject = $subject;
      
        $mail->msgHTML(file_get_contents($message), __DIR__);
        //Replace the plain text body with one created manually  
        $mail->Body = $message;
        
        $mail->send();
    
        // ** Finaliza envio de correo
    
        $c = db_conectar();

        mysqli_query($c,"INSERT INTO `facturas` (`serie`, `folio`, `estatus`, `cliente`) VALUES ('$cfdi_serie', '$folio', 'Vigente', '$cfdi_cliente_id');");

        echo mysqli_error($c);

        //Verificar si se remisiona
        if ($remisionar > 0)
        {
            //venta y cotizaciones
            if ($stock == 1)
            {
                $con = db_conectar();  
                $fecha = date("Y-m-d H:i:s");
                $descuento = Sale_Descuento($folio);
                $total = 0;

                $Lproducts = mysqli_query($con,"SELECT product, unidades, precio, product_sub, p_generico FROM `product_venta` where folio_venta = '$folio';");
                while($row = mysqli_fetch_array($Lproducts))
                {
                    if ($row[4] == "")
                    {
                        $total = $total + ($row[1] * $row[2]);
                        if ($row[3])
                        {
                            DescontarProductosStock_hijo($row[3], $row[1]);
                        }else
                        {
                            DescontarProductosStock($row[0], $row[1]);
                        }
                    }
                }

                $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");
                while($row = mysqli_fetch_array($genericos))
                {
                    $total = $total + ($row[0] * $row[2]);
                }
                $total = $total - ($total * ($descuento / 100));
                
                
                mysqli_query($con,"UPDATE `folio_venta` SET `open` = '0', `cotizacion` = '0', `fecha_venta` = '$fecha', `cobrado` = '$total' WHERE folio = $folio;");
            }else
            {
                //pedido
                $con = db_conectar();  
                $total = 0;
                $descuento = Sale_Descuento($folio);
                

                $Lproducts = mysqli_query($con,"SELECT product, unidades, precio, p_generico FROM `product_pedido` where folio_venta = '$folio';");
                while($row = mysqli_fetch_array($Lproducts))
                {
                    if ($row[3] == "")
                    {
                        $total = $total + ($row[1] * $row[2]);
                    }
                }

                $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id FROM product_pedido v WHERE p_generico != '' and folio_venta = '$folio'");
                while($row = mysqli_fetch_array($genericos))
                {
                    $total = $total + ($row[0] * $row[2]);
                }

                $total = $total - ($total * ($descuento / 100));
                
                $abonos = mysqli_query($con,"SELECT cobrado FROM folio_venta WHERE folio_venta_ini = '$folio'");

                while($row = mysqli_fetch_array($abonos))
                {
                    $t_abonos = $t_abonos + $row[0];
                }
                
                $adeudo = $total - $t_abonos;

                
                if ($adeudo <= 0)
                {
                    mysqli_query($con,"UPDATE `folio_venta` SET `open` = '0' WHERE folio = $folio;");
                }
            }
        }
        echo '<script>location.href = "SDK2/timbrados/'.$folio.'.pdf"</script>';
        //Causa conflictos. pro por libreria dublicada
        //SendMailLog($folio);
    }
    }else
    {
        echo 'Este folio ya se encuenta facturado. consulte facturas.';
    }
?>
