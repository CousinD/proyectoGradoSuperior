<?php 
	session_start();		//de esta forma podre obtener id del usuario que este con la session iniciada
	class Conexion{
		public static function conectar(){
			$servidor = 'Localhost'; //porque de momento estoy trabajando en local
			$usuario = 'u635247344_root'; // el usuario que se puso en un principio
			$clave = 'Photoline10'; // contraseña de la base de datos
			$base_de_datos = 'u635247344_image'; // nombre de la base de datos
			$con = new mysqli($servidor, $usuario, $clave, $base_de_datos);

			
			if ($con==FALSE){
			 echo "<font color=red>Fallo al conectar a MySQL: (" . $con->connect_errno . ") " . $con->connect_error;
			  echo "</font>\n" ;
			  die ();
			}
			
			return $con;
		}

		public static function desconectar($con){
			$con -> close();
		}

		// public static function getFotos(){		//fu
		// 	$con = conectar();
		// 	$res = $con ->query("Select * from fotos_concurso");
		// 	$array = array();
		// 	while($infoTabla = $res -> fetch_assoc()){
		// 		array_push($array, $infoTabla[])
		// 	}

		// 	return $respuesta;
		// }


		public static function getFotos(){
			$con = conectar();
			$res = $con ->query("Select * from fotos_concurso");
			$array = array();
			while($infoTabla = $res -> fetch_row()){
				array_push($array, $infoTabla);
			}
			return $array;
		}

		public static function deleteFotos(){
			$con = conectar();
			$res = $con ->query("Delete * from fotos_concurso");
			$array = array();
			while($infoTabla = $res -> fetch_row()){
				array_push($array, $infoTabla);
			}
			return $array;
		}


		public static function insertarFotos($nomFoto){
			$con = conectar();
			$sql = "Insert into fotos_concurso (id_usuario,id_concurso,ruta) values ('".$_SESSION['id']."','idconcurso','".$nomFoto."')";
			$con -> query($sql);
		}
	}
?>