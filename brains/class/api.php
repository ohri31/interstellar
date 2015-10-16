<?php
	class Api{
		/*
			* Developed by Mirza Ohranovic (mirza.ohranovic@gmail.com)
			* In order to properly use your Api class you have to define some initial values
			* * * * * * * * * * * * * * * * * *
			* $api_name 	-> everything has to have a name, a name is your Api's ID
			* $amount  		-> number of items that your api will generate
			* $itdc 		-> short for ID, Title, Description and Content; it allows your api to show the basic post things
							-> it's set to TRUE by default
			* $galleries 	-> will your api show galleris
							-> by default FALSE

 		*/
		private $api_name 	= null;
		private $amount 		= null;

		public function generate_user_login($email, $password){
			global $db;

      $email    = $db->escape_string($email);
      $password = $db->escape_string($password);
      $password = md5("probaohakaton".$password."pobijediohakaton");

		  $user = $db->query("SELECT * FROM m_user WHERE email = '{$email}' AND password = '{$password}'");
      $api  = array();

      if($user->num_rows == 1){
        $u            = $user->fetch_assoc();

        $api['id']        = $u['id'];
        $api['ime']       = $u['ime'];
        $api['prezime']   = $u['prezime'];
        $api['email']     = $u['email'];

        print_r(json_encode($api, JSON_PRETTY_PRINT));
      }else{
        echo 0;
      }
		}

    public function register_user($json){
      global $db;
      $validno  = true;

      foreach($json as $item)
        if($item == "") $validno = false;

      if(!$validno) die(0);

      $pass = md5("probaohakaton".$json['password']."pobijediohakaton");

      $postoji = $db->query("SELECT id FROM m_user WHERE email = '{$json['email']}'")->num_rows;

      if($postoji > 0) die("-1");

      if($db->query("INSERT INTO m_user (id, ime, prezime, email, password) VALUES ('null', '{$json['ime']}', '{$json['prezime']}', '{$json['email']}', '{$pass}')")){
          print_r(json_encode($json, JSON_PRETTY_PRINT));
      }else{
          echo 0;
      }
    }

    public function add_friend($ko, $koga){
      global $db;

      $ko     = $db->escape_string($ko);
      $koga   = $db->escape_string($koga);

      $postoji = $db->query("SELECT id FROM m_friend WHERE ko = {$ko} AND koga = {$koga}")->num_rows;
      if($postoji > 0)
        die("0");
      else
        $db->query("INSERT INTO m_friend (id, ko, koga) VALUES ('null', {$ko}, {$koga})");

      die("1");
    }

    public function who_are_my_friends($me){
      global $db;

      $me = (int)$me;
      $vrati = array();
      $friends = $db->query("SELECT koga FROM m_friend WHERE ko = {$me}");
      while($row = $friends->fetch_assoc()){
        $vrati[] = $row['koga'];
      }

      if(count($vrati) > 0){
        print_r(json_encode($vrati, JSON_PRETTY_PRINT));
      }else echo 0;
    }

    public function add_mitter($data){
      global $db;

      $lokacija   = array();
      $lokacija   = $data['lokacija'];

      $vrijeme    = array();
      $vrijeme    = $data['vrijeme'];

      $invites    = array();
      $invites    = $data['invites'];

      $ko         = $data['who'];

      if($db->query("INSERT INTO m_mitter (id, who, status) VALUES ('null', {$ko}, 0)")){
        $mitter_id = $db->insert_id;

        foreach($vrijeme as $item){
          $sati     = date("H", $item);
          $minute   = date("i", $item);

          $datum    = date("Y-m-d", $item);

          $db->query("INSERT INTO m_mitter_vrijeme (id, sati, minute, datum, mitter) VALUES ('null', {$sati}, {$minute}, '{$datum}', {$mitter_id})");
        }

        foreach($lokacija as $item){
          $item = $db->escape_string($item);
          $db->query("INSERT INTO m_mitter_lokacija (id, mitter, lokacija) VALUES ('null', {$mitter_id}, '{$item}')");
        }

        foreach($invites as $item){
          $item = (int)$item;
          $db->query("INSERT INTO m_mitter_invites (id, mitter, user) VALUES ('null', {$mitter_id}, {$item})");
        }
      }

    }
	}
?>
