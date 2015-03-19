<?php
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_TIME, 'de_DE@euro', 'de_DE', 'deu_deu');
    date_default_timezone_set('Europe/Berlin');
    $order = [
                ['id' => 'weather1', 'duration' => 10], 
                ['id' => 'weather2', 'duration' => 6], 
                ['id' => 'weather3', 'duration' => 6],  
                ['id' => 'weather4', 'duration' => 15], 
            ];

    $db = new SQLite3('news.sqlite');

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Segelclub Schwielochsee - Infoscreen</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/grayscale.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="fonts/font1.css" rel="stylesheet" type="text/css">
    <link href="fonts/font2.css" rel="stylesheet" type="text/css">

</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <!-- Navigation -->
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top">
                    <img src="img/scslogo.png" style="float:left; padding-right:10px; padding-top:2px;"/>  <span style="color:#005aa7;">Segelclub</span> Schwielochsee e.V.
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#weather1"><i class="fa fa-cloud"></i> Wetter</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#twitter1"><i class="fa fa-twitter"></i> Twitter</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#article1"><i class="fa fa-comments"></i> Berichte</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#contact"><i class="fa fa-envelope"></i> Kontakt</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Intro Header -->
    <header class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <a href="#about" class="btn btn-circle page-scroll">
                            <i class="fa fa-5x fa-angle-double-down animated"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Weather 1 Section -->
    <section id="weather1" class="text-center" style="padding-top:35px;">
        <div class="weather-section">
        </div>
    </section>

    <!-- Weather 2 Section -->
    <section id="weather2" class="container content-section text-center">
            <h2>DWD Prognose - 48 Stunden</h2>
            <h4>Stand: <?php echo date("d.m.Y H:i", filemtime("weather/prognose_48h.jpg")); ?> Uhr</h4>
            <img src="weather/prognose_48h.jpg" />
    </section>

    <!-- Weather 3 Section -->
    <section id="weather3" class="container content-section text-center">
            <h2>DWD Wetterradar - aktuell</h2>
            <h4>Stand: <?php echo date("d.m.Y H:i", filemtime("weather/radar_deutschland.jpg")); ?> Uhr</h4>
            <img src="weather/radar_deutschland.jpg"/>
    </section>

    <!-- Weather 4 Section -->
    <section id="weather4" class="container content-section text-center">
            <h2>Windguru Prognose - 72 Stunden</h2>
            <h4>Stand: <?php echo date("d.m.Y H:i", filemtime("weather/windguru.png")); ?> Uhr</h4>
            <img src="weather/windguru.png" style="margin-left:-125px;"/>
    </section>


    <?php
        
        $results = $db->query("SELECT * FROM news WHERE published > date('now', '-3 month') and type='twitter' ORDER BY published ASC LIMIT 6");
        $count = 1; 
        while ($row = $results->fetchArray()) {
            array_push($order, ['id' => 'twitter' . $count, 'duration' => 10]);
    ?>

        <!-- Twitter Section -->
        <section id="twitter<?php echo $count; ?>" class="content-section twitter-section" >
            <div class="twitter-image twitter-image-<?php echo $count; ?>">
                <div class="container">
                    <div class="col-lg-8 col-lg-offset-2">
                        <blockquote><?php echo $row['text']; ?>
                        <cite><?php echo utf8_encode(strftime("%A, den %d. %B %Y %H:%M", strtotime($row['published']))); ?> Uhr</cite></blockquote>
                    </div>
                </div>
            </div>
        </section>

    <?php
            $count++;
        }
    ?>

    <?php
        
        $results = $db->query("SELECT * FROM news WHERE published > date('now', '-3 month') and type='article' ORDER BY published ASC LIMIT 6");
        $count = 1; 
        while ($row = $results->fetchArray()) {
            array_push($order, ['id' => 'article' . $count, 'duration' => 0]);
    ?>

        <!-- Article Section -->
        <style>
            #article-image-<?php echo $count; ?>{
                    width: 100%;
                    color: #FFF;
                    background: url(<?php echo $row['image']; ?>) no-repeat center center scroll;
                    background-color: #fff;
                    -webkit-background-size: cover;
                    -moz-background-size: cover;
                    background-size: cover;
                    -o-background-size: cover;
                    padding: 170px 0;
            }
        </style>
        <section id="article<?php echo $count; ?>" class="article-section container article">
            <?php
                if(strlen($row['image']) > 10) {
            ?>
                    <div class="article-image" id="article-image-<?php echo $count; ?>">            
                    </div>
            <?php
                }
            ?>
            <div class="article-heading" style="">
                <h1><?php echo $row['title']; ?></h1>
                <cite><?php echo $row['author']; ?>, <?php echo utf8_encode(strftime("%A, den %d. %B %Y", strtotime($row['published']))); ?></cite>
            </div>
            <div class="article-text">
                <?php echo $row['text']; ?>
            </div>
            <span class='article-bottom'></span>
            </section>

    <?php
            if(strlen($row['google_album']) > 5 && file_exists('scs_website/files/articles/' . $row['google_album'] . '/') ) {
                $files = glob('scs_website/files/articles/' . $row['google_album'] . '/' . "*.{jpg,png,gif,jpeg}",GLOB_BRACE);
                $img_count = 1;

                foreach ($files as $file) {
                    array_push($order, ['id' => 'article' . $count . '-image' . $img_count, 'duration' => 7]);
    ?>

                        <!-- Article  Image Section -->
                        <section id="article<?php echo $count; ?>-image<?php echo $img_count; ?>" class="container content-section text-center">
                            <img src="<?php echo $file; ?>"/>
                        </section>
    <?php
                    $img_count++;
                }
            }

            $count++;
        }

            array_push($order, ['id' => 'contact', 'duration' => 20]);
    ?>


    <!-- Contact Section -->
    <section id="contact" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Ideen und Kontakt</h2>
                <p>Wir würden uns freuen in Zukunft die Webseite noch häufiger mit aktuellen Informationen zu versorgen. Dafür sind wir jedoch auf Ihre Zuarbeit angewiesen und freuen uns daher über Berichte, Ergebnisse, Bilder und alles was ansonsten die Mitglieder des Segelclubs interessieren könnte.</p>
                <p><a href="mailto:webmaster@sc-schwielochsee.de">webmaster@sc-schwielochsee.de</a><br />
                <a href="callto:01781383873">0178 / 138 38 73</a>
                </p>
                <ul class="list-inline banner-social-buttons">
                    <li>
                        <a href="https://twitter.com/SCSchwielochsee" class="btn btn-default btn-lg"><i class="fa fa-twitter fa-fw"></i> <span class="network-name">Twitter</span></a>
                    </li>
                    <li>
                        <a href="https://www.facebook.com/SCSchwielochsee" class="btn btn-default btn-lg"><i class="fa fa-facebook fa-fw"></i> <span class="network-name">Facebook</span></a>
                    </li>
                    <li>
                        <a href="https://plus.google.com/b/114918712676267270723/" class="btn btn-default btn-lg"><i class="fa fa-google-plus fa-fw"></i> <span class="network-name">Google+</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center" style="padding-top:150px;">
            <p>Segelclub Schwielochsee e.V. <?php echo date("Y"); ?></p>
            <p><a href="http://sc-schwielochsee.de">sc-schwielochsee.de</a></p>
        </div>
    </footer>


    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="js/jquery.easing.min.js"></script>

    <!-- Scrollspy JavaScript -->
    <script src="js/scrollspy.js"></script>

    <script type="text/javascript">

        var order = <?php print(json_encode($order)); ?>;
        var cursor = -1;

        function next() {
            cursor = cursor + 1;
            return order[cursor];
        }

        function scrollTo(anchor) {
            $('html, body').stop().animate({
                scrollTop: $(anchor).offset().top
            }, 1500, 'easeInOutExpo');
        }

        function showNext() {
            var nextView = next();

            // return null;
            
            if (nextView === undefined) {
                window.location.href = "http://localhost/infoscreen";
            } else {

                scrollTo($('#' + nextView['id']));

                if( nextView['duration'] > 0) {
                    setTimeout(function(){
                        showNext()
                    }, nextView['duration'] * 1000);
                }
            }
        }

        $(document).ready(function() {
            setTimeout(function(){
                showNext();
            }, 3000);

        });
    </script>

    <!-- Custom Theme JavaScript -->
    <script src="js/grayscale.js"></script>
    

</body>

</html>
