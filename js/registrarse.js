//Este archivo será mi JS para controlar el registro de usuarios
$(document).ready(function(){

	var domains = ['gmail.com', 'aol.com'];		//estos van a ser los dominios que yo implemento
	var secondLevelDomains = ['hotmail'];		//este será mi dominio 'base' por así decirlo ya que es el más común a priori
	var topLevelDomains = ["com", "net", "org","es"];
	$('#email').on('blur', function(event) {		//la función trata de avisar o ayudar al usuario de que posiblemente haya escrtito mal el email
	  	$(this).mailcheck({
		    domains: domains,
		    secondLevelDomains: secondLevelDomains,
		    topLevelDomains: topLevelDomains,
		    suggested: function(element, suggestion) {
		      // Aquí aparecen las sugerencias si el dominio es incorrecto
		      // console.log("suggestion ", suggestion.full);
		      $('.suggestion').html("¿Quizás quisiste decir <b><i>" + suggestion.full + "</i></b>?");
		      $('#suggestion').css('display','block');
		    },
		    empty: function(element) {
		      	//si no ha escrito nada en el campo email aparece este mensaje
		      	if(element[0].value == ''){
		      		$('.suggestion').html("D'oh! No puedes dejar este campo vacio");
		    		$('#suggestion').css('display','block'); //muestro el bloque con el mensaje
		    	}else{
		    		//si no ha dejado el campo vacio y ha escrito bien el dominio entrará aqui y procedremos a comporbarlo con la expresion regular
		    		var expReg = /^[^@\s]+@[^@\.\s]+(\.[^@\.\s]+)+$/;
					//explicación  --> al principio cualquier caracter menos @, porque tiene que haber texto antes de poner @
					//				   después va seguido el @ y seguido de ninguno de los caracteres que hay despues de este carecter ([^@\.\s])
					var email = $('#email').val();
					if(!expReg.test(email)){
						//hago que aparezca el bloque y muestro el mensaje
						$('#suggestion').css('display','block');
						$('.suggestion').html("D'oh! Ese email es invalido");
					}else{
						$('.suggestion').html('');		//por si en algún momento ha tenido que aparecer algún mensaje de error entonces así los quito
						$('#suggestion').css('display','none'); //como esta correcto escondo el div
					}
		    	}
		    }
	  	});
	});		/* Fin de mi función para la ayuda de posibles equivocaciones cuando el user vaya escribir el correo */ 

	$('#registro #registrar_user').click(function(event){		//función para controlar el registro de un usuario cuando haga click en el botón de registrar
		event.preventDefault();		//para el que el evento no se propague en caso de fallo
		//compruebo que no tenga los campos vacios, recorro todos los inputs que encuentre en mi form
		var vacios = false;
		$("#registro").find(':input').each(function(){
			if(this.type != 'submit'){
				if(this.value== ''){
					this.focus();
					vacios = true;
	    	    }
			}
        });
        if(!vacios){		//si no están vacios recojo el sus valores y entonces llamo a mi función validar campos
			var nombreUser = $('#registro .nomUser').val();
			var correo = $('#email').val();
			var clave = $('#registro .password').val();
			var claveDos = $('#passdos').val();
			// var key_captcha = $('#captcha').val(); //será el código captcha que creemos
			var captcha = $('#val_captcha').val();
			$('#registro .alert-danger').css('display','none'); //para que desaparezcan mis mensajes de error
			if(validarCampos(nombreUser,correo,clave,claveDos)){
				//si los campos son correctos entonces procedré a realizar el alta del usuario
				$.ajax({	//de esta forma hago una petición para introducir los valores a mi BD
					type: 'POST',	//como voy a pasar los datos 
					url: './pages/registroUsuarios.php',		//al fichero que quiero hacer la petición
					async: true,		//la peticion es asincrona
					data: 'correo='+correo+'&nomUsr='+nombreUser+'&clave='+clave+'&captcha='+captcha,
					//cuando se haya hecho la petición hacemos que salte la función para reedirigirnos a la página pero registrados o mostrar mensaje de error si hubiesen
					success: function(respuesta){
						if(respuesta == 'correcto'){		//si hemos registrado al usuario entonces llamaremos a nuestra página principal y tendrá privilegios para inscribirse en el concurso
							window.location = './index.php';
						}else{
							if(respuesta == "userRepetido"){
								$('#user_repetido').css('display','block');
							}else if(respuesta == 'emailEncontrado'){
								$('#mail_repetido').css('display','block');
							}else if(respuesta == 'failCaptcha'){
								$('#error_captcha').css('display','block');
							}
						}
					}
				})
			}
		}else{
			$('#camposVacios').css('display','block'); //aparece el mensaje de que hay campos vaciós
			$('#registro .nomUser').focus(); // apunto al primer campo del formulario
		}
	});



	function validarCampos(nombreUser,correo,clave,claveDos){
		var correcto = true;		//de primeras todos los campos supondremos que son correctos
		//llamaré a cada funcion de validaciones.js para que valide los campos y devuelva true o false
		//si en la funcioes devuelve false es porque eese campo es invalido por lo tanto muestro su mensaje de error

		if(!validarNombreUsr(nombreUser)){		//valido el nombre de usuario
			correcto = false;
		}
		if(!validarEmail(correo)){		//valido el correo
			correcto = false;
		}
		// if(!validrExtensionFile(imagen) && imagen != ""){		//valido si la extensión del archivo que he seleccionado esta permitida, siempre y cuando haya seleccionado algún archivo
		// 	$('#falloExtension').css('display','block');
		// 	correcto = false;
		// }

		if(clave != claveDos){		//si las contraseñas son distintas directamente muestro el error no validaré nada
			$('#passDistinto').css('display','block');
			correcto = false;
		}else{
			if(!validarPassword(clave)){
				correcto = false;		//lo pongo dentro de aqui porque si no lo pusiese siempre valdria al final false la variable correcto, porque siempre va a entrar en esta condición(antes lo tenia fuera de esta condición)
				$('#passWrong').css('display','block');
			}
		}

		return correcto;
	}
});