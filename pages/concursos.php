<?php 
	require_once('./clases/conexion.php');
	//cuando entre en está página deberé recoger el número total de concursos que hay, aunque estén finalizados o no
	//obviamente los primeros que aparezcan serán los que tenga la fecha más antigua y los que esten activos
	$allConcursosBD = "";		//variable para almacenar todos los concursos de mi bd y después mostrarlos 
	$tamPaginas = 6;		//será el número de concursos que quiero que me muestren por páginas
	if(isset($_GET['pagina'])){	//comprobamos si el usuario es la primera vez que entra en la página o no
		if($_GET['pagina'] == 1){
			//no quiero se que muestre en la url la primera pagina por lo tanto cargo de nuevo la página como si fuese la primera vez que entra en ella
			header("Location:index.php?p=concursos");
		}else{
			$pagina = $_GET['pagina'];
		}
	}else{
		$pagina = 1;	//variable para decirle en que página nos encontramos - será la primera vez que el usuario entra a esta página
	}
	
	$empezarDesde = ($pagina-1)*$tamPaginas; //variable que indicará en el limit el número de registro que empezará
	$con = Conexion::conectar();
	$sql = "Select id,nombre,imagen_portada,fecha_start,fecha_end, activo from concursos order by activo desc,fecha_start";		//para todos los concursos que tengo en la BD
	$res = $con ->query($sql);
	$numFilas = $res -> num_rows;	//almaceno el número de registros que nos devuelve la sentencia sql - esta variable es el número total de concursos
	$totalPaginas = ceil($numFilas/$tamPaginas);	//ceil redondea el resultado
	if($pagina != 1){
		if(($tamPaginas*$pagina) > $numFilas){
			$mostrandoConcursos = $numFilas;
		}else{
			$mostrandoConcursos = ($tamPaginas*$pagina);
		}
	}else{
		$mostrandoConcursos = ($tamPaginas*$pagina);
		if($mostrandoConcursos > $numFilas){
			$mostrandoConcursos = $numFilas;
		}
	}
	if($numFilas > 0){	//porque hay concursos - y dentro de la condición volveremos hacer otra consulta

		$sql_limite = "Select id,nombre,imagen_portada,fecha_start,fecha_end, activo from concursos order by activo desc,fecha_start limit $empezarDesde,$tamPaginas";
		$res_limite = $con ->query($sql_limite);
		while($linea = $res_limite -> fetch_assoc()){
			if($linea['activo'] == 0){ //concurso finalizado
				$allConcursosBD .= "<div class='container contenedorConcursos col-xs-12 col-sm-6 col-md-4'><div class='row'><div class='soloImagen col-xs-12'><a href='".$_SERVER['PHP_SELF']."?p=verConcurso&exp=".$linea['id']."'><img src='../img/frontPage/".$linea['imagen_portada']."' /></a></div><div class='col-xs-6'><p class='tituloConcurso'>".$linea['nombre']."</p></div><div class='col-xs-6'><p style='color:red;'>Finalizado</p></div><div class='col-xs-6'><p class='fecha_inicio'>".$linea['fecha_start']."</p></div><div class='col-xs-6'><p class='fecha_fin'>".$linea['fecha_end']."</p></div></div></div>";
			}else{
				$allConcursosBD .= "<div class='container contenedorConcursos col-xs-12 col-sm-6 col-md-4'><div class='row'><div class='soloImagen col-xs-12'><a href='".$_SERVER['PHP_SELF']."?p=verConcurso&exp=".$linea['id']."'><img src='../img/frontPage/".$linea['imagen_portada']."' /></a></div><div class='col-xs-6'><p class='tituloConcurso'>".$linea['nombre']."</p></div><div class='col-xs-6'><p  style='color:green;'>Activo</p></div><div class='col-xs-6'><p class='fecha_inicio'>".$linea['fecha_start']."</p></div><div class='col-xs-6'><p class='fecha_fin'>".$linea['fecha_end']."</p></div></div></div>";
			}
			

		}
	}else{
		$allConcursosBD =  "<h3>Aún no se han registrado concursos</h3>";
	}
	Conexion::desconectar($con);
?>

<main>
	<div class="allConcursos container-fluid text-center">
		<div class="row">
			<div class="col-xs-12">
				<legend>Mostrando <?php echo $mostrandoConcursos ?> de <?php echo $numFilas ?> concursos</legend>
			</div>
		</div>
		<div class="row">
			<?php echo $allConcursosBD ?>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<nav>
					<ul class="pagination">
						<?php 
							//aquí creare mi código para crear la paginación de la página - para ello nos ayudará la variable que nosotros creamos $totalPaginas
							for($i=1;$i<=$totalPaginas;$i++){
								if($i == $pagina){
									echo "<li class='page-item active'><a id='detenerEvento' class='page-link' href='' tabindex='-1'>".$i."</a></li>";
								}else{
									echo "<li><a href='?p=concursos&pagina=$i'>".$i."</a></li>";
								}
								
							}
						?>
					</ul>
				</nav>
			</div>
		</div>
	</div>

</main>