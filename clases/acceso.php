<?php
// Lo primero que harémos una vez que hayamos llamado la clase acceso, que controlará el acceso de los usuarios, será incluir el archivo conexión.php
	require_once('conexion.php');
	
	class Acceso{
		//declaro algunos de los atributos que me harán falta para controlar a los usuarios
		private $userName;
		private $pass;
		private $firstName;
		private $lastName;
		private $email;
		private $rango;
		private $online;		//atributo de la clase que indica si el usuario esta conectado o no

		//función para iniciar sesión, y en el caso de que no este establecida la sesión del usuario establecemela a vacio
		public static function iniciar_session(){
			if(!isset($_SESSION['usuario'])){
				$_SESSION['usuario'] = "";
				$_SESSION['id'] = "";
				$_SESSION['rango'] = "";
			}
		}

		//función para que me realice una petición al comienzo de la aplicación para modificar el campo activo de concursos
		public static function concursos_activos(){
			$sql = "Select fecha_end,id from concursos";	//recojo todas las fechas de finalización de mis concursos u el id
			$con = Conexion::conectar();
			$res = $con->query($sql);
			$diaActual = date('d/m/Y');
			if($res->num_rows>0){		//porque ha realizado la consulta correctamente
				while($linea = $res ->fetch_assoc()){	//para cada líena recojo su información
					
					if($linea['fecha_end'] < $diaActual){		//es porque ya ha pasado la fecha del concurso
						$con -> query("Update concursos SET activo = 0 WHERE id = ".$linea['id']);
					}
				}
			}
			Conexion::desconectar($con);		//cierro la conexión
		}

		public function buscaNomUser($user){
			$encontrado = false;		//variable que nos dirá si ha encontrado ya un nombre de usuario en la BD igual o no
			//para comprobar si el nombre de user ya existe debo de conectar con la BD
			$sql = "Select Username from usuarios";		//recojo todos los nombres para luego hacer la comprobación uno a uno
			$conexion = Conexion::conectar();		//realizo conexion con la BD
			$res = $conexion->query($sql);		//mi consulta con la sentencia sql que he creado arriba
			if($res->num_rows>0){		//porque ha realizado la consulta correctamente
				while($linea = $res ->fetch_assoc()){	//para cada líena recojo su información
					if(strtolower($linea['Username']) == $user)
						$encontrado = true;
				}
			}
			Conexion::desconectar($conexion);		//cierro la conexión
			return $encontrado;
		}


		public function buscaMail($mail){
			$encontrado = false;		//variable que nos dirá si ha encontrado ya una email en mi BD, por lo que dirá que el usuario ya existe
			//para comprobar si el email ya existe debo de conectar con la BD
			$sql = "Select email from usuarios where email='$mail'";
			$conexion = Conexion::conectar();
			$res = $conexion->query($sql);
			if($res->num_rows>0) //aunque rcoja una línea de mi BD, quiere decir que ha encontrado un email igual al que le he pasado
				$encontrado = true;
			Conexion::desconectar($conexion);
			return $encontrado;
		}

		public function registrar_Usu($correo,$nombreUser,$clave){
			$valido = true;
			//cuando vaya a guardar mi contraseña primero la encripto
			// según el manual de PHP nos aconsejan que lo hago por el método password_hash o crypt() debido a que los otros metodos conocidos como md5 etc son muy antiguos y faciles de hackear
			//para este método debemos pasarle 3 parametros la clave - una constraseña por defecto (es aconsejable) - y un array indicandole que el nivel de coste(fuerza, intensidad) de mi constraseña será el que le indique ej: 10<15
			$clave = password_hash($clave,PASSWORD_DEFAULT,array('cost'=>12));
			//pongo el this en la llamada de la función porque hago referencia al objeto actual
			// en el campo rango_user pondré de primeras 'participante' ya que cuando te registras aún no tienes el privilegio de ser jurado y muhco menos administrador
			//en el campo online pondre conectado ya que cuando te registras estas en línea aún
			$sql = "Insert into usuarios (Username,Password,email,rango_user,online) values ('$nombreUser','$clave','$correo','participante','conectado')";
			$con = Conexion::conectar();
			if(!$con->query($sql)){		//si no se ha realizado conrrectamente la consulta
				$valido = false;
			}else{		//si se ha guardado correctamente
				$sql = "Select id_user from usuarios Where Username ='".$nombreUser."'";		//uan vez qe he realizado el insert hgo una consulta para coger el id de ese usuario
				$res = $con->query($sql);
				$this -> userName = $nombreUser;
				$this -> pass = $clave;
				$this -> email = $correo;
				$this -> rango = 'participante';
				$this -> online = 'conectado';
				$_SESSION['usuario'] = $nombreUser;		//lo guardo en mi variable session así podre acceder a él cuando quiera mostrar el nombre
				$linea = $res->fetch_assoc();
				$_SESSION['id'] = $linea['id_user'];	//cuando necesite la id para conectar tablas de mi BD
				$_SESSION['rango'] = 'participante';		//cuando te registras el rango más alto que eres es participante
			}
			Conexion::desconectar($con);
			return $valido;
		
		}

		public function validar_Usu($userName,$pass){
			$valido = false; 	//de primeras no se va a cumplir el proceso correctamente
			//creamos una variable contador para saber si el usuario que ha introdufido los datos en el apartado login coincide con los datos que se encuentra en mi BD e incrementará si lo encuentra
			$contador = 0;	
			//solo seleccionaré los datos de la tabla usuarios que coincida con el nombre que ha puesto el user en username
			$sql = "Select Username,Password,id_user,rango_user from usuarios where Username='$userName'";
			$con = Conexion::conectar();
			$res = $con->query($sql);
			if($res->num_rows > 0){		//si esto es true es porque ha encontrado en la BD el usuario
				$linea = $res -> fetch_assoc();	//guardo en línea el usuario registrado - ya que solo va encontrar un usuario con ese nombre
				//comprubeo con la función password_verify() si la contraseña coincide con el hash que tiene este usuario - le paso dos parametros (contraseña introducida y contraseña (el hash) guardada en la BD)
				if(password_verify($pass,$linea['Password'])){
					$contador++;
				}
				if($contador > 0){
					$this->userName = $linea['Username'];
					$this ->pass = $linea['Password'];
					//y actualizo el campo de mi tabla usuarios de la BD para que ponga ahora conectado en la parte de website
					$con->query("Update usuarios set online = 'conectado' Where Username='$userName'");
					$this->online = "conectado";
					//inicializo la variable session para el id y para recoger el nombre
					$_SESSION['usuario'] = $linea['Username'];
					$_SESSION['id'] = $linea['id_user'];
					$_SESSION['rango'] = $linea['rango_user'];		//por si más adelante necesito el rango del participante para algunos permisos
					$valido = true;		//le digo que el proceso de cumplido correctamente
				}
			}

			Conexion::desconectar($con);
			return $valido;
		}

		public function set_Conectados($nomUser){		//función para devolver una cadena de todos los usuarios que esten conectados
			$conectados = "";		// cadena que contendrá los nombres de los usuarios conectados
			//sentencia sql que recoje todos los usuarios de mi BD qie esten conectados y que no tengan como nombre de usuario el que le indico
			$sql = "Select Username from usuarios where not Username='$nomUser' and online='conectado'";
			$con = Conexion::conectar();
			$res = $con->query($sql);		//almaceno la consulta en una variable respuesta
			if($res -> num_rows >0){		//si es cierto es que ha encontrsdo ussuarios que no coincidan con ese nombre
				while($linea = $res ->fetch_assoc()) {
					$conectados .= $linea['Username'].'</br>';
				}	
			}
			Conexion::desconectar($con);
			return $conectados;
		}

		public function close_session($username){
			$apagado = true;	//supondremos que hemos apagado correctamente todo
			$sql = "Update usuarios set online = '' Where Username='$username'";
			$con = Conexion::conectar();
			if($con -> query($sql)){
				$_SESSION['usuario'] = '';
				$_SESSION['id'] = '';
				$_SESSION['rango'] = '';
    			session_destroy();
    			session_start();
			}else{
				$apagado = false;
			}
			Conexion::desconectar($con);
			return $apagado;
		}
		
	}		//fin de la clase
?>