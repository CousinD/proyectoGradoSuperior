<?php
	session_start();
	//archivo que mostrará un formulario para pedir ser jurado del concurso
	require_once('./clases/conexion.php');

	if(isset($_GET['exp'])){
		$exp = $_GET['exp']; 
		if(isset($_SESSION['id']) && $_SESSION['rango'] == 'jurado'){		//para asegurarnos que el usuario que esta entrando aquí es jurado y que encima ha logeado session
			//compruebo si este usuario ya ha hecho la peticion para este concurso
			$permisoParaEnviarCorreo = true;		//de primeras dejaremos que el usuario envie
			$sql = "Select peticionJurado from users_inscritos where user_jurado=".$_SESSION['id']." and id_concurso=".$_GET['exp'];
			$con = Conexion::conectar();
			$res = $con ->query($sql);
			if($res->num_rows > 0){		//ha encontrado alguien
				$valorPeticion = $res -> fetch_assoc();
				if($valorPeticion['peticionJurado'] == 1){		//este usuario ya ha solicitado ser jurado
					$permisoParaEnviarCorreo = false;
				}
			}
			$sqlEmail = "Select email from usuarios where id_user = ".$_SESSION['id'];
			$resEmail = $con -> query($sqlEmail);
			if($resEmail -> num_rows > 0){
				$emailUsuario = $resEmail -> fetch_assoc();
				$emailUsuario = $emailUsuario['email'];
			}


			Conexion::desconectar($con);		//cierro la conexión
			if($permisoParaEnviarCorreo){
?>

				<main>
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" enctype="multipart/form-data" class="container" id="mailSolicitud">
						<legend>¡Hola <?php echo $_SESSION['usuario']; ?>! - Gracias por interesarte en ser Jurado</legend>
						<div class="row">
							<div class="col-xs-12 input-group">
								<span class="input-group-addon">Asunto:</span>
								<input type="text" class="form-control" name="tituloAsuntoSolicitud" id="tituloAsuntoSolicitud" placeholder="Agregar un asunto" required />
							</div>
						</div>
						<div class="row">
							<div class="cl-xs-12">
								<textarea required class="form-control" placeholder="Añada aqui su mensaje..." name="msgSolicitudJurado" id="msgSolicitudJurado" cols="30" rows="10"></textarea>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<input type="hidden" name="correSolicitudOculto" id="correSolicitudOculto" value="<?php echo $emailUsuario; ?>">
								<input type="submit" class="btn btn-default btn-block" value="Enviar email" name="btnFormSolicitudJurado" id="btnFormSolicitudJurado">
							</div>
						</div>
						<div class="row">	<!-- Contenedor para mensaje de información -->
							<div class="col-xs-12 text-center">
								<div id="solicitarJuradoVacios" class="alert alert-info">Ha dejado campos vacios</div>
								<div id="solicirarJuradoError" class="alert alert-warning">Error al enviar el mensaje</div>
								<div id="solicirarJuradoSuccess" class="alert alert-success">Correo enviado correctamente</div>
							</div>
						</div>
					</form>
				</main>

<?php 
			}else{
				// echo "<div class='alert alert-info text-center'>Ya has enviado la solicitud intentaremos responderte lo antes posible</div>";
				echo "<div class='alert alert-info text-center'>Ya has enviado la solicitud intentaremos responderte lo antes posible<a href='index.php?p=verConcurso&exp=$exp'>Volver atrás</a></div>";
			}
		}else{
			echo "<div class='alert alert-danger text-center'>No tienes acceso a esta página</div>";
		}
	}else{
		echo "<div class='alert alert-danger text-center'>Está página no existe</div>";
	}
?>