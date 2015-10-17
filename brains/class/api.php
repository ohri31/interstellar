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
      $friends = $db->query("SELECT m_friend.koga, m_user.* FROM m_friend
				LEFT JOIN m_user ON m_user.id = m_friend.koga
				WHERE ko = {$me}");
			$i = 0;
      while($row = $friends->fetch_assoc()){
				$vrati['friends'][$i]['id'] 			= $row['id'];
				$vrati['friends'][$i]['ime'] 		= $row['ime'];
				$vrati['friends'][$i]['prezime'] = $row['prezime'];
        $vrati['friends'][$i]['email'] 	= $row['email'];
				$i++;
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

		public function list_mitter($invites, $me){
			global $db;

			$list = array();
			if($invites == 1){
				$inivited_to = $db->query("SELECT m_mitter_invites.mitter FROM m_mitter_invites
						LEFT JOIN m_mitter ON m_mitter_invites.mitter = m_mitter.id
						WHERE m_mitter_invites.user = {$me}
									AND m_mitter.status = 0
									AND m_mitter_invites.status = 0");
				$i = 0;
				while($row = $inivited_to->fetch_assoc()){
						$list[$i]['mitter'] = $row['mitter'];

						$get_lokacije = $db->query("SELECT id, lokacija FROM m_mitter_lokacija WHERE mitter = {$row['mitter']}");
						while($m = $get_lokacije->fetch_assoc()){
							$list[$i]['lokacja'][] 			= $m['lokacija'];
							$list[$i]['lokacija_id'][] 	= $m['id'];
						}

						$get_vrijeme = $db->query("SELECT id, sati, minute, datum FROM m_mitter_vrijeme WHERE mitter = {$row['mitter']}");
						while($t = $get_vrijeme->fetch_assoc()){
							$list[$i]['vrijeme'][] 			= strtotime($t['datum']." ".$t['sati'].":".$t['minute']);
							$list[$i]['vrijeme_id'][]		= $t['id'];
						}
						$i++;
				}

				print_r(json_encode($list, JSON_PRETTY_PRINT));
			}else{
				$my_mitters = $db->query("SELECT m_mitter.id as mitter FROM m_mitter WHERE m_mitter.who = {$me}");
				$i = 0;
				while($row = $my_mitters->fetch_assoc()){
						$list[$i]['mitter'] = $row['mitter'];

						$get_lokacije = $db->query("SELECT id, lokacija FROM m_mitter_lokacija WHERE mitter = {$row['mitter']}");
						while($m = $get_lokacije->fetch_assoc()){
							$list[$i]['lokacja'][] 			= $m['lokacija'];
							$list[$i]['lokacija_id'][] 	= $m['id'];
						}

						$get_vrijeme = $db->query("SELECT id, sati, minute, datum FROM m_mitter_vrijeme WHERE mitter = {$row['mitter']}");
						while($t = $get_vrijeme->fetch_assoc()){
							$list[$i]['vrijeme'][] 			= strtotime($t['datum']." ".$t['sati'].":".$t['minute']);
							$list[$i]['vrijeme_id'][]		= $t['id'];
						}
						$i++;
				}

				print_r(json_encode($list, JSON_PRETTY_PRINT));
			}
		}

		public function pick_mitter($who, $mitter, $vrijeme, $lokacija){
			global $db;

			$who			= (int)$who;
			$mitter 	= (int)$mitter;

			if(count($vrijeme > 0) && count($lokacija > 0)){
				foreach($vrijeme as $time){
					foreach($lokacija as $lok){
						$time 	= (int)$time;
						$lok 		= (int)$lok;

						$db->query("INSERT INTO m_mitter_pick (id, vrijeme, lokacija, mitter, user) VALUES ('null', {$time}, {$lok}, {$mitter}, {$who})");
					}
				}
			}

			$db->query("UPDATE m_mitter_invites SET status = 1 WHERE mitter = {$mitter} AND user = {$who}");

			$svi_statusi = $db->query("SELECT status FROM m_mitter_invites WHERE mitter = {$mitter}");
			$zavrseno = true;
			while($row = $svi_statusi->fetch_assoc()){
				if($row['status'] == 0) $zavrseno = false;
			}

			if($zavrseno){
				$pickovi = $db->query("SELECT vrijeme, lokacija FROM m_mitter_pick WHERE mitter = {$mitter}");
				$counter = array();

				while($row = $pickovi->fetch_assoc()){
					$counter[] = $row['vrijeme'].".".$row['lokacija'];
				}

				$c 				= array_count_values($counter);
				$val 			= array_search(max($c), $c);

				$elements 				= explode(".", $val);
				$picked_time	 		= $elements[0];
				$picked_lokacija 	= $elements[1];

				$db->query("UPDATE m_mitter SET status = 1, vrijeme = {$picked_time}, lokacija = {$picked_lokacija} WHERE id = {$mitter}");

				/* Za push notifikaciju */
				echo 2;
			}else{
				echo 1;
			}

		}

		public function show_mitter($mitter, $invite){
			global $db;

			$list = array();

			/* Check status */
			$status = $db->query("SELECT status FROM m_miter_invites WHERE mitter = {$mitter} AND id = {$invite}");
			if($status->num_rows > 0){
				$check = $status->fetch_assoc();
				if($check['status'] == 1) echo "0";
			}else echo "0";

					$list['mitter'] = $mitter;

					$ko_je_pozvao 	= $db->query("SELECT * FROM m_user LEFT JOIN m_mitter ON m_mitter.who = m_user.id WHERE m_mitter.id = {$mitter}")->fetch_assoc() or die(mysqli_error($db));

					$list['user_invited']['id']			= $ko_je_pozvao['id'];
					$list['user_invited']['ime']		= $ko_je_pozvao['ime'];
					$list['user_invited']['prezime']= $ko_je_pozvao['prezime'];
					$list['user_invited']['email'] 	= $ko_je_pozvao['email'];

					$get_lokacije = $db->query("SELECT id, lokacija FROM m_mitter_lokacija WHERE mitter = {$mitter}");
					while($m = $get_lokacije->fetch_assoc()){
						$list['lokacja'][] 			= $m['lokacija'];
						$list['lokacija_id'][] 	= $m['id'];
					}

					$get_vrijeme = $db->query("SELECT id, sati, minute, datum FROM m_mitter_vrijeme WHERE mitter = {$mitter}");
					while($t = $get_vrijeme->fetch_assoc()){
						$list['vrijeme'][] 			= strtotime($t['datum']." ".$t['sati'].":".$t['minute']);
						$list['vrijeme_id'][]		= $t['id'];
					}

			print_r(json_encode($list, JSON_PRETTY_PRINT));
		}

	}
?>
