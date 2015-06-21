<?php
  require_once('brains/global.php');

  if(isset($_POST['dodaj'])){
      $ime      = $db->escape_string($_POST['ime']);
      $prezime  = $db->escape_string($_POST['prezime']);
      $email    = $db->escape_string($_POST['email']);
      $password = $db->escape_string($_POST['password']);

      $password = password_gen($password);

      $privilegije = (int)$_POST['privilegije'];

    if($_POST['who'] == 0){
      $db->query("INSERT INTO n_users (id, ime, prezime, mail, password, privilegije) VALUES ('null', '{$ime}', '{$prezime}', '{$email}', '{$password}', {$privilegije})");
    }else{
      $who = (int)$_POST['who'];

      $db->query("UPDATE n_users SET 
        ime = '{$ime}',
        prezime = '{$prezime}',
        mail = '{$email}', 
        password = '{$password}',
        privilegije = {$privilegije}
        WHERE id = {$who}");
    }

    header('Location: '.$url_home.'korisnici.php');
  }
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/datepicker.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
     <div class="navbar navbar-fixed-top background-header" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a style="padding:0px!important;" class="navbar-brand" href="<?=$url_home;?>"><img src="<?=$url_home;?>img/quatronic_logo.png" /></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav" style="color:#0B1137!important;">
            <li class="active"><a href="<?=$url_home;?>" style="color:#0B1137!important;"><i class="glyphicon glyphicon-dashboard"></i> Dashboard</a></li>
            <li><a href="<?=$url_home;?>post.php" style="color:#0B1137!important;"><i class="glyphicon glyphicon-pencil"></i> Novi post</a></li>
            <li><a href="<?=$url_home;?>list.php" style="color:#0B1137!important;"><i class="glyphicon glyphicon-th-list"></i> Postovi</a></li>
            <li><a href="<?=$url_home;?>korisnici.php" style="color:#0B1137!important;"><i class="glyphicon glyphicon-user"></i> Korisnici</a></li>
            <li><a href="<?=$url_home;?>kategorije.php" style="color:#0B1137!important;"><i class="glyphicon glyphicon-file"></i> Kategorije</a></li>
            <li><a href="<?=$url_home;?>tasks.php" style="color:#0B1137!important;"><i class="glyphicon glyphicon-tasks"></i> Zadaci</a></li>
            <li class="pull-right"><a href="<?=$url_home;?>logout.php" style="color:#0B1137!important;">Log out</a></li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <div class="container main-container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" >
            <h4>Korisnici</h4>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td class="col-md-4"><b>Ime i prezime</b></td>
                  <td class="col-md-3"><b>E-mail</b></td>
                  <td class="col-md-2"><b>Privilegije</b></td>
                  <td class="col-md-3" style="text-align:right;"><b>Akcija</b></td>
                </tr>
              </thead>
              <tbody>
                <?php
                  $posts = $db->query("SELECT * FROM n_users");
                  while($row = $posts->fetch_assoc()){
                    ?>
                      <tr>
                        <td class="col-md-4"><?=$row['ime'].' '.$row['prezime'];?></td>
                        <td class="col-md-3"><?=$row['mail'];?></td>
                        <td class="col-md-2">
                          <?=$privilegije[$row['privilegije']];?>
                        </td>
                        <td class="col-md-3">
                          <button class="btn btn-xs btn-danger pull-right" onclick="remove_user(<?=$row['id'];?>);"><i class="glyphicon glyphicon-remove"></i></button>
                          <a href="javascript:edit_user(<?=$row['id'];?>);" class="btn btn-xs btn-success pull-right" style="margin-right:5px;"><i class="glyphicon glyphicon-edit"></i></a>
                        </td>
                      </tr>
                    <?php
                  }
                ?>
              </tbody>
            </table>

            
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <h4>Korisnik</h4>
          <div class="line-spacer"></div>
          <form action="" method="post">
            <div class="form-group">
                <label for="ime">Ime</label>
                <input type="text" class="form-control" id="ime" name="ime" placeholder="Ime korisnika" />
            </div>

            <div class="form-group">
                <label for="ime">Prezime</label>
                <input type="text" class="form-control" id="prezime" name="prezime" placeholder="Prezime korisnika" />
            </div>

            <div class="form-group">
                <label for="ime">E-mail</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="E-mail" />
            </div>

            <div class="form-group">
                <label for="ime">Password</label>
                <input type="text" class="form-control" id="password" name="password" placeholder="Password" />
            </div>

            <div class="form-group">
                <label for="tagovi">Privilegije</label>
                <select name="privilegije" id="privilegije" class="form-control" >
                  <option value="3">Autor</option>
                  <option value="1">Admin</option>
                  <option value="2">Moderator</option>
                </select>

                 <div style="clear:both;"></div>
            </div>

            <input type="hidden" name="who" id="who" value="0" />

            
            <button class="btn btn-success pull-right" name="dodaj">Snimi</button>
            <button class="btn btn-danger pull-right" name="reset" style="margin-right:10px;">Reset</button>
          </form>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; NCMS 2014</p>
      </footer>
    </div> <!-- /container -->        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.1.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/vendor/bootstrap-datepicker.js"></script>

        <script src="js/main.js"></script>

        <script>
          function edit_user(id){
            $.post('action.php', {action: 'get_user', id: id}, function(res){
              user = JSON.parse(res);
              $('#who').val(user['id']);
              $('#ime').val(user['ime']);
              $('#prezime').val(user['prezime']);
              $('#email').val(user['email']);
              $('#password').val('');
              $('#privilegije').val(user['privilegije']);
            });
          }

          function remove_user(id){
            if(confirm("Da li ste sigurni da želite obrisati korisnika?") == true){
              $.post('action.php', {action: 'remove_user', id: id}, function(){
                location.reload();
              });
            }else{
              return false;
            }
          }
        </script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
    </body>
</html>
