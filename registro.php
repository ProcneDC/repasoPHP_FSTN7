<?php

	require_once 'funciones.php';
	//SI ME LLEGAN datos por post
	if($_POST){
		//llename este array $errores con lo que sea que devuelve
		// validarInformacion($datos) (OSEA $_POST)
		$errores = validarInformacion($_POST);
		//y si encima ese array llega con 0 valores
		if(count($errores) == 0){
			//creame un usuario en esta variable $usuario
			$usuario = crearUsuario($_POST);
			//armamos un array de errores para la subida de imagenes nomas
			$erroresDeImagen = guardarImagen($usuario);
			//combinamos $errores y $erroresDeImagen 
			//con la funcion de PHP array_merge()
			$errores = array_merge($errores, $erroresDeImagen);
			if(count($errores) == 0){
				//y usa esta funcion para guardarla en usuarios.json!
				guardarUsuario($usuario);
				//ah y mandame a loguearme!
				header('Location: login.php');
				exit;
			}
		}

	}
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="css/master.css">
		<title>Demo Registro</title>
	</head>
		<body>
			<main class="flex-container">
			<header>
				<h1> Registrate </h1>
			</header>
			<form class="formulario" action="registro.php" method="post" enctype="multipart/form-data">
					<label>E-mail</label>
					<input type="text" name="email">
					<label for="">Foto de Perfil</label>
					<input type="file" name="avatar">
					<label>Password</label>
					<input type="password" name="password">
					<label>Confirmar password</label>
					<input type="password" name="cpassword"> 
					<input type="submit" class="btn-registro" value="Registrarme">
			</form>
			<a href="login.php">Si ya tenes cuenta logueate aca</a>
		</main>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
	</body>
</html>
