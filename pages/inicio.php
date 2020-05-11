	<?php
		require_once('./clases/conexion.php');
		$infoConcursos='';
		/* Cuando se cargué la página realizaré una petición para recoger el nombre e imagen de portada de los concursos más actuales, y que esten activos */
		$sql = "Select nombre,imagen_portada,enfocado,descripcion,id from concursos where activo=1 order by fecha_start desc limit 5";
		$conexion = conexion::conectar();
		$res = $conexion->query($sql);
		if($res->num_rows>0){		//porque ha encontrado concursos en la BD
				while($linea = $res -> fetch_assoc()){	//en cada vuelta recojo la nformación del número de concursos que quiero que se muestren en mi carrusel
					$infoConcursos .= "<div class='slide' style='background-image: url(./img/frontPage/".$linea['imagen_portada'].")'><a href='".$_SERVER['PHP_SELF']."?p=verConcurso&exp=".$linea['id']."'><div class='slide-block'><h4>".$linea['nombre']."</h4><p>".$linea['descripcion']."</p></div></a></div>";
				}
		}else{
			$sinConcursos =  "<h1 style='color:red;'>Aún no se han registrado concursos para paraticipar. Disculpas del administrador</h1>";
		}

		Conexion::desconectar($conexion);		//cierro la conexión
	?>
	<main>
		<div id="contenedorCarousel">
			<!-- datos -->
			<?php 
				// En esta condición indico que si la variable esta vacía o no existe es porque si ha encontrado concursos. En caso contrario, la variable existe y la hemos llenado por la petición de arriba (no hay concursos)
				if(!isset($sinConcursos)){
			?>
				<!--  Aquí es donde introduzco mi variable que he creado dinamicamente, recogiendo los datos de mi BD-->
				<div id="carousel">
					<?php echo $infoConcursos ?>
				</div>
				<!-- <div id="carousel">
					<ul id="carousel" class="top_slider roundabout-holder" style="margin-top: 200px;">
						
					</ul>
				</div> -->
			
			<?php
				}else{
					echo $sinConcursos;
				}
			 ?>
		</div>
		<?php // Tanto para iniciar session y registrarse deberé administrarlos a través de un formulario ?>
		 <?php //Estos tendran un id para poder identificarlos con JS ya que los formularios serán desarollados en el lado del cliente  ?>
		 <?php //Cuando me registree o inicie sesión la variable de usuario tendrá valor por lo tanto ya no tendrán que aparecer estos dos formularios ?>

		<?php
			if(isset($_SESSION['usuario']) && $_SESSION['usuario'] == ''){
		?>
		<form class="container" id="login" method="POST" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
		<?php //Como tanto el nombre de usuario y contraseña serán campos que esten en el formulario de logueo y el de registr tengo que ponerlo con una clase para luego acceder a ellos mediante JS, pero refiriendome al id de formulario de cada uno ?>
		<!-- DEBO DE TENER CUIDADO PORQUE NO PUEDO PONER DOS ATRIBUTOS CLASES DIFERENTES EN MI ETIQUETA -->
			<h3><i class="glyphicon glyphicon-home"></i> Iniciar Sesión</h3>
			<div class="row">
				<div class="col-xs-6">
					<div class="input-group">
						<label class="input-group-addon"><i class="glyphicon glyphicon-user"></i></label>
						<input class="form-control nomUser" type="text" placeholder="Nombre de Usuario" required="required" name="username">
					</div>
				</div>
				<div class="col-xs-6">
					<div class="input-group">
						<label class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></label>
						<input class="form-control password" type="password" placeholder="Contraseña" required="required" name="password">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<button id="logear_user" class="btn btn-default btn-block">
						<i class="glyphicon glyphicon-log-in"></i>
						Iniciar Sesión
					</button>
				</div>
			</div>
			<div class="row">
				<div class="alert alert-danger col-xs-12" id="no_existe">Esta cuenta no existe, revise los datos o <a href="" id="enfocarRegis">obtenga una</a>.</div>
			</div>
			<div class="row">
				<div class="alert alert-danger col-xs-12" id="loglabel_empty">No puede dejar los campos vacíos</div>
			</div>
		</form>
		<form class="container" id="registro" method="POST" action=" <?php echo $_SERVER['PHP_SELF']; ?>" >
			<h3><i class="glyphicon glyphicon-send"></i> Registrarse</h3>
			<fieldset class="form-group">
				<div class="input-group">
					<label class="input-group-addon"><i class="glyphicon glyphicon-user"></i></label>
					<input class="form-control nomUser" type="text" placeholder="Nombre de Usuario" required="required" value="" name="username" pattern="/^[a-z\d_]{4,15}$/i">
				</div>
				<div class="alert alert-danger" id="user_repetido"><i>Este nombre ya esta registrado</i></div>
			</fieldset>
			<fieldset class="form-group">
				<div class="input-group">
					<label class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></label>
					<input class="form-control" type="email" id="email" placeholder="Email" required="required" name="email" pattern="^[^@\s]+@[^@\.\s]+(\.[^@\.\s]+)+$">
				</div>
				<div class="alert alert-danger suggestion" id="suggestion"></div>
				<div class="alert alert-danger" id="mail_repetido"><i>Este correo ya esta registrado</i></div>
			</fieldset>
			<div class="row">
				<div class="col-xs-6">
					<div class="input-group">
						<label class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></label>
						<!-- <input class="form-control password" type="password" placeholder="Password" required="required"name="password" pattern="(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d){6,20}.+$)" title="Debe tener al menos una letra 
						mayúscula, una minúscula y algún número. Una longitud entre 6 y 20 caracteres."> -->
						<input class="form-control password" type="password" placeholder="Contraseña" required="required"name="password" pattern="[A-Za-z0-9!?-]{8,12}" title="Debe tener al menos una letra 
						mayúscula, una minúscula y algún número. Una longitud entre 6 y 20 caracteres.">
						
					</div>
					<?php //La contraseña que tenga por lo menos una letra en mayúscula, una letra en minúscula y un número y que su longitud sea entre 6 y 20 caracteres. Esto es para asegurarnos de que la contraseña sea segura. ?>
				</div>
				<div class="col-xs-6">
					<div class="input-group">
						<label class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></label>
						<input class="form-control" type="password" id="passdos" placeholder="Repite Contraseña" required="required" name="confir_password" title="Las constraseñas deben coincidir">
					</div>
				</div>
				<div class="col-xs-12">
					<div class="alert alert-danger" id="passDistinto"><i>Las constraseñas deben coincidir</i></div>
				</div>
				<div class="col-xs-12">
					<div class="alert alert-danger" id="passWrong"><i>La contraseña debe tener una longitud entre 6 y 20, con Mayusculas y minusculas y algún número.</i></div>
				</div>
			</div>
			<?php //Para poder realizar el captcha tengo que utlizar php y algunas de sus funciones para recoger valores del sistema que cambian constantememte así me aseguro que los valores que aparezcan nunca serán los mismos y ponerle la propiedad 'disabled' al input y así no podrán modificarlo ?>
			<div class="row">
				<div class="col-xs-6">
					<div class="input-group">
						<label class="input-group-addon"><i class="glyphicon glyphicon-eye-close"></i></label>
						<div id="caja_captcha">
							<img src="./captcha.php" alt="Captcha image" title="Imagen Captcha" />
						</div>
					</div>
					<!-- <input type="hidden" name="captcha" id="captcha" value="<?php //echo $_SESSION['key'];?>"/> -->
				</div>
				<div class="col-xs-6">
					<div class="input-group">
						<label class="input-group-addon"><i class="glyphicon glyphicon-eye-open"></i></label>
						<input class="form-control" type="text" id="val_captcha" placeholder="Escriba lo que vea" required name="verificar_captcha" title="Introduza el código captcha">
					</div>
				</div>
				<div class="col-xs-12">
					<div class="alert alert-danger" id="error_captcha"><i>Código captcha inválido</i></div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="alert alert-danger" id="camposVacios"><i>Ha dejado campos vaciós</i></div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<button id="registrar_user" class="btn btn-default btn-block">
						<i class="glyphicon glyphicon-ok"></i>
						Registrarse
					</button>
				</div>
			</div>
		</form>
		<?php
			}
		?>
		<script>
			/* A PARTIR DE AQUÍ MI SCRIPT PARA EL CAROUSEL DEL INICIO */
			crearCarousel();
			
			function crearCarousel() {
				$('#carousel').carouFredSel({
					width: '100%',
					align: false,
					items: 3,
					items: {
						width: $('#contenedorCarousel').width() * 0.15,
						height: 500,
						visible: 1,
						minimum: 1
					},
					scroll: {
						items: 1,
						timeoutDuration : 5000,
						onBefore: function(data) {
			 
							//	find current and next slide
							var currentSlide = $('.slide.active', this),
								nextSlide = data.items.visible,
								_width = $('#contenedorCarousel').width();
			 
							//	resize currentslide to small version
							currentSlide.stop().animate({
								width: _width * 0.15
							});		
							currentSlide.removeClass( 'active' );
			 
							//	hide current block
							data.items.old.add( data.items.visible ).find( '.slide-block' ).stop().fadeOut();					
			 
							//	animate clicked slide to large size
							nextSlide.addClass( 'active' );
							nextSlide.stop().animate({
								width: _width * 0.7
							});						
						},
						onAfter: function(data) {
							//	show active slide block
							data.items.visible.last().find( '.slide-block' ).stop().fadeIn();
						}
					},
					onCreate: function(data){
			 
						//	clone images for better sliding and insert them dynamacly in slider
						var newitems = $('.slide',this).clone( true ),
							_width = $('#contenedorCarousel').width();
			 
						$(this).trigger( 'insertItem', [newitems, newitems.length, false] );
			 
						//	show images 
						$('.slide', this).fadeIn();
						$('.slide:first-child', this).addClass( 'active' );
						$('.slide', this).width( _width * 0.15 );
			 
						//	enlarge first slide
						$('.slide:first-child', this).animate({
							width: _width * 0.7
						});
			 
						//	show first title block and hide the rest
						$(this).find( '.slide-block' ).hide();
						$(this).find( '.slide.active .slide-block' ).stop().fadeIn();
					}
				});
			 
				//	Handle click events
				$('#carousel').children().click(function() {
					$('#carousel').trigger( 'slideTo', [this] );
				});
			 
				//	Enable code below if you want to support browser resizing
				$(window).resize(function(){
			 
					var slider = $('#carousel'),
						_width = $('#contenedorCarousel').width();
			 
					//	show images
					slider.find( '.slide' ).width( _width * 0.15 );
			 
					//	enlarge first slide
					slider.find( '.slide.active' ).width( _width * 0.7 );
			 
					//	update item width config
					slider.trigger( 'configuration', ['items.width', _width * 0.15] );
				});
			 
			};
		</script>
	</main>