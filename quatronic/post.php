<?php
  require_once('brains/global.php');

  $sati = date('H');
  $minuta = date('i');
  $koji_datum = date('d.m.Y');

  // Dodavanje novog eventa u bazu podataka
  if(isset($_POST['snimi']) && !isset($_GET['post'])){
      // Info 
      $naslov   = $db->escape_string($_POST['naslov']);
      $uvod     = $db->escape_string($_POST['uvod']);
      $sadrzaj  = $db->escape_string($_POST['sadrzaj']);
      $tag      = $db->escape_string($_POST['tagovi']);
      $label    = $db->escape_string($_POST['label']);
      $kategorija = $db->escape_string($_POST['kategorija']);

    //  $sati    = (int)$_POST['h'];
    //  $minute = (int)$_POST['m'];

      $datum = $db->escape_string($_POST['datum']);
      $datum = date('Y-m-d', strtotime($datum));

      $vrijeme_objave = strtotime($sati.':'.$minute.' '.$datum);

      preg_match_all("/(#\w+)/", $tag, $tagovi);

      $post->save_post($naslov, $uvod, trim($sadrzaj), $label, $datum, $vrijeme_objave, $kategorija);
      $post->tag_post($tagovi[0]);

      if(!empty($_FILES['naslovna']['tmp_name'])){
        $location = $_FILES['naslovna']['tmp_name'];
        $post->main_image($location);
      }


      if(!empty($_FILES['galerija'])){
        
        // Redni broj slike, ako vec postoje uzmi za jedan veci od tog, ako ne uzmi 1
        $get_i = $db->query("SELECT broj FROM n_galerije WHERE post = {$post->id} ORDER BY broj DESC LIMIT 0,1")->fetch_assoc();
        if($get_i['broj'] == null || $get_i['broj'] == '') $i = 1;
          else $i = $get_i['broj'] + 1;

        foreach($_FILES['galerija']['tmp_name'] as $location){
          if(!empty($location)){
            $post->upload_to_gallery($location, $i);
            $i++;
          }
        }

        
      }

      $na = $post->id;
      header("Location: ".$url_home."post.php?post=".$na);

     /* if(!empty($_FILES['naslovna']['tmp_name'])){    
          $location = $_FILES['naslovna']['tmp_name'];
          $event->upload_image($location);    
      }   */
    }

    // Uzimanje podataka o trenutno odabranom postu
    if(isset($_GET['post'])){
      $id = (int)$_GET['post'];
      $post->query_post($id);

      if($post->time != 0){
        $vrijeme_objavljeno = $post->time;
        $sati = date('H', $vrijeme_objavljeno);
        $minuta = date('i', $vrijeme_objavljeno);
        $koji_datum = date('d.m.Y', $vrijeme_objavljeno);
      }



      $tagovano = '';
      foreach($post->tagovi as $tag){
        $tagovano .= '#'.$tag.' ';
      }
    }

    // Snimanje vec napravljenog posta
    if(isset($_POST['snimi']) && isset($_GET['post'])){
      // Info 
      $naslov   = $db->escape_string($_POST['naslov']);
      $uvod     = $db->escape_string($_POST['uvod']);
      $sadrzaj  = $db->escape_string($_POST['sadrzaj']);
      $tag      = $db->escape_string($_POST['tagovi']);
      $label    = $db->escape_string($_POST['label']);
      $kategorija = $db->escape_string($_POST['kategorija']);
      $datum  = $db->escape_string($_POST['datum']);

      preg_match_all("/(#\w+)/", $tag, $tagovi);

      $datum = date('Y-m-d', strtotime($datum));

      $sati    = (int)$_POST['h'];
      $minute = (int)$_POST['m'];

      $vrijeme_objave = strtotime($sati.':'.$minute.' '.$datum);

      $post->id = (int)$_GET['post'];
      $post->save_post($naslov, $uvod, trim($sadrzaj), $label, $datum, $vrijeme_objave, $kategorija, true);
      $post->tag_post($tagovi[0]);

      if(!empty($_FILES['naslovna']['tmp_name'])){
        $location = $_FILES['naslovna']['tmp_name'];
        $post->main_image($location);
      }

      if(!empty($_FILES['galerija'])){
        
        // Redni broj slike, ako vec postoje uzmi za jedan veci od tog, ako ne uzmi 1
        $get_i = $db->query("SELECT broj FROM n_galerije WHERE post = {$post->id} ORDER BY broj DESC LIMIT 0,1")->fetch_assoc();
        if($get_i['broj'] == null || $get_i['broj'] == '') $i = 1;
          else $i = $get_i['broj'] + 1;

        foreach($_FILES['galerija']['tmp_name'] as $location){
          if(!empty($location)){
            $post->upload_to_gallery($location, $i);
            $i++;
          }
        }

        
      }

      $na = $post->id;
      header("Location: ".$url_home."post.php?post=".$na);
    } 
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
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
        <form role="form" method="post" action="" enctype="multipart/form-data">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
          
            <div class="form-group">
              <label for="naslov">Naslov</label>
              <input type="text" class="form-control" id="naslov" name="naslov" placeholder="Unesite naslov" value="<?php if(isset($_GET['post'])) echo $post->naslov; ?>">
            </div>

            <div class="form-group">
              <label for="uvod">Uvod u post</label>
              <textarea class="form-control" id="uvod" name="uvod" placeholder="Unesite uvod" rows="3"><?php if(isset($_GET['post'])) echo stripslashes($post->uvod); ?></textarea>
            </div>

            <div class="form-group">
              <label for="sadrzaj">Sadržaj</label>
              <br />
                <button class="btn btn-default" style="margin-bottom:10px;" onclick="format_text('B');"><b>B</b></button>
                <button class="btn btn-default" style="margin-bottom:10px;" onclick="format_text('I');"><i>i</i></button>
                <button class="btn btn-default" style="margin-bottom:10px;" onclick="format_text('U');"><u>U</u></button>
              <textarea class="form-control" id="sadrzaj" name="sadrzaj" placeholder="Sadržaj posta" rows="9"><?php if(isset($_GET['post'])) echo $post->sadrzaj; ?></textarea>
            </div>

            <div class="form-group">
              <label for="tagovi">Tagovi</label>
              <textarea class="form-control" id="tagovi" name="tagovi" placeholder="Oznacavati sa prefiksom # npr. #tag #sarajevo #bosna" rows="2"><?php if(isset($tagovano)) echo $tagovano;?></textarea>
            </div>
          
        </div>

        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <div class="form-group">
              <label for="tagovi">Kategorija</label>
              <select name="kategorija" class="form-control" style="width:100%;">
                <?php echo kategorije_dropdown(); ?>
              </select>

            </div>

            <div class="form-group">
              <label for="vrijeme-objave">Label</label>
              <div style="clear:both;"></div>

               <input type="text" class="form-control" id="label" name="label" placeholder="Unesite label" value="<?php if(isset($_GET['post'])) echo $post->label; ?>">
              
              <div style="clear:both;"></div>
            </div>

            <div class="form-group">
              <label for="vrijeme-objave">Vrijeme objave</label>
              <div style="clear:both;"></div>

              <input type="text" class="form-control pull-left" style="width:50px; margin-right:6px; text-align:center;" id="sati" name="h" placeholder="HH" value="<?=$sati;?>">
              <input type="text" class="form-control pull-left" style="width:50px; margin-right:6px; text-align:center;" id="minuta" name="m" placeholder="MM" value="<?=$minuta;?>">

              <input type="text" class="form-control pull-left" style="width:100px; text-align:center;" id="date" name="datum" placeholder="MM" value="<?=$koji_datum;?>">
              
              <div style="clear:both;"></div>
            </div>
          <div class="form-group">
              <label for="vrijeme-objave">Naslovna slika</label><br />

                <button class="btn btn-success odabrano disabled" <?php if($post->naslovna == 0) echo 'style="display:none;float:left;margin-right:5px;"'; ?>><i class="glyphicon glyphicon-ok"></i></button>
                <a href="<?php if(isset($post->id)) echo $vmedia_url.$post->id.'.jpg'; ?>" target="_blank" class="btn btn-primary pogledaj" <?php if($post->naslovna == 0) echo 'style="display:none;float:left;margin-right:5px;"'; ?>><i class="glyphicon glyphicon-link"></i></a>

                <button class="btn btn-danger ne-odabrano disabled" <?php if($post->naslovna != 0) echo 'style="display:none;float:left;margin-right:5px;"'; ?>><i class="glyphicon glyphicon-warning-sign"></i></button>

              <button id="naslovna" class="btn btn-default">Odaberi sliku</button>
              <input type="file" class="naslovna" name="naslovna" style="display:none;" />
            </div>

            <div class="form-group">
              <label for="vrijeme-objave">Galerija</label><br />

                <button class="btn btn-success odabrano-gal disabled" <?php if($post->galerija == 0) echo 'style="display:none;float:left;margin-right:5px;"'; ?>><i class="glyphicon glyphicon-ok"></i></button>
                <button class="btn btn-danger ne-odabrano-gal disabled" <?php if($post->galerija != 0) echo 'style="display:none;float:left;margin-right:5px;"'; ?>><i class="glyphicon glyphicon-warning-sign"></i></button>

              <button class="btn btn-default" id="galerija" style="margin-right:10px;display:inline-block;"><i class="glyphicon glyphicon-picture"></i> Galerija </button>
              <input type="file" class="galerija" name="galerija[]" style="display:none;" multiple />

              <?php if($post->galerija != 0){ ?>
                <br />
                <?php echo $post->gal_thumbnails_show(); ?>
              <?php } ?>

              <div style="clear:both;"></div>
            </div>
        </div>
      </div>
            <div class="form-group">
              <button class="btn btn-success pull-right" name="snimi"><i class="glyphicon glyphicon-floppy-disk"></i> Snimi</button>
              <button class="btn btn-danger pull-right" style="margin-right:10px;" onclick="delete_post(<?=$post->id;?>);"><i class="glyphicon glyphicon-trash"></i> Obrisi</button>

              <?php if(isset($_GET['post'])){ ?>
                <?php if($post->objavljeno == 0): ?><button class="btn btn-primary pull-right" style="margin-right:10px;" onclick="objavi(<?=$post->id;?>);"><i class="glyphicon glyphicon-saved"></i> Objavi</button><?php endif; ?>
                <?php if($post->objavljeno == 1): ?><button class="btn btn-primary pull-right" style="margin-right:10px;" onclick="ukini_objava(<?=$post->id;?>);"><i class="glyphicon glyphicon-remove"></i> Ukini</button><?php endif; ?>
              <?php } ?>

              <?php if(isset($_GET['post'])){ ?>
                <?php if($post->promo == 0): ?><button class="btn btn-primary pull-right" style="margin-right:10px;" onclick="promo(<?=$post->id;?>);"><i class="glyphicon glyphicon-star"></i> Promo</button><?php endif; ?>
                <?php if($post->promo == 1): ?><button class="btn btn-primary pull-right" style="margin-right:10px;" onclick="ukini_promo(<?=$post->id;?>);"><i class="glyphicon glyphicon-remove"></i> Ukini promo</button><?php endif; ?>
              <?php } ?>
              
            </div>

            <div style="clear:both;"></div>

      </form>
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
          $('#date').datepicker({
            format: "dd.mm.yyyy",
            orientation: "top auto",
            todayHighlight: true
          });

          $(document).on('keyup paste change', '#sati', function(){
            if($(this).val() > 23 || $(this).val() < 0 || isNaN($(this).val())) $(this).val('00');
          });

          $(document).on('keyup paste change', '#minuta', function(){
            if($(this).val() > 59 || $(this).val() < 0 || isNaN($(this).val())) $(this).val('00');
          });

          $(document).on('click', '#naslovna', function(){
            $('.naslovna').trigger('click');
             event.preventDefault();
          });

          $(document).on('change', '.naslovna', function(){
            event.preventDefault();
            $('.odabrano').css('display', 'inline-block');
            $('.ne-odabrano').css('display', 'none');
          });

          $(document).on('click', '#galerija', function(){
            $('.galerija').trigger('click');
             event.preventDefault();
          });

          $(document).on('change', '.galerija', function(){
            event.preventDefault();
            $('.odabrano-gal').css('display', 'inline-block');
            $('.ne-odabrano-gal').css('display', 'none');
          });

          function obrisi_gal(gal, broj){
            event.preventDefault();
            $.post('action.php', {action: 'obrisi_gal', gal: gal, broj: broj}, function(res){
              $('.gal-pre-'+broj).remove();
            });
          }

          function delete_post(id){
            event.preventDefault();
            if(confirm("Da li ste sigurni da želite obrisati post?") == true){
              $.post('action.php', {action: 'obrisi_post', id: id}, function(res){
                window.location = 'list.php';
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
