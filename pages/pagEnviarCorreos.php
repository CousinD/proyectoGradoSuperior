<?php 
	
	//archivo que recogerá información para cuando vaya a enviar un correo
	require('../lib/phpMailer/class.phpmailer.php');
	require('../lib/PHPMailer/PHPMailerAutoload.php');

	if(isset($_POST['enviarCorreo'])){		// entra aqui porque he apretado el boton de enviar el correo
		$correoEnviado = 'ok';		//supondremos que todo será enviado correctamente
		//recojo los valores que he enviado
		$correoRemitente = $_POST['correoRemitente'];
		$asunto = $_POST['asuntoMensaje'];
		$mensaje = $_POST['mensajeCorreo'];
		
		$email = new PHPMailer(); // creo mi variable de la clase PHPMailer

		//como el destinatario siempre será el mismo que es el admin de la página web pues puedo dejarlo así y me ahorro una petición más para recoger el correo del admin
		$emailDestino = "dario.blasco.ladevesa@gmail.com";


		//Esta es mi confirguración para e PHPMAILER 
		$email-> IsSMTP();		//es SMTP para enviar el correo
		// $email -> SMTPDebug = 1; //para ver las cosas que van ocurriendo - errores,etc
		$email-> Host = "smtp.gmail.com";  // Especifico el dominio donde van a recibir el correo
		$email-> SMTPAuth = true; // que este disponible la autenticación con SMTP
		$email-> Username = 'dario.blasco.ladevesa@gmail.com';                 // SMTP username
		$email-> Password = 'lpoikj369';                           // SMTP password
		$email-> SMTPSecure = 'ssl'; 
		$email-> Port = 465;		//el puerto de gmail

		// /* Esta será mi configurción para enviar el correo */
		$email-> setFrom($correoRemitente,'Imagentender');
		$email-> AddReplyTo($correoRemitente,"Imagentender");
		$email-> addAddress($emailDestino); // El atributo addAddress tengo que colocar el correo a quien va dirigido - destinatario

		$email-> Subject = $asunto;
		$email-> Body = $mensaje;


		if(!$email->send()) {
			$correoEnviado = 'no';
			// echo $email->ErrorInfo;
		}

		echo $correoEnviado;
		
	}



?>