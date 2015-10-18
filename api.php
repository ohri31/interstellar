<?php
  require_once('brains/global.php');
  require_once('brains/class/api.php');

  switch($_GET['api']){
    case 'login':
      $api = new Api;
      $api->generate_user_login($_GET['email'], $_GET['pass']);
    break;

    case 'register':
      $api = new Api;
      $json = $_GET;
      $api->register_user($json);
    break;

    case 'add':
      $api = new Api;
      $api->add_friend($_GET['ko'], $_GET['koga']);
    break;

    case 'friends':
      $api = new Api;
      $api->who_are_my_friends($_GET['me']);
    break;

    case 'add_mitter':
      $api = new Api;
      $data = $_GET;
      $api->add_mitter($data);
    break;

    case 'list_mitter':
      $api      = new Api;
      $invites  = (int)$_GET['invites'];
      $me       = (int)$_GET['me'];
      $api->list_mitter($invites, $me);
    break;

    case 'pick_mitter':
      $api      = new Api;
      $who      = (int)$_GET['who'];
      $mitter   = (int)$_GET['mitter'];
      $lokacija = $_GET['lokacija'];
      $vrijeme  = $_GET['vrijeme'];
      $api->pick_mitter($who, $mitter, $vrijeme, $lokacija);
    break;

    case 'mitter':
      $api      = new Api;
      $mitter   = $_GET['mitter'];
      $invite   = $_GET['invite'];
      $api->show_mitter($mitter, $invite);
    break;
  }
?>
