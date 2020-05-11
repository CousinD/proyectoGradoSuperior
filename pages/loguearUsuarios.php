<?php 
	//fichero para administrar el logueo y el cierre session de los usuarios
	session_start();	//por si en algún momento necesit las variables de session
	require_once('../clases/acceso.php');

	//creo mi variable acceso para poder utilizarla y llamar las funciones de la clase
	$acceso = new Acceso();
	$respuesta = '';

	if(isset($_POST['cerrar'])){
		//si entra en esta condicion es porque quiero cerrar la session
		if($acceso -> close_session($_SESSION['usuario'])){
			$respuesta = 'ok';
		}
	}


	if(isset($_POST['entrar'])){	//Condición que entrará cuando el usuario vaya a loguearse y necesite recoger valores de la BD para compararlos con los que ha introducido
		//recojo los valores
		$nomUsuario = $_POST['nomUser'];
		$pass = $_POST['clave'];
		if($acceso ->validar_Usu($nomUsuario,$pass)){
			$respuesta = 'ok';
		}else{
			$respuesta = 'noExiste';
		}

	}


	echo $respuesta;
?>