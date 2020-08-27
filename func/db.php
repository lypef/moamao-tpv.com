<?php

	function db_conectar ()
	{
		$host = "localhost";
		$user = "moamaotp_user";
		$password = ";kdT#AD=wjNT";
		$db = "moamaotp_store";
		$coneccion = new mysqli($host,$user,$password,$db);
		mysqli_query($coneccion, "SET NAMES 'utf8'");
		return $coneccion;
	}


	function urlWhatsapp ()
	{
		return 'https://wa.me/4144444';
	}
	
	function GetDominio ()
	{
		return 'http://www.url.mx';
	}
	
	function GetNumberDecimales ()
	{
		return 2;
	}
	
	function static_empresa_nombre ()
	{
		return "Grupo Ascgar";
	}

	function static_empresa_email()
	{
		return "contacto@cyberchoapas.com";
	}
	
	function ColorBarrReport ()
	{
		return "#9c0003 ";
	}

	function DesglosarReportIva ()
	{
		return true;
	}

	function Ticket ()
	{
		return false;
	}

	function ReportCotTranfers ()
	{
		return '';
	}

	function CheckCredit ($id, $folio)
	{
		$con = db_conectar();  
		
		$data = mysqli_query($con,"SELECT adeudo, (abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = $id) ) AS adeudo FROM credits WHERE id = $id");
		if ($row = mysqli_fetch_array($data))
	    {
			if ($row[1] >= $row[0])
			{
				mysqli_query($con,"UPDATE `credits` SET `pay` = '1' WHERE `credits`.`id` = $id ;");
				
				if (is_numeric($folio))
				{
					//Finalizar venta
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

					if (!mysqli_error($con))
					{
						SendMailLog($folio, true);   
					}
					// Finaliza finalizar venta
				}
			}
	    }
	}


	function CheckCreditExistCotizacion ($folio)
	{
		$b = false;

		$con = db_conectar();  
		
		$data = mysqli_query($con,"SELECT id from credits WHERE factura = '$folio'");
		if ($row = mysqli_fetch_array($data))
	    {
			$b = true;
		}
		
		return $b;
	}

	function GetOxxoPayFolio ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT oxxo_pay FROM `folio_venta` where folio= $folio");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}

	function GetOxxoPayFolioUpdate ($folio, $ref)
	{
		mysqli_query(db_conectar(),"UPDATE `folio_venta` SET `oxxo_pay` = '$ref' WHERE `folio_venta`.`folio` = '$folio';");
	}
	
	function GetOxxoPayFolioEmail ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT c.correo FROM folio_venta v, clients c WHERE v.client = c.id and v.folio = $folio");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        if (strlen($row[0]) > 2)
			{
				$body = $row[0];
			}else
			{
				$body = static_empresa_email();
			}
	    }
		return $body;
	}
	
	function GetOxxoPayRefEmail ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT c.correo FROM folio_venta v, clients c WHERE v.client = c.id and v.oxxo_pay = $folio");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}
	
	function GetOxxoPayFolioVenta ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT v.folio FROM folio_venta v, clients c WHERE v.client = c.id and v.oxxo_pay = $folio");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}
	
	function GetOxxoPayFolioTel ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT c.telefono FROM folio_venta v, clients c WHERE v.client = c.id and folio = $folio");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        if (strlen($row[0]) > 9)
			{
				$body = "+52" . $row[0];
			}else
			{
				$body = "+529231200505";
			}
			
	    }
		return $body;
	}

    function GetOxxoPayFolioCliente ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT c.nombre FROM folio_venta v, clients c WHERE v.client = c.id and folio = $folio");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}
	
	function GenRefOxxo ($total, $folio)
	{
		$FolExist = GetOxxoPayFolio($folio);
		if ($FolExist == "0")
		{
			$r_informacion = 0; $r_promo_nego = 0; $referencia = "";
			
			require_once('./oxxo_pay/lib/Conekta.php');
			\Conekta\Conekta::setApiKey("key_");
			\Conekta\Conekta::setApiVersion("2.0.0");

			try{
			
			$order = \Conekta\Order::create(
				array(
					"line_items" => array(
					array(
						"name" => "Pago correspondiente al folio: " . $folio,
						"unit_price" => number_format($total,GetNumberDecimales(),"",""),
						"quantity" => 1
					)),
					"currency" => "MXN",
					"customer_info" => array(
					"name" => GetOxxoPayFolioCliente($folio),
					"email" => GetOxxoPayFolioEmail($folio),
					"phone" => GetOxxoPayFolioTel($folio)
					),
					"charges" => array(
						array(
							"payment_method" => array(
							"type" => "oxxo_cash"
							)
						)
					)
				)
				);
				
				$referencia = $order->charges[0]->payment_method->reference;
				GetOxxoPayFolioUpdate($folio,$referencia);
				// Return
				$var = str_split($referencia, 4);
				$r = "";
				
				for($i=0; $i<count($var); $i++)
				{
				  $r .= $var[$i] . "-";
			   }
			  
				return substr($r, 0, -1);
				
			} catch (\Conekta\ParameterValidationError $error)
			{
				return $error->getMessage();
			} catch (\Conekta\Handler $error)
			{
				return $error->getMessage();
			}
		}else
		{
			// Return fol ya existe
			$var = str_split($FolExist, 4);
			$r = "";
			
			for($i=0; $i<count($var); $i++)
			{
			  $r .= $var[$i] . "-";
		   }
		  
			return substr($r, 0, -1);
		}
	}

	function ReturnImgLogo ()
	{
		return 'images/logolola.jpg';
	}
	
	function db_sessionValidarYES ()
	{
		session_start();
  		if (isset($_SESSION['users_id'])){ echo '<script>location.href = "products.php"</script>';}
	}

	function db_sessionValidarNO ()
	{
		session_start();
  		if (isset($_SESSION['users_id']) == false){ echo '<script>location.href = "/index.php"</script>';}
	}

	function db_sessionDestroy ()
	{
		session_start();
		session_destroy();
		echo '<script>location.href = "/"</script>';
	}

	function db_sessionDestroy_login ()
	{
		session_start();
		session_destroy();
		echo '<script>location.href = "/login.php"</script>';
	}

	function AddLog($contenido)
	{
		session_start();
	  	$userid = $_SESSION['usuario'];
		$contenido = strtoupper($contenido);
		$date_time = date("Y-m-d H:i:s");
		mysql_query("insert into logs (user, fecha, registro) values ('$userid', '$date_time', '$contenido')");
	}

	function DescontarProductosStock ($id, $unidades)
	{
		mysqli_query(db_conectar(),"UPDATE `productos` SET stock = stock - '$unidades' WHERE id = $id;");
	}

	function DescontarProductosStock_hijo ($id, $unidades)
	{
		mysqli_query(db_conectar(),"UPDATE `productos_sub` SET stock = stock - '$unidades' WHERE id = $id;");
	}

	function returnproducts ($departamento)
	{
		//Regesamos los ultimos 2 productos agregados
		$data = mysqli_query(db_conectar(),"SELECT p.id, p.nombre, p.foto0, p.oferta, p.precio_normal, p.precio_oferta FROM productos p WHERE p.departamento = '$departamento' ORDER by p.id desc LIMIT 0, 2 ");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $precio = "";
	        $oferta = "";
	        if ($row[3] == 1)
	        {
	        	$precio = $row[5];
	        	$oferta = "<strong style='color:#FF0000';>OFERTA!</strong> ";
	        }else
	        {
	        	$precio = $row[4];
	        }

	        $body = $body.'<li><a href="products_detail.php?id='.$row[0].'"><img src = "images/'.$row[2].'" style="
	        	height: 40px;
			    width: 40px;
			    background-repeat: no-repeat;
			    background-position: 50%;
			    border-radius: 50%;
			    background-size: 100% auto;
			    "> '.$oferta.substr($row[1],0,28).' ... <strong>$ '.$precio.'</strong></a></li>';
	    }
		return $body;
	}

	function ReturnProductsOferta ()
	{
		//Regesamos los ultimos 2 productos agregados
		$data = mysqli_query(db_conectar(),"SELECT p.id, p.nombre, p.foto0, p.oferta, p.precio_normal, p.precio_oferta FROM productos p WHERE p.oferta = 1 ORDER by p.id desc LIMIT 0, 24");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body. "<ul class='single-mega-item'>";
	        $oferta = "<strong style='color:#1E90FF';>OFERTA!</strong> ";
	        $body = $body.'<li><a href="products_detail.php?id='.$row[0].'"><img src = "images/'.$row[2].'" style="
	        	height: 40px;
			    width: 40px;
			    background-repeat: no-repeat;
			    background-position: 50%;
			    border-radius: 50%;
			    background-size: 100% auto;
			    "> '.$oferta.substr($row[1],0,28).' ... <strong style=color:#FF0000;>$ '.$row[5].'</strong> antes $'.$row[4].'</a></li>';
		    $body = $body."</ul>";
	    }
		return $body;
	}

	function ReturnNewsProductsList()
	{
		$data = mysqli_query(db_conectar(),"SELECT p.id, p.nombre, p.foto0, p.oferta, p.precio_normal, p.precio_oferta FROM productos p ORDER by p.id desc LIMIT 0, 4");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $precio = "";
	        $oferta = "";
	        if ($row[3] == 1)
	        {
	        	$precio = $row[5];
	        	$oferta = "<strong style='color:#FF0000';>OFERTA!</strong> ";
	        }else
	        {
	        	$precio = $row[4];
	        }

	        $body = $body.'<li><a href="products_detail.php?id='.$row[0].'" target="_blank"><img src = "images/'.$row[2].'" style="
	        	height: 60px;
			    width: 60px;
			    background-repeat: no-repeat;
			    background-position: 50%;
			    border-radius: 50%;
			    background-size: 100% auto;
			    "> '.$oferta.substr($row[1],0,40).' ... <strong>$ '.$precio.'</strong></a></li>';
	    }
		return $body;
	}

	function Almacen_ubicacion_p_sub ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT a.nombre , p.ubicacion FROM productos_sub p, almacen a where p.almacen = a.id and p.id = $id ");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = substr($row[0],0,3) . ', ' . $row[1];
	    }
		return $body;
	}
	
	function Select_Almacen ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM almacen ORDER by nombre asc");
		$body = "<option value='0'>LISTA DE ALMACENES</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function GetAlmacen ($sucursal)
	{
		$data = mysqli_query(db_conectar(),"SELECT a.nombre, sa.id FROM sucursal_almacen sa, almacen a, sucursales s WHERE sa.sucursal = s.id and sa.almacen = a.id and sa.sucursal = '$sucursal' ;");
		$body = "<ul>";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'<li><b> * </b>'.$row[0].'  | <a href="/func/suc_alm_delete.php?id='.$row[1].'">Eliminar</a></li>';
		}

		$body = $body . "</ul>";

		return $body;
	}

	function validateFolioVenta ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT pedido FROM folio_venta WHERE folio = '$folio'");
		while($row = mysqli_fetch_array($data))
		{
			$value = $row[0];
		}
		if ($value)
		{
			echo '<script>location.href = "/sale_order.php?pagina=1?folio='.$folio.'"</script>';
		}
	}

	function validateFolioVentaBack ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT pedido FROM folio_venta WHERE folio = '$folio'");
		while($row = mysqli_fetch_array($data))
		{
			$value = $row[0];
		}
		if (!$value)
		{
			echo '<script>location.href = "/products.php?pagina=1"</script>';
		}
	}

	function validateFolioVentaBack_cot ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT cotizacion FROM folio_venta WHERE folio = '$folio'");
		while($row = mysqli_fetch_array($data))
		{
			$value = $row[0];
		}
		if (!$value)
		{
			echo '<script>location.href = "/products.php?pagina=1"</script>';
		}
	}

	function Select_Almacen_cero ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM almacen ORDER by nombre asc");
		$body = "<option value=''>LISTA DE ALMACENES</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function Select_Almacen_ALL ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM almacen ORDER by nombre asc");
		$body = "<option value=''>TODOS LOS ALMACENES</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function Select_Marca ()
	{
		$data = mysqli_query(db_conectar(),"SELECT DISTINCT marca FROM `productos` ORDER BY marca ASC");
		$body = "<option value=''>TODAS LAS MARCAS</option>";
		while($row = mysqli_fetch_array($data))
	    {
			if ($row[0])
			{
				$body = $body.'<option value="'.$row[0].'">'.$row[0].'</option>';
			}
	    }
		return $body;
	}

	function Select_Proveedor ()
	{
		$data = mysqli_query(db_conectar(),"SELECT DISTINCT proveedor FROM `productos` ORDER BY proveedor ASC");
		$body = "<option value=''>TODOS LOS PROVEEDORES</option>";
		while($row = mysqli_fetch_array($data))
	    {
			if ($row[0])
			{
				$body = $body.'<option value="'.$row[0].'">'.$row[0].'</option>';
			}
	    }
		return $body;
	}

	function Return_NombreAlmacen ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM almacen where id = $id ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}

	function Return_NombreClient ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM clients where id = $id ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}
	
	function Return_ExistRelationsAnnuity ($client)
	{
		$r = false;
		
		$data = mysqli_query(db_conectar(),"SELECT id FROM `annuities` where client = $client ");
		if($row = mysqli_fetch_array($data))
	    {
			$r = true;
	    }
		return $r;
	}
	
	function Return_ExistRelationsSale ($client)
	{
		$r = false;
		
		$data = mysqli_query(db_conectar(),"SELECT * FROM folio_venta where cobrado > 0.0 and client = $client ");
		if($row = mysqli_fetch_array($data))
	    {
			$r = true;
	    }
		return $r;
	}
	
	function Return_NombreUser ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM users where id = $id ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}

	function Return_NombreSucursal ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM sucursales where id = $id");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}
	
	function Return_SueldoUser ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT sueldo FROM users where id = $id ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}

	function Return_NombreProduct ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM productos where id = $id ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}
	
	function ReturnNameAnnuity ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT client FROM `annuities` WHERE id =  $id ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}
	
	function ReturnEmailClientAnnuities ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT c.correo FROM annuities a, clients c where a.client = c.id and a.id = '$id' ");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}

	function Select_products ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, `no. De parte` FROM productos ORDER by nombre asc");
		$body = "<option value='0'>LISTA DE PRODUCTOS</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].' | NO. PARTE: '.$row[2].'</option>';
	    }
		return $body;
	}

	function Select_productsFinance_Products ($txt)
	{
		$con = db_conectar();

		$data = mysqli_query($con,"SELECT id, nombre, `no. De parte` FROM productos where nombre like '%$txt%' or `no. De parte` like '%$txt%'  ORDER by nombre asc");
		$body = "<option value='0'>LISTA DE PRODUCTOS</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].' | NO. PARTE: '.$row[2].'</option>';
		}
		
		return $body;
	}
	
	function Select_Usuarios ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM users ORDER by nombre asc");
		$body = "<option value='0'>LISTA DE USUARIOS</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function Select_UsuariosCutBox ($user)
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM users ORDER by nombre asc");
		$body = "<option value='0' selected>TODOS LOS USUARIOS</option>";

		while($row = mysqli_fetch_array($data))
	    {
			if ($row[0] == $user)
			{
				$body = $body.'<option value='.$row[0].' selected>'.$row[1].'</option>';
			}else
			{
				$body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
			}
			
	    }
		return $body;
	}

	function Select_SucursalesCutBox ($sucursal)
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM sucursales ORDER by nombre asc");
		$body = "<option value='0' selected>TODAS LAS SUCURSALES</option>";

		while($row = mysqli_fetch_array($data))
	    {
			if ($row[0] == $sucursal)
			{
				$body = $body.'<option value='.$row[0].' selected>'.$row[1].'</option>';
			}else
			{
				$body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
			}
		}
		
		return $body;
	}

	function Select_clients ($client)
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM `clients` order by nombre asc");
		$body = "<option value='0'>TODOS LOS CLIENTES</option>";
		while($row = mysqli_fetch_array($data))
	    {
			if ($client == $row[0])
			{
				$body = $body.'<option value='.$row[0].' selected>'.$row[1].'</option>';
			}else
			{
				$body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
			}
	    }
		return $body;
	}

	function Select_sucursales ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM sucursales ORDER by nombre asc");
		$body = "<option value='0'>LISTA DE SUCURSALES</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function Select_sucursales_selected ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM sucursales ORDER by nombre asc");
		$body = "<option value='0'>LISTA DE SUCURSALES</option>";
		while($row = mysqli_fetch_array($data))
	    {
			if ($id == $row[0])
			{
				$body = $body.'<option value='.$row[0].' selected>'.$row[1].'</option>';
			}else
			{
				$body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
			}

	    }
		return $body;
	}

	function Select_sucursales_Add_user ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM sucursales ORDER by nombre asc");
		$body = "<option value=''>LISTA DE SUCURSALES</option>";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function Select_Departamento ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre FROM departamentos ORDER by nombre asc");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $body.'<option value='.$row[0].'>'.$row[1].'</option>';
	    }
		return $body;
	}

	function GetFilterAlmacen ($sucursal)
	{
		$data = mysqli_query(db_conectar(),"SELECT almacen FROM sucursal_almacen where sucursal = '$sucursal';");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body .= ' almacen = '.$row[0].' or';
	    }
		return $body;
	}

	function _getProducts ($pagina)
	{
		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		if (isset($_SESSION['sucursal']))
		{
		    $c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		    $c_s = str_replace("almacen","s.almacen",$c);
		    $c_p = str_replace("almacen","p.almacen",$c);
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p )");
		}else 
		{
			$data = mysqli_query(db_conectar(),"SELECT nombre, stock, oferta, precio_normal, precio_oferta, foto0, foto1, foto2, foto3, id, `no. De parte` FROM productos order by id asc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT id FROM productos");
		}
		

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		$body = '<div class="row">
					<div class="col-md-12">
						<div class="section-title-2 text-uppercase mb-40 text-center">
							<h4>LISTA DE PRODUCTOS</h4>
						</div>
					</div>
				</div>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
		  if ($login)
		  {
			if ($_SESSION['product_gest'] == 1)
			{
				$icons_edit = 
				'<a href="#" title="Edicion rapida" data-toggle="modal" data-target="#edit_flash'.$row[9].'">
					<i class="zmdi zmdi-flash"></i>
				</a>
				<a href="/products_edit.php?id='.$row[9].'&url='.$_SERVER['REQUEST_URI'].'" title="Editar">
					<i class="zmdi zmdi-edit"></i>
				</a>
				';
			}else {$icons_edit = '';}

		  }

		    $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[10].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[10].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
						'.$icons_edit.'
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="/products_detail.php?id='.$row[9].'" title="'.$row[0].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProducts_sale ($pagina, $folio)
	{
		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);

		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		$c_s = str_replace("almacen","s.almacen",$c);
		$c_p = str_replace("almacen","p.almacen",$c);
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p )");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.($pagina - 1 ).'"><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		$body = '<div class="row">
		<div class="col-md-12">
		<div class="section-title-2 text-uppercase mb-40 text-center">
		<h4>AGREGUE PRODUCTOS A SU VENTA: '.$folio.'</h4>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-8">
			<form class="header-search-box" action="sale.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off">
				<input type="hidden" id="folio" name="folio" value="'.$folio.'">
				<input type="hidden" id="pagina" name="pagina" value="1">
			</div>
			
		</div>
		<div class="col-md-4">
			<button class="submit-btn" type="submit">Buscar</button>
			<a href="#" title="Agregar producto generico" data-toggle="modal" data-target="#add_car_generic">
				<button class="submit-btn" type="submit">+ Producto generico</button>
			</a>
			</form>
		</div>
	</div>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[10].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[10].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">
							<i class="zmdi zmdi-shopping-cart"></i>
						</a>
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProducts_cot ($pagina, $folio)
	{
		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);

		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		$c_s = str_replace("almacen","s.almacen",$c);
		$c_p = str_replace("almacen","p.almacen",$c);
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p )");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.($pagina - 1 ).'"><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '<div class="row">
					<div class="col-md-12">
						<div class="section-title-2 text-uppercase mb-40 text-center">
							<h4>AGREGUE PRODUCTOS A SU COTIZACION: '.$folio.'</h4>
						</div>
					</div>
					<div class="col-md-12">
						<div class="col-md-8 text-center">
							<form class="header-search-box" action="sale_cot.php">
							<div>
								<input type="text" placeholder="Buscar" name="search" autocomplete="off">
								<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							</div>
							
						</div>
						<div class="col-md-4 text-right">
							<button class="submit-btn" type="submit">Buscar</button>
							<a href="#" title="Agregar producto generico" data-toggle="modal" data-target="#add_car_generic">
								<button class="submit-btn" type="submit">+ Producto generico</button>
							</a>
							</form>
						</div>
					</div>
				</div>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[12].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[12].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">
							<i class="zmdi zmdi-shopping-cart"></i>
						</a>
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProducts_sale_order ($pagina, $folio)
	{
		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		$c_s = str_replace("almacen","s.almacen",$c);
		$c_p = str_replace("almacen","p.almacen",$c);
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p )");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
					
					
		$body = '<div class="row">
		<div class="col-md-12">
		<div class="section-title-2 text-uppercase mb-40 text-center">
			<h4>AGREGUE PRODUCTOS A PEDIR, FOLIO: '.$folio.'</h4>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-8">
			<form class="header-search-box" action="sale_order.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off">
				<input type="hidden" id="folio" name="folio" value="'.$folio.'">
			</div>
			
		</div>
		<div class="col-md-4">
			<button class="submit-btn" type="submit">Buscar</button>
			<a href="#" title="Agregar producto generico" data-toggle="modal" data-target="#add_car_generic">
				<button class="submit-btn" type="submit">+ Producto generico</button>
			</a>
			</form>
		</div>
	</div>
				</div>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
		    $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[10].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[10].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">
							<i class="zmdi zmdi-shopping-cart"></i>
						</a>
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProducts_saleSearch ($txt, $folio, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
			$c_s = str_replace("p.almacen","s.almacen",$c);
		    $c_p = str_replace("p.almacen","p.almacen",$c);
			
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			LIMIT $inicio, $TAMANO_PAGINA");


			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.($pagina - 1 ).'"><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
									
		$body = '<div class="row">
		<div class="col-md-12">
		<div class="section-title-2 text-uppercase mb-40 text-center">
		<h4>AGREGUE PRODUCTOS A SU VENTA: '.$folio.'</h4>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-8">
			<form class="header-search-box" action="sale.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" value="'.$txt.'">
				<input type="hidden" id="folio" name="folio" value="'.$folio.'">
				<input type="hidden" id="pagina" name="pagina" value="1">
			</div>
			
		</div>
		<div class="col-md-4">
			<button class="submit-btn" type="submit">Buscar</button>
			<a href="#" title="Agregar producto generico" data-toggle="modal" data-target="#add_car_generic">
				<button class="submit-btn" type="submit">+ Producto generico</button>
			</a>
			<br><br><br><br>
			</form>
		</div>
	</div>

	'.$pagination.'

	';
		

		while($row = mysqli_fetch_array($data))
	    {
		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[12].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[12].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">
							<i class="zmdi zmdi-shopping-cart"></i>
						</a>
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProducts_CotSearch ($txt, $folio, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
		$c_s = str_replace("p.almacen","s.almacen",$c);
		$c_p = str_replace("p.almacen","p.almacen",$c);

		$data = mysqli_query(db_conectar(),"
		SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

		p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		LIMIT $inicio, $TAMANO_PAGINA");


		$datatmp = mysqli_query(db_conectar(),"
		SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

		p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.($pagina - 1 ).'"><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';



		$body = '<div class="row">
		<div class="col-md-12">
		<div class="section-title-2 text-uppercase mb-40 text-center">
			<h4>AGREGUE PRODUCTOS A SU COTIZACION: '.$folio.'</h4>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-8 text-right">
			<form class="header-search-box" action="sale_cot.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" value = "'.$txt.'">
				<input type="hidden" id="folio" name="folio" value="'.$folio.'">
			</div>
			
		</div>
		<div class="col-md-4 text-center">
			<button class="submit-btn" type="submit">Buscar</button>
			<a href="#" title="Agregar producto generico" data-toggle="modal" data-target="#add_car_generic">
				<button class="submit-btn" type="submit">+ Producto generico</button>
			</a>
			</form>
		</div>
	</div>
</div>
'.$pagination.'
';
		

		while($row = mysqli_fetch_array($data))
	    {
		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[12].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[12].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">
							<i class="zmdi zmdi-shopping-cart"></i>
						</a>
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProducts_saleSearch_order ($txt, $folio, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
        
        
        
		$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
			$c_s = str_replace("p.almacen","s.almacen",$c);
		    $c_p = str_replace("p.almacen","p.almacen",$c);
			
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			LIMIT $inicio, $TAMANO_PAGINA");


			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

        $num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.($pagina - 1 ).'"><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';


		
		$body = '<div class="row">
		<div class="col-md-12">
		<div class="section-title-2 text-uppercase mb-40 text-center">
			<h4>AGREGUE PRODUCTOS A PEDIR, FOLIO: '.$folio.'</h4>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-8">
			<form class="header-search-box" action="sale_order.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" value = "'.$txt.'">
				<input type="hidden" id="folio" name="folio" value="'.$folio.'">
			</div>
			
		</div>
		<div class="col-md-4">
			<button class="submit-btn" type="submit">Buscar</button>
			<a href="#" title="Agregar producto generico" data-toggle="modal" data-target="#add_car_generic">
				<button class="submit-btn" type="submit">+ Producto generico</button>
			</a>
			</form>
		</div>
	</div>
				</div>
				'.$pagination.'
				';
		

		while($row = mysqli_fetch_array($data))
	    {
		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[12].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[12].'  | Antes $ '.$row[4].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">
							<i class="zmdi zmdi-shopping-cart"></i>
						</a>
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#add_car'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProductsDepartment ($departamento, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		
		
		if ($login)
		{
			$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		    $c_s = str_replace("almacen","s.almacen",$c);
		    $c_p = str_replace("almacen","p.almacen",$c);
			
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, 
			p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, 
			p.loc_almacen FROM productos p, almacen a, departamentos d where
			p.almacen = a.id and p.departamento = d.id  and p.departamento = $departamento and ( $c_p ) or 
			p.almacen = a.id and p.departamento = d.id  and p.departamento = $departamento and p.departamento = $departamento and (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 
			order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte` FROM productos p where p.departamento = '$departamento' and ( $c_p ) or p.departamento = '$departamento' and (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0");
		}else 
		{
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, 
			p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, 
			p.loc_almacen FROM productos p, almacen a, departamentos d where
			p.almacen = a.id and p.departamento = d.id  and p.departamento = $departamento or 
			p.almacen = a.id and p.departamento = d.id  and p.departamento = $departamento and p.departamento = $departamento
			order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");

			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.id p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, 
			p.loc_almacen FROM productos p, almacen a, departamentos d where
			p.almacen = a.id and p.departamento = d.id  and p.departamento = $departamento or 
			p.almacen = a.id and p.departamento = d.id  and p.departamento = $departamento and p.departamento = $departamento
			order by p.id desc");
		}

		
		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?department='.$departamento.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?department='.$departamento.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?department='.$departamento.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?department='.$departamento.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?department='.$departamento.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?department='.$departamento.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
									
									
		$body = '<div class="row">
					<div class="col-md-12">
						<div class="section-title-2 text-uppercase mb-40 text-center">
							<h4>LISTA DE PRODUCTOS: '.DepartamentosReturnNombre($departamento).' </h4>
						</div>
					</div>
				</div>';
		$body .= $pagination;

		while($row = mysqli_fetch_array($data))
	    {
		  if ($login)
		  {
			if ($_SESSION['product_gest'] == 1)
			{
				$icons_edit = 
				'<a href="#" title="Edicion rapida" data-toggle="modal" data-target="#edit_flash'.$row[9].'">
					<i class="zmdi zmdi-flash"></i>
				</a>
				<a href="/products_edit.php?id='.$row[9].'&url='.$_SERVER['REQUEST_URI'].'" title="Editar">
					<i class="zmdi zmdi-edit"></i>
				</a>';
			}else {$icons_edit = '';}
		  }

		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[12].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[12].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
						'.$icons_edit.'
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="/products_detail.php?id='.$row[9].'" title="'.$row[0].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProductsAlmacen ($almacen, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		
		if ($login)
		{
			$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		    $c_s = str_replace("almacen","s.almacen",$c);
		    $c_p = str_replace("almacen","p.almacen",$c);
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte` FROM productos p where p.almacen = '$almacen' and ( $c_p ) or (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte` FROM productos p where p.almacen = 2 and ( $c_p ) or (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0");
		}else 
		{
			$data = mysqli_query(db_conectar(),"SELECT nombre, stock, oferta, precio_normal, precio_oferta, foto0, foto1, foto2, foto3, id, `no. De parte` FROM productos where almacen = $almacen ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT id FROM productos where almacen = $almacen");
		}

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?almacen='.$almacen.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?almacen='.$almacen.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?almacen='.$almacen.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?almacen='.$almacen.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?almacen='.$almacen.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?almacen='.$almacen.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '<div class="row">
					<div class="col-md-12">
						<div class="section-title-2 text-uppercase mb-40 text-center">
							<h4>LISTA DE PRODUCTOS: '.AlmacenReturnNombre($almacen).' </h4>
						</div>
					</div>
				</div>';
		
		$body .= $pagination;

		while($row = mysqli_fetch_array($data))
	    {
		  if ($login)
		  {
			if ($_SESSION['product_gest'] == 1)
			{
				$icons_edit = 
				'<a href="#" title="Edicion rapida" data-toggle="modal" data-target="#edit_flash'.$row[9].'">
					<i class="zmdi zmdi-flash"></i>
				</a>
				<a href="/products_edit.php?id='.$row[9].'&url='.$_SERVER['REQUEST_URI'].'" title="Editar">
					<i class="zmdi zmdi-edit"></i>
				</a>';
			}else {$icons_edit = '';}
		  }

		  $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[10].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[10].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
						'.$icons_edit.'
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		
		
		
		';
		}
		$body = $body . $pagination;
		return $body;
	}

	function _getProductsSearch ($txt, $pagina)
	{
		$login = false;
		$contador = 0;
		$icons_edit = "";
		
		if (isset($_SESSION['users_id'])){ $login = true;}

		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		
		if ($login)
		{
			$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
			$c_s = str_replace("p.almacen","s.almacen",$c);
		    $c_p = str_replace("p.almacen","p.almacen",$c);
			
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			LIMIT $inicio, $TAMANO_PAGINA");


			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)");
		}else 
		{
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%'
			LIMIT $inicio, $TAMANO_PAGINA");


			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' ");
		}

		
		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';
                            

        $num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'"><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';



		$body = '<div class="row">
					<div class="col-md-12">
						<div class="section-title-2 text-uppercase mb-40 text-center">
							<h4>LISTA DE PRODUCTOS : '.$txt.' </h4>
						</div>
					</div>
				</div>
				'.$pagination.'
				';
		

		while($row = mysqli_fetch_array($data))
	    {
		  $contador += 1;
		  if ($login)
		  {
			if ($_SESSION['product_gest'] == 1)
			{
				$icons_edit = 
				'<a href="#" title="Edicion rapida" data-toggle="modal" data-target="#edit_flash'.$row[9].'">
					<i class="zmdi zmdi-flash"></i>
				</a>
				<a href="/products_edit.php?id='.$row[9].'&url='.$_SERVER['REQUEST_URI'].'" title="Editar">
					<i class="zmdi zmdi-edit"></i>
				</a>';
			}else {$icons_edit = '';}
		  }

		    $precio = $row[3];
			$msg_oferta = "";
			$_stock = '<p>PN: '.$row[12].'</p>';

			if ($row[2] == 1)
			{
				$precio = $row[4];
				$msg_oferta = '<span class="new-label red-color text-uppercase">off</span>';
				$_stock = '<p>PN: '.$row[12].'  | Antes $ '.$row[3].' MXN</p>';
			}

	        $body = $body.'<div class="col-md-3">
                                    
			<div class="single-product mb-40">
				<div class="product-img-content mb-20">
					<div class="product-img">
						<a href="/products_detail.php?id='.$row[9].'">
							<img src="../images/'.$row[5].'" alt="" style="max-height: 180px;">
						</a>
					</div>
					'.$msg_oferta.'
					<div class="product-action text-center">
						<a href="#" title="Ver detalles" data-toggle="modal" data-target="#viewM'.$row[9].'">
							<i class="zmdi zmdi-eye"></i>
						</a>
						'.$icons_edit.'
					</div>
				</div>
				<div class="product-content text-center text-uppercase">
					<a href="/products_detail.php?id='.$row[9].'" title="'.$row[0].'">'.substr($row[0], 0, 25).'.</a>
					<div class="rating-icon">
						'.$_stock.'
					</div>
					<div class="product-price">
						<span class="new-price">$ '.$precio.' MXN</span>
					</div>
				</div>
			</div>
		</div>
		
		';
		}
		$body = $body . $pagination;

		if ($contador <= 0)
		{
			$body = '<center><p>
				<h3>NO CONTAMOS POR EL MOMENTO CON ESTE PRODUCTO</h3>
				<br>
				<h4>
				'.$_SESSION["empresa_nombre"].'
				<br>
				TELEFONOS: '.$_SESSION["empresa_telefono"].'
				<br>
				<br>
				'.$_SESSION["empresa_correo"].'
				</h3>
				<br>
				<br>
			</p></center>';
		}

		return $body;
	}

	function   _getProductsID ($id)
	{
		
		$con = db_conectar();

		$data = mysqli_query($con,"SELECT `no. De parte`, nombre, precio_normal, precio_oferta, stock, `tiempo de entrega`, descripcion, almacen, departamento, loc_almacen, marca, proveedor, oferta, id, foto0, foto1, foto2, foto3, stock_min, stock_max, precio_costo, cv, um, um_des FROM productos where id = $id ");

		while($row = mysqli_fetch_array($data))
	    {
		  	$body = '
		  	<form id="contact-form" action="func/product_update.php" method="post" enctype="multipart/form-data">
	          <div class="row">
	          	  <input type="hidden" id="id" name="id" value="'.$row[13].'">
				  <input type="hidden" id="url" name="url" value="'.$_GET['url'].'">

	              <div class="col-md-12">
	                  <div class="section-title text-uppercase mb-40">
	                      <h4>Editar producto '.$row[1].'</h4>
	                  </div>
	              </div>
                  
	              <div class="col-md-4">
	                <label>Numero de parte</label>
	                <input type="text" name="parte" id="parte" placeholder="AEF594-S" value='.$row[0].'>
	              </div>
                  
	              <div class="col-md-4">
	                <label>Nombre del producto</label>
	                <input type="text" name="name" id="name" placeholder="Nombre producto" value="'.$row[1].'">
	              </div>
                  
                  <div class="col-md-4">
	                <label>Clave sat producto</label>
	                <input type="text" name="cv" id="cv" placeholder="Clave del producto" value="'.$row[21].'">
	              </div>
	              
	              <div class="col-md-3">
	                <label>Unidad de medida</label>
	                <input type="text" name="um" id="um" placeholder="U. Medida sat" value="'.$row[22].'">
				  </div>

				  <div class="col-md-3">
	                <label>Unidad de medida des</label>
	                <input type="text" name="um_des" id="um_des" placeholder="U. Medida sat" value="'.$row[23].'">
				  </div>
                  
                  <div class="col-md-3">
	                <label>Stock minimo<span class="required">*</span></label>
	                <input type="number" name="stock_minimo" id="stock_minimo" placeholder="Stock minimo" value="'.$row[18].'">
				  </div>
				  
				  <div class="col-md-3">
				  <label>Stock maximo<span class="required">*</span></label>
				  <input type="number" name="stock_maximo" id="stock_maximo" placeholder="Stock minimo" value="'.$row[19].'">
				 </div>
				
				<div class="col-md-6">
	                <label>Precio normal<span class="required">*</span></label>
	                <input type="text" name="precio" id="precio" placeholder="Precio al publico" value="'.$row[2].'">
				  </div>

				<div class="col-md-6">
					<label>Precio de costo<span class="required">*</span></label>
					<input type="text" name="precio_costo" id="precio_costo" placeholder="Precio de costo" value="'.$row[20].'">
				</div>

	            <div class="col-md-6">
	                <label>Precio oferta<span class="required">*</span></label>
	                <input type="text" name="p_oferta" id="p_oferta" placeholder="Precio con oferta al publico" value="'.$row[3].'">
	            </div>

	            <div class="col-md-6">
	                <label>Unidades existentes<span class="required">*</span></label>
	                <input type="text" name="stock" id="stock" placeholder="Stock" value="'.$row[4].'">
	            </div>

	            <div class="col-md-6">
	                <label>Tiempo de entrega</label>
	                <input type="text" name="t_entrega" id="t_entrega" placeholder="1 Dia habil" value="'.$row[5].'">
	            </div>

				<div class="country-select shop-select col-md-6">
	                <label> Usar precio de oferta ? <span class="required">*</span></label>
	                <select id="use_oferta" name = "use_oferta" id="use_oferta">
	                    <option value="1">Si usar</option>
	                    <option value="0">No usar</option>
	                </select>                                       
				</div>
				
	              <div class="col-md-12">
	              <label>Ingrese  una descripcion o caracteristicas del producto</label>
	              <textarea placeholder="..." name="descripcion" id="descripcion" class="custom-textarea">'.$row[6].'</textarea>
	              </div>

	              <div class="country-select shop-select col-md-6">
	                <label> Seleccione Almacen <span class="required">*</span></label>
	                <select id="almacen" name="almacen">
	                    '.Select_Almacen().'
	                </select>                                       
	            </div>
	            
	            <div class="country-select shop-select col-md-6">
	                <label> Seleccione Departamento <span class="required">*</span></label>
	                <select id="departamento" name = "departamento">
	                    '.Select_Departamento().'
	                </select>                                       
	            </div>
	            <div class="col-md-12">
	                <br>
	                <label>Especifique ubicacion exacta en almacen</label>
	                <textarea placeholder="Anaquel b-15" name="ubicacion" id="ubicacion" class="custom-textarea">'.$row[9].'</textarea>
	            </div>
	            <div class="col-md-6">
	                <br><label>Ingres la marca del producto</label>
	                <input type="text" name="marca" id="marca" placeholder="Marca" value="'.$row[10].'">
	            </div>
	            <div class="col-md-6">
	                <br><label>Ingrese proveedor</label>
	                <input type="text" name="proveedor" id="proveedor" placeholder="Proveedor" value="'.$row[11].'">
	            </div>


	            <div class="row">
                    
                    <div class="col-md-3">
                      <div class="thumbnail">
                      <img src="images/'.$row[14].'" alt="Imagen 1" style="width:100%" id="_img1" name="_img1">
                      </div>
                    </div>
                    
                    <div class="col-md-3">
                      <div class="thumbnail">
                          <img src="images/'.$row[15].'" alt="Imagen 2" style="width:100%" id="_img2" name="_img2">
                      </div>
                    </div>
                    
                    <div class="col-md-3">
                      <div class="thumbnail">
                          <img src="images/'.$row[16].'" alt="Imagen 3" style="width:100%" id="_img3" name="_img3">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="thumbnail">
                          <img src="images/'.$row[17].'" alt="Imagen 4" style="width:100%" id="_img4" name="_img4">
                      </div>
                    </div>
                  
                </div>
				
				<div class="col-md-3">
					<label class="containeruser">Eliminar imagen 1
						<input type="checkbox" id="delete_img_1" name="delete_img_1">
						<span class="checkmark"></span>
					</label>
				</div>

				<div class="col-md-3">
					<label class="containeruser">Eliminar imagen 2
						<input type="checkbox" id="delete_img_2" name="delete_img_2">
						<span class="checkmark"></span>
					</label>
				</div>

				<div class="col-md-3">
				<label class="containeruser">Eliminar imagen 3
						<input type="checkbox" id="delete_img_3" name="delete_img_3">
						<span class="checkmark"></span>
					</label>
				</div>

				<div class="col-md-3">
					<label class="containeruser">Eliminar imagen 4
						<input type="checkbox" id="delete_img_4" name="delete_img_4">
						<span class="checkmark"></span>
					</label>
				</div>
				<hr>

	            <div class="country-select shop-select col-md-6">
	                <label>Seleccione una Imagen si desea actualiza la imagen 1 <span class="required">*</span></label>
	                <input type="file" name="imagen0" id="imagen0" accept="image/jpeg,image/jpg" onclick="chargeImg(1)">
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label>Seleccione una Imagen si desea actualiza la imagen 2 <span class="required">*</span></label>
	                <input type="file" id="imagen1" name="imagen1" accept="image/jpeg,image/jpg" >
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label>Seleccione una Imagen si desea actualiza la imagen 3 <span class="required">*</span></label>
	                <input type="file" name="imagen2" id="imagen2" accept="image/jpeg,image/jpg" >
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label>Seleccione una Imagen si desea actualiza la imagen 4 <span class="required">*</span></label>
	                <input type="file" name="imagen3" id="imagen3" accept="image/jpeg,image/jpg" >
	            </div>

	            <script>
	            	document.getElementById("almacen").value = "'.$row[7].'";    
	            	document.getElementById("departamento").value = "'.$row[8].'";    
	            	document.getElementById("use_oferta").value = "'.$row[12].'";    
	            </script>
	            <div class="country-select shop-select col-md-12 text-center">
	                <button class="submit-btn mt-20" type="submit">Actualizar</button>
				</div>

	          </div>
		  </form>
		  ';
		}
        
		$body .= '
		<div class="col-md-12">
			<div class="section-title text-uppercase mb-20">
				<h4>Agregar afiliado</h4>
			</div>
		</div>
		
		<div class="col-md-12">
			<form id="contact-form" action="func/product_add_sub.php" method="post" enctype="multipart/form-data">
			<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
			<div class="row">
					<br><input type="hidden" id="padre" name="padre" value="'.$id.'">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					

					<div class="col-md-2">
						<label> Seleccione Almacen <span class="required">*</span></label>
						<select id="almacen" name="almacen" required>
							'.Select_Almacen_cero().'
						</select>                                       
					</div>
					
					<div class="col-md-2">
						<label>Unidades</label>
						<input type="number" name="stock" id="stock" placeholder="Stock" value="1" required>
					</div>

					<div class="col-md-2">
						<label>Unidades minimas</label>
						<input type="number" name="min" id="min" placeholder="Stock" value="1" required>
					</div>

					<div class="col-md-2">
						<label>Unidades maximas</label>
						<input type="number" name="max" id="max" placeholder="Stock" value="1" required>
					</div>

					<div class="col-md-2">
						<label>Ubicacion de fisica</label>
						<input type="text" name="ubicacion" id="ubicacion" placeholder="...">
					</div>

					<div class="col-md-2 text-center">
						<button class="submit-btn mt-20" type="submit">Agregar</button>
					</div>
				</div>
			</form>
		</div>
		';

		$sub = mysqli_query($con,"SELECT p.id, a.nombre, p.stock, p.ubicacion, p.min, p.max FROM productos_sub p, almacen a where p.almacen = a.id and p.padre = $id ");

		$body .= '
		<div class="col-md-12">
			<div class="section-title text-uppercase mb-40">
			<br><br><br><h4>Afiliados</h4>
			</div>
		</div>
		<div class="row">';
		while($row = mysqli_fetch_array($sub))
	    {
			$body .= '
				<div class="col-md-6">
					<form id="contact-form" action="func/product_update_sub.php" method="post">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<div class="row">
							<br><input type="hidden" id="id" name="id" value="'.$row[0].'">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

							<div class="col-md-12">
								<div class="section-title text-uppercase mb-10">
									<h4>'.$row[1].'</h4>
								</div>
							</div>
							<div class="col-md-4 ">
								<label>Existencia</label>
								<input type="number" name="stock" id="stock" placeholder="Stock" value='.$row[2].'>
							</div>
							<div class="col-md-4 text-right">
								<button class="submit-btn mt-20" type="submit">Actualizar</button>
							</div>
							<div class="col-md-4 text-left">
								<a href="#" data-toggle="modal" data-target="#delete_hijo'.$row[0].'" >
									<button class="submit-btn mt-20" type="submit">Eliminar</button>
								</a>
							</div>
							<div class="col-md-12 ">
								<br>
								<label>Ubicacion</label>
								<input type="text" name="ubicacion" id="ubicacion" placeholder="..." value="'.$row[3].'">
							</div>

							<div class="col-md-6 ">
								<br>
								<label>Unicades minimas</label>
								<input type="number" name="min" id="min" placeholder="..." value="'.$row[4].'">
							</div>

							<div class="col-md-6 ">
								<br>
								<label>Unidades maximas</label>
								<input type="number" name="max" id="max" placeholder="..." value="'.$row[5].'">
							</div>
							</form>
						</div>
				</div>
			';
		}
		$body .= '</div>';
        
		return $body;
	}
	
	function ModelProductHijosDelete ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `productos_sub` WHERE padre = '$id' ");

		while($row = mysqli_fetch_array($data))
	    {
		  	
			$body .= '
			<div class="modal fade" id="delete_hijo'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR SUB PRODUCTO ACTUAL?</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Esta seguro de eliminar el su producto? despues de esta accion no abra posibilidad de recuperar el sub producto.</p>
				</div>
				<div class="modal-footer">
					<form action="func/product_delete_sub.php" method="post">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" id="id" name="id" value="'.$row[0].'">
						<button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">NO</button>
						<button type="submit" class="btn btn-danger">SI</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		return $body;
	}
	
	function _getProductsModal ($pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		
		if (isset($_SESSION['sucursal']))
		{
			$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		    $c_s = str_replace("almacen","s.almacen",$c);
		    $c_p = str_replace("almacen","p.almacen",$c);
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p )");
			
		}else 
		{
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id or p.departamento = d.id and p.almacen = a.id  order by p.id asc LIMIT $inicio, $TAMANO_PAGINA");
		}
		
		$con_hijos  = db_conectar();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$fb_share = 'https://www.ascgar.com/products_detail_nosesion.php?id='.$row[9];
			
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[16].' | '.$row[1].' UDS</option>';
			

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';
			} //Finaliza hijos

			
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			
			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
			}
			
			if ($row[2] == 1)
        	{
        		$select = '<option value="1" selected>Si usar</option>
            	 <option value="0">No usar</option>';
        	}
        	else
        	{
        		$select = '<option value="1">Si usar</option>
            	 <option value="0" selected>No usar</option>';
        	}

			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[10].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														<p>Unidades disponibles: '.$stock.' UDS</>
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[11].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[12].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[13].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
													'.$row[15].'
													</div>
												</div>
												<div class="country-select shop-select col-md-12">
													<label> Existencias</label>
													<select>
														'.$almacen.'
													</select>                                
												</div>
												<div class="col-md-12 text-right">
												    <div class="fb-share-button" data-href="'.$fb_share.'" data-layout="button" data-size="large"><a target="_blank" href="'.$fb_share.'" class="fb-xfbml-parse-ignore">Compartir</a></div> 
												</div>
											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--End of Quickview Product-->
				<div class="modal fade" id="edit_flash'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">EDIDICION RAPIDA: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        <form id="contact-form" action="func/product_update_flash.php" method="post" enctype="multipart/form-data">
	          <div class="row">
	          	  <input type="hidden" id="id" name="id" value="'.$row[9].'">
				  <input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

	              <div class="col-md-6">
	                <label>Numero de parte</label>
	                <input type="text" name="parte" id="parte" placeholder="AEF594-S" value='.$row[10].'>
	              </div>
	              <div class="col-md-6">
	                <label>Nombre del producto</label>
	                <input type="text" name="name" id="name" placeholder="Nombre producto" value="'.$row[0].'">
	              </div>
	              
	              <div class="col-md-6">
	                <label>Precio normal<span class="required">*</span></label>
	                <input type="text" name="precio" id="precio" placeholder="Precio al publico" value="'.$row[3].'">
	            </div>

	            <div class="col-md-6">
	                <label>Precio oferta<span class="required">*</span></label>
	                <input type="text" name="p_oferta" id="p_oferta" placeholder="Precio con oferta al publico" value="'.$row[4].'">
	            </div>

	            <div class="col-md-6">
	                <label>Unidades existentes<span class="required">*</span></label>
	                <input type="text" name="stock" id="stock" placeholder="Stock" value="'.$row[1].'">
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label> Usar precio de oferta ? <span class="required">*</span></label>
	                <select id="use_oferta" name="use_oferta">
	                	'.$select.'
	                </select>                                       
	            </div>
	            
	          </div>
  		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		        <button type="submit" class="btn btn-primary">Guardar</button>
		        </form>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}

	function _getProductsModal_sale ($pagina, $folio)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		$c_s = str_replace("almacen","s.almacen",$c);
		$c_p = str_replace("almacen","p.almacen",$c);
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p )");

		$con_hijos  = db_conectar();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			$precio_ = $row[3];

			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
				$precio_ = $row[4];
			}
			
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[16].' | '.$row[1].' UDS</option>';
			
			$exist = '
			<tr>
				<td class="item-des"><p>'.$row[16].'</p></td>
				<td class="item-des"><p>'.$row[1].' UDS</p></td>
				<td class="item-des"><p>
					<div class="col-md-12">
						<form action="func/producst_add_sale.php" autocomplete="off" method="post">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							<input type="hidden" id="product" name="product" value="'.$row[9].'">
							<input type="hidden" id="costo" name="costo" value="'.$precio_.'">
							<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							<input type="hidden" id="hijo" name="hijo" value="0">
							
							<div class="col-md-6">
								<input type="number" step="1" id="unidades" name="unidades" placeholder="0" value ="1" min="1" /></p>		
							</div>

							<div class="col-md-6">
								<button type="submit" class="btn btn-primary">Agregar</button>
							</div>
						</form>
					</div>
				</td>
			</tr>
			';

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';

				$exist .= '
				<tr>
					<td class="item-des"><p>'.$item[2].'</p></td>
					<td class="item-des"><p>'.$item[3].' UDS</p></td>
					<td class="item-des">
					<div class="col-md-12">
						<form action="func/producst_add_sale.php" autocomplete="off" method="post">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							<input type="hidden" id="product" name="product" value="'.$row[9].'">
							<input type="hidden" id="costo" name="costo" value="'.$precio_.'">
							<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							<input type="hidden" id="hijo" name="hijo" value="'.$item[0].'">
							
							<div class="col-md-6">
							<input type="number" step="1" id="unidades" name="unidades" placeholder="0" value ="1" min="1" /></p>		
							</div>

							<div class="col-md-6">
								<button type="submit" class="btn btn-primary">Agregar</button>
							</div>
						</form>
					</div>
					</td>
				</tr>
				';

			} //Finaliza hijos
			
			
			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[10].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														<p>Unidades disponibles: '.$stock.' UDS</>
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[11].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[12].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[13].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[15].'
													</div>
												</div>
												<div class="country-select shop-select col-md-12">
													<label> Existencias</label>
													<select>
														'.$almacen.'
													</select>                                       
												</div>
											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!--Agragar producto a venta-->
					<div class="modal fade" id="add_car'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">AGREGAR: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        
	          <div class="row">
				 <div class="col-md-12">
					<div class="country-select shop-select col-md-6">
						<p>Precio: '.$precio.'</p>
					</div>
					
					<div class="country-select shop-select col-md-6">
					 <p>Unidades disponibles: '.$stock.' UDS</>
				  	</div>
						<div class="col-md-12">
							<div class="section-title-2 text-uppercase mb-40 text-center">
								<h4>EXISTENCIAS</h4>
							</div>
						</div>
						
						<table class="cart table">
						<thead>
							<tr>
								<th class="table-head th-name uppercase">ALMACEN</th>
								<th class="table-head th-name uppercase">STOCK</th>
								<th class="table-head th-name uppercase">AGREGAR</th>
							</tr>
						</thead>
						<tbody>
							'.$exist.'
						</tbody>
						</table>
						
				</div>
	          </div>
  		      </div>
		      <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">X</button>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}

	function _getProductsModal_sale_order ($pagina, $folio)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		$c_s = str_replace("almacen","s.almacen",$c);
		$c_p = str_replace("almacen","p.almacen",$c);
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.`no. De parte`, p.descripcion, d.nombre, p.marca, p.loc_almacen, p.`tiempo de entrega`, a.nombre FROM productos p, departamentos d, almacen a where p.departamento = d.id and p.almacen = a.id AND (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 or p.departamento = d.id and p.almacen = a.id AND ( $c_p ) order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
			
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			$precio_ = $row[3];

			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
				$precio_ = $row[4];
			}
			
			
			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[10].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[11].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[12].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[13].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[15].'
													</div>
												</div>
											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!--Agragar producto a venta-->
					<div class="modal fade" id="add_car'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">AGREGAR: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
	          <div class="row">
				 <div class="col-md-12">
					<div class="country-select shop-select col-md-6">
						<p>Precio: '.$precio.'</p>
					</div>
					
					<div class="country-select shop-select col-md-6">
					 <p>Unidades disponibles: '.$stock.' UDS</>
				  	</div>
						<div class="col-md-12">
							<div class="section-title-2 text-uppercase mb-40 text-center">
								<h4>Agregar</h4>
							</div>
						</div>
						<form action="func/producst_add_sale_order.php" autocomplete="off" method="post">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							<input type="hidden" id="product" name="product" value="'.$row[9].'">
							<input type="hidden" id="costo" name="costo" value="'.$precio_.'">
							<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							
							<div class="col-md-4">
								<input type="number" step="1" id="unidades" name="unidades" placeholder="0" value ="1" min="1" " style="text-align: center;"></p>		
							</div>

							<div class="col-md-8">
								<button type="submit" class="btn btn-primary">Agregar</button>
							</div>

						</form>
				</div>
	          </div>
  		      </div>
		      <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">X</button>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}

	function _getProductsModal_sale_search ($txt, $folio, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
		$c_s = str_replace("p.almacen","s.almacen",$c);
		$c_p = str_replace("p.almacen","p.almacen",$c);
		
		$data = mysqli_query(db_conectar(),"
		SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

		p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		LIMIT $inicio, $TAMANO_PAGINA");
		
		$select = "";

		$con_hijos  = db_conectar();

		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			$precio_ = $row[3];

			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
				$precio_ = $row[4];
			}
			
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[13].' | '.$row[1].' UDS</option>';
			
			$exist = '
			<tr>
				<td class="item-des"><p>'.$row[13].'</p></td>
				<td class="item-des"><p>'.$row[1].' UDS</p></td>
				<td class="item-des"><p>
					<div class="col-md-12">
						<form action="func/producst_add_sale.php" autocomplete="off" method="post">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							<input type="hidden" id="product" name="product" value="'.$row[9].'">
							<input type="hidden" id="costo" name="costo" value="'.$precio_.'">
							<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							<input type="hidden" id="hijo" name="hijo" value="0">
							
							<div class="col-md-6">
								<input type="number" step="1" id="unidades" name="unidades" placeholder="0" value ="1" min="1" /></p>		
							</div>

							<div class="col-md-6">
								<button type="submit" class="btn btn-primary">Agregar</button>
							</div>
						</form>
					</div>
				</td>
			</tr>
			';

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';

				$exist .= '
				<tr>
					<td class="item-des"><p>'.$item[2].'</p></td>
					<td class="item-des"><p>'.$item[3].' UDS</p></td>
					<td class="item-des">
					<div class="col-md-12">
						<form action="func/producst_add_sale.php" autocomplete="off" method="post">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							<input type="hidden" id="product" name="product" value="'.$row[9].'">
							<input type="hidden" id="costo" name="costo" value="'.$precio_.'">
							<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							<input type="hidden" id="hijo" name="hijo" value="'.$item[0].'">
							
							<div class="col-md-6">
							<input type="number" step="1" id="unidades" name="unidades" placeholder="0" value ="1" min="1" /></p>		
							</div>

							<div class="col-md-6">
								<button type="submit" class="btn btn-primary">Agregar</button>
							</div>
						</form>
					</div>
					</td>
				</tr>
				';

			} //Finaliza hijos
			
			
			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[12].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														<p>Unidades disponibles: '.$stock.' UDS</>
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[10].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[15].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[16].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[11].'
													</div>
												</div>
												<div class="country-select shop-select col-md-12">
													<label> Existencias</label>
													<select>
														'.$almacen.'
													</select>                                       
												</div>
											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!--Agragar producto a venta-->
					<div class="modal fade" id="add_car'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">AGREGAR: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        
	          <div class="row">
				 <div class="col-md-12">
					<div class="country-select shop-select col-md-6">
						<p>Precio: '.$precio.'</p>
					</div>
					
					<div class="country-select shop-select col-md-6">
					 <p>Unidades disponibles: '.$stock.' UDS</>
				  	</div>
						<div class="col-md-12">
							<div class="section-title-2 text-uppercase mb-40 text-center">
								<h4>EXISTENCIAS</h4>
							</div>
						</div>
						
						<table class="cart table">
						<thead>
							<tr>
								<th class="table-head th-name uppercase">ALMACEN</th>
								<th class="table-head th-name uppercase">STOCK</th>
								<th class="table-head th-name uppercase">AGREGAR</th>
							</tr>
						</thead>
						<tbody>
							'.$exist.'
						</tbody>
						</table>
						
				</div>
	          </div>
  		      </div>
		      <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">X</button>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}


	function _getProductsModal_sale_search_order ($txt, $folio, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
		$c_s = str_replace("p.almacen","s.almacen",$c);
		$c_p = str_replace("p.almacen","p.almacen",$c);
		$data = mysqli_query(db_conectar(),"
		SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

		p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		or
		p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
		( 
				$c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
		)
		LIMIT $inicio, $TAMANO_PAGINA");

		
		$select = "";

		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			$precio_ = $row[3];

			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
				$precio_ = $row[4];
			}
			
			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[12].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[10].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[15].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[16].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[11].'
													</div>
												</div>
											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!--Agragar producto a venta-->
					<div class="modal fade" id="add_car'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">AGREGAR: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        
	          <div class="row">
				 <div class="col-md-12">
					<div class="country-select shop-select col-md-6">
						<p>Precio: '.$precio.'</p>
					</div>
					
					<div class="country-select shop-select col-md-6">
					 <p>Unidades disponibles: '.$stock.' UDS</>
				  	</div>
						<div class="col-md-12">
							<div class="section-title-2 text-uppercase mb-40 text-center">
								<h4>AGREGAR</h4>
							</div>
						</div>
						
						<form action="func/producst_add_sale_order.php" autocomplete="off" method="post">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							<input type="hidden" id="product" name="product" value="'.$row[9].'">
							<input type="hidden" id="costo" name="costo" value="'.$precio_.'">
							<input type="hidden" id="folio" name="folio" value="'.$folio.'">
							
							<div class="col-md-4">
								<input type="number" step="1"  id="unidades" name="unidades" placeholder="0" value ="1" min="1" " style="text-align: center;"></p>		
							</div>

							<div class="col-md-8">
								<button type="submit" class="btn btn-primary">Agregar</button>
							</div>

						</form>
						
				</div>
	          </div>
  		      </div>
		      <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">X</button>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}

	function _getProductsModalDepartment ($departamento, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$login = false;
		$icons_edit = "";

		if (isset($_SESSION['users_id'])){ $login = true;}
		
        if ($login)
		{
			$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		    $c_s = str_replace("almacen","s.almacen",$c);
		    $c_p = str_replace("almacen","p.almacen",$c);
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, 
			p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, 
			p.loc_almacen FROM productos p, almacen a, departamentos d where
			p.almacen = a.id and p.departamento = d.id  and p.departamento = '$departamento' and ( $c_p ) or 
			p.almacen = a.id and p.departamento = d.id  and p.departamento = '$departamento' and (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 
			order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		}else 
		{
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where p.almacen = a.id and p.departamento = d.id and p.departamento = $departamento order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		}

		
		
		
		$con_hijos  = db_conectar();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$fb_share = 'https://www.ascgar.com/products_detail_nosesion.php?id='.$row[9];
			
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[13].' | '.$row[1].' UDS</option>';
			

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';
			} //Finaliza hijos
			
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			
			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
			}
			
			if ($row[2] == 1)
        	{
        		$select = '<option value="1" selected>Si usar</option>
            	 <option value="0">No usar</option>';
        	}
        	else
        	{
        		$select = '<option value="1">Si usar</option>
            	 <option value="0" selected>No usar</option>';
        	}

			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[12].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														<p>Unidades disponibles: '.$stock.' UDS</>
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[10].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[15].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[16].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[11].'
													</div>
												</div>
												<div class="country-select shop-select col-md-12">
													<label> Existencias</label>
													<select>
														'.$almacen.'
													</select>                                       
												</div>
												<div class="col-md-12 text-right">
												    <div class="fb-share-button" data-href="'.$fb_share.'" data-layout="button" data-size="large"><a target="_blank" href="'.$fb_share.'" class="fb-xfbml-parse-ignore">Compartir</a></div> 
												</div>

											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--End of Quickview Product-->
				<div class="modal fade" id="edit_flash'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">EDIDICION RAPIDA: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        <form id="contact-form" action="func/product_update_flash.php" method="post" enctype="multipart/form-data">
	          <div class="row">
	          	  <input type="hidden" id="id" name="id" value="'.$row[9].'">
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

	              <div class="col-md-6">
	                <label>Numero de parte</label>
	                <input type="text" name="parte" id="parte" placeholder="AEF594-S" value='.$row[12].'>
	              </div>
	              <div class="col-md-6">
	                <label>Nombre del producto</label>
	                <input type="text" name="name" id="name" placeholder="Nombre producto" value="'.$row[0].'">
	              </div>
	              
	              <div class="col-md-6">
	                <label>Precio normal<span class="required">*</span></label>
	                <input type="text" name="precio" id="precio" placeholder="Precio al publico" value="'.$row[3].'">
	            </div>

	            <div class="col-md-6">
	                <label>Precio oferta<span class="required">*</span></label>
	                <input type="text" name="p_oferta" id="p_oferta" placeholder="Precio con oferta al publico" value="'.$row[4].'">
	            </div>

	            <div class="col-md-6">
	                <label>Unidades existentes<span class="required">*</span></label>
	                <input type="text" name="stock" id="stock" placeholder="Stock" value="'.$row[1].'">
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label> Usar precio de oferta ? <span class="required">*</span></label>
	                <select id="use_oferta" name="use_oferta">
	                	'.$select.'
	                </select>                                       
	            </div>
	            
	          </div>
  		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="submit" class="btn btn-primary">Guardar</button>
		        </form>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}

	function _getProductsModalSearch ($txt, $pagina)
	{
		$login = false;
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		if (isset($_SESSION['users_id'])){ $login = true;}
		
		if ($login)
		{
			$c = "( " . str_replace("almacen","p.almacen",substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2)) . " )";
			$c_s = str_replace("p.almacen","s.almacen",$c);
		    $c_p = str_replace("p.almacen","p.almacen",$c);
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			LIMIT $inicio, $TAMANO_PAGINA");
			
			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			)
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' and 
			( 
				 $c_p or ( SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and $c_s ) > 0  
			) ");
		}else 
		{
			$data = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' 
			LIMIT $inicio, $TAMANO_PAGINA");
			
			$datatmp = mysqli_query(db_conectar(),"
			SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where 

			p.almacen = a.id and p.departamento = d.id and p.nombre like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.descripcion like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.marca like '%$txt%' 
			or
			p.almacen = a.id and p.departamento = d.id and p.proveedor like '%$txt%' ");
		}

		$con_hijos  = db_conectar();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$fb_share = 'https://www.ascgar.com/products_detail_nosesion.php?id='.$row[9];
			
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[13].' | '.$row[1].' UDS</option>';
			

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';
			} //Finaliza hijos
			
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			
			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
			}
			
			if ($row[2] == 1)
        	{
        		$select = '<option value="1" selected>Si usar</option>
            	 <option value="0">No usar</option>';
        	}
        	else
        	{
        		$select = '<option value="1">Si usar</option>
            	 <option value="0" selected>No usar</option>';
        	}

			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[12].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														<p>Unidades disponibles: '.$stock.' UDS</>
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[10].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[15].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[16].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[11].'
													</div>
												</div>
												<div class="country-select shop-select col-md-12">
													<label> Existencias</label>
													<select>
														'.$almacen.'
													</select>                                       
												</div>
												<div class="col-md-12 text-right">
												    <div class="fb-share-button" data-href="'.$fb_share.'" data-layout="button" data-size="large"><a target="_blank" href="'.$fb_share.'" class="fb-xfbml-parse-ignore">Compartir</a></div> 
												</div>

											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--End of Quickview Product-->
				<div class="modal fade" id="edit_flash'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">EDIDICION RAPIDA: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        <form id="contact-form" action="func/product_update_flash.php" method="post" enctype="multipart/form-data">
	          <div class="row">
	          	  <input type="hidden" id="id" name="id" value="'.$row[9].'">
				  <input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

	              <div class="col-md-6">
	                <label>Numero de parte</label>
	                <input type="text" name="parte" id="parte" placeholder="AEF594-S" value='.$row[12].'>
	              </div>
	              <div class="col-md-6">
	                <label>Nombre del producto</label>
	                <input type="text" name="name" id="name" placeholder="Nombre producto" value="'.$row[0].'">
	              </div>
	              
	              <div class="col-md-6">
	                <label>Precio normal<span class="required">*</span></label>
	                <input type="text" name="precio" id="precio" placeholder="Precio al publico" value="'.$row[3].'">
	            </div>

	            <div class="col-md-6">
	                <label>Precio oferta<span class="required">*</span></label>
	                <input type="text" name="p_oferta" id="p_oferta" placeholder="Precio con oferta al publico" value="'.$row[4].'">
	            </div>

	            <div class="col-md-6">
	                <label>Unidades existentes<span class="required">*</span></label>
	                <input type="text" name="stock" id="stock" placeholder="Stock" value="'.$row[1].'">
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label> Usar precio de oferta ? <span class="required">*</span></label>
	                <select id="use_oferta" name="use_oferta">
	                	'.$select.'
	                </select>                                       
	            </div>
	            
	          </div>
  		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="submit" class="btn btn-primary">Guardar</button>
		        </form>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}


	function _getProductsModalAlmacen ($almacen, $pagina)
	{
		$TAMANO_PAGINA = 16;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$login = false;
		
		if (isset($_SESSION['users_id'])){ $login = true;}
		
        if ($login)
		{
			$c = substr(GetFilterAlmacen($_SESSION['sucursal']), 0, -2);
		    $c_s = str_replace("almacen","s.almacen",$c);
		    $c_p = str_replace("almacen","p.almacen",$c);
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where p.almacen = a.id and p.departamento = d.id and p.almacen = '$almacen' and ( $c_p) or p.almacen = a.id and p.departamento = d.id and (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0 order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where p.almacen = a.id and p.departamento = d.id and p.almacen = '$almacen' and ( $c_s) or p.almacen = a.id and p.departamento = d.id and (SELECT COUNT(s.id) as id  FROM productos_sub s WHERE s.padre = p.id and ( $c_s ) ) > 0");
		}else 
		{
			$data = mysqli_query(db_conectar(),"SELECT nombre, stock, oferta, precio_normal, precio_oferta, foto0, foto1, foto2, foto3, id, `no. De parte` FROM productos order by id asc LIMIT $inicio, $TAMANO_PAGINA");
			$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where p.almacen = a.id and p.departamento = d.id and p.almacen = $almacen order by p.id desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		
		
		$con_hijos  = db_conectar();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        //$fb_share = 'https://www.ascgar.com/products_detail_nosesion.php?id='.$row[9];
	        
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[13].' | '.$row[1].' UDS</option>';
			

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';
			} //Finaliza hijos
			
			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			
			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
			}
			
			if ($row[2] == 1)
        	{
        		$select = '<option value="1" selected>Si usar</option>
            	 <option value="0">No usar</option>';
        	}
        	else
        	{
        		$select = '<option value="1">Si usar</option>
            	 <option value="0" selected>No usar</option>';
        	}

			$body = $body.'<!--Quickview Product Start -->
			
						<!-- Modal -->
						<div class="modal fade" id="viewM'.$row[9].'" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="modal-product">
											<div class="single-product-image">
												<div id="product-img-content">
													<div id="my-tab-content" class="tab-content mb-20">
														<div class="tab-pane b-img active" id="'.$row[9].'view1">
															<a class="venobox" href="images/'.$row[5].'" data-gall="gallery" title=""><img src="images/'.$row[5].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view2">
															<a class="venobox" href="images/'.$row[6].'" data-gall="gallery" title=""><img src="images/'.$row[6].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view3">
															<a class="venobox" href="images/'.$row[7].'" data-gall="gallery" title=""><img src="images/'.$row[7].'" alt=""></a>
														</div>
														<div class="tab-pane b-img" id="'.$row[9].'view4">
															<a class="venobox" href="images/'.$row[8].'" data-gall="gallery" title=""><img src="images/'.$row[8].'" alt=""></a>
														</div>
													</div>
													<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
														<div class="pro-view b-img active"><a href="#'.$row[9].'view1" data-toggle="tab"><img src="images/'.$row[5].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view2" data-toggle="tab"><img src="images/'.$row[6].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view3" data-toggle="tab"><img src="images/'.$row[7].'" alt=""></a></div>
														<div class="pro-view b-img"><a href="#'.$row[9].'view4" data-toggle="tab"><img src="images/'.$row[8].'" alt=""></a></div>
													</div>
												</div>
											</div>
											<div class="product-details-content">
												<div class="product-content text-uppercase">
													<p>Parte NO: '.$row[12].' | '.$row[0].'</p>
													<div class="rating-icon pb-20 mt-10">
														<p>Unidades disponibles: '.$stock.' UDS</>
													</div>
													<div class="product-price pb-20">
														'.$precio.'
													</div>
												</div>
												<div class="product-view pb-20">
													<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
													<p>'.$row[10].'</p>
												</div>
												<div class="product-attributes clearfix">
													<div class="product-color text-uppercase pb-30">
														<h4 class="product-details-tilte text-uppercase pb-10">Almacen</h4>
														<ul>
														<p>'.$row[13].'</p>
														</ul>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
														<p>'.$row[14].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
														<p>'.$row[15].'</p>
													</div>
												</div>
												<div class="product-attributes clearfix">
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
														<p>'.$row[16].'</p>
													</div>
													<div class="pull-left" id="quantity-wanted">
														<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
														'.$row[11].'
													</div>
												</div>
												<div class="country-select shop-select col-md-12">
													<label> Existencias</label>
													<select>
														'.$almacen.'
													</select>                                       
												</div>
												<div class="col-md-12 text-right">
												    <div class="fb-share-button" data-href="'.$fb_share.'" data-layout="button" data-size="large"><a target="_blank" href="'.$fb_share.'" class="fb-xfbml-parse-ignore">Compartir</a></div> 
												</div>

											</div>
											<!-- .product-info -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--End of Quickview Product-->
				<div class="modal fade" id="edit_flash'.$row[9].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLongTitle">EDIDICION RAPIDA: '.$row[0].'</h5>
				        </button>
				      </div>
				      <div class="modal-body">
				        


				        <form id="contact-form" action="func/product_update_flash.php" method="post" enctype="multipart/form-data">
	          <div class="row">
	          	  <input type="hidden" id="id" name="id" value="'.$row[9].'">
				  <input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

	              <div class="col-md-6">
	                <label>Numero de parte</label>
	                <input type="text" name="parte" id="parte" placeholder="AEF594-S" value='.$row[12].'>
	              </div>
	              <div class="col-md-6">
	                <label>Nombre del producto</label>
	                <input type="text" name="name" id="name" placeholder="Nombre producto" value="'.$row[0].'">
	              </div>
	              
	              <div class="col-md-6">
	                <label>Precio normal<span class="required">*</span></label>
	                <input type="text" name="precio" id="precio" placeholder="Precio al publico" value="'.$row[3].'">
	            </div>

	            <div class="col-md-6">
	                <label>Precio oferta<span class="required">*</span></label>
	                <input type="text" name="p_oferta" id="p_oferta" placeholder="Precio con oferta al publico" value="'.$row[4].'">
	            </div>

	            <div class="col-md-6">
	                <label>Unidades existentes<span class="required">*</span></label>
	                <input type="text" name="stock" id="stock" placeholder="Stock" value="'.$row[1].'">
	            </div>

	            <div class="country-select shop-select col-md-6">
	                <label> Usar precio de oferta ? <span class="required">*</span></label>
	                <select id="use_oferta" name="use_oferta">
	                	'.$select.'
	                </select>                                       
	            </div>
	            
	          </div>
  		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button type="submit" class="btn btn-primary">Guardar</button>
		        </form>
		      </div>
		    </div>
		  </div>
		  </div>';
		}
		
		return $body;
	}

	function _getProductsDetails($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where p.almacen = a.id and p.departamento = d.id and p.id = $id ");
		$con_hijos  = db_conectar();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			// Add hijos
			$stock = $row[1];
			$almacen = '<option value='.$row[9].'>'.$row[13].' | '.$row[1].' UDS</option>';
			

			$hijos = mysqli_query($con_hijos,"SELECT s.id, s.padre, a.nombre, s.stock FROM productos_sub s, almacen a where s.almacen = a.id and padre = '$row[9]' ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$stock = $stock + $item[3];
				$almacen .= '<option value='.$item[0].'>'.$item[2].' | '.$item[3].' UDS</option>';
			} //Finaliza hijos

			$precio = '<span class="new-price">$ '.$row[3].' MXN</span>';
			
			if ($row[2] == 1)
			{
				$precio = '<span class="new-price">$ '.$row[4].' MXN</span>';
				$precio = $precio . ' <span class="old-price">$ '.$row[3].' MXN</span>';
			}
			
			if ($row[2] == 1)
        	{
        		$select = '<option value="1" selected>Si usar</option>
            	 <option value="0">No usar</option>';
        	}
        	else
        	{
        		$select = '<option value="1">Si usar</option>
            	 <option value="0" selected>No usar</option>';
        	}

            $fb_producto = $row[0];
            
			$body = $body.'<div class="product-details-area section-padding">
			<div class="container">
				<div class="row">
					<div class="col-sm-5">
						<div class="single-product-image">
							<div id="product-img-content">
								<div id="my-tab-content" class="tab-content mb-30">
									<div class="tab-pane b-img active" id="view1">
										<a href="images/'.$row[5].'" data-gall="gallery" title="Imagen numero 1"><img src="images/'.$row[5].'" alt=""></a>
									</div>
									<div class="tab-pane b-img" id="view2">
										<a href="images/'.$row[6].'" data-gall="gallery" title="Imagen numero 2"><img src="images/'.$row[6].'" alt=""></a>
									</div>
									<div class="tab-pane b-img" id="view3">
										<a href="images/'.$row[7].'" data-gall="gallery" title="Imagen numero 3"><img src="images/'.$row[7].'" alt=""></a>
									</div>
									<div class="tab-pane b-img" id="view4">
										<a href="images/'.$row[8].'" data-gall="gallery" title="Imagen numero 4"><img src="images/'.$row[8].'" alt=""></a>
									</div>
								</div>
								<div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
									<div class="pro-view b-img active"><a href="#view1" data-toggle="tab"><img src="images/'.$row[5].'" alt="" style="width: 104px; height: 128px;" ></a></div>
									<div class="pro-view b-img"><a href="#view2" data-toggle="tab"><img src="images/'.$row[6].'" alt="" style="width: 104px; height: 128px;" ></a></div>
									<div class="pro-view b-img"><a href="#view3" data-toggle="tab"><img src="images/'.$row[7].'" alt="" style="width: 104px; height: 128px;" ></a></div>
									<div class="pro-view b-img"><a href="#view4" data-toggle="tab"><img src="images/'.$row[8].'" alt="" style="width: 104px; height: 128px;" ></a></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-7">
						<div class="product-details-content">
							<div class="product-content text-uppercase">
								<p>NO. DE PARTE '.$row[12].' | '.$row[0].'</p>
								<div class="rating-icon pb-30 mt-10">
									<p>Unidades disponibles: '.$stock.' UDS</>
								</div>
								<div class="product-price pb-30">
								'.$precio.'
								</div>
							</div>
							<div class="product-view pb-30">
								<h4 class="product-details-tilte text-uppercase">Descripcion</h4>
								<p>'.$row[10].'</p>
							</div>
							<div class="product-attributes clearfix">
							<div class="product-color text-uppercase pb-30">
								<h4 class="product-details-tilte text-uppercase pb-10">Almacen</h4>
								<ul>
								<p>'.$row[13].'</p>
								</ul>
							</div>
							<div class="pull-left" id="quantity-wanted">
								<h4 class="product-details-tilte text-uppercase pb-10">Departamento</h4>
								<p>'.$row[14].'</p>
							</div>
							<div class="pull-left" id="quantity-wanted">
								<h4 class="product-details-tilte text-uppercase pb-10">Marca</h4>
								<p>'.$row[15].'</p>
							</div>
						</div>
						<div class="product-attributes clearfix">
							<div class="pull-left" id="quantity-wanted">
								<h4 class="product-details-tilte text-uppercase pb-10">Ubicacion</h4>
								<p>'.$row[16].'</p>
							</div>
							<div class="pull-left" id="quantity-wanted">
								<h4 class="product-details-tilte text-uppercase pb-10">T. Entrega</h4>
								'.$row[11].'
							</div>
						</div>
						<div class="country-select shop-select col-md-12">
							<label> Existencias</label>
							<select>
								'.$almacen.'
							</select>                                       
						</div>
						</div>
					</div>
				</div>
		<!-- End Of Shop Full Grid View -->';
		}
		return $body;
	}
	
	function _getHeaderFB($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT p.nombre, p.stock, p.oferta, p.precio_normal, p.precio_oferta, p.foto0, p.foto1, p.foto2, p.foto3, p.id, p.descripcion, p.`tiempo de entrega`, p.`no. De parte`, a.nombre, d.nombre, p.marca, p.loc_almacen FROM productos p, almacen a, departamentos d where p.almacen = a.id and p.departamento = d.id and p.id = $id ");
		

		$body = "";
		if($row = mysqli_fetch_array($data))
	    {
			
			$precio = $row[3] . ' MXN';
			
			if ($row[2] == 1)
			{
				$precio = $row[4] . ' MXN';
			}
			
			$desc = "$row[10]";
			
			if (empty($desc)){$desc="Click en la imagen para mas informacion";}
        $body = '
			<meta property="og:url" content="https://www.ascgar.com/products_detail_nosesion.php?id='.$id.'" />
        <meta property="og:type"  content="article" />
        <meta property="og:title" content="'.$row[0].' $'.$precio.' " />
        <meta property="og:description" content="'.$desc.'" />
        <meta property="og:image" content="https://www.ascgar.com/images/'.$row[5].'" />
			';
		}
		return $body;
	}

	function EmpresaNombre ()
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM empresa ");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}

	function FooterPageReport ()
	{
		$data = mysqli_query(db_conectar(),"SELECT footer FROM empresa ");
		
		while($row = mysqli_fetch_array($data))
	    {
	        $body .= $row[0];
	    }
		return $body;
	}

	function Sale_Descuento ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT descuento FROM `folio_venta` where folio = '$folio'");
		$r = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $r = $row[0];
	    }
		return $r;
	}

	function ProductStock ($id)
	{
		$stock_db = 0;

		$data = mysqli_query(db_conectar(),"SELECT stock FROM `productos` where id = '$id' ");
		
		while($row = mysqli_fetch_array($data))
	    {
	        $stock_db = $row[0];
		}
		
		
		return $stock_db;
	}

	function ProductStock_hijo ($id)
	{
		$stock_db = 0;

		$data = mysqli_query(db_conectar(),"SELECT stock FROM `productos_sub` where id = '$id' ");
		
		while($row = mysqli_fetch_array($data))
	    {
	        $stock_db = $row[0];
		}
		
		
		return $stock_db;
	}
	
	function ProductStock_SaleUnidad ($produc, $unidades)
	{
		$data = mysqli_query(db_conectar(),"SELECT stock FROM `productos` where id = '$produc' ");
		
		while($row = mysqli_fetch_array($data))
	    {
	        $stock_db = $row[0];
		}
		
		$r = false;

		if ($unidades <= $stock_db)
		{
			$r = true;
		}

		return $r;
	}

	function CompareFolioOpen ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT `open` FROM `folio_venta` where folio = '$folio' ");
		
		while($row = mysqli_fetch_array($data))
	    {
	        $open = $row[0];
		}
		
		if ($open == 0)
		{
			echo '<script>location.href = "products.php?pagina=1"</script>';
		}
	}

	function ProductVentaStock_SaleUnidad ($id, $unidades)
	{
		$data = mysqli_query(db_conectar(),"SELECT p.stock FROM product_venta v, productos p WHERE v.product = p.id and v.id = '$id' ");
		
		while($row = mysqli_fetch_array($data))
	    {
	        $stock_db = $row[0];
		}
		
		$r = false;

		if ($unidades <= $stock_db)
		{
			$r = true;
		}

		return $r;
	}

	function DepartamentosReturnNombre ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM departamentos where id = $id ");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}

	function AlmacenReturnNombre ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre FROM almacen where id = $id ");
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $body = $row[0];
	    }
		return $body;
	}

	function table_departamento ()
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `departamentos` ORDER by nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modaldepartament_edit'.$row[0].'" ><span> Editar</span> </a>
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modaldepartament_delete'.$row[0].'" ><span> Eliminar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		
		return $body;
	}

	
	function table_sale_products_finaly_ ($folio)
	{
		$permiso_gest_products = $_SESSION['product_gest'];
		
		$data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock FROM product_venta v, productos _p WHERE v.product = _p.id and v.folio_venta = '$folio' ");
		$data_ = mysqli_query(db_conectar(),"SELECT v.nombre, c.nombre, f.descuento, f.fecha, f.iva  FROM folio_venta f, users v, clients c WHERE f.vendedor = v.id and f.client = c.id and f.folio = '$folio' ");
		$genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");

		$total = 0;
		$total_productos = 0;

		$vendedor = "";
		$cliente = "";
		$descuento = 0;

		$body = '<!-- Start Wishlist Area -->
		<div class="wishlist-area" style="background-color: #f5f5f5;">
		<div class="container">
		<div class="row">
			<div class="col-md-11">
				<div class="wishlist-content">
						<div class="wishlist-table table-responsive p-30 text-uppercase">
							<table>
								<thead>
									<tr>
										<th class="product-thumbnail"></th>
										<th class="product-name"><span class="nobr">Producto</span></th>
										<th class="product-prices"><span class="nobr">Precio </span></th>
										<th class="product-add-to-cart"><span class="nobr">Unidades </span></th>
										<th class="product-remove"><span class="nobr">Quitar</span></th>
									</tr>
								</thead>
								<tbody>';
		while($row = mysqli_fetch_array($data_))
		{
			$vendedor = $row[0];
			$cliente = $row[1];
			$descuento = $row[2];
			$fecha = $row[3];
			$iva = $row[4];
		}

		while($row = mysqli_fetch_array($data))
	    {
			$total = $total + ($row[2] * $row[0]);
			$total_productos = $total_productos + $row[0];
            
            $body_costo = "";
            if ($permiso_gest_products)
            {
                $body_costo = '<input type="text" name="costo" id="costo"  value="'.$row["2"].'" style="text-align:center;" >';
            }else
            {
                $body_costo = '<span class="amount">$ '.$row[2].' MXN</span>';
            }
            
			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>
					'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>
					'.$row[8].'
				</p>
			</td>
			
			<form action="func/product_sale_update.php" method="post">	
			    <td class="product-prices">
			        '.$body_costo.'
			    </td>
		    <td class="product-value">
				<input type="hidden" id="id" name="id" value="'.$row[3].'">
				<div class="col-md-12">
					<div class="col-md-8">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<input type="number" step="1" name="unidades" id="unidades" min="1" max="'.$row[9].'" value="'.$row[0].'" style="text-align:center;">
					</div>
					<div class="col-md-4">
					<button type="submit" class="btn btn-primary"><i class="zmdi zmdi-upload"></i></button>
					</div>
				</div>

			</form>

			</td>
			<td class="product-remove">
			<a href="#" data-toggle="modal" data-target="#modalsalequit'.$row[3].'" >X</a>
			</td>
		</tr>
			';
		}

		//Genericos
		while($row = mysqli_fetch_array($genericos))
	    {
			$total = $total + ($row[0] * $row[2]);
			$total_productos = $total_productos + $row[0];

            $body_costo = "";
            if ($permiso_gest_products)
            {
                $body_costo = '<input type="text" name="costo" id="costo"  value="'.$row["2"].'" style="text-align:center;" >';
            }else
            {
                $body_costo = '<span class="amount">$ '.$row[2].' MXN</span>';
            }
            
			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank"  title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>NA'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>NA
				</p>
			</td>
			
			<form action="func/product_sale_update.php" method="post">	
				<input type="hidden" id="id" name="id" value="'.$row[3].'">
				
				<td class="product-prices">
			        '.$body_costo.'
			    </td>
		        <td class="product-value">
				
				<div class="col-md-12">
					<div class="col-md-8">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<input type="number" step="1" name="unidades" id="unidades" min="1" " value="'.$row[0].'" style="text-align:center;">
					</div>
					<div class="col-md-4">
					<button type="submit" class="btn btn-primary"><i class="zmdi zmdi-upload"></i></button>
					</div>
				</div>

			</form>

			</td>
			<td class="product-remove">
			<a href="#" data-toggle="modal" data-target="#modalsalequit'.$row[3].'" >X</a>
			</td>
		</tr>
			';
		}
		
		$ivac = '.'.$iva;

		$total_ = number_format($total,GetNumberDecimales(),".",",");

		$pagar = $total * ($descuento / 100);

		$total_desc = $pagar;

		$pagar = $total - $pagar;

        $pagarUsd = GetUsdToMXN($pagar);
        
		$subtotal = number_format(($pagar / 1.160000),GetNumberDecimales(),".",",");

		$iva_ = number_format($pagar - ($pagar / 1.160000),GetNumberDecimales(),".",",");
		
		$pagar = number_format($pagar,GetNumberDecimales(),".",",");
		
		$pagarUsd = number_format($pagarUsd,GetNumberDecimales(),".",",");

		$ShowTotalDesc = "";

		if ($total_desc > 0)
		{
			$ShowTotalDesc = '
			<tr class="cart-total">
				<th>Total</th>
				<td>$ '.$total_.' MXN</td>
			</tr>
			<tr class="cart-shipping">
				<th> - '.$descuento.' % Desc.</th>
				<td>$ '.$total_desc.' MXN</td>
			</tr>';
		}else
		{
			$ShowTotalDesc = '
			<tr class="cart-shipping">
				<th>Total</th>
				<td>$ '.$total_.' MXN</td>
			</tr>';
		}
		
		$ShowIva = "";

		if (DesglosarReportIva())
		{
			$ShowIva = '
			<tr class="cart-total">
				<th>Subtotal</th>
				<td>$ '.$subtotal.' MXN</td>
			</tr>
			<tr class="cart-shipping">
				<th> iva '.$iva.' %</th>
				<td>$ '.$iva_.' MXN</td>
			</tr>
			';
		}

		$body = $body . '
			</tbody>
			</table>
		</div>


		<div class="row">
		<div class="cart-requerment mt-50 clearfix">
			<div class="col-md-4 col-sm-6 clearfix">
				
			</div> 
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>INFORMACION</strong></h5>
					</div>
					<p>CLIENTE: '.$cliente.'</p>
					<p>VENDEDOR: '.$vendedor.'</p>
					<p>CREADO: '.GetFechaText($fecha).'</p>
					
				</div>
			</div> 
			<div class="col-md-offset-0 col-md-4 col-sm-offset-3 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>TOTALES</strong></h5>
					</div>
					<table>
						<tbody>
						<tr class="cart-total">
						<th>Productos</th>
						<td>'.$total_productos.' Unidades</td>
					</tr>
						'.$ShowTotalDesc.'
						'.$ShowIva.'
						<tr class="cart-total">
							<th><b>Pagar</b></th>
							<td><b>$ '.$pagar.' MXN</b></td>
						</tbody>
					</table> 
				</div>
			</div>                                            
		</div>
	</div>  
	</div>                            
	</div>
	</div>

	</div><br>
	</div>
		';
		return $body;
	}

    function table_sale_products_finaly_cfdi ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock, _p.cv, _p.um FROM product_venta v, productos _p WHERE v.product = _p.id and v.folio_venta = '$folio' ");
		$data_ = mysqli_query(db_conectar(),"SELECT v.nombre, c.nombre, f.descuento, f.fecha, f.iva FROM folio_venta f, users v, clients c WHERE f.vendedor = v.id and f.client = c.id and f.folio = '$folio' ");
		$genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");

		$total = 0;
		$total_productos = 0;

		$vendedor = "";
		$cliente = "";
		$descuento = 0;
		$fecha = "";

		$body = '<!-- Start Wishlist Area -->
		<div class="wishlist-area" style="background-color: #f5f5f5;">
		<div class="container">
		<div class="row">
			<div class="col-md-11">
				<div class="wishlist-content">
						<div class="wishlist-table table-responsive p-30 text-uppercase">
							<table>
								<thead>
									<tr>
										<th class="product-thumbnail"></th>
										<th class="product-name"><span class="nobr">Producto</span></th>
										<th class="product-prices"><span class="nobr">Precio </span></th>
										<th class="product-add-to-cart"><span class="nobr"><center>Unidades</center></span></th>
                                        <th class="product-add-to-cart"><span class="nobr">Clave sat </span></th>
                                        <th class="product-add-to-cart"><span class="nobr">U. Medida </span></th>
									</tr>
								</thead>
								<tbody>';
		while($row = mysqli_fetch_array($data_))
		{
			$vendedor = $row[0];
			$cliente = $row[1];
			$descuento = $row[2];
			$fecha = $row[3];
			$iva = $row[4];
		}

		while($row = mysqli_fetch_array($data))
	    {
			$total = $total + ($row[2] * $row[0]);
			$total_productos = $total_productos + $row[0];

			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>
					'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>
					'.$row[8].'
				</p>
			</td>
			<td class="product-prices"><span class="amount">$ '.$row[2].' MXN</span></td>
            <td class="product-prices"><span class="amount"><center>'.$row[0].'</center></span></td>
            <td class="product-prices"><span class="amount">'.$row[10].'</span></td>
            <td class="product-prices"><span class="amount">'.$row[11].'</span></td>
		</tr>
			';
		}

		//Genericos
		while($row = mysqli_fetch_array($genericos))
	    {
			$total = $total + ($row[0] * $row[2]);
			$total_productos = $total_productos + $row[0];

			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank"  title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>NA'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>NA
				</p>
			</td>
			<td class="product-prices"><span class="amount">$ '.$row[2].' MXN</span></td>
            <td class="product-prices"><span class="amount"><center>'.$row[0].'</center></span></td>
            <td class="product-prices"><span class="amount">01010101</span></td>
            <td class="product-prices"><span class="amount">H87</span></td>
			
		</tr>
			';
		}
		
		$ivac = '0.'.$iva;

		$total_ = number_format($total,GetNumberDecimales(),".",",");

		$pagar = $total * ($descuento / 100);

		$total_desc = $pagar;

		$pagar = $total - $pagar;

		$subtotal = number_format(($pagar / 1.160000),GetNumberDecimales(),".",",");

		$iva_ = number_format($pagar - ($pagar / 1.160000),GetNumberDecimales(),".",",");
		
		$pagar = number_format($pagar,GetNumberDecimales(),".",",");

		$body = $body . '
			</tbody>
			</table>
		</div>


		<div class="row">
		<div class="cart-requerment mt-50 clearfix">
			<div class="col-md-4 col-sm-6 clearfix">
				
			</div> 
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>INFORMACION</strong></h5>
					</div>
					<p>CLIENTE: '.$cliente.'</p>
					<p>VENDEDOR: '.$vendedor.'</p>
					<p>CREADO: '.$fecha.'</p>                                      
				</div>
			</div> 
			<div class="col-md-offset-0 col-md-4 col-sm-offset-3 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>TOTALES</strong></h5>
					</div>
					<table>
						<tbody>
						<tr class="cart-total">
						<th>Productos</th>
						<td>'.$total_productos.' Unidades</td>
					</tr>
						<tr class="cart-total">
							<th>Total</th>
							<td>$ '.$total_.' MXN</td>
						</tr>
						<tr class="cart-shipping">
							<th> - '.$descuento.' % Desc.</th>
							<td>$ '.$total_desc.' MXN</td>
						</tr>
						<tr class="cart-total">
							<th>Subtotal</th>
							<td>$ '.$subtotal.' MXN</td>
						</tr>
						<tr class="cart-shipping">
							<th> iva '.$iva.' %</th>
							<td>$ '.$iva_.' MXN</td>
						</tr>
						<tr class="cart-total">
							<th>Pagar</th>
							<td>$ '.$pagar.' MXN</td>
						</tr>
						</tbody>
					</table> 
				</div>
			</div>                                            
		</div>
	</div>  
	</div>                            
	</div>
	</div>
	</div>
	</div>
		';
		return $body;
	}

	function table_sale_products_finaly_order ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock FROM product_pedido v, productos _p WHERE v.product = _p.id and  v.folio_venta = '$folio' ");
		$data_ = mysqli_query(db_conectar(),"SELECT v.nombre, c.nombre, f.descuento, f.fecha, f.iva FROM folio_venta f, users v, clients c WHERE f.vendedor = v.id and f.client = c.id and f.folio = '$folio' ");
		$genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_pedido v WHERE p_generico != '' and folio_venta = '$folio'");
		$abonos = mysqli_query(db_conectar(),"SELECT folio, cobrado, fecha_venta FROM folio_venta WHERE folio_venta_ini = '$folio'");

		
		$total = 0;
		$total_productos = 0;
		$total_abono = 0;
		$vendedor = "";
		$cliente = "";
		$descuento = 0;
		$fecha = "";

		$body = '
						
		</a>                                                 
		<!-- Start Wishlist Area -->
		<div class="wishlist-area" style="background-color: #f5f5f5;">
		<div class="container">
		<div class="row">
			<div class="col-md-11">
				<div class="wishlist-content">
						<div class="wishlist-table table-responsive p-30 text-uppercase">
							<table>
								<thead>
									<tr>
										<th class="product-thumbnail"></th>
										<th class="product-name"><span class="nobr">Producto</span></th>
										<th class="product-prices"><span class="nobr">Precio </span></th>
										<th class="product-add-to-cart"><span class="nobr">Unidades </span></th>
										<th class="product-remove"><span class="nobr">Quitar</span></th>
									</tr>
								</thead>
								<tbody>';
		while($row = mysqli_fetch_array($data_))
		{
			$vendedor = $row[0];
			$cliente = $row[1];
			$descuento = $row[2];
			$fecha = $row[3];
			$iva = $row[4];
		}

		while($row = mysqli_fetch_array($abonos))
		{
			$pagos .= '<p>$ '.$row[1].' MXN - '.$row[2].'</p>';
			$total_abono = $total_abono + $row[1];
		}

		
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['super_pedidos'] == 1)
			{
				$unidades_update = '
				<form action="func/product_sale_update_order.php" method="post">	
				<input type="hidden" id="id" name="id" value="'.$row[3].'">
				<div class="col-md-12">
					<div class="col-md-8">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<input type="number" name="unidades" id="unidades" min="1" value="'.$row[0].'" style="text-align:center;">
					</div>
					<div class="col-md-4">
					<button type="submit" class="btn btn-primary"><i class="zmdi zmdi-upload"></i></button>
					</div>
				</div>

			</form>
				';
				$quitar = '<a href="#" data-toggle="modal" data-target="#modalsalequit'.$row[3].'" >X</a>';
			}

			$total = $total + ($row[2] * $row[0]);
			$total_productos = $total_productos + $row[0];

			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>
					'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>
					'.$row[8].'
				</p>
			</td>
			<td class="product-prices"><span class="amount">$ '.$row[2].' MXN</span></td>
			<td class="product-value">
			'.$unidades_update.'
			</td>
			<td class="product-remove">
			'.$quitar.'
			</td>
		</tr>
			';
		}

		//Genericos
		while($row = mysqli_fetch_array($genericos))
	    {
			$total = $total + ($row[0] * $row[2]);
			$total_productos = $total_productos + $row[0];

			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank"  title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>NA'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>NA
				</p>
			</td>
			<td class="product-prices"><span class="amount">$ '.$row[2].' MXN</span></td>
			<td class="product-value">
			
			<form action="func/product_sale_update_order.php" method="post">	
				<input type="hidden" id="id" name="id" value="'.$row[3].'">
				<div class="col-md-12">
					<div class="col-md-8">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<input type="number" name="unidades" id="unidades" min="1" value="'.$row[0].'" style="text-align:center;">
					</div>
					<div class="col-md-4">
					<button type="submit" class="btn btn-primary"><i class="zmdi zmdi-upload"></i></button>
					</div>
				</div>

			</form>

			</td>
			<td class="product-remove">
			<a href="#" data-toggle="modal" data-target="#modalsalequit'.$row[3].'" >X</a>
			</td>
		</tr>
			';
		}
		
		$ivac = '0.'.$iva;

		$total_ = number_format($total,GetNumberDecimales(),".",",");

		$pagar = $total * ($descuento / 100);

		$total_desc = $pagar;

		$pagar = $total - $pagar;

		$tt = $pagar - $total_abono;

		$subtotal = number_format(($pagar / 1.160000),GetNumberDecimales(),".",",");

		$iva_ = number_format($pagar - ($pagar / 1.160000),GetNumberDecimales(),".",",");
		
		$pagar = number_format($pagar,GetNumberDecimales(),".",",");
		
		$tt = number_format($tt,GetNumberDecimales(),".",",");
		
		$ShowTotalDesc = "";

		if ($total_desc > 0)
		{
			$ShowTotalDesc = '
			<tr class="cart-total">
				<th>Total</th>
				<td>$ '.$total_.' MXN</td>
			</tr>
			<tr class="cart-shipping">
				<th> - '.$descuento.' % Desc.</th>
				<td>$ '.$total_desc.' MXN</td>
			</tr>';
		}else
		{
			$ShowTotalDesc = '
			<tr class="cart-shipping">
				<th>Total</th>
				<td>$ '.$total_.' MXN</td>
			</tr>';
		}

		$ShowIva = "";

		if (DesglosarReportIva())
		{
			$ShowIva = '
			<tr class="cart-total">
				<th>Subtotal</th>
				<td>$ '.$subtotal.' MXN</td>
			</tr>
			<tr class="cart-shipping">
				<th> iva '.$iva.' %</th>
				<td>$ '.$iva_.' MXN</td>
			</tr>
			';
		}

		$body = $body . '
			</tbody>
			</table>
		</div>


		<div class="row">
		<div class="cart-requerment mt-50 clearfix">
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>INFORMACION</strong></h5>
					</div>
					<p>CLIENTE: '.$cliente.'</p>
					<p>VENDEDOR: '.$vendedor.'</p>
					<p>CREADO: '.$fecha.'</p>                                      
				</div>
			</div> 
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
				<div class="cart-title text-uppercase">
					<h5 class="mb-30"><strong>Lista de Pagos/Abonos</strong></h5>
				</div>
				'.$pagos.'
				<tr class="cart-total">
					<th>Total abonos</th>
					<td>$ '.$total_abono.' MXN</td>
				</tr>
			</div>
			</div> 
			<div class="col-md-offset-0 col-md-4 col-sm-offset-3 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>TOTALES</strong></h5>
					</div>
					<table>
						<tbody>
						<tr class="cart-total">
						<th>Productos</th>
						<td>'.$total_productos.' Unidades</td>
					</tr>
						'.$ShowTotalDesc.'
						'.$ShowIva.'
						<tr class="cart-total">
							<th>Abonos</th>
							<td>$ '.$total_abono.' MXN</td>
						</tr>
						<tr class="cart-total">
							<th><b>Adeudo</b></th>
							<td><b>$ '.$tt.' MXN</b></td>
						</tr>
						</tbody>
					</table>
					
				</div>
			</div>                                            
		</div>
	</div>  
	</div>                            
	</div>
	</div>
	</div>
	</div>
		';
		return $body;
	}

    function table_sale_products_finaly_order_cfdi ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock, _p.cv, _p.um FROM product_pedido v, productos _p WHERE v.product = _p.id and  v.folio_venta = '$folio' ");
		$data_ = mysqli_query(db_conectar(),"SELECT v.nombre, c.nombre, f.descuento, f.fecha, f.iva FROM folio_venta f, users v, clients c WHERE f.vendedor = v.id and f.client = c.id and f.folio = '$folio' ");
		$genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_pedido v WHERE p_generico != '' and folio_venta = '$folio'");
		$abonos = mysqli_query(db_conectar(),"SELECT folio, cobrado, fecha_venta FROM folio_venta WHERE folio_venta_ini = '$folio'");

		
		$total = 0;
		$total_productos = 0;
		$total_abono = 0;
		$vendedor = "";
		$cliente = "";
		$descuento = 0;
		$fecha = "";

		$body = '
						
		</a>                                                 
		<!-- Start Wishlist Area -->
		<div class="wishlist-area" style="background-color: #f5f5f5;">
		<div class="container">
		<div class="row">
			<div class="col-md-11">
				<div class="wishlist-content">
						<div class="wishlist-table table-responsive p-30 text-uppercase">
							<table>
								<thead>
									<tr>
										<th class="product-thumbnail"></th>
										<th class="product-name"><span class="nobr">Producto</span></th>
										<th class="product-prices"><span class="nobr">Precio </span></th>
										<th class="product-add-to-cart"><span class="nobr"><center>Unidades </center></span></th>
										<th class="product-remove"><span class="nobr">Clave sat</span></th>
                                        <th class="product-remove"><span class="nobr">U. Medida</span></th>
									</tr>
								</thead>
								<tbody>';
		while($row = mysqli_fetch_array($data_))
		{
			$vendedor = $row[0];
			$cliente = $row[1];
			$descuento = $row[2];
			$fecha = $row[3];
			$iva = $row[4];
		}

		while($row = mysqli_fetch_array($abonos))
		{
			$pagos .= '<p>$ '.$row[1].' MXN - '.$row[2].'</p>';
			$total_abono = $total_abono + $row[1];
		}

		
		while($row = mysqli_fetch_array($data))
	    {
			$total = $total + ($row[2] * $row[0]);
			$total_productos = $total_productos + $row[0];

			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>
					'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>
					'.$row[8].'
				</p>
			</td>
			<td class="product-prices"><span class="amount">$ '.$row[2].' MXN</span></td>
			<td class="product-value">
			<center>'.$row[0].'</center>
			</td>
			<td class="product-remove">
			'.$row[10].'
			</td>
            <td class="product-remove">
			'.$row[11].'
			</td>
		</tr>
			';
		}

		//Genericos
		while($row = mysqli_fetch_array($genericos))
	    {
			$total = $total + ($row[0] * $row[2]);
			$total_productos = $total_productos + $row[0];

			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank"  title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>NA'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>NA
				</p>
			</td>
			<td class="product-prices"><span class="amount">$ '.$row[2].' MXN</span></td>
			<td class="product-prices"><span class="amount"><center>'.$row[0].'</center>
			</td>
			<td class="product-remove">01010101</td>
            <td class="product-remove">H87</td>
		</tr>
			';
		}
		
		$ivac = '1.'.$iva;

		$total_ = number_format($total,GetNumberDecimales(),".",",");

		$pagar = $total * ($descuento / 100);

		$total_desc = $pagar;

		$pagar = $total - $pagar;

		$tt = $pagar - $total_abono;

		$subtotal = number_format($pagar / $ivac,GetNumberDecimales(),".",",");

		$iva_ = number_format($pagar - ($pagar / $ivac),GetNumberDecimales(),".",",");
		
		$pagar = number_format($pagar,GetNumberDecimales(),".",",");
		
		$tt = number_format($tt,GetNumberDecimales(),".",",");
		
		
		$body = $body . '
			</tbody>
			</table>
		</div>


		<div class="row">
		<div class="cart-requerment mt-50 clearfix">
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>INFORMACION</strong></h5>
					</div>
					<p>CLIENTE: '.$cliente.'</p>
					<p>VENDEDOR: '.$vendedor.'</p>
					<p>CREADO: '.$fecha.'</p>                                      
				</div>
			</div> 
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
				<div class="cart-title text-uppercase">
					<h5 class="mb-30"><strong>Lista de Pagos/Abonos</strong></h5>
				</div>
				'.$pagos.'
				<tr class="cart-total">
					<th>Total abonos</th>
					<td>$ '.$total_abono.' MXN</td>
				</tr>
			</div>
			</div> 
			<div class="col-md-offset-0 col-md-4 col-sm-offset-3 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>TOTALES</strong></h5>
					</div>
					<table>
						<tbody>
						<tr class="cart-total">
						<th>Productos</th>
						<td>'.$total_productos.' Unidades</td>
					</tr>
						<tr class="cart-total">
							<th>Total</th>
							<td>$ '.$total_.' MXN</td>
						</tr>
						<tr class="cart-shipping">
							<th> - '.$descuento.' % Desc.</th>
							<td>$ '.$total_desc.' MXN</td>
						</tr>
						<tr class="cart-total">
							<th>Subtotal</th>
							<td>$ '.$subtotal.' MXN</td>
						</tr>
						<tr class="cart-shipping">
							<th> iva '.$iva.' %</th>
							<td>$ '.$iva_.' MXN</td>
						</tr>
						<tr class="cart-total">
							<th>Total</th>
							<td>$ '.$pagar.' MXN</td>
						</tr>
						<tr class="cart-shipping">
							<th>Abonos</th>
							<td>$ '.$total_abono.' MXN</td>
						</tr>
						<tr class="cart-total">
							<th>Adeudo</th>
							<td>$ '.$tt.' MXN</td>
						</tr>
						</tbody>
					</table>
					
				</div>
			</div>                                            
		</div>
	</div>  
	</div>                            
	</div>
	</div>
	</div>
	</div>
		';
		return $body;
	}

	function table_sale_products_finaly_cotizacion ($folio)
	{
		$permiso_gest_products = $_SESSION['product_gest'];
		
		$data = mysqli_query(db_conectar(),"SELECT v.unidades, _p.nombre, v.precio, v.id, _p.descripcion, _p.foto0, _p.id, _p.`no. De parte`, _p.marca, _p.stock FROM product_venta v, productos _p WHERE v.product = _p.id and v.folio_venta = '$folio' ");
		$data_ = mysqli_query(db_conectar(),"SELECT v.nombre, c.nombre, f.descuento, f.fecha, f.iva FROM folio_venta f, users v, clients c WHERE f.vendedor = v.id and f.client = c.id and f.folio = '$folio' ");
		$genericos = mysqli_query(db_conectar(),"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");
		
		$total = 0;
		$total_productos = 0;

		$vendedor = "";
		$cliente = "";
		$descuento = 0;
		$fecha = "";

		$body = '<!-- Start Wishlist Area -->
		<div class="wishlist-area" style="background-color: #f5f5f5;">
		<div class="container">
		<div class="row">
			<div class="col-md-11">
				<div class="wishlist-content">
						<div class="wishlist-table table-responsive p-30 text-uppercase">
							<table>
								<thead>
									<tr>
										<th class="product-thumbnail"></th>
										<th class="product-name"><span class="nobr">Producto</span></th>
										<th class="product-prices"><span class="nobr">Precio </span></th>
										<th class="product-add-to-cart"><span class="nobr">Unidades </span></th>
										<th class="product-remove"><span class="nobr">Quitar</span></th>
									</tr>
								</thead>
								<tbody>';
		while($row = mysqli_fetch_array($data_))
		{
			$vendedor = $row[0];
			$cliente = $row[1];
			$descuento = $row[2];
			$fecha = $row[3];
			$iva = $row[4];
		}

		while($row = mysqli_fetch_array($data))
	    {
			$total = $total + ($row[2] * $row[0]);
			$total_productos = $total_productos + $row[0];

            $body_costo = "";
            if ($permiso_gest_products)
            {
                $body_costo = '<input type="text" name="costo" id="costo"  value="'.$row["2"].'" style="text-align:center;" >';
            }else
            {
                $body_costo = '<span class="amount">$ '.$row[2].' MXN</span>';
            }


			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank" href="products_detail.php?id='.$row[6].'" title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>
					'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>
					'.$row[8].'
				</p>
			</td>
			
			
			<form action="func/product_sale_update.php" method="post">	
				<input type="hidden" id="id" name="id" value="'.$row[3].'">

				<td class="product-prices">
			        '.$body_costo.'
			    </td>
		        <td class="product-value">

				<div class="col-md-12">
					<div class="col-md-8">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<input type="number" name="unidades" id="unidades" min="1" max="'.$row[9].'" value="'.$row[0].'" style="text-align:center;">
					</div>
					<div class="col-md-4">
					<button type="submit" class="btn btn-primary"><i class="zmdi zmdi-upload"></i></button>
					</div>
				</div>

			</form>

			</td>
			<td class="product-remove">
			<a href="#" data-toggle="modal" data-target="#modalsalequit'.$row[3].'" >X</a>
			</td>
		</tr>
			';
		}

		//Genericos
		while($row = mysqli_fetch_array($genericos))
	    {
			$total = $total + ($row[0] * $row[2]);
			$total_productos = $total_productos + $row[0];

            $body_costo = "";
            if ($permiso_gest_products)
            {
                $body_costo = '<input type="text" name="costo" id="costo"  value="'.$row["2"].'" style="text-align:center;" >';
            }else
            {
                $body_costo = '<span class="amount">$ '.$row[2].' MXN</span>';
            }
            
			$body = $body.
			'
			<tr>
			<td class="product-thumbnail"><a target="_blank" title="'.$row[1].'"><img src="images/'.$row[5].'" alt="" height="110" width="110" /></a></td>
			<td class="product-name pull-left mt-20">
				<a target="_blank"  title="'.$row[4].'">'.$row[1].'</a>
				<p class="w-color m-0">
					<label> No. parte :</label>NA'.$row[7].'
				</p>
				<p class="w-size m-0">
					<label> Marca :</label>NA
				</p>
			</td>
			
			
			<form action="func/product_sale_update.php" method="post">	
				<input type="hidden" id="id" name="id" value="'.$row[3].'">
				
				<td class="product-prices">
			        '.$body_costo.'
			    </td>
		        <td class="product-value">


                <div class="col-md-12">
					<div class="col-md-8">
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<input type="number" name="unidades" id="unidades" min="1" " value="'.$row[0].'" style="text-align:center;">
					</div>
					<div class="col-md-4">
					<button type="submit" class="btn btn-primary"><i class="zmdi zmdi-upload"></i></button>
					</div>
				</div>

			</form>

			</td>
			<td class="product-remove">
			<a href="#" data-toggle="modal" data-target="#modalsalequit'.$row[3].'" >X</a>
			</td>
		</tr>
			';
		}

		$ivac = '.'.$iva;

		$total_ = number_format($total,GetNumberDecimales(),".",",");

		$pagar = $total * ($descuento / 100);

		$total_desc = $pagar;

		$pagar = $total - $pagar;
        
        $PagarUsd = GetUsdToMXN($pagar);
        
		$subtotal = number_format(($pagar / 1.160000),GetNumberDecimales(),".",",");

		$iva_ = number_format($pagar - ($pagar / 1.160000),GetNumberDecimales(),".",",");
		
		$pagar = number_format($pagar,GetNumberDecimales(),".",",");
		
		$PagarUsd = number_format($PagarUsd,GetNumberDecimales(),".",",");
		
		$ShowTotalDesc = "";

		if ($total_desc > 0)
		{
			$ShowTotalDesc = '
			<tr class="cart-total">
				<th>Total</th>
				<td>$ '.$total_.' MXN</td>
			</tr>
			<tr class="cart-shipping">
				<th> - '.$descuento.' % Desc.</th>
				<td>$ '.$total_desc.' MXN</td>
			</tr>';
		}else
		{
			$ShowTotalDesc = '
			<tr class="cart-shipping">
				<th>Total</th>
				<td>$ '.$total_.' MXN</td>
			</tr>';
		}

		$ShowIva = "";

		if (DesglosarReportIva())
		{
			$ShowIva = '
			<tr class="cart-total">
				<th>Subtotal</th>
				<td>$ '.$subtotal.' MXN</td>
			</tr>
			<tr class="cart-shipping">
				<th> iva '.$iva.' %</th>
				<td>$ '.$iva_.' MXN</td>
			</tr>
			';
		}

		$body = $body . '
			</tbody>
			</table>
		</div>


		<div class="row">
		<div class="cart-requerment mt-50 clearfix">
			<div class="col-md-4 col-sm-6 clearfix">
				
			</div> 
			<div class="col-md-4 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>INFORMACION</strong></h5>
					</div>
					<p>CLIENTE: '.$cliente.'</p>
					<p>VENDEDOR: '.$vendedor.'</p>
					<p>CREADO: '.$fecha.'</p>                                      
				</div>
			</div> 
			<div class="col-md-offset-0 col-md-4 col-sm-offset-3 col-sm-6 clearfix">
				<div class="counpon-total ml-35">
					<div class="cart-title text-uppercase">
						<h5 class="mb-30"><strong>TOTALES</strong></h5>
					</div>
					<table>
						<tbody>
							<tr class="cart-total">
								<th>Productos</th>
								<td>'.$total_productos.' Unidades</td>
							</tr>
							'.$ShowTotalDesc.'
							'.$ShowIva.'
							<tr class="cart-total">
								<th><b>Pagar</b></th>
								<td><b>$ '.$pagar.' MXN </b></td>
							</tr>
						</tbody>
					</table> 
				</div>
			</div>                                            
		</div>
	</div>  
	</div>                            
	</div>
	</div>
	</div>
	</div>
		';
		return $body;
	}

	function table_clientes ($pagina)
	{
		$TAMANO_PAGINA = 10;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, if (direccion = '' , 'DIRECCION DESCONOCIDA', direccion) as  direccion, if (telefono = '' , 'TELEFONO DESCONOCIDO', telefono) AS telefono, if (razon_social  = '' , 'RAZON SOCIAL DESCONOCIDA', razon_social  ) AS razon_social FROM `clients` ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		
		$hoy = date("Y-m-d");
		
		$body = '<br>
		<form class="header-search-box" action="clients.php">
			<div>
				<input type="hidden" id="pagina" name="pagina" value="1">
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              ">
			</div>
		</form><br>
		'.$pagination.'
		<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">NOMBRE CLIENTE</th>
							<th class="table-head item-nam">DIRECCION</th>
							<th class="table-head item-nam">TELEFONO</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">EMAIL</th>
							<th class="table-head item-nam">EDITAR</th>
							<th class="table-head item-nam">ELIMINAR</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body;

		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['client_guest'] == 1)
			{
				$boton = '
				<td class="item-des"><center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#mailcliente'.$row[0].'"><i class="zmdi zmdi-mail-send zmdi-hc-2x"></i></a></center></td>
				<td class="item-des"><center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalclient_edit'.$row[0].'" ><span> Editar</span> </a></p></center></td>
				<td class="item-des"><center><p><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalclient_delete'.$row[0].'" ><span> Eliminar</span> </a></p></center></td>
				';
			}else {
				// No pueden editar
				$boton = '
				<td class="item-des"><center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#mailcliente'.$row[0].'"><i class="zmdi zmdi-plus zmdi-hc-2x"></i></a></center></td>
				<td class="item-des"><center><a class="button extra-small button-black mb-20" data-toggle="modal"><span> Editar</span> </a></p></center></td>
				<td class="item-des"><center><p><a class="button extra-small button-black mb-20"><span> Eliminar</span> </a></p></center></td>
				';
			}


			$body = $body.'
			<tr>
			<td class="item-quality"><a href="/finance_clients.php?inicio=2013-05-29&finaliza='.$hoy.'&usuario=0&sucursal=0&client='.$row[0].'">'.$row[1].'</a></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			'.$boton.'
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>';

		$body = $body . $pagination;
		return $body;
	}

    function table_facturas_search ($txt, $pagina)
	{
		$TAMANO_PAGINA = 8;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$data = mysqli_query(db_conectar(),"SELECT f.folio, f.serie, f.estatus, c.nombre FROM facturas f, clients c where f.cliente = c.id and f.folio LIKE '%$txt%' or f.cliente = c.id and c.nombre LIKE '%$txt%'  LIMIT $inicio, $TAMANO_PAGINA ");
		$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM facturas f, clients c where f.cliente = c.id and f.folio LIKE '%$txt%' or f.cliente = c.id and c.nombre LIKE '%$txt%'");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body .= $pagination;

        $body .= '
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head item-nam">CLIENTE</th>
                            <th class="table-head item-nam">FOLIO FACTURA</th>
							<th class="table-head item-nam"><center>SERIE</center></th>
							<th class="table-head th-name uppercase"><center>ESTATUS</center></th>
                            <th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[3].'</p></td>
            <td class="item-des"><p><a href="/sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></p></td>
			<td class="item-des"><p><center>'.$row[1].'</center></p></td>
			<td class="item-quality"><center>'.$row[2].'</center></td>
            <td class="item-des">
                <center>
                <a href="/func/SDK2/timbrados/'.$row[0].'.pdf" target="_blank">
                    <i class="zmdi zmdi-eye zmdi-hc-2x"></i>
                </a>
                
                <a data-toggle="modal" data-target="#sendmail'.$row[0].'" >
                <i class="zmdi zmdi-mail-send zmdi-hc-2x"></i></a>
                
                <a data-toggle="modal" data-target="#cancelcfdi33'.$row[0].'" >
                <i class="zmdi zmdi-close zmdi-hc-2x"></i></a>
                    
                </a>
                </center>
            </td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

    function table_facturas ($pagina)
	{
		
		$TAMANO_PAGINA = 8;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT f.folio, f.serie, f.estatus, c.nombre FROM facturas f, clients c where f.cliente = c.id LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT folio FROM facturas");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		$body = '
		<div class="compare-wraper mt-0">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head item-nam">CLIENTE</th>
                            <th class="table-head item-nam">FOLIO FACTURA</th>
							<th class="table-head item-nam"><center>SERIE</center></th>
							<th class="table-head th-name uppercase"><center>ESTATUS</center></th>
                            <th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[3].'</p></td>
            <td class="item-des"><p><a href="/sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></p></td>
			<td class="item-des"><p><center>'.$row[1].'</center></p></td>
			<td class="item-quality"><center>'.$row[2].'</center></td>
            <td class="item-des">
                <center>
                <a href="/func/SDK2/timbrados/'.$row[0].'.pdf" target="_blank">
                    <i class="zmdi zmdi-eye zmdi-hc-2x"></i>
                </a>
                
                <a data-toggle="modal" data-target="#sendmail'.$row[0].'" >
                <i class="zmdi zmdi-mail-send zmdi-hc-2x"></i></a>
                
                <a data-toggle="modal" data-target="#cancelcfdi33'.$row[0].'" >
                <i class="zmdi zmdi-close zmdi-hc-2x"></i></a>
                    
                </a>
                </center>
            </td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function table_UsersModal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, imagen, product_add, product_gest, gen_orden_compra, client_add, client_guest, almacen_add, almacen_guest, depa_add, depa_guest, propiedades, usuarios, finanzas,change_suc, sucursal_gest, sucursal, descripcion, caja, super_pedidos, vtd_pg  FROM `users` ORDER by nombre asc");
		$permisos = '';
		$select = Select_sucursales();
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($row[3] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar producto
						<input type="checkbox" checked id="product_add" name="product_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar producto
						<input type="checkbox" id="product_add" name="product_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[4] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar producto
						<input type="checkbox" checked id="product_gest" name="product_gest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar producto
						<input type="checkbox" id="product_gest" name="product_gest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[5] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Generar orden de compra
						<input type="checkbox" checked id="gen_orden_compra"  name="gen_orden_compra">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Generar orden de compra
						<input type="checkbox" id="gen_orden_compra"  name="gen_orden_compra">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[6] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar cliente
						<input type="checkbox" checked id="client_add" name="client_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar cliente
						<input type="checkbox" id="client_add" name="client_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[7] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar clientes
						<input type="checkbox" checked id="client_guest" name="client_guest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar clientes
						<input type="checkbox" id="client_guest" name="client_guest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[8] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar almacen
						<input type="checkbox" checked name="almacen_add" id="almacen_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar almacen
						<input type="checkbox" name="almacen_add" id="almacen_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[9] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar almacen
						<input type="checkbox" checked name="almacen_guest" id="almacen_guest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar almacen
						<input type="checkbox" name="almacen_guest" id="almacen_guest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[10] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar departamento
						<input type="checkbox" checked id="depa_add" name="depa_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Agregar departamento
						<input type="checkbox" id="depa_add" name="depa_add">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[11] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar departamento
						<input type="checkbox" checked id="depa_guest" name="depa_guest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar departamento
						<input type="checkbox" id="depa_guest" name="depa_guest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[12] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Acceso a propiedades
						<input type="checkbox" checked id="propiedades" name="propiedades">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Acceso a propiedades
						<input type="checkbox" id="propiedades" name="propiedades">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[13] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Acceso a usuario
						<input type="checkbox" checked id="usuarios" name="usuarios">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Acceso a usuario
						<input type="checkbox" id="usuarios" name="usuarios">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[14] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Acceso a finanzas
						<input type="checkbox" checked id="finanzas" name="finanzas">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Acceso a finanzas
						<input type="checkbox" id="finanzas" name="finanzas">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[15] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Cambiar sucursal
						<input type="checkbox" checked id="change_suc" name="change_suc">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Cambiar sucursal
						<input type="checkbox" id="change_suc" name="change_suc">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[16] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar sucursal
						<input type="checkbox" checked id="sucursal_gest" name="sucursal_gest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Gestionar sucursal
						<input type="checkbox" id="sucursal_gest" name="sucursal_gest">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			if ($row[19] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Usar caja
						<input type="checkbox" checked id="caja" name="caja">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Usar caja
						<input type="checkbox" id="caja" name="caja">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}

			if ($row[20] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">Permitir ventas
						<input type="checkbox" checked id="super_pedidos" name="super_pedidos">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
				<label class="containeruser">Permitir ventas
						<input type="checkbox" id="super_pedidos" name="super_pedidos">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			
			if ($row[21] == 1)
			{
				$permisos .= '
				<div class="col-md-4">
					<label class="containeruser">VTD Personalizadas
						<input type="checkbox" checked id="vtd_pg" name="vtd_pg">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}else
			{
				$permisos .= '
				<div class="col-md-4">
				<label class="containeruser">VTD Personalizadas
						<input type="checkbox" id="vtd_pg" name="vtd_pg">
						<span class="checkmark"></span>
					</label>
				</div>
				';
			}
			
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="useredit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle"><img src = "images/'.$row[2].'" style="
					height: 50px;
					width: 50px;
					background-repeat: no-repeat;
					background-position: 50%;
					border-radius: 50%;
					background-size: 100% auto;
					"> '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/update_user.php" method="post" autocomplete="off" enctype="multipart/form-data">
					<div class="row">
					<input type="hidden" id="id" name="id" value="'.$row[0].'">
					
					<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
					<div class="col-md-12">
						<label>Nombre de usuario<span class="required">*</span></label>
						<input type="text" name="nombre" id="nombre" placeholder="Nombre o razon social" required value="'.$row[1].'">
					</div>
					<div class="country-select shop-select col-md-12">
						<br><label>Seleccione imagen si desea cambiarla<span class="required">*</span></label>
						<input type="file" name="imagen" id="imagen" accept="image/jpeg,image/jpg" >
					</div>
					<div class="col-md-12">
						<br>
						<label>Seleccione sucursal de venta predeterminada<span class="required">*</span></label>
						<select id="suc'.$row[0].'" name="suc'.$row[0].'">
							'. $select .'
						</select>
						<script>
							document.getElementById("suc'.$row[0].'").value = "'.$row[17].'";
						</script>
					
					</div>
					<div class="col-md-12">
						<br><label>Descripcion usuario</label>
						<input type="text" name="descripcion" id="descripcion" value="'.$row[18].'">
					</div>
					<div class="col-md-12">
						<br><label>Ingrese contraseña si desea cambiarla</label>
						<input type="password" name="pass1" id="pass1">
					</div>
					<div class="col-md-12">
						<br><label>Confirme contraseña</label>
						<input type="password" name="pass2" id="pass2">
					</div>
					<div class="col-md-12">
						<div class="section-title-2 text-uppercase mb-40 text-center">
							<br><h5>PERMISOS DE USUARIO</h5>
						</div>
					</div>
					'.$permisos.'
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="userdelete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR USUARIO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/user_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro de eliminar el usuario ? Al eliminarlo, todas sus ventas y registros seran borrados sin poder recuperarlo.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
			$permisos = '';
		}
		
		return $body;
	}

	function table_users()
	{
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, username  FROM `users` ORDER by nombre asc");
		$body = '
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">NOMBRE USUARIO</th>
							<th class="table-head th-name uppercase">USERNAME</th>
							<th class="table-head th-name uppercase">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#useredit'.$row[0].'" ><span> Editar</span> </a>
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#userdelete'.$row[0].'" ><span> Eliminar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function table_orders()
	{
		$data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.fecha FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.pedido = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id");
		$body = '
		<form class="header-search-box" action="orders.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off">
			</div>
		</form>
		<div class="table-responsive compare-wraper mt-30">
		<table class="cart table">
			<thead>
				<tr>
					<th class="table-head th-name uppercase">FOLIO PEDIDO</th>
					<th class="table-head th-name uppercase">vendedor</th>
					<th class="table-head th-name uppercase">cliente</th>
					<th class="table-head th-name uppercase">creado</th>
					<th class="table-head th-name uppercase">opciones</th>
				</tr>
			</thead>
			<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><a href="/sale_order.php?folio='.$row[0].'">'.$row[0].'</a></td>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des">'.$row[2].'</td>
			<td class="item-des">'.$row[3].'</td>
			
			<td class="item-des">
				<div class="col-md-12">
					
					<div class="col-md-6">
						<a href="/sale_order.php?folio='.$row[0].'" class="button extra-small button-black mb-20" ><span> Ver</span></a>
					</div>

					<div class="col-md-6">
					<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> +</span></a>
					</div>
					
				</div>
			</td>
			</tr>
			';
			/*
			<div class="col-md-12">
				<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> Opciones</span></a>
			</div>
			*/
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function table_cotizaciones()
	{
	    			
	    if ($_SESSION['propiedades'] > 0)
	    {
	        $data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.fecha FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.pedido = 0 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id order by f.fecha desc");    
	    }else
	    {
	        $data = mysqli_query(db_conectar(),'SELECT f.folio, u.nombre, c.nombre, f.fecha FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.pedido = 0 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and f.vendedor = '.$_SESSION['users_id']. ' order by f.fecha desc' );
	    }
		
		$body = '<br>
		<form class="header-search-box" action="cotizaciones.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              ">
			</div>
		</form>
		<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">FOLIO cotizacion</th>
							<th class="table-head th-name uppercase">vendedor</th>
							<th class="table-head th-name uppercase">cliente</th>
							<th class="table-head th-name uppercase">creado</th>
							<th class="table-head th-name uppercase">Ver</th>
							<th class="table-head th-name uppercase">Credito</th>
							<th class="table-head th-name uppercase">Enviar</th>
							<th class="table-head th-name uppercase">opciones</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><a href="/sale_finaly_report_cotizacion.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><a href="/clients.php?pagina=1&search='.$row[2].'">'.$row[2].'</a></td>
			<td class="item-des">'.GetFechaText($row[3]).'</td>
			
			<td class="item-des">
				<center><a href="/sale_cot.php?folio='.$row[0].'" class="button extra-small button-black mb-20" ><i class="zmdi zmdi-eye zmdi-hc-2x"></i></a></center>
			</td>
			
			<td class="item-des">
				<center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#credit'.$row[0].'"><i class="zmdi zmdi-money zmdi-hc-2x"></i></a></center>
			</td>

			<td class="item-des">
				<center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#mail'.$row[0].'"><i class="zmdi zmdi-email zmdi-hc-2x"></i></a></center>
			</td>
			</td>
			
			<td class="item-des">
                <center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> +</span></a></center>				
			</td>
			
			</tr>
			';
		}
		/*Opciones
		<td class="item-des">
		<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> Opciones</span></a>
		</div>
		
		</td>
		*/
		$body = $body . '
		</tbody>
			</table>';

		$body = $body . $pagination;
		return $body;
	}
	
	function table_annuity($search)
	{
	    $total = 0;			
	    $cont = 0;			
	    
	    if (empty($search))
	    {
	        $data = mysqli_query(db_conectar(),"SELECT a.id, c.nombre, a.concepto, a.price, a.date_ini, a.date_last, a.active, IF(a.active = 1, 'ACTIVO', 'SUSPENDIDO') as estado  FROM annuities a, clients c WHERE a.client = c.id order by a.date_last asc");    
	    }else
	    {
	        $data = mysqli_query(db_conectar(),"SELECT a.id, c.nombre, a.concepto, a.price, a.date_ini, a.date_last, a.active, IF(a.active = 1, 'ACTIVO', 'SUSPENDIDO') as estado  FROM annuities a, clients c WHERE a.client = c.id and c.nombre LIKE '%$search%' or a.client = c.id and c.razon_social LIKE '%$search%' or a.client = c.id and a.concepto LIKE '%$search%' order by a.date_last asc");    
	        
	    }
	    
        		
		$body = '<br>
		<form class="header-search-box" action="annuity.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              ">
			</div>
		</form>
		<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase" style="width: 24% !important;">CLIENTE</th>
							<th class="table-head th-name uppercase" style="width: 24% !important;">concepto</th>
							<th class="table-head th-name uppercase" style="width: 10% !important; text-align: center;">PRECIO</th>
							<th class="table-head th-name uppercase" style="width: 18% !important; text-align: center;">F. INICIO</th>
							<th class="table-head th-name uppercase" style="width: 18% !important; text-align: center;">ULTIMO PAGO</th>
							<th class="table-head th-name uppercase" style="text-align: center;">ESTADO</th>
							<th class="table-head th-name uppercase">opciones</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			$cont += 1;
			$red = "";
			if ($row[6] == 1)
			{
			    $red = "<font color ='#000000'>";
			}else
			{
			    $red = "<font color ='#D30606'>";
			}
			
			$total += $row[3];
			$body = $body.'
			<tr>
			<td class="item-des"><a href= "/clients.php?search='.$row[1].'">'.$red.$row[1].'</font></a></td>
			<td class="item-des">'.$red.$row[2].'</font></td>
			<td class="item-des" style="text-align: center;">
    			<table style="height: auto;" width="100%">
                	<tbody>
                		<tr>
                			<td style="text-align: left;">'.$red.'$</font></td>
                			<td style="text-align: right;">&nbsp;'.$red.number_format($row[3],GetNumberDecimales(),".",",").'</font></td>
                		</tr>
                	</tbody>
                </table>
			</td>
			<td class="item-des" style="text-align: center;">'.$red.GetFechaText($row[4]).'</font></td>
			<td class="item-des" style="text-align: center;">'.$red.GetFechaText($row[5]).'</font></td>
			<td class="item-des" style="text-align: center;">'.$red.$row[7].'</font></td>
			<td class="item-des">
                <center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#annuityupdate'.$row[0].'" ><span> +</span></a></center>				
			</td>
			
			</tr>
			';
		}
		/*Opciones
		<td class="item-des">
		<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> Opciones</span></a>
		</div>
		
		</td>
		*/
		$utilidad = $total;
		$utilidad = $utilidad - 7200;
		
		$body = $body . '
		</tbody>
			</table><br>
			<h4 style="text-align: center;"><strong>INGRESO $ '.number_format($total,GetNumberDecimales(),".",",").' | EGRESO $ '.number_format(7200,GetNumberDecimales(),".",",").'</strong></h4>
			<h3 style="text-align: center;"><strong>UTILIDAD $ '.number_format($utilidad,GetNumberDecimales(),".",",").' MXN, DE UN TOTAL DE '.$cont.' ITEMS.</strong></h3>
			';

		
		return $body;
	}

    function table_AnnuityModal ($search)
	{
		if (empty($search))
	    {
	        $data = mysqli_query(db_conectar(),"SELECT a.id, c.nombre, a.concepto, a.price, a.date_ini, a.date_last, a.active, IF(a.active = 1, 'ACTIVO', 'SUSPENDIDO') as estado  FROM annuities a, clients c WHERE a.client = c.id order by a.active asc");    
	    }else
	    {
	        $data = mysqli_query(db_conectar(),"SELECT a.id, c.nombre, a.concepto, a.price, a.date_ini, a.date_last, a.active, IF(a.active = 1, 'ACTIVO', 'SUSPENDIDO') as estado  FROM annuities a, clients c WHERE a.client = c.id and c.nombre LIKE '%$search%' or a.client = c.id and c.razon_social LIKE '%$search%' or a.client = c.id and a.concepto LIKE '%$search%' order by a.active asc");
	    }
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="annuityupdate'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ANUALIDAD: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/annuity_update.php" method="post" autocomplete="off">
                <div class="row">
    		  		
    		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
    				  
    				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
                    
    				<div class="col-md-12">
    					<label>Concepto</label>
    					<input type="text" name="concepto" id="concepto" placeholder="..." value="'.$row[2].'">
    				</div>
    				
    				<div class="col-md-12">
    					<br><label>Precio</label>
    					<input type="text" name="price" id="price" placeholder="$ 0.00 MXN" value="'.$row[3].'">
    				</div>
    				
    				<div class="col-md-6 text-center">
    					<br><label><b>Fecha de registro</b></label><br><label>'.GetFechaText($row[4]).'</label>
    				</div>
    				
    				<div class="col-md-6 text-center">
    					<br><label><b>Fecha ultimo pago</b></label><br><label>'.GetFechaText($row[5]).'</label>
    				</div>
    
    				
    			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
    					<a href="func/annuity_delete.php?id='.$row[0].'">
    					    <button type="button" class="btn btn-danger">Eliminar</button>
    					</a>
    					<a href="func/annuity_email.php?id='.$row[0].'&concepto='.$row[2].'&price='.$row[3].'&client='.$row[1].'&lastpay='.GetFechaText($row[5]).'">
    					    <button type="button" class="btn btn-warning">Email</button>
    					</a>
    					<a href="func/annuity_renovar.php?id='.$row[0].'&concepto='.$row[2].'&price='.$row[3].'">
    					    <button type="button" class="btn btn-success">Renovar</button>
    					</a>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>

			';
		}
		
		return $body;
	}
	
	function table_orders_search($txt)
	{
		$data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.fecha FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.pedido = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and f.folio like '%$txt%' or f.open = 1 and f.pedido = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and c.nombre like '%$txt%' or f.open = 1 and f.pedido = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and u.nombre like '%$txt%'");
		$body = '
		<form class="header-search-box" action="orders.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off">
			</div>
		</form>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">FOLIO PEDIDO</th>
							<th class="table-head th-name uppercase">vendedor</th>
							<th class="table-head th-name uppercase">cliente</th>
							<th class="table-head th-name uppercase">creado</th>
							<th class="table-head th-name uppercase">opciones</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><a href="/sale_finaly_order.php?folio='.$row[0].'">'.$row[0].'</a></td>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des">'.$row[2].'</td>
			<td class="item-des">'.$row[3].'</td>
			<td class="item-des">
				<div class="col-md-12">
					
					<div class="col-md-6">
						<a href="/sale_order.php?folio='.$row[0].'" class="button extra-small button-black mb-20" ><span> Ver</span></a>
					</div>

					<div class="col-md-6">
					<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> +</span></a>
					</div>
					
				</div>
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function table_cotizaciones_search($txt)
	{
		if ($_SESSION['propiedades'] > 0)
	    {
	        $data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.fecha FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and f.folio like '%$txt%' or f.open = 1 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and c.nombre like '%$txt%'");
	    }else
	    {
	        $data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.fecha FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and f.folio like '%$txt%' and f.vendedor = " . $_SESSION['users_id']
        
                ." or f.open = 1 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id and c.nombre like '%$txt%' and f.vendedor = " . $_SESSION['users_id']);
	        
	    }


		$body = '<br>
		<form class="header-search-box" action="cotizaciones.php">
			<div>
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              ">
			</div>
		</form>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">FOLIO cotizacion</th>
							<th class="table-head th-name uppercase">vendedor</th>
							<th class="table-head th-name uppercase">cliente</th>
							<th class="table-head th-name uppercase">creado</th>
							<th class="table-head th-name uppercase">Ver</th>
							<th class="table-head th-name uppercase">Credito</th>
							<th class="table-head th-name uppercase">enviar</th>
							<th class="table-head th-name uppercase">opciones</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><a href="/sale_finaly_report_cotizacion.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><a href="/clients.php?pagina=1&search='.$row[2].'">'.$row[2].'</a></td>
			<td class="item-des">'.GetFechaText($row[3]).'</td>
			
			<td class="item-des">
				<center><a href="/sale_cot.php?folio='.$row[0].'" class="button extra-small button-black mb-20" ><i class="zmdi zmdi-eye zmdi-hc-2x"></i></a></center>
			</td>
			
			<td class="item-des">
				<center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#credit'.$row[0].'"><i class="zmdi zmdi-money zmdi-hc-2x"></i></a></center>
			</td>

			<td class="item-des">
				<center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#mail'.$row[0].'"><i class="zmdi zmdi-email zmdi-hc-2x"></i></a></center>
			</td>
			</td>
			
			<td class="item-des">
                <center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> +</span></a></center>				
			</td>
			</tr>
			';
			/*Opciones
			<td class="item-des">
			<div class="col-md-12">
				<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#edit'.$row[0].'" ><span> Opciones</span></a>
			</div>
			
			</td>
			*/
		}
		$body = $body . '
		</tbody>
			</table>
		';

		$body = $body . $pagination;
		return $body;
	}

	function view_move($usuario, $sucursal)
	{
		if ($usuario == 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT v.folio, u.nombre, c.nombre, v.descuento, v.fecha, v.open, v.cobrado, v.fecha_venta, v.cut, s.nombre, v.t_pago, v.concepto , v.pedido FROM folio_venta v, sucursales s, users u, clients c where v.sucursal = s.id and v.vendedor = u.id and v.client = c.id and v.open = 0 and v.cut_global = 0 order by s.nombre asc, v.fecha_venta desc");
		}
		
		else if ($usuario > 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT v.folio, u.nombre, c.nombre, v.descuento, v.fecha, v.open, v.cobrado, v.fecha_venta, v.cut, s.nombre, v.t_pago, v.concepto , v.pedido FROM folio_venta v, sucursales s, users u, clients c where v.sucursal = s.id and v.vendedor = u.id and v.client = c.id and v.open = 0 and v.cut_global = 0 and v.vendedor = $usuario and v.sucursal = $sucursal order by v.fecha_venta desc");
		}

		else if ($usuario > 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT v.folio, u.nombre, c.nombre, v.descuento, v.fecha, v.open, v.cobrado, v.fecha_venta, v.cut, s.nombre, v.t_pago, v.concepto , v.pedido FROM folio_venta v, sucursales s, users u, clients c where v.sucursal = s.id and v.vendedor = u.id and v.client = c.id and v.open = 0 and v.cut_global = 0 and v.vendedor = $usuario order by v.fecha_venta desc");
		}

		else if ($usuario == 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT v.folio, u.nombre, c.nombre, v.descuento, v.fecha, v.open, v.cobrado, v.fecha_venta, v.cut, s.nombre, v.t_pago, v.concepto , v.pedido FROM folio_venta v, sucursales s, users u, clients c where v.sucursal = s.id and v.vendedor = u.id and v.client = c.id and v.open = 0 and v.cut_global = 0 and v.sucursal = $sucursal order by v.fecha_venta desc");
		}


		$body = '
		<div class="table-responsive">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">VENDEDOR</th>
							<th class="table-head th-name uppercase">CLIENTE</th>
							<th class="table-head th-name uppercase">FOLIO</th>
							<th class="table-head th-name uppercase">SUCURSAL</th>
							<th class="table-head th-name uppercase txt-center">FECHA_VENTA_FECHA&HORA</th>
							<th class="table-head th-name uppercase">concepto</th>
							<th class="table-head th-name uppercase"><center>cantidad_$_0.00_MXN</center></th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;
		$total = 0;
		
		while($row = mysqli_fetch_array($data))
	    {
			if (!$row[11])
			{
				$row[11] = "Venta";
				if ($row[12] == 1)
				{
					$folio = '<td class="item-des"><a href="sale_finaly_report_order.php?folio='.$row[0].'">'.$row[0].'</a></td>';
				}else
				{
					$folio = '<td class="item-des"><a href="sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>';
				}
			}else
			{
				$folio = '<td class="item-des">'.$row[0].'</td>';
			}

			$body = $body.'
			<tr>
			<td class="item-des">'.$row[1].'</td>
			<td class="item-des"><a href="/clients.php?pagina=1&search='.$row[2].'">'.$row[2].'</a></td>
			'.$folio.'
			<td class="item-des"><p>'.$row[9].'</p></td>
			<td class="item-des"><center>'.GetFechaText($row[4]).'</center></td>
			<td class="item-des uppercase"><p><center>'.$row[11].'</center></p></td>
			<td class="item-des text-right">$ '.number_format($row[6],GetNumberDecimales(),".",",").' MXN</td>
			</tr>
			';
			$total = $total + $row[6];
			

			if ($row[10] == "efectivo")
			{
				$efectivo = $efectivo + $row[6];
			}
			elseif ($row[10] == "transferencia")
			{
				$transferencia = $transferencia + $row[6];
			}
			elseif ($row[10] == "tarjeta")
			{
				$cheque = $cheque + $row[6];
			}
			elseif ($row[10] == "deposito")
			{
				$deposito = $deposito + $row[6];
			}
			elseif ($row[10] == "cheque")
			{
				$cheque0 = $cheque0 + $row[6];
			}
		}

		$body = $body . '
		</tbody>
			</table>
		</div>
		<br>
		<div align="right">
		';

		if ($efectivo > 0)
		{
			$cajatmp = $cajatmp . '
			<h5>Efectivo: $ '.number_format($efectivo,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$cajatmp = $cajatmp . '
			<h5>Tranferencia: $ '.number_format($transferencia,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($cheque > 0)
		{
			$cajatmp = $cajatmp . '
			<h5>Tarjeta: $ '.number_format($cheque,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($deposito > 0)
		{
			$cajatmp = $cajatmp . '
			<h5>Deposito: $ '.number_format($deposito,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}
		if ($cheque0 > 0)
		{
			$cajatmp = $cajatmp . '
			<h5>Cheques: $ '.number_format($cheque0,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		$body = '</div>
		<br>
		<div align="right">
			'.$cajatmp.'
			<h4>TOTAL RECAUDADO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h4>
		</div>
		' . $body;
		
		return $body;
	}

	function table_finance($inicio, $finaliza, $folio, $vendedor, $sucursal, $pagina)
	{
		//$inicio = '2018-07-18 00:00:00';
		//$finaliza = '2018-07-18 23:59:59';
		$inicio_old = $inicio;
		$f_inicio = $inicio_old . ' 00:00:00';
		
		$finaliza_old = $finaliza;
		$f_finaliza = $finaliza_old . ' 23:59:59';
		
		$total = 0;
		$porcent_comision = 0;
		
		$TAMANO_PAGINA = 10;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

        
		if ($folio != "" && $vendedor == 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.folio like '%$folio%'  order by c.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.folio like '%$folio%'  order by c.id desc ");
			$data_total = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.folio like '%$folio%'  order by c.id desc");
		}
		elseif ($folio == "" && $vendedor > 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.vendedor = '$vendedor'  order by c.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.vendedor = '$vendedor'  order by c.id desc");
			$data_total = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.vendedor = '$vendedor'  order by c.id desc");
		}
		elseif ($folio == "" && $vendedor == 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido , f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal'  order by c.id desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal'  order by c.id desc ");
			$data_total = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido , f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal'  order by c.id desc");
		}
		elseif ($folio == "" && $vendedor > 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal' and f.vendedor = '$vendedor'  order by c.id desc  LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal' and f.vendedor = '$vendedor'  order by c.id desc");
			$data_total = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal' and f.vendedor = '$vendedor'  order by c.id desc");
		}
		else
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza'  order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza'  order by f.fecha_venta desc");
			$data_total = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto, v.comision, f.comision_pagada FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza'  order by f.fecha_venta desc");
		}
		
		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&folio='.$folio.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&folio='.$folio.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&folio='.$folio.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&folio='.$folio.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body .= $pagination . '
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">FOLIO</th>
							<th class="table-head th-name uppercase">VENDEDOR</th>
							<th class="table-head th-name uppercase">F.VENTA</th>
							<th class="table-head th-name uppercase">COBRADO</th>
							<th class="table-head th-name uppercase">DETALLES</th>
                            <th class="table-head th-name uppercase">ELIMINAR</th>
                            <th class="table-head th-name uppercase">FACTURAR</th>
						</tr>
					</thead>
					<tbody>';
		
		$utilidad = 0;
		$con = db_conectar();  
		
		while($row = mysqli_fetch_array($data))
	    {
			if ($row[9] == 1)
			{
				$folio_ = '<td class="item-des"><a href="sale_finaly_report_order.php?folio='.$row[0].'">'.$row[0].'</a></td>';
				$facturar = '
				<a href="/facturar.php?folio='.$row[0].'&stocck=0" target="_blank" class="button extra-small button-black mb-20" ><i class="zmdi zmdi-shield-check zmdi-hc-lg"></i></a>
				';
			}else
			{
				$folio_ = '<td class="item-des"><a href="sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>';
				$facturar = '
				<a href="/facturar.php?folio='.$row[0].'&stocck=1" target="_blank" class="button extra-small button-black mb-20" ><i class="zmdi zmdi-shield-check zmdi-hc-lg"></i></a>
				';
			}

			$body = $body.'
			<tr>
			'.$folio_.'
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.GetFechaText($row[6]).'</p></td>
			<td class="item-des"><p>$ '.$row[5].' MXN</p></td>
			<td class="item-des uppercase"><center>
				<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#details'.$row[0].'" ><i class="zmdi zmdi-eye zmdi-hc-lg"></i></a>
			</center></td>
			<td class="item-des uppercase"><center>
				<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#delete'.$row[0].'" ><i class="zmdi zmdi-close zmdi-hc-lg"></i></a>
			</center></td>
			<td class="item-des uppercase"><center>
				'.$facturar.'
			</center></td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>
		'.$pagination.'
		<br>
		<div align="right">
		';
		
		// Totales
		while($row = mysqli_fetch_array($data_total))
	    {
	        //Utilidad
	        $porcent_comision = $row[11]; $folio_comision_pagada = $row[12];
	        
	        if (!$folio_comision_pagada)
	        {
	            $genericos = mysqli_query($con,"SELECT  unidades, precio FROM product_venta v WHERE p_generico != '' and folio_venta = $row[0] ");    
            
                while($temp0 = mysqli_fetch_array($genericos))
                {
                    $utilidad = $utilidad + ($temp0[0] * $temp0[1]);
                }
                
                $products = mysqli_query($con,"SELECT v.unidades, p.precio_costo, p.precio_normal, p.oferta FROM product_venta v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = $row[0]");                
                while($temp1 = mysqli_fetch_array($products))
                {
                    if (!$temp1[3])
                    {
                        $costo = $temp1[0] * $temp1[1];   
                        $precio_p = $temp1[0] * $temp1[2];   
                        $utilidad = $utilidad + ($precio_p - $costo);
                   }
                }    
	        }
	       
			if (!empty($row[8]))
			{
				if ($row[8] == "efectivo")
				{
					$efectivo = $efectivo + $row[5];
				}
				elseif ($row[8] == "transferencia")
				{
					$transferencia = $transferencia + $row[5];
				}
				elseif ($row[8] == "tarjeta")
				{
					$cheque = $cheque + $row[5];
				}
				elseif ($row[8] == "deposito")
				{
					$deposito = $deposito + $row[5];
				}
				elseif ($row[8] == "cheque")
				{
					$cheque0 = $cheque0 + $row[5];
				}

			}
			$total = $total + $row[5];
		}
		//Finaliza totales
		
        if ($vendedor > 0 && $utilidad > 0)
        {
            $body = $body . '
			<h5>Utilidad: $ '.number_format( $utilidad,GetNumberDecimales(),".",",").' MXN</h5>
			';
			
			$body = $body . '
			<h5>Comision: '.$porcent_comision.' % $ '.number_format( $utilidad* ($porcent_comision / 100),GetNumberDecimales(),".",",").' MXN</h5>
			';
        }
		if ($efectivo > 0)
		{
			$body = $body . '
			<h5>Efectivo: $ '.number_format($efectivo,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$body = $body . '
			<h5>Tranferencia: $ '.number_format($transferencia,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($cheque > 0)
		{
			$body = $body . '
			<h5>Tarjeta: $ '.number_format($cheque,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($deposito > 0)
		{
			$body = $body . '
			<h5>Deposito: $ '.number_format($deposito,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}
		if ($cheque0 > 0)
		{
			$body .= '
			<h5>Cheques: $ '.number_format($cheque0,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}
		
		$body = $body . '
			<h4>TOTAL RECAUDADO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h4>
		</div>
		';

		return $body;
	}

	function table_credits($client, $sucursal)
	{
		if ($client > 0)
		{
			if ($sucursal > 0)
			{
				$data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id  and c.client =  '$client' and c.sucursal = '$sucursal' ORDER by  f_vencimiento asc");
			}else
			{
				$data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id  and c.client =  '$client' ORDER by  f_vencimiento ascc");
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

		$body = '
		
		<div class="table-responsive compare-wraper mt-30">
		</div>
		
			<div class="col-md-6">
				<br><label>Seleccione cliente</label><br>
				<select id="select_client" name="select_client" onchange="loadclient()">
						'.Select_clients($client).'
				</select> 
			</div>

			<div class="col-md-6">
				<br><label>Seleccione sucursal</label>
				<select id="select_sucursal" name="select_sucursal" onchange="loadclient()">
						'.Select_sucursales_selected($sucursal).'
				</select>
			</div>

			<br>

				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">NOMBRE</th>
							<th class="table-head th-name uppercase">F. VENCIMIENTO</th>
							<th class="table-head th-name uppercase">DIAS_RESTANTES</th>
							<th class="table-head th-name uppercase">FACTURA</th>
							<th class="table-head th-name uppercase">CREDITO_TOTAL</th>
							<th class="table-head th-name uppercase">PENDIENTE_DE_PAGO</th>
							<th class="table-head th-name uppercase">DETALLES</th>
							<th class="table-head th-name uppercase">MAIL</th>
							<th class="table-head th-name uppercase">LIQUIDAR</th>
							<th class="table-head th-name uppercase">ELIMINAR</th>
						</tr>
					</thead>
					<tbody>';
		
		$con = db_conectar();  
		
		//Variables a detalle 
		$plus_lastID = 0;
		$plus_last_client = "";
        $plus_contador = 0;
        $plus_total = 0;
		$plus_cont = 0;
		$plus_total_g = 0;
		
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
					<td><i><a href="/clients.php?pagina=1&search='.$plus_last_client.'">'.$plus_last_client.'</a></i></td>
					<td><i>'.$plus_contador.' CREDITOS</i></td>
					<td><i>$ '.number_format($plus_total_g,GetNumberDecimales(),".",",").' MXN</i></td>
					<td><i>$ '.number_format($plus_total,GetNumberDecimales(),".",",").' MXN</i></td>
				</tr>


				<tr>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
					<td><br><br><br></td>
				</tr>
				
				<tr>
					<td><b>NOMBRE</b></td>
					<td><b>F. VENCIMIENTO</b></td>
					<td><b>DIAS_RESTANTES</b></td>
					<td><b>FACTURA</b></td>
					<td><b><center>CREDITO_TOTAL</b></center></td>
					<td><b><center>PENDIENTE_DE_PAGO</b></center></td>
					<td><b><center>DETALLES</b></center></td>
					<td><b><center>MAIL</b></center></td>
					<td><b><center>LIQUIDAR</b></center></td>
					<td><b><center>ELIMINAR</b></center></td>
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
				<td class="item-des" '.$font.' >'.$row[8].' DIAS</td>
				<td class="item-des" '.$font.' ><a href="http://'.$_SERVER['HTTP_HOST'].'/sale_finaly_report_cotizacion.php?folio_sale='.$row[4].'">'.$row[4].'</a></td>
				<td class="item-des" '.$font.' >$ '.number_format($row[5],GetNumberDecimales(),".",",").' MXN</td>
				<td class="item-des" '.$font.' >$ '.number_format($row[7],GetNumberDecimales(),".",",").' MXN</td>
				<td><center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#details'.$row[0].'" ><i class="zmdi zmdi-eye zmdi-hc-lg"></i></a></center></td>
				<td class="item-des"><center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#mail'.$row[0].'"><i class="zmdi zmdi-mail-send zmdi-hc-2x"></i></a></center></td>
				<td><center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#liquid'.$row[0].'" ><i class="zmdi zmdi-check zmdi-hc-lg"></i></a></center></td>
				<td><center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#delete'.$row[0].'" ><i class="zmdi zmdi-close zmdi-hc-lg"></i></a></center></td>
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
					<td><i><a href="/clients.php?pagina=1&search='.$plus_last_client.'">'.$plus_last_client.'</a></i></td>
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
		$body = $body . '
		</tbody>
			</table>
		</div>
		<br>
		<div align="right">
		';
		
		$body = $body . '
			<h4>TOTAL POR COBRAR: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h4>
			
			<a href="report_xls_credits.php?client='.$client.'&sucursal='.$sucursal.'"style="
            background-color: #58ACFA;
            border: none;
            color: white;
            padding: 18px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px;
            margin: 4px 2px;
            cursor: pointer;
			">GENERAR REPORTE XLS</a>
			
			<a href="report_pdf_credits.php?client='.$client.'&sucursal='.$sucursal.'"style="
            background-color: #58ACFA;
            border: none;
            color: white;
            padding: 18px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px;
            margin: 4px 2px;
            cursor: pointer;
            ">GENERAR REPORTE PDF</a>
		</div>
		';

		return $body;
	}

	function table_finance_client ($inicio, $finaliza, $vendedor, $sucursal, $client, $pagina)
	{
		//$inicio = '2018-07-18 00:00:00';
		//$finaliza = '2018-07-18 23:59:59';
		$inicio_old = $inicio;
		$f_inicio = $inicio_old . ' 00:00:00';
		
		$finaliza_old = $finaliza;
		$f_finaliza = $finaliza_old . ' 23:59:59';
		
		$total = 0;
		$porcent_comision = 0;
		
		$TAMANO_PAGINA = 50;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		if ($vendedor > 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.vendedor = '$vendedor' and f.client = '$client'  and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.vendedor = '$vendedor' and f.client = '$client'  and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
			$dataTotal = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.vendedor = '$vendedor' and f.client = '$client'  and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
		}
		elseif ($vendedor == 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido , f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
			$dataTotal = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido , f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
		}
		elseif ($vendedor > 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.vendedor = '$vendedor' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.vendedor = '$vendedor' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
			$dataTotal = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.vendedor = '$vendedor' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
		}
		else
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
			$datatmp = mysqli_query(db_conectar(),"SELECT f.folio FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
			$dataTotal = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc");
		}
		
		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&client='.$client.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&client='.$client.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&client='.$client.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&client='.$client.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&client='.$client.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?inicio='.$inicio_old.'&finaliza='.$finaliza_old.'&usuario='.$vendedor.'&sucursal='.$sucursal.'&client='.$client.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body .= $pagination . '
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">FOLIO</th>
							<th class="table-head th-name uppercase">VENDEDOR</th>
							<th class="table-head th-name uppercase">CLIENTE</th>
							<th class="table-head th-name uppercase">SUCURSAL</th>
							<th class="table-head th-name uppercase">F.VENTA</th>
							<th class="table-head th-name uppercase">COBRADO</th>
							<th class="table-head th-name uppercase">m. pago</th>
							<th class="table-head th-name uppercase">Eliminar</th>
                            <th class="table-head th-name uppercase">facturar</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			if (!$row[10])
			{
				if ($row[9] == 1)
				{
					$folio_ = '<td class="item-des"><a href="sale_finaly_report_order.php?folio='.$row[0].'">'.$row[0].'</a></td>';
                    $facturar = '
                    <a href="/facturar.php?folio='.$row[0].'&stocck=0" target="_blank" class="button extra-small button-black mb-20" ><span>Emitir</span> </a>
                    ';
				}else
				{
					$folio_ = '<td class="item-des"><a href="sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>';
                    $facturar = '
                    <a href="/facturar.php?folio='.$row[0].'&stocck=1" target="_blank" class="button extra-small button-black mb-20" ><span>Emitir</span> </a>
                    ';
				}

				$body = $body.'
				<tr>
				'.$folio_.'
				<td class="item-des"><p>'.$row[1].'</p></td>
				<td class="item-des"><p>'.$row[2].'</p></td>
				<td class="item-des"><p>'.$row[7].'</p></td>
				<td class="item-des"><p>'.GetFechaText($row[6]).'</p></td>
				<td class="item-des"><center><p>$ '.$row[5].' MXN</p></center></td>
				<td class="item-des uppercase"><center><p>'.$row[8].'</p></center></td>
				<td class="item-des uppercase"><center>
					<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#delete'.$row[0].'" ><span> X</span> </a>
				</center></td>
                <td class="item-des uppercase"><center>
					'.$facturar.'
				</center></td>
				</tr>
				';
			}
		}
		$body = $body . '
		</tbody>
			</table>
		</div>
		'.$pagination.'
		<div align="right">
		';

		// Totales
		while($row = mysqli_fetch_array($dataTotal))
	    {
	        //Utilidad
	        $porcent_comision = $row[11]; $folio_comision_pagada = $row[12];
	        
	        if (!$folio_comision_pagada)
	        {
	            $genericos = mysqli_query($con,"SELECT  unidades, precio FROM product_venta v WHERE p_generico != '' and folio_venta = $row[0] ");    
            
                while($temp0 = mysqli_fetch_array($genericos))
                {
                    $utilidad = $utilidad + ($temp0[0] * $temp0[1]);
                }
                
                $products = mysqli_query($con,"SELECT v.unidades, p.precio_costo, p.precio_normal, p.oferta FROM product_venta v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = $row[0]");                
                while($temp1 = mysqli_fetch_array($products))
                {
                    if (!$temp1[3])
                    {
                        $costo = $temp1[0] * $temp1[1];   
                        $precio_p = $temp1[0] * $temp1[2];   
                        $utilidad = $utilidad + ($precio_p - $costo);
                   }
                }    
	        }
	       
			if (!empty($row[8]))
			{
				if ($row[8] == "efectivo")
				{
					$efectivo = $efectivo + $row[5];
				}
				elseif ($row[8] == "transferencia")
				{
					$transferencia = $transferencia + $row[5];
				}
				elseif ($row[8] == "tarjeta")
				{
					$cheque = $cheque + $row[5];
				}
				elseif ($row[8] == "deposito")
				{
					$deposito = $deposito + $row[5];
				}
				elseif ($row[8] == "cheque")
				{
					$cheque0 = $cheque0 + $row[5];
				}

			}
			$total = $total + $row[5];
		}
		//Finaliza totales

		if ($efectivo > 0)
		{
			$body = $body . '
			<h5>Efectivo: $ '.number_format($efectivo,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$body = $body . '
			<h5>Tranferencia: $ '.number_format($transferencia,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($deposito > 0)
		{
			$body = $body . '
			<h5>Depositos: $ '.number_format($deposito,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}
		if ($cheque > 0)
		{
			$body = $body . '
			<h5>Tarjeta: $ '.number_format($cheque,GetNumberDecimales(),".",",").' MXN</h5>';
		}
		
		$body = $body . '
			<h4>TOTAL RECAUDADO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h4>
		</div>
		';

		return $body;
	}

	function table_finance_product($inicio, $finaliza, $product)
	{
		//$inicio = '2018-07-18 00:00:00';
		//$finaliza = '2018-07-18 23:59:59';
		$inicio .= ' 00:00:00';
		$finaliza .= ' 23:59:59';
		$total = 0;
		$tmp = db_conectar();
		$t_ud = 0;
		$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza'");
		
		$body = '
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">FOLIO</th>
							<th class="table-head th-name uppercase">VENDEDOR</th>
							<th class="table-head th-name uppercase">CLIENTE</th>
							<th class="table-head th-name uppercase">F.VENTA</th>
							<th class="table-head th-name uppercase">UNIDADES</th>
							<th class="table-head th-name uppercase"><center>PRODUCTO</center></th>
							<th class="table-head th-name uppercase">COBRADO</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			if ($product > 0)
			{
				$datatmp = mysqli_query($tmp,"SELECT p.nombre, pv.precio, pv.unidades, p.id FROM `product_venta` pv, productos p WHERE pv.product = p.id and pv.folio_venta = '$row[0]' and p.id = $product ");
			}
			else
			{
				$datatmp = mysqli_query($tmp,"SELECT p.nombre, pv.precio, pv.unidades, p.id FROM `product_venta` pv, productos p WHERE pv.product = p.id and pv.folio_venta = '$row[0]'");
			}
			
			while($row0 = mysqli_fetch_array($datatmp))
	    	{
				$t_ud = $t_ud + $row0[2];
				
				if (!$row[10])
				{
					if ($row[8] == "efectivo")
					{
						$efectivo = $efectivo + ($row0[2] * $row0[1]);
					}
					elseif ($row[8] == "transferencia")
					{
						$transferencia = $transferencia + ($row0[2] * $row0[1]);
					}
					elseif ($row[8] == "tarjeta")
					{
						$cheque = $cheque + ($row0[2] * $row0[1]);
					}
					
					if ($row[9] == 1)
					{
						$folio_ = '<td class="item-des"><a href="sale_finaly_report_order.php?folio='.$row[0].'">'.$row[0].'</a></td>';
					}else
					{
						$folio_ = '<td class="item-des"><a href="sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>';
					}

					$body = $body.'
					<tr>
					'.$folio_.'
					<td class="item-des"><p>'.$row[1].'</p></td>
					<td class="item-des"><p>'.$row[2].'</p></td>
					<td class="item-des"><p>'.$row[6].'</p></td>
					<td class="item-des"><center>'.$row0[2].'</center></td>
					<td class="item-des"><center><p><a target="_blank" href="/products_detail.php?id='.$row0[3].'">'.$row0[0].'</a></p></center></td>
					<td class="item-des"><center><p>$ '.$row0[2] * $row0[1].' MXN</p></center></td>
					</tr>
					';
					$total = $total + ($row0[2] * $row0[1]);
				}
			}
		}
		$body = $body . '
		</tbody>
			</table>
		</div>
		<br>
		<div align="right">
		';
		
		$body = $body . '
		<h5>Total unidades: '.$t_ud.' UDS</h5>
		';

		if ($efectivo > 0)
		{
			$body = $body . '
			<h5>Efectivo: $ '.number_format($efectivo,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$body = $body . '
			<h5>Tranferencia: $ '.number_format($transferencia,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($cheque > 0)
		{
			$body = $body . '
			<h5>Tarjeta: $ '.number_format($cheque,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}
		
		$body = $body . '
			<h4>TOTAL RECAUDADO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h4>
		</div>
		';

		return $body;
	}

	function table_finance_product_report($inicio, $finaliza, $product)
	{
		//$inicio = '2018-07-18 00:00:00';
		//$finaliza = '2018-07-18 23:59:59';
		$inicio .= ' 00:00:00';
		$finaliza .= ' 23:59:59';
		$total = 0;
		$tmp = db_conectar();
		$t_ud = 0;
		$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$inicio' and f.fecha_venta <= '$finaliza'");
		
		$body = '<table>
					<tr>
						<td>FOLIO</td>
						<td>VENDEDOR</td>
						<td>CLIENTE</td>
						<td>F.VENTA</td>
						<td>UNIDADES</td>
						<td><center>PRODUCTO</center></td>
						<td>COBRADO</td>
					</tr>
					';
		
		while($row = mysqli_fetch_array($data))
	    {
			if ($product > 0)
			{
				$datatmp = mysqli_query($tmp,"SELECT p.nombre, pv.precio, pv.unidades, p.id FROM `product_venta` pv, productos p WHERE pv.product = p.id and pv.folio_venta = '$row[0]' and p.id = $product ");
			}
			else
			{
				$datatmp = mysqli_query($tmp,"SELECT p.nombre, pv.precio, pv.unidades, p.id FROM `product_venta` pv, productos p WHERE pv.product = p.id and pv.folio_venta = '$row[0]'");
			}
			
			while($row0 = mysqli_fetch_array($datatmp))
	    	{
				$t_ud = $t_ud + $row0[2];
				
				if (!$row[10])
				{
					if ($row[8] == "efectivo")
					{
						$efectivo = $efectivo + ($row0[2] * $row0[1]);
					}
					elseif ($row[8] == "transferencia")
					{
						$transferencia = $transferencia + ($row0[2] * $row0[1]);
					}
					elseif ($row[8] == "tarjeta")
					{
						$cheque = $cheque + ($row0[2] * $row0[1]);
					}
					
					if ($row[9] == 1)
					{
						$folio_ = '<td><a href="sale_finaly_report_order.php?folio='.$row[0].'">'.$row[0].'</a></td>';
					}else
					{
						$folio_ = '<td><a href="sale_finaly_report.php?folio_sale='.$row[0].'">'.$row[0].'</a></td>';
					}

					$body = $body.'
					<tr>
					'.$folio_.'
					<td"><p>'.strtoupper($row[1]).'</p></td>
					<td"><p>'.strtoupper($row[2]).'</p></td>
					<td"><p>'.$row[6].'</p></td>
					<td"><center>'.$row0[2].'</center></td>
					<td"><center><p><a target="_blank" href="/products_detail.php?id='.$row0[3].'">'.$row0[0].'</a></p></center></td>
					<td"><center><p>$ '.$row0[2] * $row0[1].' MXN</p></center></td>
					</tr>
					';
					$total = $total + ($row0[2] * $row0[1]);
				}
			}
		}
		$body = $body . '
		</tbody>
			</table>
		</div>
		<br>
		<div align="right">
		';
		
		$body = $body . '
		<h5>Total unidades: '.$t_ud.' UDS</h5>
		';

		if ($efectivo > 0)
		{
			$body = $body . '
			<h5>Efectivo: $ '.number_format($efectivo,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($transferencia > 0)
		{
			$body = $body . '
			<h5>Tranferencia: $ '.number_format($transferencia,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}

		if ($cheque > 0)
		{
			$body = $body . '
			<h5>Tarjeta: $ '.number_format($cheque,GetNumberDecimales(),".",",").' MXN</h5>
			';
		}
		
		$body = $body . '
			<h4>TOTAL RECAUDADO: $ '.number_format($total,GetNumberDecimales(),".",",").' MXN</h4>
		</div>
		';

		return $body;
	}

	function create_sale_SelectClient ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="create_sale.php">
					<div>
						<input type="hidden" name="pagina" id="pagina" value="1">
						<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
 ">
					</div>
				</form>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

        while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#select_client_sale'.$row[0].'" ><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClient_client ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="gpc_finance.php">
					<div>
						<input type="text" placeholder="Buscar" name="search" autocomplete="off">
					</div>
				</form>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;
		$hoy = date("Y-m-d");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
				<a href="finance_clients.php?inicio=2013-05-29&finaliza='.$hoy.'&usuario=0&sucursal=0&client='.$row[0].'" class="button extra-small button-black mb-20"><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClient_ChangeClient ($pagina, $folio, $cotizacion, $pedido, $vtd)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");

		$pagination = '<div>
						<div class="col-md-12">
						<div class="shop-pagination p-20 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'&folio='.$folio.'&cotizacion='.$cotizacion.'&pedido='.$pedido.'&vtd='.$vtd.'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
	
		if ($total_paginas > 1) {

			for ($i=1;$i<=$total_paginas;$i++) {
				if ($pagina == $i)
					$pagination = $pagination . '<li><a href="#">...</a></li>';
				else
					$pagination = $pagination . '<li><a href="?pagina='.$i.'&folio='.$folio.'&cotizacion='.$cotizacion.'&pedido='.$pedido.'&vtd='.$vtd.'">'.$i.'</a></li>';
			}
		}
		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'&folio='.$folio.'&cotizacion='.$cotizacion.'&pedido='.$pedido.'&vtd='.$vtd.'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
		$body = '
		<div class="table-responsive compare-wraper mt-30">
				<form class="header-search-box" action="change_client.php">
					<div>
					<input type="hidden" name="folio" id="folio" value="'.$folio.'">
					<input type="hidden" name="cotizacion" id="cotizacion" value="'.$cotizacion.'">
					<input type="hidden" name="pedido" id="pedido" value="'.$pedido.'">
					<input type="hidden" name="vtd" id="vtd" value="'.$vtd.'">
					<input type="text" placeholder="Buscar" name="search" autocomplete="off">
					</div>
				</form>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<form action="func/update_cliente_venta.php" method="post">
				<input type="hidden" name="cliente" id="cliente" value="'.$row[0].'">
				<input type="hidden" name="folio" id="folio" value="'.$folio.'">
				<input type="hidden" name="cotizacion" id="cotizacion" value="'.$cotizacion.'">
				<input type="hidden" name="pedido" id="pedido" value="'.$pedido.'">
				<input type="hidden" name="vtd" id="vtd" value="'.$vtd.'">
				<button type="submit" class="btn btn-primary">Seleccionar</button>
			</form>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClientOrder ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="create_order.php">
					<div>
						<input type="text" placeholder="Buscar" name="search" autocomplete="off">
					</div>
				</form>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;

		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
				<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#select_client_sale'.$row[0].'" ><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClientCot ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, IF(razon_social = '', 'RAZON SOCIAL DESCONOCIDA', razon_social) as razon_social, descuento FROM `clients` ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';
									
		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="create_cotizacion.php">
					<div>
						<input type="hidden" name="pagina" id="pagina" value="1">
						<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
				   ">
					</div>
				</form>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;
		
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#select_client_sale'.$row[0].'" ><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClientSearch ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$data = mysqli_query(db_conectar(), "SELECT id, nombre, razon_social, descuento FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA ");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="create_sale.php">
					<div>
						<input type="hidden" name="pagina" id="pagina" value="1">
						<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
 				  " value = "'.$txt.'">
					</div>
				</form>
				<p>
				'.$pagination.'
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#select_client_sale'.$row[0].'" ><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClientSearch_client ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by nombre asc");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = $pagination . '
		<div class="table-responsive compare-wraper mt-30">
				<form class="header-search-box" action="gpc_finance.php">
					<div>
						<input type="text" placeholder="Buscar" name="search" autocomplete="off">
					</div>
				</form>
				<p>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		$hoy = date("Y-m-d");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a href="finance_clients.php?inicio=2013-05-29&finaliza='.$hoy.'&usuario=0&sucursal=0&client='.$row[0].'" class="button extra-small button-black mb-20"><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function ClientSearch_changeClient ($txt, $folio, $cotizacion, $pedido, $vtd)
	{
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by nombre asc ");

		$body = '
		<div class="table-responsive compare-wraper mt-30">
				<form class="header-search-box" action="change_client.php">
					<div>
						<input type="hidden" name="folio" id="folio" value="'.$folio.'">
						<input type="hidden" name="cotizacion" id="cotizacion" value="'.$cotizacion.'">
						<input type="hidden" name="pedido" id="pedido" value="'.$pedido.'">
						<input type="hidden" name="vtd" id="vtd" value="'.$vtd.'">
						<input type="text" placeholder="Buscar" name="search" autocomplete="off">
					</div>
				</form>
				<p>
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
				<form action="func/update_cliente_venta.php" method="post">
					<input type="hidden" name="cliente" id="cliente" value="'.$row[0].'">
					<input type="hidden" name="folio" id="folio" value="'.$folio.'">
					<input type="hidden" name="cotizacion" id="cotizacion" value="'.$cotizacion.'">
					<input type="hidden" name="pedido" id="pedido" value="'.$pedido.'">
					<input type="hidden" name="vtd" id="vtd" value="'.$vtd.'">
					<button type="submit" class="btn btn-primary">Seleccionar</button>
				</form>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClientSearchOrder ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$data = mysqli_query(db_conectar(),"SELECT id, nombre, razon_social, descuento FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%'  ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM clients");


        $pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="create_order.php">
					<div>
						<input type="text" placeholder="Buscar" name="search" autocomplete="off">
					</div>
				</form>
				<p> '.$pagination.'
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#select_client_sale'.$row[0].'" ><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function create_sale_SelectClientSearchCot ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$data = mysqli_query(db_conectar(),"SELECT id, nombre, IF(razon_social = '', 'RAZON SOCIAL DESCONOCIDA', razon_social) as razon_social, descuento FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%'");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '
		<div class="compare-wraper mt-30">
				<form class="header-search-box" action="create_cotizacion.php">
					<div>
				  <input type="hidden" id="pagina" name="pagina" value="1">
				  <input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
				  box-sizing: border-box;" value = "'.$txt.'">
					</div>
				</form>
				<p>
				'.$pagination.'
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-nam">CLIENTE</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">% DESCUENTO</th>
							<th class="table-head item-nam">OPCIONES</th>
						</tr>
					</thead>
					<tbody>';
		
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-des"><p>'.$row[1].'</p></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].' %</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#select_client_sale'.$row[0].'" ><span> Seleccionar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		$body = $body . $pagination;
		return $body;
	}

	function g_orden_compra_todos ($almacen, $marca, $proveedor)
	{
		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id  ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">PRODUCTO</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			
			// Add hijos
			$hijos = mysqli_query($con,"SELECT s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a where s.min >= s.stock  and s.max > s.stock and s.almacen = a.id and padre = $row[0] ");
        
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[3];
				$min = $item[4];
				$max = $item[5];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$row[1].'</td>
				<td class="item-des"><p>'.$row[2].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[2].' '.$item[6].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}
		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_almacen ($almacen, $marca, $proveedor)
	{
		$almacen0 = $almacen;

		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id and almacen = '$almacen' ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock and s.max > s.stock and s.padre = p.id and a.id = $almacen0 and s.padre = $row[0]");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}

		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_marca ($almacen, $marca, $proveedor)
	{
		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id AND p.marca like '%$marca%' ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock and s.max > s.stock and s.padre = p.id and p.marca like '%$marca%' and s.padre = $row[0] ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}

		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_proveedor ($almacen, $marca, $proveedor)
	{
		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id AND p.proveedor like '%$proveedor%' ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock  and s.max > s.stock and s.padre = p.id and p.proveedor like '%$proveedor%' and s.padre = $row[0]");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}

		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_AlmacenMarca ($almacen, $marca, $proveedor)
	{
		$almacen0 = $almacen;

		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id AND p.almacen = '$almacen' AND  p.marca like '%$marca%' ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock  and s.max > s.stock and s.padre = p.id and s.almacen = '$almacen0' and p.marca like '%$marca%' and s.padre = $row[0] ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}

		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_AlmacenProveedor ($almacen, $marca, $proveedor)
	{
		$almacen0 = $almacen;

		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id AND p.almacen = '$almacen' AND  p.proveedor like '%$proveedor%' ORDER by nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock  and s.max > s.stock and s.padre = p.id and s.almacen = '$almacen0' and p.proveedor like '%$proveedor%' and s.padre = $row[0] ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}

		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_MarcaProveedor ($almacen, $marca, $proveedor)
	{
		$almacen0 = $almacen;

		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id AND p.marca like '%$marca%' AND  p.proveedor like '%$proveedor%' ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock  and s.max > s.stock and s.padre = p.id and p.marca like '%$marca%' and p.proveedor like '%$proveedor%' and s.padre = $row[0] ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}


		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function g_orden_compra_AlmacenMarcaProveedor ($almacen, $marca, $proveedor)
	{
		$almacen0 = $almacen;

		$con = db_conectar();
		
		$data = mysqli_query($con,"SELECT p.id, p.`no. De parte`, p.nombre, p.stock_min, p.stock_max, p.stock, p.proveedor, p.marca, a.nombre ,p.loc_almacen FROM productos p, almacen a where p.stock_min >= p.stock  and p.stock_max > p.stock and p.almacen = a.id AND p.marca like '%$marca%' AND  p.proveedor like '%$proveedor%' and p.almacen = $almacen ORDER by p.nombre asc");
		
		if (!$marca)
		{
			$marca = 'Todos';
		}

		if (!$proveedor)
		{
			$proveedor = 'Todos';
		}

		if ($almacen)
		{
			$almacen = Return_NombreAlmacen($almacen);
		}

		if (!$almacen)
		{
			$almacen = 'Todos';
		}

		$_marca = 'MARCA: '. $marca . ' ';
		$_proveedor = '| PROVEEDOR: '. $proveedor . ' ';
		$_almacen = '| ALMACEN: '. $almacen . ' ';

		$val = $_marca . $_proveedor . $_almacen;
	
		$body = '
		<div class="section-title-2 text-uppercase mb-40 text-center">
				<h4>ORDEN DE COMPRA: '. $_SESSION['empresa_nombre'] .' | '. date("d-m-Y") .'</h4>
				'.$val.'
		</div>
		<div class="table-responsive compare-wraper mt-30">
				<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">no. de parte</th>
							<th class="table-head item-nam uppercase">producto</th>
							<th class="table-head item-nam uppercase">MINIMO</th>
							<th class="table-head item-nam uppercase">MAXIMO</th>
							<th class="table-head item-nam uppercase">disponible</th>
							<th class="table-head item-nam uppercase">PEDIR</th>
							<th class="table-head item-nam uppercase">UBICACION</th>
						</tr>
					</thead>
					<tbody>';
		

		while($row = mysqli_fetch_array($data))
	    {
			$pedir = 0;
			$stock = $row[5];
			$min = $row[3];
			$max = $row[4];

			
			$pedir = $max - $stock;

			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			<td class="item-des"><p>'.$stock.'</p></td>
			<td class="item-des"><p>
			<input type="number" value="'.$pedir.'">
			</p></td>
			<td class="item-des"><p>'.$row[8].' '.$row[9].'</p></td>
			</tr>
			';
			// Add hijos
			$hijos = mysqli_query($con,"SELECT p.`no. De parte`, p.nombre, s.id, s.padre, a.nombre, s.stock, s.min, s.max, s.ubicacion FROM productos_sub s, almacen a, productos p where s.almacen = a.id and s.min >= s.stock  and s.max > s.stock and s.padre = p.id and p.marca like '%$marca%' and p.proveedor like '%$proveedor%' and s.padre = $row[0] ");
			
			while($item = mysqli_fetch_array($hijos))
			{
				$pedir0 = 0;
				$stock0 = $item[5];
				$min = $item[6];
				$max = $item[7];

				
				$pedir0 = $max - $stock0;

				$body = $body.'
				<tr>
				<td class="item-quality">'.$item[0].'</td>
				<td class="item-des"><p>'.$item[1].'</p></td>
				<td class="item-des"><p>'.$min.'</p></td>
				<td class="item-des"><p>'.$max.'</p></td>
				<td class="item-des"><p>'.$stock0.'</p></td>
				<td class="item-des"><p>
				<input type="number" value="'.$pedir0.'">
				</p></td>
				<td class="item-des"><p>'.$item[4].' '.$item[8].'</p></td>
				</tr>
				';
			} //Finaliza hijos
		}


		$body = $body . '
		</tbody>
			</table>
		</div>';

		return $body;
	}

	function table_clientes_search ($txt, $pagina)
	{
		
		$TAMANO_PAGINA = 10;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT id, nombre, if (direccion = '' , 'DIRECCION DESCONOCIDA', direccion) as  direccion, if (telefono = '' , 'TELEFONO DESCONOCIDO', telefono) AS telefono, if (razon_social  = '' , 'RAZON SOCIAL DESCONOCIDA', razon_social  ) AS razon_social FROM `clients`  where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		$datatmp = mysqli_query(db_conectar(),"SELECT id, nombre, if (direccion = '' , 'DIRECCION DESCONOCIDA', direccion) as  direccion, if (telefono = '' , 'TELEFONO DESCONOCIDO', telefono) AS telefono, if (razon_social  = '' , 'RAZON SOCIAL DESCONOCIDA', razon_social  ) AS razon_social FROM `clients`  where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%'");

		$pagination = '<div class="row">
						<div class="col-md-12">
						<div class="shop-pagination p-10 text-center">
							<ul>';

		
		$num_total_registros = mysqli_num_rows($datatmp);
		$total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);

		if ($pagina > 1)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina - 1 ).'" ><i class="zmdi zmdi-chevron-left"></i></a></li>';
		}
		
		
		if ($total_paginas > 1) {

			if ($pagina <= 8)
			{
				for ($i=1; $i<$pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}else
			{
				for ($i= ($pagina - 7); $i < $pagina; $i++) {
				
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';	
				}
			}
			
		}
		
		$Pag_Max = $pagina + 8;
		
		if ($total_paginas > 1) {

			for ($i=$pagina;$i<=$total_paginas;$i++) {
				
				if ( $i == $pagina)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'"><b>'.$i.'</b></a></li>';
				elseif ( $i < $Pag_Max)
					$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.$i.'">'.$i.'</a></li>';
			}
		}

		if ($pagina < $total_paginas)
		{
			$pagination = $pagination . '<li><a href="?search='.$txt.'&pagina='.($pagina + 1 ).'" ><i class="zmdi zmdi-chevron-right"></i></a></li>';
		}
		
		$pagination = $pagination . '</ul>
									</div>
									</div>
									</div><p>';

		$body = '<br>
		<form class="header-search-box" action="clients.php">
			<div>
				<input type="hidden" id="pagina" name="pagina" value="1">
				<input type="text" placeholder="Buscar" name="search" autocomplete="off" style="
				  width: 100%;
                  padding: 24px 20px;
                  margin: 8px 0;
                  display: inline-block;
                  border: 3px solid #4A4A4A;
                  border-radius: 4px;
                  box-sizing: border-box;
              " value="'.$txt.'">
			</div>
		</form><br>
		<table class="cart table">
					<thead>
						<tr>
							<th class="table-head th-name uppercase">NOMBRE CLIENTE</th>
							<th class="table-head item-nam">DIRECCION</th>
							<th class="table-head item-nam">TELEFONO</th>
							<th class="table-head item-nam">RAZON SOCIAL</th>
							<th class="table-head item-nam">EMAIL</th>
							<th class="table-head item-nam">EDITAR</th>
							<th class="table-head item-nam">ELIMINAR</th>
						</tr>
					</thead>
					<tbody>';
		$body = $body . $pagination;
		
		$hoy = date("Y-m-d");

		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['client_guest'] == 1)
			{
				$boton = '
				<td class="item-des"><center><a href="" class="button extra-small button-black mb-20" data-toggle="modal" data-target="#mailcliente'.$row[0].'"><i class="zmdi zmdi-mail-send zmdi-hc-2x"></i></a></center></td>
				<td class="item-des"><center><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalclient_edit'.$row[0].'" ><span> Editar</span> </a></p></center></td>
				<td class="item-des"><center><p><a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalclient_delete'.$row[0].'" ><span> Eliminar</span> </a></p></center></td>
				';
			}else {
				// No pueden editar
				$boton = '
				<td class="item-des"><center><a href="" class="button extra-small button-black mb-20" data-toggle="modal"><i class="zmdi zmdi-plus zmdi-hc-2x"></i></a></center></td>
				<td class="item-des"><center><a class="button extra-small button-black mb-20"><span> Editar</span> </a></p></center></td>
				<td class="item-des"><center><p><a class="button extra-small button-black mb-20"><span> Eliminar</span> </a></p></center></td>
				';
			}


			$body = $body.'
			<tr>
			<td class="item-quality"><a href="/finance_clients.php?inicio=2013-05-29&finaliza='.$hoy.'&usuario=0&sucursal=0&client='.$row[0].'">'.$row[1].'</a></td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des"><p>'.$row[4].'</p></td>
			'.$boton.'
			</tr>
			';
		}
		$body = $body . '
		</tbody>
			</table>';
	    return $body . $pagination;
	}

	function table_departamentoModal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `departamentos` ORDER by nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modaldepartament_edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">DEPARTAMENTO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form action="../func/departamento_edit.php" autocomplete="off" method="post">
					<div class="row">
					<input type="hidden" name="id" id="id" value="'.$row[0].'">
					
					<div class="col-md-12">
					<label>Nombre departamento</label>
					<input type="text" name="departamento_add_nombre" id="departamento_add_nombre" value="'.$row[1].'">
					</div>
					
					<div class="col-md-12">
					<br>
					<label>Descripcion departamento</label>
					<textarea name="departamento_add_descripcion" id="departamento_add_descripcion" cols="30" rows="4">'.$row[2].'</textarea>
					</div>
		
				</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modaldepartament_delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR DEPARTAMENTO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/departamento_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro Elimnar el departamento ? Se eliminara el departamento y todos los productos asociados a el.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function g_compra_modal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT p.id, p.`no. de parte`, p.nombre, a.nombre, d.nombre, p.loc_almacen, p.marca, p.proveedor FROM productos p, almacen a, departamentos d WHERE p.almacen = a.id and p.departamento = d.id order by p.nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="g_orden_compra_detalles'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<div class="row">
				<div class="col-md-12">
				<h5 class="modal-title" id="exampleModalLongTitle">PRODUCTO: '.$row[2].'</h5>
				<br>
				<div class="row">
					<div class="col-md-6">No. de parte: '.$row[1].'</div>
					<div class="col-md-6">No. de parte: '.$row[1].'</div>
					<div class="col-md-6">Almacen: '.$row[3].'</div>
					<div class="col-md-6">Ubicacion: '.$row[5].'</div>
					<div class="col-md-6">Marca: '.$row[6].'</div>
					<div class="col-md-6">Proveedor: '.$row[7].'</div>
				</div>

				</div>
				</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}

	function table_orders_modal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.iva, f.t_pago, c.id FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.pedido = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id");
		
		$select_con = mysqli_query(db_conectar(),"SELECT id, nombre FROM clients ORDER by nombre asc");
		$select = "<option value='0'>CLIENTE</option>";
		while($row = mysqli_fetch_array($select_con))
		{
			$select = $select.'<option value='.$row[0].'>'.$row[1].'</option>';
		}

		$select_pago = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['super_pedidos'] == 1)
			{
				$eliminar = '<button type="sumbit" class="btn btn-danger">Eliminar</button>';
			}else
			{
				$eliminar = '';
			}
			

			$body = $body.'
			<div class="modal fade" id="edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">PEDIDO ABIERTO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<form action="func/product_sale_update_descuento.php" method="post">
							<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							
							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>DESCUENTO:</p>
							</div>
							
							<div class="col-md-3">
								<input type="number" id="descuento" name="descuento" autocomplete="off" value="'.$row[3].'" min="0" max="100" style="text-align:center;">
							</div>
							
							<div class="col-md-3">
								<p>%</p>
							</div>

							<div class="col-md-3">
								
							</div>
							</div>


							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>IVA:</p>
							</div>
							
							<div class="col-md-3">
								<input type="number" id="iva" name="iva" autocomplete="off" value="'.$row[8].'" min="0" max="100" style="text-align:center;">
							</div>
							
							<div class="col-md-3">
								<p>%</p>
							</div>

							<div class="col-md-3">
								
							</div>
							</div>



							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>CLIENTE:</p>
							</div>
							
							<div class="col-md-9">
								<select id="cliente'.$row[0].'" name="cliente'.$row[0].'">
									'.$select.'
								</select>
							</div>
							</div>
							<script>
								document.getElementById("cliente'.$row[0].'").value = "'.$row[10].'";
							</script>

							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>TIPO DE PAGO:</p>
							</div>
							
							<div class="col-md-9">
							<select id="t_pago'.$row[0].'" name="t_pago'.$row[0].'">
									'.$select_pago.'
								</select>
							</div>
							</div>
							<script>
								document.getElementById("t_pago'.$row[0].'").value = "'.$row[9].'";
							</script>
							
							<div class="col-md-12">
							
							<div class="col-md-3">
								<p></p>
							</div>
							
							<div class="col-md-3">
								<br><button class="submit-btn mt-2" type="submit">Actualizar</button>
							</div>
							
							<div class="col-md-3">
							</div>

							<div class="col-md-3">
								
							</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					
					<form action="func/delete_f_venta.php" autocomplete="off" method="post">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<a href="/change_client.php?folio='.$row[0].'&pedido=1"><button type="button" class="btn btn-primary">Cambiar cliente</button></a>
						'.$eliminar.'
					</form>
					
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function UpdateSaleVTD ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT folio, descuento, iva, client, t_pago, fecha FROM `folio_venta` WHERE folio = $folio ");
		
		$select_con = mysqli_query(db_conectar(),"SELECT id, nombre FROM clients ORDER by nombre asc");
		$select = "<option value='0'>CLIENTE</option>";
		while($row = mysqli_fetch_array($select_con))
		{
			$select = $select.'<option value='.$row[0].'>'.$row[1].'</option>';
		}

		$select_pago = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$date = date_create($row[5]);

			$body = $body.'
			<form action="func/product_sale_update_descuento.php" method="post">
            <input type="hidden" id="folio" name="folio" value="'.$row[0].'">
            <input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
            
            <div class="col-md-12">
            
            <div class="col-md-3">
                <p>DESCUENTO:</p>
            </div>
            
            <div class="col-md-3">
                <input type="number" id="descuento" name="descuento" autocomplete="off" value="'.$row[1].'" min="0" max="100" style="text-align:center;">
            </div>
            
            <div class="col-md-3">
                <p>%</p>
            </div>

            <div class="col-md-3">
                
            </div>
            </div>


            <div class="col-md-12">
            
            <div class="col-md-3">
                <p>IVA:</p>
            </div>
            
            <div class="col-md-3">
                <input type="number" id="iva" name="iva" autocomplete="off" value="'.$row[2].'" min="0" max="100" style="text-align:center;">
            </div>
            
            <div class="col-md-3">
                <p>%</p>
            </div>

            <div class="col-md-3">
                
            </div>
            </div>



            <div class="col-md-12">
            
            <div class="col-md-3">
                <p>CLIENTE:</p>
            </div>
            
            <div class="col-md-9">
                <select id="cliente'.$row[0].'" name="cliente'.$row[0].'">
                    '.$select.'
                </select>
            </div>
            </div>
            <script>
                document.getElementById("cliente'.$row[0].'").value = "'.$row[3].'";
            </script>

			<div class="col-md-12">
							
			<div class="col-md-3">
				<p>TIPO DE PAGO:</p>
			</div>
			
			<div class="col-md-9">
			<select id="t_pago'.$row[0].'" name="t_pago'.$row[0].'">
					'.$select_pago.'
				</select>
			</div>
			</div>
			<script>
				document.getElementById("t_pago'.$row[0].'").value = "'.$row[4].'";
			</script>
            
            <div class="col-md-12">
            
            <div class="col-md-3">
            </div>
            
			<div class="col-md-9">
	
			</div>
            
            <div class="col-md-3">
                
            </div>
            </div>
			';
		}
		
		return $body;
	}

	function table_cotizacion_modal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT f.folio, u.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.iva, f.t_pago, c.id, c.correo FROM folio_venta f, users u, clients c, sucursales s WHERE f.open = 1 and f.cotizacion = 1 and f.vendedor = u.id and f.client = c.id and f.sucursal = s.id");
		
		$select_con = mysqli_query(db_conectar(),"SELECT id, nombre FROM clients ORDER by nombre asc");
		$select = "<option value='0'>CLIENTE</option>";
		while($row = mysqli_fetch_array($select_con))
		{
			$select = $select.'<option value='.$row[0].'>'.$row[1].'</option>';
		}
		$select_pago = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			
			if ($_SESSION['super_pedidos'] == 1)
			{
				$eliminar = '<button type="sumbit" class="btn btn-danger">Eliminar</button>';
			}else
			{
				$eliminar = '';
			}
			

			$body = $body.'
			<div class="modal fade" id="edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">COTIZACION ABIERTA</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/product_sale_update_descuento.php" method="post">
							<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
							<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
							
							<div class="col-md-12">
							
							<div class="col-md-3">
								<p> % DESCUENTO:</p>
							</div>
							
							<div class="col-md-3">
								<input type="number" id="descuento" name="descuento" autocomplete="off" value="'.$row[3].'" min="0" max="100" style="text-align:center;">
							</div>
							
							<div class="col-md-3">
								
							</div>
							</div>


							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>% IVA:</p>
							</div>
							
							<div class="col-md-3">
								<input type="number" id="iva" name="iva" autocomplete="off" value="'.$row[8].'" min="0" max="100" style="text-align:center;">
							</div>
							
							<div class="col-md-3">
								
							</div>
							</div>


							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>CLIENTE:</p>
							</div>
							
							<div class="col-md-9">
								<select id="cliente'.$row[0].'" name="cliente'.$row[0].'">
									'.$select.'
								</select>
							</div>
							</div>
							
							<script>
								document.getElementById("cliente'.$row[0].'").value = "'.$row[10].'";
							</script>
							
							<div class="col-md-12">
							
							<div class="col-md-3">
								<p>TIPO DE PAGO:</p>
							</div>
							
							<div class="col-md-9">
							<select id="t_pago'.$row[0].'" name="t_pago'.$row[0].'">
									'.$select_pago.'
								</select>
							</div>
							</div>
							<script>
								document.getElementById("t_pago'.$row[0].'").value = "'.$row[9].'";
							</script>
							
							<div class="col-md-12">
							
							<div class="col-md-3">
								<p></p>
							</div>
							
							<div class="col-md-3">
								<br><button class="submit-btn mt-2" type="submit">Actualizar</button>
							</div>
							
							<div class="col-md-3">
							</div>

							<div class="col-md-3">
								
							</div>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					
					<form action="func/delete_f_venta.php" autocomplete="off" method="post">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<a href="/change_client.php?folio='.$row[0].'&cotizacion=1"><button type="button" class="btn btn-primary">Cambiar cliente</button></a>
						'.$eliminar.'
					</form>
				</div>
				</div>
			</div>
			</div>






			<div class="modal fade" id="credit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">CREDITO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<center><span>Enviar esta cotizacion a credito para: <b>'.$row[2].'</b></span></center>
					</div>
				</div>
				<div class="modal-footer">
					
					<form action="func/create_credit_cotizacion.php" autocomplete="off" method="post">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
						<button type="sumbit" class="btn btn-success">Aceptar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
			
			//Se envia email
			$body = $body.'
			<div class="modal fade" id="mail'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">ENVIAR COTIZACION POR CORREO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/cotizacion_sendmail.php" autocomplete="on" method="post">
							<div class="col-md-12">
								<label>Ingrese el correo del cliente</label>
								<input type="text" name="mail" id="mail" placeholder="correo1,Correo2,..."  value="'.$row[11].'">
							</div>

							<div class="col-md-12">
								<br>
								<label>CABECERA</label>
								<input type="text" name="header" id="header" placeholder="..."  value="'.static_empresa_nombre().'">
							</div>
							
							<input id="body" name="body" type="hidden" value="APRECIABLE <b>'.$row[2].'</b>. SE ADJUNTA <b>COTIZACION VIGENTE </b>%cot_cot%">
							
							<div class="col-md-12">
							<br>
								<label>Mensaje</label>
								<textarea placeholder="Escriba aqui un texto html si es necesario" name="txtxtra" id="txtxtra" class="custom-textarea"></textarea>
							</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" id="url_web" name="url_web" value="'.$_SERVER['HTTP_HOST'].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function sales_delete_finance ($inicio, $finaliza, $folio, $vendedor, $sucursal, $pagina)
	{
		//$inicio = '2018-07-18 00:00:00';
		//$finaliza = '2018-07-18 23:59:59';
		$inicio_old = $inicio;
		$f_inicio = $inicio_old . ' 00:00:00';
		
		$finaliza_old = $finaliza;
		$f_finaliza = $finaliza_old . ' 23:59:59';
		
		$total = 0;
		$porcent_comision = 0;
		
		$TAMANO_PAGINA = 10;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

        
		if ($folio != "" && $vendedor == 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.fecha_venta, f.t_pago, f.cobrado, c.correo, f.titulo FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.folio like '%$folio%'  order by c.id desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		elseif ($folio == "" && $vendedor > 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.fecha_venta, f.t_pago, f.cobrado, c.correo, f.titulo FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.vendedor = '$vendedor'  order by c.id desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		elseif ($folio == "" && $vendedor == 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.fecha_venta, f.t_pago, f.cobrado, c.correo, f.titulo FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal'  order by c.id desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		elseif ($folio == "" && $vendedor > 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.fecha_venta, f.t_pago, f.cobrado, c.correo, f.titulo FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' and f.sucursal = '$sucursal' and f.vendedor = '$vendedor'  order by c.id desc  LIMIT $inicio, $TAMANO_PAGINA");
		}
		else
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.fecha_venta, f.t_pago, f.cobrado, c.correo, f.titulo FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza'  order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$pdf = "";
			
			if ($_SESSION['super_pedidos'] == 1)
			{
				$eliminar = '
				<button type="button" class="btn btn-success" data-dismiss="modal">NO</button>
				<button type="sumbit" class="btn btn-danger">Si eliminar</button>';
			}else
			{
				$eliminar = '<button type="button" class="btn btn-success" data-dismiss="modal">NO</button>';
			}
			
			if (!empty($row[9]))
			{
				$pdf = '
				<div class="row">
					<div class="col-md-12">
					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingThree">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								<i class="zmdi zmdi-folder-outline"></i>
							<span> Firma: Terminos y condiciones</span>
							</a>
						</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
								<embed src="/'.$row[9].'" type="application/pdf" width="100%" height="600px" />
						</div>
						</div>
						</div>
					</div>
				</div>
				';
			}

			$body = $body.'
			<div class="modal fade" id="delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Elimnar registro</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
				
					<div class="col-md-12">
						<p>Tome en cuenta que al eliminar el registro, el folio sera elimnado de la base de datos y no existira mas, al igual que los productos asociados seran afectados.</p>
					</div>
					</div>
				</div>
				<div class="modal-footer">
					
					<form action="func/delete_f_venta.php" autocomplete="off" method="post">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						'.$eliminar.'
					</form>
					
				</div>
				</div>
			</div>
			</div>
			
			<!-- Detalles -->
			<div class="modal fade" id="details'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">INFORMACION VENTA # '.$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
    					<div class="col-md-6">
    						<p><b>VENDEDOR:</b> '.$row[1].'</p>
    					</div>
    					<div class="col-md-6">
    						<p><b>CLIENTE:</b> '.$row[2].'</p>
    					</div>
					</div>
					
					<div class="row">
    					<div class="col-md-6">
    						<p><b>FECHA REGISTRO:</b><br> '.GetFechaText($row[4]).'</p>
    					</div>
    					<div class="col-md-6">
    						<p><b>FECHA REMISION:</b><br> '.GetFechaText($row[5]).'</p>
    					</div>
					</div>
					
					<div class="row">
    					<div class="col-md-4">
    						<p><b>COBRADO:</b><br> $ '.$row[7].' MXN</p>
    					</div>
    					<div class="col-md-4">
    						<p><b>T. PAGO:</b><br> '.strtoupper($row[6]).'</p>
    					</div>
    					<div class="col-md-4">
    						<p><b>DESCUENTO:</b><br> '.($row[3] / 10).' %</p>
    					</div>
					</div>
					'.$pdf.'
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">OK</button>
				</div>
				</div>
			</div>
			</div>
			
			
			<!-- Entregar -->
			<div class="modal fade" id="delivery'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">ENTREGA DIGITAL VENTA # '.$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/entregar_sendmail.php" autocomplete="on" method="post" enctype="multipart/form-data">
							
							<div class="col-md-12">
								<label>Ingrese documento probatorio<span class="required">*</span></label>
                				<input type="file" name="titulo" id="titulo" accept="file/pdf" required>
							</div>

							<div class="col-md-12">
								<br>
								<label>Ingrese el correo del cliente</label>
								<input type="text" name="mail" id="mail" placeholder="correo1,Correo2,..."  value="'.$row[8].'" required>
							</div>

							<div class="col-md-12">
								<br>
								<label>CABECERA</label>
								<input type="text" name="header" id="header" placeholder="..."  value="'.static_empresa_nombre().'" required>
							</div>
							
							<input id="body" name="body" type="hidden" value="APRECIABLE <b>'.$row[2].'</b>. SE ADJUNTA <b>LICENCIA</b> Y ENLACE DE <b>DESCARGA</b>">
							
							<div class="col-md-12">
								<br>
								<label>ENLACE DE DESCARGA</label>
								<input type="URL" name="link" id="link" placeholder="Ingrese url de descarga" required>
							</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" id="url_web" name="url_web" value="'.$_SERVER['HTTP_HOST'].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Entregar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function sales_delete_finance_clients ($inicio, $finaliza, $client, $vendedor, $sucursal, $pagina)
	{
		//$inicio = '2018-07-18 00:00:00';
		//$finaliza = '2018-07-18 23:59:59';
		$inicio_old = $inicio;
		$f_inicio = $inicio_old . ' 00:00:00';
		
		$finaliza_old = $finaliza;
		$f_finaliza = $finaliza_old . ' 23:59:59';
		
		$total = 0;
		$porcent_comision = 0;
		
		$TAMANO_PAGINA = 50;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

        
		if ($vendedor > 0 && $sucursal == 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.vendedor = '$vendedor' and f.client = '$client'  and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		elseif ($vendedor == 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido , f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		elseif ($vendedor > 0 && $sucursal > 0)
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.sucursal = '$sucursal' and f.vendedor = '$vendedor' and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		else
		{
			$data = mysqli_query(db_conectar(),"SELECT f.folio, v.nombre, c.nombre, f.descuento, f.fecha, f.cobrado, f.fecha_venta, s.nombre, f.t_pago, f.pedido, f.concepto FROM folio_venta f, clients c, users v, sucursales s  WHERE f.vendedor = v.id and f.client = c.id and f.open = 0 and f.sucursal = s.id and f.client = '$client' and f.fecha_venta >= '$f_inicio' and f.fecha_venta <= '$f_finaliza' order by f.fecha_venta desc LIMIT $inicio, $TAMANO_PAGINA");
		}
		
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			
			if ($_SESSION['super_pedidos'] == 1)
			{
				$eliminar = '
				<button type="button" class="btn btn-success" data-dismiss="modal">NO</button>
				<button type="sumbit" class="btn btn-danger">Si eliminar</button>';
			}else
			{
				$eliminar = '<button type="button" class="btn btn-success" data-dismiss="modal">NO</button>';
			}
			

			$body = $body.'
			<div class="modal fade" id="delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Elimnar registro</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
				
					<div class="col-md-12">
						<p>Tome en cuenta que al eliminar el registro, el folio sera elimnado de la base de datos y no existira mas, al igual que los productos asociados seran afectados.</p>
					</div>
					</div>
				</div>
				<div class="modal-footer">
					
					<form action="func/delete_f_venta.php" autocomplete="off" method="post">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						'.$eliminar.'
					</form>
					
				</div>
				</div>
			</div>
			</div>
			
			';
		}
		
		return $body;
	}

	function sales_delete_credits ($client, $sucursal)
	{
		if ($client > 0)
		{
			if ($sucursal > 0)
			{
				$data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono, cc.correo FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id  and c.client =  '$client' and c.sucursal = '$sucursal' ORDER by  f_vencimiento asc");
			}else
			{
				$data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono, cc.correo FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id  and c.client =  '$client' ORDER by  f_vencimiento asc");
			}
		}else{
			if ($sucursal > 0)
			{
				$data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono, cc.correo FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id and c.pay = 0 and c.sucursal = '$sucursal' ORDER by  f_vencimiento asc");
			}else
			{
				$data = mysqli_query(db_conectar(),"SELECT c.id, cc.nombre, c.f_registro, INTERVAL c.dias_credit DAY + c.f_registro as f_vencimiento, c.factura, c.adeudo, (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) ) as abono, (c.adeudo - (c.abono + (SELECT COALESCE(SUM(monto), 0) as total FROM credit_pay WHERE credito = c.id) )) as pd_pago, DATEDIFF(DATE_ADD(c.f_registro,INTERVAL (c.dias_credit) DAY), NOW()) as dias_credit, s.nombre, cc.id, c.abono, cc.correo FROM credits c, clients cc, sucursales s WHERE c.client = cc.id and c.sucursal = s.id and c.pay = 0 ORDER by  f_vencimiento asc");
			}
		}
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			
			if ($_SESSION['super_pedidos'] == 1)
			{
				$eliminar = '
				<button type="button" class="btn btn-success" data-dismiss="modal">NO</button>
				<button type="sumbit" class="btn btn-danger">Si eliminar</button>';
			}else
			{
				$eliminar = '<button type="button" class="btn btn-success" data-dismiss="modal">NO</button>';
			}
			

			$body = $body.'
			<!-- Enviar mail a cliente-->
			<div class="modal fade" id="mail'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">ENVIAR CORREO ELECTRONICO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/sendmail_normal.php" autocomplete="on" method="post">
							<div class="col-md-12">
								<label>Ingrese el correo del cliente</label>
								<input type="text" name="mail_cliente" id="mail_cliente" placeholder="correo1,Correo2,..."  value="'.$row[12].'">
							</div>

							<div class="col-md-12">
								<br>
								<label>ASUNTO</label>
								<input type="text" name="asunto" id="asunto" placeholder="..."  value="Notificacion de adeudo">
							</div>
							<div class="col-md-12">
							<br>
								<label>Mensaje</label>
								<textarea name="body_msg" id="body_msg'.$row[0].'">HOLA ! <b>'.$row[1].'</b>, LE RECORDAMOS QUE USTED TIENE UN ADEUDO POR LA CANTIDAD <b>'.number_format($row[7],GetNumberDecimales(),".",",").' MXN</b> CON FOLIO: <a href="'.$_SERVER['HTTP_HOST'].'/sale_finaly_report_cotizacion.php?folio_sale='.$row[4].'">'.$row[4].'</textarea>
								<script>CKEDITOR.replace( body_msg'.$row[0].' );</script>
							</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			

			<div class="modal fade" id="delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Elimnar registro</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
				
					<div class="col-md-12">
						<p>Tome en cuenta que al eliminar el registro, el folio sera elimnado de la base de datos y no existira mas.</p>
					</div>
					</div>
				</div>
				<div class="modal-footer">
					<form action="func/delete_credit.php" autocomplete="off" method="post">
						<input type="hidden" id="id" name="id" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						'.$eliminar.'
					</form>
					
				</div>
				</div>
			</div>
			</div>
			
			<!-- Detalles -->
			<div class="modal fade" id="details'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">INFORMACION CREDITICIA # '.$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
    					<div class="col-md-6">
    						<p><b>CLIENTE:</b><br> '.$row[1].'</p>
						</div>

						<div class="col-md-6">
    						<p><b>FACTURA:</b><br> '.$row[4].'</p>
    					</div>
					</div>

					<div class="row">
    					<div class="col-md-12">
    						<p><b>SUCURSAL:</b><br> '.$row[9].'</p>
						</div>
					</div>
					
					<div class="row">
    					<div class="col-md-4">
    						<p><b>FECHA REGISTRO:</b><br> '.GetFechaText($row[2]).'</p>
    					</div>
    					<div class="col-md-4">
    						<p><b>FECHA VENCIMIENTO:</b><br> '.GetFechaText($row[3]).'</p>
						</div>
						<div class="col-md-4">
    						<p><b>DIAS DE CREDITO:</b><br> '.$row[8].' DIAS</p>
    					</div>
					</div>
					
					<div class="row">
    					<div class="col-md-4">
    						<p><b>TOTAL:</b><br> $ '.number_format($row[5],GetNumberDecimales(),".",",").' MXN</p>
    					</div>
    					<div class="col-md-4">
    						<p><b>ABONO:</b><br>$ '.number_format($row[6],GetNumberDecimales(),".",",").' MXN</p>
    					</div>
    					<div class="col-md-4">
    						<p><b>PENDIENTE PAGO:</b><br> '.number_format($row[7],GetNumberDecimales(),".",",").' MXN</p>
    					</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">OK</button>
				</div>
				</div>
			</div>
			</div>

			<div class="modal fade" id="liquid'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">ADEUDO TOTAL: $ '.number_format($row[7],GetNumberDecimales(),".",",").' MXN</h5>
				</div>
				<div class="modal-body">
					<div class="row">
					
					<form action="func/update_abono_credit.php" autocomplete="off" method="post">
						<div class="col-md-12">
							<br>
							<input type="hidden" id="folio" name="folio" value="'.$row[4].'">
							<label>Ingrese abono</label>
							<input type="number" step="0.0001"  name="abono" id="abono" placeholder="0.0" value= "'.$row[7].'" max= "'.$row[7].'" required >
						</div>
						
						</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="id" name="id" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
						<button type="sumbit" class="btn btn-success">ABONAR</button>;
					</form>
					
				</div>
				</div>
			</div>
			</div>

			';
		}
		
		return $body;
	}

    function table_facturas_options_modal ($pagina)
	{
		$TAMANO_PAGINA = 8;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$data = mysqli_query(db_conectar(),"SELECT f.folio, c.correo, f.serie FROM facturas f, clients c where f.cliente = c.id LIMIT $inicio, $TAMANO_PAGINA");

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<div class="modal fade" id="sendmail'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Reenviar factura: '.$row[2].$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="func/resendmail.php" autocomplete="off" method="post">
                    <div class="row">
				
					<div class="col-md-12">
						<p>Si desea agregar 1 o mas correos deberan ir separados por comas (,)</p>
                        <input type="text" name="cfdi_cliente_correo" id="cfdi_cliente_correo" placeholder="correo@empresa.com" required value="'.$row[1].'">
					</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
                        <input type="hidden" id="cfdi_serie" name="cfdi_serie" value="'.$row[2].$row[0].'">
                        <button type="sumbit" class="btn btn-primary" onclick="javascript:this.form.submit(); this.disabled= true;">Enviar</button>
					</form>
					
				</div>
				</div>
			</div>
			</div>
            
            
            <div class="modal fade" id="cancelcfdi33'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Cancelar factura: '.$row[2].$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="func/cancelar_cfdi33.php" autocomplete="off" method="post">
                    <div class="row">
				
					<div class="col-md-12">
						<center><p>Se realizara la cancelacion de la factura: '.$row[2].$row[0].', Es correcto ?</p></center>
					</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
                        <button type="sumbit" class="btn btn-danger" onclick="javascript:this.form.submit(); this.disabled= true;">Si, cancelar</button>
                </form>
					
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function table_facturas_options_modal_Search ($txt, $pagina)
	{
		$TAMANO_PAGINA = 8;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$data = mysqli_query(db_conectar(),"SELECT f.folio, c.correo, f.serie FROM facturas f, clients c where f.cliente = c.id and f.folio LIKE '%$txt%' or f.cliente = c.id and c.nombre LIKE '%$txt%'  LIMIT $inicio, $TAMANO_PAGINA ");
		$datatmp = mysqli_query(db_conectar(),"SELECT f.folio, c.correo, f.serie FROM facturas f, clients c where f.cliente = c.id and f.folio LIKE '%$txt%' or f.cliente = c.id and c.nombre LIKE '%$txt%'");

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<div class="modal fade" id="sendmail'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Reenviar factura: '.$row[2].$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="func/resendmail.php" autocomplete="off" method="post">
                    <div class="row">
				
					<div class="col-md-12">
						<p>Si desea agregar 1 o mas correos deberan ir separados por comas (,)</p>
                        <input type="text" name="cfdi_cliente_correo" id="cfdi_cliente_correo" placeholder="correo@empresa.com" required value="'.$row[1].'">
					</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
                        <input type="hidden" id="cfdi_serie" name="cfdi_serie" value="'.$row[2].$row[0].'">
                        <button type="sumbit" class="btn btn-primary" onclick="javascript:this.form.submit(); this.disabled= true;">Enviar</button>
					</form>
					
				</div>
				</div>
			</div>
			</div>
            
            
            <div class="modal fade" id="cancelcfdi33'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Cancelar factura: '.$row[2].$row[0].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="func/cancelar_cfdi33.php" autocomplete="off" method="post">
                    <div class="row">
				
					<div class="col-md-12">
						<center><p>Se realizara la cancelacion de la factura: '.$row[2].$row[0].', Es correcto ?</p></center>
					</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="folio" name="folio" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
                        <button type="sumbit" class="btn btn-danger" onclick="javascript:this.form.submit(); this.disabled= true;">Si, cancelar</button>
                </form>
					
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function table_SalesModal ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT v.id, p.nombre FROM product_venta v, productos p WHERE  v.product = p.id and folio_venta = '$folio' ");
		$gen = mysqli_query(db_conectar(),"SELECT v.id, v.p_generico FROM product_venta v WHERE v.p_generico != '' and folio_venta = '$folio' ");

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalsalequit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">QUITAR PRODUCTO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/product_sale_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro QUITAR el producto ? Se quitara este producto de esta lista de venta.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}

		while($row = mysqli_fetch_array($gen))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalsalequit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">QUITAR PRODUCTO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/product_sale_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro QUITAR el producto ? Se quitara este producto de esta lista de venta.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function table_SalesModal_order ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT v.id, p.nombre FROM product_pedido v, productos p WHERE  v.product = p.id and folio_venta = '$folio' ");
		$gen = mysqli_query(db_conectar(),"SELECT v.id, v.p_generico FROM product_pedido v WHERE v.p_generico != '' and folio_venta = '$folio' ");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalsalequit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">QUITAR PRODUCTO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/product_sale_delete_order.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro QUITAR el producto ? Se quitara este producto de esta lista de venta.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}

		while($row = mysqli_fetch_array($gen))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalsalequit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">QUITAR PRODUCTO: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/product_sale_delete_order.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro QUITAR el producto ? Se quitara este producto de esta lista de venta.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-danger">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function table_ClientesModal_search ($txt, $pagina)
	{
		$TAMANO_PAGINA = 10;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT * FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalclient_edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/client_edit.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Ingrese nombre de cliente<span class="required">*</span></label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre o razon social" required value="'.$row[1].'">
				</div>
				<div class="col-md-12">
					<br><label>Ingrese direccion de cliente</label>
					<input type="text" name="direccion" id="direccion" placeholder="Direccion fisica de cliente" value="'.$row[2].'">
				</div>
				
				<div class="col-md-12">
					<label>Ingrese telefono. (Puede ser mas de uno)</label>
					<input type="text" name="telefono" id="telefono" placeholder="Telefono de contacto" value="'.$row[3].'">
				</div>

				<div class="col-md-12">
					<br><label>Ingrese porcentaje de descuento<span class="required">*</span></label>
					<input type="number" name="p_descuento" id="p_descuento" placeholder="Ingrese el porcentaje para descuento en compras" min="0" max="100" required value="'.$row[4].'">
				</div>

				<div class="col-md-12">
					<br><label>Ingrese rfc para emitir factura</label>
					<input type="text" name="rfc" id="rfc" placeholder="Rfc de cliente o empresa" value="'.$row[5].'">
				</div>

				<div class="col-md-12">
					<br><label>Ingrese razon social</label>
					<input type="text" name="r_social" id="r_social" placeholder="Razon social de cliente o empresa" value="'.$row[6].'">
				</div>

				<div class="col-md-12">
					<br><label>Ingrese correo electronico</label>
					<input type="email" name="correo" id="correo" placeholder="Email de cliente o empresa" value="'.$row[7].'">
				</div>
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modalclient_delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/client_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro Elimnar el cliente ? Se eliminara el cliente y todos los registros asociados a el. En el futuro no sera posible recuperarlo.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			
			<!-- Enviar mail a cliente-->
			<div class="modal fade" id="mailcliente'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">ENVIAR CORREO ELECTRONICO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/sendmail_normal.php" autocomplete="on" method="post">
							<div class="col-md-12">
								<label>Ingrese el correo del cliente</label>
								<input type="text" name="mail_cliente" id="mail_cliente" placeholder="correo1,Correo2,..."  value="'.$row[7].'">
							</div>

							<div class="col-md-12">
								<br>
								<label>ASUNTO</label>
								<input type="text" name="asunto" id="asunto" placeholder="..."  value="'.static_empresa_nombre().'">
							</div>
							<div class="col-md-12">
							<br>
								<label>Mensaje</label>
								<textarea name="body_msg" id="body_msg'.$row[0].'">HOLA ! <b>'.$row[1].'</textarea>
								<script>CKEDITOR.replace( body_msg'.$row[0].' );</script>
							</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			
			<!-- Agregar anualidad -->
			<div class="modal fade" id="annuitycliente'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">NUEVA ANUALIDAD PARA: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/annuity_add.php" autocomplete="on" method="post">
							
                            <div class="col-md-12">
								<label>CONCEPTO</label>
								<input type="text" name="concepto" id="concepto" placeholder="Ingrese concepto">
							</div>
							
							<div class="col-md-12">
								<br>
								<label>Ingrese el precio anual</label>
								<input type="text" name="price" id="price" placeholder="$ 0.00 MXN">
							</div>
							
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" id="client" name="client" value="'.$row[0].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Agregar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function table_ClientesModal ($pagina)
	{
		$TAMANO_PAGINA = 10;
		
		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$data = mysqli_query(db_conectar(),"SELECT * FROM `clients` ORDER by nombre asc LIMIT $inicio, $TAMANO_PAGINA");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalclient_edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/client_edit.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
				<div class="col-md-12">
					<label>Ingrese nombre de cliente<span class="required">*</span></label>
					<input type="text" name="nombre" id="nombre" placeholder="Nombre o razon social" required value="'.$row[1].'">
				</div>
				<div class="col-md-12">
					<br><label>Ingrese direccion de cliente</label>
					<input type="text" name="direccion" id="direccion" placeholder="Direccion fisica de cliente" value="'.$row[2].'">
				</div>
				
				<div class="col-md-12">
					<label>Ingrese telefono. (Puede ser mas de uno)</label>
					<input type="text" name="telefono" id="telefono" placeholder="Telefono de contacto" value="'.$row[3].'">
				</div>
				<div class="col-md-12">
					<br><label>Ingrese porcentaje de descuento<span class="required">*</span></label>
					<input type="number" name="p_descuento" id="p_descuento" placeholder="Ingrese el porcentaje para descuento en compras" min="0" max="100" required value="'.$row[4].'">
				</div>
				<div class="col-md-12">
					<br><label>Ingrese rfc para emitir factura</label>
					<input type="text" name="rfc" id="rfc" placeholder="Rfc de cliente o empresa" value="'.$row[5].'">
				</div>
				<div class="col-md-12">
					<br><label>Ingrese razon social</label>
					<input type="text" name="r_social" id="r_social" placeholder="Razon social de cliente o empresa" value="'.$row[6].'">
				</div>
				<div class="col-md-12">
					<br><label>Ingrese correo electronico</label>
					<input type="email" name="correo" id="correo" placeholder="Email de cliente o empresa" value="'.$row[7].'">
				</div>
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			<!-- Modal -->
			<div class="modal fade" id="modalclient_delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/client_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro Elimnar el cliente ? Se eliminara el cliente y todos los registros asociados a el. En el futuro no sera posible recuperarlo.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			
			<!-- Enviar mail a cliente-->
			<div class="modal fade" id="mailcliente'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">ENVIAR CORREO ELECTRONICO</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/sendmail_normal.php" autocomplete="on" method="post">
							<div class="col-md-12">
								<label>Ingrese el correo del cliente</label>
								<input type="text" name="mail_cliente" id="mail_cliente" placeholder="correo1,Correo2,..."  value="'.$row[7].'">
							</div>

							<div class="col-md-12">
								<br>
								<label>ASUNTO</label>
								<input type="text" name="asunto" id="asunto" placeholder="..."  value="'.static_empresa_nombre().'">
							</div>
							<div class="col-md-12">
							<br>
								<label>Mensaje</label>
								<textarea name="body_msg" id="body_msg'.$row[0].'">HOLA ! <b>'.$row[1].'</textarea>
								<script>CKEDITOR.replace( body_msg'.$row[0].' );</script>
							</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			
			<!-- Agregar anualidad -->
			<div class="modal fade" id="annuitycliente'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">NUEVA ANUALIDAD PARA: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/annuity_add.php" autocomplete="on" method="post">
							
                            <div class="col-md-12">
								<label>CONCEPTO</label>
								<input type="text" name="concepto" id="concepto" placeholder="Ingrese concepto">
							</div>
							
							<div class="col-md-12">
								<br>
								<label>Ingrese el precio anual</label>
								<input type="text" name="price" id="price" placeholder="$ 0.00 MXN">
							</div>
							
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
						<input type="hidden" id="client" name="client" value="'.$row[0].'">
						<button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Agregar</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}


	function select_client_sale_modal ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$desc = "";
		$disabled = "";
		
		if ($_SESSION['change_suc'] == 0) { $disabled = "disabled"; }

		for ($i = 0; $i < 101; $i++)
		{
			$desc = $desc.'<option value="'.$i.'">'.$i.' %</option>';
		}

		for ($i = 1; $i < 101; $i++)
		{
			if ($i == $_SESSION['iva'])
			{
				$desc0 = $desc0.'<option value="'.$i.'" selected>'.$i.' %</option>';
			}else
			{
				$desc0 = $desc0.'<option value="'.$i.'">'.$i.' %</option>';
			}
		}
		
		$data = mysqli_query(db_conectar(),"SELECT * FROM clients ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$m_pago_ = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['change_suc'] == 1)
			{
				$select_ = '
				<div class="col-md-12">
					<br>
					<label>Seleccione sucursal en la que se realiza venta<span class="required">*</span></label>
					<select id="suc'.$row[0].'" name="suc'.$row[0].'" '.$disabled.'>
						'. Select_sucursales() .'
					</select>
				</div>
				<script>
					document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
					document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
				';
			}else
			{
				$select_ = '
					<input type="hidden" id="suc'.$row[0].'" name="suc'.$row[0].'" value="'.$_SESSION['sucursal'].'">
				';
			}
		
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="select_client_sale'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/create_sale.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Seleccione descuento<span class="required">*</span></label>
					<select id="desc'.$row[0].'" name="desc'.$row[0].'">
                    	'.$desc.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione % iva<span class="required">*</span></label>
					<select id="iva'.$row[0].'" name="iva'.$row[0].'" required>
                    	'.$desc0.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione tipo de pago<span class="required">*</span></label>
					<select id="t_pago" name="t_pago" required>
						'.$m_pago_.'
                	</select>
				</div>
				'.$select_.'
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Crear</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}

	function select_client_sale_modal_order ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		
		$desc = "";
		$disabled = "";
		
		if ($_SESSION['change_suc'] == 0) { $disabled = "disabled"; }

		for ($i = 0; $i < 101; $i++)
		{
			$desc = $desc.'<option value="'.$i.'">'.$i.' %</option>';
		}

		for ($i = 1; $i < 101; $i++)
		{
			if ($i == $_SESSION['iva'])
			{
				$desc0 = $desc0.'<option value="'.$i.'" selected>'.$i.' %</option>';
			}else
			{
				$desc0 = $desc0.'<option value="'.$i.'">'.$i.' %</option>';
			}
		}
		
		$data = mysqli_query(db_conectar(),"SELECT * FROM clients ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$m_pago_ = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['change_suc'] == 1)
			{
				$select_ = '
				<div class="col-md-12">
					<br>
					<label>Seleccione sucursal en la que se realiza venta<span class="required">*</span></label>
					<select id="suc'.$row[0].'" name="suc'.$row[0].'" '.$disabled.'>
						'. Select_sucursales() .'
					</select>
				</div>
				<script>
					document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
					document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
				';
			}else
			{
				$select_ = '
					<input type="hidden" id="suc'.$row[0].'" name="suc'.$row[0].'" value="'.$_SESSION['sucursal'].'">
				';
			}
		
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="select_client_sale'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/create_sale_order.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Seleccione descuento<span class="required">*</span></label>
					<select id="desc'.$row[0].'" name="desc'.$row[0].'">
                    	'.$desc.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione % iva<span class="required">*</span></label>
					<select id="iva'.$row[0].'" name="iva'.$row[0].'" required>
                    	'.$desc0.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione tipo de pago<span class="required">*</span></label>
					<select id="t_pago" name="t_pago" required>
						'.$m_pago_.'
                	</select>
				</div>
				'.$select_.'
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Crear</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}

	function select_client_sale_modal_cot ($pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}
		
		$desc = "";
		$disabled = "";
		
		if ($_SESSION['change_suc'] == 0) { $disabled = "disabled"; }

		for ($i = 0; $i < 101; $i++)
		{
			$desc = $desc.'<option value="'.$i.'">'.$i.' %</option>';
		}

		for ($i = 1; $i < 101; $i++)
		{
			if ($i == $_SESSION['iva'])
			{
				$desc0 = $desc0.'<option value="'.$i.'" selected>'.$i.' %</option>';
			}else
			{
				$desc0 = $desc0.'<option value="'.$i.'">'.$i.' %</option>';
			}
		}
		
		$data = mysqli_query(db_conectar(),"SELECT * FROM clients ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$m_pago_ = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['change_suc'] == 1)
			{
				$select_ = '
				<div class="col-md-12">
					<br>
					<label>Seleccione sucursal en la que se realiza venta<span class="required">*</span></label>
					<select id="suc'.$row[0].'" name="suc'.$row[0].'" '.$disabled.'>
						'. Select_sucursales() .'
					</select>
				</div>
				<script>
					document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
					document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
				';
			}else
			{
				$select_ = '
					<input type="hidden" id="suc'.$row[0].'" name="suc'.$row[0].'" value="'.$_SESSION['sucursal'].'">
				';
			}
		
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="select_client_sale'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/create_sale_cot.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Seleccione descuento<span class="required">*</span></label>
					<select id="desc'.$row[0].'" name="desc'.$row[0].'">
                    	'.$desc.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione % iva<span class="required">*</span></label>
					<select id="iva'.$row[0].'" name="iva'.$row[0].'" required>
                    	'.$desc0.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione tipo de pago<span class="required">*</span></label>
					<select id="t_pago" name="t_pago" required>
						'.$m_pago_.'
                	</select>
				</div>
				'.$select_.'
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Crear</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}

	function select_client_sale_modal_search ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$desc = "";
		$disabled = "";
		
		if ($_SESSION['change_suc'] == 0) { $disabled = "disabled"; }

		for ($i = 0; $i < 101; $i++)
		{
			$desc = $desc.'<option value="'.$i.'">'.$i.' %</option>';
		}
		
		for ($i = 1; $i < 101; $i++)
		{
			if ($i == $_SESSION['iva'])
			{
				$desc0 = $desc0.'<option value="'.$i.'" selected>'.$i.' %</option>';
			}else
			{
				$desc0 = $desc0.'<option value="'.$i.'">'.$i.' %</option>';
			}
		}

		$data = mysqli_query(db_conectar(),"SELECT * FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$m_pago_ = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['change_suc'] == 1)
			{
				$select_ = '
				<div class="col-md-12">
					<br>
					<label>Seleccione sucursal en la que se realiza venta<span class="required">*</span></label>
					<select id="suc'.$row[0].'" name="suc'.$row[0].'" '.$disabled.'>
						'. Select_sucursales() .'
					</select>
				</div>
				<script>
					document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
					document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
				';
			}else
			{
				$select_ = '
					<input type="hidden" id="suc'.$row[0].'" name="suc'.$row[0].'" value="'.$_SESSION['sucursal'].'">
				';
			}
			
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="select_client_sale'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/create_sale.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Seleccione descuento<span class="required">*</span></label>
					<select id="desc'.$row[0].'" name="desc'.$row[0].'">
                    	'.$desc.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione % iva<span class="required">*</span></label>
					<select id="iva'.$row[0].'" name="iva'.$row[0].'" required>
                    	'.$desc0.'
                	</select>
				</div>		

				<div class="col-md-12">
					<br><label>Seleccione tipo de pago<span class="required">*</span></label>
					<select id="t_pago" name="t_pago" required>
						'.$m_pago_.'
                	</select>
				</div>		
				
				'.$select_.'

			  	<script>
				  document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
				  document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Crear</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}


	function select_client_sale_modal_search_order ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$desc = "";
		$disabled = "";
		
		if ($_SESSION['change_suc'] == 0) { $disabled = "disabled"; }

		for ($i = 0; $i < 101; $i++)
		{
			$desc = $desc.'<option value="'.$i.'">'.$i.' %</option>';
		}
		
		for ($i = 1; $i < 101; $i++)
		{
			if ($i == $_SESSION['iva'])
			{
				$desc0 = $desc0.'<option value="'.$i.'" selected>'.$i.' %</option>';
			}else
			{
				$desc0 = $desc0.'<option value="'.$i.'">'.$i.' %</option>';
			}
		}

		$data = mysqli_query(db_conectar(),"SELECT * FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA");
		$m_pago_ = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['change_suc'] == 1)
			{
				$select_ = '
				<div class="col-md-12">
					<br>
					<label>Seleccione sucursal en la que se realiza venta<span class="required">*</span></label>
					<select id="suc'.$row[0].'" name="suc'.$row[0].'" '.$disabled.'>
						'. Select_sucursales() .'
					</select>
				</div>
				<script>
					document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
					document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
				';
			}else
			{
				$select_ = '
					<input type="hidden" id="suc'.$row[0].'" name="suc'.$row[0].'" value="'.$_SESSION['sucursal'].'">
				';
			}
			
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="select_client_sale'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/create_sale_order.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Seleccione descuento<span class="required">*</span></label>
					<select id="desc'.$row[0].'" name="desc'.$row[0].'">
                    	'.$desc.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione % iva<span class="required">*</span></label>
					<select id="iva'.$row[0].'" name="iva'.$row[0].'" required>
                    	'.$desc0.'
                	</select>
				</div>		

				<div class="col-md-12">
					<br><label>Seleccione tipo de pago<span class="required">*</span></label>
					<select id="t_pago" name="t_pago" required>
						'.$m_pago_.'
                	</select>
				</div>		
				
				'.$select_.'

			  	<script>
				  document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
				  document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Crear</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}

	function select_client_sale_modal_search_cot ($txt, $pagina)
	{
		$TAMANO_PAGINA = 5;

		if (!$pagina) {
			$inicio = 0;
			$pagina = 1;
		}
		else {
			$inicio = ($pagina - 1) * $TAMANO_PAGINA;
		}

		$desc = "";
		$disabled = "";
		
		if ($_SESSION['change_suc'] == 0) { $disabled = "disabled"; }

		for ($i = 0; $i < 101; $i++)
		{
			$desc = $desc.'<option value="'.$i.'">'.$i.' %</option>';
		}
		
		for ($i = 1; $i < 101; $i++)
		{
			if ($i == $_SESSION['iva'])
			{
				$desc0 = $desc0.'<option value="'.$i.'" selected>'.$i.' %</option>';
			}else
			{
				$desc0 = $desc0.'<option value="'.$i.'">'.$i.' %</option>';
			}
		}

		$data = mysqli_query(db_conectar(),"SELECT * FROM `clients` where `nombre` like '%$txt%' or `rfc` like '%$txt%' or `razon_social` like '%$txt%' or `correo` like '%$txt%' ORDER by id desc LIMIT $inicio, $TAMANO_PAGINA ");
		$m_pago_ = Metodo_Pago_ListBox();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			if ($_SESSION['change_suc'] == 1)
			{
				$select_ = '
				<div class="col-md-12">
					<br>
					<label>Seleccione sucursal en la que se realiza venta<span class="required">*</span></label>
					<select id="suc'.$row[0].'" name="suc'.$row[0].'" '.$disabled.'>
						'. Select_sucursales() .'
					</select>
				</div>
				<script>
					document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
					document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
				';
			}else
			{
				$select_ = '
					<input type="hidden" id="suc'.$row[0].'" name="suc'.$row[0].'" value="'.$_SESSION['sucursal'].'">
				';
			}
			
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="select_client_sale'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">CLIENTE: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form id="contact-form" action="func/create_sale_cot.php" method="post" autocomplete="off">
          <div class="row">
		  		<input type="hidden" id="id" name="id" value="'.$row[0].'">
				  
				<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

				<div class="col-md-12">
					<label>Seleccione descuento<span class="required">*</span></label>
					<select id="desc'.$row[0].'" name="desc'.$row[0].'">
                    	'.$desc.'
                	</select>
				</div>
				<div class="col-md-12">
					<br><label>Seleccione % iva<span class="required">*</span></label>
					<select id="iva'.$row[0].'" name="iva'.$row[0].'" required>
                    	'.$desc0.'
                	</select>
				</div>		

				<div class="col-md-12">
					<br><label>Seleccione tipo de pago<span class="required">*</span></label>
					<select id="t_pago" name="t_pago" required>
						'.$m_pago_.'
                	</select>
				</div>		
				
				'.$select_.'

			  	<script>
				  document.getElementById("desc'.$row[0].'").value = "'.$row[4].'";
				  document.getElementById("suc'.$row[0].'").value = "'.$_SESSION['sucursal'].'";
				</script>
			</div>
      
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Crear</button>
					</form>
				</div>
				</div>
			</div>
			</div>';
		}
		
		return $body;
	}

	function table_almacen ()
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `almacen` ORDER by nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalalmacen_edit'.$row[0].'" ><span> Editar</span> </a>
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalalmacen_delete'.$row[0].'" ><span> Eliminar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		
		return $body;
	}

	function table_sucursales ()
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `sucursales` ORDER by nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<tr>
			<td class="item-quality">'.$row[1].'</td>
			<td class="item-des"><p>'.$row[2].'</p></td>
			<td class="item-des"><p>'.$row[3].'</p></td>
			<td class="item-des">
			
			<div class="col-md-12">
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalsucursal_v_almacenes'.$row[0].'" ><span> V. Almacenes</span> </a>
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalsucursal_a_almacen'.$row[0].'" ><span> A. Almacen</span> </a>
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalsucursal_edit'.$row[0].'" ><span> Editar</span> </a>
			<a class="button extra-small button-black mb-20" data-toggle="modal" data-target="#modalalsucursal_delete'.$row[0].'" ><span> Eliminar</span> </a>
			</div>
			
			</td>
			</tr>
			';
		}
		
		return $body;
	}

	function table_almacenModal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `almacen` ORDER by nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalalmacen_edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ALMECEN: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form action="../func/almacen_edit.php" autocomplete="off" method="post">
					<div class="row">

					<input type="hidden" name="id" id="id" value="'.$row[0].'">

					<div class="col-md-12">
					<label>Nombre</label>
					<input type="text" name="almacen_nombre" id="almacen_nombre" placeholder="Ingrese nombre" value="'.$row[1].'">
					</div>
					
					<div class="col-md-12">
					<br><label>Ubicacion</label>
					<input type="text" name="almacen_ubicacion" id="almacen_ubicacion" placeholder="Ingrese ubicacion" value="'.$row[2].'">
					</div>

					<div class="col-md-12">
					<br><label>Telefono</label>
					<input type="text" name="almacen_telefono" id="almacen_telefono" placeholder="Ingrese telefono" value="'.$row[3].'">
					</div>
		
				</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modalalmacen_delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR ALMACEN: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/almacen_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro Elimnar el almacen ? Se eliminara el almacen y todos los productos asociados a el.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function table_SucursalModal ()
	{
		$data = mysqli_query(db_conectar(),"SELECT * FROM `sucursales` ORDER by nombre asc");
		
		$select_almacen = Select_Almacen();

		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body = $body.'
			<!-- Modal -->
			<div class="modal fade" id="modalsucursal_edit'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">SUCURSAL: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				
				<form action="../func/sucursal_edit.php" autocomplete="off" method="post">
					<div class="row">

					<input type="hidden" name="id" id="id" value="'.$row[0].'">

					<div class="col-md-12">
					<label>Nombre</label>
					<input type="text" name="almacen_nombre" id="almacen_nombre" placeholder="Ingrese nombre" value="'.$row[1].'">
					</div>
					
					<div class="col-md-12">
					<br><label>Direccion</label>
					<input type="text" name="almacen_ubicacion" id="almacen_ubicacion" placeholder="Ingrese ubicacion" value="'.$row[2].'">
					</div>

					<div class="col-md-12">
					<br><label>Telefono</label>
					<input type="text" name="almacen_telefono" id="almacen_telefono" placeholder="Ingrese telefono" value="'.$row[3].'">
					</div>
                    
                    <div class="col-md-12">
					<br><label>SERIE CFDI</label>
					<input type="text" name="serie_cfdi" id="serie_cfdi" placeholder="Ingrese telefono" value="'.$row[4].'">
					</div>
		
				</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Actualizar</button>
					</form>
				</div>
				</div>
			</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modalalsucursal_delete'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR SUCURSAL: '.$row[1].'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="../func/sucursal_delete.php" autocomplete="off" method="post">
					<div class="row">
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<div class="col-md-12">
						<br>
						<label>Esta seguro Elimnar la sucursal ? Se eliminara la sucursal y todos los productos asociados a el.</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Eliminar</button>
					</form>
				</div>
				</div>
			</div>
			</div>


			<!-- Agregar almacen -->
			<div class="modal fade" id="modalsucursal_a_almacen'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-body">
					<form action="../func/sucursal_add_almacen.php" autocomplete="off" method="post">
					<div class="row">
						
						<input type="hidden" name="id" id="id" value="'.$row[0].'">
						<input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">

						<div class="col-md-12">
						
						<label> Seleccione Almacen <span class="required">*</span></label>
						<select id="almacen" name="almacen">
							'.$select_almacen.'
						</select>                                       
					
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary" onclick="javascript:this.form.submit(); this.disabled= true;" >Agregar</button>
					</form>
				</div>
				</div>
			</div>
			</div>

			<!-- ver almacenes -->
			<div class="modal fade" id="modalsucursal_v_almacenes'.$row[0].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-body">
				'.GetAlmacen($row[0]).'
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
			';
		}
		
		return $body;
	}

	function MejoresVendedores()
	{
		$data = mysqli_query(db_conectar(),"SELECT nombre, direccion, telefono FROM `sucursales` ORDER by nombre asc");
		
		$body = "";
		while($row = mysqli_fetch_array($data))
	    {
			$body .= '
			<div class="single-testimonial text-center">
			<img alt="" src="/">
			<div class="testimonial-info white-bg clearfix">
			<div class="testimonial-author text-uppercase">
				<h5>'.$row[0].'</h5>
				<p></p>
			</div>
			<p>DIRECCION: '.$row[1].'
			<br>
			TELEFONO: '.$row[2].'
			</p>
			</div>
			</div>
			';
		}
		return $body;
	}

	function LoadValuesOfflineEmpresa ()
	{
		$tmp = mysqli_query(db_conectar(), "SELECT * FROM empresa");
		while($row = mysqli_fetch_array($tmp))
		{
			$_SESSION['empresa_nombre'] = $row[1];
			$_SESSION['empresa_nombre_corto'] = $row[2];
			$_SESSION['empresa_direccion'] = $row[3];
			$_SESSION['empresa_correo'] = $row[4];
			$_SESSION['empresa_telefono'] = $row[5];
			$_SESSION['empresa_mision'] = $row[6];
			$_SESSION['empresa_vision'] = $row[7];
			$_SESSION['empresa_contacto'] = $row[8];
			$_SESSION['empresa_fb'] = $row[9];
			$_SESSION['empresa_yt'] = $row[10];
			$_SESSION['empresa_tw'] = $row[11];
		}
	}

	function before ($a, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $a));
	};
	
	function after ($a, $inthat)
    {
        if (!is_bool(strpos($inthat, $a)))
        return substr($inthat, strpos($inthat,$a)+strlen($a));
	};
	
	//------    CONVERTIR NUMEROS A LETRAS         ---------------
	//------    Máxima cifra soportada: 18 dígitos con 2 decimales
	//------    999,999,999,999,999,999.99
	// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE BILLONES
	// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE MILLONES
	// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE PESOS 99/100 M.N.
	//------    Creada por:                        ---------------
	//------             ULTIMINIO RAMOS GALÁN     ---------------
	
	function numtoletras($xcifra)
	{
		$xarray = array(0 => "Cero",
			1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
			"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
			"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
			100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
		);
	//
		$xcifra = trim($xcifra);
		$xlength = strlen($xcifra);
		$xpos_punto = strpos($xcifra, ".");
		$xaux_int = $xcifra;
		$xdecimales = "00";
		if (!($xpos_punto === false)) {
			if ($xpos_punto == 0) {
				$xcifra = "0" . $xcifra;
				$xpos_punto = strpos($xcifra, ".");
			}
			$xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
			$xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
		}

		$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
		$xcadena = "";
		for ($xz = 0; $xz < 3; $xz++) {
			$xaux = substr($XAUX, $xz * 6, 6);
			$xi = 0;
			$xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
			$xexit = true; // bandera para controlar el ciclo del While
			while ($xexit) {
				if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
					break; // termina el ciclo
				}

				$x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
				$xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
				for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
					switch ($xy) {
						case 1: // checa las centenas
							if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
								
							} else {
								$key = (int) substr($xaux, 0, 3);
								if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
									$xseek = $xarray[$key];
									$xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
									if (substr($xaux, 0, 3) == 100)
										$xcadena = " " . $xcadena . " CIEN " . $xsub;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
									$xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
								}
								else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
									$key = (int) substr($xaux, 0, 1) * 100;
									$xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
									$xcadena = " " . $xcadena . " " . $xseek;
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 0, 3) < 100)
							break;
						case 2: // checa las decenas (con la misma lógica que las centenas)
							if (substr($xaux, 1, 2) < 10) {
								
							} else {
								$key = (int) substr($xaux, 1, 2);
								if (TRUE === array_key_exists($key, $xarray)) {
									$xseek = $xarray[$key];
									$xsub = subfijo($xaux);
									if (substr($xaux, 1, 2) == 20)
										$xcadena = " " . $xcadena . " VEINTE " . $xsub;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
									$xy = 3;
								}
								else {
									$key = (int) substr($xaux, 1, 1) * 10;
									$xseek = $xarray[$key];
									if (20 == substr($xaux, 1, 1) * 10)
										$xcadena = " " . $xcadena . " " . $xseek;
									else
										$xcadena = " " . $xcadena . " " . $xseek . " Y ";
								} // ENDIF ($xseek)
							} // ENDIF (substr($xaux, 1, 2) < 10)
							break;
						case 3: // checa las unidades
							if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
								
							} else {
								$key = (int) substr($xaux, 2, 1);
								$xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
								$xsub = subfijo($xaux);
								$xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
							} // ENDIF (substr($xaux, 2, 1) < 1)
							break;
					} // END SWITCH
				} // END FOR
				$xi = $xi + 3;
			} // ENDDO

			if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
				$xcadena.= " DE";

			if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
				$xcadena.= " DE";

			// ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
			if (trim($xaux) != "") {
				switch ($xz) {
					case 0:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN BILLON ";
						else
							$xcadena.= " BILLONES ";
						break;
					case 1:
						if (trim(substr($XAUX, $xz * 6, 6)) == "1")
							$xcadena.= "UN MILLON ";
						else
							$xcadena.= " MILLONES ";
						break;
					case 2:
						if ($xcifra < 1) {
							$xcadena = "CERO PESOS $xdecimales/100 M.N.";
						}
						if ($xcifra >= 1 && $xcifra < 2) {
							$xcadena = "UN PESO $xdecimales/100 M.N. ";
						}
						if ($xcifra >= 2) {
							$xcadena.= " PESOS $xdecimales/100 M.N. "; //
						}
						break;
				} // endswitch ($xz)
			} // ENDIF (trim($xaux) != "")
			// ------------------      en este caso, para México se usa esta leyenda     ----------------
			$xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
			$xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
			$xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
			$xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
		} // ENDFOR ($xz)
		return trim($xcadena);
	}

	// END FUNCTION

	function subfijo($xx)
	{ // esta función regresa un subfijo para la cifra
		$xx = trim($xx);
		$xstrlen = strlen($xx);
		if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
			$xsub = "";
		//
		if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
			$xsub = "MIL";
		//
		return $xsub;
	}

	// END FUNCTION

	function ReturnUrlImg1 ($produc)
	{
		$data = mysqli_query(db_conectar(),"SELECT foto0 FROM `productos` WHERE id = '$produc'");
		$r = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $r = $row[0];
	    }
		return $r;
	}

	function ReturnUrlImg2 ($produc)
	{
		$data = mysqli_query(db_conectar(),"SELECT foto1 FROM `productos` WHERE id = '$produc'");
		$r = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $r = $row[0];
	    }
		return $r;
	}

	function ReturnUrlImg3 ($produc)
	{
		$data = mysqli_query(db_conectar(),"SELECT foto2 FROM `productos` WHERE id = '$produc'");
		$r = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $r = $row[0];
	    }
		return $r;
	}

	function ReturnUrlImg4 ($produc)
	{
		$data = mysqli_query(db_conectar(),"SELECT foto3 FROM `productos` WHERE id = '$produc'");
		$r = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $r = $row[0];
	    }
		return $r;
	}

	function ReturnStockSubProduct ($id)
	{
		$data = mysqli_query(db_conectar(),"SELECT stock FROM `productos_sub` WHERE id = '$id';");
		$r = "";
		while($row = mysqli_fetch_array($data))
	    {
	        $r = $row[0];
	    }
		return $r;
	}

    function ExistFact ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT folio FROM `facturas` WHERE folio = '$folio';");
		$r = false;
		while($row = mysqli_fetch_array($data))
	    {
	        $r = true;
	    }
		return $r;
	}

    function ReturnSerieSelect ($folio)
	{
		$data = mysqli_query(db_conectar(),"SELECT s.cfdi_serie FROM folio_venta v, sucursales s WHERE v.sucursal = s.id and v.folio = '$folio';");
		$s = "";
        $r = '
        <div class="country-select shop-select col-md-3">
            <label> Serie <span class="required">*</span></label>
            <select id="cfdi_serie" name = "cfdi_serie">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="F">F</option>
                <option value="G">G</option>
                <option value="H">H</option>
                <option value="I">I</option>
                <option value="J">J</option>
                <option value="K">K</option>
                <option value="L">L</option>
                <option value="M">M</option>
                <option value="N">N</option>
                <option value="O">O</option>
                <option value="P">P</option>
                <option value="Q">Q</option>
                <option value="R">R</option>
                <option value="S">S</option>
                <option value="T">T</option>
                <option value="V">V</option>
                <option value="W">W</option>
                <option value="X">X</option>
                <option value="Y">Y</option>
                <option value="Z">Z</option>
            </select>                                       
        </div>
        ';
		while($row = mysqli_fetch_array($data))
	    {
	        $s = $row[0];
	    }
        $r .= '
        <script>
            document.getElementById("cfdi_serie").value = "'.$s.'";
        </script>';
        
		return $r;
	}

    function ReturnSerieT_pago ($folio)
        {
            $data = mysqli_query(db_conectar(),"SELECT v.t_pago FROM folio_venta v, sucursales s WHERE v.sucursal = s.id and v.folio =  '$folio';");
            $s = "";
            $r = '
            <div class="country-select shop-select col-md-6">
                    <label> Forma de pago<span class="required">*</span></label>
                    <select id="cfdi_f_pago" name = "cfdi_f_pago">
                        <option value="01">Efectivo</option>
                        <option value="02">Cheque Nominativo</option>
                        <option value="03">Transferencia electrónica de fondos</option>
                        <option value="04">Tarjetas de crédito</option>
                        <option value="05">Monedero electrónico</option>
                        <option value="06">Dinero electrónico</option>
                        <option value="08">Vales de despensa</option>
                        <option value="28">Tarjeta de Débito</option>
                        <option value="29">Tarjeta de Servicio</option>
                        <option value="99">Otros</option>
                    </select>                                       
                </div>
            ';
            while($row = mysqli_fetch_array($data))
            {
                $s = $row[0];
            }
            
            if ($s == "efectivo")
            {
                $s = "01";
            }
        
            elseif($s == "transferencia")
            {
                $s = "03";
            }
        
            elseif($s == "tarjeta")
            {
                $s = "28";
            }
            else
            {
                $s = "99";
            }
            
                
                
            $r .= '
            <script>
                document.getElementById("cfdi_f_pago").value = "'.$s.'";
            </script>';

            return $r;
        }

		function loginPermanent ($url)
		{
			session_start();
			if(!isset($_SESSION['users_id']) && isset($_COOKIE['clta_session'])) 
			{
				$con = db_conectar();
				$user = mysqli_real_escape_string($con, $_COOKIE['clta_session_user']);
				$pass = mysqli_real_escape_string($con, $_COOKIE['clta_session_pass']);
				$consulta = mysqli_query($con, "SELECT * FROM users WHERE username = '$user' AND password = '$pass'");
				if (mysqli_num_rows($consulta) > 0)
				{
						while($row = mysqli_fetch_array($consulta))
						{
						$_SESSION['users_id'] = $row[0];
						$_SESSION['users_username'] = $row[1];
						$_SESSION['users_nombre'] = $row[3];
						$_SESSION['users_foto'] = $row[4];
						$_SESSION['product_add'] = $row[5];
						$_SESSION['product_gest'] = $row[6];
						$_SESSION['gen_orden_compra'] = $row[7];
						$_SESSION['client_add'] = $row[8];
						$_SESSION['client_guest'] = $row[9];
						$_SESSION['almacen_add'] = $row[10];
						$_SESSION['almacen_guest'] = $row[11];
						$_SESSION['depa_add'] = $row[12];
						$_SESSION['depa_guest'] = $row[13];
						$_SESSION['propiedades'] = $row[14];
						$_SESSION['usuarios'] = $row[15];
						$_SESSION['finanzas'] = $row[16];
						$_SESSION['sucursal'] = $row[18];
						$_SESSION['change_suc'] = $row[19];
						$_SESSION['sucursal_gest'] = $row[20];
						$_SESSION['caja'] = $row[21];
						$_SESSION['super_pedidos'] = $row[22];
						$_SESSION['vtd_pg'] = $row[25];
						}
						
						$tmp = mysqli_query($con, "SELECT * FROM empresa");
						while($row = mysqli_fetch_array($tmp))
						{
						$_SESSION['empresa_nombre'] = $row[1];
						$_SESSION['empresa_nombre_corto'] = $row[2];
						$_SESSION['empresa_direccion'] = $row[3];
						$_SESSION['empresa_correo'] = $row[4];
						$_SESSION['empresa_telefono'] = $row[5];
						$_SESSION['empresa_mision'] = $row[6];
						$_SESSION['empresa_vision'] = $row[7];
						$_SESSION['empresa_contacto'] = $row[8];
						$_SESSION['empresa_fb'] = $row[9];
						$_SESSION['empresa_yt'] = $row[10];
						$_SESSION['empresa_tw'] = $row[11];
						$_SESSION['iva'] = $row[12];
						$_SESSION['empresa_footer'] = $row[13];
						$_SESSION['cfdi_lugare_expedicion'] = $row[14];
						$_SESSION['cfdi_rfc'] = $row[15];
						$_SESSION['cfdi_regimen'] = $row[16];
						$_SESSION['cfdi_cer'] = $row[17];
						$_SESSION['cfdi_key'] = $row[18];
						$_SESSION['cfdi_pass'] = $row[19];
						$_SESSION['token'] = $row[20];
						}
						setcookie('clta_session', 'yes', time() + (86400 * 30), "/");
						setcookie('clta_session_user', $user, time() + (86400 * 30), "/");
						setcookie('clta_session_pass', $pass, time() + (86400 * 30), "/");
						echo '<script>location.href = "'.$url.'"</script>';
				}
				else
				{
						setcookie('clta_session', '', 0, "/");
						setcookie('clta_session_user', '', 0, "/");
						setcookie('clta_session_pass', '', 0, "/");
						echo '<script>location.href = "/login.php"</script>';
				}
			}
		}

		function loginPermanent_login ()
		{
			if(isset($_COOKIE['clta_session']))
			{
				$con = db_conectar();
				$user = mysqli_real_escape_string($con, $_COOKIE['clta_session_user']);
				$pass = mysqli_real_escape_string($con, $_COOKIE['clta_session_pass']);
				$consulta = mysqli_query($con, "SELECT * FROM users WHERE username = '$user' AND password = '$pass'");
				if (mysqli_num_rows($consulta) > 0)
				{
						while($row = mysqli_fetch_array($consulta))
						{
						$_SESSION['users_id'] = $row[0];
						$_SESSION['users_username'] = $row[1];
						$_SESSION['users_nombre'] = $row[3];
						$_SESSION['users_foto'] = $row[4];
						$_SESSION['product_add'] = $row[5];
						$_SESSION['product_gest'] = $row[6];
						$_SESSION['gen_orden_compra'] = $row[7];
						$_SESSION['client_add'] = $row[8];
						$_SESSION['client_guest'] = $row[9];
						$_SESSION['almacen_add'] = $row[10];
						$_SESSION['almacen_guest'] = $row[11];
						$_SESSION['depa_add'] = $row[12];
						$_SESSION['depa_guest'] = $row[13];
						$_SESSION['propiedades'] = $row[14];
						$_SESSION['usuarios'] = $row[15];
						$_SESSION['finanzas'] = $row[16];
						$_SESSION['sucursal'] = $row[18];
						$_SESSION['change_suc'] = $row[19];
						$_SESSION['sucursal_gest'] = $row[20];
						$_SESSION['caja'] = $row[21];
						$_SESSION['super_pedidos'] = $row[22];
						$_SESSION['vtd_pg'] = $row[25];
						}
						
						$tmp = mysqli_query($con, "SELECT * FROM empresa");
						while($row = mysqli_fetch_array($tmp))
						{
						$_SESSION['empresa_nombre'] = $row[1];
						$_SESSION['empresa_nombre_corto'] = $row[2];
						$_SESSION['empresa_direccion'] = $row[3];
						$_SESSION['empresa_correo'] = $row[4];
						$_SESSION['empresa_telefono'] = $row[5];
						$_SESSION['empresa_mision'] = $row[6];
						$_SESSION['empresa_vision'] = $row[7];
						$_SESSION['empresa_contacto'] = $row[8];
						$_SESSION['empresa_fb'] = $row[9];
						$_SESSION['empresa_yt'] = $row[10];
						$_SESSION['empresa_tw'] = $row[11];
						$_SESSION['iva'] = $row[12];
						$_SESSION['empresa_footer'] = $row[13];
						$_SESSION['cfdi_lugare_expedicion'] = $row[14];
						$_SESSION['cfdi_rfc'] = $row[15];
						$_SESSION['cfdi_regimen'] = $row[16];
						$_SESSION['cfdi_cer'] = $row[17];
						$_SESSION['cfdi_key'] = $row[18];
						$_SESSION['cfdi_pass'] = $row[19];
						$_SESSION['token'] = $row[20];
						}
						setcookie('clta_session', 'yes', time() + (86400 * 30), "/");
						setcookie('clta_session_user', $user, time() + (86400 * 30), "/");
						setcookie('clta_session_pass', $pass, time() + (86400 * 30), "/");
						echo '<script>location.href = "/products.php?pagina=1"</script>';
				}
				else
				{
						setcookie('clta_session', '', 0, "/");
						setcookie('clta_session_user', '', 0, "/");
						setcookie('clta_session_pass', '', 0, "/");
						echo '<script>location.href = "/index.php"</script>';
				}
			}
		}
	function Metodo_Pago_ListBox ()
	{
		return '
		<option value="efectivo" selected>Efectivo</option>
		<option value="transferencia">Tranferencia</option>
		<option value="tarjeta">Tarjeta</option>
		<option value="deposito">Deposito</option>
		<option value="cheque">Cheque</option>
		<option value="oxxo">Oxxo</option>
		';
	}
	
	function SendMailLog ($folio, $open)
	{
	    $cliente = "";
	    $correo = "";
	    
    	$data = mysqli_query(db_conectar(),"SELECT c.nombre ,c.correo FROM folio_venta f, clients c WHERE f.client = c.id and folio =" . $folio);
		
		
		if($row = mysqli_fetch_array($data))
	    {
	        $cliente = $row[0];
	        $correo = $row[1];
	    }
	    
	    $correo .= ','.static_empresa_email().'';
	    
	    $correo = str_replace("", ",,", $correo);
	    
	    
        $message = '<center><br>APRECIABLE <b>'.$cliente.'</b>, SE EMITE <b>REMISION</b> DE SU COMPRA, <a href="'.GetDominio().'/sale_finaly_report.php?folio_sale='.$folio.'" target="_blank">VISUALIZAR</a><br><br></center>';
        
        $formato = '
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
				<div class="opps">
				<div class="opps-header">
					<div class="opps-reminder">REMISION DE VENTA</div>
					<div class="opps-info">
						<div class="opps-reference">
							<h4>FOLIO</h4>
					<h3><a href="'.GetDominio().'/sale_finaly_report.php?folio_sale='.$folio.'" target="_blank">'.$folio.'</a></h3>
								</div>
						</div>
                  		<span>'.$message.'</span>
                  </p>
						<div class="opps-instructions">
							<h2>Servicio post venta</h2>
							<ol>
								<li>Contacto xpres por <a href="'.urlWhatsapp().'" target="_blank">whatsapp</a></li>
							</ol>
							<div class="opps-footnote"><strong>AGRADECEMOS SU COMPRA</strong></div>
						</div>
					</div>	
				</body>
			</html>
        ';
		
		if ($open)
		{
			require '../phpmailer/PHPMailerAutoload.php';
		}
    
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        
        $mail->Username = "documentos@cyberchoapas.com";
        $mail->Password = "8b19e87ff57efaace42006fb1d6ba6c8";
        $mail->setFrom(static_empresa_email(), static_empresa_nombre() );
        $mail->AddReplyTo(static_empresa_email(), static_empresa_nombre() );
        
        //Email receptor
        $ArrMail = explode(",",$correo);
        
        foreach ($ArrMail as $valor) {
            $mail->addAddress($valor);
        }
    
        
        //Asunto
        $mail->Subject = 'VENTA REGISTRADA CON EXITO';
      
        $mail->msgHTML(file_get_contents($formato), __DIR__);
        //Replace the plain text body with one created manually  
        $mail->Body = $formato;
        
        $mail->send();
    }    
    
    function Return_TotalPagar_Folio ($folio)
	{
		
        $con = db_conectar();  
        
        $venta = mysqli_query($con,"SELECT u.nombre, c.nombre, v.descuento, v.fecha, v.cobrado, v.fecha_venta, s.nombre, s.direccion, s.telefono, v.iva, c.razon_social, c.direccion FROM folio_venta v, users u, clients c, sucursales s WHERE v.vendedor = u.id and v.client = c.id and v.sucursal = s.id and v.folio = '$folio'");
        
        while($row = mysqli_fetch_array($venta))
        {
            $vendedor = $row[0];
			$cliente = $row[1];
			$descuento = $row[2];
			$fecha_ini = $row[3];
			$cobrado = $row[4];
			$fecha_fini = $row[5];
			$sucursal = $row[6];
			$direccion = $row[7];
			$tel = $row[8];
			$iva = $row[9];
			$bodysucursal = $row[7] . '
			<br><span style="font-size: 14px;">RESPONSABLE: ' . $vendedor . '</span>';
			$r_social = $row[10];
			$cliente_direccion = $row[11];
        }
    
        $genericos = mysqli_query($con,"SELECT unidades, p_generico, precio, id FROM product_venta v WHERE p_generico != '' and folio_venta = '$folio'");
    
        $products = mysqli_query($con,"SELECT p.nombre, p.`no. De parte`, v.unidades, v.precio , a.nombre, p.loc_almacen, v.product_sub, p.stock FROM product_venta v, productos p, almacen a WHERE v.product = p.id and p.almacen = a.id and v.folio_venta = '$folio'");
    
        while($row = mysqli_fetch_array($products))
        {
            $total_sin = $total_sin + ($row[2] * $row[3]);
        }
        
        while($row = mysqli_fetch_array($genericos))
        {
            $total_sin = $total_sin + ($row[0] * $row[2]);
        }
    
        $ivac = '.'.$iva;
    
        $total_pagar = $total_sin - ($total_sin * ($descuento / 100));
        $total_pagar_ = $total_pagar;
        
        $subtotal = ($total_pagar / 1.160000);
    
        $iva_ = $total_pagar - $subtotal;
    
        $subtotal = number_format($subtotal,GetNumberDecimales(),".",",");
        //$total_pagar = number_format($total_pagar,GetNumberDecimales(),".",",");
        $iva_ = number_format($iva_,GetNumberDecimales(),".",",");
        
        return $total_pagar;
	}

	function remove_url_query_args($url,$keys=array()) {
        $url_parts = parse_url($url);
        if(empty($url_parts['query'])) return $url;
                
        parse_str($url_parts['query'], $result_array);
        foreach ( $keys as $key ) { unset($result_array[$key]); }
        $url_parts['query'] = http_build_query($result_array);
        $url = (isset($url_parts["scheme"])?$url_parts["scheme"]."://":"").
                (isset($url_parts["user"])?$url_parts["user"].":":"").
                (isset($url_parts["pass"])?$url_parts["pass"]."@":"").
                (isset($url_parts["host"])?$url_parts["host"]:"").
                (isset($url_parts["port"])?":".$url_parts["port"]:"").
                (isset($url_parts["path"])?$url_parts["path"]:"").
                (isset($url_parts["query"])?"?".$url_parts["query"]:"").
                (isset($url_parts["fragment"])?"#".$url_parts["fragment"]:"");
        return $url;
    }
    
    function GetFechaText ($fecha) {
      $time = $fecha;
      $fecha = substr($fecha, 0, 10);
      
      $numeroDia = date('d', strtotime($fecha));
      
      $dia = date('l', strtotime($fecha));
      $mes = date('F', strtotime($fecha));
      $anio = date('Y', strtotime($fecha));
      
      $dias_ES = array("Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do");
      $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
      $nombredia = str_replace($dias_EN, $dias_ES, $dia);
      
      $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Noviembre", "Diciembre");
      $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
      $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
      
      $hora = date('G', strtotime($time));
      $minuto = date('i', strtotime($time));
      $segundo = date('s', strtotime($time));
      
      return strtoupper($nombredia)." ".$numeroDia."-".strtoupper($nombreMes)."-".$anio .' '. $hora .':'. $minuto .':'. $segundo;
    }
    
    function MailConfig ()
    {
        require '../phpmailer/PHPMailerAutoload.php';
    
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        
        $mail->Username = "documentos@cyberchoapas.com";
        $mail->Password = "8b19e87ff57efaace42006fb1d6ba6c8";
        $mail->setFrom(static_empresa_email(), static_empresa_nombre() );
		$mail->AddReplyTo(static_empresa_email(), static_empresa_nombre() );
		return $mail;
    }
    
    function GetUsd ()
    {
        $r = 0;
        $data = file_get_contents("https://www.floatrates.com/daily/mxn.json");
        $divisas = json_decode($data, true);
        foreach ($divisas as $moneda) 
        {
            if ($moneda["code"] == "USD")
            {
                $r = $moneda["inverseRate"];
                break;
            }
            
        }
        $r = $r / 1.05;
        
        return $r; 
    }
    
    function GetUsdToMXN ($price)
    {
        $r = 0;
        $data = file_get_contents("https://www.floatrates.com/daily/mxn.json");
        $divisas = json_decode($data, true);
        foreach ($divisas as $moneda) 
        {
            if ($moneda["code"] == "USD")
            {
                $r = $moneda["inverseRate"];
                break;
            }
            
        }
        $r = $r / 1.05;
        
        return $price / $r; 
    }
    
    function ValidateAnnuities ()
	{
		$con = db_conectar();
		
		$data = mysqli_query(db_conectar(),"SELECT id, date_last FROM annuities");
		while($row = mysqli_fetch_array($data))
	    {
            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
            $fecha_db = strtotime($row[1]."+ 362 days");
            
		    if($fecha_actual > $fecha_db)
        	{
        	    //La fecha actual es mayor a la comparada.
        	    mysqli_query($con,"UPDATE annuities SET active = 0 WHERE id = '$row[0]';");
        	}
	    }
	}

	function table_service_support($pagina, $folio, $url)
	{		
		$data = mysqli_query(db_conectar(),"SELECT * FROM `soporte` ORDER BY id DESC");

		$body = '
		<div class="country-select shop-select col-md-12">
		<center><br><label>SERVICIOS DISPONIBLES A AGREGAR</label></center>

		<form action="func/producst_add_sale_soport.php" autocomplete="off" method="post">
        
        <input type="hidden" id="url" name="url" value="'.$url.'">
        <input type="hidden" id="folio" name="folio" value="'.$folio.'">
			<select id="soporte_id" name = "soporte_id">
		';
		while($row = mysqli_fetch_array($data))
		{
			$body .='
			<option value="'.$row[0].'">'.'['.$row[0].'] '.$row[1].' ($ '.number_format($row[2],GetNumberDecimales(),".",",").' MXN)</option>
			';
		}

		$body .= '
			</select>                                       
		</div>
		<div class="col-lg-12 col-md-12 text-center">
			<button type="submit" class="btn btn-primary mb-20">Agregar</button>
			</form>
		</div>
		';

		return $body;
	}
	
	function SendMailPayOxxo ($mail, $referencia)
	{
	    $correo = $mail.','.static_empresa_email().'';
		$correo = str_replace("", ",,", $correo);
	    
	    
        $message = '<center><br /><strong>PAGO</strong>&nbsp;ACREDITADO<br /><br /></center>';
        
        $formato = '
        <html>
			<head>
				<meta charset="utf-8">
				<link href="styles.css" media="all" rel="stylesheet" type="text/css" />
			<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
			<style>
				/* Reset -------------------------------------------------------------------- */
			* 	 { margin: 0;padding: 0; }
				body { font-size: 14px; }
				/* OPPS --------------------------------------------------------------------- */
				h3 {
					margin-bottom: 10px;
					font-size: 13px;
					font-weight: 600;
					text-transform: uppercase;
				}
				.opps {
					width: 496px; 
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
				.opps-brand {
					width: 45%;
					float: left;
				}
				.opps-brand img {
					max-width: 150px;
					margin-top: 2px;
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
				h1 {
					font-size: 27px;
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
					margin: 17px 0 0 16px;
				}
				li + li {
					margin-top: 10px;
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
								<div class="opps-reminder">SOFTBOXZAC</div>
								<div class="opps-info">
						
						<div class="opps-ammount">
						<center><h3>PAGO CON REF: '.$referencia.' ACREDITADO.</h3></center>
									</div>
						<hr><br>
					</body>
				</html>
        ';
        //require '../phpmailer/PHPMailerAutoload.php';
    
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        
        $mail->Username = "documentos@cyberchoapas.com";
        $mail->Password = "8b19e87ff57efaace42006fb1d6ba6c8";
        $mail->setFrom(static_empresa_email(), static_empresa_nombre() );
        $mail->AddReplyTo(static_empresa_email(), static_empresa_nombre() );
        
        //Email receptor
        $ArrMail = explode(",",$correo);
        
        foreach ($ArrMail as $valor) {
            $mail->addAddress($valor);
        }
    
        
        //Asunto
        $mail->Subject = 'OXXO PAY ACREDITADO';
      
        $mail->msgHTML(file_get_contents($formato), __DIR__);
        //Replace the plain text body with one created manually  
        $mail->Body = $formato;
        
        $mail->send();
	}
	
	function GetToken ()
	{
		$data = mysqli_query(db_conectar(),"SELECT token FROM `empresa`  where id = 1");
		while($row = mysqli_fetch_array($data))
	    {
			$body = $row[0];
	    }
		return $body;
	}
	
	function MailLogText ($txt, $asunto)
	{
		$mail = MailConfig();
		
		//Email receptor
		$ArrMail = explode(",",static_empresa_email());
		
		foreach ($ArrMail as $valor) {
			$mail->addAddress($valor);
		}

		//Asunto
		$mail->Subject = $asunto;
	
		$mail->msgHTML(file_get_contents($txt), __DIR__);
		//Replace the plain text body with one created manually  
		$mail->Body = $txt;
		
		$mail->send();
	}
	
	function UpdateAdeudoCredits ($folio)
	{
	    $total = Return_TotalPagar_Folio($folio);
	    mysqli_query(db_conectar(),"UPDATE credits SET adeudo = $total WHERE factura = $folio ");
	}
?>
