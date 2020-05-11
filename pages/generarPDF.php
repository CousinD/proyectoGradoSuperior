<?php
	//en esta página generamos un pdf para nuestra web - se descargará cuando nosotros nos registremos en un concurso
	ob_end_clean();
	require_once('./clases/conexion.php');	//importa para realizar un select del usuario que esta viendo esto
	require('./lib/FPDF/fpdf.php');

	/* Cuando importo los ficheros que me hace falta comienzo a hacer el select para recoger los datos */
	$sql="Select Username, Nombre, Apellidos, email, rango_user, imageProfile from usuarios where id_user=".$_SESSION['id'];
	$conexion = conexion::conectar();
	$res = $conexion->query($sql);
	if($res->num_rows>0){		//porque ha encontrado el usuario
		$infoUser = $res -> fetch_assoc();	//recojo los valores del usuario
	}

	Conexion::desconectar($conexion);

	/*Una vez que recojo los datos creo mi pdf y le doy forma con los datos que tengo */
	$pdf = new FPDF();
	$pdf->AddPage();
	if($infoUser['imageProfile'] == ''){		//porque aún no se ha puesto una foto de perfil
		$infoUser['imageProfile'] = 'imgDefault.png';
	}
	 
	$pdf -> Image('./img/profile/'.$infoUser['imageProfile'],60,45,75,75);
	$pdf->Ln(80);
	/* Muestro una tabla con toda la información */
	/* Primera Fila de Celdas */
	$pdf->SetFont('Times','B',18);
	$pdf->Cell(95,15,'Nombre de usuario',0,0,'C');
	$pdf->SetFont('Courier','I',16);
	$str = iconv('UTF-8', 'windows-1252',$infoUser['Username']);
	$pdf->Cell(95,15,$str,0,0,'C');
	$pdf->Ln();

	/* Segunda Fila de Celdas */
	$pdf->SetFont('Times','B',18);
	$pdf->Cell(95,15,'Nombre',0,0,'C');
	$pdf->SetFont('Courier','I',16);
	$str = iconv('UTF-8', 'windows-1252',$infoUser['Nombre']);
	$pdf->Cell(95,15,$str,0,0,'C');
	$pdf->Ln();

	/* Tercera Fila de Celdas */
	$pdf->SetFont('Times','B',18);
	$pdf->Cell(95,15,'Apellidos',0,0,'C');
	$pdf->SetFont('Courier','I',16);
	$str = iconv('UTF-8', 'windows-1252',$infoUser['Apellidos']);
	$pdf->Cell(95,15,$str,0,0,'C');
	$pdf->Ln();

	/* Cuarta Fila de Celdas */
	$pdf->SetFont('Times','B',18);
	$pdf->Cell(95,15,'Correo electronico',0,0,'C');
	$pdf->SetFont('Courier','I',16);
	$str = iconv('UTF-8', 'windows-1252',$infoUser['email']);
	$pdf->Cell(95,15,$str,0,0,'C');
	$pdf->Ln();

	/* Quinta Fila de Celdas */
	$pdf->SetFont('Times','B',18);
	$pdf->Cell(95,15,'Rango de usuario',0,0,'C');
	$pdf->SetFont('Courier','I',16);
	$str = iconv('UTF-8', 'windows-1252',$infoUser['rango_user']);
	$pdf->Cell(95,15,$str,0,0,'C');

	$pdf->Output('user_registrado.pdf','I');
?>