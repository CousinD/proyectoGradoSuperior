<?php 
		session_start();
		require_once('./clases/acceso.php');
		//con la clase acceso importo el fichero y llamo a la función para iniciar session
		Acceso::iniciar_session();
		Acceso::concursos_activos();		//para saber que concursos están activos o no
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
	<title>ImageTender</title>
	<!-- // Importamos tanto los archivos como las librerías que vaya a utilizar, las librerias siempre van priemro que mis propios documentos -->
	<script type="text/javascript" src="./lib/jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="./lib/mailcheck.min.js"></script>
	<script type="text/javascript" src="./lib/jquery-ui.min.js"></script>
	<script type="text/javascript" src="./lib/bootstrap.min.js"></script>
	<script type="text/javascript" src="./lib/bootstrap-filestyle.min.js"></script>

	<!-- // Esta es mi librería para el carrousel -->
	<script type="text/javascript" src="./lib/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>
	<!-- Este es mi archivo js para el carousel con las funciones de la librería-->
	<script type="text/javascript" src="./js/carousel.js"></script>

	

	<!-- //código propio -->
	<script type="text/javascript" src="./js/registrarse.js"></script>
	<script type="text/javascript" src="./js/logueo.js"></script>
	<!-- //importo la librería casera de mis funciones de JS para validar -->
	<script type="text/javascript" src="./js/validaciones.js"></script>
	<!-- //Mi código principal de javascript lo añadire en el último lugar para que carguen todos los demás ficheros primero -->
	<script type="text/javascript" src="./js/proyecto.js"></script>

	<!-- // importo mis archivos de las librerias css -->
	<link rel="stylesheet" href="./css/jquery-ui.min.css">
	<link rel="stylesheet" href="./css/bootstrap.min.css">

	<!-- <link rel="stylesheet" href="./css/carousel.css"> -->
	<link rel="stylesheet" href="./css/proyecto.css"> <!-- CSS principal de mi proyecto -->
	<link rel="stylesheet" href="./css/carousel.css">

	<!-- Favicon de mi web -->
	<link rel="apple-touch-icon" sizes="57x57" href="./img/favicon/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="./img/favicon/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="./img/favicon/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="./img/favicon/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="./img/favicon/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="./img/favicon/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="./img/favicon/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="./img/favicon/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="./img/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="./img/favicon/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="./img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="./img/favicon/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="./img/favicon/favicon-16x16.png">
	<link rel="manifest" href="./img/favicon/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="./img/favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
</head>
<body>
	<header>
		<div class="container">
			<div class="row">
				<div id="logo" class="col-sm-4 col-md-2 col-lg-6">
					<a href="./index.php">
						<img src="./img/LogoMakr.png" title="ImageTender" alt="Logo Web ImageTender">
					</a>
				</div>
				<input type="checkbox" id="btn-menu">
				<label for="btn-menu" class="glyphicon glyphicon-menu-hamburger"></label>
				<nav class="menu col-xs-12 col-sm-8 col-md-10 col-lg-6">
				<?php if($_SESSION['usuario'] != ''){ ?>
					<ul style='width: 500px;'>
				<?php }else{ ?>
					<ul style='width: 400px;'>
				<?php } ?>
					
						<li><a href="./index.php?p=inicio">Inicio</a></li>
						<li><a href="./index.php?p=concursos">Concursos</a></li>
						<li><a href="./index.php?p=sobreNosotros">Conocenos</a></li>
						<li><a href="./index.php?p=contactar">Contactar</a></li>
						<?php if($_SESSION['usuario'] != ''){ ?>
		<!-- tendre una opción desplegable para los usuarios registrados y depende los privilegios unas opciones u otras -->
							<li class='submenu'><a href='#'><?php echo $_SESSION['usuario']; ?><span class="caret"></span></a>
								<ul>
								<?php if($_SESSION['rango'] == 'admin'){ ?>
									<li><a href="./index.php?p=crearConcurso">Crear Concurso</a></li>
								<?php } ?>
									<li><a href="./index.php?p=perfil">Perfil</a></li>
									<li><a href="./index.php?p=datosPdf">Datos PDF</a></li>
									<li><a href="" id="offSession">Cerrar Sesion</a></li>
								</ul>
							</li>
						<?php
							} 
						?>
					</ul>
				</nav>
			</div>
		</div>
		<div id="barraSearch">
			<input type="text" autofocus placeholder="Buscar concurso" class="form-control" id="inputSearch">
			<span class="glyphicon glyphicon-search"></span>	
		</div>
	</header>