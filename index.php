<?php
 //utilizo la función 'require()' porque así se que si hay un fallo no se enviaraá 
	include('cabecera.php');
	
?>
<?php
		// por defecto simpre comenzará enla página inicial por lo tanto guardo el nombre en un variable
		$opcion = "inicio";
			if(isset($_GET['p']))
				$opcion = $_GET['p']; //le doy el valor que tenga en mi url la variable 'p'

			/* ESTAS SON MI OPCIONES PRINCIPALES DONDE PODRÉ ACCEDER FACILMENTE DEBIDO A QUE SON LOS ENLACES QUE SE VERÁN EN TODAS LAS PÁGINAS D MI APLIC */
			if($opcion=="inicio"){
				include('./pages/inicio.php');
			}

			if($opcion=="concursos"){
				include('./pages/concursos.php');
			}

			if($opcion=="sobreNosotros"){
				include('./pages/conocenos.php');
			}

			if($opcion == "contactar"){
				include('./pages/pagContacto.php');
			}

			/* Esta opción solo será accesible cuando un usuario se haya registrado */
			if($opcion=="perfil"){
				include('./pages/perfilUsuarios.php');
			}

			if($opcion=="datosPdf"){
				include('./pages/generarPDF.php');
			}

			/* Esta opción solo será accesible para el usuairo administrador */
			if($opcion == "crearConcurso"){
				include('./pages/nuevoConcurso.php');
			}

			/* A PARTIR DE AQUÍ, ACCEDEREMOS A LAS OPCIONES SECUNDARIAS, A LAS QUE ENTRAREMOS SI QUEREMOS EJECUTAR UNA ACCIÓN ESPECIFICA AL ALUMNO */

			if($opcion == "verConcurso"){
				include('./pages/interactuar.php');
			}


			if($opcion == "serJurado"){
				include('./pages/correoPedirSerJurado.php');
			}
		?>
		
<?php
	require_once('pie.php');
?>
