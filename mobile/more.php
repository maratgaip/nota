<?php

@session_start ();

@ob_start ();

@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );

@ini_set ( 'display_errors', false );

@ini_set ( 'html_errors', false );

@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

$type = $_SERVER ['QUERY_STRING'];

header ( 'Content-type: text/json' );

header ( 'Content-type: application/json' );

if($type != 'getCommunicationToken'){
	
	@session_start ();
	
	define ( 'ROOT_DIR', dirname ( __FILE__ ) );
	
	define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );
	
	include (INCLUDE_DIR . '/config.inc.php');
	
	require_once INCLUDE_DIR . '/class/_class_mysql.php';
	
	require_once INCLUDE_DIR . '/db.php';
	
	require_once INCLUDE_DIR . '/functions.php';
	
	$_TIME = date( "Y-m-d H:i:s", time() );
	
	function json_build($session, $version, $prefetchEnabled, $result) {
		
		$json = array ();
		
		$json ['header'] ['session'] = session_id ();
		$json ['header'] ['serviceVersion'] = "20100903";
		$json ['header'] ['prefetchEnabled'] = true;
		$json ['result'] = $result;
		$json = json_encode ( $json );
		
		return $json;
	}
	
	$result = array ();
	
	$_TIME = time () + ($config ['date_adjust'] * 60);
	
	$_TIME = date ( "Y-m-d H:i:s", $_TIME );
	
	if (isset ( $_REQUEST ['id'] ))
		$id = intval ( $_REQUEST ['id'] );
	else
		$id = "";
	
	
	$json = file_get_contents ( 'php://input' );
	
	$obj = json_decode ( $json );
}

if ($type == "getResultsFromSearch") {
	
	$query = $obj->{'parameters'}->{'query'};
	$type = $obj->{'parameters'}->{'type'}; //araya()
	$guts = $obj->{'parameters'}->{'guts'}; //what the fuck is this shit
	$ppOverride = $obj->{'parameters'}->{'ppOverride'};
	
	$query = $db->safesql($query);
	
	$db->query ( "SELECT vass_albums.id AS AlbumID, vass_albums.name AS AlbumName, vass_artists.id AS ArtistID,
	vass_artists.name AS ArtistName, vass_songs.id AS SongID, vass_songs.title AS Name
	FROM vass_songs
	LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
	LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
	WHERE vass_songs.title LIKE '%$query%' LIMIT 0,200" );
		
		$result = array ();
		
		$result['result'] ['Songs'] = array ();
		
		while ( $row = $db->get_row () ) {
			$row ['SongName'] = $row ['Name'];
			$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
			$row ['TrackNum'] = "0";
			$result['result'] ['Songs'] [] = $row;
			
		}
		
		$result['result'] ['Albums'] = array ();
		
		$result['result'] ['Playlists'] = array ();
		
		$db->query ( "SELECT id AS PlaylistID, name AS Name, descr AS About FROM vass_albums
		WHERE name LIKE '%$query%' LIMIT 0,100" );
		
		while ( $row = $db->get_row () ) {
			$row ['CoverArtFilename'] = $row ['PlaylistID'] . ".jpg";
			$result['result'] ['Playlists'] [] = $row;
			
		}
		
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );
	

} elseif ($type == "getPlaylistByID") {
	
	$playlist_id = intval ( $obj->{'parameters'}->{'playlistID'} );
	
	$result = $db->super_query ( "SELECT vass_playlists.id AS PlaylistID, vass_playlists.name AS Name, vass_playlists.descr AS About, vass_users.user_id AS user_id, 
	vass_users.username AS Username FROM vass_playlists LEFT JOIN vass_users ON vass_playlists.user_id = vass_users.user_id WHERE vass_playlists.id = '" . $playlist_id . "' LIMIT 0,1" );
	
	$result ['Picture'] = $result ['PlaylistID'] . ".jpg";
	$result ['Songs'] = array();
	
	$query = $db->query ( "SELECT vass_albums.id AS AlbumID, vass_albums.name AS AlbumName, vass_artists.id AS ArtistID,
	vass_artists.name AS ArtistName, vass_songs.id AS SongID, vass_songs.title AS Name
	FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
	LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
	WHERE vass_song_playlist.playlist_id =  '" . $playlist_id . "'" );
	$pos = 0;
	while ( $row = $db->get_row ($query) ) {
		$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
		$row ['TrackNum'] = $pos;
		$Songs[] = $row;
		$result ['Songs'] = $Songs;
		$pos++;
	}
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

}elseif ($type == "popularGetSongs") {
	
		$db->query ( "SELECT vass_artists.id AS ArtistID, vass_artists.name AS ArtistName, vass_songs.id AS SongID, vass_songs.title AS Name, vass_albums.id AS AlbumID, vass_albums.name AS AlbumName
	FROM vass_songs
	LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
	LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
	ORDER BY rand()
	LIMIT 0, 100" );
		
		$result = array ();
		
		while ( $row = $db->get_row () ) {
			$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
			$row ['TrackNum'] = "0";
			$result ['Songs'] [] = $row;
		}
		
		$buffer = json_build ( $session, $version, $prefetchEnabled, $result );
		
	

} elseif ($type == "playlistGetSongs") {
	
	$playlistID = intval ( $obj->{'parameters'}->{'playlistID'} );
	
	$songID = $db->super_query ( "SELECT songID FROM vass_playlist_songs WHERE playlistID = '$playlistID'" );
	
	if ($songID) {
		
		$Songs = $db->query ( "SELECT vass_albums.id AS AlbumID, 
			vass_albums.name AS AlbumName, vass_artists.id AS ArtistID, 
			vass_artists.name AS ArtistName,
			 vass_songs.id AS SongID, vass_songs.title AS Name
FROM vass_songs
LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
WHERE vass_songs.id
IN ( " . $songID ['songID'] . " )
LIMIT 0, 30" );
		
		$result = array ();
		$i = 0;
		
		while ( $row = $db->get_row ( $Songs ) ) {
			$row ['AvgRating'] = null;
			$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
			$row ['Year'] = "";
			$row ['EstimateDuration'] = "";
			$row ['Popularity'] = "1212800047";
			$row ['TrackNum'] = "0";
			$row ['IsLowBitrateAvailable'] = "0";
			$row ['Flags'] = "0";
			$row ['Sort'] = $i;
			$result ['Songs'] [] = $row;
			$i ++;
		}
	
	} else
		$result ['Songs'] = array ();
	;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "authenticateUser") {
	
	$username = $db->safesql ( $obj->{'parameters'}->{'username'} );
	$password = $db->safesql ( $obj->{'parameters'}->{'password'} );
	
	$_POST ['login'] = "submit";
	
	$_POST['username'] = $username;
	
	$_POST['password'] = $password;
	
	require_once INCLUDE_DIR . '/member.php';
	
	if ($logged) {
		
		$member_id ['userID'] = intval($member_id ['UserID']);
		$member_id ['IsPremium'] = 1;
		
		if ($member_id ['avatar'])
			$member_id ['Picture'] = $member_id ['UserID'] . ".jpg";
		
		$json = array ();
		$json ['header'] ['session'] = session_id ();
		$json ['header'] ['serviceVersion'] = "20100903";
		$json ['header'] ['prefetchEnabled'] = true;
		$json ['result'] = $member_id;
		$buffer = json_encode ( $json );
	
	} else
		$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "getFavorites") {
	
	$user_id = intval ( $obj->{'parameters'}->{'userID'} );
	
	$type = $obj->{'parameters'}->{'ofWhat'};
	
	if ($type == "Songs") {
		
		$db->query ( "SELECT  vass_albums.id AS AlbumID, vass_albums.name AS AlbumName, vass_artists.id AS ArtistID,
		vass_artists.name AS ArtistName, vass_songs.id AS SongID, vass_songs.title AS Name
		FROM vass_song_love LEFT JOIN vass_songs ON vass_song_love.song_id = vass_songs.id
		LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
		LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
		WHERE vass_song_love.user_id = '$user_id'");
		
		$result = array ();
		$pos = 0;
		while ( $row = $db->get_row () ) {
			$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
			$row ['TrackNum'] = $pos;
			$result [] = $row;
			$pos++;
		}
		
		$buffer = json_build ( $session, $version, $prefetchEnabled, $result );
	
	} elseif ($type == "Users") {

	$userID = intval ( $obj->{'parameters'}->{'userID'} );

	$offset = intval ( $obj->{'parameters'}->{'offset'} );

	$result = array ();


	$db->query ( "SELECT vass_friendship.follower_id AS UserID, vass_users.username AS FName, vass_users.username,
	vass_users.name, vass_users.bio, 
	vass_users.website, vass_users.total_loved, vass_users.location, vass_users.total_loved, 
	vass_users.total_following, vass_users.total_followers, vass_users.avatar
	FROM vass_users LEFT JOIN vass_background ON vass_users.user_id = vass_background.user_id 
	LEFT JOIN vass_friendship ON vass_users.user_id = vass_friendship.follower_id WHERE 
	vass_friendship.user_id = '" . $userID . "';" );




	/*$db->query ( "SELECT vass_tem_users.UserID, vass_tem_users.avatar, vass_tem_usersl;FName, vass_tem_users.LName, vass_tem_users.TSAdded, vass_tem_users.City, vass_tem_users.State, vass_tem_users.Country,
	vass_tem_users.IsPremium, vass_tem_users.Sex, vass_tem_users.Flags AS FollowingFlags, vass_tem_favorited.TSFavorited
	FROM vass_tem_users LEFT JOIN vass_tem_favorited ON vass_tem_users.UserID = vass_tem_favorited.UserID WHERE what='User'
	AND vass_tem_favorited.type_id='$userID'" );
	*/
	while ( $row = $db->get_row () ) {
	if ($row ['avatar'])
		$row ['Picture'] = $row ['UserID'] . ".jpg";
	else
		$row ['Picture'] = null;
	unset ( $row ['avatar'] );
	
	
	
	$result [] = $row;
	}
	
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

	}else
		die ( "NOTHING HERE" );

} elseif ($type == "userGetPlaylists") {
	
	$user_id = intval ( $obj->{'parameters'}->{'userID'} );
	
	$db->query ( "SELECT id AS PlaylistID, name AS Name, descr AS About FROM vass_playlists
	WHERE user_id ='$user_id'" );
	
	while ( $row = $db->get_row () ) {
		$row ['CoverArtFilename'] = $row ['PlaylistID'] . ".jpg";
		$result['Playlists'] [] = $row;
			
	}
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "getItemByPageName") {
	
	$name = $obj->{'parameters'}->{'name'};
	
	$name = $db->safesql($name);
	
	if($name){
		
		$result['type'] = "user";
		
		$row = $db->super_query("SELECT user_id, FName, LName, Picture, TSAdded, City, State, Country, IsPremium, Sex, Flags, username AS Username,
		Following, Followed, FavoriteSong, Playlist, Library
		FROM
		vass_users WHERE username = '$name' LIMIT 0,1");
		
		$result ['data'] ['FollowingCount'] = $row['Following'];
		
		$result ['data'] ['FollowedCount'] = $row['Followed'];
		
		$result ['data'] ['FavoriteSongCount'] = $row['FavoriteSong'];
		
		$result ['data'] ['PlaylistCount'] = $row['Playlist'];
		
		$result ['data'] ['LibrarySongCount'] = $row['Library'];
		
		$result['user'] = $row;
		
		$row ['Picture'] = $row['user_id'] . ".jpg";
		
		$result ['user']['TSAdded'] = strtotime ( $row ['TSAdded'] );
		
	}else $result['type'] = false;

	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );
	
} elseif ($type == "getUserByID") {
	
	$user_id = intval ( $obj->{'parameters'}->{'user_id'} );
	
	if($user_id){
		
		$result['type'] = "user";
		
		$row = $db->super_query("SELECT user_id, FName, LName, TSAdded, City, State, Country, IsPremium, Sex, Flags, username AS Username FROM
		vass_users WHERE user_id = '$user_id' LIMIT 0,1");
		
		$result['User'] = $row;
		
		$row ['Picture'] = $user_id . ".jpg";
		
		$result ['User']['TSAdded'] = strtotime ( $row ['TSAdded'] );
		
	}else 	$result['type'] = false;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "getTokenForSong") {
	
	$songID = intval ( $obj->{'parameters'}->{'songID'} );
	
	$result ['Token'] = $songID;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "getSongFromToken") {
	
	$token = intval ( $obj->{'parameters'}->{'token'} );
	
	// token = songID cho nhanh, tam thoi
	
	$row = $db->super_query ( "SELECT vass_albums.id AS AlbumID, 
vass_albums.name AS AlbumName, vass_artists.id AS ArtistID, 
vass_artists.name AS ArtistName, 
vass_songs.id AS SongID, vass_songs.title AS Name
FROM vass_songs
LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
WHERE vass_songs.id = '$token'" );
	
	$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
	$row ['EstimateDuration'] = "";
	$row ['Flags'] = "0";
	$result = $row;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "logoutUser") {
	
	$lala_user_id = "";
	$lala_password = "";
	set_cookie( "lala_user_id", "", 0 );
	set_cookie( "lala_password", "", 0 );
	set_cookie( session_name(), "", 0 );
	@session_destroy();
	@session_unset();
	$logged = 0;
	
	$result = null;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

} elseif ($type == "markSongComplete") {
	
	require_once INCLUDE_DIR . '/member.php';
	
	if (! $logged)
		die ( "You A U logged?" );
	
	$user_id = intval ( $obj->{'parameters'}->{'user'}->{'user_id'} );
	
	$songID = intval ( $obj->{'parameters'}->{'song'}->{'songID'} );
	
	$artistID = intval ( $obj->{'parameters'}->{'song'}->{'artistID'} );
	
	$albumID = intval ( $obj->{'parameters'}->{'song'}->{'albumID'} );
	
	if ($artistID)
		$db->query ( "UPDATE vass_artists SET playcount=playcount+1 WHERE id = '$artistID'" );
	
	if ($albumID)
		$db->query ( "UPDATE vass_albums SET playcount=playcount+1 WHERE id = '$albumID'" );
	
	if ($songID){
		$_TODAY = date( "Y-m-d", time() );
		
		$row = $db->super_query("SELECT id, type_id, time FROM vass_events WHERE user_id = '" . $member_id ['UserID'] . "' AND type = 'songPlayed' ORDER BY id DESC LIMIT 0,1");
		
		if(date( "Y-m-d", strtotime($row['time']) ) == $_TODAY){
			
			$new_list = $row['type_id'] . "," . $songID;
			
			$new_list = explode(",", $new_list);
			
			$new_list = array_unique ( $new_list );
			
			$new_list = implode(",", $new_list);
			
			$db->query ( "UPDATE vass_events SET type_id = '$new_list', time = '$_TIME' WHERE id = '" . $row['id'] . "'");
			
		}else $db->query ( "INSERT INTO vass_events (user_id, type_id, type_id2, type, time) VALUES ('" . $member_id ['UserID'] . "','$songID', '$albumID', 'songPlayed','$_TIME')" );
		
	}
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

}elseif ($type == "getStreamKeyFromSongIDEx") {
	
	$songID = intval ( $obj->{'parameters'}->{'songID'} );
	
	$row ['streamKey'] = $songID;
	$row ['FileID'] = $songID;
	$row ['FileToken'] = $songID;
	$row ['ts'] = 1336467213;
	$row ['isMobile'] = false;
	$row ['streamServerID'] = 2;
	$row ['ip'] = "mobile.nota.kg";
	$result = $row;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

}elseif ($type == "startAutoplayTag") {
	
	$tagID = intval ( $obj->{'parameters'}->{'tagID'} );
	
	$db->query("SELECT id FROM vass_artists WHERE vass_artists.tag REGEXP '[[:<:]]" . $tagID . "[[:>:]]' ORDER BY rand(), id ASC LIMIT 0, 40");
	
	while( $row =  $db->get_row() ){
		$artist_array[] = $row['id'];
		$seedArtists[$row['id']] = "p";
		$result['autoplayState']['seedArtists'] = $seedArtists;
	}
	
	$result['frowns'] = array();
	
	$row = $db->super_query("SELECT vass_albums.id AS AlbumID, vass_albums.name AS AlbumName, 
						vass_artists.id AS ArtistID, vass_artists.name AS ArtistName, vass_songs.id AS SongID, 
						vass_songs.title AS SongName
						FROM vass_songs
						LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
						LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
						WHERE vass_artists.tag REGEXP '[[:<:]]" . $tagID . "[[:>:]]' ORDER BY rand()
						LIMIT 0, 1");
	
	$result['autoplayState']['songIDsAlreadySeen'][] = intval($row['SongID']);
	$result['autoplayState']['recentArtists'] = array();
	$result['autoplayState']['secondaryArtistWeightModifier'] = 0.9;
	$result['autoplayState']['seedArtistWeightRange'] = array(110, 130);
	$result['autoplayState']['weightModifierRange'] = array(-9, 9);
	$result['autoplayState']['minDuration'] = 60;
	$result['autoplayState']['maxDuration'] = 1500;
	$result['autoplayState']['tagID'] = $tagID;
	$row['SongID'] = intval($row['SongID']);
	$result['queuedSongs'][$row['SongID']] = intval($row['ArtistID']);
	
	$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
	$result['nextSong'] = $row;
	
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );

/*
                "AlbumID": 189886,
                "ArtistID": 8121,
                "ArtistName": "Del Tha Funkee Homosapien",
                "AlbumName": "Future Development",
                "IsLowBitrateAvailable": false,
                "IsVerified": true,
                "Popularity": "1231600006",
                "CoverArtFilename": ".grooveshark.com\/static\/amazonart\/s189886.jpg",
                "EstimateDuration": 0,
                "SponsoredAutoplayID": 0,
                "Flags": 0,
                "SongName": "X-Files"
 */


}elseif ($type == "getAutoplaySong") {
	
	$tagID = intval ( $obj->{'parameters'}->{'autoplayState'}->{'id'} );
	
	$seedArtists =  $obj->{'parameters'}->{'autoplayState'}->{'seedArtists'};
	
	$result['frowns'] = array();
	
	$row = $db->super_query("SELECT vass_albums.id AS AlbumID, vass_albums.name AS AlbumName, 
						vass_artists.id AS ArtistID, vass_artists.name AS ArtistName, vass_songs.id AS SongID, 
						vass_songs.title AS SongName
						FROM vass_songs
						LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
						LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id
						WHERE vass_artists.tag REGEXP '[[:<:]]" . $tagID . "[[:>:]]' ORDER BY rand()
						LIMIT 0, 1");
	
	$result['songIDsAlreadySeen'][] = intval($row['SongID']);
	$result['recentArtists'] = array();
	$result['secondaryArtistWeightModifier'] = 0.9;
	$result['seedArtistWeightRange'] = array(110, 130);
	$result['weightModifierRange'] = array(-9, 9);
	$result['minDuration'] = 60;
	$result['maxDuration'] = 1500;
	$result['tagID'] = $tagID;
	$result['queuedSongs'][$row['SongID']] = intval($row['ArtistID']);
	$row ['CoverArtFilename'] = $row ['AlbumID'] . "_small.jpg";
	$result['nextSong'] = $row;
	$buffer = json_build ( $session, $version, $prefetchEnabled, $result );
}

echo $buffer;

$db->close;
?>