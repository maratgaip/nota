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

$type = $db->safesql( $_REQUEST['t'] );

$artist_id = intval( $_REQUEST['id'] );

if(!$artist_id) die();

$type = $_REQUEST['type'];

if(file_exists(ROOT_DIR . '/static/artists/' . $artist_id . '_' . $type . '.jpg')){
	
	$artist_image = $config['siteurl'] . "static/artists/" . $artist_id . "_" . $type . ".jpg";
	
}else{

	$row = $db->super_query("SELECT name FROM vass_artists WHERE id = '$artist_id'");
	
	$api = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=" . urlencode($row['name']) . "&api_key=07bbb8c7f746330d2b4d4f67e1fdb837&format=json");
	
	$api = json_decode($api);
	
	$image = $api->{'artist'}->{'image'}[4]->{'#text'};
	
	if($image){
		$image = file_get_contents($image);
		$md5_rand =  md5(rand(1000, 9000));
		$fp = fopen(ROOT_DIR . "/static/artists/". $md5_rand, "w");
		fwrite($fp, $image);
		fclose($fp);
		require_once INCLUDE_DIR . '/class/_class_thumb.php';
		
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id. "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_small.jpg");
					@unlink(ROOT_DIR . "/static/artists/" . $md5_rand);
	}
	
	$artist_image =  $config['siteurl'] . "static/artists/" . $artist_id . "_" . $type . ".jpg";
}

$db->close ();

header ("Location: " . $artist_image);

?>
