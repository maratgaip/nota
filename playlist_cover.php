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

require_once INCLUDE_DIR . '/member.php';

require_once ROOT_DIR . '/modules/functions.php';

if( ! $logged ){
	
	header('HTTP/1.0 404 Not Found');
	
	die();

}
if (isset($_FILES["avatar"]) && is_uploaded_file($_FILES["avatar"]["tmp_name"]) && $_FILES["avatar"]["error"] == 0) {
	
	$id = intval($_GET['id']);
	$allowedExts = array("jpg", "jpeg", "gif", "png");
	$image = $_FILES["avatar"]["tmp_name"];
    $image_name = $_FILES['avatar']['name'];
	$image_size = $_FILES['avatar']['size'];
    $image_name  = str_replace(" ", "_", $image_name);
	$img_name_arr = explode(".",$image_name);
	$type         = end($img_name_arr);
	
	require_once INCLUDE_DIR . '/class/_class_thumb.php';
	
	$randnumber = md5(rand(100000,900000));
	
	$res = @move_uploaded_file( $image, ROOT_DIR . "/static/playlists/". $randnumber );
	
	if ($res) {
		
		$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $randnumber);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $randnumber);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $randnumber);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $randnumber);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/playlists/" . $randnumber);
		
		$row = $db->query("UPDATE vass_playlists SET `cover` = 1 WHERE `user_id` = '" . $member_id['user_id'] . "' AND id = '$id';");
		
	
	$buffer = array(
		"status_code" => 200,
		"image" => "$id.jpg"
	);
	
	header('Cache-Control: no-cache, must-revalidate');

	header('Content-type: application/json');

	print json_encode($buffer);
		
	} else print 'error';
	
}else{
	
	$id = intval($_GET['id']);
	$row = $db->super_query("SELECT cover FROM vass_playlists WHERE id = '" . $id . "'");
	if( $row['cover'] ) $avatar = $config['siteurl'] . "static/playlists/" . $id . "_small.jpg";
	else $avatar = $config['siteurl'] . "static/playlists/default.png";
	$ajax = '
		<script language="javascript" type="text/javascript">
		<!--
		var player_root       = \'' . $config['siteurl'] . '\';
		var playlist_id       = \'' . $id . '\';
		//-->
		</script>
	';
echo <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>lala image uploader</title>
        <link rel="stylesheet" href="assets/css/settings.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="assets/css/app-utils.css" type="text/css" media="screen" />
    </head>
    	{$ajax}
    <body>
        <div id="settings_profile_avatar">
            <img src="{$avatar}" width="75" height="75" id="settings_profile_avatar_img" />
        </div>
            <form enctype="multipart/form-data" action="" method="post" id="file_form" >
                <div id="settings_profile_avatar_choose" class="generic_button">
                    <input type="file" id="settings_profile_avatar_choose_input" name="avatar" />
                     Изменить фотографию
                </div>
                <div id="settings_profile_avatar_filename" class="display_none">filename</div>
                <div class="clear"></div> 
            </form>
        <script type="text/javascript">
            uploaded = false;
            loggedInUser = "User object"; 
         </script>
        <script type="text/javascript" src="{$config['siteurl']}assets/js/cover_uploader.js"></script>
    </body>
</html>
HTML;
}

$db->close ();

?>