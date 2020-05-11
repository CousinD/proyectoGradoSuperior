<?php 
	//archivo que contendrá un formulario para poder crear nuevos concursos
?>

<main>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="nuevoConcursoForm" enctype="multipart/form-data" class="container" >
		<legend class="text-left">Crear Nuevo Concurso</legend>
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<div id="selectPortada" class="thumbnail">
					<img src="./img/frontPage/defaultPortada.svg" alt="Imagen Portada">
					<button id="selectImgPortada" class="btn btn-default">Definir Portada</button>
				</div>
				<span class="alert alert-info" id="infoImage">No hay imagen aún</span>
				<input type="file" name="file" id="elegirImagenPortada" />
			</div>
			<div class="row col-xs-12 col-md-6">
				<div class="col-xs-12">
					<input type="text" id="nombreNuevoConcurso" placeholder="Nombre del concurso" class="form-control" />
				</div>
				<div class="col-xs-12">
					<input type="text" id="temaNuevoConcurso" placeholder="Genero del concurso" class="form-control">
				</div>
				<div class="col-xs-12">
					<input type="text" id="limiteNuevoConcurso" placeholder="Fecha fin del concurso" class="form-control">
				</div>
				<div class="row col-xs-12">
					<div class="col-xs-12 col-md-6">
						<label>Max. de fotos por participante</label>
					</div>
					<div class="col-xs-12 col-md-6">
					<!-- Por defecto pondre que tendrá un valor de 3 -->
						<input type="number" id="numFotosNuevoConcurso" min="3" max="8" value="3" class="form-control" />
					</div>
				</div>
				<div class="row col-xs-12">
					<div class="col-xs-12 col-md-6">
						<label >Formato de la imagenes</label>
					</div>
					<div class="col-xs-12 col-md-6">
						<select name="formatofotos" id="formatoNuevoConcurso" class="form-control">
							<option value="" selected="selected">- Elija el formato -</option>
							<option value="png">PNG</option>
							<option value="jpeg">JPG/JPEG</option>
							<option value="gif">GIF</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<textarea name="breveDesc" placeholder="Una breve descripción sobre el concurso.." id="descripcionNuevoConcurso" class="form-control" cols="30" rows="10"></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<button id="validarCreaccion" class="btn btn-default btn-block">Añadir Concurso</button>
			</div>
			<div class="col-xs-12">
				<div class="suggestion"></div>
			</div>
		</div>
		<span class="msgErrores alert alert-warning"></span>
		<span class="msgCorrect alert alert-success"></span>
	</form>
</main>