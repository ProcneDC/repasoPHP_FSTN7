<?php 
    session_start();

    function validarInformacion($datos){
        //"por cada $datos "desglozamelo" como $key y $value"
        foreach($datos as $key => $value){
            $datos[$key] = trim($value);
        }
        //ahora podemos empezar a usar cada valor del array que entre como parametro
        //el array de errores vacio que vamos a ir llenando
        $errores = [];

        /*validacion de email (usamos el email como nombre de usuario)
        if(strlen($datos['email']) == 0){
            //puso algo en el campo de email?
            $errores['email'] = "El email es obligatorio";
        } else if(!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)){
            //el mail es valido?
            $errores['email'] = "El mail ingresado no es valido";
        } else if(buscarUsuario("email", $datos['email'])){
            var_dump($datos["email"]); exit; 
            //el mail ya existe en nuestra base de datos?
            $errores['email'] = "El mail ingresado ya existe";
        }*/

        //validacion de mail
        $datos['email'] = filter_var($datos['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "Debe ingresar un mail válido";
        }
        else{
            $usuario = buscarUsuario('email', $datos['email']);
            if($usuario){
                $errores['email'] = "El usuario {$datos['email']} ya existe. Puede ingresar <a href='login.php'>aquí</a>";
            }
        }

        //validacion de password
        if(strlen($datos['password']) < 4){
            //la pass tiene 4 o mas caracteres?
            $errores['password'] = "La contraseña es muy corta";
        } else if ($datos['password'] != $datos['cpassword']){
            //los dos campos de pass coinciden?
            $errores['password'] = "La contraseña no coincide";
        }
        //devolveme el array $errores, tenga errores o no.
        return $errores;
    }

    function crearUsuario($datos){
        //la variable $usuario es el molde que, en forma de array, 
        //va a formar todos los  usuarios que nuestra app necesite crear.
        $usuario = [
            "email" => $datos["email"],
            "password" => password_hash($datos["password"], PASSWORD_DEFAULT),
            "id" => crearNuevoID()
          ];
      return $usuario;
    }

    function guardarDato($datosFormulario) {
        //le paso los datos del formulario
        //armo el array para la coleccion que se guardara en el json
        $nombreColeccion = 'usuarios';

        //llamo a la funcion para crearlo con el nombre de la coleccion
        crearJson($nombreColeccion);

        //armo el nombre del archivo del json, para que lleve el nombre de la coleccion
        $archivo = $nombreColeccion.'.json';

        //tomo el contenido del json y lo guardo en archivojson
        $archivojson = file_get_contents($archivo);

        //decodifico el json para usarlo como array
        $archivoArray = json_decode($archivojson, true);

        //agrego al final del array los datos 'nuevos' del formulario
        $archivoArray[$nombreColeccion][] = $datosFormulario;

        //codifico el array en formato json (texto plano)
        $archivoJsonConMasInfo = json_encode($archivoArray);

        //para volver a guardarlo en el archivo que ya tenia del json
        file_put_contents($archivo, $archivoJsonConMasInfo);
    }    

    function crearJson($nombreColeccion){

       //armo el nombre del archivo del json, para que lleve el nombre de la coleccion 
      $archivo = $nombreColeccion.'.json';

      //si el archivo no existe, lo creo con la ruta y le doy el nombre de la coleccion
      if(!file_exists($archivo)){
            $template = [];
            $template[$nombreColeccion] = [];

        // codifico el array en formato json      
        $json = json_encode($template);

        //guardo el array ya codificado en el archivo que armé al inicio de esta función
        file_put_contents($archivo, $json);
      }
    }

    //No se usa ya que arma el json con cada dato por separado y no en una única colección
    function guardarUsuario($datos){
        //cada vez que se use esta funcion, va a almacenar lo que le pasemos 
        //como parametro en la variable $json.
        $json = json_encode($datos);   
        //RECUERDEN, a partir de aca, PARA PHP $json es solamente un string.  
        file_put_contents("usuarios.json", $json . PHP_EOL, FILE_APPEND);
        //PHP_EOL es el "enter" (la explicacion mas tecnica busquenla, 
        //la funcion ahora es esa).
        //FILE_APPEND evita que borremos lo que el .json ya tiene
    }

    //nuestra funcion para guardar la imagen de perfil
    function guardarImagen($usuario) {
        //otra vez, $errores vacio
        $errores = [];
        $id = $usuario["id"];

        if ($_FILES["avatar"]["error"] == UPLOAD_ERR_OK) {
            $nombre = $_FILES["avatar"]["name"];
            $archivo = $_FILES["avatar"]["tmp_name"];

            $ext = pathinfo($nombre, PATHINFO_EXTENSION);

            if ($ext != "jpg" && $ext != "png" && $ext != "jpeg") {
                $errores["avatar"] = "Solo acepto formatos jpg y png";
                return $errores;
            }

            $miArchivo = dirname(__FILE__);

            $miArchivo = $miArchivo . "/img/";

        $miArchivo = $miArchivo. "perfil" . $id . "." . $ext;

            move_uploaded_file($archivo, $miArchivo);
        } else {
            $errores["avatar"] = "Hubo un error al procesar el archivo";
        }

        return $errores;
    }




    function buscarUsuario($campo, $valor)
    {

        //obtenemos un array de usuarios de nuestro archivo json
        $usuariosTraidos = traeTodaLaBase();
        //para comprobar que trae y que queremos comparar podemos verlo usando:
        //var_dump($usuariosTraidos['usuarios'][1]['email']); exit;


        //recorremos el array de usuarios que trajimos de la base json
        for ($i=0; $i <count($usuariosTraidos['usuarios']); $i++) {
            if ($usuariosTraidos['usuarios'][$i]['email'] == $valor) {

               // comparamos el campo del array con el valor que ya tenemos del usuario y si coincide, devolvemos todos los datos de ese usuario

                return $usuariosTraidos['usuarios'][$i];
                //var_dump($usuariosTraidos['usuarios'][$i]); exit;
            }
        }

        //si ya busque en todos los usuarios y no encontre el valor, entonces devuelvo que no encontre uno repetido
        return false;
    }




    function traeTodaLaBase(){


        //le asignamos a $contenido, TODO lo que tiene mi base de datos
        $contenido = file_get_contents('usuarios.json');

        //generamos el array de usuarios, PERO, ese array va a tener
        //en cada valor, un string en formato json que vamos a hacerle decode mas abajo
        //$usuariosJSON = explode(PHP_EOL, $contenido);

        //ya los trae todos en un solo array
        $usuariosTraidos = json_decode($contenido, true);


        /*array_pop($usuariosJSON);

        //array vacio de usuarios traidos, como lo llenamos? con un foreach
        $usuariosTraidos = [];

        foreach($usuariosJSON as $usuario){
            //ACA le insertamos al array vacio, un array por usuario, porque
            //a cada valor que teiamos en $usuariosJSON, le aplicamos json_decode!
            $usuariosTraidos[] = json_decode($usuario, true);    
        }

        //"returname" un array que pueda usar en PHP*/
        return $usuariosTraidos;
    }

    //para relacionar la imagen subida por el usuario, con su 
    //cuenta, necesitamos que cuando se registre se le genere un ID
    function crearNuevoID() {

        //traigo todos los datos que tengo en mi base json
        $contenido = traeTodaLaBase();
        //si la cantidad me da 0, devuelvo 1 
        if (count($contenido['usuarios']) == 0) {
            return 1;
        }
        //agrego el dato del id 
        $elUltimo = array_pop($contenido['usuarios']);
        return $elUltimo["id"] + 1;

    }



    /* Esta funcion no la usamos ya que trae todos los usuarios por separado

    function buscamePorMail($email){

        //Generamos un array de TODA LA BASE DE DATOS para poder manejarla con PHP
        $usuariosTraidos = traeTodaLaBase();

        //la procesamos para buscar el mail que pasamos como parametro 
        //a ver si existe o no

        foreach($usuariosTraidos as $usuario){
            //SI elMailDeLaBase es igualigual al mailPasadoPorParametro
            if($usuario['email'] == $email){
                //returname el usario
                return $usuario;
            }
        }
        //si no, "returname" NULL asi uso el dato para validar
        return null;
    }

    }*/

    //funcionalidad adicional comentada en clase

    // function buscamePorAlgo($campo, $valor){
    //     $usuariosTraidos = traeTodaLaBase();
    //     foreach($usuariosTraidos as $usuario){
    //         if($usuario[$campo] == $valor){
    //             return $usuario;
    //         }
    //     }

    //     return null;
    // }

    //nuestra funcion para loguear
    function login($usuario) {

        //si coinciden los valores...
        $_SESSION["email"] = $usuario["email"];
        //borramos la contraseña
        unset($usuario['password']);
        //seteamos la cookie
        setcookie("email", $usuario["email"], time()+3600);
        //var_dump($_SESSION["email"] ); exit;
    }
    //nuestro controlador de login
    function controlarLogin() {
        //SI ESTA SETEADO el email en nuestra sesion
        if (isset($_SESSION["email"])) {
            //devolveme true
            return true;
        } else {
            //si nos llega un usuario con una cookie
            if (isset($_COOKIE["email"])) {
                //que mi session sea seteada con el mail de esa cookie
                $_SESSION["email"] = $_COOKIE["email"];
                //y devolveme true
                return true;
            } else {
            //ante cualquier otro caso, dame false            
                return false;
            }
        }
    }

    //nuestra funcion para desloguear
    function logout() {
        session_destroy();
        //seteo de cookie con -1 para que "muera" D=
        setcookie("email", "", -1);
    }


 ?>
