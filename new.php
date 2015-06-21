<?php
    require_once('brains/global.php');

    // Dodavanje novog eventa u bazu podataka
    if(isset($_POST['snimi']) && !isset($_GET['event'])){
        // Info 
        $naslov = $db->escape_string($_POST['ev-naziv']);
        $opis   = $db->escape_string($_POST['ev-opis']);
        $tag    = $db->escape_string($_POST['ev-tagovi']);

        $dan    = (int)$_POST['dan'];
        $mjesec = (int)$_POST['mjesec'];

        $trenutni_mjesec = date('m');

        if($trenutni_mjesec < $mjesec){
            $godina = date('Y') + 1;
        }else{
            $godina = date('Y');
        }

        $datum = $godina.'-'.$mjesec.'-'.$dan;

        preg_match_all("/(#\w+)/", $tag, $tagovi);

        $event->save_event($naslov, $opis, $datum);
        $event->tag_event($tagovi[0]);

        if(!empty($_FILES['naslovna']['tmp_name'])){    
            $location = $_FILES['naslovna']['tmp_name'];
            $event->upload_image($location);    
        }
        
    }

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php require('inc/html_head.php'); ?>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Website header -->
        <section id="header">

            <ul class="nav-meni pull-right">
                <li>Home</li>
                <li>Music</li>
                <li>Events</li>
                <li>Clothing</li>
                <li>Travel</li>
            </ul>
        </section>

        <!-- Website container -->
        <section id="container">
            <!-- Left side website -->
            <div class="left-side pull-left" style="margin-top:20px;padding-bottom:30px;">

                <form action="" method="post" enctype="multipart/form-data" >
                 
                    <label class="label-input" for="naslov">Naziv dogadaja</label>
                    <input type="text" name="ev-naziv" class="new-input" placeholder="Naslov" id="input-naslov" />

                    <label class="label-input" for="naslov">Opis dogadjaaj</label>
                    <textarea class="new-input" name="ev-opis" placeholder="Opsi dogadjaja" id="input-sadrzaj"></textarea>

                        <div class="clear"></div>

                    <label class="label-input" for="naslov">Mjesec</label>
                    <div class="datum-holder">
                        <?php
                            // Za dan 
                            for($i = 1;$i <= 12;$i++){
                                ?>
                                <div class="small-date-pick mjesec <?php if($i % 14 == 0) echo 'mr0';?>"><?=$i;?></div>
                                <?php
                            }
                        ?>
                    </div>

                        <div class="clear"></div>

                    <label class="label-input dani-u-mjesecu" for="naslov" style="display:none;">Dan</label>
                    <div class="datum-holder dani-u-mjesecu" style="display:none;">
                        <?php
                            // Za dan 
                            for($i = 1;$i <= 31;$i++){
                                ?>
                                <div class="small-date-pick dan <?php if($i % 14 == 0) echo 'mr0';?>" style="display:none;"><?=$i;?></div>
                                <?php
                            }
                        ?>
                    </div>

                        <div class="clear"></div>
               
                    <label class="label-input" for="naslov">Tagovi</label>
                    <input type="text" class="new-input" name="ev-tagovi" placeholder="Tagovi" id="input-tag" />

                    <button id="trigger-naslovna" class="button">Odaberite fotografiju</button>
                    <input type="file" name="naslovna" id="naslovna-eventa" style="display:none;" />

                    <input type="hidden" name="dan" class="koji-dan" />
                    <input type="hidden" name="mjesec" class="koji-mjesec" />

                    <input type="submit" name="snimi" class="button btn-datum" value="Snimi" />
                </form>

                <div class="clear"></div>
               
            </div>


            <!-- Right side website -->
            <div class="right-side pull-right">
                <div class="status">
                    Priprema
                </div>
            </div>

            <div class="clear"></div>
        </section>



        <?php require('inc/html_foot.php'); ?>
    </body>
</html>
