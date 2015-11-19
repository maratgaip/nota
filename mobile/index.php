<?php

@session_start ();

@ob_start ();

@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );

@ini_set ( 'display_errors', false );

@ini_set ( 'html_errors', false );

@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'ROOT_DIR', dirname ( __FILE__ ) );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once INCLUDE_DIR . '/functions.php';

require_once INCLUDE_DIR . '/member.php';

$_TIME = date( "Y-m-d H:i:s", time() );

$main_json = array ();

if ($logged) {
	
	
	$member_id ['Picture'] = $member_id ['userID'] . ".jpg";
	$main_json ['user'] = $member_id;
	$main_json ['user']['UserID'] = intval($member_id['UserID']);
	$main_json ['user']['IsPremium'] = 1;
	
} else {
	$main_json ['user'] ['IsPremium'] = 1;
}


$main_json = json_encode ( $main_json );

$_TIME = time();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Nota Mobile</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <meta property="og:title"  name="title" content="Nota Mobile">
    <meta property="og:url" content="http://mobile.nota.kg">
    <meta property="og:image" content="http://mobile.nota.kg/">
    <meta property="og:site_name" content="Nota Mobile">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="website">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <link rel="apple-touch-icon" href="/assets/images/AppIcon60x60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/AppIcon76x76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/AppIcon60x60.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/AppIcon76x76@2x.png">
    <link rel="stylesheet" type="text/css" href="/build/app.min.css?1350258856" >
    <script type="text/javascript">
        (function(w, d) {
            var wl = w.location,
                p = wl.href.substring(wl.href.indexOf(wl.host) + wl.host.length);
            // !== means -1 as well, which means redirects from /s/ and alike
            if (p.indexOf && p.indexOf('/#') !== 0) {
                w.location = wl.protocol + '//' + wl.host + '/#!' + p;
            }
        })(window, document);
    </script>
</head>
<body>
    <div id="main-menu">
        <div class="search-menu"></div>
        <ul class="menu">
            <li id="nav-search"><a href="/#!/">Search</a></li>
            <li id="nav-popular"><a href="/#!/popular">Random</a></li>
            <!--<li id="nav-stations"><a href="/#!/music/stations">Radio</a></li>-->
        </ul>
        <div id="user-menu" class="menu-loggedin"></div>
        <ul class="menu-buttons">
            <li class="menu-loggedout">
                <a class="button" id="nav-login" href="/#!/login">Login</a>
            </li>
            <li class="menu-loggedin">
                <a class="button" id="menu-logout">Logout</a>
            </li>
            <li><a href="http://mobile.localhost/about" target="_blank" class="button">About</a></li>
        </ul>
        <div class="menu menu-bottom">
            <div id="terms">
                <a href="http://mobile.nota.kg/privacy.html" target="_blank">Privacy</a>
                <a href="http://mobile.nota.kg/terms.html" target="_blank">Terms</a>
            </div>
        </div>
    </div>

    <div id="wrapper">
        <div class="topbar">
            <div id="page-header" class="black-bar">
                <a class="button header-left menu"></a>
                <span class="page-title">Nota</span>
                <a href="/#!/login" id="header-login" class="button header-right header-login">Login</a>
            </div>
        </div>

        <div id="app"></div>

        <div class="bottombar">
            <div id="station-controls" class="hidden"></div>
            <div id="little-queue" class="little-queue black-bar hidden"></div>
        </div>

        <div class="fix-fixed"></div>
    </div>

    <div id="loading-center">
        <div id="loading"></div>
    </div>

    <div id="queue" class="popover">
        <div class="fix-fixed"></div>
    </div>

    <div class="fix-fixed"></div>

    <!-- <div id="ads"><div id="ad"></div><a id="close-ad"><span>Close Ad</span></a></div> 

    <div id="ios-pin">
        <h3>Nota on your finger</h3>
        <p>Want quick and easy access to Nota webapp? Touch below to pin it to your homescreen. Tap anywhere to close this notification.</p>
    </div>
-->
    <div id="hide-audio"></div>

    

    <script type="text/javascript">
        window.GS = {
            _cache:     {},
            config:     {},
            h:          {},
            tpl:        {},
            models:     {},
            views:      {},
            routers:    {},
            ads:        {},
            tracking:   {}
        };
        

	window.GS.config = <?php echo $main_json ?>;
    </script>

    <script type="text/javascript" src="/build/libs.min.js"></script>
    <script type="text/javascript" src="/build/app.min.js"></script>

    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-35755876-1']);

        (function() {
            // do not track things in 'dev' RUN_MODE
            if (GS.config.runMode === 'dev') {
                return;
            }

            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') +
                '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <script type="text/javascript">
        (function() {
            if (GS.config.runMode === 'dev') {
                return;
            }
        })();
    </script>
</body>
</html>