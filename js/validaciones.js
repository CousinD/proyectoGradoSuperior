
function validarEmail(email){
	var permitido = true;		//variable que contendrá el valor de si esta permitido o no
	//el valor del email lo obtengo como parametro
	var expReg = /^[^@\s]+@[^@\.\s]+(\.[^@\.\s]+)+$/;
	//explicación  --> al principio cualquier caracter menos @, porque tiene que haber texto antes de poner @
	//				   después va seguido el @ y seguido de ninguno de los caracteres que hay despues de este carecter ([^@\.\s])
	if(!expReg.test(email)){
		permitido = false;
	}
	return permitido;
}


function validarNombre(nombre){
	var permitido = true;		//variable que contendrá el valor de si esta permitido o no
	//el valor del nombre lo obtengo como parametro
	var expReg = /^[A-ZÁÉÍÓÚÑ][a-záéíóúñü]+([\s-][A-ZÁÉÍÓÚÑ][a-záéíóúñü]*)*$/;
	//explicación  --> acepta los caracteres desde la a hasta la z (incluyendo mayúsculas y espacios), acepta los caracteres: ñ-á-é-í-ó-ú 
	//				   y valida que el nombre tenga de 2 a 60 caracteres
	if(!expReg.test(nombre)){
		permitido =  false;
	}

	return permitido;
}

function validarApellidos(apellidos){
	var permitido = true;		//variable que contendrá el valor de si esta permitido o no
	//el valor del apellidos lo obtengo como parametro
	var expReg = /^[A-ZÁÉÍÓÚÑ][a-záéíóúñü]+([\s-][A-ZÁÉÍÓÚÑ][a-záéíóúñü]*)*$/;
	//explicación  --> acepta los caracteres desde la a hasta la z (incluyendo mayúsculas y espacios), acepta los caracteres: ñ-á-é-í-ó-ú 
	//				   y valida que el nombre tenga de 2 a 60 caracteres
	if(!expReg.test(apellidos)){
		permitido = false;
	}

	return permitido;
}

function validarNombreUsr(nomUser){
	var permitido = true;		//variable que contendrá el valor de si esta permitido o noes correcto
	//el valor del nombreUsr lo obtengo como parametro
	var expReg = /^[a-z\d_]{4,15}$/i;
	//explicación  --> me permite caracteres de A-z entre 4 y 15 - no admite espacios en blanco
	if(!expReg.test(nomUser)){
		permitido = false;
	}

	return permitido;
}

function validarPassword(clave){
	var permitido = true;		//variable que contendrá el valor de si esta permitido o noes correcto
	//el valor del nombreUsr lo obtengo como parametro
	// var expReg = /^(?=^.{6,}$)((?=.*[A-Za-z0-9])(?=.*[AZ])(?=.*[az]))^ . * $/;
	var expReg = /(^(?=.*[a-z])(?=.*[A-Z])(?=.*\d){6,20}.+$)/;
	//explicación  --> La contraseña que tenga por lo menos una letra en mayúscula, una letra en minúscula y un número y que su longitud sea entre 6 y 20 caracteres.
	//					Esto es para asegurarnos que la contraseña sea segura.
	if(!expReg.test(clave)){
		permitido = false;
	}

	return permitido;
}

function validrExtensionFile(imagen){
	var permitido = false;		//variable que me confirmará si el archivo que selecciono esta permitido o no - suponremos que no esta permitio aun la imagen
	//hago un array con las extensiones permitidas, para la subida de imagenes
	var img_permitidas = new Array('.png','.jpg','.ico');
	var extension = imagen.substring(imagen.lastIndexOf("."));		//cojo la parte que va despues del punto del nombre de la imagen
	for(var i=0;i<img_permitidas.length;i++){
		if(img_permitidas[i] == extension)
			permitido = true;		//porque ha encontrado una coincidencia con las posibles extensiones que tenia en mi array
	}

	return permitido;
}