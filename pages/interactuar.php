<?php 
	session_start();
	//Este es mi archivo para mostrar/participar/puntuar/borrarse de los concursos - siempre mostrando el concurso expecificado
	require_once('./clases/conexion.php');


	if(isset($_GET['exp'])){
		$inscrito = false;	//variable para comprobar si el usuario que esta viendo el concurso esta inscrito o no
		//realizo una petición para mostrar el contenido del concurso
		$sql = "Select * from concursos where id=".$_GET['exp']."";
		$con = Conexion::conectar();
		$res = $con -> query($sql);
		if($res -> num_rows > 0){	//es porque ha encontrado el concurso sino mostraré la página con el mensaje de alerta
			//guardo el contenido que he recogido de la BD en una variable
			$datos = $res -> fetch_assoc();
			//realizaré una petición para comproar si es usuario que esta viendo el concurso esta inscrito ya o no
			//por lo tanto buscaré id_usuario y el id_concurso si estan los dos es porque esta inscrito
			//la variable ultPuntuacion solo servirá para los usuarios que sean jurados
			$sql = "Select id_usuario,user_jurado,peticionJurado from users_inscritos where id_concurso=".$_GET['exp']." and id_usuario=".$_SESSION['id'];
			$res = $con->query($sql);
			if($res -> num_rows > 0){		//si ha encontrao alguna fila es porque si que esta inscrito el usuario actual
				//debo tener en cuenta que los usuarios con el rango jurado que quieran ser jurados de un concurso lo serán siempre que el admin les de ese privilegio
				$datosInteraccion = $res -> fetch_assoc();	//para saber si el usuario que he encontrado es jurado del concurso
				$inscrito = true;
			}

			//Por último realizaré una petición para recoger toda la información de las imagenes del concurso

			//entonces como voy hacer una paginación para las imagenes tengo que recoger el valor de la página que nos encontremos
			if(isset($_GET['pagina'])){		//si existe esta variable en la url entra
				if($_GET['pagina'] == 1){
					//no quiero se que muestre en la url la primera pagina por lo tanto cargo de nuevo la página como si fuese la primera vez que entra en ella
					header("Location:index.php?p=verConcurso&exp=".$_GET['exp']);
				}else{
					$pagina = $_GET['pagina'];
				}
			}else{
				$pagina = 1;	//variable para decirle en que página nos encontramos - será la primera vez que el usuario entra a esta página
			}

			$numFotosPagina = 8;		//mostraré 8 imagenes en cada página
			$mostrarApartirDe = ($pagina-1)*$numFotosPagina;

			//la variable id solo la utilizaré para los usuarios jurados
			$sqlImagenes = "Select id,ruta,puntuacion,nombreUser from fotos_concurso where id_concurso =".$_GET['exp'];
			$res = $con ->query($sqlImagenes);
			$totalImagenes = $res -> num_rows;	//guaro el número total de imagenes para este concurso
			$numPaginas =  ceil($totalImagenes/$numFotosPagina);	//ceil redondea el resultado
			//realizo una condición para que me muestre el número correcto de imagenes por página
			if($pagina != 1){
				if(($numFotosPagina*$pagina) > $totalImagenes){
					$mostrandoImgPorPagina = $totalImagenes;
				}else{
					$mostrandoImgPorPagina = ($numFotosPagina*$pagina);
				}
			}else{
				$mostrandoImgPorPagina = ($numFotosPagina*$pagina);
				if($mostrandoImgPorPagina > $totalImagenes){
					$mostrandoImgPorPagina = $totalImagenes;
				}
			}
			if($totalImagenes > 0){
				//una vez dentro realizo otra condición para sql con limite para el paginado
				$sqlImagenes_limite = "Select id,ruta,puntuacion,nombreUser from fotos_concurso where id_concurso =".$_GET['exp']." limit $mostrarApartirDe,$numFotosPagina";
				$res_limite = $con ->query($sqlImagenes_limite);
				while($linea = $res_limite -> fetch_assoc()){
					//tengo que poner un input para puntuar mostrando la última puntuación que ha dejado el jurado (cada jurado mostraré la suya) y en puntuación todo la suma
					if(($_SESSION['rango'] == 'jurado' && $datosInteraccion['user_jurado'] == 1) && $datos['activo'] == 1){
						//para cada vuelta tengo que realizar una petición para saber la puntuacion de cada jurado
						$sqlPuntucionJurado = "Select ultPuntuacion from img_puntuadas where id_foto=".$linea['id']." and id_user=".$_SESSION['id']." and id_concurso=".$_GET['exp'];
						$resSobrePuntuacion = $con ->query($sqlPuntucionJurado);
						if($resSobrePuntuacion -> num_rows > 0){
							$inforPuntuacion = $resSobrePuntuacion ->fetch_assoc();
							//los atributos 'data' del input me servirán para guardar id de foto y la ult putuacion de cada usuario jurado 
							$allFotosConcurso .= "<div class='col-xs-6 col-md-3'><img src='../img/imgConcursos/".$datos['nombre']."/".$linea['ruta']."' /><br><p>Subida por: ".$linea['nombreUser']."</p><p>Puntuación: ".$linea['puntuacion']." </p><p class='text-center'>Tu última puntuación</p><input type='number' min='1' max='100' value=".$inforPuntuacion['ultPuntuacion']." class='form-control' data-ultpun=".$inforPuntuacion['ultPuntuacion']." data-id=".$linea['id']."><button class='btnParaPuntuar btn btn-default btn-block' value=".$linea['id'].">Puntuar</button></div>";
						}else{
							$allFotosConcurso .= "<div class='col-xs-6 col-md-3'><img src='../img/imgConcursos/".$datos['nombre']."/".$linea['ruta']."' /><br><p>Subida por: ".$linea['nombreUser']."</p><p>Puntuación: ".$linea['puntuacion']." </p><p class='text-center'>Tu última puntuación</p><input type='number' min='1' max='100' value='1' class='form-control' data-id=".$linea['id']." /><button class='btnParaPuntuar btn btn-default btn-block' value=".$linea['id'].">Puntuar</button></div>";
						}
						//en el botón pongo class y no id porque habrán más de un botón
					}else{
						$allFotosConcurso .= "<div class='col-xs-6 col-md-3'><img src='../img/imgConcursos/".$datos['nombre']."/".$linea['ruta']."' /><br><p>Subida por: ".$linea['nombreUser']."</p><p>Puntuación: ".$linea['puntuacion']."</p></div>";
					}
				}
				//una vez que he recogido todos los datos de las imagenes para este concursos compruebo hago una segunda petición siempre y cuando el concurso haya terminado
				if($datos['activo'] == 0){
					$sqlImagenesPremio = "Select ruta,puntuacion,nombreUser from fotos_concurso where id_concurso = ".$_GET['exp']." order by puntuacion desc limit 3";
					$resPremio = $con ->query($sqlImagenesPremio);
					if($resPremio -> num_rows > 0){
						//como yo limito el número de filas que nos devuelve puedo 'jugar' con ese valor
						$contadorPremiados = 1;		//creo mi variable contador para contar las vueltas
						while($lineaDos = $resPremio -> fetch_assoc()){
							switch($contadorPremiados){
								case 1:
									$allFotosPremiadas .= "<div class='col-xs-12 col-md-4 text-center'><img id='primerPremio' src='../img/imgConcursos/".$datos['nombre']."/".$lineaDos['ruta']."' /><br><p>Subida por: ".$lineaDos['nombreUser']."</p><p>Puntuación: ".$lineaDos['puntuacion']."</p></div>";
									break;
								case 2:
									$allFotosPremiadas .= "<div class='col-xs-12 col-md-4 text-center'><img id='segundoPremio' src='../img/imgConcursos/".$datos['nombre']."/".$lineaDos['ruta']."' /><br><p>Subida por: ".$lineaDos['nombreUser']."</p><p>Puntuación: ".$lineaDos['puntuacion']."</p></div>";
									break;
								case 3:
									$allFotosPremiadas .= "<div class='col-xs-12 col-md-4 text-center'><img id='tercerPremio' src='../img/imgConcursos/".$datos['nombre']."/".$lineaDos['ruta']."' /><br><p>Subida por: ".$lineaDos['nombreUser']."</p><p>Puntuación: ".$lineaDos['puntuacion']."</p></div>";
									break;
							}
							$contadorPremiados++;
							
						}
					}
				}
			}else{
				$allFotosConcurso =  "<h3 class='alert alert-warning text-center'>Este concurso aún no tiene imagenes</h3>";
			}

			 

			Conexion::desconectar($con);		//cierro la conexion
	?>
<main class="contenidoCentralPagina">
	<div>
		<h1>Nombre del concurso: <i><?php echo $datos['nombre']; ?></i></h1>
		<div class="container-fluid" id="headerConcurso">
			<img src="../img/frontPage/<?php echo $datos['imagen_portada']; ?>" class="img-responsive center-block" title="Imagen de la portdada del concurso <?php echo $datos['nombre'];?>" alt="Imagen portada" />
			<!-- quiero mostrar mi portda del concurso y que dentro del espacio de la imagen y se encuentre una información de mi concurso - por eso debo introducir tanto la imagen como descripcion mismo recip -->
			<div class="row text-center">
				<div class="col-xs-12 col-sm-6 col-md-8">
					<div class="row">
						<div class="col-xs-6">
							<?php 
								if($datos['activo'] == 1){
									echo "<h3 class='alert alert-success'>Este concurso se encuentra activo</h3>";
								}else{
									echo "<h3 class='alert alert-danger'>Finalizado</h3>";
								}
							?>
						</div>
						<div class="col-xs-6">
							<div class="alert alert-info">
								<p>CATEGORÍA: <b><?php echo $datos['enfocado'];?></b></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="alert alert-info">
								<p>FECHA INICIO: <b><?php echo $datos['fecha_start'];?></b></p>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="alert alert-info">
								<p>FECHA FIN: <b><?php echo $datos['fecha_end'];?></b></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="alert alert-info">
								<p>NUM. FOTOS POR PARTICIPANTE: <b><?php echo $datos['num_fotos'];?></b></p></div>
							</div>
						<div class="col-xs-6">
							<div class="alert alert-info">
								<p id="hiddenConFormato">FORMATO DE LAS IMÁGENES: <b><?php echo $datos['formato_img'];?></b></p>
							</div>
						</div>
					</div>		
				</div>
				<div class="col-xs-12 col-sm-6 col-md-4">
							<?php
							//tengo que comprobar que el usuario haya iniciado session o registrado para poder mostrar las opciones de interactuar
							if(isset($_SESSION['id']) && $_SESSION['id'] != ''){	//es porque es un usuario 'visitante'
							?>
							<!-- Si quiero añadir filas nuevas tengo que hacerlo dentro de la condición  -->
							<div class="row">
								<div class="col-xs-12">
							<?php
							//Cualquier usuario registrado podrá crear hilos del concurso
								if($datos['activo'] == 1){	//si esta activo podré interactuar con el concurso
									//cualquier usuario podrá crear un hilo - tema de debate - nuevo al concurso
									switch($_SESSION['rango']){		//para averiguar que rango es el usuario
										case 'jurado':
											//para los usuarios jurados
											if( $datosInteraccion['user_jurado'] == 1){
											//si entra es poque es jurado del concurso
											echo "<p class='btn btn-primary btn-block'>Eres Jurado</p>";
											}else{
												// Sino tendrá la opción de pedir ser jurado
												if($datosInteraccion['peticionJurado'] == 1){
													//condición parsa saber si ha pedido ser jurado
													echo "<p>Solicitud Enviada</p>";
												}else{
													if($inscrito){
														echo "<button class='btn btn-primary btn-block'>Compitiendo</button>";
														//aquí realizaré una consulta para averiguar si el usuario ya ha subido todas las imagenes permitidas
														$subirFotosPermitido = true;	//de primeras diremos que el usuario puede subir fotos
														$con = Conexion::conectar();
														$sql = "Select count(id_usuario) from fotos_concurso where id_usuario='".$_SESSION['id']."' and id_concurso='".$_GET['exp']."'";
														$res = $con -> query($sql);
														//si el numero de veces que coinciden - los ids de la tabla - es igual al número de fotos permitas es porque ese usuario no puede subir mas fotos - recojo el valor de la fila que nos devuelva
														$fotosSubidasUser = $res -> fetch_array();	//le paso el array
														$fotosSubidasUser = $fotosSubidasUser['0']; //recojo el valor de mi array y lo asigno
														echo $fotosSubidasUser;
														if($fotosSubidasUser == $datos['num_fotos']){
															$subirFotosPermitido = false;
														}
														Conexion::desconectar($con);
													}else{
														echo "<button id='btnPedirSerJurado' class='btn btn-info btn-block'>Ser jurado del concurso</button>";
														echo "<button class='btn btn-info btn-block' id='btnInscripcion'>Participar</button>";
													}
												}
											}
										break;
										case 'participante':
											//para los usuarios participantes
											if($inscrito){		//si esta inscrito mostraré que ya estoy participando
												echo "<button class='btn btn-primary btn-block'>Compitiendo</button>";
												//aquí realizaré una consulta para averiguar si el usuario ya ha subido todas las imagenes permitidas
												$subirFotosPermitido = true;	//de primeras diremos que el usuario puede subir fotos
												$con = Conexion::conectar();
												$sql = "Select count(id_usuario) from fotos_concurso where id_usuario='".$_SESSION['id']."' and id_concurso='".$_GET['exp']."'";
												$res = $con -> query($sql);
												//si el numero de veces que coinciden - los ids de la tabla - es igual al número de fotos permitas es porque ese usuario no puede subir mas fotos - recojo el valor de la fila que nos devuelva
												$fotosSubidasUser = $res -> fetch_array();	//le paso el array
												$fotosSubidasUser = $fotosSubidasUser['0']; //recojo el valor de mi array y lo asigno
												if($fotosSubidasUser == $datos['num_fotos']){
													$subirFotosPermitido = false;
												}
												Conexion::desconectar($con);
											}else{
												echo "<button class='btn btn-info btn-block' id='btnInscripcion'>Participar</button>";
											}
										break;
										default:

									}
							?>
						</div>
						<div class="col-xs-12">
							<?php
									//input hidden para poder enviar el id del concurso
									echo "<input type='hidden' name='idConcursoOculto' id='idConcursoOculto' value='".$_GET['exp']."' />";
								}
							?>
						</div>
					</div>
					
						<?php
							}else{
								if($datos['activo'] == 1){		//solo mostraré este mensaje si el concurso esta activo si no que gracia tiene
						?>
									<div class="row">
										<div class="col-xs-12">
											<div class='alert alert-info'>Para poder participar en los concursos debes registrarte en la web</div>
										</div>
									</div>
						<?php

								}
							}
						?>
					
					<div class="row text-left">
						<div id="descripConcurso" class="col-xs-12">
							<h3 class="alert alert-info">Descripción</h3>
							<p><?php echo $datos['descripcion']; ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Los span para mensajes de información  -->
		<span class="msgErrores alert alert-warning"></span>
		<span class="msgCorrect alert alert-success"></span>
	</div>
	<?php 		//este código php es para implementar otro div que solo aparecerá cuando el concurso haya terminado
		if($datos['activo'] == 0){ ?>
			<div id="contenedorDePremios" class="container">
				<div class="row">
					<div class='alert alert-info text-center'><h2><span class="glyphicon glyphicon-sunglasses"></span> IMÁGENES PREMIADAS <span class="glyphicon glyphicon-gift"></span></h2></div>
				</div>
				<div class="row">
					<?php echo $allFotosPremiadas; ?>
				</div>
			</div>
	<?php	}
	?>
	<div class="container">
		<!-- Donde irán todas las fotos del concurso -->
		<?php 
		if($inscrito){
			//solo hará la peticion de si ya ha subido fotos si ya esta participando pero debo poner de nuevo la condición ya que solo lo permitire que suban fotos si estas participando en el concurso
			if($subirFotosPermitido){	//si entra es porque aún le queda imagenes por subir
				//este 'echo' es para saber las fotos que me quedan - en el futuro lo quitaré
				if($fotosSubidasUser == 0){ // no ha subido ninguna imagen
					echo "<div class='alert alert-info text-center'>Aún no has subido ninguna imagen. ¿A qué estas esperando?</div>";	
				}else{
					switch($datos['num_fotos']-$fotosSubidasUser){
						case 1:
							echo "<div class='alert alert-info'>Has subido ".$fotosSubidasUser." imagenes. Te queda ".($datos['num_fotos']-$fotosSubidasUser)." por subir.¡No lo olvides!</div>";
							break;
						default:
							if($fotosSubidasUser == 1){
								echo "<div class='alert alert-info'>Has subido ".$fotosSubidasUser." imagen. Te quedan ".($datos['num_fotos']-$fotosSubidasUser)." por subir.¡No lo olvides!</div>";
							}else{
								echo "<div class='alert alert-info'>Has subido ".$fotosSubidasUser." imagenes. Te quedan ".($datos['num_fotos']-$fotosSubidasUser)." por subir.¡No lo olvides!</div>";
							}
							
					}
					
				}
		?>
				<div class="container">
					<!-- Debo añadir la etiqueta form porque al fin y al cabo se envia con formulario -->
					<form method="POST" id="inputFilePlugin" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
						<label>Selecciona tus imagenes</label>
						<?php 
						for($i=0;$i<($datos['num_fotos']-$fotosSubidasUser);$i++){
						?>
							<div class="imagenesDelInput">
								<input class="fotosdelConcurso filestyle" type="file" name="subirImgConcurso" data-placeholder="Esperando imagen..." data-buttonText="Elige una imagen" data-buttonName="btn-primary" >
							</div>
						<?php
						}
						?>
						<input type="hidden" id="expedienteDeLaPagina" value="<?php echo $_GET['exp']; ?>">
						<input type="hidden" id="nombreDelConcurso" value="<?php echo $datos['nombre']; ?>">
						<button id="subirImagenes" class="btn btn-success btn-block">Particpar con estas imagenes</button>
						<div class="alert alert-warning text-center" id="imagenNoAceptada">Recuerda que el formato para este concurso es '<?php echo $datos['formato_img']; ?>' y el archivo debe ser una imagen </div>
						<div class="alert alert-danger text-center" id="elegirImgConError">Una de las imagenes elegidas no es compatible con este concurso, mira las bases del concurso.</div>
						<div class="alert alert-danger text-center" id="enviarSinImg">No has seleccionado ninguna imagen.</div>
						<div class="alert alert-danger text-center" id="imgRepetidaEnCarpeta">Una de la imagenes que intentabas subir ya existe en el concurso. Cambia de imagen o de nombre.</div>
						<div class="alert alert-danger text-center" id="imgRepetidaEnPagina">Alguna de las imagenes que intentas subir tienen el mismo nombre.</div>
						<div class="alert alert-danger text-center" id="errorAlSubir">Ha ocurrido un error al intentar subir la imagenes.</div>
					</form>
				</div><!-- end .container -->
		<?php 
			}else{
				//haré una condición para que compruebe si el usuario es jurado o participante
				if($_SESSION['rango'] == 'jurado'){
					if($datos['activo'] == 1){		//si el concurso esta activo 'informaré' por si aún no han puntuado
						echo "<div class='alert alert-info text-center'><h2>¿Has puntudado las fotos ya?</h2><p style='color:black;font-size:10px;'>/*Ten en cuanta que este es un mensaje informativo, puede que ya lo hayas hecho*/</p></div>";
						//crearé un contenedor para que cuando se puntue muestre un mensaje - y otro para mostrar error
						echo "<div id='hasPuntuado' class='alert alert-success text-center'>¡Puntuación cambiada!</div>";
						echo "<div id='mismosPuntos' class='alert alert-warning text-center'>¿Me vacilas? No has cambiado la puntuación</div>";
					}else{	//agradeceré su puntuación
						echo "<h2 class='alert alert-success text-center'>Gracias por puntuar</h2>";
					}
				}else{
					// este mensaje saldrá cuando el usuario haya añadido todas las fotos que tenia permitido
					echo "<h2 class='alert alert-success text-center'>Gracias por participar</h2>";
				}
				
			}	
		}
		if($totalImagenes > 0){		//solo mostraré el paginado si hay imagenes en el concurso
		?>
			<div class="row text-center">
				<div class="col-xs-12">
					<legend>Mostrando <?php echo $mostrandoImgPorPagina ?> de <?php echo $totalImagenes ?> imagenes</legend>
				</div>
			</div>
		<?php } ?>
		<div class="allPhotosConcurso row">
			<?php echo $allFotosConcurso; ?>
		</div>
		<?php if($totalImagenes > 0){ ?>
			<div class="row text-center">
				<div class="col-xs-12">
					<nav>
						<ul class="pagination">
							<?php 
								//aquí creare mi código para crear la paginación de la página - para ello nos ayudará la variable que nosotros creamos $totalPaginas
								for($i=1;$i<=$numPaginas;$i++){
									if($i == $pagina){
										echo "<li class='page-item active'><a id='detenerEvento' class='page-link' href='' tabindex='-1'>".$i."</a></li>";
									}else{
										echo "<li><a href='?p=concursos&exp=".$_GET['exp']."&pagina=$i'>".$i."</a></li>";
									}
									
								}
							?>
						</ul>
					</nav>
				</div>
			</div>
		<?php } ?>
	</div> <!-- End. del container de todas la imagenes del concurso -->
</main>
<?php
		}else{	//este 'else' pertenece a la condición de la busqueda mediante sql con select del principio de página
			//si no encuentra el id del concurso es que el concurso no existe
			echo "<div class='alert alert-danger text-center'>Lo sentimos, el concruso que busca no existe.</div>";
		}
	}else{	//este 'else' pertenece a la condición de isset() del principio de página
		echo "Quiere acceder a una página inexistente. Por favor, le recomendamos que vuelva hacía atrás.";
	}
?>