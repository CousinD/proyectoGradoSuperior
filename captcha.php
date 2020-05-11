<?php 
	/* ANTES DE COMENZAR A REALIZAR LA CREACIÓN PARA EL CAPTCHA DEBO DECIR QUE HE UTILIZADO LA LIBRERÍA GD DE PHP, PARA PODER IMPORTAR IMAGENES Y TRABAJAR CON ELLAS */
	session_start();
	// Indicamos el tamaño de nuestro captcha, puede ser aleatorio para mayor seguridad. Para reocger un valor que cambia y no es constante puede servirnos la fecha y la hora del servidor
	//para poder recoger estos valores utilizaremos las funciones microtime() y time() y lo cifraremos con la función md5()
	$captchaTextSize = 7; //indicar que mi captcha será de 7 carácteres
	do {
	// Generamos un string aleatorio y lo encriptamos con md5
	$md5Hash = md5( microtime( ) * time( ) );
	// Eliminamos cualquier caracter extraño
	preg_replace( '([1aeilou0])',"", $md5Hash );
	} while( strlen( $md5Hash ) < $captchaTextSize );
	// necesitamos sólo 7 caracteres para este captcha
	$key = substr( $md5Hash, 0, $captchaTextSize );
	// Guardamos la clave en la variable de sesión. La clave esta encriptada.
	$_SESSION['key'] = md5($key);

	//una vez que tenemos la clave la juntamos con nuestra imagen captcha
	//guardamos la imagen en la variable llamando la función de la lib gd que importa imágenes
	$imagenCaptcha = imagecreatefrompng('./img/captcha.png');
	/*
	Seleccionamos un color de texto. Cómo nuestro fondo es un azul agua junto con letras grises, escogeremos un cólor verdoso agua para el texto. El color del texto es, preferentemente, el mismo que el del background, aunque un poco más oscuro para poder distnguirlo.
	*/
	$colorTexto = imagecolorallocate( $imagenCaptcha, 31, 118, 92 );
	/*
	Seleccionamos un color para las líneas que queremos se dibujen en nuestro captcha. En este caso usaremos una mezcla entre verde y azul
	*/
	$colorLinea = imagecolorallocate( $imagenCaptcha, 15, 103, 103 );

	/* Una vez que tenemos los colores y la imagen entonces añadimos unas lineas de por medio para aumentar la dificultad */
	// recuperamos el parametro tamaño de imagen
	$imgTam = getimagesize('./img/captcha.png');
	// decidimos cuantas líneas queremos dibujar
	$numLineas = 10;
	// Añadimos las líneas de manera aleatoria
	for( $i = 0; $i < $numLineas; $i++ ) {
		// utilizamos la función mt_rand()
		$xStart = mt_rand( 0, $imgTam[ 0 ] );
		$xEnd = mt_rand( 0, $imgTam[ 0 ] );
		// Dibujamos la linea en el captcha
		imageline($imagenCaptcha, $xStart, 0, $xEnd, $imgTam[1], $colorLinea);
	}

	/* LLegamos al paso de escribir nuestro clave dentro de la imagen del captcha para ello utilizaremos la función 'imagettftext() */
	//tendré que descargarme una fuente para insertarla en la imagen
	imagettftext($imagenCaptcha, 20, 0, 35, 35, $colorTexto, './fonts/Pacifico.ttf', $key);

	// Mostramos nuestra imagen. Preparamos las cabeceras de la imagen previniendo que no se almacenen en la cache del navegador
	header ('Content-type: image/png');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Fri, 19 Jan 1994 05:00:00 GMT');
	header('Pragma: no-cache');
	imagepng( $imagenCaptcha );
?>