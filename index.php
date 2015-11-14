<?php

@session_start ();

@ob_start ();

@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );

@ini_set ( 'display_errors', true );

@ini_set ( 'html_errors', false );

@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'ROOT_DIR', dirname ( __FILE__ ) );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

@include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once ROOT_DIR . '/modules/functions.php';

require_once INCLUDE_DIR . '/member.php';

if( $_REQUEST['oauth_token'] ){
    
    header("Location: " . $config['siteurl'] . "create-account/twitter/?oauth_token=" . $_REQUEST['oauth_token'] . "&oauth_verifier=" . $_REQUEST['oauth_verifier'] );
    
    die();
    
}

if( $_REQUEST['action'] == 'logout' ){
    
    $member_id = array ();
    
    set_cookie( "user_id", "", 0 );
    
    set_cookie( "login_pass", "", 0 );
    
    $_SESSION['user_id'] = 0;
    
    $_SESSION['login_pass'] = "";
    
    @session_destroy();
    
    @session_unset();
    
    header("Location: http://localhost/");
    
    die();
    
}

$thistime = time();

$analytics = str_replace( "&#036;", "$", $config['analytics'] );
$analytics = str_replace( "&#123;", "{", $analytics );
$analytics = str_replace( "&#125;", "}", $analytics );

$allscripts = '';

$metatags = '
<title>'.$config['sitetitle'].'</title>
<meta name="title" content='.$config["sitetitle"].' />
<meta property="og:title" name="title" content='.$config["sitetitle"].' />
<meta property="og:url" content="http://localhost//" />
<meta property="og:site_name" content='.$config["sitetitle"].' />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content='.$config["facebook_app_id"].' />
<meta property="og:type" content="music" />
<meta name="description" property="og:description" content='.$config["webdesc"].' />
<meta name="keywords" content='.$config["keywords"].' />';

?>

<?php 
if(!empty($_SESSION['user_id'])){ ?>

    <?php
    header("Location: main.php");
    die();
    ?>

<?php }else{ ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta property="og:image" content="http://localhost//images/kiandaimg.jpg" />
    <link rel="apple-touch-icon" href="/mobile/assets/images/AppIcon60x60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/mobile/assets/images/AppIcon76x76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/mobile/assets/images/AppIcon60x60.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/mobile/assets/images/AppIcon76x76@2x.png">
    <title>Nota - A música que nos une.</title>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,600,300&amp;subset=latin,cyrillic-ext,greek-ext,greek,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <?php echo $metatags ?>
    <link rel="stylesheet" href="css/style.css" media="all">
    <link rel="stylesheet" href="css/color.css" media="all">
    <link rel="stylesheet" href="css/dark.css" media="all"> 
    <script type="text/javascript">
 // <![CDATA[  
    var mobile = (/iphone|ipod|android|blackberry|mini|windows (ce|phone)|palm/i.test(navigator.userAgent.toLowerCase()));  
    if (mobile) {  
      //  document.location = "http://mobile.nota.com/";  
       // document.getElementById('themainsite').innerHTML = "<a href='http://mobile.nota.com'>Mobile Site</a>";
    } // ]]>
    </script>
    <script src="js/jquery.min.js"></script>
    <script src="js/modernizr.js"></script>

    <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <link rel="stylesheet" type="text/css" href="css/ie8.css" />
    <![endif]-->

    <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="css/ie.css" />
    <![endif]-->

</head>

<body>
    <div id="qLoverlay"></div>
    <div id="topi"></div>

    <section class="hero mvisible parallax-background top-waypoint parallax-background1" data-type="parallax" data-animate-up="header-hide" data-animate-down="header-hide">
        <div class="overlay lighterbg canvas"></div>
        <div class="container">
            <div class="hero-inner left">
                <h1 class="big_white">Nota é
                    <span class="element" data-elements="Música, Entretenimento, Partilhar, Diversão"></span>
                </h1>
                <div class="hero-copy">A MÚSICA QUE NOS UNE.</div>
                <span class="btn-holder"><a href="#about-us" class="hero-btn lm-button">Mais</a>
                </span>
            </div>
        </div>
    </section>

    <section id="home-content" class="top-waypoint" data-animate-up="header-hide" data-animate-down="header-show">
        
        <header id="header-section" class="ha-header">
            <div class="container">
                <div class="row">
                    <div class="column two">
                        <h1 class="logo">
                            <a href="#top">
                                <img src="images/elements/logoxmas2.gif" width="164" height="60" alt="logo">
                            </a>
                        </h1>
                    </div>
                    <nav id="navigation" class="column ten hide-on-mobile">
                        <ul>
                            <li><a class="themainsite" href="/main.php">Site Principal</a>
                            </li>
                        </ul>
                    </nav>
                    <a href="#" id="menu-toggle-wrapper">
                        <div id="menu-toggle"></div>
                    </a>
                </div>

            </div>

        </header>

        <section id="about-us" class="menu-in">
            <header class="section-header">
                <h2 class="section-title" data-anim-delay="400">Sobre Nós</h2>
                <p class="section-tagline">Nota é uma plataforma musical, que irá disponiblizar aos usuários o acesso à centenas de músicas Angolanas e países vizinhos.</p>
            </header>
            <div id="about-icons">
                <div class="container">
                    <div class="row">
                        <div class="column four border">
                            <div class="main">
                                <i class="pe-7s-graph1"></i>
                                <h2>Nosso Objectivo</h2>
                            </div>
                            <span class="desc">Resgatar e preservar as musicas angolanas. Proporcionando as pessoas, em qualquer lugar do mundo acesso às mesmas.</span>
                        </div>
                        <div class="column four border">
                            <div class="main">
                                <i class="pe-7s-light"></i>
                                <h2>Visão</h2>
                            </div>
                            <span class="desc">Ser uma plataforma musical de referência como melhor opção para os ouvintes e artistas, pela nossa individualidade e qualidade.</span>
                        </div>
                        <div class="column four">
                            <div class="main">
                                <i class="pe-7s-loop"></i>
                                <h2>Valores</h2>
                            </div>
                            <span class="desc">Inovação e melhoria contínua.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="quote" class="parallax-background parallax-background4" data-type="parallax">
            <div class="overlay"></div>
            <div class="container wow zoomIn">
                <h2 class="parallax-quote">Sem música, a vida seria um erro.</h2>
                <span class="quote-author">- Friedrich Nietsche</span>
            </div>
        </section>

        <section id="quote-request">
            <div class="container">
                <div class="row">
                    <div class="column six">
                        <p class="wow fadeInLeft">Das musicas mais recentes</p>       
                    </div>
                    <div class="column six">
                         <div class="artistShow1"></div>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="achievements" class="parallax-background parallax-background3" data-type="parallax">
            <div class="overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="column twelve">
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features">
            <header class="section-header">
                <h2 class="section-title">Features</h2>
            </header>
            <div class="container">
                <div class="row">
                    <div class="column four wow fadeInUp" data-wow-delay="0.3s">
                        <i class="pe-7s-music"></i>
                        <h3>Centenas de Músicas</h3>
                        <p>Ouve aqui os sons mais recentes e recorda os velhos sucessos! Todos os estilos, num só lugar.</p>
                    </div>
                    <div class="column four wow fadeInUp" data-wow-delay="0.6s">
                        <i class="pe-7s-musiclist"></i>
                        <h3>Crie Playlists</h3>
                        <p>Tu escolhes o que queres ouvir. Monta a tua lista de reprodução, ouve as músicas favoritas dos teus amigos, dedica e compartilha.</p>
                    </div>
                    <div class="column four wow fadeInUp" data-wow-delay="1s">
                        <i class="pe-7s-compass"></i>
                        <h3>Explore ao maximo</h3>
                        <p>Crie um perfil com cores e imagens a tua escolha. Segue pessoas com gosto musical parecido ao teu e aproveita todas as novidades que o Nota tem para ti.</p>
                    </div>
                </div>
                
            </div>
        </section>

        <section id="quote-request">
            <div class="container">
                <div class="row">
                    <div class="column six">
                        <p class="wow fadeInLeft">As musicas mais antigas</p>       
                    </div>
                    <div class="column six">
                         <div class="artistShow2"></div>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="twitter" class="parallax-background parallax-background5" data-type="parallax">
            <div class="overlay"></div>
            <div id="twitter-container">

            </div>
        </section>

        <section id="clients">
        <header class="section-header">
                <h2 class="section-title">Parceiros</h2>
            </header>
            <div class="container">
                <div class="row">
                    <div class="content twelve">
                        <div class="clients">
                           <a href="http://www.jovensdabanda.co.ao/" target="_blank"><img src="images/clients/marca-3.jpg" alt="parceiros"></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
       
        <footer>
            <div id="copyright" class="wow fadeInUp" data-wow-offset="50">
                <img src="images/elements/logoxmas2.gif" width="164" height="60" alt="logo">
                <span class="block">Orgulhosamente criado com <i class="fa fa-heart"></i> por Angolanos
                    <br/>Nota Copyright 2014. Todos os direitos reservados.
                    <br/><a href="#">Politicas de Privacidade | Termos e Condicoes</a></span>
            </div>
            <div id="social" class="wow fadeInUp" data-wow-offset="0">
                <ul>
                    <li><a href="http://facebook.com/nota" target="_blank"><i class="fa fa-facebook"></i></a>
                    </li>
                    <li><a href="http://twitter.com/nota" target="_blank"><i class="fa fa-twitter"></i></a>
                    </li>
                    <li><a href="http://instagram.com/kianda_stream" target="_blank"><i class="fa fa-instagram"></i></a>
                    </li>
                    <li><a href="http://localhost//blog" target="_blank"><i class="fa fa-wordpress"></i></a>
                    </li>
                </ul>
            </div>
        </footer>

    </section>



    <script src="js/device.min.js"></script>
    <script src="js/jquery.queryloader2.min.js"></script>
    <script src="js/SmoothScroll.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/isotope.js"></script>
    <script src="js/jquery.isotope.sloppy-masonry.min.js"></script>
    <script src="js/jquery.parallax.min.js"></script>
    <script src="js/jquery.scrollTo.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/jquery.mb.YTPlayer.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/easypiechart.js"></script>
    <script src="js/typed.js"></script>
    <script src="js/wow.min.js"></script>
    <script src="js/retina.min.js"></script>
    <script src="js/jquery.soc-share.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD566XIEpVL7hnoWtvHJ6XZEEos7FEB1UA&amp;sensor=false"></script>
    <script src="js/custom.js"></script>
    <script type="text/javascript" src="js/snowfall.jquery.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $(document).snowfall();
    });
    </script>

    <!-- TRACKING CODES -->


    <script type="text/javascript">
    var clicky_site_ids = clicky_site_ids || [];
    clicky_site_ids.push(100742469);
    (function() {
      var s = document.createElement('script');
      s.type = 'text/javascript';
      s.async = true;
      s.src = '//static.getclicky.com/js';
      ( document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0] ).appendChild( s );
    })();
    </script>

    <!-- Start of Woopra Code -->
    <script>
    (function(){
        var t,i,e,n=window,o=document,a=arguments,s="script",r=["config","track","identify","visit","push","call"],c=function(){var t,i=this;for(i._e=[],t=0;r.length>t;t++)(function(t){i[t]=function(){return i._e.push([t].concat(Array.prototype.slice.call(arguments,0))),i}})(r[t])};for(n._w=n._w||{},t=0;a.length>t;t++)n._w[a[t]]=n[a[t]]=n[a[t]]||new c;i=o.createElement(s),i.async=1,i.src="//static.woopra.com/js/w.js",e=o.getElementsByTagName(s)[0],e.parentNode.insertBefore(i,e)
    })("woopra");

    woopra.config({
        domain: 'localhost'
    });
    woopra.track();
    </script>
    <!-- End of Woopra Code -->

    <!-- Start GoogleAnalytics -->
    <script type="text/javascript">

      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-48948428-1', 'localhost');
      ga('send', 'pageview');

    </script>
    <!-- End GoogleAnalytics -->

    <!-- TRACKING CODES ENDS -->
    </body>

    </html>

<?php } ?>
