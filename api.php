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
  }
?>
