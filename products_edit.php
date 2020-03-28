<?php
	include 'func/header.php';
?>
<!--Contenido-->
<!-- Start page content -->
<div class="col-md-12">
  <div class="message-box box-shadow white-bg">
      <?php echo _getProductsID($_GET["id"]) ?>
  </div>
</div>
<script>
	window.onload = function() {

	var img0 = document.getElementById('imagen0');
	var img0_ = document.getElementById('_img1');

	img0.addEventListener('change', function(e) {
		var file = img0.files[0];
		var imageType = /image.*/;

		if (file.type.match(imageType)) {
			var reader = new FileReader();

			reader.onload = function(e) {
				img0_.src = reader.result;
			}

			reader.readAsDataURL(file);	
		} else {
			img0_.innerHTML = "File not supported!";
		}
	});

	var img1 = document.getElementById('imagen1');
	var img1_ = document.getElementById('_img2');
	
	img1.addEventListener('change', function(e) {
		var file = img1.files[0];
		var imageType = /image.*/;

		if (file.type.match(imageType)) {
			var reader = new FileReader();

			reader.onload = function(e) {
				img1_.src = reader.result;
			}

			reader.readAsDataURL(file);	
		} else {
			img1_.innerHTML = "File not supported!";
		}
	});


	var img2 = document.getElementById('imagen2');
	var img2_ = document.getElementById('_img3');
	
	img2.addEventListener('change', function(e) {
		var file = img2.files[0];
		var imageType = /image.*/;

		if (file.type.match(imageType)) {
			var reader = new FileReader();

			reader.onload = function(e) {
				img2_.src = reader.result;
			}

			reader.readAsDataURL(file);	
		} else {
			img2_.innerHTML = "File not supported!";
		}
	});


	var img3 = document.getElementById('imagen3');
	var img3_ = document.getElementById('_img4');
	
	img3.addEventListener('change', function(e) {
		var file = img3.files[0];
		var imageType = /image.*/;

		if (file.type.match(imageType)) {
			var reader = new FileReader();

			reader.onload = function(e) {
				img3_.src = reader.result;
			}

			reader.readAsDataURL(file);	
		} else {
			img3_.innerHTML = "File not supported!";
		}
	});

	}

    </script>
<!--Finaliza contenido-->
<hr>
<center>
<a href="#" data-toggle="modal" data-target="#delete" >
<button type="submit" style="
	background-color: #800000;
	border: none;
	color: white;
	padding: 18px 10px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 20px;
	margin: 4px 2px;
	cursor: pointer;
	">ELIMINAR PRODUCTO Y TODOS SUS AFILIADOS</button>
</a>
</center>

<?php
	include 'func/footer.php';
	echo ModelProductHijosDelete($_GET["id"]);
?>
<script>
    if (getUrlVars()["nodelete"])
    {
        var body = "<div class='alert alert-danger alert-dismissible show' role='alert'>";
        body +="<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        body +="<span aria-hidden='true'>&times;</span>";
        body +="</button>";
        body +="<strong>ERROR!</strong> No fue posible eliminar el producto.";
        body +="</div>";
        document.getElementById("message").innerHTML = body;
    }
</script>
<div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLongTitle">ELIMINAR PRODUCTO ACTUAL?</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<div class="modal-body">
		<p>Esta seguro de eliminar el producto y todos sus afiliados ? despues de esta accion no abra posibilidad de recuperar el producto.</p>
	</div>
	<div class="modal-footer">
		<form action="func/product_delete.php" method="post">
			<input type="hidden" id="id" name="id" value="<?php echo $_GET["id"]; ?>">
			<button type="button" name="no" id="no" class="btn btn-secondary" data-dismiss="modal">NO</button>
			<button type="submit" class="btn btn-danger">SI</button>
		</form>
	</div>
	</div>
</div>
</div>