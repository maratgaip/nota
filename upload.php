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
	
					
	$image = $_FILES["avatar"]["tmp_name"];
    $image_name = $_FILES['avatar']['name'];
	$image_size = $_FILES['avatar']['size'];
    $image_name  = str_replace(" ", "_", $image_name);
	$img_name_arr = explode(".",$image_name);
	$type         = end($img_name_arr);
	
	require_once INCLUDE_DIR . '/class/_class_thumb.php';
	
	$randnumber = md5(rand(10000,90000));
	
	$res = @move_uploaded_file( $image, ROOT_DIR . "/static/users/avatar_full_" . $member_id['username'] );
	
	
	if ($res) {
		$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $member_id['username'] );
		
		$thumb->size_auto('500');
		
		$thumb->jpeg_quality( 100 );
		
		$thumb->save( ROOT_DIR . "/static/users/avatar_original_" . $member_id['username'] . ".jpg" );
		
		$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $member_id['username'] );
		
		$thumb->crop('75', '75' );
		
		$thumb->jpeg_quality( 100 );
		
		$thumb->save( ROOT_DIR . "/static/users/avatar_medium_" . $member_id['username'] . ".jpg" );
		
		$thumb = new thumbnail( ROOT_DIR . "/static/users/avatar_full_" . $member_id['username'] );
		
		$thumb->crop('35', '35' );
		
		$thumb->jpeg_quality( 100 );
		
		$thumb->save( ROOT_DIR . "/static/users/avatar_small_" . $member_id['username'] . ".jpg" );
		
		@unlink ( ROOT_DIR . "/static/users/avatar_full_" . $member_id['username'] );
		
		$row = $db->query("UPDATE vass_users SET `avatar` = 1 WHERE `user_id` = '" . $member_id['user_id'] . "';");
		
		$member_id['avatar'] = 1;
		
	$buffer['status_code'] = 200;
	
	$buffer['status_text'] = "OK";
	
	$buffer['user'] = $member_id;
	$buffer['user']['is_beta_tester'] = false;
	$buffer['user']['viewer_following'] = false;
	$buffer['user']['import_feeds'] = import_feeds($member_id['user_id']);
	$buffer['user']['image'] = avatar( $member_id['avatar'], $member_id['username'] );
	
	
	$row = $db->super_query("SELECT vass_background.color, vass_background.image, vass_background.position, vass_background.repeat, vass_background.use_image FROM vass_background WHERE vass_background.user_id = '" . $member_id['user_id'] . "';");
		
	if( $row['image'] ) {
		$use_image = true;
		$is_default = false;
	} else {
		$is_default = true;
		$use_image = false;
	}
		
	$buffer['user']['background'] = $row;
	$buffer['user']['background']['is_default'] = $is_default;
	$buffer['user']['background']['use_image'] = $use_image;

	unset($buffer['user']['password']);
	
	header('Cache-Control: no-cache, must-revalidate');

	header('Content-type: application/json');

	print json_encode($buffer);
		
	} else print 'error';
	
}else{
	
	$row = $db->super_query("SELECT avatar FROM vass_users WHERE user_id = '" . $member_id['user_id'] . "'");
	
	if( $row['avatar'] ) $avatar = $config['siteurl'] . "static/users/avatar_medium_" . $member_id['username'] . ".jpg";
	
	else $avatar = $config['siteurl'] . "static/users/avatar_medium_default.png";
	
	$ajax = '
		<script language="javascript" type="text/javascript">
		<!--
		var player_root       = \'' . $config['siteurl'] . '\';
		var player_skin       = \'' . $config['template'] . '\';
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
        <script type="text/javascript" src="{$config['siteurl']}assets/js/uploader.js"></script>
    </body>
</html>
HTML;
}

$db->close ();

?>