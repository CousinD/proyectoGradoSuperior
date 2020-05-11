//este será mi archivo JS que contendrá la mayor parte de funcionalidades que le vaya a dar a mi proyecto de cara a la programación al cliente
$(document).ready(function(){

/************** ESPACIO PARA MIS VARIABLES GLOBLES */

var condicionErrorFotosNoPermitidas = true;  // mi variable global para controlar si la imagen que intento subir cuando vaya a participar en el concurso es admitida en dicho concurso - de primeras es valido
var imagenNoAdmitida = false;		//de primeras todas mis imagenes son admitidas
var sinImagenesFormulario = true;	 //suponemos que aun no ha seleccionado ninguna imagen	


/***************************************************/

	$('#offSession').click(function(event){		//función para cerrar Sessión
		event.preventDefault();		//porque es un enlace y me saltaba su evento
		//cuando haga click en el enlace de cerrar session me desconecte el usuario
		$.ajax({
			type: 'POST',
			url:'./pages/loguearUsuarios.php',
			async: true,
			data:{cerrar:1},	//con pasarle este parametro sirve ya que en mi archivo php entrará simpre que el cerrar este definido
			success: function(respuesta){
				if(respuesta == 'ok'){
					window.location = 'index.php';
				}
			},
			error: function(respuesta){
			}
		});
	});

	$('#enfocarRegis').click(function(event){		//si selecciono el enlace para obtener una cuenta enfocaré el formulario de registro
		event.preventDefault();
		$('#registro .nomUser').focus();
		//y escondo el mensaje de 'obtener cuenta'
		$('#no_existe').css('display','none');
	});

	$('#inputSearch').autocomplete({
		source:function(request,response){
			$.ajax({
				url: './pages/infoBD_global.php',
				dataType: 'json',
				data: {q:request.term},
				success: function(data){
					response(data);
				}
			});
		},
		minLength:1,
		select: function(event,ui){
			$.ajax({
				type: 'POST',
				url: './pages/infoBD_global.php',
				async: true,
				data: {buscarConcursos: 1,concursoNom:ui.item.label},
				success: function(idExp){
					window.location = 'index.php?p=verConcurso&exp='+idExp;
				},
				error: function(){
					alert('Algo ha ido mal - puede que el concurso ya no exista');
				}
			});
		}
	});

	function ocultarMensajes(){
		$(".msgCorrect").css('display','none');
		$(".msgErrores").css('display','none');
	}

	function resetearFormulario(nombreDelForm){
		document.getElementById(nombreDelForm).reset();
	}

	/* A PARTIR DE AQUÍ MI SCRIPT PARA LA CABECERA DE LAS PÁGINAS */
	$('.submenu').click(function(){
		$(this).children('ul').slideToggle();
	});

	$('.ul').click(function(p){
		p.stopPropagation();
	});

	/* A PARTIR DE AQUÍ MI SCRIPT PARA LA PÁGINA CREAR CONCURSO */

	/**********************************************************************************/
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar',
		prevText: '<Ant',
		nextText: 'Sig>',
		currentText: 'Hoy',
		monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
		dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['es']);
	/***********************************************************************************/
	$(function (){		//llamo a mi función que me muestra el calendario compatible con todos los navegadores
		var fechaActual = new Date();
		//creando una variable de tipo Fecha y poniendo que la fecha minima sea la que le indique, me aseguro que el usuario no pueda elegir una fecha anterior a la fecha actual y ademas los concursos minimo tendrán 10 días desde la fecha actual
		$('#limiteNuevoConcurso').datepicker({
			 minDate: new Date(fechaActual.getFullYear(), fechaActual.getMonth(), fechaActual.getDate() + 10)
		});
	});

	$('#selectPortada').hover(function(){		//estas funciones es para que tengo el mismo efecto el div entero, como cuando tengo el foco en el boton de definir portada
		$(this).find('button').fadeIn();
	},function(){
		$(this).find('button').fadeOut();
	});

	function _(element){		//porque si no hago esta función no podre recoger el valor de mi input file desde otra función que no sea en el momento que yo elijo mi imagen
		return document.getElementById(element);
	}

	$('#selectImgPortada').click(function(e){
		e.preventDefault(); 	//sino, cada vez que clickee el botón saltará el evento que tiene por defecto
		//entonces lo que tengo que hacer es enlazar este botón con mi input file que habiamos escondido anteriormente
		$('#elegirImagenPortada').click().change(function(e){
			var file = this.files[0].name; 		//de esta forma me aseguro de que me salga solamente el nombre del archivo - distintos navegadores
			var typeFile = this.files[0].type.substring(0,5);		//desde el inicio hasta todo la palabra 'image'
			if(typeFile != 'image'){		//si no he seleccionado ningún archivo entrará en esta condición
				$('.msgErrores').css('display','block');
				$('.msgErrores').text('El tipo de archivo seleccionado no es una imagen');
			}else{
				var tamPhoto = this.files[0].size;
				var formatoPhoto = this.files[0].type.substring(6,this.files[0].type.lenght);
				//debo comprobar si la imagen que subimos tiene el tamaño y formato permitido - 1000000 bytes = 1MB
				if(tamPhoto <= 1000000 && formatoPhoto == 'jpeg'){
					$('#infoImage').text(file);
					//para que la imagen se visualize también temporalmente debo crear un variable del objeto FileReader y así mostrarla
					var viewImage = new FileReader();
					viewImage.onload = function(e){		//cuando la imagen este cargada muestramela en el momento
						$('#selectPortada img').attr('src',e.target.result);
					}
					viewImage.readAsDataURL(this.files[0]);
					ocultarMensajes();

				}else{
					$('.msgErrores').css('display','block');
					$('.msgErrores').text('Tamaño max. 1MB y formato admitido jpeg/jpg');
				}	
			}
		});
	});



	$('#nuevoConcursoForm #validarCreaccion').click(function(event){
		// quito el evento que tiene por defecto
		event.preventDefault();
		//creo una variable error para que cuando encuentre algún error me muestre el mensaje 
		var dataError = false;		
		// ahora recogeré los valores que tenga en mis inputs - para la fecha de finalización del concurso debo hacer un evento con jquery porque algunos navegadores no soportan el input type date
		var nombreNuevoConcurso = $('#nombreNuevoConcurso').val();
		var temaNuevoConcurso = $('#temaNuevoConcurso').val();
		var fechaLimiteConcurso = $('#limiteNuevoConcurso').val();
		
		var tmpImage = _('elegirImagenPortada').files[0];
		var formDataConcurso = new FormData();
		formDataConcurso.append('elegirImagenPortada',tmpImage);

		if($('#infoImage').text() != 'No hay imagen aún'){		//si es distinto del mensaje inicial que tenemos cuando aún no tenemos ninguna imagen entonces porque he definido la imagen
			var nombreImagePortada = $('#infoImage').text();		//debemos recoger el valor con .text() porque es la manera que tiene jQuery de recoger el valor de las etiquetas que no sea inpust, ni textarea, etc..
		}else{
			dataError = true;
		}

		var numPhotosPermitidas = $('#numFotosNuevoConcurso').val();

		var formatoNuevoConcurso = $('#formatoNuevoConcurso').val();
		if(formatoNuevoConcurso == ''){
			dataError = true;
		}
		var breveDescripcion = $('#descripcionNuevoConcurso').val();

		if(nombreNuevoConcurso.trim() == '' || temaNuevoConcurso.trim() == '' || fechaLimiteConcurso == '' || formatoNuevoConcurso == '' || breveDescripcion.trim() == '' || dataError){
			$('.msgErrores').css('display','block');
			$('.msgErrores').text('Hay algún campo vacio');
		}else{
			ocultarMensajes();		//oculto de nuevo el mensaje de error, por si había saltado en algún momento del proceso
			$.ajax({
				type: 'POST',
				url: './pages/uploadFile.php',
				async: true,
				data: formDataConcurso,
				processData: false,
				contentType: false,
				success: function(subida){
					if(subida == 'ok'){
						$.ajax({
							type: 'POST',
							url: './pages/infoBD_global.php',
							async: true,
							data: {crearConcurso:1,tituloConcurso:nombreNuevoConcurso,enfoque:temaNuevoConcurso,finConcurso:fechaLimiteConcurso,portada:nombreImagePortada,numFotos:numPhotosPermitidas,formato:formatoNuevoConcurso,descripcion:breveDescripcion},
							success: function(respuesta){
								if(respuesta == 'ok'){
									resetearFormulario('nuevoConcursoForm');
									//lo siguientes valores los tengo que cambiar así porque no entran dentro del método reset()
									$('#elegirImagenPortada').val = '';
									$('#infoImage').text('No hay imagen aún');
									$("#selectPortada img").attr("src","./img/frontPage/defaultPortada.svg");

									$('.msgCorrect').css('display','block');
									$('.msgCorrect').text('El concurso se ha creado correctamente');	//muestro un mensaje de que todo ha salido bien y borro los campos
								}else{
									$('.msgErrores').css('display','block');
									$('.msgErrores').text('Se ha producido un error al crear el concurso');
								}
							}
						});
					}else{
						$('.msgErrores').css('display','block');
						$('.msgErrores').text('La imagen que ha elegido ya se encuentra definida como portada. Elija otra imagen');
					}
				}
			});
		}		
	});


	/* A PARTIR DE AQUÍ MI SCRIPT PARA LA PÁGINA INFORMACIÓN DEL PERFIL */
	$('#fotoPerfil').hover(function(){		//estas funciones es para que tengo el mismo efecto el div entero, como cuando tengo el foco en el boton de definir portada
		$(this).find('button').fadeIn();
	},function(){
		$(this).find('button').fadeOut();
	});


	$('#btnChangeImgPortada').click(function(e){		//función para cambiar la imagen al momento pero no subida en el sevidor
		e.preventDefault(); 	//sino, cada vez que clickee el botón saltará el evento que tiene por defecto
		//entonces lo que tengo que hacer es enlazar este botón con mi input file que habiamos escondido anteriormente
		$('#changeImgProfile').click().change(function(){
			ocultarMensajes();		//para que quite el msg si lo hubiese
			var file = this.files[0].name; 		//de esta forma me aseguro de que me salga solamente el nombre del archivo - distintos navegadores
			if(file != ''){
				var tamPhoto = this.files[0].size;
				var formatoPhoto = this.files[0].type.substring(6,this.files[0].type.lenght);
				//debo comprobar si la imagen que subimos tiene el tamaño y formato permitido - 100000 bytes = 100KB
				if(tamPhoto <= 100000 && formatoPhoto == 'jpeg'){
					//para que la imagen se visualize también temporalmente debo crear un variable del objeto FileReader y así mostrarla
					var viewImage = new FileReader();
					viewImage.onload = function(e){		//cuando la imagen este cargada muestramela en el momento
						$('#fotoPerfil img').attr('src',e.target.result);
					}
					viewImage.readAsDataURL(this.files[0]);

				}else{
					$('.msgErrores').css('display','block');
					$('.msgErrores').text('Tamaño max. 1KB y formato admitido jpeg/jpg');
				}	
			}
		});
	});

	$('#verPerfil #modInfoProfile').click(function(e){		//función para modificar cambios en el perfil
		ocultarMensajes();		//para que quite el msg si lo hubiese
		e.preventDefault();		//para que no salte el evento del formulario
		//lo primero que hago es recoger los valores que hayan en los inputs
		var newNomUser = $('#userNameProfile').val();
		var newNameUser = $('#nameProfile').val();
		var newSubNameUser = $('#subNameProfile').val();
		var newNameImgProfile = '';		//de primeras no tendrá valor luego comprobaré si he seleccionado alguna imagen

		if(newNomUser == ''){		//pondre el unico campo obligatorio el del nombre de perfil ya que es el unico que no puedo dejar vacio
			$('.msgErrores').css('display','block');
			$('.msgErrores').text('El campo del nombre de usuario es obligatorio');
			$('#userNameProfile').focus();
		}else{
			var correcto = true;
			//si no estan vacios estos campos entonces valido que sean correctos
			if(!validarNombreUsr(newNomUser)){		//valido el nombre de usuario
				correcto = false;
			}

			if(correcto){		//es porque los campos están admitidos
				ocultarMensajes();		//oculto de nuevo el mensaje de error, por si había saltado en algún momento del proceso
				//primero me ocupo de subir la imagen al servidor - siempre que haya elegido el usuario
				var tmpImageProfile = _('changeImgProfile').files[0]; //recojo el fichero que haya en la carpeta temporal para la imagen
				if(typeof tmpImageProfile != 'undefined'){ //solo se lo asignará a un objeto formData siempre que no se indefinido
					newNameImgProfile = tmpImageProfile.name;
					var formDataProfile = new FormData();
					formDataProfile.append('changeImgProfile',tmpImageProfile);
					$.ajax({
						type: 'POST',
						url: './pages/uploadFile.php',
						async: true,
						data: formDataProfile,
						processData: false,
						contentType: false,
						success: function(subidaProfile){
							if(subidaProfile == 'errorSubir'){
								$('.msgErrores').css('display','block');
								$('.msgErrores').text('Ha ocurrido un error al subir la imagen, intentelo de nuevo');
							}else if(subidaProfile == 'noPermitido'){
								$('.msgErrores').css('display','block');
								$('.msgErrores').text('Tamaño max. 1KB y formato admitido jpeg/jpg');
							}
						}
					});
				}
				//si no ha elegido ninguna imagen solamente procederé a actualizar los nuevos datos que haya introducido el user
				$.ajax({
					type: 'POST',
					url: './pages/infoBD_global.php',
					async: true,
					data: {modificarUser:1,modNombreUser:newNomUser,modNombre:newNameUser,modApell:newSubNameUser,modPhotoProfile:newNameImgProfile},
					success: function(respuesta){
						if(respuesta == 'ok'){
							$('.msgCorrect').css('display','block');
							$('.msgCorrect').text('Los cambios han sido realizados');	//muestro un mensaje de que todo ha salido bien
						}else{
							$('.msgErrores').css('display','block');
							$('.msgErrores').text('Se ha producido un error al realizar los cambios');
						}
					}
				});
			}else{
				$('.msgErrores').css('display','block');
				$('.msgErrores').text('El nombre de usuario o el email no son validos');
			}
		}

	});

	

	/*  A PARTIR DE AQUÍ REALIZARÉ EL SCRIPT PARA LA PÁGINA DE TODOS LOS CONCURSOS */

	//y le voy a indicar que para la paginación si tiene la clase activa que cambie su comportamiento
	$('#detenerEvento').click(function(e){
		e.preventDefault();
	});


	/* A PARTIR DE AQUÍ REALIZARÉ EL SCRIPT PARA LA PÁGINA DE INTERACCIÓN DE LOS CONCURSOS */
	$('#btnInscripcion').click(function(){		//para incribirse en un concurso
		ocultarMensajes();
		//función para incribirme en el concurso
		//recojo el id del concurso que me voy a inscribir
		var idConcursoOculto = $('#idConcursoOculto').val();
		$.ajax({		//realizo la petición para hacer el insert
			type: 'POST',
			url: './pages/infoBD_global.php',
			async: true,
			data: {inscribirseConcurso: 1,id_concurso: idConcursoOculto},
			success: function(respuesta){
				if(respuesta == 'ok'){
					$('.msgCorrect').css('display','block');
					$('.msgCorrect').text('Acabas de inscribirte en el concurso');
					setInterval(function(){
						location.reload();
					},2000);	//envio los datos a los 2 segundos
				}else{
					$('.msgErrores').css('display','block');
					$('.msgErrores').text('Error al inscribirte');
				}
			}
		});

	});


	$('#btnPedirSerJurado').click(function(e){
		ocultarMensajes();
		e.preventDefault();
		//función para pedirle al admin ser Jurado
		//recojo el id del concurso que me voy a inscribir
		var idConcursoOculto = $('#idConcursoOculto').val();
		$.ajax({		//realizo la petición para hacer el insert
			type: 'POST',
			url: './pages/infoBD_global.php',
			async: true,
			data: {perdirSerJurado: 1,id_concurso: idConcursoOculto},
			success: function(respuesta){
				if(respuesta == 'ok'){
					location.href = 'index.php?p=serJurado&exp='+idConcursoOculto;
				}else{
					$('.msgErrores').css('display','block');
					$('.msgErrores').text('Error al pedir ser jurado');
				}
			}
		});

	});

	$('.btnParaPuntuar').attr('disabled', true); //de primeras los botones estarán desactivados


	$('.allPhotosConcurso [type=number]').change(function(inputTarget){
		//el siguiente for each dice que recorrerá todos los botones que tienen mis imagenes y que si
		//coincide su valor con el id (guardo en el input - id_imagen) habilitame el boton
		//ya que, solo los inputs que hayan sido cambiados podre puntuar
		$('.btnParaPuntuar').each(function(){
			//tengo que poner para coger el valor indice 0 ya que para cada elemento es como si cogiese un elemento nuevo por lo tanto '0'
			if($(this)[0].value == inputTarget.target.dataset['id']){
				$(this)[0].disabled = false;
			}
		});
	});


	$('.btnParaPuntuar').click(function(e){
		var valorTotal = 0;		//variable para obtener el valor que enviaré a mi BD
		var suma,resta = false;	//booleanos que me dirán si el jurado a cambiado su ult puntuacion y si ha incrementado o no su valoración
		var ultPuntuacion,nuevaPuntuacion = 0; 	//para recoger los valores de puntuacion
		$('.allPhotosConcurso [type=number]').each(function(){
			if($(this)[0].dataset['id'] == e.target.value){		//compruebo que el botón hace referencia al mismo input
				if($(this)[0].dataset['ultpun'] != undefined){	//es porque el jurado había puntuado y ahora esta cambiando su puntuación
					//recojo los valores
					ultPuntuacion = $(this)[0].dataset['ultpun'];
					nuevaPuntuacion = $(this)[0].value;
					if(nuevaPuntuacion < ultPuntuacion){ //porque ha puntuado otra vez pero con menos valoracion
						resta = true;
						//indico que es una resta y le resto el ultimo valor menos el nuevo y la diferencia es lo que le resto a la puntuacion actual
						valorTotal = ultPuntuacion - nuevaPuntuacion;
					}else if(nuevaPuntuacion > ultPuntuacion){ //porque ha puntuado otra vez pero con mas valoracion
						suma = true;
						//indico que es suma y le resto el nuevo menos el ultimo y la diferencia es lo que le tengo quer sumar a la puntuacion actual
						valorTotal = nuevaPuntuacion - ultPuntuacion;
					}else{
						//es porque ha apretado el boton puntuar - por lo tanto había cambiado el input - pero al final lo ha dejado como estaba
					    //explico el código - muestro el mensaje y recojo la posición donde se muestr y automaticamente la pantalla se mueve hacía ese mensaje
					    $("#mismosPuntos").css("display", "block").fadeOut(5000);
					    var scrollPos =  $("#mismosPuntos").offset().top;
 						$(window).scrollTop(scrollPos);
					}
				}else{		//es porque es la primera vez que puntua - entonces sabemos que va a ser una suma
					suma = true;
					valorTotal = $(this)[0].value;
				}
			}
		});
		//una vez que se los valores para cada condición veo a ver si es una suma o una resta
		if(resta || suma){		//quiere decir si alguna de las variables es true - compruebo cual es
			//por lo tanto, realizo la petición para que me actualice los datos
			if(resta){
				$.ajax({
					type: 'POST',
					url: './pages/infoBD_global.php',
					async: true,
					data: {cambiarPuntuacion:1,op_resta:1,idPhoto:e.target.value,valorTotal:valorTotal,valorCambiar:nuevaPuntuacion},
					success: function(respuesta){
						if(respuesta == 'ok'){
							$("#hasPuntuado").css("display", "block").fadeOut(3000);
						    var scrollPos =  $("#hasPuntuado").offset().top;
	 						$(window).scrollTop(scrollPos);
	 						//ahora una vez que ha mostrado el mensaje recargaré la página
	 						setInterval(function(){
								location.reload();
							},4000);		//a los 4 segundos cuando el mensaje que muestra desaparece cargo la página
						}else{
							alert('Ha ocurrido un error');
						}
					}
				});
			}else{
				$.ajax({
					type: 'POST',
					url: './pages/infoBD_global.php',
					async: true,
					data: {cambiarPuntuacion:1,op_suma:1,idPhoto:e.target.value,valorTotal:valorTotal,valorCambiar:nuevaPuntuacion},
					success: function(respuesta){
						if(respuesta == 'ok'){
							$("#hasPuntuado").css("display", "block").fadeOut(3000);
						    var scrollPos =  $("#hasPuntuado").offset().top;
	 						$(window).scrollTop(scrollPos);
	 						//ahora una vez que ha mostrado el mensaje recargaré la página
	 						setInterval(function(){
								location.reload();
							},4000);		//a los 4 segundos cuando el mensaje que muestra desaparece cargo la página
						}else{
							alert('Ha ocurrido un error');
						}
					}
				});
			}
		}
		
	});


	/* PARA SUBIR IMAGENES A LOS CONCURSOS */

	//voy a realizar una función para que cuando mi input file cambie - osea elija algún archivo - compruebe si es una imagen etc
	$('.fotosdelConcurso').change(function(e){ //entrará en esta condición siempre que elija algún archivo


		var formatoDelConcurso = $('#hiddenConFormato').text(); //de esta manera sabré si la imagen que he elegido es acceptada para ese concurso
		formatoDelConcurso = formatoDelConcurso.substring(formatoDelConcurso.indexOf(':')+2,formatoDelConcurso.length);

		

		var formatoImg = this.files[0].type.substring(this.files[0].type.indexOf('/')+1,this.files[0].type.length);
		var typeFile = this.files[0].type.substring(0,5);		//desde el inicio hasta todo la palabra 'image'
		
		if(typeFile != 'image' || formatoImg != formatoDelConcurso){		//si no he seleccionado ningún archivo entrará en esta condición
			$('#imagenNoAceptada').css('display','block');
			condicionErrorFotosNoPermitidas = false; //la imagen elegida no es compatible
		}else{
			//Esta condición esta por dos razones - la primera es porque de primeras mi variable valdrá false y si el archivo elegido no es compatible tengo que mostrar mensaje y el otro motivo es
			//porque puede ser que el usuario haya elegido una foto mal y en otro input bien, entonces solo lo cambiaré siempre que mi variable imagenNoadmitida sea 'false'
			if(!imagenNoAdmitida){
				condicionErrorFotosNoPermitidas = true; //dejaré enviar esta subida de imagen porque es compatible con el concurso
			}
			$('#imagenNoAceptada').css('display','none');
		}

		if(!condicionErrorFotosNoPermitidas){
			imagenNoAdmitida = recorrerImagenesElegidas(formatoDelConcurso);	//le paso como parametro el formato del concurso ya que luego lo tendré que comprobar en la condición
			if(imagenNoAdmitida){  //algunas de las imagenes no es admitida
				$('#elegirImgConError').css('display','block');
			}else{
				$('#elegirImgConError').css('display','none');
			}
		}


		if(sinImagenesFormulario){		//si es true es porque no hay imagenes pero como entra aqui es porque hemos seleccionado una imagen por narices entonces me cambia a sin imagenes a false y quito el mensaje
			sinImagenesFormulario = false;
			$('#enviarSinImg').css('display','none');
		}

	});

	$('#subirImagenes').click(function(event){
		//quitó el comportamiento que tiene por defecto el boton - en este caso lo que le he indicado en el formulari que es recgargar la misma página - lo indico con el action del formulario
		event.preventDefault();

		if(sinImagenesFormulario){	//es porque el usuario le ha dado a submit sin seleccionar ninguna imagen en el input
			$('#enviarSinImg').css('display','block');
		}else{
			//primero compruebo que las imagenes elegidas siguen las bases del concurso
			if(!imagenNoAdmitida){ //entrará siempre que las imagenes sean admitidas
				$('#elegirImgConError').css('display','none');
				$('#imgRepetidaEnPagina').css('display','none');
				//entonces como todo esta correcto realizaré mi subida de imagenes
				//lo primero que tengo que hacer es guardarme un un array todos los inputs file que encuentre en mi formulario
				var fileCollection;		//será mi array de mis imagenes
				fileCollection = guardarFilesEnMiArray();		//le envio todos los datos y los guarda en mi array 
				// voy a recoger el id del concurso que es el expediente, ya que luego lo necestaré
				var numExp = $('#expedienteDeLaPagina').val();
				fileCollection.append('idDelConcurso',numExp);
				var misInputsFiles = $('#inputFilePlugin input:file');
				//antes de subir las imagenes compruebo que no tengan el mismo nombre cuando vayan a subirlas
				boolMismaImg = comprobarNombreDeImagenes();
				if(!boolMismaImg){
					$.ajax({
						type: 'POST',
						url: './pages/uploadFile.php',
						async: true,
						data: fileCollection,
						processData: false,
						contentType: false,
						success: function(respuesta){
							if(respuesta == 'ok'){
								$('#imgRepetidaEnCarpeta').css('display','none');		//quito el mensaje - puede ser que se haya mostrado
								$('#errorAlSubir').css('display','none');
								//tengo que recoger el exp para que sepa el concurso en el que esta - he añadido un hidden en mi form
								//y entonces realizo una peticion ajx de tipo GET
								location.reload();
							}else if(respuesta == 'imgMismoNombre'){
								$('#imgRepetidaEnCarpeta').css('display','block');
							}else{
								$('#errorAlSubir').css('display','block');
							}
						},
						error: function(respuesta){
						}
					});
				}else{
					$('#imgRepetidaEnPagina').css('display','block');
				}
			}
		}		
	});

	function recorrerImagenesElegidas(formatoX){ //donde 'formatoX' es el formato para el concurso
		var algoVaMal = false; //suponemos algo esta mal porque ha entrado en esta opción
		var siEstaVacio = '';		//para los demás inputs que esten vacios
		var formatoCadaVuelta = ''; //variable para comprobar el nombre de cada input que voy a recorrer
		for($i=0;$i<$('#inputFilePlugin input:file').length;$i++){
			siEstaVacio = $('#inputFilePlugin input:file')[$i].value;
			if(siEstaVacio != ""){	//solo para los inputs que he elegido algo
				formatoCadaVuelta = $('#inputFilePlugin input:file')[$i].files[0].type;
				formatoCadaVuelta = formatoCadaVuelta.substring(formatoCadaVuelta.indexOf('/')+1,formatoCadaVuelta.length);				
				
				if(formatoCadaVuelta != formatoX){ //si es igual es porque las imagenes que hay son todas admitidas
					algoVaMal = true;
				}
			}
		}
		
		return algoVaMal;
	};

	function comprobarNombreDeImagenes(){
		var bool = false; //variable que nos dirá si hay imagenes con el mismo nombre
		var primeraImg = $('#inputFilePlugin input:file')[0].files[0].name;	//recojo las imagenes
		//para ello, recorro el nombre de mi priemer archivo del array y lo compruebo con el resto
		for($i=1;$i<$('#inputFilePlugin input:file').length;$i++){
			if($('#inputFilePlugin input:file')[$i].files[0] != undefined){	//puede ser que el usuario no haya elegido todas las imagenes que podia subir
				nombreOtrasImg = $('#inputFilePlugin input:file')[$i].files[0].name;
				if(nombreOtrasImg == primeraImg){
					bool = true;	//estoy intentando subir dos imagenes con el mismo nombre
				}
			}
		}

		return bool;
	}

	function guardarFilesEnMiArray(){
		var siEstaVacio = '';		//para ver si el input esta vacio - y también - para obtener el array a json
		var misInputsFiles = $('#inputFilePlugin input:file');

		var nombreDeMiConcurso = $('#nombreDelConcurso').val();
		var objetoFormData = new FormData();

		for($i=0;$i<misInputsFiles.length;$i++){
			siEstaVacio = misInputsFiles[$i].value;
			if(siEstaVacio != ""){	//solo para los inputs que he elegido algo
				//como quiero añadir varios archivos a mi objeto FormData tengo que ponerle en el nombre '[]' especificando que puede que hayan mas de uno
				objetoFormData.append('misImagenes[]',misInputsFiles[$i].files[0]);
			
			}
		}
		//al final de mi formData enviaré el nombre del concurso ya que me servirá para comprobar si existe la carpeta de las imagenes que voy a subir
		objetoFormData.append('nombreDelConcurso',nombreDeMiConcurso);
		return objetoFormData;
	}



	/*  A PARTIR DE AQUÍ REALIZARÉ EL SCRIPT PARA LA PÁGINAS DE ENVIO DE CORREOS */

	/****************************************** FUNCIONES PARA PÁGINA DE CONTACTO */
	$('#btnEnviarCorreoContacto').click(function(e){
		e.preventDefault();	//quito su comportamiento normal
		//compruebo que no haya nada vacio
		var vacios = false;
		$("#mailContacto").find(':input').each(function(){
			if(this.type != 'submit'){
				if(this.value== ''){
					this.focus();
					vacios = true;
	    	    }
			}
        });
		if(!vacios){	//envio el correo
			$('#contactoVacios').css('display','none');		//en el caso de que lo haya mostrado

			//recojo los datos de los inputs
			if($('#correoUserVisitante').val() != undefined){		//es porque erá un usuario visitante
				var correoRemitente = $('#correoUserVisitante').val();
			}else{
				var correoRemitente = $('#correoOcultoContacto').val();
			}
			var asuntoContacto = $('#asuntoContacto').val();
			var mensaje = $('#areaMensajeContacto').val();
			
			$.ajax({
				type: 'POST',
				url:'./pages/pagEnviarCorreos.php',
				async: true,
				data:{enviarCorreo:1,correoRemitente:correoRemitente,asuntoMensaje:asuntoContacto,mensajeCorreo:mensaje},
				success: function(respuesta){
					if(respuesta == 'ok'){
						$('#contactoSuccess').css('display','block');
						$('#contactoError').css('display','none');
						//una vez que se ha enviado correctamente limpio los inputs
						resetearFormulario('mailContacto');
						setInterval(function(){
							$('#contactoSuccess').css('display','none');
						},5000);
					}else{
						$('#contactoError').css('display','block');
					}
				},
				error: function(){
					$('#contactoError').html('Error al intentar conectar con la página que envia correos');
					$('#contactoError').css('display','block');
				}
			});

		}else{
			$('#contactoVacios').css('display','block');
		}

	});

	$('#mailContacto #correoUserVisitante').on('blur', function(event) {		//la función trata de avisar o ayudar al usuario de que posiblemente haya escrtito mal el email
	  	var dominiosAdmitidos = ['hotmail.com','hotmail.es', 'gmail.com', 'aol.com'];
		var topLevelDomains = ["com", "net", "org","es"];
	  	$(this).mailcheck({
		    domains: dominiosAdmitidos,              // optional
		    topLevelDomains: topLevelDomains,       // optional
		    suggested: function(element, suggestion) {
		      // Aquí aparecen las sugerencias si el dominio es incorrecto
		      $('#emailContactoNoValid').html("¿Quizás quisiste decir <b><i>" + suggestion.full + "</b></i>?");
		      $('#emailContactoNoValid').css('display','block');
		    },
		    empty: function(element) {
		      	//si no ha escrito nada en el campo email aparece este mensaje
		      	if(element[0].value == ''){
		      		$('#emailContactoNoValid').html("D'oh! No puedes dejar este campo vacio");
		    	}else{
		    		//si no ha dejado el campo vacio y ha escrito bien el dominio entrará aqui y procedremos a comporbarlo con la expresion regular
		    		var expReg = /^[^@\s]+@[^@\.\s]+(\.[^@\.\s]+)+$/;
					//explicación  --> al principio cualquier caracter menos @, porque tiene que haber texto antes de poner @
					//				   después va seguido el @ y seguido de ninguno de los caracteres que hay despues de este carecter ([^@\.\s])
					var email = $('#correoUserVisitante').val();
					if(!expReg.test(email)){
						$('#emailContactoNoValid').html("D'oh! Ese email es invalido");
					}else{
						$('#emailContactoNoValid').html('');		//por si en algún momento ha tenido que aparecer algún mensaje de error entonces así los quito
						$('#emailContactoNoValid').css('display','none');
					}
		    	}
		    }
	  	});
	});		/* fin de la ayuda para escirbir bien el email */

	/****************************** FIN PÁGINA DE CONTACTO */

	/***************************************** FUNCIONES PARA PÁGINA SOLICITAR SER JURADO */
	$('#btnFormSolicitudJurado').click(function(e){
		e.preventDefault();	//quito su comportamiento normal
		//compruebo que no haya nada vacio
		var vacios = false;
		$("#mailSolicitud").find(':input').each(function(){
			if(this.type != 'submit'){
				if(this.value== ''){
					this.focus();
					vacios = true;
	    	    }
			}
        });
		if(!vacios){	//envio el correo
			$('#solicitarJuradoVacios').css('display','none');		//en el caso de que lo haya mostrado

			//recojo los datos de los inputs
			var correoRemitente = $('#correSolicitudOculto').val();
			var asuntoSolicitudJurado = $('#asuntoContacto').val();
			var mensajeSolicitud = $('#areaMensajeContacto').val();
			
			$.ajax({
				type: 'POST',
				url:'./pages/pagEnviarCorreos.php',
				async: true,
				data:{enviarCorreo:1,correoRemitente:correoRemitente,asuntoMensaje:asuntoSolicitudJurado,mensajeCorreo:mensajeSolicitud},
				success: function(respuesta){
					if(respuesta == 'ok'){
						$('#solicirarJuradoSuccess').css('display','block');
						$('#solicirarJuradoError').css('display','none');
						//una vez que se ha enviado correctamente limpio los inputs
						resetearFormulario('mailSolicitud');
						setInterval(function(){
							$('#solicirarJuradoSuccess').css('display','none');
						},5000);
					}else{
						$('#solicirarJuradoError').css('display','block');
					}
				},
				error: function(){
					$('#solicirarJuradoError').html('Error al intentar conectar con la página que envia correos');
					$('#solicirarJuradoError').css('display','block');
				}
			});

		}else{
			$('#solicitarJuradoVacios').css('display','block');
		}

	});

	/****************************** FIN PÁGINA DE SOLICITUD SER JURADO */

});