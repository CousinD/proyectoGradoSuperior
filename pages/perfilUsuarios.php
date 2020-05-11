<?php 
// archivo que muestra la información del usuario
	require_once('./clases/conexion.php');
	//cuando cargue la página realizaré un petición a mi base de datos para poder recoger todos la información de mi usuario
	$sql = "Select Username,Nombre,Apellidos,email,imageProfile,rango_user from usuarios where id_user = ".$_SESSION['id'];
		$conexion = conexion::conectar();
		$res = $conexion->query($sql);
		if($res->num_rows>0){		//porque ha encontrado el usuario
			$infoUser = $res -> fetch_assoc();	//recojo los valores del usuario
		}

		Conexion::desconectar($conexion);
		
?>
<main>
	<?php if(isset($infoUser)){ ?>	<!-- Entrará siempre que la información que he recogido de la base de datos es correcta -->
	<form id="verPerfil" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="container" />
		<legend>Información de mi Perfil</legend>
		<div id="fotoPerfil" class="row">
			<div class="col-xs-12 col-md-6">
				<?php if($infoUser['imageProfile'] != ''){ //entrará en la condición siempre que el usuario tenga imagen asignada por el mismo ?>
					<img src="./img/profile/<?php echo $infoUser['imageProfile']; ?>" title="Imagen de perfil del usuario <?php echo $infoUser['Username']; ?>" />
				<?php }else{ ?>
					<img src="./img/profile/imgDefault.png" title="Imagen de perfil del usuario <?php echo $infoUser['Username']; ?>" />
				<?php }  ?>
				<button id="btnChangeImgPortada" class="btn btn-default">Foto Perfil</button>
				<input type="file" name="fileProfile" id="changeImgProfile" />
			</div>
			<div class="col-xs-12 col-md-6">
				<h3 class="alert alert-info">Nombre de Usuario</h3>
				<?php echo "<input type='text' id='userNameProfile' class='form-control' value='".$infoUser['Username']."' required />"; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<h3 class="alert alert-info">Nombre</h3>
				<?php echo "<input type='text' id='nameProfile' class='form-control' placeholder='Estoy esperando que escribas tu nombre' value='".$infoUser['Nombre']."' />"; ?>
			</div>
			<div class="col-xs-12 col-md-6">
				<h3 class="alert alert-info">Apellidos</h3>
				<?php echo "<input type='text' id='subNameProfile' class='form-control' placeholder='Algún apellido tendrás ;)' value='".$infoUser['Apellidos']."' />"; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<h3 class="alert alert-info">Rango de Usuario</h3>
				<?php echo "<h4>".$infoUser['rango_user']."</h4>"; ?>
			</div>				
			<div class="col-xs-12 col-md-6">
				<h3 class="alert alert-info">Correo</h3>
				<?php echo "<h4>".$infoUser['email']."</h4>"; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
					<button id="modInfoProfile" class="btn btn-default btn-block"> Modificar Información</button>
			</div>
			<div class="col-xs-12">
				<div class="suggestion"></div>
			</div>
		</div>
		<span class="msgErrores alert alert-warning"></span>
		<span class="msgCorrect alert alert-success"></span>
	</form>

	<?php }else{
		echo 'Usuario no encontrado';
	}?>
</main>