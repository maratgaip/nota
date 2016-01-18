<?php
//echo"fhdf";die;
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

//header ( 'Content-type: text/json' );
//
//header ( 'Content-type: application/json' );

include("PayPalcredits.php/PayPalCredit-php/paypal_functions.php");

$type = $db->safesql( $_REQUEST['type'] );

$ID = $db->safesql( $_REQUEST['id'] );

$action = $db->safesql( $_REQUEST['action'] );

$keyword = $db->safesql( $_REQUEST['keyword'] );

$keyword = makekeyword( $keyword );

$page = $db->safesql( $_REQUEST['page'] );

if( ! $page) $page = 1;

if ($type == 'explore'){

	if ( !$action || $action == 'top-of-the-week' ){

		$top_week = $db->query("SELECT COUNT(*) AS count, song_id FROM vass_analz WHERE `time` > '" . date( "Y-m-d", (time() - 7*24*3600) ) . "' GROUP BY song_id ORDER by count DESC LIMIT 0,200");

		while($top = $db->get_row($top_week)){

			if($top['song_id']) {
				$row = $db->super_query("SELECT vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title,
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $top['song_id'] . "'");
					$songs .= "<li><a rel=\"child song\" rev=\"parent songs\" href=\"{$config['siteurl']}song/{$row['song_id']}\">" . stripslashes( $row['song_title'] ) . "</a> by <a href=\"{$config['siteurl']}search/" . trim(str_replace(" ", "+", stripslashes( $row['song_artist'] ))) . "\">" . stripslashes( $row['song_artist'] ) . "</a></li>";
			}
		}

		$content = '<h2>Top songs of the week on Kiandastream</h2><ol>' . $songs . '</ol>';

	}else{

		$name = $db->safesql ( $action );

		$row = $db->super_query ( "SELECT id FROM vass_genres WHERE name LIKE '%$name%' LIMIT 0,1" );

		$sql_result = $db->query ( "SELECT vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title,
		vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN vass_albums ON
		vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
		WHERE vass_artists.tag REGEXP '[[:<:]]" . $row ['id'] . "[[:>:]]' ORDER by vass_songs.id DESC LIMIT 0,500" );

		while ( $row = $db->get_row ( $sql_result ) ) {

			$songs .= "<li><a rel=\"child song\" rev=\"parent songs\" href=\"{$config['siteurl']}song/{$row['song_id']}\">" . stripslashes( $row['song_title'] ) . "</a> by <a href=\"{$config['siteurl']}search/" . trim(str_replace(" ", "+", stripslashes( $row['song_artist'] ))) . "\">" . stripslashes( $row['song_artist'] ) . "</a></li>";

		}

		$e_title = str_replace("%name%", $name, $config['genre_page']);

		$e_descr = str_replace("%name%", $name, $config['genre_page_descr']);

		$content = '<h2>Songs int tag: ' . $name . '</h2><ol>' . $songs . '</ol>';

	}

}elseif( $type == 'trending' ){

	if ( ! $action || $action == 'top-of-the-week' ){

		$trending = $db->query("SELECT COUNT(*) AS count, song_id FROM vass_analz WHERE `time` = '" . date( "Y-m-d" ) . "' GROUP BY song_id ORDER by count DESC LIMIT 0,20");

		while($top = $db->get_row($trending)){

			if($top['song_id']) {
				$row = $db->super_query("SELECT vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title,
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $top['song_id'] . "'");
					$songs .= "<li><a rel=\"child song\" rev=\"parent songs\"  href=\"{$config['siteurl']}song/{$row['song_id']}\">" . stripslashes( $row['song_title'] ) . "</a> by <a href=\"{$config['siteurl']}search/" . trim(str_replace(" ", "+", stripslashes( $row['song_artist'] ))) . "\">" . stripslashes( $row['song_artist'] ) . "</a></li>";
			}
		}

		$content = '<h2>Trending on Kiandastream</h2><ol>' . $songs . '</ol>';

	}

}elseif( $type == 'song' ){

	$row = $db->super_query("SELECT vass_songs.id, vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.title AS song_title,
		vass_songs.album_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id
		FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id  LEFT JOIN vass_artists ON
		vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '" . $ID . "' LIMIT 0,1");

	if( $row['song_title'] ){

		$e_title = str_replace("%name%", $row['song_title'], $config['song_page_title']);

		$e_title = str_replace("%artist%", $row['song_artist'], $e_title);

		$e_descr = str_replace("%name%", $row['song_title'], $config['song_page_descr']);

		$e_descr = str_replace("%artist%", $row['song_artist'], $e_descr);

		$content = '<h2>Listening ' . $e_title . '</h2><ol>' . $songs . '</ol>';

		$similar = $row['song_artist'];

		$hash = artisthash( $row['song_artist'] );

		if($row['artist_id']) {


			$songs ['album'] = $row ['song_album'];
			$songs ['url'] = stream ( $row ['hash'] );

			if($row ['artwork_url']) $songs ['image'] = songlist_images ( $row ['artwork_url'], $row['artist_id'] );
			else $songs ['image'] = songlist_images ( $row ['album_id'], $row['artist_id'] );

			$songs ['artist'] = $row ['song_artist'];

			$facebook_image = $config[siteurl] . "static/albums/" . $row['album_id'] . '_large.jpg';

		}else{

			$songs ['album'] = $row ['description'];
			$songs ['url'] = stream ( $row ['hash'] );
			$songs ['image'] = songlist_images ( $row ['artwork_url'], $row['artist_id'] );
			$songs ['artist'] = $row ['tag_list'];

			$facebook_image = $songs ['image']['large'];

		}

	}else{

		header('HTTP/1.0 404 Not Found');

	}

}elseif( $type == 'search' ){

	/*$keyword = trim($db->safesql($_GET['keyword']));
	if(strlen($keyword) < 3) die();
	$sql_result = $db->query ( "SELECT DISTINCT vass_songs.id AS song_id, vass_songs.title AS song_title, vass_songs.loved,
	vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN
	vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id
	WHERE vass_songs.title LIKE '%$keyword%' or vass_artists.name LIKE '%$keyword%' or vass_albums.name LIKE '%$keyword%' LIMIT 0,200" );

	while ( $row = $db->get_row ( $sql_result ) ) {
		$songs .= "<li><a rel=\"child song\" rev=\"parent songs\" href=\"{$config['siteurl']}song/{$row['song_id']}\">" . stripslashes( $row['song_title'] ) . "</a> by <a href=\"{$config['siteurl']}search/" . trim(str_replace(" ", "+", stripslashes( $row['song_artist'] ))) . "\">" . stripslashes( $row['song_artist'] ) . "</a></li>";
	}

	$e_title = str_replace("%keyword%", $keyword, $config['search_page_title']);

	$e_descr = str_replace("%keyword%",$keyword, $config['search_page_descr']);

	$content = '<h2>Searth Results</h2><ol>' . $songs . '</ol>';*/

}


$ajax = <<<HTML
<script language="javascript" type="text/javascript">
var player_root = '{$config['siteurl']}';
var player_skin = '{$config['template']}';
</script>
HTML;




if ($type == 'song') {
	$metatags['title'] = $e_title;
	$metatags['description'] = $e_descr;
	$metatags['keywords']= $e_title . ', ' . $config['keyword'];
}elseif ($type == 'search'){
	$metatags['title'] = $e_title;
	$metatags['description'] = $e_descr;
	$metatags['keywords']= $e_title . ', ' . $config['keyword'];
}elseif ($type == 'trending'){
	$metatags['title'] = $config['trending_page'];
	$metatags['description'] = $config['trending_page_descr'];
	$metatags['keywords']= $config['keyword'];
}elseif ($type == 'explore'){
	if ( ! $action || $action == 'top-of-the-day' ){
		$metatags['title'] = $config['top_week_page'];
		$metatags['description'] = $config['top_week_page_descr'];
		$metatags['keywords']= $config['keyword'];
	}elseif ( $action == 'latest' ){
		$metatags['title'] = $config['latest_love_page'];
		$metatags['description'] = $config['latest_love_page_descr'];
		$metatags['keywords']= $config['keyword'];
	}else{
		$metatags['title'] = $e_title;
		$metatags['description'] = $e_descr;
		$metatags['keywords']= $config['keyword'];
	}
}

$current_url = curPageURL();

if( ! $facebook_image ) $facebook_image = $config['facebook_icon'];

$metatags = <<<HTML
<title>{$metatags['title']}</title>
<meta name="title" content="{$metatags['title']}" />
<meta property="og:title" name="title" content="{$metatags['title']}" />
<meta property="og:url" content="{$current_url}" />
<meta property="og:image" content="{$facebook_image}" />
<meta property="og:site_name" content="{$config['sitetitle']}" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="{$config['facebook_app_id']}" />
<meta property="og:type" content="musician" />
<meta name="description" property="og:description" content="{$metatags['description']}" />
<meta name="keywords" content="{$metatags['keywords']}" />
<meta name="robots" content="index, follow" />
HTML;

// tags

$db->query ( "SELECT name FROM vass_genres ORDER by rand() LIMIT 0,100" );
while($row = $db->get_row()){
$tags .= "<li><a rel=\"child tag\" rev=\"parent tags\" href=\"{$config['siteurl']}explore/{$row['name']}\">" . $row['name'] . "</a></li>";

}

echo <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{$metatags}
<link rel="stylesheet" href="{$config['siteurl']}assets/css/style.css" type="text/css" media="screen" />
{$ajax}
<script type="text/javascript">
(function () {
	var getBaseURL = function (url) {
			var base = url.match(/^http[s]?:\/\/([a-zA-Z0-9-]*\.*)*[a-zA-Z0-9-]+(?!\.[a-zA-Z0-9-]{2,3})\/*/);
			if (base) {
				return base[0];
			} else {
				return "";
			}
		};
	var location = window.location.href;
	var rootURL = getBaseURL(location);
	if (location.indexOf("#") == -1) {
		var hash = location.substr(rootURL.length);
		if (hash == "index.php" || hash == "" || hash == "/" || hash == "seo.php") {
			hash = "/";
		}
		if (rootURL.lastIndexOf("/") != rootURL.length - 1) {
			rootURL += "/";
		}
		if (hash.indexOf("/") != 0) {
			hash = "/" + hash;
		}

		//hash = hash.replace('trending/', 'trending');

		hash = hash.replace(/\/page\/[0-9]/g, '');

		if (hash == "/settings" || hash == "/settings/") {
			window.location.href = player_root;
		} else {
			window.location.href = player_root + "#!" + hash;
		}
	}
 })();
</script>
</head>
<body>
<h1>{$config[sitetitle]} Player</h1>
    <h2>{$config[sitetitle]} - The best way to listen and share music with your friends.</h2>
        <a href="{$config['siteurl']}trending">Trending</a>
        <a href="{$config['siteurl']}explore">Explore</a>
        <noscript>
            <h2>Free listen and download music online.</h2>
            <p>Begin by searching a song on Kiandastream!</p>
            <form method="get" action="/search/" class="searchSite" name="searchSite" id="searchSite">
                <input type="text" name="q" class="searchBoxInput" autocomplete="off" placeholder="Search" />
                <button id="searchSubmitButton" type="submit" class="submit" title="Search"></button>
            </form>
            <ul class="links">
                <li><a href="{$config['siteurl']}trending">Trending</a></li>
                <li><a href="{$config['siteurl']}explore">Explore</a></li>
            </ul>
        </noscript>
		{$content}
		<div class="tags"><ol>{$tags}</ol></div>
</div>
</body>
</html>
HTML;

$db->close ();
?>
