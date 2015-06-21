<?php

class Event {
	public $id = null;
	public $naziv = null;
	public $opis = null;


	public function save_event($naziv, $opis, $datum, $update = false){
		global $db;

		$naziv 	= $db->escape_string($naziv);
		$opis	= $db->escape_string($opis);
		$datum 	= $db->escape_string($datum);

		if(!$update){
			$db->query("INSERT INTO e_event (id, naziv, opis, datum) VALUES ('null', '{$naziv}', '{$opis}', '{$datum}')");

			// Get current event id
			$current_id = $db->query("SELECT id FROM e_event WHERE naziv = '{$naziv}' AND opis = '{$opis}' AND datum = '{$datum}' LIMIT 0, 1")->fetch_assoc();
			$this->id 	= $current_id['id'];

			$this->naziv 	= $naziv;
			$this->opis 	= $opis;
		}else{
			$db->query("UPDATE SET naziv = '{$naziv}', opis = '{$opis}', datume = '{$datum}' WHERE id = {$this->id}");

			$this->naziv 	= $naziv;
			$this->opis 	= $opis;
		}
	}

	public function tag_event($tagovi){
		global $db;

		if(is_null($this->id)) trigger_error("Set the event id", E_USER_ERROR);
		foreach($tagovi as $tag){
			if($tag != ''){
				$tag = strtolower(str_replace('#', '', $tag));

				$getid = $db->query("SELECT id FROM e_tag WHERE e_tag.tag = '{$tag}'");
				if($getid->num_rows == 0){
					$db->query("INSERT INTO e_tag (id, tag) VALUES ('null', '{$tag}')");

					$getid = $db->query("SELECT id FROM e_tag WHERE e_tag.tag = '{$tag}'");
					
					$getid = $getid->fetch_assoc();
					$tagid = $getid['id'];
					$db->query("DELETE FROM e_tag_event WHERE tag = {$tagid} AND event = {$this->id}");
					$db->query("INSERT INTO e_tag_event (id, tag, event) VALUES ('null', {$tagid}, {$this->id})");
				}else{
					$getid = $getid->fetch_assoc();
					$tagid = $getid['id'];
					$db->query("DELETE FROM e_tag_event WHERE tag = {$tagid} AND event = {$this->id}");
					$db->query("INSERT INTO e_tag_event (id, tag, event) VALUES ('null', {$tagid}, {$this->id})");
				}
			}
		}
	}

	public function upload_image($location){
		global $db;
		global $media_url;
		global $media_path;

		if(!file_exists($location)) { echo 'No such file ('.$location.')'; break; }

		list($width, $height) = getimagesize($location);

		copy($location, $media_path.'ev_'.$this->id.'.jpg');

		// Resize code

		if($width > 150 && $height > 30){
				$v_location = $media_path.'v_'.$this->id.'.jpg';
				$s_location = $media_path.'s_'.$this->id.'.jpg';
				$k_location = $media_path.'k_'.$this->id.'.jpg';

				// Velika slika 
				$image_v = new Imagick;
				$image_v->readImage($location);

					$hratio 	= 360 / $height;
					$wratio		= 970 / $width;

					if($wratio < 1 && $hratio < 1){
						$odabran = min($hratio, $wratio);

						if($odabran == $hratio){
							$nova = $width * $wratio;
							$image_v->scaleImage($nova, 0);
						}elseif($odabran == $wratio){
							$nova = $height * $hratio;
							$image_v->scaleImage(0, $nova);
						}
					}elseif($wratio < 1 && $hratio >= 1){
						$nova = $height * $hratio;
						$image_v->scaleImage(0, $nova);
					}elseif($wratio >= 1 && $hratio < 1){
						$nova = $width * $wratio;
						$image_v->scaleImage($nova, 0);
					}else{
						$image_v->scaleImage(970, 0);
					}
					
				$v = $image_v->getImageGeometry();
				$w = $v['width'];
				$h = $v['height'];

				$x = floor(($w - 970) / 2);
				$y = floor(($h - 360) / 2);

				if($x <= 1) $x = 0;
				if($y <= 1) $y = 0;
				
				$image_v->cropImage(970, 360, $x, $y);
				$image_v->writeImage($v_location);
				$image_v->clear();
				$image_v->destroy();

				// Vertikalnn mala
				$image_s = new Imagick;
				$image_s->readImage($location);
			
					$hratio 	= 210 / $height;
					$wratio		= 490 / $width;

					if($wratio < 1 && $hratio < 1){
						$odabran = min($hratio, $wratio);

						if($odabran == $hratio){
							$nova = $width * $wratio;
							$image_s->scaleImage($nova, 0);
						}elseif($odabran == $wratio){
							$nova = $height * $hratio;
							$image_s->scaleImage(0, $nova);
						}
					}elseif($wratio < 1 && $hratio >= 1){
						$nova = $height * $hratio;
						$image_s->scaleImage(0, $nova);
					}elseif($wratio >= 1 && $hratio < 1){
						$nova = $width * $wratio;
						$image_s->scaleImage($nova, 0);
					}else{
						$image_s->scaleImage(490, 0);
					}
					
				$v = $image_s->getImageGeometry();
				$w = $v['width'];
				$h = $v['height'];

				$x = floor(($w - 490) / 2);
				$y = floor(($h - 210) / 2);

				if($x <= 1) $x = 0;
				if($y <= 1) $y = 0;

				$image_s->cropImage(490, 210, $x, $y);
				$image_s->writeImage($s_location);
				$image_s->clear();
				$image_s->destroy();

				// Square
				$image_k = new Imagick;
				$image_k->readImage($location);

					$hratio 	= 240 / $height;
					$wratio		= 330 / $width;

					if($wratio < 1 && $hratio < 1){
						$odabran = min($hratio, $wratio);

						if($odabran == $hratio){
							$nova = $width * $wratio;
							$image_k->scaleImage($nova, 0);
						}elseif($odabran == $wratio){
							$nova = $height * $hratio;
							$image_k->scaleImage(0, $nova);
						}
					}elseif($wratio < 1 && $hratio >= 1){
						$nova = $height * $hratio;
						$image_k->scaleImage(0, $nova);
					}elseif($wratio >= 1 && $hratio < 1){
						$nova = $width * $wratio;
						$image_k->scaleImage($nova, 0);
					}else{
						$image_k->scaleImage(330, 0);
					}
					
				$v = $image_k->getImageGeometry();
				$w = $v['width'];
				$h = $v['height'];

				$x = floor(($w - 330) / 2);
				$y = floor(($h - 240) / 2);

				if($x <= 1) $x = 0;
				if($y <= 1) $y = 0;

				$image_k->cropImage(330, 240, $x, $y);
				$image_k->writeImage($k_location);
				$image_k->clear();
				$image_k->destroy();

				echo 'k';
		}else{

			echo 'nije;';
		}
	}

}

?>