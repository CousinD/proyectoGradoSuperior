<?php 		
	session_start();
	//este archivo nos servirá para realizar consultas a la hora de subir archivos
	require_once('../clases/acceso.php');
	require_once('../clases/conexion.php');

		if(isset($_FILES['elegirImagenPortada'])){
			$datos_devuelta = 'ok';
			$rutaProvisional = $_FILES['elegirImagenPortada']['tmp_name'];		//donde se encuentra en este momento la imagen
			$nomImage = $_FILES['elegirImagenPortada']['name'];		//nombre del archivo
			$carpetaDestino = '../img/frontPage/';

			if(is_uploaded_file($rutaProvisional)){		//condición que me comprueba si hay algún archivo en la carperta temportal
				//antes de realizar la subida de la imagen a la carpeta debo comprobar si esa imagen ya esta en el sevidor
				if(is_file($carpetaDestino.$nomImage)){
					$datos_devuelta = 'imgExiste';
				}else{
					move_uploaded_file($rutaProvisional, $carpetaDestino.$nomImage);
				}

			}	
		
			echo $datos_devuelta;
		}

		if(isset($_FILES['changeImgProfile'])){		//si es la petición para modificar los datos del perfil entrará aquí
			$datos_devuelta = 'ok';
			$rutaProvisional = $_FILES['changeImgProfile']['tmp_name'];		//donde se encuentra en este momento la imagen
			$nomImage = $_FILES['changeImgProfile']['name'];		//nombre del archivo
			$carpetaDestino = '../img/profile/';
			$tamImageProfile = $_FILES['changeImgProfile']['size'];
			$typeImgProfile = $_FILES['changeImgProfile']['type'];

			if(is_uploaded_file($rutaProvisional)){		//condición que me comprueba si hay algún archivo en la carperta temportal
				if($typeImgProfile == 'image/jpeg' && $tamImageProfile <= 100000){		//tipo y tamaño permitidos
					//antes de realizar la subida de la imagen a la carpeta debo comprobar si esa imagen ya esta en el sevidor
					if(is_file($carpetaDestino.$nomImage)){
						//como la imagen ya existe entonces tendremos que darle un nuevo nombre - ya que los usuarios pueden tener la misma foto de perfil pero no puede tener el mismo nombre
						$timeSubida = time();
						$nomImage = $timeSubida.'-'.$nomImage;
					}
					// no pongo else porque así no tengo que poner dos veces la misma función 'move_up...'
					move_uploaded_file($rutaProvisional, $carpetaDestino.$nomImage);
					//una vez que la he subido al servidor entonces guardo el nombre en la base de datos
					$sql = "UPDATE usuarios SET imageProfile = '".$nomImage."' WHERE id_user='".$_SESSION['id']."'";
					$con = Conexion::conectar();
					if(!$con->query($sql)){
						$datos_devuelta = 'errorSubir';
					}

					Conexion::desconectar($con);		//cierro la conexión
				}else{
					$datos_devuelta = 'noPermitido';
				}
			}else{
				$datos_devuelta = 'errorSubir';
			}
		
			echo $datos_devuelta;
		}
		
		/* MÉTODOS PARA SUBIR IMAGENES DEL CONCURSO */
		//cuando recoja mis imagenes que quiero subir comprobaré si la carpeta de imagenes del concurso existe - sino crearé una carpeta con el nombre del concurso para saber a donde pertenecen mis imagenes
		
		if(isset($_FILES['misImagenes'])){
			$datos_devuelta = 'ok';
			// una vez que entre aqui tendre que ir recogiendo mis imagenes y subiendolas al servidor y guardandolas en mi carpeta - por lo tanto vamos a empezar con mi carpeta
			$miCarpetaDeImagenes = "../img/imgConcursos/".$_POST['nombreDelConcurso'];	//esta es la ruta de mi carpeta
			$miContenedorDeImagenes = $_FILES['misImagenes'];

			//voy a crear dos variables para que accedan rapido a la ruta de mi objeto FormData ya que así no tengo que escribir todo el rato ['tmp_name'] or ['name']
			$rutaProvisional = $miContenedorDeImagenes['tmp_name'];
			$nomImage = $miContenedorDeImagenes['name'];

			if(!file_exists($miCarpetaDeImagenes)){		//le estoy diciendo que si esta carpeta no existe que entre en la condición
				mkdir($miCarpetaDeImagenes,0777); // '0777' crea el directorio de la manera más amplia posible

			}

			//una vez hecho esto recorro mi $_FILES y voy añadiendo las imagenes a la carpeta y subo el nombre al servidor - con count() cuento los elementos de mi array
			$contar = count($miContenedorDeImagenes);
			$con = Conexion::conectar(); //creo mi objeto Conexión ya que sino estaría todo el rato creando en el bucle
			
			for($i=0;$i<$contar;$i++){
				if(is_uploaded_file($rutaProvisional[$i])){	//condición que me comprueba si hay algún archivo en la carperta temporal
					if(is_file($miCarpetaDeImagenes.'/'.$nomImage[$i])){	//por si ya esta en el servidor
						$datos_devuelta = 'imgMismoNombre';
					}else{
						//primero realizo el insert de mi base de datos y si se ha realizo entonces la subiré al servidor
						$sql = "INSERT INTO fotos_concurso (id_concurso, id_usuario, ruta, nombreUser) VALUES ('".$_POST['idDelConcurso']."','".$_SESSION['id']."','".$nomImage[$i]."','".$_SESSION['usuario']."')";
						if(!$con->query($sql)){
							//recojo el valor del error de sql
							$datos_devuelta = mysqli_error($con);
						}else{
							//si tiene problema al subirlo al servidor entonces entrará en esta condición
							if(!move_uploaded_file($rutaProvisional[$i], $miCarpetaDeImagenes.'/'.$nomImage[$i])){
								$datos_devuelta = 'errorAlSubir';
							}
						}
					}
				}
			}
			Conexion::desconectar($con);		//cierro la conexión

			echo $datos_devuelta;
		}

		
 ?>