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
	
	header("Location: " . $config['siteurl'] );
	
	die();
	
}

//Load genres


$ajax = <<<HTML
<script language="javascript" type="text/javascript">
var player_root = '{$config['siteurl']}';

var mail_contact = 'info@kiandastream.com';

</script>
HTML;

$allscripts = <<<HTML
<script>loggedInUser = null;userBackground = {};</script>
<script type="text/javascript" src="/assets/js/core.js"></script>
<script type="text/javascript" src="landing/js/site.js"></script>
<script type="text/javascript" src="/assets/js/templates.js?{$thistime}"></script>
<script type="text/javascript" src="/assets/js/app.js?{$thistime}"></script>
<script type="text/javascript" src="/assets/js/style.js?{$thistime}"></script>
HTML;

$metatags = <<<HTML
<title>{$config['sitetitle']}</title>
<meta name="title" content="{$config['sitetitle']}" />
<meta property="og:title" name="title" content="{$config['sitetitle']}" />
<meta property="og:url" content="{$config['sitetitle']}" />
<meta property="og:image" content="{$config['facebook_icon']}" />
<meta property="og:site_name" content="{$config['sitetitle']}" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="{$config['facebook_app_id']}" />
<meta property="og:type" content="musician" />
<meta name="description" property="og:description" content="{$config['webdesc']}" />
<meta name="keywords" content="{$config['keywords']}" />
HTML;


$thistime = time();

$analytics = str_replace( "&#036;", "$", $config['analytics'] );
$analytics = str_replace( "&#123;", "{", $analytics );
$analytics = str_replace( "&#125;", "}", $analytics );


if(!empty($_SESSION['user_id'])){

	header("Location: indexb.php");


	die();
}


else{





echo <<<HTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{$metatags}
<link rel="stylesheet" href="/assets/css/app.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="/assets/css/style.css" />
<link rel="stylesheet" type="text/css" href="/assets/css/fontello/css/ks.css" />
<link href="landing/css/base.css" type="text/css" rel="stylesheet" />
<link href="landing/css/styleln.css" type="text/css" rel="stylesheet" />
<link href="landing/css/jquery-ui.min.css" type="text/css" rel="stylesheet" />
<!--[if IE 8]><link rel="stylesheet" type="text/css" media="screen" href="/assets/css/lala-app-ie8.css" /><![endif]-->
<link rel="shortcut icon" href="{$config['site_icon']}" />
{$ajax}
<script type="text/javascript">
var FRAGMENT = 'None';var _gaq = _gaq || [];
</script>


<script type="text/javascript">
 // <![CDATA[  
    var mobile = (/iphone|ipod|android|blackberry|mini|windows (ce|phone)|palm/i.test(navigator.userAgent.toLowerCase()));  
    if (mobile) {  
        document.location = "http://kiandastream.com//mobile.html";  
    } // ]]>
</script>


</head>

<body>

<div id="fb-root"></div>
<script>

(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<div class="loader"></div>
<div id="header2">
		<div class="inner">
			<h1 class="logo">
				<a rel="home-nav" href="#"></a>
			</h1>
		  
			<ul class="navigation">
				<li><a href="/sign-in">Login</a></li>
				<li><a href="/create-account">Register</a></li>
			</ul>
			
			<span class="scroll-glyph"></span>
		</div>
	</div>
	
	<div id="middle2">
		<div id="slide1" class="slide intro-layout home-nav">
			<div class="content">
                
				<div class="slide-bg">
					<img class="slide-bg" src="landing/images/main%20bg%203.jpg">
				</div>
                 <a class="scroll1 go-down1" href="#slide3"><div class="imagedown"></div></a>
			</div>
           
		</div>

		<div id="slide2" class="slide layout-image">
			<div class="content">			
				<div class="slide-bg">
					<img alt="" src="landing/images/kiandaimg1.jpg" class="slide-bg">
				</div>
			</div>
		</div>

		<div id="slide3" class="slide2 callout-layout no-bg">
			<div class="content">
                <h2>O Que e o Kiandastream?</h2>
                <div class="col2">
                    
                <p>O KiandaStream e uma plataforma musical, que ira disponibilizar aos usuarios o acesso a milhares de musicas angolanas dos mais variados estilos. A plataforma ira implementar um servico unico e personalizado que ira permitir os usuarios de usufruir dos mais variados recursos que estarao ao seu dispor. Isso sem contar com o vasto leque de informacoes sobre os artistas nacionais.
                    </p> 
                </div>
			</div>
		</div>

		<div id="slide4" class="slide layout-image">
			<div class="content">			
				<div class="slide-bg">
					<img alt="" src="landing/images/6B3A5008.jpg" class="slide-bg"/>
				</div>
			</div>
		</div>

		<div id="slide5" class="slide2 callout-layout no-bg">
			<div class="content">
				<h2>Nosso Objectivo</h2>
                <div class="col2">
                    
                    <p>Como objectivo principal queremos fazer com que haja um maior reconhecimento da musica Angolana alem fronteiras, mas tambem servir como um novo meio facilitador da comunicacao entre os artistas e os amantes das suas musicas. Eliminando assim a barreira actual na procura de musica angolana na diaspora.</p>
                </div>
			</div>
		</div>

		<div id="slide6" class="slide layout-image">
			<div class="content">			
				<div class="slide-bg">
					<img alt="" src="landing/images/6B3A4432.jpg" class="slide-bg">
				</div>
			</div>
		</div>

		<div id="slide7" class="slide2 about-layout no-bg about-nav">
			<div class="content">

				<h2>Quem Somos?</h2>

				<div class="col2">
					<p>
						Nos somos tu, somos um so, somos os amantes da musica da banda, somos o passado, o presente e o futuro da nossa cultura musical. Nos somos musica Angolana.
					</p>
				</div>

				<div class="clear"></div>
			</div>
		</div>

		<div id="slide8" class="slide layout-image">
			<div class="content">
				<div class="slide-bg">
					<img alt="" src="landing/images/6B3A5733.JPG" class="slide-bg"/>
				</div>			
			</div>
		</div>
     
        
	<div id="footer">
		<div class="inner">
			<div class="footer-title">
				Fique sempre ligado conosco:
			</div>
		
          
            <a href="http://facebook.com/kiandastream" target="_blank"><img src="landing/images/facebook.png" alt="facebook" width="80" height="80"></a>
            
            <a href="http://twitter.com/kiandastream" target="_blank"><img src="landing/images/twitter.png" alt="twitter" width="80" height="80"></a>
            
            <a href="http://kiandastream.com//blog" target="_blank"><img src="landing/images/kiandalg.png" alt="kianda blog" width="80" height="80"></a>
            
			<br><br><br> &copy; KiandaStream 2014
		</div>
	</div>
    
    <!--
    <div id="loginm" style="display:none">
        <p>Estaremos desponiveis em breve!! Fique Ligado.</p>
    </div>
   <form action="" class="login_form modal" id="loginm1" style="display:none;">
      <h3>Please login to continue</h3>
      <p><label>Username:</label><input name="username" type="text" /></p>
      <p><label>Password:</label><input name="password" type="password" /></p>
      <p><input type="submit" value="Login" /></p>
      <p><input type="submit" value="Close" rel="modal:close" /></p>

    </form>
    -->









			
<div id="middle" class="display_none">
	<div id="left">
		<div id="user_nav"> 
			<br />
            
            <div class="left_row_text left_row_discover">Discover</div>
            <div class="leftmenus">
            <a class="left_row_custom" href="/trending"><div class="left_row_text left_row_text2 nnnn"><i class="icon-gauge-1"></i> Trending</div></a>
            <a class="left_row_custom" href="/explore/top-of-the-week"><div class="left_row_text left_row_text2 nnnn"><i class="icon-chart-line"></i> Top Charts</div></a>
            <a class="left_row_custom" href="/explore/genres"><div class="left_row_text left_row_text2 nnnn"><i class="icon-note-beamed"></i> Genres</div></a>
         
            
            <a class="left_row_custom" href="/explore/album-of-the-week"><div class="left_row_text left_row_text2 nnnn"><i class="icon-star-empty-1"></i> Feature Album</div></a>
			
            <a class="left_row_custom" href="/artists"><div class="left_row_text left_row_text2 nnnn"><i class="icon-users"></i> Artists</div></a>
			

            <a class="left_row_custom" href="/albums"><div class="left_row_text left_row_text2 nnnn"><i class="icon-cd-1"></i> Albums</div></a>
	
            </div>
            
            <div class="left_row_text spp"></div>
		</div>
	</div>
	<div style="color:#141318" id="right">
		<div id="home_section" class="display_none"></div>
		<div id="song_list" class="display_none"></div>
		<div id="sites_list" class="display_none">sites</div>
		<div id="settings" class="display_none"></div>
	</div>
	<!-- end right -->
	<div id="current_playlist">
				<div id="current_playlist_header">
					<div id="current_playlist_clear">Clear Queue</div>
					<div id="current_playlist_save">Save As Playlist ...</div>
					<div id="current_playlist_close"></div>
					<div id="queue_to_playlist_dropdown" class="display_none">
				<li class="queue_to_new_playlist_dropdown_link" data-playlist-id="NEW"><span>Create playlist</span></li>
				{$playlists_queue}
					</div>
				</div>
				<ul id="current_playlist_rows"></ul>
				<div id="current_playlist_blur"></div>
	</div>
	<div id="resort_playlist">
		<div id="resort_playlist_header">
			<div id="resort_playlist_close"></div>
		</div>
		<ul id="resort_playlist_rows">
		</ul>
	</div>
	<div id="right_cover" class="display_none"> 
		<!-- <div id="right_cover_loading">Loading...</div> --> 
	</div>
</div>
<!-- end middle -->
<!--<div id="blurredplayer">
	<div id="blurredplayer_color">
		<div id="blurredplayer_effect">
		</div>
	</div>
	
	
</div>
-->
<div id="bottom" class="display_none">
	<div id="bottom_controls">
		<div id="prev_button" class="controls_button"></div>
		<div id="play_button" class="play_button controls_button"></div>
		<div id="next_button" class="controls_button"></div>
	</div>
	<div id="volume">
		<div id="volume_speaker" class="volume_on"></div>
		<div id="volume_back">
            <div id="volume_thumb"></div>
        </div>
	</div>
	<div id="display">
		<div id="display_coverart" class="hide_when_stopped display_none"> <img src="/assets/images/glare_45x45.png" width="40" height="40" class="display_coverart_glare" /></div>
		<div id="display_logo" class="hide_when_playing"></div>
		<div id="display_text" class="hide_when_stopped display_none"> <a id="display_song"></a> <a id="display_artist"></a>
			<div id="display_album"></div>
			<a id="display_domain" target="_blank" outbound_type="bottom_player_source"></a> </div>
		<div id="display_time" class="hide_when_stopped display_none">
			<div id="display_time_count"></div>
			<div id="display_progress"></div>
			<div id="display_progressed"></div>
			<div id="display_seek_thumb"></div>
			<div id="display_time_total"></div>
		</div>
		<div id="current_song_love_icon" class="hide_when_stopped display_none tooltip" tooltip="Love this song"></div>
		<div id="current_song_share_icon" class="hide_when_stopped display_none tooltip" tooltip="Share this song"></div>
	</div>
	<div id="playlist_button" tooltip="Open or close Queue" class="tooltip"></div>
	<div id="shuffle_button" tooltip="Shuffe" class="tooltip"></div>
	<div class="mainblurr">
		<div class="currentartpic"></div>
	</div>
</div>

<div id="tooltip_display"></div>
<div id="full_cover" class="display_none"></div>
<div id="tutorial_container" class="display_none"></div>
{$allscripts}
<div id="dropdown-1" class="dropdown dropdown-tip">
	<ul class="dropdown-menu" id="all_playlist_menu">
		<li id="create_playlist_click"><span>Create Playlist</span></li>
		<li id="add_to_queue_click"><span>Add to Queue</span></li>
		<li class="dropdown-divider"></li>
		{$playlists}
	</ul>
</div>
{$analytics}
</body>
</html>
HTML;

	

}





?>
