<?php
function crop_algorithm($location, $save_to, $set_width, $set_height){
	// Algoritam developed by Mirza Ohranovic | mirza.ohranovic@gmail.com
	// Coded by Mirza Ohranovic
	// October 2014. 

	$image = new Imagick;
	$image->readImage($location);

	// Get size
	list($width, $height) = getimagesize($location);

		$hratio 	= $set_height / $height;
		$wratio		= $set_width / $width;

		if($wratio < 1 && $hratio < 1){
			$odabran = min($hratio, $wratio);

			if($odabran == $hratio){
				$nova = $width * $wratio;
				$image->scaleImage($nova, 0);
			}elseif($odabran == $wratio){
				$nova = $height * $hratio;
				$image->scaleImage(0, $nova);
			}
		}elseif($wratio < 1 && $hratio >= 1){
			$nova = $height * $hratio;
			$image->scaleImage(0, $nova);
		}elseif($wratio >= 1 && $hratio < 1){
			$nova = $width * $wratio;
			$image->scaleImage($nova, 0);
		}else{
			$image->scaleImage($set_width, 0);
		}

		$v = $image->getImageGeometry();
			$w = $v['width'];
			$h = $v['height'];

		$x = floor(($w - $set_width) / 2);
		$y = floor(($h - $set_height) / 2);

		if($x <= 1) $x = 0;
		if($y <= 1) $y = 0;
				
		$image->cropImage($set_width, $set_height, $x, $y);
		$image->writeImage($save_to);
		$image->clear();
		$image->destroy();
}

// Provjera velicine ya galeriju
function gallery_save($location, $to){

	list($width, $height) = getimagesize($location);

	if($width > 1024){

		$image = new Imagick;
		$image->readImage($location);
		$image->scaleImage(960, 0);
		$image->writeImage($to);

		$image->clear();
		$image->destroy();

	}elseif($height > 1024){
		
		$image = new Imagick;
		$image->readImage($location);
		$image->scaleImage(0, 960);
		$image->writeImage($to);

		$image->clear();
		$image->destroy();

	}else{
		copy($location, $to);
	}
}

/* Remove image */
function remove_img($location){
	unlink($location);
}

/* Remove image from databease */
function remove_img_db($gal, $broj){
	global $db;

	$db->query("DELETE FROM n_galerije WHERE post = {$gal} AND broj = {$broj}");
}

/* Ispis kategorija */
function kategorije_dropdown(){
	global $db;

	$kategorije = $db->query("SELECT id, naziv FROM n_kategorije");
	while($row = $kategorije->fetch_assoc()){
		?><option value="<?=$row['id'];?>"><?=$row['naziv'];?></option><?php
	}

}

/* Funkcija za paginaciju
 * Napisana nekada u augustu/septmebru 2014
 * inspired by: klix.ba && cms.klix.ba paginacija
 * autor: mirza ohranovic
 */
// Paginacija
function pagination($page, $range, $total, $url, $addition){

	$pages = ceil($total / $range);

	?><nav>
  		<ul class="pagination">	
  	<?php
	if($pages < 8){
		for($i = 1; $i <= $pages; $i++){
			if($i == $page){
				?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>" disabled><?=$i;?></a></li><?php
			}else{
				?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>"><?=$i;?></a></li><?php
			}
		}
	}else{
		$drugi = $pages - 3;
		if(($page >= 1 && $page <= 3) || ($page >= $pages - 4)){
			for($i = 1; $i <= 4; $i++){
				if($i == $page){
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>" disabled><?=$i;?></a></li><?php
				}else{
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>"><?=$i;?></a></li><?php
				}
			}

			?><li><a href="javascript:void(0);" disabled>...</a></li><?php	

			for($i = $drugi; $i <= $pages; $i++){
				if($i == $page){
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>" disabled><?=$i;?></a></li><?php
				}else{
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>"><?=$i;?></a></li><?php
				}
			}
		}else{
			?><li><a href="<?=$url;?><?=$addition;?>" class="kockica">1</a></li><?php
			?><li><a href="javascript:void(0);" disabled>...</a></li><?php
			
			for($i = $page - 1; $i <= $page + 3; $i++){
				if($i == $page){
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>" disabled><?=$i;?></a></li><?php
				}else{
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>"><?=$i;?></a></li><?php
				}
			}	

			?><li><a href="javascript:void(0);" disabled>...</a></li><?php	

			for($i = $drugi; $i <= $pages; $i++){
				if($i == $page){
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>" disabled><?=$i;?></a></li><?php
				}else{
					?><li><a href="<?=$url;?><?=$addition;?><?=$i;?>"><?=$i;?></a></li><?php
				}
			}
		}
	}
	?>
		</ul>
	</nav>
	<?php
}

/*
	* Password generator for ncms
	* Bitno je radi sigurnosti 
	* 28.12.2014.
	* op.a. iskreno se nadam da ce ovo imati neku funkciju, ali eto :)
*/

function password_gen($password){
	$password = 'stavi_ispred'.$password;
	$password .= '19.05.1995.';
	$password .= 'haj_sad_probiji ako mozes';

	$password = md5($password);
	$password .= 'jos_nesto_posalji'.$password;

	$password = md5($password);

	return $password;
}

/* 
	* Provjera da li je korisnik ulogoban
	* 28.12.2014.
*/

function check_if_logged(){
	global $user;
	
	if(isset($_SESSION['user'])){
		$user->id = (int)$_SESSION['user'];
		$user->user_info();
	}else{
		global $url_home;

		header('Location: '.$url_home.'login.php');
	}
}

/* 
	* Racunanje zavrsenih zadataka
	* 29.12.2014.
*/
function zavrseni_zadaci(){
	global $db;

	$ukupno 	= 0;
	$zavrseno 	= 0;

	$get = $db->query("SELECT finished FROM n_tasks");
	while($row = $get->fetch_assoc()){
		$ukupno++;
		if($row['finished'] == 1) $zavrseno++;
	}

	$procentualno = $zavrseno / $ukupno * 100;
	$procentualno = ceil($procentualno);

	?>
		<h2><?=$procentualno;?>% <small>Zadataka završeno</small></h2>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?=$procentualno;?>%;">
            </div>
        </div>
	<?php
}

/* Stack overflow - MAJKA! */
function prettyPrint($json)
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}
?>