</div>
                </div>
            </div>
            <!-- End of Banner Area --> 
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
                                        <p><?php echo $_SESSION['empresa_telefono'];?></p>
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
    
    <!-- Ventanas modal-->
    <div class="modal fade" id="Empresa_datos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ACTUALIZAR DATOS DE: <?php echo $_SESSION['empresa_nombre'];?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/updateEmpresaDatos.php" autocomplete="off" method="post">
        <div class="row">

            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">

            <div class="col-md-12">
            <label>Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo $_SESSION['empresa_nombre'];?>">
            </div>

            <div class="col-md-12">
            <label>Nombre corto</label>
            <input type="text" name="nombre_corto" id="nombre_corto" value="<?php echo $_SESSION['empresa_nombre_corto'];?>">
            </div>

            <div class="col-md-12">
            <label>Direccion</label>
            <input type="text" name="direccion" id="direccion" value="<?php echo $_SESSION['empresa_direccion'];?>" >
            </div>

            <div class="col-md-12">
            <label>Correo</label>
            <input type="text" name="correo" id="correo" value="<?php echo $_SESSION['empresa_correo'];?>">
            </div>

            <div class="col-md-12">
            <label>Telefono</label>
            <input type="text" name="telefono" id="telefono" value="<?php echo $_SESSION['empresa_telefono'];?>">
            </div>

            <div class="col-md-12">
            <label>Url Facebook</label>
            <input type="text" name="facebook" id="facebook" value="<?php echo $_SESSION['empresa_fb'];?>">
            </div>

            <div class="col-md-12">
            <label>Url twitter</label>
            <input type="text" name="twitter" id="twitter" value="<?php echo $_SESSION['empresa_tw'];?>">
            </div>

            <div class="col-md-12">
            <label>Url Youtube</label>
            <input type="text" name="youtube" id="youtube" value="<?php echo $_SESSION['empresa_yt'];?>" >
            </div>

        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
<!-- Datos cfdi -->
    <div class="modal fade" id="Empresa_datos_cfdi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">DATOS CFDI 3.3</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/updateEmpresaDatosCfdi.php" autocomplete="off" method="post">
        <div class="row">

            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">

            <div class="col-md-6">
            <label>Lugar de expedicion</label>
            <input type="text" name="cfdi_lugare_expedicion" id="cfdi_lugare_expedicion" value="<?php echo $_SESSION['cfdi_lugare_expedicion'];?>">
            </div>

            <div class="col-md-6">
            <label>Rfc emisor</label>
            <input type="text" name="cfdi_rfc" id="cfdi_rfc" value="<?php echo $_SESSION['cfdi_rfc'];?>" >
            </div>

            <div class="col-md-6">
            <label>Clave Regimen fiscal</label>
            <input type="text" name="cfdi_regimen" id="cfdi_regimen" value="<?php echo $_SESSION['cfdi_regimen'];?>">
            </div>
            
            <div class="col-md-6">
            <label>Contrase&ntilde;a Sello</label>
            <input type="password" name="cfdi_pass" id="cfdi_pass" value="<?php echo $_SESSION['cfdi_pass'];?>">
            </div>
            
            <div class="country-select shop-select col-md-6">
                <label>Ruta .Cer <span class="required">*</span></label>
                <input type="text" name="cfdi_cer" id="cfdi_cer" value="<?php echo $_SESSION['cfdi_cer'];?>">
            </div>
            
            <div class="country-select shop-select col-md-6">
                <label>Ruta .Key<span class="required">*</span></label>
                <input type="text" name="cfdi_key" id="cfdi_key" value="<?php echo $_SESSION['cfdi_key'];?>">
            </div>

        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Mision-->
    <div class="modal fade" id="Empresa_Mision" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ACTUALIZAR MISION DE: <?php echo $_SESSION['empresa_nombre'];?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/updateEmpresaDatos_mision.php" autocomplete="off" method="post">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">
            <textarea name="mision_new" id="mision_new" cols="30" rows="8"><?php echo $_SESSION['empresa_mision'];?></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Vision-->
    <div class="modal fade" id="Empresa_Vision" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ACTUALIZAR VISION DE: <?php echo $_SESSION['empresa_nombre'];?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/updateEmpresaDatos_vision.php" autocomplete="off" method="post">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">
            <textarea name="vision_new" id="vision_new" cols="30" rows="8"><?php echo $_SESSION['empresa_vision'];?></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="sumbit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Contacto-->
    <div class="modal fade" id="Empresa_Contacto" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ACTUALIZAR DATOS DE CONTACTO: <?php echo $_SESSION['empresa_nombre'];?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/updateEmpresaDatos_contacto.php" autocomplete="off" method="post">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">
            <textarea name="contacto_new" id="contacto_new" cols="30" rows="8"><?php echo $_SESSION['empresa_contacto'];?></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Pie de pagina-->
    <div class="modal fade" id="Empresa_footer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">ACTUALIZAR PIE DE PAGINA COTIZACIONES Y PEDIDOS</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/updateEmpresaDatos_footer.php" autocomplete="off" method="post">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">
            <textarea name="footer" id="footer" cols="30" rows="8"><?php echo $_SESSION['empresa_footer'];?></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--add departamento-->
    <div class="modal fade" id="departament_add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Nuevo departamento</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/departamento_add.php" autocomplete="off" method="post">
        
        <div class="row">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">

            <div class="col-md-12">
            <label>Nombre departamento</label>
            <input type="text" name="departamento_add_nombre" id="departamento_add_nombre" placeholder="Ingrese nombre">
            </div>
            
            <div class="col-md-12">
            <br>
            <label>Descripcion departamento</label>
            <textarea name="departamento_add_descripcion" id="departamento_add_descripcion" cols="30" rows="4"></textarea>
            </div>

        </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="sumbit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div> 
    <!--add almacen-->
    <div class="modal fade" id="almacen_add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Nuevo almacen</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        <form action="../func/almacen_add.php" autocomplete="off" method="post">
        
        <div class="row">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']
            ;?>">

            <div class="col-md-12">
            <label>Nombre</label>
            <input type="text" name="almacen_nombre" id="almacen_nombre" placeholder="Ingrese nombre">
            </div>
            
            <div class="col-md-12">
            <br><label>Ubicacion</label>
            <input type="text" name="almacen_ubicacion" id="almacen_ubicacion" placeholder="Ingrese ubicacion">
            </div>

            <div class="col-md-12">
            <br><label>Telefono</label>
            <input type="text" name="almacen_telefono" id="almacen_telefono" placeholder="Ingrese telefono">
            </div>

        </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="sumbit" class="btn btn-primary">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Lista de ventas abiertas-->
    <?php echo $modal_ventas; ?>
    <!--Corte z global-->
    <div class="modal fade" id="cut_z_yes_global" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">REALIZAR CORTE Z GLOBAL?</h5>
            <button type="button" id="cut_z_yes_global_close" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Este procedimiento vaciara todas las ventas filtradas previemante, Desea continuar ?</p>
        </div>
        <div class="modal-footer">
            <form action="/func/cut_z_global.php" method="post">
                
                <input type="hidden" id="usuario" name="usuario" value="<?php echo $_GET["usuario"]; ?>">
                <input type="hidden" id="sucursal" name="sucursal" value="<?php echo $_GET["sucursal"]; ?>">
                
                <button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">NO</button>
                <button type="submit" class="btn btn-danger" onclick='document.getElementById("cut_z_yes_global_close").click();'>SI</button>
            </form>
        </div>
        </div>
    </div>
    </div>

    <!--Corte z usuario-->
    <div class="modal fade" id="cut_z_yes_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">REALIZAR CORTE Z?</h5>
            <button type="button" id="cut_z_yes_global_close" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Este procedimiento vaciara todas las ventas afiliadas a el usuario actual ?</p>
        </div>
        <div class="modal-footer">
            <form action="/func/cut_x_view.php" method="post">
                
                <input type="hidden" id="cut" name="cut" value="1">
                <button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">NO</button>
                
                <button type="submit" class="btn btn-danger" onclick='document.getElementById("cut_z_yes_user").click();'>SI</button>
            </form>
        </div>
        </div>
    </div>
    </div>

    <!--Perfil-->
    <div class="modal fade" id="profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">
            <img src = "images/<?php echo $_SESSION['users_foto']; ?>" style="
            height: 50px;
            width: 50px;
            background-repeat: no-repeat;
            background-position: 50%;
            border-radius: 50%;
            background-size: 100% auto;
            "> <?php echo $_SESSION['users_nombre']; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="func/update_profile.php" method="post" enctype="multipart/form-data">
            
            <div class="row">
                <input type="hidden" id="id" name="id" value="<?php echo $_SESSION['users_id'];?>">
                <div class="col-md-12">
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="nombre" value ="<?php echo $_SESSION['users_nombre'];?>">
                </div>
                <div class="country-select shop-select col-md-12">
                    <label>Seleccione imagen si desea cambiarla<span class="required">*</span></label>
                    <input type="file" name="imagen" id="imagen" accept="image/jpeg,image/jpg" >
                </div>
                <div class="col-md-12">
                    <label>Ingrese contrase&ntilde;a si desea cambiarla</label>
                    <input type="password" name="pass1" id="pass1">
                </div>
                <div class="col-md-12">
                    <label>Confirme contrase&ntilde;a</label>
                    <input type="password" name="pass2" id="pass2">
                </div>
            </div>

        </div>
        <div class="modal-footer">
            
                <button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Actualizar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Repoortes pdf-->
    <div class="modal fade" id="inv_pdf" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Generar reporte pdf</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="products_pdf.php" target="_blank">
            
            <label>Seleccione almacen</label><br>
            <select id="almacen" name="almacen" required>
                    <?php echo Select_Almacen_cero() ?>
                    <option value='full'>TODOS LOS ALMACENES</option>
            </select>                                       

        </div>
        <div class="modal-footer">
            
                <button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Generar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!--Repoortes xls-->
    <div class="modal fade" id="inv_xls" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Generar reporte EXCEL</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="products_xls.php">
            
            <label>Seleccione almacen</label><br>
            <select id="almacen" name="almacen" required>
                    <?php echo Select_Almacen_cero() ?>
                    <option value='full'>TODOS LOS ALMACENES</option>
            </select>                                       

        </div>
        <div class="modal-footer">
            
                <button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Generar</button>
            </form>
        </div>
        </div>
    </div>
    </div>
    <!-- Agregar usuario -->
    <?php
    if ($_SESSION['usuarios'] == 1)
    {
        echo '
        <div class="modal fade" id="user_add" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Agregar nuevo usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            
            <form id="contact-form" action="func/add_user.php" method="post" autocomplete="off" enctype="multipart/form-data">
                <div class="row">
                <input type="hidden" id="url" name="url" value="'.$_SERVER['REQUEST_URI'].'">
                <div class="col-md-12">
                    <label>Nombre de usuario<span class="required">*</span></label>
                    <input type="text" name="username" id="username" placeholder="Nombre o razon social" required>
                </div>
                <div class="col-md-12">
                <br><label>Escriba una contrase&ntilde;a<span class="required">*</span></label>
                    <input type="password" name="pass" id="pass" required>
                </div>
                <div class="col-md-12">
                    <br><label>Nombre<span class="required">*</span></label>
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre o razon social" required>
                </div>
                <div class="country-select shop-select col-md-12">
                    <br><label>Seleccione imagen si desea cambiarla</label>
                    <input type="file" name="imagen" id="imagen" accept="image/jpeg,image/jpg" >
                </div>
                <div class="col-md-12">
                    <br><label>Descripcion usuario</label>
                    <input type="text" name="descripcion" id="descripcion"">
                </div>
                <div class="col-md-12">
                    <br>
                    <label>Seleccione sucursal de venta predeterminada</label>
                    <select id="sucursal" name="sucursal" required >
                        '. Select_sucursales_Add_user() .'
                    </select>
                </div>
                
                <div class="col-md-12">
                    <div class="section-title-2 text-uppercase mb-40 text-center">
                        <br><h5>PERMISOS DE USUARIO</h5>
                    </div>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Agregar producto
						<input type="checkbox" id="product_add" name="product_add">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Gestionar producto
						<input type="checkbox" id="product_gest" name="product_gest">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Generar orden de compra
						<input type="checkbox" id="gen_orden_compra"  name="gen_orden_compra">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Agregar cliente
						<input type="checkbox" id="client_add" name="client_add">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Gestionar clientes
						<input type="checkbox" id="client_guest" name="client_guest">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Agregar almacen
						<input type="checkbox" name="almacen_add" id="almacen_add">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Gestionar almacen
						<input type="checkbox" name="almacen_guest" id="almacen_guest">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Agregar departamento
						<input type="checkbox" id="depa_add" name="depa_add">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Gestionar departamento
						<input type="checkbox" id="depa_guest" name="depa_guest">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Acceso a propiedades
						<input type="checkbox" id="propiedades" name="propiedades">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Acceso a usuario
						<input type="checkbox" id="usuarios" name="usuarios">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Acceso a finanzas
						<input type="checkbox" id="finanzas" name="finanzas">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Cambiar sucursal
						<input type="checkbox" id="change_suc" name="change_suc">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Gestionar sucursal
						<input type="checkbox" id="sucursal_gest" name="sucursal_gest">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Usar caja
						<input type="checkbox" id="caja" name="caja">
						<span class="checkmark"></span>
					</label>
                </div>
                <div class="col-md-4">
					<label class="containeruser">Permitir ventas
						<input type="checkbox" id="super_pedidos" name="super_pedidos">
						<span class="checkmark"></span>
					</label>
				</div>
        </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
            </div>
        </div>
        </div>      
            ';
        }
    ?>
    <!--Ingreso-->
    <div class="modal fade" id="ingreso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Registrar ingreso</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        
        <form class="header-search-box" action="/func/add_ingreso.php" autocomplete="off" method="POST">
            <div class="row">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']?>">
            <div class="col-md-12">
                <label>Monto<span class="required">*</span> </label>
                <input type="number" step="0.0001" placeholder="$ 0.0" id="monto" name="monto" autocomplete="off" required>
            </div>
            <div class="col-md-12">
                <br>
                <label>Ingrese un concepto<span class="required">*</span> </label>
                <input type="text" placeholder="Indique concepto" id="concepto" name="concepto" autocomplete="off" required>
            </div>

            <?php 
                if ($_SESSION['change_suc'] == 1)
                {
                    echo '
                    <div class="col-md-12">
                        <br><label>Selecione sucursal<span class="required">*</span></label><br>
                        <select id="sucursal" name="sucursal" required>
                                '.Select_sucursales_selected($_SESSION['sucursal']).'
                        </select>                                       
                    </div>
                    ';
                }else
                {
                    echo '<input type="hidden" id="sucursal" name="sucursal" value="'.$_SESSION['sucursal'].'">';
                }
            ?>
            </div>
    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="sumbit" class="btn btn-success" onclick="javascript:this.form.submit(); this.disabled= true;">Guardar</button>
            </form>
        </div>
        </div>
    </div>
    </div> 
    <!--Egreso-->
    <div class="modal fade" id="egreso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Registrar egreso</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
        
        <form class="header-search-box" action="/func/add_egreso.php" autocomplete="off" method="POST">
            <div class="row">
            <input type="hidden" name="url" id="url" value="<?php echo $_SERVER['REQUEST_URI']?>">
            <div class="col-md-12">
                <label>Monto<span class="required">*</span> </label>
                <input type="number" step="0.0001" placeholder="$ 0.0" id="monto" name="monto" autocomplete="off" required>
            </div>
            <div class="col-md-12">
                <br>
                <label>Ingrese un concepto<span class="required">*</span> </label>
                <input type="text" placeholder="Indique concepto" id="concepto" name="concepto" autocomplete="off" required>
            </div>
            <?php 
                if ($_SESSION['change_suc'] == 1)
                {
                    echo '
                    <div class="col-md-12">
                        <br><label>Selecione sucursal<span class="required">*</span></label><br>
                        <select id="sucursal" name="sucursal" required>
                                '.Select_sucursales_selected($_SESSION['sucursal']).'
                        </select>                                       
                    </div>
                    ';
                }else
                {
                    echo '<input type="hidden" id="sucursal" name="sucursal" value="'.$_SESSION['sucursal'].'">';
                }
            ?>
            </div>
    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="sumbit" class="btn btn-danger" onclick="javascript:this.form.submit(); this.disabled= true;">Guardar</button>
            </form>
        </div>
        </div>
    </div>
    </div> 
    <!-- Finaliza Ventanas modal-->
    
    <!-- Inicia SendMail Para todas las cotizaciones-->
    <div class="modal fade" id="SendCotAll" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><center>** ENVIAR TODAS LAS COTIZACION POR CORREO !</center></center></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form action="func/cotizacion_sendmailAll.php" autocomplete="on" method="post">
                        <div class="col-md-12">
                                <label>ASUNTO</label>
                                <input type="text" name="header" id="header" placeholder="Cotizacion: #######"  value="">
                            </div>
                            <div class="col-md-12">
                            <br>
								<label>Mensaje</label>
								<textarea name="txtxtra" id="txtxtra"></textarea>
								<script>CKEDITOR.replace('txtxtra');</script>
							</div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
						<input type="hidden" id="url_web" name="url_web" value="<?php echo $_SERVER['HTTP_HOST'] ?>">
						<button type="sumbit" class="btn btn-success"  onclick="javascript:this.form.submit(); this.disabled= true;" >Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
    <!-- Finaliza modulo sendmail-->
    
    <!-- Inicia Agregar cliente -->
    <div class="modal fade" id="addclient" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><center>AGREGAR CLIENTE</center></center></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form id="contact-form" action="func/client_add.php" method="post" autocomplete="off">
                              <div class="col-md-12">
                                <label>Ingrese nombre de cliente<span class="required">*</span></label>
                                <input type="text" name="nombre" id="nombre" placeholder="Nombre o razon social" required>
                              </div>
                              
                              <div class="col-md-12">
                                <br>
                                <label>Ingrese direccion de cliente</label>
                                <input type="text" name="direccion" id="direccion" placeholder="Direccion fisica de cliente">
                              </div>
                              
                              <div class="col-md-12">
                                  <br>
                                <label>Ingrese telefono. (Puede ser mas de uno)</label>
                                <input type="number" name="telefono" id="telefono" placeholder="Telefono de contacto">
                            </div>
                
                            <div class="col-md-12">
                                <br>
                                <label>Ingrese porcentaje de descuento<span class="required">*</span></label>
                                <input type="number" name="p_descuento" id="p_descuento" placeholder="Ingrese el porcentaje para descuento en compras" min="0" max="100" value="0" required>
                            </div>
                
                            <div class="col-md-12">
                                <br>
                                <label>Ingrese rfc para emitir factura</label>
                                <input type="text" name="rfc" id="rfc" placeholder="Rfc de cliente o empresa">
                            </div>
                
                            <div class="col-md-12">
                                <br>
                                <label>Ingrese razon social</label>
                                <input type="text" name="r_social" id="r_social" placeholder="Razon social de cliente o empresa">
                            </div>
                
                            <div class="col-md-12">
                                <br>
                                <label>Ingrese correo electronico</label>
                                <input type="text" name="correo" id="correo" placeholder="Email de cliente o empresa">
                            </div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
						<input type="hidden" id="url_web" name="url_web" value="<?php echo $_SERVER['HTTP_HOST'] ?>">
						<button type="sumbit" class="btn btn-success"  onclick="javascript:this.form.submit(); this.disabled= true;" >Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
    <!-- Finaliza agregar cliente-->

    <!-- Inicia Agregar credito -->
    <div class="modal fade" id="addcredit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel"><center>AGREGAR NUEVO CREDITO</center></center></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<form id="contact-form" action="func/credit_add.php" method="post" autocomplete="off">
                              <div class="col-md-12">
                                <label>Seleccione cliente<span class="required">*</span></label>
                                <select id="select_client" name="select_client">
                                        <?php echo Select_clients(0) ?>
                                </select>
                              </div>

                              <div class="col-md-12">
                                <br><label>Seleccione sucursal<span class="required">*</span></label>
                                <select id="select_sucursal" name="select_sucursal">
                                        <?php echo Select_sucursales(0) ?>
                                </select>
                              </div>
                              
                              <div class="col-md-12">
                                <br>
                                <label>Ingrese no. de factura</label>
                                <input type="text" name="factura" id="factura" placeholder="F: ##-##">
                              </div>
                              
                              <div class="col-md-12">
                                  <br>
                                <label>Ingrese adeudo total</label>
                                <input type="number" step="1"  name="adeudo" id="adeudo" placeholder="Ingrese adeudo" required value="1">
                            </div>
                
                            <div class="col-md-12">
                                <br>
                                <label>Ingrese abono</label>
                                <input type="number" step="1"  name="abono" id="abono" placeholder="Opcional" required value="0">
                            </div>
                
                            <div class="col-md-12">
                                <br>
                                <label>Ingrese dias estimados de credito</label>
                                <input type="number" step="1"  name="dias" id="dias" placeholder="Opcional" required value="0">
                            </div>
					</div>
				</div>
				<div class="modal-footer">
						<input type="hidden" id="url" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
						<input type="hidden" id="url_web" name="url_web" value="<?php echo $_SERVER['HTTP_HOST'] ?>">
						<button type="sumbit" class="btn btn-success"  onclick="javascript:this.form.submit(); this.disabled= true;" >Enviar</button>
					</form>
				</div>
				</div>
			</div>
			</div>
    <!-- Finaliza agregar credito-->


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
                                <input type="tel" name="folio" id="folio" placeholder="Folio venta, Licencia de usuario" required>
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

    <script>
    if (getUrlVars()["error_update_empresa"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> No se actualizaron los datos de la empresa.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["add_department"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO!</strong> Departamento agregado con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["noadd_department"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El departamento no se agrego.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["add_almacen"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO!</strong> Almacen agregado con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["noadd_almacen"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El almacen no se agrego.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["sale_delete"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO!</strong> Venta eliminada.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["sale_nodelete"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> La venta no se elimino.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["add_product_sale"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO!</strong> Producto agregado.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["noadd_product_sale"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> El producto no fue agregado, verifique stock.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
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
    if (getUrlVars()["delete"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO!</strong> Cliente eliminado correctamente.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["deleteno"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> No es posible eliminar este cliente.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["Cont_MailSend"])
    {
        var body = "<div class='alert alert-info alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="Correos <b>enviados:</b> [<?php echo $_GET["Cont_MailSend"]; ?>], correos <b>no enviados</b> [<?php echo $_GET["Cont_MailNoSend"]; ?>]";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["client_add_add"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>AGREGADO!</strong> Cliente agregado con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["client_add_noadd"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> Verifique informacion de cliente";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["credit_add_add"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>AGREGADO!</strong> credito agregado con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["credit_add_noadd"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> Verifique informacion";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["okannuity"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO! </strong> Anualidad procesada con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["noannuity"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong> La anualidad no se afecto.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["sale_liquid"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO! </strong> Venta sin adeudo.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    if (getUrlVars()["process_yes"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>HECHO! </strong> Proceso exitoso.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    
    if (getUrlVars()["sale_noliquid"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>Error!</strong>El proceso no se realizo con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }

    function hideMenuVarMobile() 
    {
        jQuery('.mean-nav ul:first').slideUp();
        jQuery(".meanmenu-reveal.meanclose").toggleClass("meanclose").html("<span /><span /><span />");
        this.menuOn = false;
    }

    </script>
</body>

</html>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
$(window).load(function() {
	$('#preloader').fadeOut('slow');
	$('body').css({'overflow':'visible'});
})
</script>
<div id="fb-root"></div>
