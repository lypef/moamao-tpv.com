<?php
  error_reporting(0);
  include 'func/db.php';
  LoadValuesOfflineEmpresa();
  $departamentos = mysqli_query(db_conectar(),"SELECT id, nombre FROM departamentos");
  $departamentos_ = mysqli_query(db_conectar(),"SELECT id, nombre FROM departamentos");
  ValidateAnnuities();
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $_SESSION['empresa_nombre'] ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">

    <!-- All css files are included here -->
    <!-- Bootstrap fremwork main css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- This core.css file contents all plugings css file. -->
    <link rel="stylesheet" href="css/core.css">
    <!-- Theme shortcodes/elements style -->
    <link rel="stylesheet" href="css/shortcode/shortcodes.css">
    <!-- Theme main style -->
    <link rel="stylesheet" href="style.css">
    <!-- Responsive css -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- User style -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- Style customizer (Remove these two lines please) -->
    <link rel="stylesheet" href="css/color/skin-default.css">


    <!-- Modernizr JS -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
    <style type="text/css">
    body {
        overflow: hidden;
    }
    /* preloader */
    #preloader {
        position: fixed;
        top:0; left:0;
        right:0; bottom:0;
        background: #000;
        z-index: 100;
    }
    #loader {
        width: 100px;
        height: 100px;
        position: absolute;
        left:50%; top:50%;
        background: url(images/_loader.gif) no-repeat center 0;
        margin:-50px 0 0 -50px;
    }
    </style>
<body>
    <div id="preloader">
        <div id="loader"></div>
    </div>
    <div id="main">
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Body main wrapper start -->
    <div class="wrapper">
        <!-- Start of header area -->
        <header>
            <div class="header-top-bar white-bg ptb-20">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="header-top">
                                <ul>
                                <li class="lh-50">
                                    <form action="index.php" autocomplete="off">
                                        <input type="text" placeholder="Buscar" name="search" autocomplete="off">
                                    </form>
                                </li>
                            </div>
                        </div>
                        
                        <div class="col-md-4 hidden-sm hidden-xs">
                            <div class="middle text-center">
                                <ul>
                                    <li class="mr-30 lh-50">
                                        <a href="/index.php"><strong><i class="zmdi zmdi-store"></i></strong> <?php echo EmpresaNombre() ;?></a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="header-top header-top-right">
                                <ul>
                                <li class="lh-50">
                                    <a href="#" class="prl-20 text-uppercase">DEPARTAMENTOS</a>
                                    <div class="header-top-down header-top-hover header-top-down-lang pl-15 lh-35 lh-35">
                                        <ul>
                                            <?php
                                            while($row = mysqli_fetch_array($departamentos))
                                            {
                                                echo '<li><a href=index.php?department='.$row[0].'>'.$row[1].'</a></li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </li>
                                <li class="lh-50">
                                        <a href="login.php" class="prl-20 text-uppercase">Login</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sticky-header" class="header-area header-wrapper transparent-header">
                <div class="header-middle-area black-bg">
                    <div class="container">
                        <div class="full-width-mega-dropdown">
                            <div class="row">
                                <div class="col-md-12">
                                    <nav id="primary-menu">
                                        <ul class="main-menu text-center">
                                            <li class="mega-parent"><a href="#"><i class="zmdi zmdi-equalizer"></i> Ofertas</a>
                                                <div class="mega-menu-area header-top-hover p-30">
                                                    <?php
                                                        echo ReturnProductsOferta();
                                                    ?>

                                                </div>
                                            </li>
                                            <li class="mega-parent"><a href="#"><i class="zmdi zmdi-plus"></i> Lo mas nuevo</a>
                                                <div class="mega-menu-area header-top-hover p-30">
                                                    
                                                <?php
                                                    while($row = mysqli_fetch_array($departamentos_))
                                                    {
                                                        echo '
                                                        <ul class="single-mega-item">
                                                        <li>
                                                        <a href="departamento.php/?id='.$row[0].'"><h2 class="mega-menu-title mb-15">'.$row[1].'</h2></a>
                                                        </li>
                                                        '.returnproducts($row[0]).'
                                                        </ul>';
                                                    }
                                                ?>
                                                    
                                                </div>
                                            </li>
                                            <li><a href="index.php">Productos</a></li>
                                            <li><a href="#" title="Agregar cliente" data-toggle="modal" data-target="#addsoportetecnico">
                                            Soporte Tecnico</a></li>
                                            <li><a href="#" data-toggle="modal" data-target="#como">Como comprar? </a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile Menu Start -->
            <div class="mobile-menu-area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="mobile-menu">
                                <nav id="dropdown">
                                    <ul>
                                        <li><a href="/">Home</a></li>
                                        <li><a href="/login.php">Login</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Mobile Menu End -->
        </header>
        <!-- End of header area -->
        <!-- Start page content -->
        <section id="page-content" class="page-wrapper">
            <br><br>
            <!-- Start Product List -->
            <div class="product-list-tab">
                <div class="container">
                    <div class="row">
                        <div class="product-list tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                            <div id="message"></div>
                            <?php 
                                // Codigo 
                                echo table_service_support($_GET["pagina"], $_GET["folio"], $_SERVER['REQUEST_URI']);
                                echo table_sale_products_finaly_cotizacion($_GET["folio"]); 
                            ?>
                            <br>    
                            <div class="col-lg-12 col-md-6 text-center">
                                <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#delete"><span>Cancelar</span> </a>
                                <a class="button small button-black mb-20" href="/sale_finaly_report_cotizacion.php?folio_sale=<?php echo $_GET["folio"] ?>"><span>Imprimir formato de pago oxxo</span> </a>
                                <a class="button small button-black mb-20" href="#" data-toggle="modal" data-target="#mail<?php echo $_GET["folio"] ?>"><span>Enviar por email</span> </a>
                            </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Product List -->
            <br>
            <br>
            <br>
        </section>
        <!-- End page content -->
        <!-- Start footer area -->
        <footer id="footer" class="footer-area">
            <div class="footer-top-area gray-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="footer-widget">
                                <div class="footer-widget-img pb-30">
                                    <a href="#">
                                        <img src="images/logo/logo-2.png" alt="">
                                    </a>
                                </div>
                                <ul class="toggle-footer text-white">
                                    <li class="mb-30 pl-45">
                                        <i class="zmdi zmdi-pin"></i>
                                        <p><?php echo $_SESSION['empresa_direccion'];?></p>
                                    </li>
                                    <li class="mb-30 pl-45">
                                        <i class="zmdi zmdi-email"></i>
                                        <a href="mailto:<?php echo $_SESSION['empresa_correo']?>">
                                        <p><?php echo before ('@', $_SESSION['empresa_correo']); ?>@</p>
                                        <p><?php echo after ('@', $_SESSION['empresa_correo']); ?></p>
                                        </a>
                                    </li>
                                    <li class="pl-45">
                                        <i class="zmdi zmdi-phone"></i>
                                        <p><?php echo $_SESSION['empresa_telefono']; ?></p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-white footer-about-us">
                                <h4 class="pb-40 m-0 text-uppercase">Mision</h4>
                                <p><?php echo $_SESSION['empresa_mision'];?></p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-white footer-about-us">
                                <h4 class="pb-40 m-0 text-uppercase">Vision</h4>
                                <p><?php echo $_SESSION['empresa_vision'];?></p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-white footer-about-us">
                                <h4 class="pb-40 m-0 text-uppercase">Contacto</h4>
                                <p><?php echo $_SESSION['empresa_contacto'];?></p>
                                <ul class="footer-social-icon">
                                    <li><a target="_blank" href="<?php echo $_SESSION['empresa_fb'];?>"><i class="zmdi zmdi-facebook"></i></a></li>
                                    <li><a target="_blank" href="<?php echo $_SESSION['empresa_yt'];?>"><i class="zmdi zmdi-instagram"></i></a></li>
                                    <li><a target="_blank" href="<?php echo $_SESSION['empresa_tw'];?>"><i class="zmdi zmdi-twitter"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom black-bg ptb-15">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="copyright text-white">
                                <p>Desarrollado por <a target="_blank" href="https://www.cyberchoapas.com"> CLTA DESARROLLO & DISTRIBUCION DE SOFTWARE</a>.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="footer-img">
                                <img src="images/payment.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- End footer area -->
        <!--Quickview Product Start -->
        <div id="quickview-wrapper">
            <!-- Modal -->
            <div class="modal fade" id="productModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-product">
                                <div class="single-product-image">
                                    <div id="product-img-content">
                                        <div id="my-tab-content" class="tab-content mb-20">
                                            <div class="tab-pane b-img active" id="view1">
                                                <a class="venobox" href="images/product/product-details/1.jpg" data-gall="gallery" title=""><img src="images/product/product-details/1.jpg" alt=""></a>
                                            </div>
                                            <div class="tab-pane b-img" id="view2">
                                                <a class="venobox" href="images/product/product-details/2.jpg" data-gall="gallery" title=""><img src="images/product/product-details/2.jpg" alt=""></a>
                                            </div>
                                            <div class="tab-pane b-img" id="view3">
                                                <a class="venobox" href="images/product/product-details/3.jpg" data-gall="gallery" title=""><img src="images/product/product-details/3.jpg" alt=""></a>
                                            </div>
                                            <div class="tab-pane b-img" id="view4">
                                                <a class="venobox" href="images/product/product-details/4.jpg" data-gall="gallery" title=""><img src="images/product/product-details/4.jpg" alt=""></a>
                                            </div>
                                        </div>
                                        <div id="viewproduct" class="nav nav-tabs product-view bxslider" data-tabs="tabs">
                                            <div class="pro-view b-img active"><a href="#view1" data-toggle="tab"><img src="images/product/product-details/s-1.jpg" alt=""></a></div>
                                            <div class="pro-view b-img"><a href="#view2" data-toggle="tab"><img src="images/product/product-details/s-2.jpg" alt=""></a></div>
                                            <div class="pro-view b-img"><a href="#view3" data-toggle="tab"><img src="images/product/product-details/s-3.jpg" alt=""></a></div>
                                            <div class="pro-view b-img"><a href="#view4" data-toggle="tab"><img src="images/product/product-details/s-4.jpg" alt=""></a></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-details-content">
                                    <div class="product-content text-uppercase">
                                        <a href="product-details.html" title="Slim Shirt With Stretch">Slim Shirt With Stretch</a>
                                        <div class="rating-icon pb-20 mt-10">
                                            <i class="zmdi zmdi-star"></i>
                                            <i class="zmdi zmdi-star"></i>
                                            <i class="zmdi zmdi-star"></i>
                                            <i class="zmdi zmdi-star-half"></i>
                                            <i class="zmdi zmdi-star-half"></i>
                                        </div>
                                        <div class="product-price pb-20">
                                            <span class="new-price">£ 185.00</span>
                                            <span class="old-price">£ 200.00</span>
                                        </div>
                                    </div>
                                    <div class="product-view pb-20">
                                        <h4 class="product-details-tilte text-uppercase">overview</h4>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. luptate. </p>
                                    </div>
                                    <div class="product-size text-uppercase pb-30">
                                        <h4 class="product-details-tilte text-uppercase pb-10">size</h4>
                                        <ul>
                                            <li><a href="#">s</a></li>
                                            <li><a href="#">m</a></li>
                                            <li><a href="#">l</a></li>
                                            <li><a href="#">xl</a></li>
                                            <li><a href="#">xxl</a></li>
                                        </ul>
                                    </div>
                                    <div class="product-attributes clearfix">
                                        <div class="product-color text-uppercase pb-30">
                                            <h4 class="product-details-tilte text-uppercase pb-10">color</h4>
                                            <ul>
                                                <li class="color-1"><a href="#"></a></li>
                                                <li class="color-2"><a href="#"></a></li>
                                                <li class="color-3"><a href="#"></a></li>
                                                <li class="color-4"><a href="#"></a></li>
                                            </ul>
                                        </div>
                                        <div class="pull-left" id="quantity-wanted">
                                            <h4 class="product-details-tilte text-uppercase pb-10">quantity</h4>
                                            <input type="number" value="1">
                                        </div>
                                    </div>
                                    <div class="product-action-shop text-center mb-30">
                                        <a href="#" title="Quick view">
                                            <i class="zmdi zmdi-eye"></i>
                                        </a>
                                        <a href="#" title="Add to cart">
                                            <i class="zmdi zmdi-shopping-cart"></i>
                                        </a>
                                        <a href="#" title="Add to Wishlist">
                                            <i class="zmdi zmdi-favorite"></i>
                                        </a>
                                    </div>
                                    <div class="socialsharing-product">
                                        <h4 class="product-details-tilte text-uppercase pb-10">share this on</h4>
                                        <button type="button"><i class="zmdi zmdi-facebook"></i></button>
                                        <button type="button"><i class="zmdi zmdi-instagram"></i></button>
                                        <button type="button"><i class="zmdi zmdi-rss"></i></button>
                                        <button type="button"><i class="zmdi zmdi-twitter"></i></button>
                                        <button type="button"><i class="zmdi zmdi-pinterest"></i></button>
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
    </div>
    <!-- Body main wrapper end -->

    <!-- Placed js at the end of the document so the pages load faster -->

    <!-- jquery latest version -->
    <script src="js/vendor/jquery-3.1.1.min.js"></script>
    <!-- Bootstrap framework js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Particles js -->
    <script src="js/particles.js"></script>
    <!-- All js plugins included in this file. -->
    <script src="js/plugins.js"></script>
    <!-- Main js file that contents all jQuery plugins activation. -->
    <script src="js/main.js"></script>
</body>

</html>

<?php
    echo table_SalesModal($_GET["folio"]);
    echo table_cotizacion_modal();
?>


<!--Eliminar venta-->
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"></h5>
            </button>
            </div>
            <div class="modal-body">
            <div class="row">
        <div class="col-md-12">
        <div class="col-md-12">
            <div class="section-title-2 text-uppercase mb-40 text-center">
                <h4>Eliminar cotizacion</h4>
            </div>
            <form action="func/delete_f_venta.php" autocomplete="off" method="post">
                <input type="hidden" id="folio" name="folio" value="<?php echo $_GET["folio"] ?>">
                <input type="hidden" id="url" name="url" value="/">
        </div>
        </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button type="sumbit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
</div>
</div>
</div>

<!-- Inicia Generar ticket soporte tecnico -->
<div class="modal fade" id="addsoportetecnico" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><center>Ingrese folio de venta o licencia</center></center></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form id="contact-form" action="func/create_sale_soporte.php" method="post" autocomplete="off">
                              <div class="col-md-12">
                                <input type="text" name="folio" id="folio" placeholder="Folio venta, Licencia de usuario" required>
                              </div>
					</div>
				</div>
				<div class="modal-footer">
						<button type="sumbit" class="btn btn-success"  onclick="javascript:this.form.submit(); this.disabled= true;" >Solicitar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
    <!-- Finaliza Generar ticket soporte tecnico -->

<!-- Como -->
<div id="como" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Instrucciones para realizar proceso de compra</h4>
      </div>
      <div class="modal-body">
      <p>
      1.- Nos proporciona nombre y correo electrónico (datos de facturación en caso que requiera factura + Iva )
      <br><br>
      2.- Se genera su cotización.  (Su cotización trae los métodos de pago por transferencias, Oxxo, PayPal, MercadoPago o depósitos en ventanilla)
      <br><br>
      3.-  Una vez generada en cuanto realice su pago, remisionamos o facturamos su compra. 
      <br><br>
      4.- En ese momento el sistema genera licencia y le proporcionamos su sistema para que ustedes lo instalen o nos dan acceso para que nosotros lo instalemos.
      </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
      </div>
    </div>

  </div>
</div>
</div>
<script>
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
    vars[key] = value.replace(/%20/g, " ");
    });
return vars;
}

if (getUrlVars()["sendmail"])
{
    var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>HECHO!</strong> Correo enviado correctamente.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
if (getUrlVars()["nosendmail"])
{
    var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
    body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
    body +="<span aria-hidden='true'>&times;</span>";
    body +="</button>";
    body +="<strong>Error!</strong> No se envio el correo.";
    body +="</div>";
    document.getElementById("message").innerHTML = body;
}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
$(window).load(function() {
	$('#preloader').fadeOut('slow');
	$('body').css({'overflow':'visible'});
})

</script>


<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v4.0"></script>