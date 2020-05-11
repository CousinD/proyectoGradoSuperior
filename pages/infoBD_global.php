<?php 
	session_start();
	//este archivo nos servirá para ir recogiendo información de toda nuestra base de datos - tendremos acceso en toda la páginas
	require_once('../clases/acceso.php');
	require_once('../clases/conexion.php');

	if(isset($_POST['buscarConcursos'])){
		$con = Conexion::conectar();
		$sql = "Select id from concursos where nombre = '".$_POST['concursoNom']."'";
		$res = $con->query($sql);
		if($res->num_rows > 0){
			$res = $res->fetch_array();
			$datos_devuelta = $res['id'];
			
		}
		Conexion::desconectar($con);		//cierro la conexión
		echo $datos_devuelta;
	}

	if(isset($_GET['q'])){
		$con = Conexion::conectar();
		$sql = "Select nombre from concursos where nombre like '%".$_GET['q']."%'";
		$res = $con->query($sql);
		$datos = array();

		while($row = $res->fetch_array()){
			array_push($datos, $row['nombre']);
		}

		echo json_encode($datos);
	}

	if(isset($_POST['crearConcurso'])){		//función que entrará cuando cree un concurso
		//lo primero que debo de hacer es recoger la fecha actual - ya que será un campo de mi base de datos que tengo que rellenar
		$hoy =  date('d/m/Y');
		//reocojo los valores para el resto de mi insert
		$tituloConcurso = $_POST['tituloConcurso'];
		$enfoque = $_POST['enfoque'];
		$finConcurso = $_POST['finConcurso'];
		$portada = $_POST['portada'];
		$idCreador = $_SESSION['id'];
		$descripcion = $_POST['descripcion'];
		$formato = $_POST['formato'];
		$num_fotos = $_POST['numFotos'];
		// realizo la petición a la base de datos crear el concurso
		$datos_devuelta = 'ok';
		$con = Conexion::conectar();
		$sql = "INSERT INTO concursos (nombre, enfocado, fecha_start, fecha_end, imagen_portada, id_usuario, descripcion, formato_img, num_fotos, activo) VALUES ('".$_POST['tituloConcurso']."','".$_POST['enfoque']."','$hoy','".$_POST['finConcurso']."','".$_POST['portada']."','".$_SESSION['id']."','".$_POST['descripcion']."','".$_POST['formato']."','".$_POST['numFotos']."','1')";
		// $sql = "INSERT INTO concursos (nombre, enfocado, fecha_start, fecha_end, imagen_portada, id_usuario, descripcion, formato_img, num_fotos, activo) VALUES ('$tituloConcurso','$enfoque','$hoy','$finConcurso','$portada','$idCreador','$descripcion','$formato','$num_fotos', '1')";
		if(!$con->query($sql)){
			$datos_devuelta = 'bad';
		}

		Conexion::desconectar($con);		//cierro la conexión

		echo $datos_devuelta;
	}
	
	if(isset($_POST['modificarUser'])){		//función que entrará cuando vaya a realizar los cambios de la información del perfil
		//puede ser que el usuario no cambié la foto entonces no actualizaré la foto porque será la misma que tiene
		if($_POST['modPhotoProfile'] != ''){ //cuando no ha seleccionado ninguna foto
			$sql = "UPDATE usuarios SET Username = '".$_POST['modNombreUser']."', Nombre = '".$_POST['modNombre']."',Apellidos ='".$_POST['modApell']."', imageProfile ='".$_POST['modPhotoProfile']."' WHERE id_user='".$_SESSION['id']."'";
		}else{
			$sql = "UPDATE usuarios SET Username = '".$_POST['modNombreUser']."', Nombre = '".$_POST['modNombre']."',Apellidos ='".$_POST['modApell']."' WHERE id_user='".$_SESSION['id']."'";
		}
		
		$datos_devuelta = 'ok';
		$con = Conexion::conectar();
		if(!$con->query($sql)){
			$datos_devuelta = 'bad';
		}

		Conexion::desconectar($con);		//cierro la conexión

		echo $datos_devuelta;
	}


	if(isset($_POST['addHilo'])){

		$creationHilo = date('d/m/Y');	//variable para recoger la fecha del día actual, que será e dia que se creo el hilo

		$datos_devuelta = 'ok';
		$con = Conexion::conectar();
		$sql = "INSERT INTO tema_concursos( id_concurso, id_usuario, nombre, hilo_tema, fecha) VALUES ('".$_POST['idConcurso']."','".$_SESSION['id']."','".$_SESSION['usuario']."','".$_POST['tituloHilo']."','".$creationHilo."')";
		if(!$con->query($sql)){
			$datos_devuelta = 'bad';
		}else{
			//entrará en esta condición si se ha realizado correctamente la consulta - una vez que se ha realizado entonces recogjo el id principal del hilo de la última inserción - para luego devolverlo como parametro a la petición ajax
			$sql = "SELECT MAX(id) AS id FROM tema_concursos";
			$res = $con->query($sql);
			if($datoRow = $res->fetch_row()){
				$lastID = trim($datoRow[0]);		//tengo el último id añadido a la tabla
				$sql = "INSERT INTO mensaje_tema (id_tema, id_usuario, comentario, fecha) VALUES ('".$lastID ."','".$_SESSION['id']."','".$_POST['comenNewHilo']."','".$creationHilo."')";
				if(!$con->query($sql)){
					$datos_devuelta = 'bad';
				}
			}else{
				$datos_devuelta = 'bad';
			}
		}

		Conexion::desconectar($con);		//cierro la conexión

		echo $datos_devuelta;
	}

	if(isset($_POST['inscribirseConcurso'])){
		//entrará aqui siempre que vaya a inscribir un usuario en el concurso
		$datos_devuelta = 'ok';
		$sql = "Insert into users_inscritos (id_usuario,id_concurso) values ('".$_SESSION['id']."','".$_POST['id_concurso']."')";
		$con = Conexion::conectar();
		if(!$con -> query($sql)){
			$datos_devuelta = 'error';
		}

		Conexion::desconectar($con);		//cierro la conexión

		echo $datos_devuelta;
	}

	if(isset($_POST['enviarCorreo'])){

		//enviamos el correo al destinatario
		if(mail($_POST['emailDesti'],$_POST['asunto'],$_POST['mensaje'])){
			$datos_devuelta = 'ok';
		}else{
			$datos_devuelta = 'error';
		}

		echo $datos_devuelta;
	}

	if(isset($_POST['perdirSerJurado'])){		//solo para comprbar que ese id_concurso existe
		$datos_devuelta = 'ok';
		$sql = "Select id from concursos where id=".$_POST['id_concurso'];
		$con = Conexion::conectar();
		if(!$con -> query($sql)){
			$datos_devuelta = 'error';
		}

		Conexion::desconectar($con);		//cierro la conexión

		echo $datos_devuelta;
	}


	if(isset($_POST['cambiarPuntuacion'])){  //función para actualizar la puntuacion de imagen indicada
		$datos_devuelta = 'ok';	//suponemos que todo irá bien

		//lo primero que haré será hacer una petición para recoger el valor de la puntuación actual
		$sqlSelectPunActual = "Select puntuacion from fotos_concurso where id=".$_POST['idPhoto'];
		$con = Conexion::conectar();
		$res = $con->query($sqlSelectPunActual);
		if($res->num_rows > 0){
			$res = $res->fetch_array();
			$puntuacionActual = $res['puntuacion'];
		}


		$id_deImagen = $_POST['idPhoto'];		//como dice la variable es la id de la imagen
		$nuevoValorJurado = $_POST['valorCambiar'];		//la puntuacion del jurado
		$nuevoValor = $_POST['valorTotal'];	//el valor para sumar o restar a mi puntuacion de la imagen
		
		//una vez dentro compruebo si es resta o suma
		if(isset($_POST['op_resta'])){		//entrará cuando sea una resta
			$sqlNuevaPuntuacion = "update img_puntuadas set ultPuntuacion ='".$_POST['valorCambiar']."' WHERE id_foto =".$_POST['idPhoto'];
			
			if(!$con -> query($sqlNuevaPuntuacion)){
				$datos_devuelta = 'error';
			}else{
				//antes de actualizar la puntuacion de la tabla fotos_concurso tengo que hacer la operacion
				//basicamente lo que hace es restarle la diferencia de lo que ha cambiado el jurado
				$nuevoValor = $puntuacionActual - $_POST['valorTotal'];
				$sqlCambiarPuntuacion = "update fotos_concurso set puntuacion ='".$nuevoValor."' WHERE id =".$_POST['idPhoto'];
				if(!$con -> query($sqlCambiarPuntuacion)){
					$datos_devuelta = 'error';
				}
			}

			Conexion::desconectar($con);		//cierro la conexión
		}

		if(isset($_POST['op_suma'])){ //lo mismo pero con suma
			$sqlNuevaPuntuacion = "update img_puntuadas set ultPuntuacion ='".$_POST["valorCambiar"]."' WHERE id_foto =".$_POST['idPhoto'];
			
			if(!$con -> query($sqlNuevaPuntuacion)){
				$datos_devuelta = 'error';
			}else{
				//antes de actualizar la puntuacion de la tabla fotos_concurso tengo que hacer la operacion
				//y en este caso al revés, sumarle la diferencia
				$nuevoValor = $puntuacionActual + $_POST['valorTotal'];
				$sqlCambiarPuntuacion = "update fotos_concurso set puntuacion ='".$nuevoValor."' WHERE id =".$_POST['idPhoto'];
				if(!$con -> query($sqlCambiarPuntuacion)){
					$datos_devuelta = 'error';
				}
			}

			Conexion::desconectar($con);		//cierro la conexión
		}

		echo $datos_devuelta;
	}


?>