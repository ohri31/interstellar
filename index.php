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
            <div class="center960">
                <a href="#"><img src="img/logo.png" /></a>
            </div>
        </section>


        <section id="main">
            <!-- Dinamicna naslovnica -->
            <div id="dyn" ng-controller="naslovnaCntrl" ng-switch on="clanak">
                <div ng-class="(clanak.order == 1 || clanak.order == 4) ? 'dynwide' : 'dynsmall'" ng-repeat="clanak in dyn">
                    <div class="shadow"></div>
                    <img src="{{ clanak.dyn_slika }}" />
                    <h1>
                        <label ng-if="clanak.label != ''">{{ clanak.label }}</label><br />
                        {{ clanak.naslov }}
                            <div style="clear:both;"></div>
                        <a href="#" ng-repeat="tags in clanak.tags">{{ '#' + tags }}</a>
                    </h1>
                </div>
                <div style="clear:both;"></div>
            </div>

            <!-- Hangaar republic part -->
            <div id="popularno" ng-controller="repCntrl">
                <div class="heading">hangaar.republic</div>

                <div class="pitem" ng-repeat="reps in rep">
                    <hr /><a href="#">{{ reps.naslov }}</a>
                </div>

                <div style="clear:both;"></div>
            </div>
    
            <!-- Dva lijeva članka -->
            <div id="left-mid" ng-controller="leftCntrl">
                <div class="lmitem" ng-repeat="clanak in levo">
                    <div class="image" ng-if="clanak.order == 1">
                        <img src="{{ clanak.s_slika }}" />
                    </div>

                    <div class="details">
                        <h3><a href="#">{{ clanak.naslov }}</a></h3>
                        <p>{{ clanak.datum }}</p>
                        <p>{{ clanak.uvod }}</p>
                        <a class="nastavi" href="#">nastavi čitati...</a>
                    </div>

                     <div class="image" ng-if="clanak.order == 2">
                        <img src="{{ clanak.s_slika }}" />
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>

            <div id="right-mid">
                <div class="adspace">

                </div>

                <div class="subdiv">

                </div>
                <div style="clear:both;"></div>
            </div>

            <div id="podcast" ng-controller="podcastCntrl">
                <div class="dynsmall" ng-repeat="clanak in podcast">
                    <div class="shadow"></div>
                    <img src="{{ clanak.s_slika }}" />
                    <h1>
                        <label ng-if="clanak.label != ''">{{ clanak.label }}</label><br />
                        {{ clanak.naslov }}
                            <div style="clear:both;"></div>
                        <a href="#" ng-repeat="tags in clanak.tags">{{ '#' + tags }}</a>
                    </h1>
                </div>
              
                <div style="clear:both;"></div>
            </div>
            

            <div id="parntership-programme">

            </div>

            <div id="footer">

            </div>
           
        </section>

        <?php require('inc/html_foot.php'); ?>
        <script type="text/javascript">
            angular.bootstrap(document.getElementById('dyn'), ['dynHangaar']);
            angular.bootstrap(document.getElementById('popularno'), ['repHangaar']);
            angular.bootstrap(document.getElementById('left-mid'), ['leftHangaar']);
            angular.bootstrap(document.getElementById('podcast'), ['podcastHangaar']);
        </script>
    </body>
</html>
