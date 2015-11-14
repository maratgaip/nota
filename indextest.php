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

$ajax = <<<HTML
<script language="javascript" type="text/javascript">
var player_root = '{$config['siteurl']}';
var genre_list = [{$genre_list}];
var mail_contact = 'webmaster@tancode.com';

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

$testingln = <<<HTML
<!DOCTYPE html>
<html>
<head></head>
<body><h1>No Logged In!!</h1></body>
</html>
HTML;

$username = $_SESSION['user_id'];

$mydataq = $db->query("SELECT username FROM `vass_users` WHERE user_id = '$username'");

while ($mydata1 = $db->get_row($mydataq)) {

	$mydatar .= '' . $mydata1['username'] .'"';

}

$mydatar = substr($mydatar, 0, ( strLen( $mydatar ) - 1 ) );



$mydataq1 = $db->query("SELECT email FROM `vass_users` WHERE user_id = '$username'");

while ($mydata2 = $db->get_row($mydataq1)) {

	$mydatar1 .= '' . $mydata2['email'] .'';

}

$mydatar1 = substr($mydatar1, 0, ( strLen( $mydatar1 ) ) );

if(!empty($_SESSION['user_id'])){

	echo $mydatar;
	echo "</br>";
	echo $mydatar1;

}


else{

	echo $testingln;

}




?>