<?php

	class Usuario {
		//atributos!
		private $id;
		private $email;
		private $password;

		//nuestro constructor
		public function __construct($email, $password, $id = null){
			if ($id == null) {
				$this->password = password_hash($password, PASSWORD_DEFAULT);
			} else {
				$this->password = $password;
			}
			$this->id = $id;
			$this->email = $email;
		}
		
		//getters y setters
		public function getId() {
			return $this->id;
		}

		public function setId($id) {
			$this->id = $id;
		}

		public function getEmail() {
			return $this->email;
		}

		public function setEmail($email) {
			$this->email = $email;
		}

		public function getPassword() {
			return $this->password;
		}

		public function setPassword($password) {
			$this->password = $password;
		}

		public function guardarFoto() {
		    $archivo = $_FILES["foto-perfil"]["tmp_name"];

		    $nombreDeLaFoto = $_FILES["foto-perfil"]["name"];
		    $extension = pathinfo($nombreDeLaFoto, PATHINFO_EXTENSION);

		    $nombre = dirname(__FILE__) . "/img/" . $this->email . ".$extension";

		    move_uploaded_file($archivo, $nombre);
		  }

	}


?>