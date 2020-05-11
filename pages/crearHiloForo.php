<?php
	session_start();
	//archivo que contendrá un formulario donde podré crear un nuevo hilo para mi concurso
	require_once('./clases/conexion.php');
	//de primeras recogeré el valor que hay en la url - que será el nombre del concurso para indicar a cual hago referencia
	if(isset($_GET['concurso']) && isset($_GET['exp'])){
		// //realizo una petición para recoger el id del 
		// $sql = "Select * from concursos where id=".$_GET['exp']."";
		// $con = Conexion::conectar();
		// $res = $con -> query($sql);
		// if($res -> num_rows > 0){		//es porque ha encontrado el concurso sino mostraré la página con el mensaje de alerta
		// 	//guardo el contenido que he recogido de la BD en una variable
		// 	$datos = $res -> fetch_assoc();
?>
	<main>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="crearNuevoHilo">
			<p>Hola <?php echo $_SESSION['usuario']; ?>, ¿sobre que quieres crear un nuevo hilo?</p>
			<p>Para el concurso: <?php echo $_GET['concurso']; ?></p>
			<input type="text" placeholder="Título sobre el hilo" id="tituloNuevoHilo" name="tituloNuevoHilo" required /><br>
			<textarea name="comentarioNuevoHilo" id="txtDelNuevoHilo" cols="100" rows="10" placeholder="Añada su comentario..." required ></textarea><br>
			<!-- Como voy a realizar la creación de mi formulario por medio de ajax debo pasarle de alguna manera los datos entonces por eso creo este input hidden para poder recogerlo de alguna forma en javascript -->
			<input type="hidden" value="<?php echo $_GET['exp'];?>" id="idConcursoHilo"/>
			<input type="submit" value="Crear" name="addNuevoHilo" id="btnCrearNuevoHilo" />
			<span class="msgErrores alert alert-warning"></span>
			<span class="msgCorrect alert alert-success"></span>
		</form>
	</main>
<?php 
	}else{
		echo "Quiere acceder a una página inexistente. Por favor, le recomendamos que vuelva hacía atrás.";
	}
?>