<?php
/*
	#eventmaping

	Project started: 23.09.2014

	Dev: Mirza Ohranovic

	Motivation: Mirza Ohranovic
*/
	session_start();

	$debug = true;
	//header("Content-Type: text/xml; charset=utf-8");

	date_default_timezone_set("Europe/Sarajevo");	

	if($debug) { 
		error_reporting(E_ALL);
 		ini_set('display_errors');
	}

	//

	include_once 'config.php';
	include_once 'functions.php';

	require_once('class/post.php');
	require_once('class/user.php');

	$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
	$db->set_charset("utf8");
	

	$user = new User;	
	$post = new Post;

	// Check if logged
	// $need_login - array that contains all the file names that need login
	$need_login = array(
		"post.php",
		"list.php",
		"index.php"
	);

	$current = basename($_SERVER['PHP_SELF']); 

	if(in_array($current, $need_login)){
		check_if_logged();
	}



?>