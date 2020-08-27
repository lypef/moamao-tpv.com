<?php
    include 'func/header.php';
    if ($_SESSION['product_add'] == 0)
    {
        echo '<script>location.href = "products.php?pagina=1"</script>';
    }
?>
<!--Contenido-->
<!-- Start page content -->
<div class="col-md-12">
  <div class="message-box box-shadow white-bg">
      <form id="contact-form" action="func/product_add.php" method="post" enctype="multipart/form-data">
          <div class="row">
              <div class="col-md-12">
                  <div class="section-title text-uppercase mb-40">
                      <h4>Agregar producto</h4>
                  </div>
              </div>
              <div class="col-md-4">
                <label>Numero de parte</label>
                <input type="text" name="parte" id="parte" placeholder="AEF594-S"   >
              </div>

              <div class="col-md-4">
                <label>Nombre del producto</label>
                <input type="text" name="name" id="name" placeholder="Nombre producto" required>
              </div>
              
              <div class="col-md-4">
                <label>Clave del producto</label>
                <input type="text" name="cv" id="cv" placeholder="Clave sat">
              </div>
              
              <div class="col-md-3">
                <label>Unidad de medida</label>
                <input type="text" name="um" id="um" placeholder="U. Medida sat">
              </div>

              <div class="col-md-3">
                <label>Unidad de medida descripcion</label>
                <input type="text" name="um_des" id="um_des" placeholder="U. Medida des sat">
              </div>
              
              <div class="col-md-3">
                <label>Stock minimo <span class="required">*</span> </label>
                <input type="number" name="stock_minimo" id="stock_minimo" value="0" required>
              </div>

              <div class="col-md-3">
                <label>Stock maximo <span class="required">*</span> </label>
                <input type="number" name="stock_maximo" id="stock_maximo" value="0" required>
              </div>

              
              <div class="col-md-6">
                <label>Precio normal<span class="required">*</span></label>
                <input type="text" name="precio" id="precio" placeholder="Precio al publico" required>
            </div>

            <div class="col-md-6">
                <label>Precio de costo</label>
                <input type="text" name="precio_costo" id="precio_costo" placeholder="Precio de costo">
            </div>

            <div class="col-md-6">
                <label>Precio oferta<span class="required">*</span></label>
                <input type="text" name="p_oferta" id="p_oferta" placeholder="Precio con oferta al publico" required>
            </div>

            <div class="col-md-6">
                <label>Unidades existentes<span class="required">*</span></label>
                <input type="text" name="stock" id="stock" placeholder="Stock" required>
            </div>

            <div class="col-md-6">
                <label>Tiempo de entrega</label>
                <input type="text" name="t_entrega" id="t_entrega" placeholder="1 Dia habil">
            </div>

            <div class="country-select shop-select col-md-6">
                <label> Usar precio de oferta ? <span class="required">*</span></label>
                <select id="use_oferta" name = "use_oferta" id="use_oferta">
                    <option value='si'>Si usar</option>
                    <option value='no' selected>No usar</option>
                </select>                                       
            </div>

              <div class="col-md-12">
              <label>Ingrese  una descripcion o caracteristicas del producto</label>
              <textarea placeholder="..." name="descripcion" id="descripcion" class="custom-textarea"></textarea>
              </div>

              <div class="country-select shop-select col-md-6">
                <label> Seleccione Almacen <span class="required">*</span></label>
                <select id="almacen" name="almacen" required>
                    <?php echo Select_Almacen() ?>
                </select>                                       
            </div>
            <div class="country-select shop-select col-md-6">
                <label> Seleccione Departamento <span class="required">*</span></label>
                <select id="departamento" name = "departamento" required>
                    <?php echo Select_Departamento() ?>
                </select>                                       
            </div>
            
            <div class="col-md-12">
                <br>
                <label>Especifique ubicacion exacta en almacen</label>
                <textarea placeholder="Anaquel b-15" name="ubicacion" id="ubicacion" class="custom-textarea"></textarea>
            </div>
            <div class="col-md-6">
                <br><label>Ingres la marca del producto</label>
                <input type="text" name="marca" id="marca" placeholder="Marca">
            </div>
            <div class="col-md-6">
                <br><label>Ingrese proveedor</label>
                <input type="text" name="proveedor" id="proveedor" placeholder="Proveedor">
            </div>


            <div class="country-select shop-select col-md-6">
                <label>Imagen 1 <span class="required">*</span></label>
                <input type="file" name="imagen0" id="imagen0" accept="image/jpeg,image/jpg" >
            </div>

            <div class="country-select shop-select col-md-6">
                <label>Imagen 2 <span class="required">*</span></label>
                <input type="file" name="imagen1" accept="image/jpeg,image/jpg" >
            </div>

            <div class="country-select shop-select col-md-6">
                <label>Imagen 3 <span class="required">*</span></label>
                <input type="file" name="imagen2" id="imagen2" accept="image/jpeg,image/jpg" >
            </div>

            <div class="country-select shop-select col-md-6">
                <label>Imagen 4 <span class="required">*</span></label>
                <input type="file" name="imagen3" id="imagen3" accept="image/jpeg,image/jpg" >
            </div>

            <div class="country-select shop-select col-md-6">
                <button class="submit-btn mt-20" type="submit">Guardar</button>
            </div>


          </div>
      </form>
  </div>
</div>
<script>
    if (getUrlVars()["add"])
    {
        var body = "<div class='alert alert-success alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>AGREGADO!</strong> Producto agregado con exito.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    if (getUrlVars()["noadd"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> Se encontraron errores en el alta del producto.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
    

    if (getUrlVars()["parte"])
    {
        document.getElementById("parte").value = getUrlVars()["parte"];    
    }
    
    if (getUrlVars()["name"])
    {
        document.getElementById("name").value = getUrlVars()["name"];    
    }
    
    if (getUrlVars()["Precio"])
    {
        document.getElementById("precio").value = getUrlVars()["Precio"];    
    }
    
    if (getUrlVars()["Precio_oferta"])
    {
        document.getElementById("p_oferta").value = getUrlVars()["Precio_oferta"];    
    }
    
    if (getUrlVars()["TiempoEntrega"])
    {

        document.getElementById("t_entrega").value = getUrlVars()["TiempoEntrega"];
    }

    if (getUrlVars()["Descripcion"])
    {
        document.getElementById("descripcion").value = getUrlVars()["Descripcion"];
    }
    
    if (getUrlVars()["Almacen"])
    {
        document.getElementById("almacen").value = getUrlVars()["Almacen"];    
    }
    
    if (getUrlVars()["Departamento"])
    {
        document.getElementById("departamento").value = getUrlVars()["Departamento"];
    }
    
    if (getUrlVars()["Ubicacion"])
    {
        document.getElementById("ubicacion").value = getUrlVars()["Ubicacion"];    
    }
    
    if (getUrlVars()["Proveedor"])
    {
        document.getElementById("proveedor").value = getUrlVars()["Proveedor"];    
    }
    
    if (getUrlVars()["Marca"])
    {
        document.getElementById("marca").value = getUrlVars()["Marca"];    
    }
    
    if (getUrlVars()["Stock"])
    {
        document.getElementById("stock").value = getUrlVars()["Stock"];
    }
    
    if (getUrlVars()["user_ofertaR"])
    {
        document.getElementById("use_oferta").value = getUrlVars()["user_ofertaR"];    
    }

    if (getUrlVars()["stock_min"])
    {
        document.getElementById("stock_minimo").value = getUrlVars()["stock_min"];    
    }

    if (getUrlVars()["stock_max"])
    {
        document.getElementById("stock_maximo").value = getUrlVars()["stock_max"];    
    }

    if (getUrlVars()["precio_costo"])
    {
        document.getElementById("precio_costo").value = getUrlVars()["precio_costo"];    
    }
    
    if (getUrlVars()["cv"])
    {
        document.getElementById("cv").value = getUrlVars()["cv"];    
    }
    
    if (getUrlVars()["um"])
    {
        document.getElementById("um").value = getUrlVars()["um"];    
    }

    if (getUrlVars()["um_des"])
    {
        document.getElementById("um_des").value = getUrlVars()["um_des"];    
    }
    

</script>
<!--Finaliza contenido-->
<?php
    include 'func/footer.php'
?>
