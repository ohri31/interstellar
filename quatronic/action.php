<?php
	require_once('brains/global.php');
	// Ovdje ide provjera logiranosti

	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		switch ($_POST['action'])
		{
			case 'obrisi_gal':
				$gal = (int)$_POST['gal'];
				$broj = (int)$_POST['broj'];

				remove_img($gal_glavna.$gal.'.'.$broj.'.jpg');
				remove_img($gal_thumb.$gal.'.'.$broj.'.jpg');

				remove_img_db($gal, $broj);
			break;

			case 'obrisi_post':
				$id = (int)$_POST['id'];

				// Brisanje posta
				$db->query("DELETE FROM n_post WHERE id = {$id}");

				// Brisanje slika sa diska 
				$slike = $db->query("SELECT broj FROM n_galerije WHERE post = {$id}");
				while($row = $slike->fetch_assoc()){
					unlink($gal_glavna.$id.'.'.$row['broj'].'.jpg');
					unlink($gal_thumb.$id.'.'.$row['broj'].'.jpg');
				}

				// Brisanje galerije 
				$db->query("DELETE FROM n_galerije WHERE post = {$id}");

			break;

			case 'objavi_post':
				$id 	= (int)$_POST['id'];
				$datum 	= date('Y-m-d', strtotime($db->escape_string($_POST['date'])));
	
					$time = (int)$_POST['sati'].':'.(int)$_POST['minuta'].' '.$datum;
					$time = strtotime($time);

 				$db->query("UPDATE n_post SET objavljeno = 1, time = {$time}, datum = '{$datum}' WHERE id = {$id}");
			break;

			case 'ukini_objava':
				$id = (int)$_POST['id'];
				$db->query("UPDATE n_post SET objavljeno = 0 WHERE id = {$id}");
			break;

			case 'get_user':
				$id = (int)$_POST['id'];
				$s = $db->query("SELECT * FROM n_users WHERE id = {$id}");
				$sve = array();

				while($row = $s->fetch_assoc()){
					$sve['id'] = $row['id'];
					$sve['ime'] = $row['ime'];
					$sve['prezime'] = $row['prezime'];
					$sve['privilegije'] = $row['privilegije'];
					$sve['email'] = $row['mail'];
				}

				echo json_encode($sve);
			break;

			case 'remove_user':
				$id = (int)$_POST['id'];
				$db->query("DELETE FROM n_users WHERE id = {$id}");
			break;

			case 'get_kategorija':
				$id = (int)$_POST['id'];
				$s = $db->query("SELECT * FROM n_kategorije WHERE id = {$id}");
				$sve = array();

				while($row = $s->fetch_assoc()){
					$sve['id'] = $row['id'];
					$sve['kategorija'] = $row['naziv'];
					$sve['meni'] = $row['meni'];
				}

				echo json_encode($sve);
			break;

			case 'remove_kategorija':
				$id = (int)$_POST['id'];
				$db->query("UPDATE n_post SET objavljeno = 0 WHERE kategorija = {$id}");
				$db->query("DELETE FROM n_kategorije WHERE id = {$id}");
			break;

			case 'promo_post':
				$id = (int)$_POST['id'];
				$db->query("UPDATE n_post SET izdvojeno = 1 WHERE id = {$id}");
			break;

			case 'ukini_promo_post':
				$id = (int)$_POST['id'];
				$db->query("UPDATE n_post SET izdvojeno = 0 WHERE id = {$id}");
			break;

			case 'remove_task':
				$id = (int)$_POST['id'];
				$db->query("DELETE FROM n_tasks WHERE id = {$id}");
			break;

			case 'end_task':
				$id = (int)$_POST['id'];
				$db->query("UPDATE n_tasks SET finished = 1 WHERE id = {$id}");
			break;

			case 'open_task':
				$id = (int)$_POST['id'];
				$db->query("UPDATE n_tasks SET finished = 0 WHERE id = {$id}");
			break;

			case 'get_task':
				$id = (int)$_POST['id'];
				$s = $db->query("SELECT * FROM n_tasks WHERE id = {$id}");
				$sve = array();

				while($row = $s->fetch_assoc()){
					$sve['id'] = $row['id'];
					$sve['task'] = $row['task'];
					$sve['user'] = $row['user'];
					if($row['deadline'] != 0) $sve['deadline'] = date('d.m.Y', $row['deadline']);
						else $sve['deadline'] = '';
					$sve['status'] = $row['status'];
				}

				echo json_encode($sve);
			break;
		}
	}
?>