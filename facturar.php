<?php
    include 'func/header.php';
?>
<!-- Start Accordion Area -->
<div class="">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    PRODUCTOS AGREGADOS A SU VENTA
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                <?php
                                    if ($_GET["folio"] && $_GET["stocck"] == 1)
                                    {
                                        echo table_sale_products_finaly_cfdi($_GET["folio"]); 
                                    }else
                                    {
                                        echo table_sale_products_finaly_order_cfdi($_GET["folio"]); 
                                    }
                                ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <form id="contact-form" action="func/timbrar.php" method="post" autocomplete="off" target="_blank">
            
            <input type="hidden" id="folio" name="folio" value="<?php echo $_GET["folio"] ?>">
            <input type="hidden" id="stock" name="stock" value="<?php echo $_GET["stocck"] ?>">
                
            
                
                <div class="country-select shop-select col-md-3">
                    <label> Tipo de comprobante <span class="required">*</span></label>
                    <select id="cfdi_tipo" name = "cfdi_tipo">
                        <option value='I'>Ingreso</option>
                        <option value='E'>Egreso</option>
                        <option value='T'>Traslado</option>
                    </select>                                       
                </div>
                
                <div class="country-select shop-select col-md-3">
                    <label> Metodo de pago <span class="required">*</span></label>
                    <select id="cfdi_m_pago" name = "cfdi_m_pago">
                        <option value='PUE'>Pago en una sola exhibición</option>
                        <option value='PPD'>Pago en parcialidades o diferido</option>
                    </select>                                       
                </div>
                
                <div class="country-select shop-select col-md-3">
                    <label> Moneda <span class="required">*</span></label>
                    <select id="cfdi_moneda" name = "cfdi_moneda">
                        <option value='MXN'>Pesos mexicano</option>
                        <option value='USD'>Dolar americano</option>
                        <option value='EUR'>Euro</option>
                        <option value='CAD'>Dolas canadiense</option>
                    </select>                                       
                </div>
                
                <?php 
                    echo ReturnSerieSelect($_GET["folio"]);
                ?>
                
                <?php 
                    echo ReturnSerieT_pago($_GET["folio"]);
                ?>
                
                <div class="country-select shop-select col-md-6">
                    <label> Uso cfdi 3.3 <span class="required">*</span></label>
                    <select id="cfdi_uso" name = "cfdi_uso">
                        <option value='G03'>Gastos en general</option>
                        <option value='G01'>Adquisición de mercancias</option>
                        <option value='G02'>Devoluciones, descuentos o bonificaciones</option>
                        <option value='I01'>Construcciones</option>
                        <option value='I02'>Mobilario y equipo de oficina por inversiones</option>
                        <option value='I03'>Equipo de transporte</option>
                        <option value='I04'>Equipo de computo y accesorios</option>
                        <option value='I05'>Dados, troqueles, moldes, matrices y herramental</option>
                        <option value='I06'>Comunicaciones telefónicas</option>
                        <option value='I07'>Comunicaciones satelitales</option>
                        <option value='I08'>Otra maquinaria y equipo</option>
                        <option value='D01'>Honorarios médicos, dentales y gastos hospitalarios.</option>
                        <option value='D02'>Gastos médicos por incapacidad o discapacidad</option>
                        <option value='D03'>Gastos funerales.</option>
                        <option value='D04'>Donativos.</option>
                        <option value='D05'>Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).</option>
                        <option value='D06'>Aportaciones voluntarias al SAR.</option>
                        <option value='D07'>Primas por seguros de gastos médicos.</option>
                        <option value='D08'>Gastos de transportación escolar obligatoria.</option>
                        <option value='D09'>Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.</option>
                        <option value='D10'>Pagos por servicios educativos (colegiaturas)</option>
                        <option value='P01'>Por definir</option>
                    </select>                                       
                </div>
                
                <div class="col-md-2" align="left">
                    <label class="containeruser">Afectar inventario (Remisionar)
                        <input type="checkbox" id="remisionar" name="remisionar" checked>
                        <span class="checkmark"></span>
                    </label>
                </div>    
                
                <div class="col-md-10" align="left">
                <button type="submit" style="
                    background-color: #99e6ff;
                    border: none;
                    color: white;
                    padding: 18px 10px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 20px;
                    margin: 4px 2px;
                    cursor: pointer;
                    ">Emitir factura</button>
                </div>
                
                </form>
            
            </div>
        </div>
    </div>            
    <!-- End Of Accordion Area -->

<?php
    include 'func/footer.php';
?>
