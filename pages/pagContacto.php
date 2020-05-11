<?php
	//archivo donde habrá un formulario para poder enviar un correo para consulta o sugerencia al admin
	session_start();
	require_once('./clases/conexion.php');

	if(isset($_SESSION['id']) && $_SESSION['id'] != ''){
		//entrará si es un usuario que esta registrado - por lo tanto debo recoger su correo
		$sql = "Select email from usuarios where id_user = '".$_SESSION['id']."'";
		$con = Conexion::conectar();
		$res = $con -> query($sql);
		if($res -> num_rows > 0){
			$emailUser = $res -> fetch_assoc();
			//Remitente
			$emailUserRegistrado = $emailUser['email'];	//guardo el correo del usuario en una variable
			
		}
		
		Conexion::desconectar($con);		//cierro la conexion - entre o no la última condición
	}

?>

<main>
	<?php 
		if($_SESSION['rango'] != 'admin'){
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id='mailContacto' class='container'>
		<legend>Contacta con nosotros:</legend>
		<?php 
			if(isset($_SESSION['id']) && $_SESSION['id'] == ''){	//es porque es un usuario 'visitante'
		?>
				<div class="row">
					<div class="col-xs-12">
						<div class="alert alert-info">¿No tienes cuenta aún? <a href="./index.php">Registrate</a> para más interacción</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 input-group">
						<span class="input-group-addon">Introduzca su correo:</span>
						<input type="email" class="form-control" id="correoUserVisitante"  name="emailUserVisitante" placeholder="Email" pattern="^[^@\s]+@[^@\.\s]+(\.[^@\.\s]+)+$" required />
					</div>
					<div class="col-xs-12 text-center">
						<div id="emailContactoNoValid" class="alert alert-warning"></div>

					</div>
				</div>
		<?php
			}else{		//si no lo es, entonces enviaré un hidden con el correo del usuario que esta registrado
				echo "<input type='hidden' name='correoOcultoContacto' id='correoOcultoContacto' value='$emailUserRegistrado' />";
			}	
		?>
		<div class="row">
			<div class="col-xs-12 input-group">
				<span class="input-group-addon">Asunto:</span>
				<input type="text" class="form-control"  name="tituloAsuntoEmail" id="asuntoContacto" placeholder="Agregar un asunto" required />
			</div>
		</div>
		<div class="row">
			<div class="cl-xs-12">
				<textarea required class="form-control" placeholder="Añada aqui su mensaje..." name="areaMensaje" id="areaMensajeContacto" cols="30" rows="10"></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<input type="submit" class="btn btn-default btn-block" value="Enviar email" name="btnEnviarCorreoContacto" id="btnEnviarCorreoContacto">
			</div>
		</div>
		<div class="row">	<!-- Contenedor para mensaje de información -->
			<div class="col-xs-12 text-center">
				<div id="contactoVacios" class="alert alert-info">Ha dejado campos vacios</div>
				<div id="contactoError" class="alert alert-warning">Error al enviar el mensaje</div>
				<div id="contactoSuccess" class="alert alert-success">Correo enviado correctamente</div>
			</div>
		</div>
	</form>
	<?php }else{
		echo "<div class='alert alert-info text-center'>¿Porque querría enviarme un correo a mi mismo?</div>";
		} ?>
</main>