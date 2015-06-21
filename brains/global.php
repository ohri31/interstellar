<?php
/*
	#eventmaping

	Project started: 23.09.2014

	Dev: Mirza Ohranovic

	Motivation: Mirza Ohranovic
*/
	session_start();

	$debug = true;

	if($debug) { 
		error_reporting(E_ALL);
 		ini_set('display_errors', 1);
	}

	include_once 'config.php';
	include_once 'functions.php';

	require_once('class/event.php');

	$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
	$db->set_charset("utf8");

	$event = new Event;

?>