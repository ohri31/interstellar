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
		public $api_name 	= null;
		public $amount 		= null;
		
		public $title 		= true;
		public $description = true;
		public $content 	= true;
		public $date 		= true;
		public $label 		= true;
		public $tags 		= false;
		public $order_show 	= false;

		public $time_written 	= true;
		public $time_published 	= true;

		public $published 	= true;
		public $main_image 	= true;

		public $galleries	= true;

		public function generate_api_content($query = false){
			global $db;
			global $gal_gl_url;
			global $gal_th_url;
			global $media_url;
			
			/* Order of the elements */
			if($this->order_show) $order = 1;

			$api = array();
			$b = 0;

			if(!$query) $posts = $db->query("SELECT * FROM n_post");
				else 	$posts = $db->query("SELECT * FROM n_post ".$query);


			while($row = $posts->fetch_assoc()){
					if($this->title) 		$api[$b]['naslov'] 	= stripslashes($row['naslov']);
					if($this->description) 	$api[$b]['uvod']	= nl2br($row['uvod']);
					if($this->content)		$api[$b]['sadrzaj']	= nl2br(str_replace('<br />', PHP_EOL, $row['sadrzaj']));
					if($this->date)			$api[$b]['datum']	= date('d.m.Y.', strtotime($row['datum']));
					if($this->label)		$api[$b]['label']	= stripslashes(strtoupper($row['label']));

					if($this->time_written) 	$api[$b]['vrijeme_objave'] = $row['time'];
					if($this->time_published) 	$api[$b]['vrijeme_pisanja'] = $row['time_saved'];

					if($this->published) 	$api[$b]['objavljeno'] = $row['objavljeno'];

					if($this->main_image)	$api[$b]['naslovna_slika'] = $media_url.'media.main_'.$row['id'].'.jpg';

					/* Just for hangaar dynamic*/
					$api[$b]['v_slika'] = $media_url.'mediax.v_'.$row['id'].'.jpg';
					$api[$b]['s_slika'] = $media_url.'mediax.s_'.$row['id'].'.jpg';

					if($this->order_show){
						if($order == 1 || $order == 4) $api[$b]['dyn_slika'] = $media_url.'mediax.v_'.$row['id'].'.jpg';
							else $api[$b]['dyn_slika'] = $media_url.'mediax.s_'.$row['id'].'.jpg';
					}

					/* Return articles order */
					if($this->order_show){
						$api[$b]['order'] = $order;
						$order++;
					}

					if($this->galleries){
						if($row['galerija'] == 1){

							$api[$b]['galerija'] = 1;

							$i = 0;
							$gle = $db->query("SELECT broj FROM n_galerije WHERE post = {$row['id']}");

							while($wor = $gle->fetch_assoc()){
								$api[$b]['galerija_items'][$i] 		= stripslashes($gal_gl_url.$row['id'].'.'.$wor['broj'].'.jpg');
								$api[$b]['galerija_items_th'][$i]	= stripslashes($gal_th_url.$row['id'].'.'.$wor['broj'].'.jpg');
								$i++;
							}
						}else{ $api[$b]['galerija'] = 0; }
					}

					if($this->tags){
						$get_tags = $db->query("SELECT n_tag.tag as item FROM n_tag 
							LEFT JOIN n_tag_post ON n_tag.id = n_tag_post.tag 
							LEFT JOIN n_post ON n_post.id = n_tag_post.post 
							WHERE n_post.id = {$row['id']}");

						$i = 0;
						while($m = $get_tags->fetch_assoc()){
							$api[$b]['tags'][$i] = $m['item'];
							$i++;
						}
					} 

					$b++;
			}

			echo prettyPrint(json_encode($api));
		}

	}
?>