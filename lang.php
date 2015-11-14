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

require_once ROOT_DIR . '/modules/functions.php';

$lang = $_REQUEST['get'];

if($lang == "pr"){
	require_once ROOT_DIR . '/language/pr.php';
	set_cookie( "lang", "pr", 365 );
}else if($lang == 'en'){
	require_once ROOT_DIR . '/language/en.php';
	set_cookie( "lang", "en", 365 );
}else{
	require_once ROOT_DIR . '/language/ru.php';
	set_cookie( "lang", "ru", 365 );
}
echo json_encode($lang);