<?php

	$url_home = 'http://192.241.189.218/beta/hangaar/quatronic/';
	$dir_root = '/var/www/html/beta/hangaar/quatronic/';

	$media_url = "http://192.241.189.218/media/";
	$media_path = "/var/www/html/media/";

	$db_host = 'localhost';
	$db_user = 'root';
	$db_pass = 'kasaba1905';
	$db_name = 'hangaar';
	
	// Media locations
	$v_location = $media_path.'mediax.v_';
	$s_location = $media_path.'mediax.s_';
	$k_location = $media_path.'mediax.k_';

	$vmedia_url = $media_url.'mediax.v_';
	$smedia_url = $media_url.'mediax.s_';
	$kmedia_url = $media_url.'mediax.k_';

	$gal_glavna = $media_path.'mediax.gal.';
	$gal_thumb  = $media_path.'mediax.gal.thumb.';

	$gal_gl_url = $media_url.'mediax.gal.';
	$gal_th_url = $media_url.'mediax.gal.thumb.';

	/*  Dimensions of the main image
		Par1: image width;
		Par2: image height;
		Par3: image location;
	*/
	$dimensions = array(
		array(640, 320, $v_location),
		array(320, 320, $s_location)
	);


	// Privilegije
	$privilegije = array("", "Admin", "Moderator", "Autor");

	// Meni
	$meni = array("Iskljuceno", "Ukljuceno");
?>