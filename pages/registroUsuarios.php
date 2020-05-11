<?php 
	//inicio la session, ya que posiblemente tenga que utilizar variables de session para recoger datos como el captcha por ejemplo
	session_start();
	require_once('../clases/acceso.php');		//importo mi fichero de acceso para trabajar con la clase y así poder llamar a sus funciones
	$respuesta = '';		//será mi variable que devolver y determinará si se ha realizado todo bien o si ha encontrado errores
	//primero compruebo que mi código captcha es el mismo al que ha introducido el usuario, como lo tengo en mi variable session - guardaa previamente en el archivo captcha.php -  puedo acceder a él
	if(md5($_POST['captcha']) != $_SESSION['key']){	//si son distintos entonces muestr error, sino entonces continuo con el registro
		$respuesta = 'failCaptcha';
	}else{
		//creo mi variable acceso para llamar a las funciones de registro que conectan con la BD
		$acceso = new Acceso();
		//recojo los valores que he pasado
		$nomUser = $_POST['nomUsr'];
		$correo = $_POST['correo'];
		$password = $_POST['clave'];
		//tengo que comprobar primero si el email o el nombre de usuario que estoy utilizando, no están ocupados antes de registrar
		$encontrado = $acceso ->buscaMail($correo);
		if(!$encontrado){
			//si no lo ha encontrado busco el nombre de usuario - que lo hago en minusculas para luego comprobarlo con la BD
			$encontrado = $acceso->buscaNomUser(strtolower($nomUser));
			if(!$encontrado){
				//si no lo ha encontrado es que no estan estos valores en la base de datos entonces inserto los valores en la BD
				$valido = $acceso->registrar_Usu($correo,$nomUser,$password);//esta función devolverá true o false
				if($valido){
					$respuesta = "correcto";		//hemos registrado el usuario correctamente
				}else{
					$respuesta = "incorrecto";		//no se ha podido registrar el usuario
				}
			}else{
				$respuesta = "userRepetido";		//el usuario que ha introducido es invalido porque esta repetido
			}
		}else{
			$respuesta = "emailEncontrado";		//el email esta repetido en la BD
		}
	}
	echo $respuesta;
?>