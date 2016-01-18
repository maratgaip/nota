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

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once INCLUDE_DIR . '/member.php';

require_once ROOT_DIR . '/modules/functions.php';

header ( 'Content-type: text/json' );

header ( 'Content-type: application/json' );

$_TIME = date ( "Y-m-d H:i:s", time () );

if (isset ( $_REQUEST ['t'] ))
	$type = $_REQUEST ['t'];

if (isset ( $_REQUEST ['start'] ))
	$start = intval ( $_REQUEST ['start'] );

if ($type == "top") {
	
	      if(isset($_SESSION['country_Id'])){
	$top_date = trim( $_REQUEST ['date'] );
	
	if ($top_date =="the-day" ) $D_SORT = 1;
	elseif ($top_date =="the-week" ) $D_SORT = 7;
	elseif ($top_date =="the-month" ) $D_SORT = 30;
	elseif ($top_date =="the-year" ) $D_SORT = 356;
	elseif ($top_date =="all-time" ) $D_SORT = 800;
	
	$top_week = $db->query ( "SELECT COUNT(*) AS count, song_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY song_id ORDER by count DESC LIMIT $start,20" );
	
	$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM (SELECT COUNT(*) AS bit, song_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY song_id ORDER by bit DESC LIMIT 0,200) AS count" );
	
	while ( $top = $db->get_row ( $top_week ) ) {
		
		if ($top ['song_id']) {
			$row = $db->super_query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
			vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
			FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
			vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $top ['song_id'] . "' and vass_songs.song_country = '".$_SESSION['country_Id']."'" );
			$songs ['album'] = $row ['song_album'];
			$songs ['artist_id'] = $row ['artist_id'];
			$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
			$songs ['buy_link'] = null;
			$songs ['artist'] = $row ['song_artist'];
			$songs ['url'] = stream ( $row ['song_id'] );
			$songs ['image'] = songlist_images ( $row ['album_id'] );
			$songs ['title'] = $row ['song_title'];
			$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$songs ['sources'] = sources ( $row ['song_id'] );
			$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$songs ['last_loved'] = null;
			$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$songs ['aliases'] = aliases ( $row ['song_id'] );
			$songs ['loved_count'] = $row ['loved'];
			$songs ['id'] = $row ['song_id'];
                        $songs ['album_id'] = $row['album_id'];
			$songs ['tags'] = tags ( $row ['song_id'] );
			$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$songs ['user_love'] = "";
			$result ['songs'] [] = $songs;
		}
	}
        }
        else {
            $top_date = trim( $_REQUEST ['date'] );
	
	if ($top_date =="the-day" ) $D_SORT = 1;
	elseif ($top_date =="the-week" ) $D_SORT = 7;
	elseif ($top_date =="the-month" ) $D_SORT = 30;
	elseif ($top_date =="the-year" ) $D_SORT = 356;
	elseif ($top_date =="all-time" ) $D_SORT = 800;
	
	$top_week = $db->query ( "SELECT COUNT(*) AS count, song_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY song_id ORDER by count DESC LIMIT $start,20" );
	
	$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM (SELECT COUNT(*) AS bit, song_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY song_id ORDER by bit DESC LIMIT 0,200) AS count" );
	
	while ( $top = $db->get_row ( $top_week ) ) {
		
		if ($top ['song_id']) {
			$row = $db->super_query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
			vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
			FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
			vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $top ['song_id'] . "'" );
			$songs ['album'] = $row ['song_album'];
			$songs ['artist_id'] = $row ['artist_id'];
			$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
			$songs ['buy_link'] = null;
			$songs ['artist'] = $row ['song_artist'];
			$songs ['url'] = stream ( $row ['song_id'] );
			$songs ['image'] = songlist_images ( $row ['album_id'] );
			$songs ['title'] = $row ['song_title'];
			$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$songs ['sources'] = sources ( $row ['song_id'] );
			$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$songs ['last_loved'] = null;
			$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$songs ['aliases'] = aliases ( $row ['song_id'] );
			$songs ['loved_count'] = $row ['loved'];
			$songs ['id'] = $row ['song_id'];
                        $songs ['album_id'] = $row['album_id'];
			$songs ['tags'] = tags ( $row ['song_id'] );
			$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$songs ['user_love'] = "";
			$result ['songs'] [] = $songs;
		}
	}
        }
        
        
	
	$result ['status_text'] = "OK";
	$result ['status_code'] = "200";
	$result ['results'] = $total_results['count'];
	$result ['start'] = $start;
	$result ['total'] = $total_results['count'];
	
	echo json_encode ( $result );

} elseif ($type == "aotw") {
	
	$album = $db->super_query ( "SELECT vass_albums.name AS album_title, vass_albums.descr, vass_albums.date, vass_artists.name AS artist_name, vass_albums.id AS album_id FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id WHERE vass_albums.id='" . $config['album_week'] . "'" );
	
	$sql_result = $db->query ( "SELECT vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_albums.id = '" . $config['album_week'] . "'" );
	
	while ( $row = $db->get_row ( $sql_result ) ) {
		
		$song_list ['album'] = $row ['song_album'];
		$song_list ['similar_artists'] = similar_artists ( $row ['song_id'] );
		$song_list ['buy_link'] = null;
		$song_list ['artist'] = $row ['song_artist'];
		$song_list ['url'] = stream ( $row ['song_id'] );
		$song_list ['image'] = songlist_images ( $row ['album_id'] );
		$song_list ['title'] = $row ['song_title'];
		$song_list ['metadata_state'] = metadata_state ( $row ['song_id'] );
		$song_list ['sources'] = sources ( $row ['song_id'] );
		$song_list ['viewer_love'] = viewer_love ( $row ['song_id'] );
		$song_list ['last_loved'] = null;
		$song_list ['recent_loves'] = recent_loves ( $row ['song_id'] );
		$song_list ['aliases'] = aliases ( $row ['song_id'] );
		$song_list ['loved_count'] = $row ['loved'];
		$song_list ['id'] = $row ['song_id'];
		$song_list ['tags'] = tags ( $row ['song_id'] );
                $song_list ['album_id'] = $row ['album_id'];
		$song_list ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
		$songs [] = $song_list;
	
	}
	
	$buffer = array ("status_text" => "OK", "status_code" => 200, "results" => 1, "start" => 0, "total" => 1, "albums" => array ("description" => $album ['descr'], "artist" => $album ['artist_name'], "date" => date ( 'D M d Y H:i:s O', strtotime ( $album ['date'] ) ), "artwork_url" => $config['siteurl'] . "static/albums/" . $config['album_week'] ."_extralarge.jpg", "title" => $album ['album_title'], "day" => 20111005, "songs" => $songs ) );
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
} elseif ($type == "last_loved") {
	$sql_result = $db->query ( "SELECT DISTINCT vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id, vass_song_love.created_on, vass_users.username, vass_users.user_id FROM vass_song_love LEFT JOIN vass_friendship ON vass_friendship.follower_id = vass_song_love.user_id LEFT JOIN vass_songs ON vass_song_love.song_id = vass_songs.id LEFT JOIN vass_users ON vass_song_love.user_id = vass_users.user_id LEFT JOIN vass_albums on vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id ORDER BY vass_song_love.id DESC" );
	
	$start = $_REQUEST ['start'];
	
	$page_start = $start;
	
	$page_end = $start + 20;
	
	$total_results = $db->num_rows ( $sql_result );
	
	$i = 0;
	
	while ( $row = $db->get_row ( $sql_result ) ) {
		
		if ($i >= $page_start) {
			
			$object ['title'] = $row ['song_title'];
			$object ['object'] ['album'] = $row ['song_album'];
			$object ['object'] ['similar_artists'] = similar_artists ( $row ['song_id'] );
			$object ['object'] ['buy_link'] = null;
			$object ['object'] ['artist'] = $row ['song_artist'];
			$object ['object'] ['url'] = stream ( $row ['song_id'] );
			$object ['object'] ['image'] = songlist_images ( $row ['album_id'] );
			$object ['object'] ['title'] = $row ['song_title'];
			$object ['object'] ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$object ['object'] ['sources'] = sources ( $row ['song_id'] );
			$object ['object'] ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$object ['object'] ['last_loved'] = null;
			$object ['object'] ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$object ['object'] ['aliases'] = aliases ( $row ['song_id'] );
			$object ['object'] ['loved_count'] = $row ['loved'];
			$object ['object'] ['id'] = $row ['song_id'];
			$object ['object'] ['tags'] = tags ( $row ['song_id'] );
			$object ['object'] ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$object ['object'] ['user_love'] = array ("username" => $row ['username'], "created_on" => date ( 'D M d Y H:i:s O', strtotime ( $row ['created_on'] ) ) );
			$activities [] = $object;
		
		}
		$i ++;
		
		if ($i >= $page_end)
			break;
	}
	
	$buffer ['status_text'] = "OK";
	$buffer ['status_code'] = "200";
	$buffer ['results'] = $total_results;
	$buffer ['start'] = $start;
	$buffer ['total'] = $total_results;
	$buffer ['activities'] = $activities;
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );

} elseif ($type == "genre") {
	
	$name = $db->safesql ( $_REQUEST ['name'] );
	
	$row = $db->super_query ( "SELECT id FROM vass_genres WHERE name LIKE '%$name%' LIMIT 0,1" );
	
	$sql_result = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
	vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN vass_albums ON 
	vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id 
	WHERE vass_artists.tag REGEXP '[[:<:]]" . $row ['id'] . "[[:>:]]' LIMIT $start,200" );
	
	$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_songs LEFT JOIN vass_albums ON 
	vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id 
	WHERE vass_artists.tag REGEXP '[[:<:]]" . $row ['id'] . "[[:>:]]' LIMIT 0,200" );;
	
	while ( $row = $db->get_row ( $sql_result ) ) {
		
		$songs ['album'] = $row ['song_album'];
		$songs ['artist_id'] = $row ['artist_id'];
		$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
		$songs ['buy_link'] = null;
		$songs ['artist'] = $row ['song_artist'];
		$songs ['url'] = stream ( $row ['song_id'] );
		$songs ['image'] = songlist_images ( $row ['album_id'] );
		$songs ['title'] = $row ['song_title'];
		$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
		$songs ['sources'] = sources ( $row ['song_id'] );
		$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
		$songs ['last_loved'] = null;
		$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
		$songs ['aliases'] = aliases ( $row ['song_id'] );
		$songs ['loved_count'] = $row ['loved'];
		$songs ['id'] = $row ['song_id'];
		$songs ['tags'] = tags ( $row ['song_id'] );
                $songs['album_id'] = $row['album_id'];
		$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
		$songs ['user_love'] = null;
		$result ['songs'] [] = $songs;
	
	}
	
	$result ['status_text'] = "OK";
	$result ['status_code'] = "200";
	$result ['start'] = $start;
	$result ['total'] = $total_results['count'];
	echo json_encode ( $result );

} elseif ($type == "member") {
	$username = $db->safesql ( $_REQUEST ['username'] );
	
	$username = preg_replace ( "/[^a-zA-Z0-9\s]/", "", $username );
	
	$action = $db->safesql ( $_REQUEST ['action'] );
	
	if ($action == "loved") {
		
		$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE username = '" . $username . "'" );
		
		$sql_result = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id, vass_song_love.created_on, vass_users.username, vass_users.user_id FROM vass_song_love LEFT JOIN vass_songs ON vass_song_love.song_id = vass_songs.id LEFT JOIN vass_users ON vass_song_love.user_id = vass_users.user_id LEFT JOIN vass_albums on vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_love.user_id = '" . $row ['user_id'] . "' ORDER BY vass_song_love.id DESC" );
		
		$start = $_REQUEST ['start'];
		
		$page_start = $start;
		
		$page_end = $start + 20;
		
		$total_results = $db->num_rows ( $sql_result );
		
		$i = 0;
		
		while ( $row = $db->get_row ( $sql_result ) ) {
			
			if ($i >= $page_start) {
				
				$songs ['album'] = $row ['song_album'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['artist'] = $row ['song_artist'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'] );
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
				$songs ['user_love'] = array ("username" => $member_id ['username'] );
				$buffer ['songs'] [] = $songs;
			
			}
			
			$i ++;
			
			if ($i >= $page_end)
				break;
		
		}
		
		$buffer ['status_text'] = "OK";
		$buffer ['status_code'] = "200";
		$buffer ['results'] = $total_results;
		$buffer ['start'] = $start;
		$buffer ['total'] = $total_results;
	
	} elseif ($action == "feedlove") {
		
		$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE username = '" . $username . "'" );
		
		if ($username == "tastemakers") {
			
			$sql_result = $db->query ( "SELECT DISTINCT vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id, vass_song_love.created_on, vass_users.username, vass_users.user_id FROM vass_song_love LEFT JOIN vass_friendship ON vass_friendship.follower_id = vass_song_love.user_id LEFT JOIN vass_songs ON vass_song_love.song_id = vass_songs.id LEFT JOIN vass_users ON vass_song_love.user_id = vass_users.user_id LEFT JOIN vass_albums on vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id" );
		
		} else {
			
			$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE username = '" . $username . "'" );
			
			$sql_result = $db->query ( "SELECT DISTINCT vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id, vass_song_love.created_on, vass_users.username, vass_users.user_id FROM vass_song_love LEFT JOIN vass_friendship ON vass_friendship.follower_id = vass_song_love.user_id LEFT JOIN vass_songs ON vass_song_love.song_id = vass_songs.id LEFT JOIN vass_users ON vass_song_love.user_id = vass_users.user_id LEFT JOIN vass_albums on vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_friendship.user_id = '" . $row ['user_id'] . "' ORDER BY vass_song_love.id DESC" );
		}
		$start = $_REQUEST ['start'];
		
		$page_start = $start;
		
		$page_end = $start + 20;
		
		$total_results = $db->num_rows ( $sql_result );
		
		$i = 0;
		
		while ( $row = $db->get_row ( $sql_result ) ) {
			
			if ($i >= $page_start) {
				$object ['title'] = $row ['song_title'];
				$object ['object'] ['album'] = $row ['song_album'];
				$object ['object'] ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$object ['object'] ['buy_link'] = null;
				$object ['object'] ['artist'] = $row ['song_artist'];
				$object ['object'] ['url'] = stream ( $row ['song_id'] );
				$object ['object'] ['image'] = songlist_images ( $row ['album_id'] );
				$object ['object'] ['title'] = $row ['song_title'];
				$object ['object'] ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$object ['object'] ['sources'] = sources ( $row ['song_id'] );
				$object ['object'] ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$object ['object'] ['last_loved'] = null;
				$object ['object'] ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$object ['object'] ['aliases'] = aliases ( $row ['song_id'] );
				$object ['object'] ['loved_count'] = $row ['loved'];
				$object ['object'] ['id'] = $row ['song_id'];
				$object ['object'] ['tags'] = tags ( $row ['song_id'] );
				$object ['object'] ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
				$object ['object'] ['user_love'] = array ("username" => $row ['username'], "created_on" => date ( 'D M d Y H:i:s O', strtotime ( $row ['created_on'] ) ) );
				$activities [] = $object;
				/*
				 * $buffer .= '"user_love": { "username": ' . json_encode (
				 * $user_love ) . ', "comment": "", "context": "", "source": ' .
				 * json_encode ( $config['siteurl'] . 'song/' . $song_id ) . ',
				 * "created_on": "' . date( 'D M d Y H:i:s O', strtotime(
				 * $user_love_on ) ) . '", "client_id": "lala_web" },
				 */
			}
			
			$i ++;
			
			if ($i >= $page_end)
				break;
		
		}
		
		$buffer ['status_text'] = "OK";
		$buffer ['status_code'] = "200";
		$buffer ['results'] = $total_results;
		$buffer ['start'] = $start;
		$buffer ['total'] = $total_results;
		$buffer ['activities'] = $activities;
	
	} elseif ($action == "following") {
		
		$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1" );
		
		$sql_result = $db->query ( "SELECT vass_friendship.follower_id, vass_users.username, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.follower_id WHERE vass_friendship.user_id = '" . $row ['user_id'] . "';" );
		
		$start = $_REQUEST ['start'];
		
		$page_start = $start;
		
		$page_end = $start + 20;
		
		$total_results = $db->num_rows ( $sql_result );
		
		$i = 0;
		
		while ( $result = $db->get_row ( $sql_result ) ) {
			
			if ($i >= $page_start) {
				
				$buffer = $result;
				$buffer ['is_beta_tester'] = false;
				$buffer ['viewer_following'] = viewer_following ( $result ['follower_id'] );
				$buffer ['import_feeds'] = import_feeds ( $result ['user_id'] );
				$buffer ['image'] = avatar ( $result ['avatar'], $result ['username'] );
				
				$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $result ['user_id'] . "';" );
				
				if ($row ['image']) {
					$use_image = true;
					$is_default = false;
				} else {
					$is_default = true;
					$use_image = false;
				}
				
				$buffer ['background'] = $row;
				$buffer ['background'] ['is_default'] = $is_default;
				$buffer ['background'] ['use_image'] = $use_image;
				
				unset ( $buffer ['password'] );
				
				$following [] = $buffer;
			
			}
			$i ++;
			
			if ($i >= $page_end)
				break;
		}
		
		$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "following" => $following, "total" => $total_results );
	
	} elseif ($action == "followers") {
		
		$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1" );
		
		$sql_result = $db->query ( "SELECT vass_users.user_id, vass_users.username, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.user_id WHERE vass_friendship.follower_id = '" . $row ['user_id'] . "';" );
		
		$start = $_REQUEST ['start'];
		
		$page_start = $start;
		
		$page_end = $start + 20;
		
		$total_results = $db->num_rows ( $sql_result );
		
		$i = 0;
		
		while ( $result = $db->get_row ( $sql_result ) ) {
			
			if ($i >= $page_start) {
				
				$buffer = $result;
				$buffer ['is_beta_tester'] = false;
				$buffer ['viewer_following'] = viewer_following ( $result ['user_id'] );
				$buffer ['import_feeds'] = import_feeds ( $result ['user_id'] );
				$buffer ['image'] = avatar ( $result ['avatar'], $result ['username'] );
				
				$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $result ['user_id'] . "';" );
				
				if ($row ['image']) {
					$use_image = true;
					$is_default = false;
				} else {
					$is_default = true;
					$use_image = false;
				}
				
				$buffer ['background'] = $row;
				$buffer ['background'] ['is_default'] = $is_default;
				$buffer ['background'] ['use_image'] = $use_image;
				
				unset ( $buffer ['password'] );
				
				$followers [] = $buffer;
			
			}
			$i ++;
			
			if ($i >= $page_end)
				break;
		}
		
		$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "followers" => $followers, "total" => $total_results );
	
	} elseif ($action == "tastemakers") {
		
		$sql_result = $db->query ( "SELECT DISTINCT vass_friendship.follower_id, vass_users.username, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar FROM vass_users LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.follower_id" );
		
		$total_results = $db->num_rows ( $sql_result );
		
		while ( $result = $db->get_row ( $sql_result ) ) {
			
				
				$buffer = $result;
				$buffer ['is_beta_tester'] = false;
				$buffer ['viewer_following'] = viewer_following ( $result ['user_id'] );
				$buffer ['import_feeds'] = import_feeds ( $result ['user_id'] );
				$buffer ['image'] = avatar ( $result ['avatar'], $result ['username'] );
				
				$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $result ['user_id'] . "';" );
				
				if ($row ['image']) {
					$use_image = true;
					$is_default = false;
				} else {
					$is_default = true;
					$use_image = false;
				}
				
				$row['is_default'] = $is_default;
				$row['use_image'] = $use_image;
				$buffer ['background'] = $row;
				$following [] = $buffer;
			
		}
		
		if (! $following)
			$following = "";
		
		$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "following" => $following, "total" => $total_results );

		
	} elseif ($action == "notifications") {
		
		$buffer = '{
			    "status_text": "OK", 
			    "status_code": 200, 
			    "results": 0, 
			    "sites": [], 
			    "start": 0, 
			    "total": 0
			}';
	
	} elseif ($action == "playlist") {
		
            
                
		$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1" );
		
		$sql_result = $db->query ( "SELECT vass_playlists.name, vass_playlists.date, vass_playlists.id AS playlist_id, vass_playlists.cover, vass_playlists.descr,
		vass_users.username 
		FROM vass_playlists LEFT JOIN vass_users ON vass_playlists.user_id = vass_users.user_id WHERE vass_playlists.user_id = '" . $row ['user_id'] . "';" );
		
		$start = $_REQUEST ['start'];
		
		$page_start = $start;
		
		$page_end = $start + 20;
		
		$total_results = $db->num_rows ( $sql_result );
		
		$i = 0;
		
		while ( $result = $db->get_row ( $sql_result ) ) {
			
			if ($i >= $page_start) {
				
				$buffer = $result;
				
				$playlists [] = $buffer;
			
			}
			$i ++;
			
			if ($i >= $page_end)
				break;
		}
		
		$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "playlists" => $playlists, "total" => $total_results );
	
	} elseif ($username) {
		
		$row = $db->super_query ( "SELECT vass_friendship.user_id, vass_users.user_id, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.follower_id WHERE vass_users.username = '" . $username . "';" );
		
		if (! $row ['user_id']) {
			
			header ( 'HTTP/1.0 403 Not Found' );
			$buffer ['status_code'] = 400;
			$buffer ['status_text'] = "Unknown user {$username}.";
		
		} else {
			
			$buffer ['status_code'] = 200;
			
			$buffer ['status_text'] = "OK";
			
			$buffer ['user'] = $row;
			$buffer ['user'] ['is_beta_tester'] = false;
			$buffer ['user'] ['viewer_following'] = viewer_following ( $row ['user_id'] );
			$buffer ['user'] ['import_feeds'] = import_feeds ( $row ['user_id'] );
			$buffer ['user'] ['image'] = avatar ( $row ['avatar'], $username );
			$total_playlist = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_playlists WHERE user_id = '" . $member_id['user_id'] . "';" );
			$buffer ['user'] ['total_playlist'] = $total_playlist['count'];
		
			$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $row ['user_id'] . "';" );
			
			if ($row ['image']) {
				$use_image = true;
				$is_default = false;
			} else {
				$is_default = true;
				$use_image = false;
			}
			
			$buffer ['user'] ['background'] = $row;
			$buffer ['user'] ['background'] ['is_default'] = $is_default;
			$buffer ['user'] ['background'] ['use_image'] = $use_image;
			
			unset ( $buffer ['user'] ['password'] );
		
		}
	}
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
} elseif ($type == "profile") {
	
	if (! $logged) {
		
		header ( "HTTP/1.0 401 UNAUTHORIZED" );
		
		$buffer ['status_code'] = 401;
		
		$buffer ['status_text'] = "Authentication required.";
	
	} else {
		
		$username = $db->safesql ( $_REQUEST ['username'] );
		
		$username = preg_replace ( "/[^a-zA-Z0-9\s]/", "", $username );
		
		$action = $db->safesql ( $_REQUEST ['action'] );
		
		if ($action == "maybe-friends") {
			
			$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "users" => array (), "total" => $total_results );
		
		} elseif ($action == "notifications") {
			
			$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "sites" => array (), "total" => $total_results );
		
		} elseif ($action == "follow") {
			
			$row = $db->super_query ( "SELECT user_id, avatar FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1" );
			
			$db->query ( "INSERT IGNORE INTO vass_friendship SET user_id = '" . $member_id ['user_id'] . "', follower_id = '" . $row ['user_id'] . "'" );
			
			$db->query ( "UPDATE vass_users SET total_following  = total_following+1 WHERE user_id = '" . $member_id ['user_id'] . "'" );
			
			$db->query ( "UPDATE vass_users SET total_followers  = total_followers+1 WHERE user_id = '" . $row ['user_id'] . "'" );
			
			$buffer = array ("status_code" => 200, "status_text" => "OK", "user" => array ("username" => $username, "image" => avatar ( $row ['avatar'], $username ) ) );
		
		} elseif ($action == "unfollow") {
			
			$row = $db->super_query ( "SELECT user_id, avatar FROM vass_users WHERE username = '" . $username . "' LIMIT 0,1" );
			
			$db->query ( "DELETE FROM vass_friendship WHERE user_id = '" . $member_id ['user_id'] . "' AND follower_id = '" . $row ['user_id'] . "'" );
			
			$db->query ( "UPDATE vass_users SET total_following  = total_following-1 WHERE user_id = '" . $member_id ['user_id'] . "'" );
			
			$db->query ( "UPDATE vass_users SET total_followers  = total_followers-1 WHERE user_id = '" . $row ['user_id'] . "'" );
			
			$buffer = array ("status_code" => 200, "status_text" => "OK", "user" => array ("username" => $username, "image" => avatar ( $row ['avatar'], $username ) ) );
		
		} else {
			
			$buffer ['status_code'] = 200;
			
			$buffer ['status_text'] = "OK";
			
			$buffer ['user'] = $member_id;
			$buffer ['user'] ['is_beta_tester'] = false;
			$buffer ['user'] ['viewer_following'] = viewer_following ( $member_id ['user_id'] );
			$buffer ['user'] ['import_feeds'] = import_feeds ( $member_id ['user_id'] );
			$buffer ['user'] ['image'] = avatar ( $member_id ['avatar'], $member_id ['username'] );
			
			$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id ['user_id'] . "';" );
			
			if ($row ['image']) {
				$use_image = "true";
				$is_default = "false";
			} else {
				$is_default = "true";
				$use_image = "false";
			}
			
			$buffer ['user'] ['background'] = $row;
			$buffer ['user'] ['background'] ['is_default'] = $is_default;
			$buffer ['user'] ['background'] ['use_image'] = $use_image;
			
			unset ( $buffer ['user'] ['password'] );
		
		}
	}
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );

}
elseif ($type == "favouriteArtist"){
    if (! $_REQUEST ['id']) {
		
		header ( 'HTTP/1.0 404 Not Found' );
	
	}
        else{
             
             $artist_id = ($_REQUEST ['id']);
             $user_id = ($_REQUEST ['userid']);
             $row1 = $db->query ("SELECT likecount FROM vass_artists WHERE id = '" . $artist_id . "'");
             $rowuser = $db->get_row($row1);

     
             $rowuser2[] = $rowuser['likecount'];
             array_push($rowuser2,$user_id);
             $rowuser2 = implode(",", $rowuser2);
        
             $row = $db->query ("UPDATE vass_artists SET likecount= '" .$rowuser2. "' WHERE id = '" . $artist_id . "'" );
        
        }
        $buffer = array ("status_code" => "200","user" => $user_id );
	
	print json_encode ( $buffer );
    
}
 
elseif ($type == "unlikeArtist"){

             $artist_id = ($_REQUEST ['id']);
             $user_id = ($_REQUEST ['userid']);
             $row1 = $db->query ("SELECT likecount FROM vass_artists WHERE id = '" . $artist_id . "'");
             $rowuser = $db->get_row($row1);
            $rowuser3 = explode(",", $rowuser['likecount']);
            
            if(($key = array_search($user_id,$rowuser3)) !== false){
            unset($rowuser3[$key]);
            $rowuser3 = implode(",", $rowuser3);
                
            }
             $row = $db->query ("UPDATE vass_artists SET likecount= '" .$rowuser3. "' WHERE id = '" . $artist_id . "'" );
           
        $buffer = array ("status_code" => "200" );
	
	print json_encode ( $buffer );
}

elseif ($type == "me") {
	
	if (! $member_id) {
		
		header ( "HTTP/1.0 401 UNAUTHORIZED" );
		
		$buffer ['status_code'] = 401;
		
		$buffer ['status_text'] = "Authentication required.";
	
	} else {
		
		$buffer ['status_code'] = 200;
		
		$buffer ['status_text'] = "OK";
		
		$buffer ['user'] = $member_id;
		$buffer ['user'] ['is_beta_tester'] = false;
		$buffer ['user'] ['viewer_following'] = false;
		$buffer ['user'] ['import_feeds'] = import_feeds ( $member_id ['user_id'] );
		$buffer ['user'] ['image'] = avatar ( $member_id ['avatar'], $member_id ['username'] );
		
		$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id ['user_id'] . "';" );
		
		if ($row ['image']) {
			$use_image = true;
			$is_default = false;
		} else {
			$is_default = true;
			$use_image = false;
		}
		
		$buffer ['user'] ['background'] = $row;
		$buffer ['user'] ['background'] ['is_default'] = $is_default;
		$buffer ['user'] ['background'] ['use_image'] = $use_image;
		
		unset ( $buffer ['user'] ['password'] );
	
	}
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
} elseif ($type == "trending") {
	
	$_DATE = date ( "d", time () );
	
		$genre = $db->safesql($_REQUEST['genre']);
		if(isset($_SESSION['country_Id'])) {
		$genre_id = $db->super_query("SELECT id FROM vass_genres WHERE name LIKE '%". $genre . "%'");
		
		$trending_day = $db->query ( "SELECT COUNT(*) AS count, song_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time ()  - 7*24*3600) ) . "' GROUP BY song_id ORDER by count DESC LIMIT 0,20" );
		
		$i = 1;
		
		while ( $trending = $db->get_row ( $trending_day ) ) {
			
				if(!empty($genre)){
				$row = $db->super_query ( "SELECT vass_songs.id song_id, vass_songs.artist_id, 
				vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
					vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $trending ['song_id'] . "' AND vass_songs.tags REGEXP '[[:<:]]" . $genre_id['id'] . "[[:>:]]' AND vass_songs.song_country = '".$_SESSION['country_Id']."' " );
			}else{
				$row = $db->super_query ( "SELECT vass_songs.id AS song_id, vass_songs.artist_id, 
				vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $trending ['song_id'] . "' AND vass_songs.song_country = '".$_SESSION['country_Id']."' " );
			}
			
			
			if($row ['song_id']){
				$songs ['album'] = $row ['song_album'];
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['artist'] = $row ['song_artist'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'] );
				$songs ['artist_image'] = artist_images ( $row ['album_id'], $row ['artist_id'] );
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = $i;
				$result ['songs'] [] = $songs;
			}else $result ['songs'] = array();
			$i ++;
		}
                }
                else{
                    $genre_id = $db->super_query("SELECT id FROM vass_genres WHERE name LIKE '%". $genre . "%'");
		
		$trending_day = $db->query ( "SELECT COUNT(*) AS count, song_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time ()  - 7*24*3600) ) . "' GROUP BY song_id ORDER by count DESC LIMIT 0,20" );
		
		$i = 1;
		
		while ( $trending = $db->get_row ( $trending_day ) ) {
			
				if(!empty($genre)){
				$row = $db->super_query ( "SELECT vass_songs.id song_id, vass_songs.artist_id, 
				vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
					vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $trending ['song_id'] . "' AND vass_songs.tags REGEXP '[[:<:]]" . $genre_id['id'] . "[[:>:]]' " );
			}else{
				$row = $db->super_query ( "SELECT vass_songs.id AS song_id, vass_songs.artist_id, 
				vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $trending ['song_id'] . "'" );
			}
			
			
			if($row ['song_id']){
				$songs ['album'] = $row ['song_album'];
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['artist'] = $row ['song_artist'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'] );
				$songs ['artist_image'] = artist_images ( $row ['album_id'], $row ['artist_id'] );
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = $i;
				$result ['songs'] [] = $songs;
			}else $result ['songs'] = array();
			$i ++;
		}
                }
                
		$result ['trending_date'] = date ( "Y-m-d", time () );
		$result ['status_text'] = "OK";
		$result ['status_code'] = "200";
		$result ['start'] = 0;
		
		if ($result && $i > 19)
			cache ( "trending/" . $_DATE, json_encode ( $result ) );
		
		echo json_encode ( $result );
	
	//} else {
		
	//	echo $trending_json;
	
	//}

} elseif ($type == "search") {
	$qtxt = $_REQUEST [q];
	
	$qtxt = stripUnicode ( strtolower($qtxt) );
	
	$qtxt = makekeyword ( $qtxt );
	
	$sql_result = $db->query ( "SELECT DISTINCT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id WHERE LOWER(vass_songs.title) LIKE '%$qtxt%' or LOWER(vass_artists.name) LIKE '%$qtxt%' or LOWER(vass_albums.name) LIKE '%$qtxt%'" );
	
	$start = $_REQUEST ['start'];
	
	$page_start = $start;
	
	$page_end = $start + 20;
	
	$total_results = $db->num_rows ( $sql_result );
	
	$i = 0;
	
	while ( $row = $db->get_row ( $sql_result ) ) {
		
		if ($i >= $page_start) {
			
			$songs ['album'] = $row ['song_album'];
			$songs ['artist_id'] = $row ['artist_id'];
			$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
			$songs ['buy_link'] = null;
			$songs ['artist'] = $row ['song_artist'];
			$songs ['url'] = stream ( $row ['song_id'] );
			$songs ['image'] = songlist_images ( $row ['album_id'] );
			$songs ['title'] = $row ['song_title'];
			$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$songs ['sources'] = sources ( $row ['song_id'] );
			$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$songs ['last_loved'] = null;
			$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$songs ['aliases'] = aliases ( $row ['song_id'] );
			$songs ['loved_count'] = $row ['loved'];
			$songs ['id'] = $row ['song_id'];
                        $songs ['album_id'] = $row ['album_id'];
			$songs ['tags'] = tags ( $row ['song_id'] );
			$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$result ['songs'] [] = $songs;
		}
		
		$i ++;
		
		if ($i >= $page_end)
			break;
	
	}
	
	$result ['status_text'] = "OK";
	$result ['status_code'] = "200";
	$result ['results'] = $total_results;
	$result ['start'] = $start;
	$result ['total'] = $total_results;
	
	$db->close ();
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );

	//print json_encode ( $result );

/* Namba Search started */

echo file_get_contents('http://namba.kg/#!/search/mp3/' . $qtxt);
$stringData = file_get_contents("http://namba.kg/api/?service=home&action=search&type=mp3&query=" . $qtxt . "&page=1&sort=desc&country_id=0&city_id=0");
$data = json_decode($stringData);
//$data->{'mp3Files'} = "songs";

$object = (object) array(
'songs'   => $data->mp3Files,
'server' => $data->filesBackend,
'query'  => $data->query
);

ob_end_clean();
print json_encode($data);

/* Namba Search finished */

/* Super.kg Search started */

echo file_get_contents('api/search.php?pg=1&q=' . $qtxt);

/* Super.kg Search finished */


} elseif ($type == "love") {
	
	if (! $logged) {
		
		header ( "HTTP/1.0 401 UNAUTHORIZED" );
		
		$buffer ['status_code'] = 401;
		
		$buffer ['status_text'] = "Authentication required.";
	
	} else {
		
		$songid = $db->safesql ( $_REQUEST ['songid'] );
		
		$action = $db->safesql ( $_REQUEST ['action'] );
		
		if ($action == "love") {
			
			$song_id = $db->safesql ( $_REQUEST ['songid'] );
			
			$db->query ( "INSERT IGNORE INTO vass_song_love SET song_id = '" . $songid . "', user_id= '" . $member_id ['user_id'] . "', created_on = '" . date ( "Y-m-d H:i:s", time () ) . "'" );
			
			$db->query ( "UPDATE vass_songs SET loved = loved+1, last_loved = '" . date ( "Y-m-d H:i:s", time () ) . "' WHERE id = '" . $songid . "'" );
			
			$db->query ( "UPDATE vass_users SET total_loved = total_loved+1 WHERE user_id= '" . $member_id ['user_id'] . "'" );
			
			$buffer ['status_code'] = 200;
			
			$buffer ['status_text'] = "Added song to lover.";
		
		} elseif ($action == "unlove") {
			
			$db->query ( "DELETE FROM vass_song_love WHERE song_id = '" . $songid . "' AND user_id= '" . $member_id ['user_id'] . "'" );
			
			$db->query ( "UPDATE vass_songs SET loved = loved-1 WHERE id = '" . $songid . "'" );
			
			$db->query ( "UPDATE vass_users SET total_loved = total_loved-1 WHERE user_id= '" . $member_id ['user_id'] . "'" );
			
			$buffer ['status_code'] = 200;
			
			$buffer ['status_text'] = "removed song to lover.";
			
			$buffer ['song'] ['id'] = $songid;
		
		}
	}
	
	$db->close ();
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
} elseif ($type == "now_playing") {
	if (! $_REQUEST ['songid']) {
		
		header ( 'HTTP/1.0 404 Not Found' );
		
		$buffer = '{
    				"status_code": 401, 
    				"status_text": "Authentication required"
				}';
	
	} else {
		
		$song_id = intval ( $_REQUEST ['songid'] );
                $album_name = ( $_REQUEST ['album']);
                $artist_name = ($_REQUEST ['artist']);
                $user_id=($_REQUEST ['user']);
// 		echo"<pre>"; print_r($artist_id); echo"</pre>"; die;
               
		$db->query ( "UPDATE vass_songs SET played=played+1 WHERE id = '" . $song_id . "'" );
                
                $db->query ("UPDATE vass_albums SET playcount=playcount+1 WHERE name = '" . $album_name . "'" );
           
                $db->query ("UPDATE vass_artists SET playcount=playcount+1 WHERE name = '" . $artist_name . "'" );
           
                $row1= $db->super_query ( "SELECT id from vass_albums WHERE name = '" . $album_name . "'" );
                $row2= $db->super_query ( "SELECT id from vass_artists WHERE name = '" . $artist_name . "'" ); 
                
                $db->query ( "INSERT INTO vass_analz SET `time`= '$_TIME', song_id = '" . $song_id . "',album_id = '" . $row1['id'] . "',artist_id = '" . $row2['id'] ."', user_id = '".$user_id."' " );
               
	
	}
	$buffer = array ("status_code" => "200" );
	
	print json_encode ( $buffer );

} elseif ($type == "song") {
	if (! $_REQUEST ['songid']) {
		
		header ( 'HTTP/1.0 404 Not Found' );
		
		$buffer ['status_code'] = 404;
		
		$buffer ['status_text'] = "NOT FOUND.";
	
	} else {
		
		$song_id = $db->safesql ( $_REQUEST ['songid'] );
		
		$row = $db->super_query ( "SELECT vass_songs.artist_id, vass_songs.created_on, vass_songs.artist_id, vass_artists.tag AS tags, vass_songs.id AS song_id, vass_songs.title AS song_title, 
		vass_songs.loved, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
		FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id  LEFT JOIN vass_artists ON 
		vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $song_id . "' LIMIT 0,1" );
		
		if ($row ['song_title']) {
			
			$songs ['album'] = $row ['song_album'];
			$songs ['artist_id'] = $row ['artist_id'];
			$songs ['created_on'] = date ( 'D M d Y H:i:s O', strtotime ( $row ['created_on'] ) );
			$songs ['artist_id'] = $row ['artist_id'];
			$songs ['similar_artists'] = similar_artists ( $row ['tags'] );
			$songs ['buy_link'] = null;
			$songs ['artist'] = $row ['song_artist'];
			$songs ['url'] = stream ( $row ['song_id'] );
			$songs ['image'] = songlist_images ( $row ['album_id'] );
			$songs ['title'] = $row ['song_title'];
			$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$songs ['sources'] = sources ( $row ['song_id'] );
			$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$songs ['last_loved'] = null;
			$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$songs ['aliases'] = aliases ( $row ['song_id'] );
			$songs ['loved_count'] = $row ['loved'];
			$songs ['id'] = $row ['song_id'];
                        $songs ['album_id'] = $row ['album_id'];
			$songs ['tags'] = tags ( $row ['tags'] );
			$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$songs ['user_love'] = null;
			
			$buffer ['status_code'] = 200;
			$buffer ['status_text'] = "OK";
			$buffer ['song'] = $songs;
		
		} else {
			
			header ( 'HTTP/1.0 404 Not Found' );
			
			$buffer ['status_code'] = 404;
			
			$buffer ['status_text'] = "NOT FOUND.";
		
		}
	}
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
} elseif ($type == "sotd") {
	echo '{
	    "status_text": "OK", 
	    "status_code": 200, 
	    "results": 1, 
	    "sites": [
	    ], 
	    "start": 0, 
	    "total": 5
	}';

} elseif ($type == "settings") {
	if (! $logged) {
		
		header ( "HTTP/1.0 401 UNAUTHORIZED" );
		
		$buffer ['status_code'] = 401;
		
		$buffer ['status_text'] = "Authentication required.";
	
	} else {
		
		$username = $db->safesql ( $_REQUEST ['username'] );
		
		$action = $db->safesql ( $_REQUEST ['action'] );
		
		if ($action == "maybe-friends") {
			
			$buffer ['status_code'] = 200;
			
			$buffer ['users'] [] = null;
			
			$buffer ['status_text'] = "OK";
			
			$buffer ['results'] = 20;
			
			$buffer ['start'] = 0;
			
			$buffer ['total'] = 0;
		
		} elseif( $action == "tastemakers" ){
		
		$sql_result = $db->query("SELECT vass_users.user_id, vass_users.username, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image
FROM vass_users
LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id
ORDER BY vass_users.user_id LIMIT 0,10");
		
		
			$start = $_REQUEST['start'];
	
	$page_start = $start;
	
	$page_end = $start + 20;
	
	$total_results = $db->num_rows( $sql_result );
	
	$i = 0;
	
	
	while ($result = $db->get_row($sql_result)){
		
		if ( $i >= $page_start ){
			
	
	$folow = $db->super_query("SELECT follower_id FROM vass_friendship WHERE follower_id = '" . $result['user_id'] . "'");
			
	$buffer = $result;
	$buffer['is_beta_tester'] = false;
	$buffer['viewer_following'] = viewer_following($folow['follower_id']);
	$buffer['import_feeds'] = import_feeds($result['user_id']);
	$buffer['image'] = avatar( $result['avatar'], $result['username'] );
	
	
	$row = $db->super_query("SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $result['user_id'] . "';");
		
	if( $row['image'] ) {
		$use_image = true;
		$is_default = false;
	} else {
		$is_default = true;
		$use_image = false;
	}
		
	$buffer['background'] = $row;
	$buffer['background']['is_default'] = $is_default;
	$buffer['background']['use_image'] = $use_image;

	unset($buffer['password']);
	
	
	$following[] = $buffer;
			
		}
		$i++;
		
		if ($i >= $page_end) break;
	}
	
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"following" => $following, "total" => $total_results);
	
	}elseif ($action == "search") {
			
			$keyword = $db->safesql ( $_REQUEST ['q'] );
			
			$sql_result = $db->query ( "SELECT DISTINCT vass_friendship.follower_id, vass_users.username, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.follower_id WHERE vass_users.name LIKE '%$keyword%' or vass_users.email LIKE '%$keyword%' or vass_users.username LIKE '%$keyword%'" );
			
			$start = $_REQUEST ['start'];
			
			$page_start = $start;
			
			$page_end = $start + 20;
			
			$total_results = $db->num_rows ( $sql_result );
			
			$i = 0;
			
			while ( $result = $db->get_row ( $sql_result ) ) {
				
				if ($i >= $page_start) {
					
					$buffer = $result;
					$buffer ['is_beta_tester'] = false;
					$buffer ['viewer_following'] = viewer_following ( $result ['follower_id'] );
					$buffer ['import_feeds'] = import_feeds ( $result ['user_id'] );
					$buffer ['image'] = avatar ( $result ['avatar'], $result ['username'] );
					
					$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $result ['user_id'] . "';" );
					
					if ($row ['image']) {
						$use_image = true;
						$is_default = false;
					} else {
						$is_default = true;
						$use_image = false;
					}
					
					$buffer ['background'] = $row;
					$buffer ['background'] ['is_default'] = $is_default;
					$buffer ['background'] ['use_image'] = $use_image;
					
					unset ( $buffer ['password'] );
					
					$users [] = $buffer;
				
				}
				$i ++;
				
				if ($i >= $page_end)
					break;
			}
			
			if (! $users)
				$users = "";
			
			$buffer = array ("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start, "users" => $users, "total" => $total_results );
		
		} elseif ($action == "notifications") {
			
			$buffer = '{
					    "status_text": "OK", 
					    "status_code": 200, 
					    "results": 0, 
					    "sites": [], 
					    "start": 0, 
					    "total": 0
					}';
		
		} elseif ($action == "background") {
			
			$color = $db->safesql ( $_POST ['color'] );
			
			$image = $db->safesql ( $_POST ['image'] );
			
			$position = $db->safesql ( $_POST ['position'] );
			
			$repeat = $db->safesql ( $_POST ['repeat'] );
			
			$use_image = $db->safesql ( $_POST ['use_image'] );
			
			if ($color)
				$db->query ( "INSERT INTO vass_background SET `user_id` = '" . $member_id ['user_id'] . "', `color` = '$color', `image` = '$image', `position` = '$position', `repeat` = '$repeat', `use_image` = '$use_image' ON DUPLICATE KEY UPDATE `color` = '$color', `image` = '$image', `position` = '$position', `repeat` = '$repeat', `use_image` = '$use_image';" );
			
			$buffer ['status_code'] = 200;
			$buffer ['status_text'] = "OK";
			$buffer ['user'] = $member_id;
			$buffer ['user'] ['is_beta_tester'] = false;
			$buffer ['user'] ['viewer_following'] = viewer_following ( $member_id ['user_id'] );
			$buffer ['user'] ['import_feeds'] = import_feeds ( $member_id ['user_id'] );
			$buffer ['user'] ['image'] = avatar ( $member_id ['avatar'], $member_id ['username'] );
			
			$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id ['user_id'] . "';" );
			
			if ($row ['image']) {
				$use_image = true;
				$is_default = false;
			} else {
				$is_default = true;
				$use_image = false;
			}
			
			$buffer ['user'] ['background'] = $row;
			$buffer ['user'] ['background'] ['is_default'] = $is_default;
			$buffer ['user'] ['background'] ['use_image'] = $use_image;
			
			unset ( $buffer ['user'] ['password'] );
		
		} elseif ($action == "email") {
			
			if(trim($_POST['email'])){
				$db->query("UPDATE vass_users SET email = '" . trim($db->safesql($_POST['email'])) . "' WHERE user_id = '" . $member_id ['user_id'] . "'");
				
				$member_id ['email'] = trim($_POST['email']);
			}
			
			$buffer = array ("status_code" => 200, "status_text" => "OK", "user" => array ("email" => $member_id ['email'] ) );
		
		} elseif ($action == "services") {
			
			if (isset ( $_REQUEST ['force'] ) && $_REQUEST ['force'] == "remove") {
				
				if (isset ( $_REQUEST ['social'] ))
					$social = $_REQUEST ['social'];
				
				if ($social == "facebook") {
					$buffer ['status_code'] = 200;
					$buffer ['removed'] = "facebook";
					$buffer ['status_text'] = "OK";
					$db->query("DELETE FROM vass_facebook WHERE user_id = '" . $member_id['user_id'] . "'");
				}elseif ($social == "twitter") {
					$db->query("DELETE FROM vass_twitter WHERE user_id = '" . $member_id['user_id'] . "'");
					$buffer ['status_code'] = 200;
					$buffer ['removed'] = "twitter";
					$buffer ['status_text'] = "OK";
				}
			} else {
				
				$row = $db->super_query ( "SELECT screen_id AS twitter_screen_id, screen_name AS twitter_screen_name, date AS twitter_date FROM vass_twitter WHERE user_id = '" . $member_id ['user_id'] . "'" );
				
				if ($row ['twitter_screen_id']) {
					$service ['twitter'] ['name'] = $row ['twitter_screen_name'];
					$service ['twitter'] ['last_refresh'] = date ( 'D M d Y H:i:s O', strtotime ( $row ['twitter_date'] ) );
					$service ['twitter'] ['pic'] = "http://api.twitter.com/1/users/profile_image?screen_name=" . $row ['twitter_screen_name'] . "&size=bigger";
					$service ['twitter'] ['lookup_id'] = $row ['twitter_screen_name'];
					$service ['twitter'] ['type'] = "twitter";
					$service ['twitter'] ['added_on'] = date ( 'D M d Y H:i:s O', strtotime ( $row ['twitter_date'] ) );
				}
				
				$row = $db->super_query ( "SELECT screen_id AS facebook_screen_id, screen_name AS facebook_screen_name, date AS facebook_date FROM vass_facebook WHERE user_id = '" . $member_id ['user_id'] . "'" );
				
				if ($row ['facebook_screen_id']) {
					
					$service ['facebook'] ['name'] = $row ['facebook_screen_name'];
					$service ['facebook'] ['last_refresh'] = date ( 'D M d Y H:i:s O', strtotime ( $row ['facebook_date'] ) );
					$service ['facebook'] ['pic'] = "https://graph.facebook.com/" . $row ['facebook_screen_id'] . "/picture?type=large";
					$service ['facebook'] ['lookup_id'] = $row ['facebook_screen_id'];
					$service ['facebook'] ['type'] = "facebook";
					$service ['facebook'] ['added_on'] = date ( 'D M d Y H:i:s O', strtotime ( $row ['facebook_date'] ) );
				}
				
				if (! $service)
					$service = "";
				
				$buffer = array ("status_code" => 200, "status_text" => "OK", "services" => $service );
			}
		
		} elseif ($action == "password") {
			
			$password = $db->safesql ( md5 ( $_POST ['password'] ) );
			
			$new_password = $db->safesql ( md5 ( $_POST ['new_password'] ) );
			
			$confirm_new_password = $db->safesql ( md5 ( $_POST ['confirm_new_password'] ) );
			
			$row = $db->super_query ( "SELECT user_id FROM vass_users WHERE user_id = '" . $member_id ['user_id'] . "' AND password = '" . $password . "'" );
			
			if (! $row ['user_id']) {
				$buffer = array ("status_code" => 400, "status_text" => "Old password is incorrect" );
			
			} else {
				
				$db->query ( "UPDATE vass_users SET password = '$new_password' WHERE user_id = '" . $member_id ['user_id'] . "'" );
                                $member_id['password'] = $new_password;
				
                                set_cookie( "login_pass", $member_id['password'], 365 );
			        $_SESSION['user_id'] = $member_id['user_id'];
			        $_SESSION['login_pass'] = $member_id['password'];
				$buffer = array ("status_code" => 200, "status_text" => "OK", "success" => true );
			
			}
		
		} elseif ($action == "profile") {
			
			$bio = $db->safesql ( $_POST ['bio'] );
			
			$location = $db->safesql ( $_POST ['location'] );
			
			$name = $db->safesql ( $_POST ['name'] );
			
			$website = $db->safesql ( $_POST ['website'] );
			
			$db->query ( "UPDATE vass_users SET bio = '$bio', location = '$location', name = '$name', website = '$website' WHERE user_id = '" . $member_id ['user_id'] . "'" );
			
			$row = $db->super_query ( "SELECT vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id WHERE vass_users.username = '" . $username . "';" );
			
			$buffer ['status_code'] = 200;
			
			$buffer ['status_text'] = "OK";
			
			$buffer ['user'] = $row;
			$buffer ['user'] ['is_beta_tester'] = false;
			$buffer ['user'] ['viewer_following'] = viewer_following ( $member_id ['user_id'] );
			$buffer ['user'] ['import_feeds'] = import_feeds ( $member_id ['user_id'] );
			$buffer ['user'] ['image'] = avatar ( $member_id ['avatar'], $member_id ['username'] );
			
			$row = $db->super_query ( "SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id ['user_id'] . "';" );
			
			if ($row ['image']) {
				$use_image = true;
				$is_default = false;
			} else {
				$is_default = true;
				$use_image = false;
			}
			
			$buffer ['user'] ['background'] = $row;
			$buffer ['user'] ['background'] ['is_default'] = $is_default;
			$buffer ['user'] ['background'] ['use_image'] = $use_image;
			
			unset ( $buffer ['user'] ['password'] );
		
		}
	}
	
	$db->close ();
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif ($type == "playlist") {
	
	$action = $db->safesql ( $_REQUEST ['action'] );
	
	if ($action == "create") {
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		$name = $db->safesql ( $_REQUEST ['name'] );
		
		$descr = $db->safesql ( $_REQUEST ['descr'] );
                
                $access = $db->safesql ( $_REQUEST ['access'] );
		
		if( ! $name ) die();
		else{
			$db->query("INSERT INTO vass_playlists (user_id, `date`, name, descr, user_access) VALUES ('" . $member_id ['user_id'] . "', '$_TIME', '$name', '$descr', '$access');");
			
			$playlist_id = $db->insert_id();
			
			$buffer ['playlist_id'] = $playlist_id;
			$buffer ['name'] = $name;
			$buffer ['status_text'] = "OK";
			$buffer ['status_code'] = "200";
			$buffer ['playlist']['name'] = $name;
			$buffer ['playlist']['date'] = $_TIME;
			$buffer ['playlist']['playlist_id'] = $playlist_id;
			$buffer ['playlist']['name'] = $name;
			$buffer ['playlist']['cover'] = 0;
			$buffer ['playlist']['descr'] = $descr;
			$buffer ['playlist']['username'] = $member_id ['username'];
                        
		}
                
		
	}elseif ($action == "edit") {
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		$name = $db->safesql ( $_REQUEST ['name'] );
		
		$descr = $db->safesql ( $_REQUEST ['descr'] );
		
		$id = intval ( $_REQUEST ['id'] );
                
                $access = $db->safesql ( $_REQUEST ['approve']);
                
                
              
		
		if( ! $name ) die();
		else{
			$db->query("UPDATE vass_playlists SET name= '$name', descr = '$descr', user_access = '$access' WHERE user_id = '" . $member_id ['user_id'] . "' AND id = '$id';");
			$buffer ['name'] = $name;
			$buffer ['descr'] = $descr;
			$buffer ['status_text'] = "OK";
			$buffer ['status_code'] = "200";
		}
		
	}
    elseif($action == "likeplaylist"){
         
         if (! $_REQUEST ['id']) {
		
		header ( 'HTTP/1.0 404 Not Found' );
	
	}
        else{
             
             $playlist_id = ($_REQUEST ['id']);
             $user_id = ($_REQUEST ['userid']);
             $row2 = $db->query ("SELECT likecount FROM vass_playlists WHERE id = '" . $playlist_id . "'");
             $rowuser = $db->get_row($row2);
//             echo $rowuser['likecount'];
     
             $rowuser2[] = $rowuser['likecount'];

             array_push($rowuser2,$user_id);
             $rowuser2 = implode(",", $rowuser2);
             $row = $db->query ("UPDATE vass_playlists SET likecount= '" .$rowuser2. "' WHERE id = '" . $playlist_id . "'" );
             
        }
        
        $buffer = array ("status_code" => "200","user" => $user_id, "playlist2" => $playlist_id);
	
//	print json_encode ( $buffer );
        
//        $playlist_id = ($_REQUEST['id']);
//        $user_id = ($_REQUEST['user_id']);
//       
//        $db->query ( "UPDATE vass_playlists SET likecount  = likecount+1 WHERE user_id = '" . $member_id ['user_id'] . "'" );
//        
//        $buffer = array("status_code" => 200, "status_text" => "OK", "playlist1" => $playlist_id);
        
        
        
}
    elseif($action == "unlikeplaylist"){
         $playlist_id = ($_REQUEST ['id']);

        $user_id = ($_REQUEST ['userid']);
        $row1 = $db->query("SELECT likecount FROM vass_playlists WHERE id = '" . $playlist_id . "'");
        $rowuser = $db->get_row($row1);

        $rowuser3 = explode(",", $rowuser['likecount']);

        if (($key = array_search($user_id, $rowuser3)) !== false) {
            unset($rowuser3[$key]);

            $rowuser3 = implode(",", $rowuser3);
        }
        $row = $db->query("UPDATE vass_playlists SET likecount= '" . $rowuser3 . "' WHERE id = '" . $playlist_id . "'");

        $buffer = array("status_code" => "200");

//        print json_encode($buffer);
        
    }
elseif ($action == "doresort") {
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		
		$playlist_id = intval($_REQUEST ['playlist_id']);
		
		$items = $_REQUEST ['songs'];
		
		$items = explode("==", $items);
		
		$i=1;
		
		foreach($items as $item){
			
			if(!empty($item)){
				$db->query ("UPDATE vass_song_playlist SET pos = '$i' WHERE song_id = '" . $db->safesql($item) . "' AND playlist_id = '$playlist_id'");
				$i++;
			}
			$buffer ['status_text'] = "OK";
			$buffer ['status_code'] = "200";
		}
		
		
	}elseif ($action == "remove") {
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		$id = intval ( $_REQUEST ['id'] );
		
		if( ! $id ) die();
		else{
			$db->query("DELETE FROM vass_playlists WHERE user_id = '" . $member_id ['user_id'] . "' AND id = '$id';");
			$buffer ['playlist_id'] = $id;
			$buffer ['status_text'] = "OK";
			$buffer ['status_code'] = "200";
		}
		
	}elseif($action == "info"){
		
        $id = intval ( $_REQUEST ['id'] );
	
		$row = $db->super_query ( "SELECT vass_playlists.name, vass_playlists.date, vass_playlists.id AS playlist_id, vass_playlists.user_access AS checked,vass_playlists.likecount ,vass_playlists.cover, vass_playlists.descr,
		vass_users.username 
		FROM vass_playlists LEFT JOIN vass_users ON vass_playlists.user_id = vass_users.user_id WHERE vass_playlists.id = '" . $id . "';" );
		$row['checked'] = ($row['user_access'])? "checked": "";
		$playlist = $row;
                
                $rowuser1 = explode(",", $row['likecount']);
                
                 $playlist['count'] = count($rowuser1);
//                 echo $playlist['count'];
                $updp = 0;
               for ($i = 0; $i < count($rowuser1); $i++) {
                     if ($rowuser1[$i] == $_SESSION['user_id']) {
                     $updp = 1;
                break;
            }
        }
                $playlist['updp'] = $updp;
            
		$buffer = array ("status_code" => 200, "status_text" => "OK", "playlist" => $playlist);
		
	}elseif($action == "addsong"){
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		$playlist_id = intval ( $_REQUEST ['playlist_id'] );
		
		$song_id = $db->safesql ( $_REQUEST ['song_id'] );
		
		if($song_id && $playlist_id) $db->query("INSERT IGNORE INTO vass_song_playlist (song_id, playlist_id) VALUES ('$song_id', '$playlist_id')");
		
		$buffer = array ("status_code" => 200, "status_text" => "OK");
		
	}elseif($action == "addfromqueue"){
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		$playlist_id = intval ( $_REQUEST ['playlist_id'] );
		
		$json = file_get_contents ( 'php://input' );
		
		$obj = json_decode ( $json );
		
		foreach ($obj as $item) {
			
			$song_id = $item->id;
			
			if($song_id && $playlist_id) $db->query("INSERT IGNORE INTO vass_song_playlist (song_id, playlist_id) VALUES ('$song_id', '$playlist_id')");
			
		}
		
		$buffer = array ("status_code" => 200, "status_text" => "OK");
		
	}elseif($action == "remove_song"){
		
		if (! $logged) {
			
			header ( "HTTP/1.0 401 UNAUTHORIZED" );
			
			$buffer ['status_code'] = 401;
			
			$buffer ['status_text'] = "Authentication required.";
			
			die();
			
		}
		$playlist_id = intval ( $_REQUEST ['playlist_id'] );
		
		$song_id = $db->safesql ( $_REQUEST ['song_id'] );
		
		if($song_id && $playlist_id) $db->query("DELETE FROM vass_song_playlist WHERE song_id = '$song_id' AND playlist_id = '$playlist_id'");
		
		$buffer = array ("status_code" => 200, "status_text" => "OK");
		
	}elseif($action == "songs"){
		
		$playlist_id = intval ( $_REQUEST ['id'] );
		
		$owner = $db->super_query("SELECT user_id FROM vass_playlists WHERE id = '$playlist_id'");
		
		$sql_query = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_playlist.playlist_id = '" . $playlist_id . "' ORDER BY vass_song_playlist.pos ASC LIMIT $start,20");
		
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count 
				FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_playlist.playlist_id = '" . $playlist_id . "'" );
		
		while ( $row = $db->get_row ($sql_query) ) {
				
				if($logged && $member_id['user_id'] == $owner ['user_id']) {
					$songs['playlist_owner'] = true;
					$songs['playlist_id'] = $playlist_id;
				}
				if($row['artist_id']) {
					
					$songs ['album'] = $row ['song_album'];
					$songs ['url'] = stream ( $row ['song_id'] );
					$songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );
					$songs ['artist'] = $row ['song_artist'];
					
				}else{
					
					$songs ['album'] = $row ['description'];
					$songs ['url'] = stream ( $row ['song_id'] );
					$songs ['image'] = songlist_images ( $row ['artwork_url'], $row['artist_id'] );
					$songs ['artist'] = $row ['tag_list'];
					
				}
				$songs ['artist_id'] = $row ['artist_id'];
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
				$songs ['user_love'] = null;
				$result ['songs'] [] = $songs;
		}
		
		$result ['status_text'] = "OK";
		$result ['status_code'] = "200";
		$result ['results'] = $total_results['count'];
		$result ['start'] = $start;
		$result ['total'] = $total_results['count'];
		
		$buffer = $result;
	}elseif($action == "resort"){
		
		$playlist_id = intval ( $_REQUEST ['id'] );
		
		$owner = $db->super_query("SELECT user_id FROM vass_playlists WHERE id = '$playlist_id'");
		
		$sql_query = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_playlist.playlist_id = '" . $playlist_id . "' ORDER BY vass_song_playlist.pos ASC" );
		
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count 
				FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_playlist.playlist_id = '" . $playlist_id . "'" );
		$i = 0;
		while ( $row = $db->get_row ($sql_query) ) {
				
				if($row['artist_id']) {
					
					$songs ['album'] = $row ['song_album'];
					$songs ['url'] = stream ( $row ['song_id'] );
					$songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );
					$songs ['artist'] = $row ['song_artist'];
					
				}else{
					
					$songs ['album'] = $row ['description'];
					$songs ['url'] = stream ( $row ['song_id'] );
					$songs ['image'] = songlist_images ( $row ['artwork_url'], $row['artist_id'] );
					$songs ['artist'] = $row ['tag_list'];
					
				}
				$songs ['artist_id'] = $row ['artist_id'];
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
				$songs ['user_love'] = null;
				$songs ['position'] = $i;
				$songs['playlist_id'] = $playlist_id;
				$buffer [] = $songs;
				$i++;
		}
	}
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
        
        
	
}elseif ($type == "last_playlists") {
	
	$sql_result = $db->query ( "SELECT vass_playlists.name, vass_playlists.date, vass_playlists.id AS playlist_id, vass_playlists.cover, vass_playlists.descr,
	vass_users.username 
	FROM vass_playlists LEFT JOIN vass_users ON vass_playlists.user_id = vass_users.user_id ORDER by vass_playlists.id DESC;" );
	
	$start = $_REQUEST ['start'];
	
	$page_start = $start;
	
	$page_end = $start + 20;
	
	$total_results = $db->num_rows ( $sql_result );
	
	$i = 0;
	
	while ( $row = $db->get_row ( $sql_result ) ) {
		
		if ($i >= $page_start) {
			
			$object ['title'] = $row ['name'];
			$object ['object'] ['title'] = $row ['name'];
			$object ['object'] ['album'] = $row ['description'];
			$object ['object'] ['url'] = stream ( $row ['song_id'] );
			$object ['object'] ['artist'] = $row ['username'];
			$object ['object'] ['playlist_id'] = $row ['playlist_id'];
			$object ['object'] ['cover'] = $row ['cover'];
			$object ['object'] ['descr'] = $row ['descr'];
			$object ['object'] ['created_on'] = date ( 'D M d Y H:i:s O', strtotime ( $row ['date'] ) );
			$object ['object'] ['owner'] = array ("username" => $row ['username'], "created_on" => date ( 'D M d Y H:i:s O', strtotime ( $row ['created_on'] ) ) );
			$activities [] = $object;
		
		}
		$i ++;
		if ($i >= $page_end)
		break;
	}
	
	
	$buffer ['status_text'] = "OK";
	$buffer ['status_code'] = "200";
	$buffer ['results'] = $total_results;
	$buffer ['start'] = $start;
	$buffer ['total'] = $total_results;
	$buffer ['activities'] = $activities;
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif ($type == "artist") {
	
	$action = $db->safesql($_REQUEST ['action']);

    if ($action == "info") {
        $id = intval($_REQUEST ['id']);
        $row = $db->super_query("SELECT id AS artist_id, name, bio,likecount FROM vass_artists WHERE id = '" . $id . "'");

        $artist = $row;
        $rowuser1 = explode(",", $row['likecount']);
//         echo  $_SESSION['user_id'];
        $upd = 0;
        for ($i = 0; $i < count($rowuser1); $i++) {
            if ($rowuser1[$i] == $_SESSION['user_id']) {
                $upd = 1;
                break;
            }
        }
        $artist['upd'] = $upd;

        $buffer = array("status_code" => 200, "status_text" => "OK", "artist" => $artist);
    } elseif ($action == "songs") {

        if (isset($_SESSION['country_Id'])) {
            $id = intval($_REQUEST ['id']);

            $sql_query = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.artist_id REGEXP '[[:<:]]" . $id . "[[:>:]]' and vass_songs.song_country = '".$_SESSION['country_Id']."' LIMIT $start,20");
		
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.artist_id REGEXP '[[:<:]]" . $id . "[[:>:]]' and vass_songs.song_country = '".$_SESSION['country_Id']."'"  );
		
		while ( $row = $db->get_row ($sql_query) ) {
				
				$songs ['album'] = $row ['song_album'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );
				$songs ['artist'] = $row ['song_artist'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
                                $songs['album_id'] = $row['album_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
				$songs ['user_love'] = null;
				$result ['songs'] [] = $songs;
		}
              
                if($result == 0){
                    $result['msg'] = "No songs available for this country";
                }
                
                }else{
                    $id = intval ( $_REQUEST ['id'] );
		
		$sql_query = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.artist_id REGEXP '[[:<:]]" . $id . "[[:>:]]' LIMIT $start,20");
		
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.artist_id REGEXP '[[:<:]]" . $id . "[[:>:]]'" );
		
		while ( $row = $db->get_row ($sql_query) ) {
				
				$songs ['album'] = $row ['song_album'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );
				$songs ['artist'] = $row ['song_artist'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
                                $songs['album_id'] = $row['album_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
				$songs ['user_love'] = null;
				$result ['songs'] [] = $songs;
		}
		
                }
		$result ['status_text'] = "OK";
		$result ['status_code'] = "200";
		$result ['results'] = $total_results['count'];
		$result ['start'] = $start;
		$result ['total'] = $total_results['count'];
		
		$buffer = $result;
	}
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif ($type == "album") {
	
	$action = $db->safesql ( $_REQUEST ['action'] );
	
	if($action == "info"){
		
		$id = intval ( $_REQUEST ['id'] );
		
		$row = $db->super_query ( "SELECT vass_albums.descr, vass_albums.id AS album_id, vass_albums.name, vass_artists.id AS artist_id, vass_artists.name AS artist FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id =  vass_artists.id WHERE vass_albums.id = '" . $id . "';" );
		
		$album = $row;
		
		$buffer = array ("status_code" => 200, "status_text" => "OK", "album" => $album );
		
	}elseif($action == "songs"){
		        if(isset($_SESSION['country_Id'])){
		$id = intval ( $_REQUEST ['id'] );
		
		$sql_query = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.album_id = '$id' and vass_songs.song_country = '".$_SESSION['country_Id']."' LIMIT $start,20");
		
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_songs WHERE album_id = '$id' and song_country = '".$_SESSION['country_Id']."'"  );
		
		while ( $row = $db->get_row ($sql_query) ) {
				
				$songs ['album'] = $row ['song_album'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );
				$songs ['artist'] = $row ['song_artist'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
                                $songs['album_id'] = $id;
				$songs ['user_love'] = null;
				$result ['songs'] [] = $songs;
		}
  
                
                }else{
                    $id = intval ( $_REQUEST ['id'] );
		
		$sql_query = $db->query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.album_id = '$id' LIMIT $start,20");
		
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_songs WHERE album_id = '$id'" );
		
		while ( $row = $db->get_row ($sql_query) ) {
				
				$songs ['album'] = $row ['song_album'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );
				$songs ['artist'] = $row ['song_artist'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
                                $songs['album_id'] = $id;
				$songs ['user_love'] = null;
				$result ['songs'] [] = $songs;
		}
                }
		$result ['status_text'] = "OK";
		$result ['status_code'] = "200";
		$result ['results'] = $total_results['count'];
		$result ['start'] = $start;
		$result ['total'] = $total_results['count'];
               
		
		$buffer = $result;
	}

	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif( $type == "userlist" ){
	
	$start = $_REQUEST['start'];
	
	$sql_result = $db->query("SELECT vass_friendship.follower_id, vass_users.username, vass_users.name, vass_users.bio, vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, vass_users.total_following, vass_users.total_followers, vass_users.avatar, vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.follower_id LIMIT 0,50");
	
	$total_results = $db->num_rows( $sql_result );
	
	while ($result = $db->get_row($sql_result)){
		$buffer = $result;
		$buffer['is_beta_tester'] = false;
		$buffer['viewer_following'] = viewer_following($result['follower_id']);
		$buffer['import_feeds'] = import_feeds($result['user_id']);
		$buffer['image'] = avatar( $result['avatar'], $result['username'] );
		
		$row = $db->super_query("SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $result['user_id'] . "';");
		
		if( $row['image'] ) {
			$use_image = true;
			$is_default = false;
		} else {
			$is_default = true;
			$use_image = false;
		}
		$buffer['background'] = $row;
		$buffer['background']['is_default'] = $is_default;
		$buffer['background']['use_image'] = $use_image;
		
		unset($buffer['password']);
		
		$following[] = $buffer;
	}
	
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"users" => $following, "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif( $type == "albumlist" ){
		
	$letter = $db->safesql ( $_REQUEST ['letter'] );
	
	$start = intval($_REQUEST['start']);
	
	$end = $start+20;
	
	if(!empty($letter)){
		$query = $db->query("SELECT vass_albums.artist_id, vass_artists.name AS artist, vass_albums.id, vass_albums.id, vass_albums.view, vass_albums.name FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id WHERE vass_albums.name LIKE '$letter%' LIMIT $start,$end");
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_albums WHERE vass_albums.name LIKE '$letter%'" );
		$total_results = $total_results['count'];
	}else{
		$query = $db->query("SELECT vass_albums.artist_id, vass_artists.name AS artist, vass_albums.id, vass_albums.id, vass_albums.view, vass_albums.name FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id LIMIT $start,$end");
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_albums" );
		$total_results = $total_results['count'];
	}
	while ($row = $db->get_row($query)){
	
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 1000, "start" => $start,"albums" => $buffer, "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif( $type == "artistlist" ){
	
	$start = intval($_REQUEST['start']);
	
	$end = $start+20;
	
	$letter = $db->safesql ( $_REQUEST ['letter'] );
	
	if(!empty($letter)){
		$artists = $db->query("SELECT id, name FROM vass_artists WHERE name LIKE '$letter%' LIMIT $start,$end");
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_artists WHERE name LIKE '$letter%'" );
		$total_results = $total_results['count'];
	}else{
		$artists = $db->query("SELECT id, name FROM vass_artists LIMIT $start,$end");
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_artists" );
		$total_results = $total_results['count'];
	}
	while ($row = $db->get_row($artists)){
		
		$num_songs = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs WHERE artist_id = '" . $row['id'] . "'");
		
		$row['total_songs'] = $num_songs['count'];
		
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"artists" => $buffer, "total" => $total_results, "start" => $start, "results" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif( $type == "suggess" ){
	
	//{"status_text": "OK", "status_code": 200, "suggess": [{"title":"A Men", "url":"artist\/a-men\/1"},{"title":"A Zflow", "url":"artist\/a-zflow\/2"},{"title":"Bari Niich", "url":"album\/bari-niich\/2"},{"title":"Dir Lkhir Talqa Lkhir", "url":"album\/dir-lkhir-talqa-lkhir\/3"},{"title":"Bari niich", "url":"song\/bari-niich\/2"},{"title":"Dir Lkhir Talqa Lkhir", "url":"song\/dir-lkhir-talqa-lkhir\/3"}]}
	
	$query = $db->safesql( $_REQUEST['query'] );
	
	$db->query("SELECT id, title FROM vass_songs WHERE title LIKE '%$query%' LIMIT 0,10");
	
	while ($row = $db->get_row()){
	
		$buffer[] = $row;
	
	}
	
	$buffer = array("status_code" => 200,"suggess" => $buffer);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif( $type == "searchalbum" ){
	
	$q = $db->safesql ( $_REQUEST ['q'] );
	
	$db->query("SELECT vass_albums.artist_id, vass_artists.name AS artist, vass_albums.id, vass_albums.id, vass_albums.view, vass_albums.name FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id WHERE vass_albums.name LIKE '%$q%' LIMIT 0,20");
	
	while ($row = $db->get_row()){
	
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"albums" => $buffer, "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif( $type == "searchartist" ){
	
	$q = $db->safesql ( $_REQUEST ['q'] );
	
	$db->query("SELECT id, name FROM vass_artists WHERE name LIKE '%$q%' LIMIT 0,5");
	
	while ($row = $db->get_row()){
	
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"albums" => $buffer, "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif( $type == "artistallalbum" ){
	
	$id = intval ( $_REQUEST ['id'] );
	
	$db->query("SELECT vass_albums.artist_id, vass_artists.name AS artist, vass_albums.id, vass_albums.id, vass_albums.view, vass_albums.name 
	FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id WHERE vass_artists.id = '$id'");
	
	while ($row = $db->get_row()){
	
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$total_results = $db->super_query("SELECT COUNT(*) AS count FROM vass_albums WHERE artist_id = '$id'");
	$total_results = $total_results['count'];
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"albums" => $buffer, "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif( $type == "artistbio" ){
	
	$id = intval ( $_REQUEST ['id'] );
	
	$db->query("SELECT bio FROM vass_artists WHERE id = '$id'");
	
	while ($row = $db->get_row()){
	
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	

	
	$buffer = array("status_code" => 200, "status_text" => "OK", "buffer" => $buffer);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}
elseif( $type == "artistsimilar" ){
 
	
	$id = intval ( $_REQUEST ['id'] );
	
	$db->query("SELECT tag FROM vass_artists WHERE id = '$id'");
	
	  
	  $tags = $db->get_row();
          $tags = explode(",",$tags[tag]);
//          echo count($tags);
          
            for($i=0; $i<count($tags); $i++){
	$db->query("SELECT id FROM vass_artists WHERE vass_artists.tag REGEXP '[[:<:]]" . $tags[$i] . "[[:>:]]' and vass_artists.id <> '$id' ");
        
	while ($artist = $db->get_row()){
	 if($artist['id']) $artists_id[] = $artist['id'];
	
	}
          }
        if($artists_id){
        $artists_id=array_unique($artists_id);
        }
//        echo"<pre>";print_r($artists_id);echo "<pre>";die;
        for($j=0; $j<count($artists_id); $j++){
//             echo"<pre>";print_r($artists_id[]);echo "<pre>";die;
        $db->query("SELECT id ,name FROM vass_artists WHERE id='".$artists_id[$j]."'");
       
       while( $Row = $db->get_row()){
       if($Row) $buffer[]=$Row;
           
       }
        
        }

        $total_results = count($artists_id);
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "buffer" => $buffer , "total" => $total_results );
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif( $type == "historysongs"){
    $D_SORT = 30;
    $id = intval ( $_REQUEST ['userid'] );
    
   $type= $db->query("SELECT COUNT(*) AS count,song_id ,user_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' and user_id = '".$id."'GROUP BY song_id ORDER by count DESC");
    
	while ( $his = $db->get_row ( $type) ) {
		
		if ($his ['song_id']) {
                    
			$row = $db->super_query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
			vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
			FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
			vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $his ['song_id'] . "'" );
			$songs ['album'] = $row ['song_album'];
			$songs ['artist_id'] = $row ['artist_id'];
			$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
			$songs ['buy_link'] = null;
			$songs ['artist'] = $row ['song_artist'];
			$songs ['url'] = stream ( $row ['song_id'] );
			$songs ['image'] = songlist_images ( $row ['album_id'] );
			$songs ['title'] = $row ['song_title'];
			$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$songs ['sources'] = sources ( $row ['song_id'] );
			$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$songs ['last_loved'] = null;
			$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$songs ['aliases'] = aliases ( $row ['song_id'] );
			$songs ['loved_count'] = $row ['loved'];
			$songs ['id'] = $row ['song_id'];
			$songs ['tags'] = tags ( $row ['song_id'] );
                        $songs ['album_id'] =$row['album_id'];
			$songs ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$songs ['user_love'] = "";
			$result ['songs'] [] = $songs;
		}
	}
        
        
	
//	$result ['status_text'] = "OK";
//	$result ['status_code'] = "200";
//	$result ['results'] = $total_results['count'];
//	$result ['start'] = $start;
//	$result ['total'] = $total_results['count'];
//	
//	echo json_encode ( $result );
        $buffer = array("status_code" => 200, "status_text" => "OK", "buffer" =>$result , "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}
elseif( $type == "artistallvideo" ){
	
	$id = intval ( $_REQUEST ['id'] );
	
	$db->query("SELECT vass_videos.artist_id, vass_videos.tube_key, vass_artists.name AS artist, vass_videos.id, vass_videos.id, vass_videos.view, vass_videos.name 
	FROM vass_videos LEFT JOIN vass_artists ON vass_videos.artist_id = vass_artists.id WHERE vass_artists.id = '$id'");
	
	while ($row = $db->get_row()){
	
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$total_results = $db->super_query("SELECT COUNT(*) AS count FROM vass_videos WHERE artist_id = '$id'");
	
	$total_results = intval($total_results['count']);
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"videos" => $buffer, "total" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif( $type == "video" ){
	
	$id = intval ( $_REQUEST ['id'] );
	
	$row = $db->super_query("SELECT vass_videos.artist_id, vass_videos.tube_key, vass_artists.name AS artist, vass_videos.id, vass_videos.id, vass_videos.view, vass_videos.name 
	FROM vass_videos LEFT JOIN vass_artists ON vass_videos.artist_id = vass_artists.id WHERE vass_videos.id = '$id'");
	
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "video" => $row);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
	
}elseif($type == "radio"){
    
     $db->query("SELECT * FROM vass_radio ");
    
    
    while($row =$db->get_row ()){
    $buffer[]=$row;
    }

    $buffer = array("status_code" => 200, "status_text" => "OK", "buffers" => $buffer);
    
    header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}elseif($type == 'station_songs'){
    
    
       $songs=$db->safesql ( $_REQUEST ['song_ids'] );
     
       
    $song_id = explode(",",$songs);
//      print_r( $song_id);die;
    shuffle($song_id);
    for($i=0;$i<count($song_id);$i++){
         
    
        if($song_id[$i]){
            
            
            $row = $db->super_query ( "SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
			vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
			FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
			vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $song_id[$i] . "'" );
                
                 
                 
			$songsArr ['album'] = $row ['song_album'];
			$songsArr ['artist_id'] = $row ['artist_id'];
			$songsArr ['similar_artists'] = similar_artists ( $row ['song_id'] );
			$songsArr ['buy_link'] = null;
			$songsArr ['artist'] = $row ['song_artist'];
			$songsArr ['url'] = stream ( $row ['song_id'] );
			$songsArr ['image'] = songlist_images ( $row ['album_id'] );
			$songsArr ['title'] = $row ['song_title'];
			$songsArr ['metadata_state'] = metadata_state ( $row ['song_id'] );
			$songsArr ['sources'] = sources ( $row ['song_id'] );
			$songsArr ['viewer_love'] = viewer_love ( $row ['song_id'] );
			$songsArr ['last_loved'] = null;
			$songsArr ['recent_loves'] = recent_loves ( $row ['song_id'] );
			$songsArr ['aliases'] = aliases ( $row ['song_id'] );
			$songsArr ['loved_count'] = $row ['loved'];
			$songsArr ['id'] = $row ['song_id'];
			$songsArr ['tags'] = tags ( $row ['song_id'] );
                        $songsArr ['album_id'] = $row['album_id'];
			$songsArr ['trending_rank_today'] = trending_rank_today ( $row ['song_id'] );
			$songsArr ['user_love'] = "";
                        $songsArr ['type'] = "radio";
			$result['songs'][]  = $songsArr;
                        
//                        print_r($result);die;
        }
    
    }
    
    
    $buffer = array("status_code" => 200, "status_text" => "OK", "buffers" => $result);
    
    header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}
elseif ($type == "newrelease") {       
    
                          if(isset($_SESSION['country_Id'])) {
                             
                          $release = $db->query ( "SELECT vass_songs.id AS song_id, vass_songs.artist_id, 
			vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
			vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
			FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
			vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.recent = 1 and vass_songs.song_country = '". $_SESSION['country_Id'] ."' LIMIT 0,20" );
                          
                        	while ( $row = $db->get_row ( $release ) ) {
			if($row ['song_id']){
				$songs ['album'] = $row ['song_album'];
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['artist'] = $row ['song_artist'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'] );
				$songs ['artist_image'] = artist_images ( $row ['album_id'], $row ['artist_id'] );
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = $i;
				$result ['songs'] [] = $songs;
			}else $result ['songs'] = array();
			$i ++;
		}           
                          } 
                         
                          else{
                          
			$release = $db->query ( "SELECT vass_songs.id AS song_id, vass_songs.artist_id, 
			vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
			vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
			FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
			vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.recent = 1 LIMIT 0,20" );
                        
                        	while ( $row = $db->get_row ( $release ) ) {
			if($row ['song_id']){
				$songs ['album'] = $row ['song_album'];
                                $songs ['album_id'] = $row ['album_id'];
				$songs ['artist_id'] = $row ['artist_id'];
				$songs ['similar_artists'] = similar_artists ( $row ['song_id'] );
				$songs ['buy_link'] = null;
				$songs ['artist'] = $row ['song_artist'];
				$songs ['url'] = stream ( $row ['song_id'] );
				$songs ['image'] = songlist_images ( $row ['album_id'] );
				$songs ['artist_image'] = artist_images ( $row ['album_id'], $row ['artist_id'] );
				$songs ['title'] = $row ['song_title'];
				$songs ['metadata_state'] = metadata_state ( $row ['song_id'] );
				$songs ['sources'] = sources ( $row ['song_id'] );
				$songs ['viewer_love'] = viewer_love ( $row ['song_id'] );
				$songs ['last_loved'] = null;
				$songs ['recent_loves'] = recent_loves ( $row ['song_id'] );
				$songs ['aliases'] = aliases ( $row ['song_id'] );
				$songs ['loved_count'] = $row ['loved'];
				$songs ['id'] = $row ['song_id'];
				$songs ['tags'] = tags ( $row ['song_id'] );
				$songs ['trending_rank_today'] = $i;
				$result ['songs'] [] = $songs;
			}else $result ['songs'] = array();
			$i ++;
		}
                        
                          }
		
		$result ['status_text'] = "OK";
		$result ['status_code'] = "200";
		$result ['start'] = 0;
		
		echo json_encode ( $result );


}elseif( $type == "toplist" ){
         
        $top_date =$db->safesql (trim( $_REQUEST ['date'] ));	
        $D_SORT = 7;

        $type = $db->safesql ( trim($_REQUEST ['type']) );
 
	if($type ==  "album"){
            
            if(isset($_SESSION['country_id'])){
                $top_week = $db->query ( "SELECT COUNT(*) AS count,album_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY album_id ORDER by count DESC LIMIT 0,20" );
                while ($top = $db->get_row($top_week)){
		if($top['album_id']){
                $query = $db->query("SELECT vass_albums.artist_id, vass_artists.name AS artist, vass_albums.id, vass_albums.name FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id LEFT JOIN vass_artists ON vass_songs.artist_id WHERE vass_albums.id = " .$top['album_id'] ." and vass_songs.songs_country = '".$_SESSION['country_Id']."'");
        }
        
           while ($row = $db->get_row($query)){
			$row['type'] = "album";
                        $buffer[] = $row;      
         }
      }
            }
 else {
                $top_week = $db->query ( "SELECT COUNT(*) AS count,album_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY album_id ORDER by count DESC LIMIT 0,20" );
                while ($top = $db->get_row($top_week)){
		if($top['album_id']){
                $query = $db->query("SELECT vass_albums.artist_id, vass_artists.name AS artist, vass_albums.id, vass_albums.name FROM vass_albums LEFT JOIN vass_artists ON vass_albums.artist_id = vass_artists.id WHERE vass_albums.id = " .$top['album_id'] ." ");
        
//                 echo $query;
        }
       
           while ($row = $db->get_row($query)){
			$row['type'] = "album";
                        $album_name = "album_name";
                        $buffer[] = $row;      
         }
      }
            
 }
 if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "buffers" => $buffer);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );

   }
                else if($type ==  "artist"){
       if(isset($_SESSION['country'])){    
                $top_week = $db->query ( "SELECT COUNT(*) AS count,artist_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY artist_id ORDER by count DESC LIMIT 0,20" );    
		while ($top = $db->get_row($top_week)){
		if($top['artist_id']){
                $query = $db->query("SELECT id,name FROM vass_artists LEFT JOIN vass_artists ON vass_songs.artist_id WHERE vass_artists.id = " .$top['artist_id'] ." and vass_songs.song_country = '".$_SESSION['country_Id']."'");
             
              while ($row = $db->get_row($query)){
			$row['type'] = "artist";
                        $num_songs = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs WHERE artist_id = '" . $row['id'] . "'");
			$row['total_songs'] = $num_songs['count'];
                        $buffer[] = $row;      
         } 
          }    
         }
         }
         else{
                   $top_week = $db->query ( "SELECT COUNT(*) AS count,artist_id FROM vass_analz WHERE `time` > '" . date ( "Y-m-d", (time () - $D_SORT * 24 * 3600) ) . "' GROUP BY artist_id ORDER by count DESC LIMIT 0,20" );    
		while ($top = $db->get_row($top_week)){
		if($top['artist_id']){
                $query = $db->query("SELECT id,name FROM vass_artists WHERE vass_artists.id = " .$top['artist_id'] ." ");
             
              while ($row = $db->get_row($query)){
			$row['type'] = "artist";
                        $num_songs = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs WHERE artist_id = '" . $row['id'] . "'");
			$row['total_songs'] = $num_songs['count'];
                        $buffer[] = $row;      
         } 
          }    
         } 
         }
         if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "buffers" => $buffer);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
        }
        
        
         else if ($type == "playlist"){
        
        $playlist = $db->safesql($_REQUEST['id']);
    
    $row = $db->query("SELECT id,likecount FROM vass_playlists ");

    while ($row = $db->get_row()) {

        $rowplaylist = explode(",", $row['likecount']);
        $countplaylist[$row['id']] = count($rowplaylist)-1;
    }
//       print_r ($countplaylist);echo "<br>"; 
    arsort($countplaylist);
//            print_r ($countplaylist);echo "<br>";
//             $data_id = array();
    $i = 0;
    foreach ($countplaylist as $key => $value) {
        if ($value != 0) {
            $data_id = $key;
            $i++;
            if ($i == 9)
                break;
            $query = $db->query("SELECT * FROM vass_playlists WHERE id = '" . $data_id . "' ");
            while ($row = $db->get_row($query)) {
                $row['type'] = "playlist";
                $row2 = $db->super_query("SELECT username FROM vass_users WHERE user_id = '" .$row['user_id']. "'");
               $name = $row2['username'];
                $num_songs = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs WHERE id = '" . $row['id'] . "'");
                $row['total_songs'] = $num_songs['count'];
                $buffer[] = $row;
                $user[] = $row2;
                
            }
            
        }
    }

//    print_r($buffer);
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "buffers" => $buffer);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
         }
}

elseif( $type == "lyrics" ){
	
	$song_id = $db->safesql ( trim(intval($_REQUEST ['song_id']) ));
	
	$row = $db->super_query("SELECT lyrics FROM vass_songs WHERE id = '$song_id'");
	
	$row['lyrics'] = stripslashes(str_replace("\n","<br>",$row['lyrics']));
	
	$buffer['lyrics'] = $row['lyrics'];
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
}
elseif($type == "playlistlist"){
    $start = intval($_REQUEST['start']);
	
	$end = $start+20;
	
	$letter = $db->safesql ( $_REQUEST ['letter'] );
	
	if(!empty($letter)){
		$playlists = $db->query("SELECT id, name FROM vass_playlists WHERE name LIKE '$letter%' and ( user_id = '".$_SESSION['user_id']."' or user_access = 1 ) LIMIT $start,$end");
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_playlists WHERE name LIKE '$letter%'  and ( user_id = '".$_SESSION['user_id']."' or user_access = 1 )" );
		$total_results = $total_results['count'];
	}else{
		$playlists = $db->query("SELECT id, name FROM vass_playlists WHERE user_id = '".$_SESSION['user_id']."' or user_access = 1 LIMIT $start,$end");
		$total_results = $db->super_query ( "SELECT COUNT(*) AS count FROM vass_playlists  WHERE user_id = '".$_SESSION['user_id']."' or user_access = 1" );
		$total_results = $total_results['count'];
	}
	while ($row = $db->get_row($playlists)){
		
		$num_songs = $db->super_query("SELECT COUNT(*) AS count FROM vass_song_playlist WHERE playlist_id = '" . $row['id'] . "'");
		
		$row['total_songs'] = $num_songs['count'];
		
		$buffer[] = $row;
	
	}
	
	if(!$buffer) $buffer = array();
	
	$buffer = array("status_code" => 200, "status_text" => "OK", "results" => 20, "start" => $start,"playlists" => $buffer, "total" => $total_results, "start" => $start, "results" => $total_results);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
        
}  
elseif($type == "highestplaylist"){
    
    $db->query( "SHOW TABLE STATUS LIKE 'vass_playlists'");
    $row = $db->get_row();
 //echo $row['Auto_increment'];die;   
    $buffer=$row['Auto_increment'];
    header ( 'Cache-Control: no-cache, must-revalidate' );
    header ( 'Content-type: application/json' );
    print json_encode ( $buffer );
}
elseif($type == "country"){
// echo "country"; die;
   
    $db->query("SELECT * FROM vass_country ");
    while($row = $db->get_row()){
        $buffer[] = $row;
    }
    //print_r ($buffer); die;
    $buffer = array("status_code" => 200, "status_text" => "OK", "buffers" => $buffer);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
    
//    print json_encode ( $buffer );
     
}
elseif($type == "price"){
// echo "country"; die;
    $action = $db->safesql ( $_REQUEST ['action'] );
    if($action == "specific"){
   $data = $db->safesql(trim($_REQUEST ['data'])) ;

    $row = $db->super_query("SELECT price FROM vass_pricedetails WHERE content = '$data'");
  
   
    $buffer = array("status_code" => 200, "status_text" => "OK", "price" => $row['price']);
	
	header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
    
//    print json_encode ( $buffer );
    }
    elseif($action == "all"){
        
        $db->query("SELECT price FROM vass_pricedetails ");
       while($row = $db->get_row()){
        $price[] = $row;
    }
        
        $buffer = array("status_code" => 200, "status_text" => "OK", "songprice" => $price[0], "albumprice" => $price[1]);
        
        header ( 'Cache-Control: no-cache, must-revalidate' );
	
	header ( 'Content-type: application/json' );
	
	print json_encode ( $buffer );
    }
     
}

$db->close;
?>