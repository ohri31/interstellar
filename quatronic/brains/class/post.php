<?php

class Post {
	public $id = null;
	public $naslov = null;
	public $uvod = null;
	public $sadrzaj = null;
	public $label = null;
	public $galerija = 0;
	public $objavljeno = 0;
	public $tagovi = array();
	public $naslovna = 0;
	public $promo = 0;
	public $time = 0;


	public function query_post($id){
		global $db;

		$get_all = $db->query("SELECT * FROM n_post WHERE id = {$id}");

		if($get_all -> num_rows  == 0){
			header('Location: '.$url_home.'post.php');
		}else{
			$get_all = $get_all->fetch_assoc();
		}

		$get_tags = $db->query("SELECT t.tag FROM n_tag t LEFT JOIN n_tag_post ON t.id = n_tag_post.tag WHERE n_tag_post.post = {$id}");

		$this->id 		= $id;
		$this->naslov 	= $get_all['naslov'];
		$this->uvod 	= $get_all['uvod'];
		$this->sadrzaj 	= $get_all['sadrzaj'];
		$this->galerija = $get_all['galerija'];
		$this->objavljeno = $get_all['objavljeno'];
		$this->naslovna = $get_all['naslovna'];
		$this->promo 	= $get_all['izdvojeno'];
		$this->time 	= $get_all['time'];
		$this->label 	= $get_all['label'];

		$this->uvod		= str_replace('<br />', PHP_EOL, $this->uvod);
		$this->sadrzaj 	= str_replace('<br />', PHP_EOL, $this->sadrzaj);

		while($row = $get_tags->fetch_assoc()){
			$this->tagovi[] = $row['tag'];
		}

		echo $db->error;
	}

	public function save_post($naslov, $uvod, $sadrzaj, $label, $datum, $vrijeme_objave, $kategorija, $update = false){
		global $db;

		$naslov 	= $db->escape_string($naslov);
		$uvod		= $db->escape_string($uvod);
		//$sadrzaj	= $db->escape_string($sadrzaj);
		$datum 		= $db->escape_string($datum);
		$kategorija	= $db->escape_string($kategorija);

		$time = time();

		if(!$update){
			$db->query("INSERT INTO n_post (id, naslov, uvod, sadrzaj, datum, time_saved, kategorija, galerija, label) VALUES ('null', '{$naslov}', '{$uvod}', '{$sadrzaj}', '{$datum}', {$time}, {$kategorija}, 0, '{$label}')");

			// Get current event id
			$current_id = $db->query("SELECT id FROM n_post WHERE naslov = '{$naslov}' AND uvod = '{$uvod}' AND sadrzaj = '{$sadrzaj}' AND kategorija = {$kategorija} AND datum = '{$datum}' LIMIT 0, 1")->fetch_assoc();

			$this->id 	= $current_id['id'];

			$this->naslov 	= $naslov;
			$this->uvod 	= $uvod;
			$this->sadrzaj 	= $sadrzaj;
			$this->label 	= $label;

			echo $db->error;
		}else{
			$db->query("UPDATE n_post SET naslov = '{$naslov}', uvod = '{$uvod}', sadrzaj = '{$sadrzaj}', kategorija = {$kategorija}, datum = '{$datum}', time = {$vrijeme_objave}, label = '{$label}' WHERE id = {$this->id}");

			$this->naslov 	= $naslov;
			$this->uvod 	= $uvod;
			$this->sadrzaj 	= $sadrzaj;
			$this->label 	= $label;

			echo $db->error;
		}
	}

	public function tag_post($tagovi){
		global $db;

		if(is_null($this->id)) trigger_error("Set the post id", E_USER_ERROR);
		
		$db->query("DELETE FROM n_tag_post WHERE post = {$this->id}");

		foreach($tagovi as $tag){
			if($tag != ''){
				$tag = strtolower(str_replace('#', '', $tag));

				$getid = $db->query("SELECT id FROM n_tag WHERE n_tag.tag = '{$tag}'");
				if($getid->num_rows == 0){
					$db->query("INSERT INTO n_tag (id, tag) VALUES ('null', '{$tag}')");

					$getid = $db->query("SELECT id FROM n_tag WHERE n_tag.tag = '{$tag}'");
					
					$getid = $getid->fetch_assoc();
					$tagid = $getid['id'];
					$db->query("DELETE FROM n_tag_post WHERE tag = {$tagid} AND post = {$this->id}");
					$db->query("INSERT INTO n_tag_post (id, tag, post) VALUES ('null', {$tagid}, {$this->id})");
				}else{
					$getid = $getid->fetch_assoc();
					$tagid = $getid['id'];
					$db->query("DELETE FROM n_tag_post WHERE tag = {$tagid} AND post = {$this->id}");
					$db->query("INSERT INTO n_tag_post (id, tag, post) VALUES ('null', {$tagid}, {$this->id})");
				}
			}
		}
	}

	public function main_image($location){
		global $db;
		global $media_url;
		global $media_path;

		global $v_location;
		global $s_location;
		global $k_location;

		global $dimensions;

		if(!file_exists($location)) { echo 'No such file ('.$location.')'; break; }

		list($width, $height) = getimagesize($location);

		copy($location, $media_path.'media.main_'.$this->id.'.jpg');

		// Resize code

		if($width > 150 && $height > 30){
				$v_location = $v_location.$this->id.'.jpg';
				$s_location = $s_location.$this->id.'.jpg';
				$k_location = $k_location.$this->id.'.jpg';

			/*	// Dimenzije
				
				$w2 = 400; $h2 = 180;
				$w3 = 360; $h3 = 150;
				
				// Use the crop_algorithm function
				crop_algorithm($location, $v_location, $w1, $h1);
				crop_algorithm($location, $s_location, $w2, $h2);
				crop_algorithm($location, $k_location, $w3, $h3); */

				foreach($dimensions as $dim){
					unlink($dim[2]);
					crop_algorithm($location, $dim[2].$this->id.'.jpg', $dim[0], $dim[1]);
				} 

				// Prebaci na ovbljavljeno glavna slika = true
				$db->query("UPDATE n_post SET naslovna = 1 WHERE id = {$this->id}");
				$this->naslovna = 1;

				echo 'k';
		}else{

			echo 'nije;';
		}
	}

	// Osposobi galeriju
	public function gallery_on(){
		global $db;

		$db->query("UPDATE n_post SET galerija = 1 WHERE id = {$this->id}");
	}

	// Upload galerije
	public function upload_to_gallery($location, $i){
		global $db;
		global $media_url;
		global $media_path;

		global $gal_glavna;
		global $gal_thumb;

		// Kopiranje originalne slike i snimanje thumbnaila
		gallery_save($location, $gal_glavna.$this->id.'.'.$i.'.jpg');
		crop_algorithm($location, $gal_thumb.$this->id.'.'.$i.'.jpg', 120, 120);

		if($i == 1) $this->gallery_on();

		// Upload galerije
		$db->query("INSERT INTO n_galerije (id, post, broj) VALUES ('null', {$this->id}, {$i})");
	}

	

	// Prikazi thumbnails 
	public function gal_thumbnails_show(){
		global $db;
		global $gal_th_url;

		?><div style="height:240px;overflow:auto;margin-top:10px;"><?php
		$thumbs = $db->query("SELECT broj FROM n_galerije WHERE post = {$this->id}");
		while($row = $thumbs->fetch_assoc()){
			echo '<div style="width:100px;position:relative;float:left;margin:10px 10px 0px 0px;" class="gal-pre-'.$row['broj'].'"><img style="width:100px;" src="'.$gal_th_url.$this->id.'.'.$row['broj'].'.jpg" /><button class="btn btn-xs btn-danger" onclick="obrisi_gal('.$this->id.', '.$row['broj'].');" style="position:absolute;z-index:10;top:5px;right:5px;"><i class="glyphicon glyphicon-remove"></i></button></div>';
		}
		?></div><?php
	}

}

?>