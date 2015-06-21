<?php
  require_once('brains/global.php');

  $page   = 1;
  $range  = 20;
  $total  = $db->query("SELECT id FROM n_post")->num_rows;
  $url    = $url_home.'list.php';
  $addition = "?page=";

  if(isset($_GET['page'])) $page   = (int)$_GET['page'];
  $begin  = ($page - 1) * $range;
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
        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12" >
          <h3 style="margin-top:0px;margin-bottom:20px;">Postovi </h3>

          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="col-md-6 col-sm-5"><b>Naslov</b></td>
                <td class="col-md-2 col-sm-2"><b>Kategorija</b></td>
                <td class="col-md-2 col-sm-2"><b>Info</b></td>
                <td class="col-md-2 col-sm-3" style="text-align:right;"><b>Akcija</b></td>
              </tr>
            </thead>
            <tbody>
              <?php
                $posts = $db->query("SELECT n_post.*, n_kategorije.naziv FROM n_post LEFT JOIN n_kategorije ON n_post.kategorija = n_kategorije.id ORDER BY n_post.id DESC LIMIT {$begin}, 20");
                while($row = $posts->fetch_assoc()){
                  ?>
                    <tr>
                      <td class="col-md-6 col-sm-5">
                        <?php if($row['objavljeno'] == 0) echo '<i class="glyphicon glyphicon-pencil" style="color:#d5d5d5;"></i>'; ?> 
                        <?php if($row['objavljeno'] == 1) echo '<i class="glyphicon glyphicon-ok" style="color:#5cb85c;"></i>'; ?> 
                        <?=$row['naslov'];?>
                      </td>
                      <td class="col-md-2 col-sm-2">
                        <?=$row['naziv'];?>
                      </td>
                      <td class="col-md-2 col-sm-2">
                        <?php
                          $ima = false;
                          if($row['izdvojeno'] == 1){
                            echo '<i class="glyphicon glyphicon-star" style="color:#428bca;"></i>';
                            $ima = true;
                          }

                          if($row['galerija'] == 1){
                            echo '<i class="glyphicon glyphicon-camera"></i>';
                            $ima = true;
                          }

                          if($row['time'] > time()){
                            echo '<i class="glyphicon glyphicon-time"></i>';
                            $ima = true;
                          }

                          if(!$ima) echo '/';
                        ?>
                      </td>
                      <td class="col-md-2 col-sm-3">
                        <button class="btn btn-xs btn-default pull-right"><i class="glyphicon glyphicon-link" style="color:#428bca;"></i></button>
                        <a href="<?=$url_home;?>post.php?post=<?=$row['id'];?>" class="btn btn-xs btn-success pull-right" style="margin-right:5px;"><i class="glyphicon glyphicon-edit"></i></a>
                        <?php 
                          if($row['izdvojeno'] == 1): 
                        ?>
                          <button class="btn btn-xs btn-info pull-right" style="margin-right:5px;" onclick="ukini_promo(<?=$row['id'];?>);"><i class="glyphicon glyphicon-remove"></i></button>
                        <?php endif; ?>

                        <?php 
                          if($row['izdvojeno'] == 0): 
                        ?>
                          <button class="btn btn-xs btn-primary pull-right" style="margin-right:5px;" onclick="promo(<?=$row['id'];?>);"><i class="glyphicon glyphicon-star"></i></button>
                        <?php endif; ?>
                        
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

        <div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
          <h3 for="naslov" style="margin-bottom:20px;margin-top:0px;">Legenda</h3>
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td class="col-md-2" style="text-align:center;"><i class="glyphicon glyphicon-pencil" style="color:#d5d5d5;"></i></td>
                <td class="col-md-10" style="text-align:center;">U pripremi</td>
              </tr>
              <tr>
                <td class="col-md-2" style="text-align:center;"><i class="glyphicon glyphicon-ok" style="color:#5cb85c;"></i></td>
                <td class="col-md-10" style="text-align:center;">Objavljeno</td>
              </tr>
              <tr>
                <td class="col-md-2" style="text-align:center;"><i class="glyphicon glyphicon-star" style="color:#428bca;"></i></td>
                <td class="col-md-10" style="text-align:center;">Promovisano</td>
              </tr>
              <tr>
                <td class="col-md-2" style="text-align:center;"><i class="glyphicon glyphicon-camera"></i></td>
                <td class="col-md-10" style="text-align:center;">Galerija</td>
              </tr>
              <tr>
                <td class="col-md-2" style="text-align:center;"><i class="glyphicon glyphicon-time"></i></td>
                <td class="col-md-10" style="text-align:center;">Planirano</td>
              </tr>
            </tbody>
          </table>
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
