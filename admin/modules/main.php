<?php

if( ! defined( 'GLOBUS' ) ) {

	die( "Hacking attempt!" );

}
$maxmemory = (@ini_get( 'memory_limit' ) != '') ? @ini_get( 'memory_limit' ) : $lang['undefined'];
$safemode = (@ini_get( 'safe_mode' ) == 1) ? "<p class=\"text-error\">Safe mode IS <strong>ON!</strong>  We required off, please set <strong>safe mode</strong> to <strong>off</strong></p>" : "<p class=\"text-success\">Safe mode IS <strong>OFF!</strong></p>";

if(is_writable(ROOT_DIR . "/includes/admin.config.php")){
	$admin_file_status = '<p class="text-success">/includes/admin.config.php</strong> is writable.</p>';
}else{
	$admin_file_status = '<p class="text-error">/includes/admin.config.php</strong> is not writable please CHMOD this file to 666.</p>';
}
if(is_writable(ROOT_DIR . "/includes/config.inc.php")){
	$config_file_status = '<p class="text-success">/includes/config.inc.php</strong> is writable.</p>';
}else{
	$config_file_status = '<p class="text-error">/includes/config.inc.php</strong> is not writable please CHMOD this file to 666.</p>';
}
if(is_writable(ROOT_DIR . "/static")){
	$static_status = '<p class="text-success">/static</strong> is writable.</p>';
}else{
	$static_status = '<p class="text-error">/static</strong> is not writable please CHMOD this folder to 777.</p>';
}
if(is_writable(ROOT_DIR . "/cache")){
	$cache_status = '<p class="text-success">/cache</strong> is writable.</p>';
}else{
	$cache_status = '<p class="text-error">/cache</strong> is not writable please CHMOD this folder to 777.</p>';
}
if(is_writable(ROOT_DIR . "/static/artists/")){
	$static_artists_status = '<p class="text-success">/static/artists/</strong> is writable.</p>';
}else{
	$static_artists_status = '<p class="text-error">/static/artists/</strong> is not writable please CHMOD this folder to 777.</p>';
}
if(is_writable(ROOT_DIR . "/static/users/")){
	$static_users_status = '<p class="text-success">/static/users/</strong> is writable.</p>';
}else{
	$static_users_status = '<p class="text-error">/static/users/</strong> is not writable please CHMOD this folder to 777.</p>';
}
if(is_writable(ROOT_DIR . "/static/songs/")){
	$static_songs_status = '<p class="text-success">/static/songs/</strong> is writable.</p>';
}else{
	$static_songs_status = '<p class="text-error">/static/songs/</strong> is not writable please CHMOD this folder to 777.</p>';
}
if(is_writable(ROOT_DIR . "/static/albums/")){
	$static_albums_status = '<p class="text-success">/static/albums/</strong> is writable.</p>';
}else{
	$static_albums_status = '<p class="text-error">/static/albums/</strong> is not writable please CHMOD this folder to 777.</p>';
}

$row = $db->super_query("SELECT COUNT(*) AS count FROM vass_artists");
$total_artist = $row['count'];

$row = $db->super_query("SELECT COUNT(*) AS count FROM vass_albums");
$total_album = $row['count'];

$row = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs");
$total_song = $row['count'];

$row = $db->super_query("SELECT COUNT(*) AS count FROM vass_users");
$total_user = $row['count'];

$row = $db->super_query("SELECT COUNT(*) AS count FROM vass_song_love");
$total_loved = $row['count'];

$row = $db->super_query("SELECT COUNT(*) AS count FROM vass_genres");
$total_genre = $row['count'];

$row = $db->super_query("SELECT COUNT(DISTINCT played) AS countplyd FROM vass_songs");
$total_played = $row['countplyd'];


echo <<<HTML
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
			<div class="sidebar-nav">
				<ul class="nav nav-list bs-docs-sidenav affix-top">
					{$menu_li}
				</ul>
			</div>
		</div>
		<div class="span9">
			<div class="alert alert-info">
				<h2>Hello,{$member_id['username']}!</h2>
				<p>This control panel let you config your Site, you can change webtitle, description, manager album, user, artist ... And don't forget keep up to date!<p>
				<!-- <p><a href="http://tancode.com/tancode-ex" target="_blank" class="btn btn-success"><i class="icon-info-sign icon-white"></i> Check for update &raquo;</a></p> -->
			</div>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#global" data-toggle="pill">Global Information</a></li>
				<li><a href="#music" data-toggle="pill">Music Information</a></li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div id="global" class="tab-pane fade active in">
					<h3>Global Information</h3>
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Info</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Safe mode</td>
								<td>{$safemode}</td>
							</tr>
							<tr>
								<td>Config writeable</td>
								<td>{$config_file_status}</td>
							</tr>
							<tr>
								<td>Cache folder writeable</td>
								<td>{$cache_status}</td>
							</tr>
							<tr>
								<td>Static folder writeable</td>
								<td>{$static_status}</td>
							</tr>
							<tr>
								<td>Artist image folder writeable</td>
								<td>{$static_artists_status}</td>
							</tr>
							<tr>
								<td>Album image folder writeable</td>
								<td>{$static_albums_status}</td>
							</tr>
							<tr>
								<td>User image folder writeable</td>
								<td>{$static_users_status}</td>
							</tr>
							<tr>
								<td>Song store folder writeable</td>
								<td>{$static_songs_status}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="music" class="tab-pane fade">
					<h3>Музыка</h3>
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Инфо</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Испольнители</td>
								<td>{$total_artist}</td>
							</tr>
							<tr>
								<td>Альбомы</td>
								<td>{$total_album}</td>
							</tr>
							<tr>
								<td>Песни</td>
								<td>{$total_song}</td>
							</tr>
							<tr>
								<td>Жанры</td>
								<td>{$total_genre}</td>
							</tr>
							<tr>
								<td>Люди</td>
								<td>{$total_user}</td>
							</tr>
							<tr>
								<td>Избранное песни</td>
								<td>{$total_loved}</td>
							</tr>
							<tr>
								<td>Все проигранные песни</td>
								<td>{$total_played}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
HTML;
?>