<?php
  require_once('brains/global.php');

  $page   = 1;
  $range  = 20;
  $total  = $db->query("SELECT id FROM n_tasks")->num_rows;
  $url    = $url_home.'tasks.php';
  $addition = "?page=";

  if(isset($_GET['page'])) $page   = (int)$_GET['page'];
  $begin  = ($page - 1) * $range;

  if(isset($_POST['dodaj'])){
      $zadatak  = $db->escape_string($_POST['zadatak']);
      $korisnik = $db->escape_string($_POST['korisnik']);
      $status   = $db->escape_string($_POST['status']);
      $deadline = $db->escape_string($_POST['deadline']);

      if($deadline == '') $deadline = 0;
        else $deadline = strtotime($deadline);

    if($_POST['who'] == 0){
      $db->query("INSERT INTO n_tasks (id, task, user, status, deadline) VALUES ('null', '{$zadatak}', {$korisnik}, {$status}, {$deadline})");
    }else{
      $who = (int)$_POST['who'];

      $db->query("UPDATE n_tasks SET 
        task = '{$zadatak}',
        user = {$korisnik},
        status = {$status}, 
        deadline = {$deadline}
        WHERE id = {$who}");

    }

    header('Location: '.$url_home.'tasks.php');
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
            <h4>Lista zadataka</h4>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td class="col-md-5"><b>Zadatak</b></td>
                  <td class="col-md-3"><b>Korisnik</b></td>
                  <td class="col-md-2"><b>Deadline</b></td>
                  <td class="col-md-2" style="text-align:right;"><b>Akcija</b></td>
                </tr>
              </thead>
              <tbody>
                <?php
                  $posts = $db->query("SELECT n_tasks.id, n_tasks.finished, n_tasks.deadline, n_tasks.status, n_tasks.task, n_users.ime, n_users.prezime, n_tasks.deadline FROM n_tasks LEFT JOIN n_users ON n_tasks.user = n_users.id
                    ORDER BY n_tasks.id DESC LIMIT {$begin}, 20");
                  while($row = $posts->fetch_assoc()){
                    ?>
                      <tr <?php if($row['deadline'] < time() && $row['deadline'] != 0) echo 'style="background:#f2dede;color:#a94442;border-color:#ebccd1;"'; ?>>
                        <td class="col-md-5">
                          <?php
                            if($row['finished'] == 1) echo '<i class="glyphicon glyphicon-ok" style="margin-right:5px;color:#5cb85c;"></i>';
                              else echo '<i class="glyphicon glyphicon-time" style="margin-right:5px;"></i>';

                            if($row['status'] == 1) echo '<i class="glyphicon glyphicon-globe" ></i>';
                              else echo '<i class="glyphicon glyphicon-lock"></i>';
                          ?>
                          <?=$row['task'];?>
                        </td>
                        <td class="col-md-3"><?=$row['ime'];?> <?=$row['prezime'];?></td>
                        <td class="col-md-2">
                          <?php
                            if($row['deadline'] != 0) echo date('d.m.Y', $row['deadline']);
                              else echo 'Nema deadline';
                          ?>
                        </td>
                        <td class="col-md-2">
                          <button class="btn btn-xs btn-danger pull-right" onclick="remove_task(<?=$row['id'];?>);"><i class="glyphicon glyphicon-remove"></i></button>
                          <a href="javascript:edit_task(<?=$row['id'];?>);" class="btn btn-xs btn-success pull-right" style="margin-right:5px;"><i class="glyphicon glyphicon-edit"></i></a>
                          
                          <?php if($row['finished'] == 0 && ($row['deadline'] > time() || $row['deadline'] == 0)): ?>
                            <button class="btn btn-xs btn-default pull-right" onclick="end_task(<?=$row['id'];?>);" style="margin-right:5px;"><i class="glyphicon glyphicon-ok"></i></button>
                          <?php endif ;?>

                          <?php if($row['finished'] == 1 && ($row['deadline'] > time() || $row['deadline'] == 0)): ?>
                            <button class="btn btn-xs btn-primary pull-right" onclick="open_task(<?=$row['id'];?>);" style="margin-right:5px;"><i class="glyphicon glyphicon-remove"></i></button>
                          <?php endif ;?>
                        </td>
                      </tr>
                    <?php
                  }
                ?>
              </tbody>
            </table>

            <!-- Paginacija ove stranice -->
            <?php
              if($total > 20)
                echo pagination($page, $range, $total, $url, $addition);
            ?>

            
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
          <?=zavrseni_zadaci();?>

          <div class="line-spacer"></div>

          <form action="" method="post">
            <div class="form-group">
                <label for="ime">Zadatak</label>
                <textarea class="form-control" id="zadatak" name="zadatak" placeholder="Zadatak" style="height:100px;"></textarea>
            </div>

            <div class="form-group">
                <label for="ime">Korisnik</label>
                <select name="korisnik" id="korisnik" class="form-control" >
                  <?php
                    $users = $db->query("SELECT * FROM n_users");
                    while($row = $users->fetch_assoc()){
                      ?>
                        <option value="<?=$row['id'];?>"><?=$row['ime'].' '.$row['prezime'];?></option>
                      <?php 
                    } 
                  ?>
                
                </select>
            </div>

             <div class="form-group">
                <label for="ime">Status zadatka</label>
                <select name="status" id="status" class="form-control" >
                  <option value="1">Javni zadatak</option> 
                  <option value="2">Privatni zadatak</option>               
                </select>
            </div>

            <div class="form-group">
                <label for="ime">Deadline</label>
                <input type="text" class="form-control" id="deadline" name="deadline" placeholder="Ukoliko je prazno, zadatak nema deadline" />
            </div>            

            <input type="hidden" name="who" id="who" value="0" />

            <button class="btn btn-success pull-right" name="dodaj">Snimi</button>
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
   
          $('#deadline').datepicker({
            format: "dd.mm.yyyy",
            orientation: "top auto",
            todayHighlight: true
          });

          function edit_task(id){
            $.post('action.php', {action: 'get_task', id: id}, function(res){
              user = JSON.parse(res);
              $('#who').val(user['id']);
              $('#zadatak').val(user['task']);
              $('#korisnik').val(user['user']);
              $('#status').val(user['status']);
              $('#deadline').val(user['deadline']);
            });
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
