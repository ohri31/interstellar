<?php
	class User {
		public $id = null;
		public $ime = null;
		public $prezime = null;
		public $privilegije = null;
		public $mail = null;

		public function login($email, $password){
			global $db;
			global $url_home;
			

			$password = password_gen($password);

			$check = $db->query("SELECT COUNT(id) as num, id as id FROM n_users WHERE mail = '{$email}' AND password = '{$password}'")->fetch_assoc();

			if($check['num'] == 1){
				$_SESSION['user'] = $check['id'];
				header('Location: '.$url_home);
			}
		}

		public function logout(){
			global $url_home;
			
			if(isset($_SESSION['user'])) session_destroy(); 
			header('Location: '.$url_home);	
		}

		public function user_info(){
			global $db;

			if($this->id != null){
				$uzmi = $db->query("SELECT * FROM n_users WHERE id = {$this->id}")->fetch_assoc();
				$this->ime 		= $uzmi['ime'];
				$this->prezime	= $uzmi['prezime'];
				$this->mail 	= $uzmi['mail'];
				$this->privilegije = $uzmi['privilegije'];
			}
		}

		public function user_list(){
			global $db;

			if(isset($_SESSION['user'])){
				$users = $db->query("SELECT * FROM n_users");
				return $users;	
			}
		}
		

	}
?>