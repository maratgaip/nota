<<<<<<< .mine
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

$genres = $db->query("SELECT name FROM `vass_genres` WHERE stick= 1 ORDER by id ASC LIMIT 0,20");

while($genre = $db->get_row($genres)){

	$genre_list .= '"' . $genre['name'] . '",';
	
}

$genre_list = substr( $genre_list, 0, ( strLen( $genre_list ) - 1 ) );

if($logged){
	$playlists_query = $db->query("SELECT id, name FROM `vass_playlists` WHERE user_id = '" . $member_id ['user_id'] . "' ORDER by id DESC");
	
	while($row = $db->get_row($playlists_query)){
		$playlists .= "<li class=\"playlist_click\" data-playlist-id=\"{$row['id']}\"><span>{$row['name']}</span></li>";
		$playlists_queue .= "<span class=\"queue_to_playlist_dropdown_link\" data-playlist-id=\"{$row['id']}\">{$row['name']}</span>";
		$playlistleft .="<li><a class=\"left_row_custom left_row_text left_row_text2 nnnn\" href=\"/playlist/{$row['id']}\"><i class=\"icon-list-1\"></i>{$row['name']}</a></li>";
	}
}


$ajax = <<<HTML
<script language="javascript" type="text/javascript">
var player_root = '{$config['siteurl']}';
var genre_list = [{$genre_list}];
var mail_contact = 'info@nota.kg';

</script>
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

$username1 = $_SESSION['user_id'];

$mydataq = $db->query("SELECT username FROM `vass_users` WHERE user_id = '$username1'");

while ($mydata1 = $db->get_row($mydataq)) {

	$mydatar .= '' . $mydata1['username'] .'';

}

$mydatar = substr($mydatar, 0, ( strLen( $mydatar ) ) );



$mydataq1 = $db->query("SELECT email FROM `vass_users` WHERE user_id = '$username1'");

while ($mydata2 = $db->get_row($mydataq1)) {

	$mydatar1 .= '' . $mydata2['email'] .'';

}

$mydatar1 = substr($mydatar1, 0, ( strLen( $mydatar1 ) ) );


//if(!empty($_SESSION['user_id'])){

echo <<<HTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{$metatags}
<link rel="stylesheet" href="/assets/css/app.css?{$thistime}" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="/assets/css/style.css?{$thistime}" />
<link rel="stylesheet" type="text/css" href="/assets/css/fontello/css/ks.css?{$thistime}" />
<link rel="stylesheet" type="text/css" href="/assets/css/fontello/css/bootstrap-tour-standalone.min.css" />

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
        document.location = "http://mobile.nota.kg";  
    } // ]]>
</script>

</head>

<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<svg height="0" xmlns="http://www.w3.org/2000/svg">
    <filter id="svgBlur" x="-5%" y="-5%" width="110%" height="110%">
        <feGaussianBlur in="SourceGraphic" stdDeviation="15" />
    </filter>
</svg>


<div id="altContent">
	<h1>Nota is the best way to listening and sharing the music.</h1>
	<h2>Collection million songs.</h2>
	<noscript>
	<ul class="links">
		<li><a href="{$config['siteurl']}trending">Trending</a></li>
		<li><a href="{$config['siteurl']}explore">Explore</a></li>
	</ul>
	</noscript>
	<ul class="links">
		<li><a href="{$config['siteurl']}trending">Trending</a></li>
		<li><a href="{$config['siteurl']}explore">Explore</a></li>
	</ul>
</div>
<div id="top"> <a id="logo" href="/"><span class="left_row_icon"></span>nota</a>
	<div id="top_right"></div>
	<div id="top_search">
		<form id="top_search_form">
			<input type="text" id="top_search_input" placeholder="Search" />
		</form>
	</div>
	<div id="top_tip" class="top_tip_hidden"></div>
</div>
<div id="middle">
	<div id="left">
		<div id="user_nav"> 
			<br />
            
            <div class="left_row_text left_row_discover left_row_discover_browse" data-translate-text="left_browse">Browse</div>

            <nav>
            	<ul id="navigation_left">
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/trending"><i class="icon-chart-line"></i> Trending</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/newrelease"><i class="icon-rocket"></i> New Releases</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/explore/top-of-the-week"><i class="icon-paper-plane"></i> Explore</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/artists"><i class="icon-user"></i> Artists</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/albums"><i class="icon-cd-1"></i> Albums</a></li>
                    <li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/settings/social"><i class="icon-users"></i> Find Friends</a></li>
				</ul>
            </nav>
            
            <div class="left_row_text spp"></div>

            <div class="left_row_text left_row_discover left_row_discover_playlist" data-translate-text="left_playlists">Playlists</div>

            
            	<ul id="navigation_left_playlist">
            		<li class="left_row_custom left_row_text left_row_text2 nnnn"><a id="create_playlist_click"><i class="icon-list-add"></i> Create New</a></li>
            		<!--<li><a class="left_row_custom left_row_text left_row_text2 nnnn" href="/playlist/17"><i class="icon-chart-line"></i> Playlist 1</a></li>-->
            		{$playlistleft}
				</ul>
            
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
				<ul id="current_playlist_rows">
				</ul>
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
<div id="bottom">
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
		<div id="current_song_lyrics_icon" class="hide_when_stopped display_none tooltip" tooltip="View lyrics"></div>
		<div id="current_song_love_icon" class="hide_when_stopped display_none tooltip" tooltip="Love this song"></div>
		<div id="current_song_share_icon" class="hide_when_stopped display_none tooltip" tooltip="Share this song"></div>
	</div>
	<div id="playlist_button" tooltip="Open or close Queue" class="tooltip"></div>
	<div id="shuffle_button" tooltip="Shuffe" class="tooltip"></div>
	<div class="mainblurr">
		<div class="currentartpic"></div>
	</div>
	<svg height="0" xmlns="http://www.w3.org/2000/svg">
        <filter id="svgBlur" x="-5%" y="-5%" width="110%" height="110%">
            <feGaussianBlur in="SourceGraphic" stdDeviation="35" />
        </filter>
    </svg>
</div>


<div id="tooltip_display"></div>
<div id="top_right_dropdown" class="display_none"><a href="/settings" class="top_right_dropdown_link">Settings</a> <a class="top_right_dropdown_link" onclick="ChangeLanguage.Build('en'); return false;">English</a> <a onclick="ChangeLanguage.Build('pr'); return false;" class="top_right_dropdown_link">Portuguese</a> <a href="/settings/social" class="top_right_dropdown_link">Social</a> <a href="http://nota.kg//terms.html" target="_blank" class="top_right_dropdown_link">Terms/Privacy</a> <a href="/sign-out" class="top_right_dropdown_link" id="sign_out_link">Logout</a> </div>
<div id="full_cover" class="display_none"></div>
<div id="tutorial_container" class="display_none"></div>
<script>loggedInUser = null;userBackground = {};</script>
<script type="text/javascript" src="/assets/js/core.js?{$thistime}"></script>
<script type="text/javascript" src="/assets/js/templates.js?{$thistime}"></script>
<script type="text/javascript" src="/assets/js/app.js?{$thistime}"></script>
<!--<script type="text/javascript" src="/assets/js/bootstrap-tour-standalone.min.js"></script>-->
<script type="text/javascript" src="/assets/js/style.js?{$thistime}"></script>

<!-- TRACKING CODES
-->

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
    domain: 'nota.kg'
});

woopra.identify({
    email: ' {$mydatar1} ',
    name: ' {$mydatar} '
});
woopra.track();
</script>
<!-- End of Woopra Code -->

<!-- Start GoogleAnalytics  -->
<script type="text/javascript">

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48948428-1', 'nota.kg');
  ga('send', 'pageview');

</script>
 <!-- End GoogleAnalytics -->


<!-- TRACKING CODES ENDS -->

<div style="margin-left: -250px; width: 500px;" class="modal_box display_none" id="lyrics_box">
    <div id="lyrics_box_close_button" class="modal_close_button"></div>
    <div class="modal_top" id="lyrics_box_title"></div>
    <div style="padding: 10px; text-align: center; font-size: inherit; font-family: DINRegular;" id="lyrics_box_content" class="modal_middle">
    </div>
</div>

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

	


//	die();


?>=======
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

$genres = $db->query("SELECT name FROM `vass_genres` WHERE stick= 1 ORDER by id ASC LIMIT 0,20");

while($genre = $db->get_row($genres)){

	$genre_list .= '"' . $genre['name'] . '",';
	
}

$genre_list = substr( $genre_list, 0, ( strLen( $genre_list ) - 1 ) );

if($logged){
	$playlists_query = $db->query("SELECT id, name FROM `vass_playlists` WHERE user_id = '" . $member_id ['user_id'] . "' ORDER by id DESC");
	
	while($row = $db->get_row($playlists_query)){
		$playlists .= "<li class=\"playlist_click\" data-playlist-id=\"{$row['id']}\"><span>{$row['name']}</span></li>";
		$playlists_queue .= "<span class=\"queue_to_playlist_dropdown_link\" data-playlist-id=\"{$row['id']}\">{$row['name']}</span>";
		$playlistleft .="<li><a class=\"left_row_custom left_row_text left_row_text2 nnnn\" href=\"/playlist/{$row['id']}\"><i class=\"icon-list-1\"></i>{$row['name']}</a></li>";
	}
}


$ajax = <<<HTML
<script language="javascript" type="text/javascript">
var player_root = '{$config['siteurl']}';
var genre_list = [{$genre_list}];
var mail_contact = 'info@nota.kg';

</script>
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

$username1 = $_SESSION['user_id'];

$mydataq = $db->query("SELECT username FROM `vass_users` WHERE user_id = '$username1'");

while ($mydata1 = $db->get_row($mydataq)) {

	$mydatar .= '' . $mydata1['username'] .'';

}

$mydatar = substr($mydatar, 0, ( strLen( $mydatar ) ) );



$mydataq1 = $db->query("SELECT email FROM `vass_users` WHERE user_id = '$username1'");

while ($mydata2 = $db->get_row($mydataq1)) {

	$mydatar1 .= '' . $mydata2['email'] .'';

}

$mydatar1 = substr($mydatar1, 0, ( strLen( $mydatar1 ) ) );


//if(!empty($_SESSION['user_id'])){

echo <<<HTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{$metatags}
<link rel="stylesheet" href="/assets/css/app.css?{$thistime}" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" href="/assets/css/style.css?{$thistime}" />
<link rel="stylesheet" type="text/css" href="/assets/css/fontello/css/ks.css?{$thistime}" />
<link rel="stylesheet" type="text/css" href="/assets/css/fontello/css/bootstrap-tour-standalone.min.css" />

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
        document.location = "http://mobile.nota.kg";  
    } // ]]>
</script>

</head>

<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<svg height="0" xmlns="http://www.w3.org/2000/svg">
    <filter id="svgBlur" x="-5%" y="-5%" width="110%" height="110%">
        <feGaussianBlur in="SourceGraphic" stdDeviation="15" />
    </filter>
</svg>


<div id="altContent">
	<h1>Notas is the best way to listening and sharing the music.</h1>
	<h2>Collection million songs.</h2>
	<noscript>
	<ul class="links">
		<li><a href="{$config['siteurl']}trending">Trending</a></li>
		<li><a href="{$config['siteurl']}explore">Explore</a></li>
	</ul>
	</noscript>
	<ul class="links">
		<li><a href="{$config['siteurl']}trending">Trending</a></li>
		<li><a href="{$config['siteurl']}explore">Explore</a></li>
	</ul>
</div>
<div id="top"> <a id="logo" href="/"><span class="left_row_icon"></span>kianda</a>
	<div id="top_right"></div>
	<div id="top_search">
		<form id="top_search_form">
			<input type="text" id="top_search_input" placeholder="Search" />
		</form>
	</div>
	<div id="top_tip" class="top_tip_hidden"></div>
</div>
<div id="middle">
	<div id="left">
		<div id="user_nav"> 
			<br />
            
            <div class="left_row_text left_row_discover left_row_discover_browse" data-translate-text="left_browse">Browse</div>

            <nav>
            	<ul id="navigation_left">
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/trending"><i class="icon-chart-line"></i> Trending</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/newrelease"><i class="icon-rocket"></i> New Releases</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/explore/top-of-the-week"><i class="icon-paper-plane"></i> Explore</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/artists"><i class="icon-user"></i> Artists</a></li>
            		<li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/albums"><i class="icon-cd-1"></i> Albums</a></li>
                    <li><a class="left_row_custom left_row_text left_row_text2 nnnn"  href="/settings/social"><i class="icon-users"></i> Find Friends</a></li>
				</ul>
            </nav>
            
            <div class="left_row_text spp"></div>

            <div class="left_row_text left_row_discover left_row_discover_playlist" data-translate-text="left_playlists">Playlists</div>

            
            	<ul id="navigation_left_playlist">
            		<li class="left_row_custom left_row_text left_row_text2 nnnn"><a id="create_playlist_click"><i class="icon-list-add"></i> Create New</a></li>
            		<!--<li><a class="left_row_custom left_row_text left_row_text2 nnnn" href="/playlist/17"><i class="icon-chart-line"></i> Playlist 1</a></li>-->
            		{$playlistleft}
				</ul>
            
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
				<ul id="current_playlist_rows">
				</ul>
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
<div id="bottom">
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
		<div id="current_song_lyrics_icon" class="hide_when_stopped display_none tooltip" tooltip="View lyrics"></div>
		<div id="current_song_love_icon" class="hide_when_stopped display_none tooltip" tooltip="Love this song"></div>
		<div id="current_song_share_icon" class="hide_when_stopped display_none tooltip" tooltip="Share this song"></div>
	</div>
	<div id="playlist_button" tooltip="Open or close Queue" class="tooltip"></div>
	<div id="shuffle_button" tooltip="Shuffe" class="tooltip"></div>
	<div class="mainblurr">
		<div class="currentartpic"></div>
	</div>
	<svg height="0" xmlns="http://www.w3.org/2000/svg">
        <filter id="svgBlur" x="-5%" y="-5%" width="110%" height="110%">
            <feGaussianBlur in="SourceGraphic" stdDeviation="35" />
        </filter>
    </svg>
</div>


<div id="tooltip_display"></div>
<div id="top_right_dropdown" class="display_none"><a href="/settings" class="top_right_dropdown_link">Settings</a> <a class="top_right_dropdown_link" onclick="ChangeLanguage.Build('en'); return false;">English</a> <a onclick="ChangeLanguage.Build('pr'); return false;" class="top_right_dropdown_link">Portuguese</a> <a href="/settings/social" class="top_right_dropdown_link">Social</a> <a href="http://nota.kg/terms.html" target="_blank" class="top_right_dropdown_link">Terms/Privacy</a> <a href="/sign-out" class="top_right_dropdown_link" id="sign_out_link">Logout</a> </div>
<div id="full_cover" class="display_none"></div>
<div id="tutorial_container" class="display_none"></div>
<script>loggedInUser = null;userBackground = {};</script>
<script type="text/javascript" src="/assets/js/core.js?{$thistime}"></script>
<script type="text/javascript" src="/assets/js/templates.js?{$thistime}"></script>
<script type="text/javascript" src="/assets/js/app.js?{$thistime}"></script>
<!--<script type="text/javascript" src="/assets/js/bootstrap-tour-standalone.min.js"></script>-->
<script type="text/javascript" src="/assets/js/style.js?{$thistime}"></script>

<!-- TRACKING CODES
-->

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
    domain: 'nota.kg'
});

woopra.identify({
    email: ' {$mydatar1} ',
    name: ' {$mydatar} '
});
woopra.track();
</script>
<!-- End of Woopra Code -->

<!-- Start GoogleAnalytics  -->
<script type="text/javascript">

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48948428-1', 'nota.kg');
  ga('send', 'pageview');

</script>
 <!-- End GoogleAnalytics -->


<!-- TRACKING CODES ENDS -->

<div style="margin-left: -250px; width: 500px;" class="modal_box display_none" id="lyrics_box">
    <div id="lyrics_box_close_button" class="modal_close_button"></div>
    <div class="modal_top" id="lyrics_box_title"></div>
    <div style="padding: 10px; text-align: center; font-size: inherit; font-family: DINRegular;" id="lyrics_box_content" class="modal_middle">
    </div>
</div>

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

	


//	die();


?>>>>>>>> .r3
