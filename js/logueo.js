/* Archivo para controlar el logueo de los usuarios*/

$(document).ready(function(){

	$('#login #logear_user').click(function(event){
		//primero quitaré el transcurso de eventos
		event.preventDefault();
                //una vez hecho esto, esconderé los mensajes de error por si el caso hayan aparecido
                $('#loglabel_empty').css('display','none');
                $('#no_existe').css('display','none');
		//una vez hecho comprobaré si los campos estan vacios
		var vacios  = false; // pongo que de primeras esta falso porque supongo que el usuario ha escrito sus datos
		$("#login").find(':input').each(function(){
			if(this.type != 'submit'){
				if(this.value== ''){
					this.focus();
					vacios = true;
                                }
			}
                });
                if(!vacios){	//si los campos estan rellenados, procedo a loguearme
                        //recojo los valores que el usuario ha introducido
        			var nombreUser = $('#login .nomUser').val();
        			var clave = $('#login .password').val();
                	$.ajax({
                		type: 'POST',
                		url: './pages/loguearUsuarios.php',
                		async: true,
                		data: 'entrar=si&nomUser='+nombreUser+'&clave='+clave,
                		success: function(respuesta){
                			if(respuesta == 'ok'){
                			     window.location = './index.php';
                			}else{
                			     $('#no_existe').css('display','block');
                			}
                		}
                	});
                }else{
                        $('#loglabel_empty').css('display','block');
                }
	});
        //espacio para otros métodos



});