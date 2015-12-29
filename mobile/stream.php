<?php

@session_start ();

@ob_start ();

@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );

@ini_set ( 'display_errors', false );

@ini_set ( 'html_errors', false );

@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

$song_id = trim(intval($_REQUEST['streamKey']));

header("Location: http://nota.kg//static/songs/" . $song_id . ".mp3");

?>
